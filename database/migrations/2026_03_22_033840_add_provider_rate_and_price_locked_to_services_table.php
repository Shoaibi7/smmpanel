<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (!Schema::hasColumn('services', 'provider_rate')) {
                $table->decimal('provider_rate', 15, 5)->nullable()->after('rate');
            }
            if (!Schema::hasColumn('services', 'price_locked')) {
                $table->boolean('price_locked')->default(false)->after('provider_rate');
            }
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            if (Schema::hasColumn('services', 'price_locked')) {
                $table->dropColumn('price_locked');
            }
            if (Schema::hasColumn('services', 'provider_rate')) {
                $table->dropColumn('provider_rate');
            }
        });
    }
};
