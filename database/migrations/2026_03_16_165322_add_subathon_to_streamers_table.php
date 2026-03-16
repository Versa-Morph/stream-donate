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
            $table->boolean('subathon_enabled')->default(false)->after('alert_max_duration');
            $table->integer('subathon_duration_minutes')->default(60)->after('subathon_enabled');
            $table->json('subathon_additional_values')->nullable()->after('subathon_duration_minutes');
            $table->integer('subathon_current_minutes')->default(0)->after('subathon_additional_values');
            $table->timestamp('subathon_last_updated')->nullable()->after('subathon_current_minutes');
        });
    }

    public function down(): void
    {
        Schema::table('streamers', function (Blueprint $table) {
            $table->dropColumn([
                'subathon_enabled',
                'subathon_duration_minutes',
                'subathon_additional_values',
                'subathon_current_minutes',
                'subathon_last_updated',
            ]);
        });
    }
};
