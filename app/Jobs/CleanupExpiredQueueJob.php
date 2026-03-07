<?php

namespace App\Jobs;

use App\Models\AlertQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CleanupExpiredQueueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function handle(): void
    {
        $deleted = AlertQueue::where('expires_at', '<', now())->delete();

        Log::info("CleanupExpiredQueueJob: deleted {$deleted} expired alert queue entries");
    }

    /**
     * Job cleanup gagal setelah semua retry.
     * Jika ini terjadi, baris alert_queue expired akan terakumulasi.
     * Log agar tim tahu ada masalah DB atau resource.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('CleanupExpiredQueueJob: semua retry habis, expired queue rows tidak dibersihkan', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
