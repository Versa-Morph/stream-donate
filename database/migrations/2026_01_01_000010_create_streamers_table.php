<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('streamers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('slug')->unique()->comment('URL unik streamer, misal: budi, siti123');
            $table->string('display_name');
            $table->string('api_key', 64)->unique()->comment('Key untuk validasi OBS widget & push endpoint');
            $table->text('bio')->nullable();
            $table->string('avatar')->nullable();

            // Alert settings
            $table->integer('alert_duration')->default(8000)->comment('ms, durasi tampil alert');
            $table->boolean('yt_enabled')->default(true)->comment('Izinkan request YouTube');
            $table->boolean('sound_enabled')->default(true);
            $table->string('alert_theme')->default('default');

            // Milestone settings
            $table->string('milestone_title')->default('Target Stream Hari Ini');
            $table->bigInteger('milestone_target')->default(1000000);
            $table->boolean('milestone_reset')->default(false);

            // Leaderboard settings
            $table->string('leaderboard_title')->default('Top Donatur');
            $table->integer('leaderboard_count')->default(5);

            // Donation settings
            $table->integer('min_donation')->default(1000)->comment('Minimum donasi dalam rupiah');
            $table->boolean('is_accepting_donation')->default(true);
            $table->string('thank_you_message')->default('Terima kasih atas donasi kamu!');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('streamers');
    }
};
