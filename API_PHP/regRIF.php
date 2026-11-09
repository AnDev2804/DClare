<?php

declare(strict_types=1);

require_once __DIR__ . '/membership_service.php';

api_run(function (): void {
    api_require_method('POST');
    $db = api_db();

    try {
        $user = auth_require_user($db);
        $data = api_request_data();

        $rif = trim((string)api_string($data, 'number', 225));
        $nombre = preg_replace('/\s+/', ' ', (string)api_string($data, 'nombre', 255));
        $tipo = api_positive_int($data['tipo'] ?? null, 'tipo');

        if (!in_array($tipo, [1, 2], true)) {
            api_throw(422, 'VALIDATION_ERROR', 'El tipo de cliente debe ser 1 o 2.', [
                'field' => 'tipo',
            ]);
        }

        if ($rif === '') {
            api_throw(422, 'VALIDATION_ERROR', 'El RIF/Cédula es obligatorio.', [
                'field' => 'number',
            ]);
        }

        mysqli_begin_transaction($db);
        try {
            $lock = $db->prepare('SELECT ID FROM logdata WHERE ID = ? FOR UPDATE');
            $lock->bind_param('i', $user['idUsuario']);
            $lock->execute();
            $exists = (bool)$lock->get_result()->fetch_row();
            $lock->close();

            if (!$exists) {
                api_throw(404, 'USER_NOT_FOUND', 'Usuario no encontrado.');
            }

            membership_sync_expired($db, $user['idUsuario']);
            membership_assert_can_register_company($db, $user['idUsuario']);

            $insert = $db->prepare(
                'INSERT INTO rif (ID_Foranea, RIF, tipo_de_cliente, nombre_empresa)
                 VALUES (?, ?, ?, ?)'
            );
            $insert->bind_param('isis', $user['idUsuario'], $rif, $tipo, $nombre);
            $insert->execute();
            $idRif = (int)$db->insert_id;
            $insert->close();

            $preferences = $db->prepare(
                'INSERT IGNORE INTO rif_tax_preferences (rif) VALUES (?)'
            );
            $preferences->bind_param('s', $rif);
            $preferences->execute();
            $preferences->close();

            $registered = 1;
            $update = $db->prepare('UPDATE logdata SET reg_state = ? WHERE ID = ?');
            $update->bind_param('ii', $registered, $user['idUsuario']);
            $update->execute();
            $update->close();

            mysqli_commit($db);
        } catch (mysqli_sql_exception $e) {
            mysqli_rollback($db);
            if ((int)$e->getCode() === 1062) {
                api_throw(409, 'DUPLICATE_RIF', 'Ya existe una empresa registrada con ese RIF/Cédula.');
            }
            throw $e;
        } catch (Throwable $e) {
            mysqli_rollback($db);
            throw $e;
        }

        json_response([
            'success' => true,
            'message' => 'Empresa registrada correctamente.',
            'data' => [
                'idRif' => $idRif,
                'rif' => $rif,
                'tipo' => $tipo,
                'nombre' => $nombre,
            ],
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
