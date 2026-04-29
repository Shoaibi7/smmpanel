<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenerateApiTokensSeeder extends Seeder
{
    public function run(): void
    {
        $users = DB::table('users')->whereNull('api_token_key')->get();

        foreach ($users as $user) {
            DB::table('users')
                ->where('id', $user->id)
                ->update(['api_token_key' => Str::random(64)]);
        }

        echo "Generated tokens for {$users->count()} users.\n";
    }
}
