<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('streamer_id')->constrained()->onDelete('cascade');
            $table->string('name', 60)->comment('Nama donatur');
            $table->bigInteger('amount')->comment('Jumlah donasi dalam rupiah');
            $table->string('emoji', 10)->default('💝');
            $table->text('message')->nullable()->comment('Pesan dari donatur, max 200 char');
            $table->string('yt_url')->nullable()->comment('URL YouTube yang diminta');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['streamer_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
