<?php

declare(strict_types=1);

require_once __DIR__ . '/admin_service.php';

api_run(function (): void {
    api_require_method('POST');
    $db = api_db();

    try {
        auth_require_admin($db);
        $data = api_request_data();
        $userId = api_positive_int($data['idUsuario'] ?? null, 'idUsuario');

        $stmt = $db->prepare(
            'SELECT
                e.id_eliminada,
                e.id_rif_original,
                e.ID_Foranea,
                e.RIF,
                e.tipo_de_cliente,
                tc.desc_tc,
                e.nombre_empresa,
                e.rif_created_at,
                e.usuario_eliminador_id,
                e.usuario_eliminador_nombre,
                e.usuario_eliminador_email,
                e.fecha_eliminacion
             FROM empresas_eliminadas e
             LEFT JOIN tipo_cliente tc ON tc.id_tipo_cliente = e.tipo_de_cliente
             WHERE e.ID_Foranea = ?
             ORDER BY e.fecha_eliminacion DESC, e.id_eliminada DESC'
        );
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        $deletedList = [];
        while ($row = $result->fetch_assoc()) {
            $deletedList[] = [
                'idEliminada' => (int)$row['id_eliminada'],
                'idRifOriginal' => (int)$row['id_rif_original'],
                'idUsuario' => (int)$row['ID_Foranea'],
                'rif' => (string)$row['RIF'],
                'tipoId' => (int)$row['tipo_de_cliente'],
                'tipo' => (string)($row['desc_tc'] ?? ($row['tipo_de_cliente'] == 2 ? 'Contribuyente Especial' : 'Contribuyente Ordinario')),
                'nombre' => (string)$row['nombre_empresa'],
                'rifCreatedAt' => membership_utc($row['rif_created_at']),
                'fechaEliminacion' => membership_utc($row['fecha_eliminacion']),
                'usuarioEliminadorId' => (int)$row['usuario_eliminador_id'],
                'usuarioEliminadorNombre' => (string)($row['usuario_eliminador_nombre'] ?? ''),
                'usuarioEliminadorEmail' => (string)($row['usuario_eliminador_email'] ?? ''),
            ];
        }
        $stmt->close();

        json_response([
            'success' => true,
            'message' => 'Empresas eliminadas obtenidas.',
            'data' => $deletedList,
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
