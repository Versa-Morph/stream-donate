<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds performance indexes to improve query speed for:
     * - Leaderboard queries (name aggregation)
     * - Date range filtering in reports
     * - Amount-based sorting and filtering
     */
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            // Index for leaderboard queries: GROUP BY name with SUM(amount)
            $table->index('name', 'donations_name_index');
            
            // Composite index for date range queries with streamer (reports, stats)
            $table->index(['streamer_id', 'created_at'], 'donations_streamer_date_index');
            
            // Index for amount-based queries (top donations, sorting)
            $table->index('amount', 'donations_amount_index');
            
            // Composite index for leaderboard: streamer + name + amount
            $table->index(['streamer_id', 'name', 'amount'], 'donations_streamer_name_amount_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropIndex('donations_name_index');
            $table->dropIndex('donations_streamer_date_index');
            $table->dropIndex('donations_amount_index');
            $table->dropIndex('donations_streamer_name_amount_index');
        });
    }
};
