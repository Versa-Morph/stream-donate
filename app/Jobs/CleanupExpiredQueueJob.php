<?php

namespace App\Jobs;

use App\Models\AlertQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CleanupExpiredQueueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $deleted = AlertQueue::where('expires_at', '<', now())->delete();

        \Log::info("CleanupExpiredQueueJob: deleted {$deleted} expired alert queue entries");
    }
}
