<?php

declare(strict_types=1);

require_once __DIR__ . '/membership_service.php';

api_run(function (): void {
    api_require_method('GET');
    $db = api_db();

    try {
        $user = auth_require_user($db);
        $status = membership_status($db, $user['idUsuario']);

        json_response([
            'success' => true,
            'data' => [
                'membership' => $status,
                'methods' => membership_payment_methods($db),
            ],
        ]);
    } finally {
        if (isset($db) && $db instanceof mysqli) {
            try {
                $db->close();
            } catch (Throwable $ignored) {
            }
        }
    }
});
