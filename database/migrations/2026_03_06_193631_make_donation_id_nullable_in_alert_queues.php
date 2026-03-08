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
        Schema::table('alert_queues', function (Blueprint $table) {
            // Make donation_id nullable to support test alerts (no real donation row)
            $table->unsignedBigInteger('donation_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('alert_queues', function (Blueprint $table) {
            $table->unsignedBigInteger('donation_id')->nullable(false)->change();
        });
    }
};
