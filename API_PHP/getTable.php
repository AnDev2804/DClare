<?php

declare(strict_types=1);

require_once __DIR__ . '/membership_service.php';
require_once __DIR__ . '/connect.php';

api_run(function (): void {
    api_require_method('POST');

    $db = api_db();

    try {
        $user = auth_require_user($db);

        /*
         * Sincroniza una membresía que haya vencido antes
         * de decidir si el usuario conserva acceso Premium.
         */
        membership_sync_expired(
            $db,
            $user['idUsuario']
        );

        /*
         * Reconsultamos el rol porque membership_sync_expired()
         * pudo haber degradado Premium -> No Premium.
         */
        $roleStmt = $db->prepare(
            'SELECT num_rol_data
             FROM logdata
             WHERE ID = ?
             LIMIT 1'
        );

        $roleStmt->bind_param(
            'i',
            $user['idUsuario']
        );

        $roleStmt->execute();

        $roleRow =
            $roleStmt
                ->get_result()
                ->fetch_assoc();

        $roleStmt->close();

        if (!$roleRow) {
            api_throw(
                404,
                'USER_NOT_FOUND',
                'Usuario no encontrado.'
            );
        }

        $currentRole =
            (int)$roleRow[
                'num_rol_data'
            ];

        $data =
            api_request_data();

        $digit =
            api_string(
                $data,
                'digit',
                1
            );

        if (
            $digit === null ||
            !preg_match(
                '/^[0-9]$/',
                $digit
            )
        ) {
            api_throw(
                422,
                'INVALID_CALENDAR_DIGIT',
                'El terminal debe ser un dígito entre 0 y 9.'
            );
        }

        $digitInt =
            (int)$digit;

        /*
         * Regla del plan:
         * No Premium (rol 1) -> 0, 1 y 2.
         * Premium (rol 2)    -> 0..9.
         * Admin (rol 3)      -> 0..9.
         */
        if (
            $currentRole ===
                AUTH_ROLE_NO_PREMIUM &&
            $digitInt > 2
        ) {
            api_throw(
                403,
                'CALENDAR_PREMIUM_REQUIRED',
                'Los terminales del 3 al 9 son exclusivos para usuarios Premium.'
            );
        }

        /*
         * Conservamos el array raíz esperado por
         * VolleyMainRepository.getTable().
         */
        $obj =
            new conexion();

        $result =
            $obj->getTable(
                $digit
            );

        if ($result === false) {
            api_throw(
                500,
                'CALENDAR_QUERY_FAILED',
                'No se pudo consultar el calendario.'
            );
        }

        json_response(
            $result
        );

    } finally {
        if (
            isset($db) &&
            $db instanceof mysqli
        ) {
            try {
                $db->close();
            } catch (
                Throwable $ignored
            ) {
            }
        }
    }
});
