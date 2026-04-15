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
        Schema::create('api_providers', function (Blueprint $table) {
            $table->id();
            $table->string('api_name', 255);
            $table->string('short_name', 100)->unique();
            $table->string('api_url', 500);
            $table->text('api_key');
            $table->decimal('balance', 12, 2)->nullable()->default(null);
            $table->integer('services_count')->default(0);
            $table->enum('status', ['enabled', 'disabled'])->default('enabled');
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for better query performance
            $table->index('status');
            $table->index('short_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_providers');
    }
};
