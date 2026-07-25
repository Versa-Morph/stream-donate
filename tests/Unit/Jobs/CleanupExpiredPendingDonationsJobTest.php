<?php

namespace Tests\Unit\Jobs;

use App\Jobs\CleanupExpiredPendingDonationsJob;
use App\Models\Donation;
use App\Models\Streamer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CleanupExpiredPendingDonationsJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_stale_pending_donation_is_expired_and_media_deleted(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('donations/media/old.mp3', 'fake-audio-content');

        $streamer = Streamer::factory()->create();
        $stale = Donation::factory()->for($streamer)->create([
            'status' => 'pending',
            'media_path' => 'donations/media/old.mp3',
            'created_at' => now()->subMinutes(config('midtrans.snap_expiry_minutes', 60) + 20),
        ]);

        (new CleanupExpiredPendingDonationsJob())->handle();

        $stale->refresh();
        $this->assertSame('expired', $stale->status);
        Storage::disk('public')->assertMissing('donations/media/old.mp3');
    }

    public function test_fresh_pending_donation_is_untouched(): void
    {
        $streamer = Streamer::factory()->create();
        $fresh = Donation::factory()->for($streamer)->create([
            'status' => 'pending',
            'created_at' => now()->subMinutes(5),
        ]);

        (new CleanupExpiredPendingDonationsJob())->handle();

        $fresh->refresh();
        $this->assertSame('pending', $fresh->status);
    }
}
