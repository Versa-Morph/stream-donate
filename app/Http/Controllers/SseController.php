<?php

namespace App\Http\Controllers;

use App\Models\AlertQueue;
use App\Models\Streamer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SseController extends Controller
{
    /**
     * SSE endpoint — stream donasi real-time per streamer.
     * Setiap streamer punya stream terpisah, terisolasi by streamer_id.
     *
     * Strategi resume:
     *  1. Header Last-Event-ID dikirim oleh browser (EventSource native)
     *  2. Query param ?last_seq= dikirim oleh JS secara manual (fallback saat
     *     EventSource baru dibuat — new EventSource() me-reset Last-Event-ID)
     *  3. Jika tidak ada keduanya → fresh connect, skip alert lama
     */
    public function stream(Request $request, string $slug): StreamedResponse
    {
        $streamer = $this->findStreamerBySlug($slug);

        // Validasi API key dengan hash_equals untuk mencegah timing attack
        $apiKey = $request->query('key');
        abort_unless(
            $apiKey && hash_equals($streamer->api_key, $apiKey),
            401,
            'API key tidak valid.'
        );

        // ── Tentukan posisi awal SSE ──
        // Prioritas: Last-Event-ID header > ?last_seq query param > fresh connect
        $lastEventId  = $request->header('Last-Event-ID');
        $lastSeqParam = $request->query('last_seq');

        if ($lastEventId && is_numeric($lastEventId)) {
            // Browser mengirim Last-Event-ID secara native (auto-reconnect bawaan browser)
            $lastSeq = (int) $lastEventId;
        } elseif ($lastSeqParam && is_numeric($lastSeqParam)) {
            // JS mengirim ?last_seq= secara manual (setelah es.close() + new EventSource())
            // Ini adalah fallback untuk recovery setelah OBS reload / scene switch
            $lastSeq = (int) $lastSeqParam;
        } else {
            // Fresh connect: mulai dari seq terakhir, skip semua item yang sudah ada
            $lastSeq = AlertQueue::where('streamer_id', $streamer->id)->max('seq') ?? 0;
        }

        return new StreamedResponse(function () use ($streamer, $lastSeq) {
            if (ob_get_level() > 0) {
                ob_end_clean();
            }

            // Kirim stats awal
            try {
                $stats = $streamer->buildStats();
                echo "event: stats\n";
                echo "data: " . json_encode($stats) . "\n\n";
                flush();
            } catch (\Throwable $e) {
                Log::warning('SseController: gagal mengirim stats awal', [
                    'streamer_id' => $streamer->id,
                    'error'       => $e->getMessage(),
                ]);
                // Notify client of stats error so JavaScript can handle appropriately
                echo "event: stats_error\n";
                echo "data: " . json_encode(['error' => 'Failed to load initial stats']) . "\n\n";
                flush();
            }

            $lastPing   = time();
            $currentSeq = $lastSeq;

            // ── Loop SSE — polling setiap 1 detik ──
            while (true) {
                // Cek koneksi masih aktif
                if (connection_aborted()) {
                    break;
                }

                try {
                    // Reload streamer untuk memastikan masih ada (tidak dihapus)
                    // Cukup dilakukan di heartbeat, bukan setiap detik
                    // Baca alert baru untuk streamer ini saja (terisolasi)
                    // SAFETY: Limit alerts to prevent overwhelming client on reconnect
                    $newAlerts = AlertQueue::where('streamer_id', $streamer->id)
                        ->where('seq', '>', $currentSeq)
                        ->where('expires_at', '>', now())
                        ->orderBy('seq', 'asc')
                        ->limit(config('pagination.sse_max_alerts_batch', 50))
                        ->get();

                    if ($newAlerts->isNotEmpty()) {
                        foreach ($newAlerts as $alert) {
                            echo "id: {$alert->seq}\n";
                            echo "event: donation\n";
                            echo "data: " . json_encode($alert->payload) . "\n\n";
                            flush();
                            $currentSeq = $alert->seq;
                        }

                        // Kirim stats terbaru setelah ada donasi baru
                        $streamer->refresh();
                        $stats = $streamer->buildStats();
                        echo "event: stats\n";
                        echo "data: " . json_encode($stats) . "\n\n";
                        flush();

                        // Reset heartbeat timer — stats sudah dikirim, tidak perlu ulang di heartbeat
                        $lastPing = time();
                    }

                    // ── Heartbeat setiap 20 detik ──
                    // Mencegah timeout + update config live (theme/sound/duration)
                    if (time() - $lastPing >= 20) {
                        $streamer->refresh();
                        $stats = $streamer->buildStats();
                        echo "event: stats\n";
                        echo "data: " . json_encode($stats) . "\n\n";
                        flush();
                        echo "event: ping\n";
                        echo "data: " . json_encode(['time' => time()]) . "\n\n";
                        flush();
                        $lastPing = time();
                    }
                } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                    // Streamer dihapus saat stream aktif — tutup koneksi dengan bersih
                    Log::info('SseController: streamer tidak ditemukan, menutup stream', [
                        'streamer_id' => $streamer->id,
                    ]);
                    echo "event: stream_error\n";
                    echo "data: " . json_encode(['code' => 'streamer_not_found', 'message' => 'Stream tidak tersedia.']) . "\n\n";
                    flush();
                    break;
                } catch (\Throwable $e) {
                    // Error DB atau error tak terduga — kirim event error ke client
                    // agar overlay tahu harus reconnect, lalu tutup koneksi dengan bersih.
                    // Jangan output HTML error — itu akan merusak format SSE.
                    Log::error('SseController: error di dalam loop SSE', [
                        'streamer_id' => $streamer->id,
                        'error'       => $e->getMessage(),
                    ]);
                    echo "event: stream_error\n";
                    echo "data: " . json_encode(['code' => 'internal_error', 'message' => 'Terjadi kesalahan. Reconnecting...']) . "\n\n";
                    flush();
                    break;
                }

                sleep(1);
            }
        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Connection'        => 'keep-alive',
        ]);
    }

    /**
     * Endpoint stats JSON — snapshot satu kali (untuk inisialisasi widget).
     *
     * @param \Illuminate\Http\Request $request
     * @param string $slug Streamer slug identifier
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats(Request $request, string $slug): JsonResponse
    {
        $streamer = $this->findStreamerBySlug($slug);

        // Validasi API key dengan hash_equals untuk mencegah timing attack
        $apiKey = $request->query('key');
        abort_unless(
            $apiKey && hash_equals($streamer->api_key, $apiKey),
            401,
            'API key tidak valid.'
        );

        try {
            return response()->json($streamer->buildStats());
        } catch (\Throwable $e) {
            Log::error('SseController: gagal mengambil stats', [
                'streamer_id' => $streamer->id,
                'error'       => $e->getMessage(),
            ]);

            return response()->json([
                'error' => 'Gagal mengambil data stats.',
            ], 500);
        }
    }
}
