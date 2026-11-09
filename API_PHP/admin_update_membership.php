<?php

declare(strict_types=1);

require_once __DIR__ . '/membership_service.php';

api_run(function (): void {
    api_require_method('POST');
    $db = api_db();
    try {
        $admin = auth_require_admin($db);
        $data = api_request_data();
        $userId = api_positive_int($data['idUsuario'] ?? null, 'idUsuario');
        $premium = api_bool($data['premium'] ?? null, 'premium');
        if (!api_bool($data['confirmed'] ?? null, 'confirmed')) {
            api_throw(409, 'CONFIRMATION_REQUIRED', 'Debe confirmar la actualización de membresía.');
        }
        $observation = api_string($data, 'observacion', 500, false);
        $result = membership_admin_set_premium(
            $db,
            $admin['idUsuario'],
            $userId,
            $premium,
            $observation
        );
        $db->close();

        json_response([
            'success' => true,
            'message' => $premium
                ? 'Membresía Premium activada por el administrador.'
                : 'Membresía Premium desactivada.',
            'data' => $result,
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
