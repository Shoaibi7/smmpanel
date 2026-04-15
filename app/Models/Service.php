<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use App\Services\ApiProviderService;

class Service extends Model
{
    protected $fillable = [
        'api_provider_id',
        'api_service_id',
        'category_id',
        'name',
        'description',
        'type',
        'price_per_k',
        'rate',
        'min_qty',
        'min_order',
        'max_qty',
        'max_order',
        'is_active',
        'status',
        'drip_feed',
        'refill',
        'cancel',
        'sale_price',
        'average_time',
        'provider_rate',
        'price_locked',
    ];

    protected $casts = [
        'price_per_k' => 'decimal:5',
        'rate' => 'decimal:4',
        'is_active' => 'boolean',
        'drip_feed' => 'boolean',
        'refill' => 'boolean',
        'cancel' => 'boolean',
        'sale_price' => 'decimal:5',
        'provider_rate' => 'decimal:5',
        'price_locked' => 'boolean',
    ];

    /**
     * Relationships
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function apiProvider()
    {
        return $this->belongsTo(ApiProvider::class, 'api_provider_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')->orWhere('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive')->orWhere('is_active', false);
    }

    public function scopeByProvider($query, $providerId)
    {
        return $query->where('api_provider_id', $providerId);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeDripfeedEnabled($query)
    {
        return $query->where('dripfeed', true)->orWhere('drip_feed', true);
    }

    /**
     * Accessors
     */
    public function getFormattedRateAttribute()
    {
        return number_format($this->rate ?? $this->price_per_k, 4);
    }

    public function getFinalPriceAttribute()
    {
        return $this->calculateFinalPrice();
    }

    /**
     * Calculate final price with markup
     *
     * @param float $markup - Markup percentage (optional)
     * @return float
     */
    public function calculateFinalPrice($markup = null)
    {
        $basePrice = $this->rate ?? $this->price_per_k ?? 0;

        if ($markup === null) {
            // Get markup from settings if available
            $setting = \App\Models\Setting::where('key', 'service_markup_percentage')->first();
            $markup = $setting?->value ?? 0;
        }

        return $basePrice * (1 + ($markup / 100));
    }

    /**
     * API Integration Methods
     */

    /**
     * Check service availability with provider
     *
     * @return bool
     */
    public function checkAvailability(): bool
    {
        if (!$this->api_provider_id || !$this->api_service_id) {
            return true; // Assume available if no provider/service mapping
        }

        try {
            if (!$this->apiProvider) {
                return false;
            }

            $providerService = new ApiProviderService($this->apiProvider);
            $services = $providerService->getServices();

            foreach ($services as $service) {
                if ($service['service_id'] === $this->api_service_id) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            \Log::warning('Service availability check failed for ' . $this->name . ': ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Sync service details from provider
     *
     * @return void
     * @throws \Exception
     */
    public function syncFromProvider()
    {
        if (!$this->api_provider_id || !$this->api_service_id) {
            throw new \Exception('Service not linked to API provider');
        }

        try {
            $providerService = new ApiProviderService($this->apiProvider);
            $services = $providerService->getServices();

            foreach ($services as $apiService) {
                if ($apiService['service_id'] === $this->api_service_id) {
                    $this->update([
                        'name' => $apiService['name'] ?? $this->name,
                        'description' => $apiService['description'] ?? $this->description,
                        'rate' => $apiService['rate'] ?? $this->rate,
                        'min_order' => $apiService['min'] ?? $this->min_order,
                        'max_order' => $apiService['max'] ?? $this->max_order,
                        'dripfeed' => $apiService['dripfeed'] ?? $this->dripfeed,
                        'refill' => $apiService['refill'] ?? $this->refill,
                        'cancel' => $apiService['cancel'] ?? $this->cancel,
                        'average_time' => $apiService['average_time'] ?? $this->average_time,
                    ]);

                    \Log::info('Service synced: ' . $this->name);
                    return;
                }
            }

            throw new \Exception('Service not found in provider\'s service list');
        } catch (\Exception $e) {
            \Log::error('Service sync failed for ' . $this->name . ': ' . $e->getMessage());
            throw new \Exception('Failed to sync service: ' . $e->getMessage());
        }
    }
}
