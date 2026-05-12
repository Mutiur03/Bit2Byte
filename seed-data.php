<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/setup-data.php';

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Run this file from terminal: php seed-data.php');
}

try {
    $messages = seed_all_data($pdo);

    foreach ($messages as $message) {
        echo $message . PHP_EOL;
    }
} catch (Throwable $e) {
    fwrite(STDERR, 'Seed failed: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
