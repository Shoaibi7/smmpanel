<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use App\Services\ApiProviderService;

class ApiProvider extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'api_name',
        'short_name',
        'api_url',
        'api_key',
        'balance',
        'services_count',
        'status',
        'last_sync_at',
        'currency',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'services_count' => 'integer',
        'last_sync_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationships
     */
    public function services()
    {
        return $this->hasMany(Service::class, 'api_provider_id');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'enabled');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'disabled');
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Accessors & Mutators
     */
    public function getFormattedBalanceAttribute()
    {
        return number_format($this->balance, 2);
    }

    /**
     * API Integration Methods
     */

    /**
     * Test connection to the API provider
     *
     * @return bool
     * @throws \Exception
     */
    public function testConnection(): bool
    {
        try {
            $service = new ApiProviderService($this);
            $balance = $service->getBalance();
            return is_numeric($balance);
        } catch (\Exception $e) {
            \Log::error('API Connection Test Failed for ' . $this->short_name . ': ' . $e->getMessage());
            throw new \Exception('Connection test failed: ' . $e->getMessage());
        }
    }

    /**
     * Sync balance from external API
     *
     * @return decimal
     * @throws \Exception
     */
    public function syncBalance()
    {
        try {
            $service = new ApiProviderService($this);
            $service->clearCache(); // Clear cache to force fresh fetch
            $response = $service->makeRequest('balance');
            $balance = isset($response['balance']) ? $response['balance'] : null;
            $currency = isset($response['currency']) ? $response['currency'] : null;

            // Update the balance and currency in database
            $this->update([
                'balance' => $balance,
                'currency' => $currency,
                'last_sync_at' => now(),
            ]);

            \Log::info('Balance and currency synced for ' . $this->short_name . ': ' . $balance . ' ' . $currency);

            return $balance;
        } catch (\Exception $e) {
            \Log::error('Balance sync failed for ' . $this->short_name . ': ' . $e->getMessage());
            throw new \Exception('Failed to sync balance: ' . $e->getMessage());
        }
    }

    /**
     * Fetch services from external API and sync to database
     *
     * @return int - Number of services synced
     * @throws \Exception
     */
    public function fetchServices(): int
    {
        try {
            $service = new ApiProviderService($this);
            $service->clearCache();
            $apiServices = $service->getServices();

            $syncedCount = 0;

            foreach ($apiServices as $apiService) {
                $category = Category::firstOrCreate(
                    ['slug' => str()->slug($apiService['category'] ?? 'Other')],
                    ['name' => $apiService['category'] ?? 'Other']
                );

                // Find or create service
                $serviceModel = Service::where('api_provider_id', $this->id)
                    ->where('api_service_id', $apiService['service_id'])
                    ->first();

                $rate = isset($apiService['rate']) ? (float) $apiService['rate'] : 0;
                $min = isset($apiService['min']) ? (int) $apiService['min'] : 1;
                $max = isset($apiService['max']) ? (int) $apiService['max'] : 100000;
                $maxInt = 2147483647;
                $min = max(1, min($min, $maxInt));
                $max = max($min, min($max, $maxInt));

                $markupSetting = \App\Models\Setting::where('key', 'service_markup_percentage')->value('value');
                $markup = is_numeric($markupSetting) ? (float) $markupSetting : 0.0;
                $computedSalePrice = $rate * (1 + ($markup / 100));

                $serviceData = [
                    'api_provider_id' => $this->id,
                    'api_service_id' => $apiService['service_id'],
                    'category_id' => $category->id,
                    'name' => $apiService['name'],
                    'description' => $apiService['description'] ?? '',
                    'type' => $this->mapServiceType($apiService['type'] ?? 'default'),
                    'rate' => $rate,
                    'provider_rate' => $rate,
                    'price_per_k' => $rate,
                    'min_qty' => $min,
                    'max_qty' => $max,
                    'min_order' => $min,
                    'max_order' => $max,
                    'drip_feed' => $apiService['dripfeed'] ?? $apiService['drip_feed'] ?? false,
                    'refill' => $apiService['refill'] ?? false,
                    'cancel' => $apiService['cancel'] ?? false,
                    'is_active' => true,
                    'status' => 'active',
                    'average_time' => $apiService['average_time'] ?? null,
                ];

                if ($serviceModel) {
                    if (!$serviceModel->price_locked) {
                        $serviceData['sale_price'] = $computedSalePrice;
                    }
                    $serviceModel->update($serviceData);
                } else {
                    $serviceData['sale_price'] = $computedSalePrice;
                    Service::create($serviceData);
                }

                $syncedCount++;
            }

            // Update service count
            $this->update([
                'services_count' => $this->services()->count(),
                'last_sync_at' => now(),
            ]);

            \Log::info('Services synced for ' . $this->short_name . ': ' . $syncedCount . ' services');

            return $syncedCount;
        } catch (\Exception $e) {
            \Log::error('Services sync failed for ' . $this->short_name . ': ' . $e->getMessage());
            throw new \Exception('Failed to sync services: ' . $e->getMessage());
        }
    }

    public function syncExistingServiceRates(): int
    {
        try {
            $service = new ApiProviderService($this);
            $service->clearCache();
            $apiServices = $service->getServices();

            $apiById = [];
            foreach ($apiServices as $apiService) {
                $id = (string) ($apiService['service_id'] ?? '');
                if ($id !== '') {
                    $apiById[$id] = $apiService;
                }
            }

            if (count($apiById) === 0) {
                $this->update(['last_sync_at' => now()]);
                return 0;
            }

            $existing = Service::query()
                ->where('api_provider_id', $this->id)
                ->whereNotNull('api_service_id')
                ->get();

            $markupSetting = \App\Models\Setting::where('key', 'service_markup_percentage')->value('value');
            $markup = is_numeric($markupSetting) ? (float) $markupSetting : 0.0;

            $updated = 0;
            foreach ($existing as $serviceModel) {
                $api = $apiById[(string) $serviceModel->api_service_id] ?? null;
                if (!$api) {
                    continue;
                }

                $rate = isset($api['rate']) ? (float) $api['rate'] : 0.0;
                $min = isset($api['min']) ? (int) $api['min'] : 1;
                $max = isset($api['max']) ? (int) $api['max'] : 100000;
                $maxInt = 2147483647;
                $min = max(1, min($min, $maxInt));
                $max = max($min, min($max, $maxInt));

                $payload = [
                    'type' => $this->mapServiceType($api['type'] ?? 'default'),
                    'rate' => $rate,
                    'provider_rate' => $rate,
                    'price_per_k' => $rate,
                    'min_qty' => $min,
                    'max_qty' => $max,
                    'min_order' => $min,
                    'max_order' => $max,
                    'drip_feed' => $api['dripfeed'] ?? $api['drip_feed'] ?? false,
                    'refill' => $api['refill'] ?? false,
                    'cancel' => $api['cancel'] ?? false,
                    'average_time' => $api['average_time'] ?? null,
                ];

                if (!$serviceModel->price_locked) {
                    $payload['sale_price'] = $rate * (1 + ($markup / 100));
                }

                $serviceModel->update($payload);
                $updated += 1;
            }

            $this->update(['last_sync_at' => now()]);

            return $updated;
        } catch (\Exception $e) {
            \Log::error('Existing services rates sync failed for ' . $this->short_name . ': ' . $e->getMessage());
            throw new \Exception('Failed to sync service rates: ' . $e->getMessage());
        }
    }

    /**
     * Map external service type to internal type
     *
     * @param string $externalType
     * @return string
     */
    private function mapServiceType(string $externalType): string
    {
        $typeMap = [
            'default' => 'default',
            'subscriptions' => 'subscriptions',
            'comment' => 'custom_comments',
            'comments' => 'custom_comments',
            'mentions' => 'mentions',
            'package' => 'package',
        ];

        return $typeMap[strtolower($externalType)] ?? 'default';
    }
}
