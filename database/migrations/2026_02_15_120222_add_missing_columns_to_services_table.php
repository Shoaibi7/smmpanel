<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Add sale_price if missing
            if (!Schema::hasColumn('services', 'sale_price')) {
                $table->decimal('sale_price', 15, 5)->nullable()->after('rate');
            }

            // Change type from enum to string to support all API providers
            $table->string('type')->default('default')->change();

            // Ensure price_per_k exists
            if (!Schema::hasColumn('services', 'price_per_k')) {
                $table->decimal('price_per_k', 15, 5)->nullable()->after('type');
            }
            
            // Fix min/max qty vs order mapping
            if (!Schema::hasColumn('services', 'min_order')) {
                $table->integer('min_order')->default(0)->after('min_qty');
            }
            if (!Schema::hasColumn('services', 'max_order')) {
                $table->integer('max_order')->default(0)->after('max_qty');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['sale_price']);
            // Reverting enum change is tricky, usually not needed for fix-up migrations
        });
    }
};
