<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    // \Illuminate\Support\Facades\Log::info('CRON: Heartbeat - Scheduler is alive.');
})->everyMinute();

Schedule::command('orders:sync-provider-status')
    ->everyMinute()
    ->onSuccess(function () {
        // \Illuminate\Support\Facades\Log::info('CRON: orders:sync-provider-status completed successfully.');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('CRON: orders:sync-provider-status failed.');
    })
    ->withoutOverlapping();

Schedule::command('providers:sync-services')
    ->everyMinute()
    ->onSuccess(function () {
        // \Illuminate\Support\Facades\Log::info('CRON: providers:sync-services completed successfully.');
    })
    ->onFailure(function () {
        \Illuminate\Support\Facades\Log::error('CRON: providers:sync-services failed.');
    })
    ->withoutOverlapping();
