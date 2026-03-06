<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Streamer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Admin default
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@streamdonate.local',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Buat Streamer demo
        $streamerUser = User::create([
            'name' => 'Demo Streamer',
            'email' => 'streamer@streamdonate.local',
            'password' => Hash::make('streamer123'),
            'role' => 'streamer',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        Streamer::create([
            'user_id' => $streamerUser->id,
            'slug' => 'demo',
            'display_name' => 'Demo Streamer',
            'api_key' => Streamer::generateApiKey(),
            'bio' => 'Streamer demo untuk testing StreamDonate',
            'milestone_title' => 'Target Stream Hari Ini',
            'milestone_target' => 1000000,
            'leaderboard_title' => 'Top Donatur',
            'leaderboard_count' => 5,
            'alert_duration' => 8000,
            'yt_enabled' => true,
            'min_donation' => 1000,
            'thank_you_message' => 'Terima kasih atas donasi kamu!',
        ]);

        $this->command->info('');
        $this->command->info('=== Akun Default ===');
        $this->command->info('Admin    : admin@streamdonate.local / admin123');
        $this->command->info('Streamer : streamer@streamdonate.local / streamer123');
        $this->command->info('Demo URL : /demo (form donasi publik)');
    }
}
