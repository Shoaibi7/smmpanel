<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiProviderRequest;
use App\Models\ApiProvider;
use App\Services\ApiProviderService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class ApiProviderController extends Controller
{
    /**
     * Display a listing of API providers.
     */
    public function index(Request $request): View
    {
        $providers = ApiProvider::query()
            ->withCount(['services as db_services_count'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->get('search');
                $query->where('api_name', 'like', "%{$search}%")
                    ->orWhere('short_name', 'like', "%{$search}%");
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->get('status'));
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.api-providers.index', [
            'providers' => $providers,
        ]);
    }

    /**
     * Show the form for creating a new API provider.
     */
    public function create(): View
    {
        return view('admin.api-providers.create');
    }

    /**
     * Store a newly created API provider in storage.
     */
    public function store(ApiProviderRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            $provider = ApiProvider::create($data);

            // If api_url and api_key are present, fetch balance and currency
            if (!empty($provider->api_url) && !empty($provider->api_key)) {
                try {
                    $service = new ApiProviderService($provider);
                    $response = $service->makeRequest('balance');
                    if (isset($response['balance'])) {
                        $provider->balance = $response['balance'];
                    }
                    if (isset($response['currency'])) {
                        $provider->currency = $response['currency'];
                    }
                    $provider->save();
                    $message = 'API Provider created successfully and balance/currency fetched!';
                } catch (\Exception $e) {
                    $message = 'API Provider created, but failed to fetch balance/currency: ' . $e->getMessage();
                    \Log::warning('Balance/currency fetch failed for new provider: ' . $e->getMessage());
                }
            } else {
                $message = 'API Provider created successfully!';
            }

            return redirect()->route('admin.api-providers.show', $provider)
                ->with('success', $message);
        } catch (\Exception $e) {
            \Log::error('Failed to create API provider: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create API provider: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified API provider.
     */
    public function show(ApiProvider $apiProvider): View
    {
        $apiProvider->loadCount(['services as db_services_count']);
        $services = $apiProvider->services()
            ->paginate(20);

        return view('admin.api-providers.show', [
            'provider' => $apiProvider,
            'services' => $services,
        ]);
    }

    /**
     * Show the form for editing the specified API provider.
     */
    public function edit(ApiProvider $apiProvider): View
    {
        $apiProvider->loadCount(['services as db_services_count']);
        return view('admin.api-providers.edit', [
            'provider' => $apiProvider,
        ]);
    }

    /**
     * Update the specified API provider in storage.
     */
    public function update(ApiProviderRequest $request, ApiProvider $apiProvider): RedirectResponse
    {
        try {
            $apiProvider->update($request->validated());

            // If api_url and api_key are present, fetch balance and currency
            if (!empty($apiProvider->api_url) && !empty($apiProvider->api_key)) {
                try {
                    $service = new ApiProviderService($apiProvider);
                    $response = $service->makeRequest('balance');
                    if (isset($response['balance'])) {
                        $apiProvider->balance = $response['balance'];
                    }
                    if (isset($response['currency'])) {
                        $apiProvider->currency = $response['currency'];
                    }
                    $apiProvider->save();
                } catch (\Exception $e) {
                    \Log::warning('Balance/currency fetch failed for provider update: ' . $e->getMessage());
                }
            }

            return redirect()->route('admin.api-providers.show', $apiProvider)
                ->with('success', 'API Provider updated successfully!');
        } catch (\Exception $e) {
            \Log::error('Failed to update API provider: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update API provider: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified API provider from storage.
     */
    public function destroy(ApiProvider $apiProvider): RedirectResponse
    {
        try {
            $apiProvider->delete();

            return redirect()->route('admin.api-providers.index')
                ->with('success', 'API Provider deleted successfully!');
        } catch (\Exception $e) {
            \Log::error('Failed to delete API provider: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete API provider: ' . $e->getMessage());
        }
    }

    /**
     * Toggle API provider status (enable/disable).
     */
    public function toggle(ApiProvider $apiProvider): JsonResponse
    {
        try {
            $newStatus = $apiProvider->status === 'enabled' ? 'disabled' : 'enabled';
            $apiProvider->update(['status' => $newStatus]);

            return response()->json([
                'success' => true,
                'message' => 'Provider status updated successfully!',
                'status' => $newStatus,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to toggle API provider status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update provider status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sync balance from API provider.
     */
    public function syncBalance(ApiProvider $apiProvider): JsonResponse
    {
        try {
            $balance = $apiProvider->syncBalance();

            return response()->json([
                'success' => true,
                'message' => 'Balance synced successfully!',
                'balance' => number_format($balance, 2),
                'formatted_balance' => '$' . number_format($balance, 2),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to sync balance: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync balance: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sync services from API provider.
     */
    public function syncServices(ApiProvider $apiProvider): JsonResponse
    {
        try {
            $count = $apiProvider->syncExistingServiceRates();

            return response()->json([
                'success' => true,
                'message' => "Successfully synced {$count} services!",
                'services_count' => $count,
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to sync services: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync services: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test connection to the API provider.
     */
    public function testConnection(ApiProvider $apiProvider): JsonResponse
    {
        try {
            $service = new ApiProviderService($apiProvider);
            $isConnected = $service->testConnection();

            if ($isConnected) {
                return response()->json([
                    'success' => true,
                    'message' => 'Connection successful! API credentials are valid.',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Connection failed. Please check your API credentials.',
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection test failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Restore a soft-deleted API provider.
     */
    public function restore(ApiProvider $apiProvider): RedirectResponse
    {
        try {
            $apiProvider->restore();

            return redirect()->route('admin.api-providers.index')
                ->with('success', 'API Provider restored successfully!');
        } catch (\Exception $e) {
            \Log::error('Failed to restore API provider: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to restore API provider: ' . $e->getMessage());
        }
    }
}
