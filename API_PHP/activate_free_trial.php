<?php

declare(strict_types=1);

require_once __DIR__ . '/membership_service.php';

api_run(function (): void {
    api_require_method('POST');
    $db = api_db();

    try {
        $user = auth_require_user($db);
        $data = api_request_data();

        $status = membership_activate_free_trial(
            $db,
            $user['idUsuario'],
            $data
        );

        json_response([
            'success' => true,
            'message' => 'Prueba Premium de tres meses activada correctamente.',
            'data' => $status,
        ], 201);
    } finally {
        if (isset($db) && $db instanceof mysqli) {
            try {
                $db->close();
            } catch (Throwable $ignored) {
            }
        }
    }
});
