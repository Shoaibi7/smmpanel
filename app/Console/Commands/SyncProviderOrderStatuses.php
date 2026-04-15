<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\ApiProviderService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncProviderOrderStatuses extends Command
{
    protected $signature = 'orders:sync-provider-status {--limit=500 : Max orders to sync per run}';

    protected $description = 'Sync order status from API providers for orders that have api_order_id';

    public function handle(): int
    {
        Log::info('CRON: SyncProviderOrderStatuses started.');
        $limit = (int) $this->option('limit');
        if ($limit <= 0) {
            $limit = 500;
        }

        $orders = Order::query()
            ->whereNotNull('api_order_id')
            ->whereIn('status', ['pending', 'processing', 'partial'])
            ->where('created_at', '<=', now()->subMinutes(5))
            ->with(['service.apiProvider'])
            ->orderBy('id')
            ->limit($limit)
            ->get();

        if ($orders->isEmpty()) {
            $this->info('No orders to sync.');
            return self::SUCCESS;
        }

        $byProvider = $orders->groupBy(fn ($o) => $o->service?->api_provider_id);

        $synced = 0;
        foreach ($byProvider as $providerId => $providerOrders) {
            $provider = $providerOrders->first()?->service?->apiProvider;

            if (!$providerId || !$provider || $provider->status !== 'enabled') {
                continue;
            }

            $svc = new ApiProviderService($provider);

            $chunks = $providerOrders->chunk(100);
            foreach ($chunks as $chunk) {
                $ids = $chunk->pluck('api_order_id')->filter()->values()->all();
                if (count($ids) === 0) {
                    continue;
                }

                try {
                    // Try to sync the entire batch first
                    $statuses = $svc->checkMultipleOrderStatus($ids);
                } catch (\Throwable $e) {
                    // If batch sync fails (likely due to one bad ID), try individual syncs
                    Log::warning('Cron: Batch sync failed, trying individual syncs for provider ' . $providerId, [
                        'error' => $e->getMessage()
                    ]);
                    
                    $statuses = [];
                    foreach ($chunk as $order) {
                        $id = (string) $order->api_order_id;
                        
                        // Skip if the ID is clearly invalid or empty
                        if (empty($id) || !is_numeric($id)) {
                            $order->update(['status' => 'cancelled']);
                            continue;
                        }

                        try {
                            $individualStatus = $svc->checkOrderStatus($id);
                            if ($individualStatus) {
                                $statuses[$id] = $individualStatus;
                            }
                        } catch (\Throwable $individualError) {
                            $msg = $individualError->getMessage();
                            // If the API explicitly says the ID is incorrect, cancel it in our DB
                            if (str_contains(strtolower($msg), 'incorrect order id') || str_contains(strtolower($msg), 'not found')) {
                                $order->update(['status' => 'cancelled']);
                                Log::warning("Cron: Auto-cancelled order #{$order->id} - API ID {$id} not found on provider panel.");
                            }
                        }
                    }
                }

                foreach ($chunk as $order) {
                    $apiId = (string) $order->api_order_id;
                    if ($apiId === '') {
                        continue;
                    }

                    $payload = $statuses[$apiId] ?? null;
                    if (!$payload) {
                        continue;
                    }

                    if (isset($payload['error'])) {
                        Log::warning('Cron: provider status returned error', [
                            'provider_id' => $providerId,
                            'order_id' => $order->id,
                            'api_order_id' => $apiId,
                            'error' => $payload['error'],
                        ]);
                        continue;
                    }

                    $raw = strtolower((string) ($payload['status'] ?? ''));
                    $normalized = $this->normalizeOrderStatus($raw, (string) $order->status);

                    $order->update([
                        'start_count' => $payload['start_count'] ?? $order->start_count,
                        'remains' => $payload['remains'] ?? $order->remains,
                        'status' => $normalized,
                    ]);
                    $synced += 1;
                }
            }
        }

        $this->info("Synced {$synced} orders.");
        return self::SUCCESS;
    }

    private function normalizeOrderStatus(string $raw, string $fallback): string
    {
        $raw = trim($raw);
        if ($raw === '') {
            return $fallback;
        }

        if (str_contains($raw, 'complete')) {
            return 'completed';
        }
        if (str_contains($raw, 'partial')) {
            return 'partial';
        }
        if (str_contains($raw, 'cancel')) {
            return 'cancelled';
        }
        if (str_contains($raw, 'refund')) {
            return 'refunded';
        }
        if (str_contains($raw, 'progress') || str_contains($raw, 'processing') || str_contains($raw, 'in progress')) {
            return 'processing';
        }
        if (str_contains($raw, 'pending')) {
            return 'pending';
        }

        return $fallback;
    }
}

