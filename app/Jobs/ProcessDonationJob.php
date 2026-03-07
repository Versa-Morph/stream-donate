<?php

namespace App\Jobs;

use App\Models\ActivityLog;
use App\Models\AlertQueue;
use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessDonationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Jumlah percobaan ulang jika job gagal (dipakai saat dispatch via queue worker)
     */
    public int $tries = 3;

    /**
     * Backoff antar retry: 5 detik, 30 detik, 60 detik
     */
    public array $backoff = [5, 30, 60];

    public int $timeout = 30;

    public function __construct(
        public readonly Donation $donation
    ) {}

    public function handle(): void
    {
        $streamer = $this->donation->streamer;

        // ── Cleanup expired queue items untuk streamer ini (best-effort) ──
        try {
            AlertQueue::where('streamer_id', $streamer->id)
                ->where('expires_at', '<', now())
                ->delete();
        } catch (\Throwable $e) {
            // Cleanup gagal tidak boleh menghentikan proses alert utama
            Log::warning('ProcessDonationJob: cleanup expired queue gagal', [
                'donation_id' => $this->donation->id,
                'error'       => $e->getMessage(),
            ]);
        }

        // ── Generate seq dan simpan ke alert queue dalam satu transaksi atomic ──
        // lockForUpdate() mencegah race condition jika dua donasi masuk bersamaan
        // untuk streamer yang sama — keduanya tidak bisa baca MAX(seq) secara bersamaan.
        DB::transaction(function () use ($streamer) {
            // Lock baris terakhir untuk streamer ini agar tidak ada dua job
            // yang mengambil nilai seq yang sama secara bersamaan
            $lastSeq = AlertQueue::where('streamer_id', $streamer->id)
                ->lockForUpdate()
                ->max('seq') ?? 0;

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

            // Simpan ke alert queue dengan TTL 15 menit
            // (diperpanjang dari 5 menit agar OBS yang sempat offline masih bisa replay)
            AlertQueue::create([
                'streamer_id' => $streamer->id,
                'donation_id' => $this->donation->id,
                'seq'         => $nextSeq,
                'payload'     => $payload,
                'expires_at'  => now()->addMinutes(15),
                'created_at'  => now(),
            ]);

            // Log activity di dalam transaksi yang sama
            try {
                ActivityLog::log(
                    action: 'donation.queued',
                    description: "Donasi dari {$this->donation->name} sebesar Rp " . number_format($this->donation->amount, 0, ',', '.') . " dimasukkan ke antrian alert",
                    streamerId: $streamer->id,
                    payload: ['donation_id' => $this->donation->id, 'seq' => $nextSeq]
                );
            } catch (\Throwable $e) {
                Log::warning('ProcessDonationJob: gagal log activity', [
                    'donation_id' => $this->donation->id,
                    'error'       => $e->getMessage(),
                ]);
            }
        });
    }

    /**
     * Dipanggil oleh queue worker setelah semua retry habis.
     * Catat ke activity log agar streamer bisa melihat alert mana yang gagal.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessDonationJob: semua retry habis, alert TIDAK akan tampil', [
            'donation_id' => $this->donation->id,
            'donor_name'  => $this->donation->name,
            'amount'      => $this->donation->amount,
            'error'       => $exception->getMessage(),
            'trace'       => $exception->getTraceAsString(),
        ]);

        // Catat ke activity log agar terlihat di dashboard streamer
        try {
            ActivityLog::log(
                action: 'donation.alert_failed',
                description: "Alert donasi dari {$this->donation->name} (Rp " . number_format($this->donation->amount, 0, ',', '.') . ") gagal masuk antrian setelah beberapa percobaan",
                streamerId: $this->donation->streamer_id,
                payload: [
                    'donation_id' => $this->donation->id,
                    'error'       => $exception->getMessage(),
                ]
            );
        } catch (\Throwable $logError) {
            Log::error('ProcessDonationJob: gagal mencatat alert_failed ke activity log', [
                'donation_id' => $this->donation->id,
                'error'       => $logError->getMessage(),
            ]);
        }
    }
}
