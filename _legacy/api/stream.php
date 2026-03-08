<?php
/**
 * api/stream.php
 * Server-Sent Events (SSE) endpoint — Broadcast Model.
 *
 * Events yang dikirim:
 *  - ping     : heartbeat setiap 20 detik
 *  - donation : donasi baru (id: seq sebagai Last-Event-ID)
 *  - stats    : update leaderboard & milestone setelah donasi baru
 *
 * Setiap client menyimpan posisi terakhir via Last-Event-ID (seq).
 * stream.php TIDAK menghapus queue — bersifat read-only terhadap queue.
 * Cleanup dilakukan oleh push.php.
 */

while (ob_get_level()) ob_end_clean();

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('X-Accel-Buffering: no');
header('Access-Control-Allow-Origin: *');

set_time_limit(0);
ignore_user_abort(true);

$dataDir     = __DIR__ . '/../data';
$queueFile   = $dataDir . '/queue.json';
$historyFile = $dataDir . '/history.json';
$configFile  = $dataDir . '/config.json';

$hasLastId = isset($_SERVER['HTTP_LAST_EVENT_ID']) && $_SERVER['HTTP_LAST_EVENT_ID'] !== '';

if ($hasLastId) {
    // Reconnect — lanjut dari posisi terakhir
    $lastSeq = (int) $_SERVER['HTTP_LAST_EVENT_ID'];
} else {
    // Fresh connect (page load/refresh) — mulai dari max seq saat ini
    // agar item lama di queue tidak dikirim ulang.
    $lastSeq = 0;
    if (file_exists($queueFile)) {
        $raw = @file_get_contents($queueFile);
        $q   = $raw ? (json_decode($raw, true) ?: []) : [];
        foreach ($q as $item) {
            if (isset($item['seq']) && (int) $item['seq'] > $lastSeq) {
                $lastSeq = (int) $item['seq'];
            }
        }
    }
}

// Heartbeat awal
sendSSE('ping', json_encode(['time' => time()]), null);
flush();

$lastPing    = time();
$lastStatSeq = $lastSeq; // track kapan stats terakhir dikirim

while (true) {
    if (connection_aborted()) break;

    if (file_exists($queueFile)) {
        $raw   = @file_get_contents($queueFile);
        $queue = $raw ? (json_decode($raw, true) ?: []) : [];

        $newDonation = false;
        foreach ($queue as $donation) {
            $seq = (int) ($donation['seq'] ?? 0);
            if ($seq > $lastSeq) {
                sendSSE('donation', json_encode($donation, JSON_UNESCAPED_UNICODE), $seq);
                flush();
                $lastSeq     = $seq;
                $newDonation = true;
            }
        }

        // Kirim stats update setelah ada donasi baru
        if ($newDonation && $lastSeq > $lastStatSeq) {
            $stats = buildStats($historyFile, $configFile);
            sendSSE('stats', json_encode($stats, JSON_UNESCAPED_UNICODE), null);
            flush();
            $lastStatSeq = $lastSeq;
        }
    }

    if (time() - $lastPing >= 20) {
        sendSSE('ping', json_encode(['time' => time()]), null);
        flush();
        $lastPing = time();
    }

    sleep(1);
}

// ─── Helpers ────────────────────────────────────────────
function sendSSE(string $event, string $data, ?int $id): void
{
    if ($id !== null) echo "id: {$id}\n";
    echo "event: {$event}\n";
    echo "data: {$data}\n\n";
}

function buildStats(string $historyFile, string $configFile): array
{
    $history = [];
    if (file_exists($historyFile)) {
        $raw = @file_get_contents($historyFile);
        $history = $raw ? (json_decode($raw, true) ?: []) : [];
    }

    $defaultConfig = [
        'milestoneTarget'  => 1000000,
        'milestoneTitle'   => 'Target Stream Hari Ini',
        'leaderboardTitle' => 'Top Donatur',
        'leaderboardCount' => 10,
    ];
    $config = $defaultConfig;
    if (file_exists($configFile)) {
        $raw   = @file_get_contents($configFile);
        $saved = $raw ? (json_decode($raw, true) ?: []) : [];
        $config = array_merge($defaultConfig, $saved);
    }

    $total    = 0;
    $donorMap = [];
    foreach ($history as $d) {
        $amt  = (int) ($d['amount'] ?? 0);
        $name = $d['name'] ?? 'Anonim';
        $key  = mb_strtolower($name);
        $total += $amt;
        if (!isset($donorMap[$key])) {
            $donorMap[$key] = ['name' => $name, 'emoji' => $d['emoji'] ?? '🎉', 'total' => 0, 'count' => 0];
        }
        $donorMap[$key]['total'] += $amt;
        $donorMap[$key]['count']++;
    }

    $leaderboard = array_values($donorMap);
    usort($leaderboard, fn($a, $b) => $b['total'] - $a['total']);
    $leaderboard = array_slice($leaderboard, 0, (int) $config['leaderboardCount']);

    return [
        'total'       => $total,
        'count'       => count($history),
        'donors'      => count($donorMap),
        'leaderboard' => $leaderboard,
        'milestone'   => [
            'target'  => (int) $config['milestoneTarget'],
            'title'   => $config['milestoneTitle'],
            'current' => $total,
            'reached' => $total >= (int) $config['milestoneTarget'],
        ],
        'config' => $config,
    ];
}
