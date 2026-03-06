<?php

namespace App\Http\Controllers;

use App\Models\AlertQueue;
use App\Models\Streamer;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SseController extends Controller
{
    /**
     * SSE endpoint — stream donasi real-time per streamer
     * Setiap streamer punya stream terpisah, terisolasi by streamer_id
     */
    public function stream(Request $request, string $slug): StreamedResponse
    {
        $streamer = Streamer::where('slug', $slug)->firstOrFail();

        // Validasi API key
        $apiKey = $request->query('key');
        abort_unless($apiKey === $streamer->api_key, 401, 'API key tidak valid.');

        // Ambil Last-Event-ID dari header untuk SSE resume
        $lastEventId = $request->header('Last-Event-ID');
        if ($lastEventId && is_numeric($lastEventId)) {
            $lastSeq = (int) $lastEventId;
        } else {
            // Fresh connect: mulai dari seq terakhir (skip existing items)
            $lastSeq = AlertQueue::where('streamer_id', $streamer->id)->max('seq') ?? 0;
        }

        return new StreamedResponse(function () use ($streamer, $lastSeq) {
            // Set headers SSE
            if (ob_get_level() > 0) {
                ob_end_clean();
            }

            // Kirim stats awal
            $stats = $streamer->buildStats();
            echo "event: stats\n";
            echo "data: " . json_encode($stats) . "\n\n";
            flush();

            $lastPing = time();
            $currentSeq = $lastSeq;

            // Loop SSE — polling setiap 1 detik
            while (true) {
                // Cek koneksi masih aktif
                if (connection_aborted()) {
                    break;
                }

                // Baca alert baru untuk streamer ini saja (terisolasi)
                $newAlerts = AlertQueue::where('streamer_id', $streamer->id)
                    ->where('seq', '>', $currentSeq)
                    ->where('expires_at', '>', now())
                    ->orderBy('seq', 'asc')
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
                }

                // Ping heartbeat setiap 20 detik untuk mencegah timeout
                // Sekaligus kirim stats terbaru agar overlay bisa update config (theme/sound) tanpa refresh
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
     * Endpoint stats JSON — snapshot satu kali (untuk inisialisasi widget)
     */
    public function stats(Request $request, string $slug)
    {
        $streamer = Streamer::where('slug', $slug)->firstOrFail();

        // Validasi API key
        $apiKey = $request->query('key');
        abort_unless($apiKey === $streamer->api_key, 401, 'API key tidak valid.');

        return response()->json($streamer->buildStats());
    }
}
