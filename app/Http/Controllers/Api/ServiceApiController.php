<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Service;
use App\Services\ApiProviderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceApiController extends Controller
{
    // ── GET /api/v1/services ─────────────────────────────────────────────────
    public function index(): JsonResponse
    {
        $services = Service::with('category')
            ->where('is_active', true)
            ->orderBy('category_id')->orderBy('id')
            ->get()
            ->map(fn($s) => [
                'service'      => $s->id,
                'name'         => $s->name,
                'type'         => $s->type ?? 'Default',
                'category'     => $s->category?->name ?? 'Uncategorized',
                'rate'         => number_format((float) ($s->sale_price ?? $s->price_per_k), 2, '.', ''),
                'min'          => (int) $s->min_qty,
                'max'          => (int) $s->max_qty,
                'dripfeed'     => (bool) ($s->drip_feed ?? false),
                'refill'       => (bool) ($s->refill ?? false),
                'cancel'       => (bool) ($s->cancel ?? false),
                'average_time' => $s->average_time ?? null,
            ]);

        return response()->json($services);
    }

    // ── GET /api/v1/balance ──────────────────────────────────────────────────
    public function balance(): JsonResponse
    {
        $user = auth()->user();

        return response()->json([
            'balance'  => number_format((float) $user->balance, 2, '.', ''),
            'currency' => \App\Models\Setting::where('key', 'currency_code')->value('value') ?? 'USD',
        ]);
    }

    // ── GET /api/v1/orders ───────────────────────────────────────────────────
    public function orders(Request $request): JsonResponse
    {
        $perPage = min((int) ($request->query('per_page', 15)), 100);

        $orders = Order::where('user_id', auth()->id())
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'data' => $orders->map(fn($o) => [
                'order'        => $o->id,
                'api_order_id' => $o->api_order_id,
                'service'      => $o->service_id,
                'status'       => $o->status,
                'quantity'     => $o->quantity,
                'start_count'  => $o->start_count ?? 0,
                'remains'      => $o->remains ?? 0,
                'charge'       => number_format((float) $o->charge, 2, '.', ''),
                'link'         => $o->link,
                'created_at'   => $o->created_at->toISOString(),
            ]),
            'current_page' => $orders->currentPage(),
            'last_page'    => $orders->lastPage(),
            'per_page'     => $orders->perPage(),
            'total'        => $orders->total(),
        ]);
    }

    // ── GET /api/v1/order-status?order={api_order_id} ────────────────────────
    public function orderStatus(Request $request): JsonResponse
    {
        $apiOrderId = $request->query('order');

        if (!$apiOrderId) {
            return response()->json(['error' => 'order parameter is required.'], 422);
        }

        $order = Order::where('api_order_id', $apiOrderId)
            ->where('user_id', auth()->id())
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found.'], 404);
        }

        return response()->json([
            'order'        => $order->id,
            'api_order_id' => $order->api_order_id,
            'status'       => $order->status,
            'start_count'  => $order->start_count ?? 0,
            'remains'      => $order->remains ?? 0,
            'quantity'     => $order->quantity,
            'charge'       => number_format((float) $order->charge, 2, '.', ''),
            'link'         => $order->link,
            'created_at'   => $order->created_at->toISOString(),
        ]);
    }

    // ── POST /api/v1/add-order ───────────────────────────────────────────────
    public function addOrder(Request $request): JsonResponse
    {
        $user = auth()->user();

        if ($user->is_blocked) {
            return response()->json(['error' => 'Your account has been suspended.'], 403);
        }

        $request->validate([
            'service'  => 'required|integer',
            'link'     => 'required|url',
            'quantity' => 'required|integer|min:1|max:100000000',
            'comments' => 'nullable',
        ]);

        $service = Service::where('id', $request->service)
            ->where('is_active', true)
            ->select('id', 'sale_price', 'min_qty', 'max_qty', 'type', 'category_id', 'api_provider_id', 'api_service_id')
            ->with(['category:id,name', 'apiProvider:id,api_url,api_key,short_name,status'])
            ->first();

        if (!$service) {
            return response()->json(['error' => 'Service not found or inactive.'], 404);
        }

        $price = (float) ($service->sale_price ?? 0);
        if (!($price > 0)) {
            return response()->json(['error' => 'Service price is not set.'], 422);
        }

        $rawType      = strtolower((string) ($service->type ?? ''));
        $type         = trim(preg_replace('/[^a-z0-9]+/', '_', $rawType), '_');
        $categoryName = strtolower((string) ($service->category?->name ?? ''));

        $requiresComments = in_array($type, ['custom_comments', 'custom_comments_package', 'comment_replies'], true)
            || str_contains($type, 'comment');

        $comments = null;
        if ($requiresComments) {
            $commentsInput = $request->comments ?? '';
            // Accept both a plain string (newline-separated) and a JSON array
            if (is_array($commentsInput)) {
                $comments = collect($commentsInput)->map(fn($v) => trim((string) $v))->filter()->values()->all();
            } else {
                $lines    = preg_split('/\r\n|\r|\n/', (string) $commentsInput);
                $comments = collect($lines)->map(fn($v) => trim($v))->filter()->values()->all();
            }
            if (count($comments) === 0) {
                return response()->json(['error' => 'This service requires comments. Provide at least one.'], 422);
            }
        }

        $quantityForOrder = $requiresComments ? count($comments) : (int) $request->quantity;

        if ($quantityForOrder < (int) $service->min_qty || $quantityForOrder > (int) $service->max_qty) {
            return response()->json([
                'error' => 'Quantity must be between ' . number_format((int) $service->min_qty) . ' and ' . number_format((int) $service->max_qty) . '.',
            ], 422);
        }

        $isPerUnit = in_array($type, ['package', 'custom_comments_package', 'comment_replies'], true) || str_contains($type, 'package');
        $charge    = $isPerUnit ? $price * $quantityForOrder : ($price / 1000) * $quantityForOrder;

        $apiOrderId   = null;
        $shouldSubmit = $service->api_provider_id && $service->api_service_id
            && $service->apiProvider && $service->apiProvider->status === 'enabled';

        if ($shouldSubmit) {
            try {
                $res        = (new ApiProviderService($service->apiProvider))->createOrder(
                    (string) $service->api_service_id,
                    (string) $request->link,
                    $quantityForOrder,
                    $comments ? ['comments' => $comments, 'url' => (string) $request->link] : ['url' => (string) $request->link]
                );
                $apiOrderId = (string) ($res['order_id'] ?? '');
                if ($apiOrderId === '') {
                    throw new \RuntimeException('Provider did not return an order ID.');
                }
            } catch (\Throwable $e) {
                return response()->json(['error' => 'Provider error: ' . $e->getMessage()], 502);
            }
        }

        try {
            $order = DB::transaction(function () use ($user, $service, $quantityForOrder, $charge, $request, $comments, $apiOrderId, $shouldSubmit) {
                $freshUser = \App\Models\User::whereKey($user->id)->lockForUpdate()->first();
                if ((float) $freshUser->balance < (float) $charge) {
                    throw new \RuntimeException('Insufficient balance. Required: ' . number_format($charge, 2) . ', Available: ' . number_format($freshUser->balance, 2));
                }
                $order = Order::create([
                    'user_id'      => $freshUser->id,
                    'service_id'   => $service->id,
                    'quantity'     => $quantityForOrder,
                    'charge'       => $charge,
                    'link'         => $request->link,
                    'comments'     => $comments,
                    'api_order_id' => $apiOrderId ?: null,
                    'status'       => $shouldSubmit ? 'processing' : 'pending',
                ]);
                $freshUser->decrement('balance', (float) $charge);
                return $order;
            });
        } catch (\Throwable $e) {
            if ($shouldSubmit && $apiOrderId) {
                try {
                    (new ApiProviderService($service->apiProvider))->cancelOrder($apiOrderId);
                } catch (\Throwable $ignored) {}
            }
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json([
            'order'        => $order->id,
            'api_order_id' => $order->api_order_id,
            'status'       => $order->status,
            'service'      => $service->id,
            'quantity'     => $order->quantity,
            'charge'       => number_format((float) $order->charge, 2, '.', ''),
            'link'         => $order->link,
            'created_at'   => $order->created_at->toISOString(),
        ], 201);
    }

    // ── POST /api/v1/cancel-order ────────────────────────────────────────────
    public function cancelOrder(Request $request): JsonResponse
    {
        $request->validate(['order' => 'required|integer']);

        $order = Order::where('id', $request->order)
            ->where('user_id', auth()->id())
            ->with('service.apiProvider')
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found.'], 404);
        }

        if (!in_array($order->status, ['pending', 'processing'])) {
            return response()->json(['error' => 'Only pending or processing orders can be cancelled.'], 422);
        }

        if ($order->api_order_id) {
            $provider = $order->service?->apiProvider;
            if ($provider && $provider->status === 'enabled') {
                try {
                    (new ApiProviderService($provider))->cancelOrder((string) $order->api_order_id);
                } catch (\Throwable $e) {
                    return response()->json(['error' => 'Provider cancel failed: ' . $e->getMessage()], 502);
                }
            }
        }

        $order->update(['status' => 'cancelled']);

        return response()->json([
            'order'  => $order->id,
            'status' => 'cancelled',
        ]);
    }

    // ── POST /api/v1/refill-order ────────────────────────────────────────────
    public function refillOrder(Request $request): JsonResponse
    {
        $request->validate(['order' => 'required|integer']);

        $order = Order::where('id', $request->order)
            ->where('user_id', auth()->id())
            ->with('service.apiProvider')
            ->first();

        if (!$order) {
            return response()->json(['error' => 'Order not found.'], 404);
        }

        if ($order->status !== 'completed' && $order->status !== 'partial') {
            return response()->json(['error' => 'Only completed or partial orders can be refilled.'], 422);
        }

        if (!$order->api_order_id) {
            return response()->json(['error' => 'This order has no provider order ID.'], 422);
        }

        $provider = $order->service?->apiProvider;
        if (!$provider || $provider->status !== 'enabled') {
            return response()->json(['error' => 'Provider is not configured or enabled for this service.'], 422);
        }

        if (!$order->service?->refill) {
            return response()->json(['error' => 'This service does not support refills.'], 422);
        }

        try {
            $result = (new ApiProviderService($provider))->createRefill((string) $order->api_order_id);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Refill failed: ' . $e->getMessage()], 502);
        }

        return response()->json([
            'order'     => $order->id,
            'refill_id' => $result['refill_id'] ?? null,
            'status'    => 'refill_requested',
        ]);
    }
}
