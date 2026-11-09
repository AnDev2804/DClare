<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

api_run(function (): void {
    api_require_method('POST');
    $db = api_db();
    try {
        $user = auth_require_user($db);
        auth_revoke_session($db, $user['sessionId']);
        json_response(['success' => true, 'message' => 'Sesión cerrada correctamente.']);
    } finally {
        if (isset($db) && $db instanceof mysqli) {
            try {
                $db->close();
            } catch (Throwable $ignored) {
            }
        }
    }
});

