<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banned_words', function (Blueprint $table) {
            $table->id();
            // The actual word stored in lowercase
            $table->string('word', 100);
            // NULL = global (admin-owned), set = streamer-specific
            $table->foreignId('streamer_id')
                  ->nullable()
                  ->constrained('streamers')
                  ->cascadeOnDelete();
            // Who created this entry (admin or streamer user)
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamps();

            // Prevent duplicate words within the same scope
            $table->unique(['word', 'streamer_id']);
            $table->index('streamer_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banned_words');
    }
};
