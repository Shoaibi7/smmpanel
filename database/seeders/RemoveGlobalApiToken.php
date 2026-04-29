<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RemoveGlobalApiToken extends Seeder
{
    public function run(): void
    {
        DB::table('settings')->where('key', 'api_token')->delete();
    }
}
