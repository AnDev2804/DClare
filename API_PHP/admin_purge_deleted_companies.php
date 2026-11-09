<?php

declare(strict_types=1);

require_once __DIR__ . '/admin_service.php';

api_run(function (): void {
    api_require_method('POST');
    $db = api_db();

    try {
        auth_require_admin($db);
        $data = api_request_data();

        $idsRaw = $data['ids'] ?? null;
        if (!is_array($idsRaw) || empty($idsRaw)) {
            api_throw(422, 'INVALID_IDS', 'Debes seleccionar al menos una empresa eliminada para borrar.');
        }

        $ids = [];
        foreach ($idsRaw as $raw) {
            $id = filter_var($raw, FILTER_VALIDATE_INT);
            if ($id !== false && $id > 0) {
                $ids[] = (int)$id;
            }
        }

        $ids = array_values(array_unique($ids));
        if (empty($ids)) {
            api_throw(422, 'INVALID_IDS', 'Los identificadores seleccionados no son válidos.');
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $types = str_repeat('i', count($ids));

        $stmt = $db->prepare("DELETE FROM empresas_eliminadas WHERE id_eliminada IN ($placeholders)");
        $stmt->bind_param($types, ...$ids);
        $stmt->execute();
        $affected = $stmt->affected_rows;
        $stmt->close();

        json_response([
            'success' => true,
            'message' => 'Empresas eliminadas definitivamente de la base de datos.',
            'purgedCount' => $affected,
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
