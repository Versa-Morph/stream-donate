<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('streamer_id')->constrained()->onDelete('cascade');
            $table->bigInteger('gross_amount')->comment('Total donasi yang termasuk dalam payout ini');
            $table->bigInteger('platform_fee_amount')->comment('Snapshot fee saat payout dibuat, tidak dihitung ulang');
            $table->bigInteger('net_amount')->comment('gross_amount - platform_fee_amount, diterima streamer');
            $table->string('status', 20)->default('pending')->comment('pending | paid | voided');
            $table->string('bank_name')->comment('Snapshot info bank streamer saat payout dibuat');
            $table->string('bank_account_number');
            $table->string('bank_account_holder');
            $table->string('reference')->nullable()->comment('Referensi transfer bank, diisi saat mark-paid');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->comment('Admin yang membuat payout ini');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['streamer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};
