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
            // Add new columns if they don't exist
            if (!Schema::hasColumn('services', 'api_provider_id')) {
                $table->foreignId('api_provider_id')->nullable()->constrained('api_providers')->onDelete('cascade')->after('id');
            }

            if (!Schema::hasColumn('services', 'api_service_id')) {
                $table->string('api_service_id', 100)->nullable()->index()->after('api_provider_id');
            }

            if (!Schema::hasColumn('services', 'type')) {
                $table->enum('type', [
                    'default',
                    'subscriptions',
                    'custom_comments',
                    'custom_comments_package',
                    'mentions',
                    'mentions_with_hashtags',
                    'mentions_custom_list',
                    'mentions_hashtag',
                    'mentions_user_followers',
                    'mentions_media_likers',
                    'package',
                    'comment_likes',
                    'poll',
                    'comment_replies'
                ])->default('default')->after('category_id');
            }

            if (!Schema::hasColumn('services', 'rate')) {
                $table->decimal('rate', 10, 4)->nullable()->after('price_per_k');
            }

            if (!Schema::hasColumn('services', 'refill')) {
                $table->boolean('refill')->default(false)->after('drip_feed');
            }

            if (!Schema::hasColumn('services', 'cancel')) {
                $table->boolean('cancel')->default(false)->after('refill');
            }

            if (!Schema::hasColumn('services', 'status')) {
                $table->enum('status', ['active', 'inactive'])->default('active')->after('is_active');
            }

            // Add indexes
            if (!Schema::hasColumn('services', 'category_id')) {
                // This shouldn't happen, but check just in case
            } else {
                // Ensure proper indexing
                $table->index('category_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeignKeyIfExists(['api_provider_id']);
            $table->dropIndex(['api_service_id']);
            $table->dropIndex(['category_id']);
            $table->dropColumnIfExists([
                'api_provider_id',
                'api_service_id',
                'type',
                'rate',
                'refill',
                'cancel',
                'status'
            ]);
        });
    }
};
