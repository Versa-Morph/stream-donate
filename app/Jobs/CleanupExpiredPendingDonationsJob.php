<?php

namespace App\Jobs;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CleanupExpiredPendingDonationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function handle(): void
    {
        $bufferMinutes = (int) config('midtrans.snap_expiry_minutes', 60) + 10;

        $stale = Donation::where('status', 'pending')
            ->where('created_at', '<', now()->subMinutes($bufferMinutes))
            ->get();

        foreach ($stale as $donation) {
            if ($donation->media_path) {
                Storage::disk('public')->delete($donation->media_path);
            }
            $donation->update(['status' => 'expired']);
        }

        Log::info("CleanupExpiredPendingDonationsJob: expired {$stale->count()} stale pending donations");
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('CleanupExpiredPendingDonationsJob: semua retry habis, stale pending donations tidak dibersihkan', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
