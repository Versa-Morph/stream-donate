<?php
/**
 * api/push.php
 * Menerima donasi (POST JSON) dari index.php
 *
 * Menyimpan ke DUA file:
 *  - data/queue.json   : antrian notifikasi alert (broadcast SSE, cleanup 5 menit)
 *  - data/history.json : riwayat permanen (untuk leaderboard & milestone)
 *
 * Model queue: append-only log dengan field 'seq'.
 * stream.php membaca queue tanpa menghapus — setiap client punya cursor sendiri.
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$body = file_get_contents('php://input');
$data = json_decode($body, true);

if (!$data || !isset($data['name'], $data['amount'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Data tidak valid']);
    exit;
}

$amount = (int) $data['amount'];
if ($amount < 1000) {
    http_response_code(400);
    echo json_encode(['error' => 'Minimal donasi Rp 1.000']);
    exit;
}

// Sanitasi input
$donation = [
    'id'        => uniqid('don_', true),
    'name'      => mb_substr(strip_tags(trim($data['name'] ?: 'Anonim')), 0, 60),
    'amount'    => $amount,
    'msg'       => mb_substr(strip_tags(trim($data['msg'] ?? 'Semangat streamnya! 🎉')), 0, 200),
    'ytUrl'     => mb_substr(trim($data['ytUrl'] ?? ''), 0, 200),
    'emoji'     => mb_substr(trim($data['emoji'] ?? '🎉'), 0, 10),
    'duration'  => max(1000, min((int) ($data['duration'] ?? 8000), 60000)),
    'ytEnabled' => (bool) ($data['ytEnabled'] ?? true),
    'time'      => date('c'),
];

// Validasi URL YouTube
if (!empty($donation['ytUrl']) &&
    !preg_match('/^https?:\/\/(www\.)?(youtube\.com|youtu\.be)\//', $donation['ytUrl'])) {
    $donation['ytUrl'] = '';
}

$dataDir     = __DIR__ . '/../data';
$queueFile   = $dataDir . '/queue.json';
$historyFile = $dataDir . '/history.json';
$cutoff      = time() - 300; // queue cleanup: 5 menit

// ─── 1. Tulis ke queue.json ──────────────────────────────
$fp = fopen($queueFile, file_exists($queueFile) ? 'r+' : 'w+');
if (!$fp || !flock($fp, LOCK_EX)) {
    if ($fp) fclose($fp);
    http_response_code(500);
    echo json_encode(['error' => 'Gagal mengunci file queue']);
    exit;
}

$raw   = stream_get_contents($fp);
$queue = $raw ? (json_decode($raw, true) ?: []) : [];

// Cleanup item lama dari queue
$queue = array_values(array_filter($queue, function ($item) use ($cutoff) {
    return strtotime($item['time'] ?? '') > $cutoff;
}));

// Seq berikutnya
$maxSeq = 0;
foreach ($queue as $item) {
    if (isset($item['seq']) && $item['seq'] > $maxSeq) $maxSeq = $item['seq'];
}
$donation['seq'] = $maxSeq + 1;

$queue[] = $donation;
ftruncate($fp, 0);
rewind($fp);
$written = fwrite($fp, json_encode($queue, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
flock($fp, LOCK_UN);
fclose($fp);

if ($written === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Gagal menyimpan ke queue']);
    exit;
}

// ─── 2. Tulis ke history.json ────────────────────────────
// History menyimpan hanya field yang diperlukan leaderboard & milestone
$histEntry = [
    'id'     => $donation['id'],
    'name'   => $donation['name'],
    'amount' => $donation['amount'],
    'emoji'  => $donation['emoji'],
    'ytUrl'  => $donation['ytUrl'] !== '' ? $donation['ytUrl'] : null,
    'time'   => $donation['time'],
];

$fh = fopen($historyFile, file_exists($historyFile) ? 'r+' : 'w+');
if ($fh && flock($fh, LOCK_EX)) {
    $hraw    = stream_get_contents($fh);
    $history = $hraw ? (json_decode($hraw, true) ?: []) : [];
    $history[] = $histEntry;
    ftruncate($fh, 0);
    rewind($fh);
    fwrite($fh, json_encode($history, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    flock($fh, LOCK_UN);
    fclose($fh);
}
// Gagal tulis history tidak fatal — queue sudah tersimpan

echo json_encode(['success' => true, 'id' => $donation['id'], 'seq' => $donation['seq']]);
