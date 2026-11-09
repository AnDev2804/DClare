<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

api_run(function (): void {
    api_require_method('GET');
    $db = api_db();
    try {
        $admin = auth_require_admin($db);
        json_response([
            'success' => true,
            'isAdmin' => true,
            'data' => [
                'idUsuario' => $admin['idUsuario'],
                'nombre' => $admin['nombre'],
                'correo' => $admin['correo'],
                'numRol' => $admin['numRol'],
                'rol' => $admin['rol'],
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

