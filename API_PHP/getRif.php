<?php

declare(strict_types=1);

require_once __DIR__ . '/membership_service.php';

api_run(function (): void {
    api_require_method('POST');
    $db = api_db();

    try {
        $user = auth_require_user($db);
        $companies = membership_accessible_companies($db, $user['idUsuario']);

        json_response([
            'success' => true,
            'message' => $companies === []
                ? 'Sin empresas registradas.'
                : 'Empresas obtenidas.',
            'data' => $companies,
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
