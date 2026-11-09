<?php

declare(strict_types=1);

require_once __DIR__ . '/membership_service.php';

api_run(function (): void {
    api_require_method('POST');
    $db = api_db();

    try {
        $user = auth_require_user($db);
        $data = api_request_data();

        $originalRif = (string)api_string($data, 'originalRif', 225);
        $newRif = (string)api_string($data, 'newRif', 225);
        $tipo = api_positive_int($data['tipo'] ?? null, 'tipo');
        $nombre = preg_replace('/\s+/', ' ', (string)api_string($data, 'nombre', 255));

        $company = membership_require_accessible_company(
            $db,
            $user['idUsuario'],
            $originalRif
        );

        if ($user['numRol'] === AUTH_ROLE_NO_PREMIUM && $newRif !== $originalRif) {
            api_throw(
                403,
                'RIF_EDIT_PREMIUM_REQUIRED',
                'En el plan gratuito no está permitido modificar el número de RIF. Actualiza a Premium para cambiarlo.'
            );
        }

        $typeStmt = $db->prepare('SELECT 1 FROM tipo_cliente WHERE id_tipo_cliente = ? LIMIT 1');
        $typeStmt->bind_param('i', $tipo);
        $typeStmt->execute();
        $validType = (bool)$typeStmt->get_result()->fetch_row();
        $typeStmt->close();

        if (!$validType) {
            api_throw(422, 'INVALID_CLIENT_TYPE', 'El tipo de cliente no es válido.', [
                'field' => 'tipo',
            ]);
        }

        try {
            $stmt = $db->prepare(
                'UPDATE rif
                 SET RIF = ?, tipo_de_cliente = ?, nombre_empresa = ?
                 WHERE id_rif = ? AND ID_Foranea = ?
                 LIMIT 1'
            );
            $idRif = (int)$company['idRif'];
            $userId = (int)$user['idUsuario'];
            $stmt->bind_param('sisii', $newRif, $tipo, $nombre, $idRif, $userId);
            $stmt->execute();
            $stmt->close();
        } catch (mysqli_sql_exception $e) {
            if ((int)$e->getCode() === 1062) {
                api_throw(409, 'DUPLICATE_RIF', 'Ya existe un cliente con ese RIF/Cédula.');
            }
            throw $e;
        }

        json_response([
            'success' => true,
            'message' => 'Cliente actualizado correctamente.',
            'data' => [
                'idRif' => (int)$company['idRif'],
                'rif' => $newRif,
                'tipo' => $tipo,
                'nombre' => $nombre,
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
