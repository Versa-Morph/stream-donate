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
            // Media upload settings
            // JSON format: [{"min_amount": 10000, "max_duration": 15}, {"min_amount": 50000, "max_duration": 30}, ...]
            $table->json('media_duration_tiers')->nullable()->after('alert_max_duration');
            $table->boolean('media_upload_enabled')->default(true)->after('media_duration_tiers');
            $table->integer('media_max_size_mb')->default(50)->after('media_upload_enabled'); // Max file size in MB
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('streamers', function (Blueprint $table) {
            $table->dropColumn(['media_duration_tiers', 'media_upload_enabled', 'media_max_size_mb']);
        });
    }
};
