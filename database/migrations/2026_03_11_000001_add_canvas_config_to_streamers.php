<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('streamers', function (Blueprint $table) {
            $table->json('canvas_config')->nullable()->after('thank_you_message');
        });
    }

    public function down(): void
    {
        Schema::table('streamers', function (Blueprint $table) {
            $table->dropColumn('canvas_config');
        });
    }
};
