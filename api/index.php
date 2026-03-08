<?php

// buat sqlite sementara di serverless
$db = '/tmp/database.sqlite';

if (!file_exists($db)) {
    touch($db);
}

require __DIR__ . '/../public/index.php';