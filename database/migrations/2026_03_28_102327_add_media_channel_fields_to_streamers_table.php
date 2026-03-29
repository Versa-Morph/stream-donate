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
        Schema::table('streamers', function (Blueprint $table) {
            $table->boolean('tiktok_enabled')->default(false)->after('yt_enabled');
            $table->boolean('instagram_enabled')->default(false)->after('tiktok_enabled');
            $table->boolean('twitter_enabled')->default(false)->after('instagram_enabled');
            $table->boolean('spotify_enabled')->default(false)->after('twitter_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('streamers', function (Blueprint $table) {
            $table->dropColumn(['tiktok_enabled', 'instagram_enabled', 'twitter_enabled', 'spotify_enabled']);
        });
    }
};
