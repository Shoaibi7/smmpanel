<?php

namespace App\Services;

use App\Models\ApiProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ApiProviderService
{
    private ApiProvider $provider;
    private Client $httpClient;
    private const API_TIMEOUT = 120; // seconds
    private const CACHE_BALANCE_TTL = 300; // 5 minutes
    private const CACHE_SERVICES_TTL = 3600; // 1 hour

    public function __construct(ApiProvider $provider)
    {
        $this->provider = $provider;
        $this->httpClient = new Client();
    }

    /**
     * Test connection to the API provider
     *
     * @return bool
     * @throws \Exception
     */
    public function testConnection(): bool
    {
        try {
            $balance = $this->getBalance();
            return is_numeric($balance);
        } catch (\Exception $e) {
            Log::error('API connection test failed for ' . $this->provider->short_name, [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Get current balance from API
     *
     * @return float
     * @throws \Exception
     */
    public function getBalance(): float
    {
        // Check cache first
        $cacheKey = 'api_provider_balance_' . $this->provider->id;
        if ($cached = Cache::get($cacheKey)) {
            return $cached;
        }

        try {
            $response = $this->makeRequest('balance');

            if (isset($response['balance'])) {
                $balance = floatval($response['balance']);
                Cache::put($cacheKey, $balance, self::CACHE_BALANCE_TTL);
                return $balance;
            }

            throw new \Exception('Invalid balance response format');
        } catch (\Exception $e) {
            Log::error('Failed to fetch balance for ' . $this->provider->short_name, [
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Failed to fetch balance: ' . $e->getMessage());
        }
    }

    /**
     * Get services list from API
     *
     * @return array
     * @throws \Exception
     */
    public function getServices(): array
    {
        try {
            $response = $this->makeRequest('services');
            
            if (!is_array($response)) {
                throw new \Exception('Invalid services response format');
            }
            
            $normalizedServices = [];
            foreach ($response as $service) {
                // Perfect Panel check: sometimes errors are inside the array
                if (isset($service['error'])) {
                    throw new \Exception('API Error: ' . $service['error']);
                }
                $normalizedServices[] = $this->normalizeServiceResponse($service);
            }
            return $normalizedServices;
        } catch (\Exception $e) {
            throw new \Exception('Failed to fetch services: ' . $e->getMessage());
        }
    }

    /**
     * Place an order with the provider
     *
     * @param string $serviceId - Service ID from provider
     * @param string $link - Target link/URL
     * @param int $quantity - Order quantity
     * @param array $additionalParams - Additional parameters
     * @return array - Response with order ID
     * @throws \Exception
     */
    public function createOrder(string $serviceId, string $link, int $quantity, array $additionalParams = []): array
    {
        try {
            if (array_key_exists('comments', $additionalParams) && is_array($additionalParams['comments'])) {
                $additionalParams['comments'] = implode("\n", array_values($additionalParams['comments']));
            }

            $url = $additionalParams['url'] ?? $additionalParams['link'] ?? $link;

            $params = array_merge([
                'action' => 'add',
                'service' => $serviceId,
                'link' => $link,
                'url' => $url,
                'quantity' => $quantity,
            ], $additionalParams);

            $response = $this->makeRequest(null, $params);

            if (isset($response['order'])) {
                return [
                    'success' => true,
                    'order_id' => $response['order'],
                    'response' => $response
                ];
            }

            if (isset($response['error'])) {
                throw new \Exception('API Error: ' . $response['error']);
            }

            throw new \Exception('Invalid order response format');
        } catch (\Exception $e) {
            Log::error('Failed to create order with ' . $this->provider->short_name, [
                'error' => $e->getMessage(),
                'service_id' => $serviceId
            ]);
            throw new \Exception('Failed to create order: ' . $e->getMessage());
        }
    }

    /**
     * Check order status
     *
     * @param string $orderId - Order ID to check
     * @return array - Order status information
     * @throws \Exception
     */
    public function checkOrderStatus(string $orderId): array
    {
        try {
            $response = $this->makeRequest('status', ['order' => $orderId]);

            return $this->normalizeStatusResponse($response);
        } catch (\Exception $e) {
            Log::error('Failed to check order status with ' . $this->provider->short_name, [
                'error' => $e->getMessage(),
                'order_id' => $orderId
            ]);
            throw new \Exception('Failed to check order status: ' . $e->getMessage());
        }
    }

    /**
     * Check multiple orders status
     *
     * @param array $orderIds - Array of order IDs
     * @return array - Array of order statuses
     * @throws \Exception
     */
    public function checkMultipleOrderStatus(array $orderIds): array
    {
        try {
            $orders = implode(',', $orderIds);
            $response = $this->makeRequest('status', ['orders' => $orders]);

            if (!is_array($response)) {
                return [
                    (string) ($orderIds[0] ?? '0') => $this->normalizeStatusResponse((array) $response),
                ];
            }

            $results = [];
            foreach ($response as $orderId => $payload) {
                if (is_array($payload) && isset($payload['error'])) {
                    $results[(string) $orderId] = [
                        'error' => $payload['error'],
                    ];
                    continue;
                }

                $results[(string) $orderId] = $this->normalizeStatusResponse((array) $payload);
            }

            return $results;
        } catch (\Exception $e) {
            Log::error('Failed to check multiple orders status with ' . $this->provider->short_name, [
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Failed to check orders status: ' . $e->getMessage());
        }
    }

    /**
     * Create a refill for an order
     *
     * @param string $orderId - Original order ID
     * @return array - Refill information
     * @throws \Exception
     */
    public function createRefill(string $orderId): array
    {
        try {
            $response = $this->makeRequest(null, [
                'action' => 'refill',
                'order' => $orderId
            ]);

            if (isset($response['refill'])) {
                return [
                    'success' => true,
                    'refill_id' => $response['refill'],
                    'response' => $response
                ];
            }

            if (isset($response['error'])) {
                throw new \Exception('API Error: ' . $response['error']);
            }

            throw new \Exception('Invalid refill response format');
        } catch (\Exception $e) {
            Log::error('Failed to create refill with ' . $this->provider->short_name, [
                'error' => $e->getMessage(),
                'order_id' => $orderId
            ]);
            throw new \Exception('Failed to create refill: ' . $e->getMessage());
        }
    }

    /**
     * Check refill status
     *
     * @param string $refillId - Refill ID to check
     * @return array - Refill status information
     * @throws \Exception
     */
    public function checkRefillStatus(string $refillId): array
    {
        try {
            $response = $this->makeRequest(null, [
                'action' => 'refill_status',
                'refill' => $refillId
            ]);

            return $response;
        } catch (\Exception $e) {
            Log::error('Failed to check refill status with ' . $this->provider->short_name, [
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Failed to check refill status: ' . $e->getMessage());
        }
    }

    /**
     * Cancel an order
     *
     * @param string $orderId - Order ID to cancel
     * @return array - Cancellation response
     * @throws \Exception
     */
    public function cancelOrder(string $orderId): array
    {
        try {
            $response = $this->makeRequest(null, [
                'action' => 'cancel',
                'order' => $orderId
            ]);

            return [
                'success' => true,
                'response' => $response
            ];
        } catch (\Exception $e) {
            Log::error('Failed to cancel order with ' . $this->provider->short_name, [
                'error' => $e->getMessage(),
                'order_id' => $orderId
            ]);
            throw new \Exception('Failed to cancel order: ' . $e->getMessage());
        }
    }

    /**
     * Make HTTP request to API
     *
     * @param string|null $action - Action parameter (optional)
     * @param array $params - Additional parameters
     * @return array - Response body
     * @throws \Exception
     */
    public function makeRequest(?string $action = null, array $params = []): array
    {
        try {
            $url = $this->provider->api_url;
            $baseParams = [
                'key' => $this->provider->api_key,
            ];

            if ($action) {
                $baseParams['action'] = $action;
            }


            $allParams = array_merge($baseParams, $params);

            // Make POST request as Form Params (Standard for SMM APIs)
            $response = $this->httpClient->post($url, [
                'form_params' => $allParams,
                'timeout' => self::API_TIMEOUT,
                'connect_timeout' => self::API_TIMEOUT,
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'application/json',
                ],
            ]);

            $body = $response->getBody()->getContents();
            $decoded = json_decode($body, true);

            if ($decoded === null) {
                Log::error('Invalid JSON response from API', ['url' => $url, 'body' => $body]);
                throw new \Exception('Invalid JSON response from API');
            }

            // Check for API errors
            if (isset($decoded['error']) && $decoded['error']) {
                throw new \Exception('API returned error: ' . $decoded['error']);
            }

            return $decoded;
        } catch (GuzzleException $e) {
            Log::error('HTTP Request failed', ['message' => $e->getMessage()]);
            throw new \Exception('HTTP Request failed: ' . $e->getMessage());
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Normalize service response from API
     *
     * @param array $service
     * @return array
     */
    private function normalizeServiceResponse(array $service): array
    {
        return [
            'service_id' => $service['service'] ?? $service['id'] ?? '',
            'name' => $service['name'] ?? '',
            'type' => $service['type'] ?? 'default',
            'category' => $service['category'] ?? 'Other',
            'rate' => floatval($service['rate'] ?? 0),
            'min' => intval($service['min'] ?? 1),
            'max' => intval($service['max'] ?? 100000),
            'dripfeed' => boolval($service['dripfeed'] ?? false),
            'refill' => boolval($service['refill'] ?? false),
            'cancel' => boolval($service['cancel'] ?? false),
            'description' => $service['description'] ?? $service['desc'] ?? '',
            'average_time' => $service['average_time'] ?? 0,
        ];
    }

    /**
     * Normalize status response from API
     *
     * @param array $status
     * @return array
     */
    private function normalizeStatusResponse(array $status): array
    {
        return [
            'charge' => floatval($status['charge'] ?? 0),
            'start_count' => intval($status['start_count'] ?? 0),
            'status' => $status['status'] ?? 'Unknown',
            'remains' => intval($status['remains'] ?? 0),
            'currency' => $status['currency'] ?? 'USD',
        ];
    }

    /**
     * Clear cache for this provider
     *
     * @return void
     */
    public function clearCache(): void
    {
        Cache::forget('api_provider_balance_' . $this->provider->id);
        Cache::forget('api_provider_services_' . $this->provider->id);
    }
}
