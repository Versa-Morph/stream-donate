<?php

use App\Jobs\CleanupExpiredQueueJob;
use App\Jobs\CleanupOrphanedFilesJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Cleanup alert_queues yang sudah expire setiap 5 menit
Schedule::job(new CleanupExpiredQueueJob)->everyFiveMinutes();

// Cleanup orphaned avatar/sound files daily at 3 AM
Schedule::job(new CleanupOrphanedFilesJob)->dailyAt('03:00');
