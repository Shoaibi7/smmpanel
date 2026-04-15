<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequest;
use App\Models\Service;
use App\Models\ApiProvider;
use App\Models\Category;
use App\Services\ApiProviderService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    /**
     * Fetch services from selected API provider (AJAX).
     */
    public function fetchApiServices(Request $request): JsonResponse
    {
        $providerId = $request->get('provider_id');
        $provider = ApiProvider::find($providerId);
        if (!$provider) {
            Log::error('API Service fetch: Provider not found', ['provider_id' => $providerId]);
            return response()->json(['success' => false, 'message' => 'Provider not found.'], 404);
        }
        try {
            Log::info('API Service fetch: Attempting for provider', ['provider_id' => $providerId, 'url' => $provider->api_url]);
            $service = new ApiProviderService($provider);
            $services = $service->getServices();
            Log::info('API Service fetch: Success', ['count' => count($services)]);
            // Add sale_price as null for UI
            foreach ($services as &$srv) {
                $srv['sale_price'] = null;
            }
            Cache::put('api_services_preview_' . $providerId, $services, now()->addMinutes(10));
            return response()->json(['success' => true, 'services' => $services]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch API services', [
                'error' => $e->getMessage(),
                'provider_id' => $providerId,
                'provider_url' => $provider->api_url ?? null,
            ]);
            $cached = Cache::get('api_services_preview_' . $providerId);
            if (is_array($cached) && count($cached) > 0) {
                return response()->json([
                    'success' => true,
                    'services' => $cached,
                    'message' => 'Provider is temporarily unavailable. Showing cached services.',
                    'cached' => true,
                ]);
            }
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

    }

    /**
     * Display a listing of services.
     */
    public function index(Request $request): View
    {
        $query = Service::with(['category', 'apiProvider'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->get('search');
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where('category_id', $request->get('category_id'));
            })
            ->when($request->filled('api_provider_id'), function ($query) use ($request) {
                $query->where('api_provider_id', $request->get('api_provider_id'));
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->get('status'));
            })
            ->when($request->filled('dripfeed'), function ($query) use ($request) {
                if ($request->get('dripfeed') === '1') {
                    $query->where('dripfeed', true)->orWhere('drip_feed', true);
                }
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->get('date_from'));
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->get('date_to'));
            })
            ->orderBy('created_at', 'asc');

        $services = $query->paginate(20);

        $categories = Category::where('is_active', true)->get();
        $providers = ApiProvider::where('status', 'enabled')->get();
        $defaultMarkup = \App\Models\Setting::where('key', 'service_markup_percentage')->value('value') ?? 0;

        return view('admin.services.index', [
            'services' => $services,
            'categories' => $categories,
            'providers' => $providers,
            'defaultMarkup' => $defaultMarkup,
        ]);
    }


    /**
     * Store a newly created service in storage.
     */
    public function store(\Illuminate\Http\Request $request)
    {
        Log::info('--- SERVICE STORE START ---', ['payload' => $request->all()]);
        
        try {
            $data = $request->all();

            // Handle Category (Auto-map)
            if (!$request->has('category_id') && $request->has('category')) {
                $categoryName = $request->input('category');
                $category = Category::updateOrCreate(
                    ['name' => $categoryName],
                    ['slug' => Str::slug($categoryName)]
                );
                $data['category_id'] = $category->id;
                Log::info('Auto-mapped category', ['name' => $categoryName, 'id' => $category->id]);
            }

            // Map and clean up data for the database
            $dbData = [
                'api_provider_id' => $data['api_provider_id'] ?? null,
                'api_service_id'  => $data['api_service_id'] ?? null,
                'category_id'     => $data['category_id'] ?? null,
                'name'            => $data['name'] ?? null,
                'type'            => $data['type'] ?? 'default',
                'description'     => $data['description'] ?? '',
                'rate'            => $data['rate'] ?? 0,
                'provider_rate'   => $data['provider_rate'] ?? ($data['rate'] ?? 0),
                'price_per_k'     => $data['price_per_k'] ?? $data['sale_price'] ?? 0,
                'sale_price'      => $data['sale_price'] ?? 0,
                'price_locked'    => (bool)($data['price_locked'] ?? false),
                'min_qty'         => $data['min_qty'] ?? $data['min_order'] ?? 1,
                'max_qty'         => $data['max_qty'] ?? $data['max_order'] ?? 100000,
                'drip_feed'       => (bool)($data['drip_feed'] ?? $data['dripfeed'] ?? false),
                'refill'          => (bool)($data['refill'] ?? false),
                'cancel'          => (bool)($data['cancel'] ?? false),
                'status'          => $data['status'] ?? 'active',
                'is_active'       => ($data['status'] ?? 'active') === 'active',
                'average_time'    => $data['average_time'] ?? null,
            ];

            // Re-verify required fields
            if (!$dbData['name'] || !$dbData['category_id']) {
                throw new \Exception('Service name and Category are required.');
            }

            $service = Service::create($dbData);
            Log::info('SERVICE CREATED SUCCESSFULLY', ['id' => $service->id]);

            return response()->json([
                'success' => true,
                'message' => 'Service added successfully!',
                'id' => $service->id
            ]);

        } catch (\Exception $e) {
            Log::error('SERVICE STORE FAILED', [
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Database Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service): View
    {
        $providers = \App\Models\ApiProvider::active()->get();
        $categories = \App\Models\Category::orderBy('name')->get();

        return view('admin.services.show', [
            'service' => $service,
            'providers' => $providers,
            'categories' => $categories,
        ]);
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(Service $service): View
    {
        $categories = Category::where('is_active', true)->get();
        $providers = ApiProvider::where('status', 'enabled')->get();

        return view('admin.services.edit', [
            'service' => $service,
            'categories' => $categories,
            'providers' => $providers,
        ]);
    }

    /**
     * Update the specified service in storage.
     */
    public function update(ServiceRequest $request, Service $service)
    {
        try {
            $service->update($request->validated());

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Service updated successfully!',
                    'service' => $service
                ]);
            }

            return redirect()->route('admin.services.show', $service)
                ->with('success', 'Service updated successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to update service: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update service: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update service: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified service from storage.
     */
    public function destroy(Service $service)
    {
        Log::info('Delete request received for service', ['id' => $service->id]);
        try {
            $service->delete();
            Log::info('Service deleted successfully', ['id' => $service->id]);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Service deleted successfully!'
                ]);
            }

            return redirect()->route('admin.services.index')
                ->with('success', 'Service deleted successfully!');
        } catch (\Exception $e) {
            Log::error('Failed to delete service', ['id' => $service?->id, 'message' => $e->getMessage()]);
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete service: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()
                ->with('error', 'Failed to delete service: ' . $e->getMessage());
        }
    }

    /**
     * Show bulk import form for a specific API provider.
     */
    public function bulkImport(Request $request, ApiProvider $provider): View
    {
        $categories = Category::where('is_active', true)->get();

        return view('admin.services.bulk-import', [
            'provider' => $provider,
            'categories' => $categories,
        ]);
    }

    /**
     * Perform bulk import of services from API provider.
     */
    public function bulkImportStore(Request $request, ApiProvider $provider): JsonResponse
    {
        try {
            $request->validate([
                'category_id' => 'required|exists:categories,id',
            ]);

            $count = $provider->fetchServices();

            // Update category for newly imported services
            if ($request->filled('category_id')) {
                $provider->services()
                    ->where('category_id', null)
                    ->update(['category_id' => $request->get('category_id')]);
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully imported {$count} services!",
                'services_count' => $count,
            ]);
        } catch (\Exception $e) {
            Log::error('Bulk import failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Bulk import failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Enable multiple services.
     */
    public function bulkEnable(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'service_ids' => 'required|array',
                'service_ids.*' => 'exists:services,id',
            ]);

            $count = Service::whereIn('id', $request->get('service_ids'))
                ->update(['status' => 'active', 'is_active' => true]);

            return response()->json([
                'success' => true,
                'message' => "Successfully enabled {$count} services!",
                'count' => $count,
            ]);
        } catch (\Exception $e) {
            Log::error('Bulk enable failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Bulk enable failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Disable multiple services.
     */
    public function bulkDisable(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'service_ids' => 'required|array',
                'service_ids.*' => 'exists:services,id',
            ]);

            $count = Service::whereIn('id', $request->get('service_ids'))
                ->update(['status' => 'inactive', 'is_active' => false]);

            return response()->json([
                'success' => true,
                'message' => "Successfully disabled {$count} services!",
                'count' => $count,
            ]);
        } catch (\Exception $e) {
            Log::error('Bulk disable failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Bulk disable failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sync a single service from provider.
     */
    public function syncFromProvider(Service $service): JsonResponse
    {
        try {
            if (!$service->apiProvider) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service is not linked to any API provider.',
                ], 400);
            }

            $service->syncFromProvider();

            return response()->json([
                'success' => true,
                'message' => 'Service synced successfully!',
                'data' => [
                    'name' => $service->name,
                    'rate' => $service->rate,
                    'min_order' => $service->min_order,
                    'max_order' => $service->max_order,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Service sync failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage(),
            ], 500);
        }
    }
}
