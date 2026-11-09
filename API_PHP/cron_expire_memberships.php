<?php

declare(strict_types=1);

require_once __DIR__ . '/membership_service.php';

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

try {
    $db = api_db();
    membership_sync_expired($db);
    $db->close();
    fwrite(STDOUT, '[' . gmdate('c') . "] Membresías sincronizadas.\n");
    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, '[' . gmdate('c') . '] Error: ' . $e->getMessage() . "\n");
    exit(1);
}

