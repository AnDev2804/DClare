<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

api_run(function (): void {
    api_require_method('POST');

    $db = api_db();

    try {
        $user = auth_require_user($db);
        $data = api_request_data();

        $name = api_string($data, 'name', 225);
        $name = preg_replace('/\s+/u', ' ', trim($name));

        if ($name === null || $name === '') {
            api_throw(
                422,
                'INVALID_NAME',
                'Por favor, ingrese un nombre y apellido válido.'
            );
        }

        // Permite nombres compuestos, apóstrofes y guiones, pero exige dos partes.
        $namePattern = "/^[\p{L}]+(?:[ '\-][\p{L}]+)+(?:\s+[\p{L}]+(?:[ '\-][\p{L}]+)*)*$/u";

        if (!preg_match($namePattern, $name)) {
            api_throw(
                422,
                'INVALID_NAME',
                'Por favor, ingrese un nombre y apellido válido.'
            );
        }

        $userId = (int)$user['idUsuario'];

        $stmt = $db->prepare(
            'UPDATE infoper
             SET nya = ?
             WHERE ID_Foranea = ?'
        );
        $stmt->bind_param('si', $name, $userId);
        $stmt->execute();
        $stmt->close();

        // Verifica el perfil incluso si no hubo cambios porque el nombre era idéntico.
        $check = $db->prepare(
            'SELECT nya
             FROM infoper
             WHERE ID_Foranea = ?
             LIMIT 1'
        );
        $check->bind_param('i', $userId);
        $check->execute();
        $row = $check->get_result()->fetch_assoc();
        $check->close();

        if (!$row) {
            api_throw(
                404,
                'PROFILE_NOT_FOUND',
                'No se encontró el perfil del usuario.'
            );
        }

        json_response([
            'success' => true,
            'message' => 'Nombre actualizado correctamente.',
            'name' => trim((string)($row['nya'] ?? '')),
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
