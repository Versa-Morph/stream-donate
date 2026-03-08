<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alert_queues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('streamer_id')->constrained()->onDelete('cascade');
            $table->foreignId('donation_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('seq')->comment('Sequence number per streamer, untuk SSE resume');
            $table->json('payload')->comment('Full data donasi untuk dikirim ke OBS');
            $table->timestamp('expires_at')->comment('TTL 5 menit setelah dibuat');
            $table->timestamp('created_at')->nullable();

            $table->index(['streamer_id', 'seq']);
            $table->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alert_queues');
    }
};
