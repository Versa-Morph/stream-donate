<?php
/**
 * api/config.php
 * POST endpoint — menyimpan konfigurasi milestone & leaderboard ke data/config.json
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

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Data tidak valid']);
    exit;
}

$config = [
    'milestoneTarget'  => max(1000, (int) ($data['milestoneTarget'] ?? 1000000)),
    'milestoneTitle'   => mb_substr(strip_tags(trim($data['milestoneTitle'] ?? 'Target Stream Hari Ini')), 0, 80),
    'milestoneReset'   => (bool) ($data['milestoneReset'] ?? false),
    'leaderboardTitle' => mb_substr(strip_tags(trim($data['leaderboardTitle'] ?? 'Top Donatur')), 0, 60),
    'leaderboardCount' => max(3, min((int) ($data['leaderboardCount'] ?? 10), 20)),
];

$configFile = __DIR__ . '/../data/config.json';
$written = file_put_contents(
    $configFile,
    json_encode($config, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT),
    LOCK_EX
);

if ($written === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Gagal menyimpan konfigurasi']);
    exit;
}

// Jika reset history diminta
if (!empty($data['resetHistory'])) {
    file_put_contents(__DIR__ . '/../data/history.json', '[]', LOCK_EX);
}

echo json_encode(['success' => true, 'config' => $config]);
