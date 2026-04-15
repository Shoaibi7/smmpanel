<?php

namespace App\Console\Commands;

use App\Models\ApiProvider;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncProviderServices extends Command
{
    protected $signature = 'providers:sync-services {--provider_id= : Sync a single provider id}';

    protected $description = 'Sync rates/min/max/etc from enabled API providers for services already saved in database';

    public function handle(): int
    {
        Log::info('CRON: SyncProviderServices started.');
        $providerId = $this->option('provider_id');

        $query = ApiProvider::query()
            ->where('status', 'enabled')
            ->whereNotNull('api_url')
            ->where('api_url', '!=', '')
            ->whereNotNull('api_key')
            ->where('api_key', '!=', '');

        if ($providerId) {
            $query->whereKey($providerId);
        }

        $providers = $query->get();
        if ($providers->isEmpty()) {
            $this->info('No providers to sync.');
            return self::SUCCESS;
        }

        $total = 0;
        foreach ($providers as $provider) {
            try {
                $count = $provider->syncExistingServiceRates();
                $total += $count;
                $this->info("Synced {$count} services for {$provider->short_name}.");
            } catch (\Throwable $e) {
                Log::error('Cron: services sync failed', [
                    'provider_id' => $provider->id,
                    'short_name' => $provider->short_name,
                    'error' => $e->getMessage(),
                ]);
                $this->error("Failed syncing {$provider->short_name}: {$e->getMessage()}");
            }
        }

        $this->info("Total services processed: {$total}");
        return self::SUCCESS;
    }
}
