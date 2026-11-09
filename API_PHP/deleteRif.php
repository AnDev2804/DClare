<?php

declare(strict_types=1);

require_once __DIR__ . '/membership_service.php';

api_run(function (): void {
    api_require_method('POST');
    $db = api_db();

    try {
        $user = auth_require_user($db);
        $data = api_request_data();

        $rif = (string)api_string($data, 'rif', 225);

        $company = membership_require_accessible_company(
            $db,
            $user['idUsuario'],
            $rif
        );

        $idRif = (int)$company['idRif'];
        $userId = (int)$user['idUsuario'];

        // Consultar los datos exactos del registro en rif antes de eliminar
        $rifStmt = $db->prepare('SELECT id_rif, ID_Foranea, RIF, tipo_de_cliente, nombre_empresa, created_at FROM rif WHERE id_rif = ? AND ID_Foranea = ? LIMIT 1');
        $rifStmt->bind_param('ii', $idRif, $userId);
        $rifStmt->execute();
        $rifData = $rifStmt->get_result()->fetch_assoc();
        $rifStmt->close();

        if (!$rifData) {
            api_throw(404, 'COMPANY_NOT_FOUND', 'La empresa no existe o ya fue eliminada.');
        }

        $eliminadorId = $userId;
        $eliminadorNombre = (string)($user['name'] ?? '');
        $eliminadorEmail = (string)($user['email'] ?? '');

        // Validar límite de eliminaciones para usuarios gratuitos (máximo 3)
        membership_sync_expired($db, $userId);
        $role = membership_user_role($db, $userId);
        $isPremiumOrAdmin = ($role === AUTH_ROLE_PREMIUM || $role === AUTH_ROLE_ADMIN);

        $totalEliminadas = 0;
        if (!$isPremiumOrAdmin) {
            $countDeletedStmt = $db->prepare('SELECT COUNT(*) AS total FROM empresas_eliminadas WHERE ID_Foranea = ?');
            $countDeletedStmt->bind_param('i', $userId);
            $countDeletedStmt->execute();
            $deletedRow = $countDeletedStmt->get_result()->fetch_assoc();
            $countDeletedStmt->close();

            $totalEliminadas = (int)($deletedRow['total'] ?? 0);
            if ($totalEliminadas >= 3) {
                api_throw(
                    403,
                    'DELETE_LIMIT_REACHED',
                    'Como usuario gratuito has alcanzado el límite máximo de 3 eliminaciones de empresa. Actualiza a Premium para eliminar empresas sin límites.',
                    [
                        'deletedCount' => $totalEliminadas,
                        'maxAllowed' => 3,
                    ]
                );
            }
        }

        mysqli_begin_transaction($db);
        try {
            // 1. Guardar en empresas_eliminadas con TODOS los datos del RIF y del usuario que eliminó
            $archiveStmt = $db->prepare(
                'INSERT INTO empresas_eliminadas (
                    id_rif_original,
                    ID_Foranea,
                    RIF,
                    tipo_de_cliente,
                    nombre_empresa,
                    rif_created_at,
                    usuario_eliminador_id,
                    usuario_eliminador_nombre,
                    usuario_eliminador_email,
                    fecha_eliminacion
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, UTC_TIMESTAMP())'
            );
            $archiveStmt->bind_param(
                'iisisisss',
                $rifData['id_rif'],
                $rifData['ID_Foranea'],
                $rifData['RIF'],
                $rifData['tipo_de_cliente'],
                $rifData['nombre_empresa'],
                $rifData['created_at'],
                $eliminadorId,
                $eliminadorNombre,
                $eliminadorEmail
            );
            $archiveStmt->execute();
            $archiveStmt->close();

            // 2. Eliminar de la tabla rif activa
            $stmt = $db->prepare('DELETE FROM rif WHERE id_rif = ? AND ID_Foranea = ? LIMIT 1');
            $stmt->bind_param('ii', $idRif, $userId);
            $stmt->execute();
            $stmt->close();

            // 3. Verificar cuántas empresas le quedan al usuario
            $countStmt = $db->prepare('SELECT COUNT(*) AS total FROM rif WHERE ID_Foranea = ?');
            $countStmt->bind_param('i', $userId);
            $countStmt->execute();
            $totalRow = $countStmt->get_result()->fetch_assoc();
            $countStmt->close();

            $remaining = (int)($totalRow['total'] ?? 0);
            if ($remaining === 0) {
                $updateState = $db->prepare('UPDATE logdata SET reg_state = 0 WHERE ID = ?');
                $updateState->bind_param('i', $userId);
                $updateState->execute();
                $updateState->close();
            }

            mysqli_commit($db);
        } catch (Throwable $e) {
            mysqli_rollback($db);
            throw $e;
        }

        json_response([
            'success' => true,
            'message' => 'Cliente eliminado correctamente.',
            'remainingCompanies' => $remaining,
            'deletedCount' => $isPremiumOrAdmin ? 0 : ($totalEliminadas + 1),
            'maxAllowed' => 3,
            'isPremium' => $isPremiumOrAdmin,
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
