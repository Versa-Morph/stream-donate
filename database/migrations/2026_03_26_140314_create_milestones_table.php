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
        Schema::create('milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('streamer_id')->constrained()->onDelete('cascade');
            $table->string('title')->comment('Judul milestone, contoh: Beli Webcam Baru');
            $table->bigInteger('target_amount')->comment('Target donasi dalam rupiah');
            $table->bigInteger('current_amount')->default(0)->comment('Total donasi terkumpul untuk milestone ini');
            $table->boolean('is_active')->default(true)->comment('Apakah milestone masih aktif');
            $table->boolean('is_completed')->default(false)->comment('Apakah milestone sudah tercapai');
            $table->integer('order')->default(0)->comment('Urutan tampilan milestone');
            $table->string('color')->default('purple')->comment('Warna tema: purple, pink, cyan, green');
            $table->text('description')->nullable()->comment('Deskripsi milestone');
            $table->timestamp('completed_at')->nullable()->comment('Waktu milestone tercapai');
            $table->timestamps();
            
            // Index untuk query performa
            $table->index(['streamer_id', 'is_active']);
            $table->index(['streamer_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('milestones');
    }
};
