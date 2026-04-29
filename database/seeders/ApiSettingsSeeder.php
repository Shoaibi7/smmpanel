<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;
use Illuminate\Support\Str;

class ApiSettingsSeeder extends Seeder
{
    public function run(): void
    {
        Setting::firstOrCreate(
            ['key' => 'api_endpoint'],
            ['value' => url('api/v1')]
        );

        Setting::firstOrCreate(
            ['key' => 'api_token'],
            ['value' => Str::random(64)]
        );
    }
}
