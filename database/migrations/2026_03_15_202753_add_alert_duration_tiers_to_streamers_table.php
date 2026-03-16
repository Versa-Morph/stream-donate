<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds two columns to the `streamers` table for per-tier alert duration:
     *
     *  alert_duration_tiers  – JSON array of {from: int (Rp), duration: int (s)} objects.
     *                          Null = use legacy alert_duration column as single-tier fallback.
     *
     *  alert_max_duration    – Maximum allowed duration in seconds a streamer can assign
     *                          to any tier. System cap enforced in controller: max 120 s.
     */
    public function up(): void
    {
        Schema::table('streamers', function (Blueprint $table) {
            $table->json('alert_duration_tiers')
                  ->nullable()
                  ->after('alert_duration')
                  ->comment('Per-tier alert durations: [{from: Rp, duration: seconds}]');

            $table->unsignedSmallInteger('alert_max_duration')
                  ->default(30)
                  ->after('alert_duration_tiers')
                  ->comment('Max alert duration in seconds (user-configurable, system cap 120 s)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('streamers', function (Blueprint $table) {
            $table->dropColumn(['alert_duration_tiers', 'alert_max_duration']);
        });
    }
};
