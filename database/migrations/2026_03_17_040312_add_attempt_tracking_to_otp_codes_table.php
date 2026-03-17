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
        Schema::table('otp_codes', function (Blueprint $table) {
            $table->string('code', 8)->change();
            $table->unsignedTinyInteger('attempt_count')->default(0)->after('used_at');
            $table->timestamp('locked_until')->nullable()->after('attempt_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('otp_codes', function (Blueprint $table) {
            $table->string('code', 6)->change();
            $table->dropColumn(['attempt_count', 'locked_until']);
        });
    }
};
