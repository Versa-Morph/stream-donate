<?php

use App\Jobs\CleanupExpiredQueueJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Cleanup alert_queues yang sudah expire setiap 5 menit
Schedule::job(new CleanupExpiredQueueJob)->everyFiveMinutes();
