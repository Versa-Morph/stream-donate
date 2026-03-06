<?php
/**
 * api/stats.php
 * GET endpoint — mengembalikan statistik donasi dan konfigurasi milestone.
 *
 * Response:
 * {
 *   "total":      123456,       // total semua donasi (Rp)
 *   "count":      42,           // jumlah transaksi
 *   "donors":     15,           // jumlah donatur unik
 *   "ytCount":    8,            // donasi dengan YouTube
 *   "biggest":    { name, amount },
 *   "leaderboard": [ { name, emoji, total, count }, ... ],  // top 10
 *   "milestone": {
 *     "target":   1000000,
 *     "title":    "Target Stream Hari Ini",
 *     "reached":  false
 *   },
 *   "config":     { ... }       // seluruh config.json
 * }
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache');

$dataDir     = __DIR__ . '/../data';
$historyFile = $dataDir . '/history.json';
$configFile  = $dataDir . '/config.json';

// ─── Baca history ────────────────────────────────────────
$history = [];
if (file_exists($historyFile)) {
    $raw = @file_get_contents($historyFile);
    $history = $raw ? (json_decode($raw, true) ?: []) : [];
}

// ─── Baca config ─────────────────────────────────────────
$defaultConfig = [
    'milestoneTarget' => 1000000,
    'milestoneTitle'  => 'Target Stream Hari Ini',
    'milestoneReset'  => false,
    'leaderboardTitle'=> 'Top Donatur',
    'leaderboardCount'=> 10,
];
$config = $defaultConfig;
if (file_exists($configFile)) {
    $raw = @file_get_contents($configFile);
    $saved = $raw ? (json_decode($raw, true) ?: []) : [];
    $config = array_merge($defaultConfig, $saved);
}

// ─── Hitung stats ────────────────────────────────────────
$total   = 0;
$count   = count($history);
$ytCount = 0;
$biggest = ['name' => '—', 'amount' => 0];
$donorMap = []; // name_lower => { name, emoji, total, count }

foreach ($history as $d) {
    $amt  = (int) ($d['amount'] ?? 0);
    $name = $d['name'] ?? 'Anonim';
    $key  = mb_strtolower($name);

    $total += $amt;
    if (!empty($d['ytUrl'])) $ytCount++;
    if ($amt > $biggest['amount']) {
        $biggest = ['name' => $name, 'amount' => $amt];
    }

    if (!isset($donorMap[$key])) {
        $donorMap[$key] = [
            'name'  => $name,
            'emoji' => $d['emoji'] ?? '🎉',
            'total' => 0,
            'count' => 0,
        ];
    }
    $donorMap[$key]['total'] += $amt;
    $donorMap[$key]['count']++;
}

// Sort leaderboard by total desc
$leaderboard = array_values($donorMap);
usort($leaderboard, fn($a, $b) => $b['total'] - $a['total']);
$leaderboard = array_slice($leaderboard, 0, (int) $config['leaderboardCount']);

$donors = count($donorMap);

echo json_encode([
    'total'       => $total,
    'count'       => $count,
    'donors'      => $donors,
    'ytCount'     => $ytCount,
    'biggest'     => $biggest,
    'leaderboard' => $leaderboard,
    'milestone'   => [
        'target'  => (int) $config['milestoneTarget'],
        'title'   => $config['milestoneTitle'],
        'current' => $total,
        'reached' => $total >= (int) $config['milestoneTarget'],
    ],
    'config'      => $config,
], JSON_UNESCAPED_UNICODE);
