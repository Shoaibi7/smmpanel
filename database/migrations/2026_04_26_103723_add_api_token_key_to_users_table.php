<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('api_token_key', 64)->nullable()->unique()->after('is_blocked');
        });

        // Generate tokens for existing users who don't have one
        \App\Models\User::whereNull('api_token_key')->each(function ($user) {
            $user->update(['api_token_key' => Str::random(64)]);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('api_token_key');
        });
    }
};
