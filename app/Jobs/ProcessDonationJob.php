<?php

namespace App\Jobs;

use App\Models\AlertQueue;
use App\Models\ActivityLog;
use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessDonationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 30;

    public function __construct(
        public readonly Donation $donation
    ) {}

    public function handle(): void
    {
        $streamer = $this->donation->streamer;

        // Cleanup expired queue items untuk streamer ini
        AlertQueue::where('streamer_id', $streamer->id)
            ->where('expires_at', '<', now())
            ->delete();

        // Dapatkan seq berikutnya untuk streamer ini
        $lastSeq = AlertQueue::where('streamer_id', $streamer->id)->max('seq') ?? 0;
        $nextSeq = $lastSeq + 1;

        // Build payload untuk SSE
        $payload = [
            'id'        => $this->donation->id,
            'seq'       => $nextSeq,
            'name'      => $this->donation->name,
            'amount'    => $this->donation->amount,
            'emoji'     => $this->donation->emoji,
            'msg'       => $this->donation->message,
            'ytUrl'     => $this->donation->yt_url,
            'ytEnabled' => $streamer->yt_enabled && !empty($this->donation->yt_url),
            'duration'  => $streamer->alert_duration,
            'time'      => $this->donation->created_at->toIso8601String(),
        ];

        // Simpan ke alert queue dengan TTL 5 menit
        AlertQueue::create([
            'streamer_id' => $streamer->id,
            'donation_id' => $this->donation->id,
            'seq'         => $nextSeq,
            'payload'     => $payload,
            'expires_at'  => now()->addMinutes(5),
            'created_at'  => now(),
        ]);

        // Log activity
        ActivityLog::log(
            action: 'donation.queued',
            description: "Donasi dari {$this->donation->name} sebesar Rp " . number_format($this->donation->amount, 0, ',', '.') . " dimasukkan ke antrian alert",
            streamerId: $streamer->id,
            payload: ['donation_id' => $this->donation->id, 'seq' => $nextSeq]
        );
    }

    public function failed(\Throwable $exception): void
    {
        \Log::error('ProcessDonationJob failed', [
            'donation_id' => $this->donation->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
