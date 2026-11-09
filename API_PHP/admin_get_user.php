<?php

declare(strict_types=1);

require_once __DIR__ . '/admin_service.php';

api_run(function (): void {
    api_require_method('POST');
    $db = api_db();
    try {
        auth_require_admin($db);
        $data = api_request_data();
        $userId = api_positive_int($data['idUsuario'] ?? null, 'idUsuario');
        $detail = admin_user_detail($db, $userId);
        json_response(['success' => true, 'data' => $detail]);
    } finally {
        if (isset($db) && $db instanceof mysqli) {
            try {
                $db->close();
            } catch (Throwable $ignored) {
            }
        }
    }
});
