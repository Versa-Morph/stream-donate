<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->string('status', 20)->default('pending')->after('ip_address')
                ->comment('pending | paid | failed | expired');
            $table->string('payment_reference')->nullable()->unique()->after('status')
                ->comment('Midtrans order_id, format TRX-{donation_id}');
            $table->string('payment_type', 40)->nullable()->after('payment_reference')
                ->comment('qris, gopay, bank_transfer, dll — dari webhook');
            $table->timestamp('paid_at')->nullable()->after('payment_type');
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn(['status', 'payment_reference', 'payment_type', 'paid_at']);
        });
    }
};
