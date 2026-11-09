<?php

declare(strict_types=1);

require_once __DIR__ . '/membership_service.php';

api_run(function (): void {
    api_require_method('GET');

    $paymentId = filter_input(INPUT_GET, 'idPago', FILTER_VALIDATE_INT);
    if ($paymentId === false || $paymentId === null || $paymentId < 1) {
        api_throw(422, 'INVALID_PAYMENT_ID', 'El identificador del pago no es válido.');
    }

    $db = api_db();

    try {
        $user = auth_require_user($db);

        $stmt = $db->prepare(
            'SELECT id_usuario, comprobante_archivo, comprobante_nombre_original,
                    comprobante_mime, comprobante_tamano, comprobante_sha256
             FROM pagos_membresia
             WHERE id_pago = ?
             LIMIT 1'
        );
        $stmt->bind_param('i', $paymentId);
        $stmt->execute();
        $receipt = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$receipt) {
            api_throw(404, 'PAYMENT_NOT_FOUND', 'Comprobante no encontrado.');
        }

        if ($user['numRol'] !== AUTH_ROLE_ADMIN
            && $user['idUsuario'] !== (int)$receipt['id_usuario']) {
            api_throw(403, 'RECEIPT_FORBIDDEN', 'No tiene permiso para ver este comprobante.');
        }

        $filename = (string)$receipt['comprobante_archivo'];
        if ($filename === '' || $filename !== basename($filename)) {
            api_throw(500, 'INVALID_RECEIPT_PATH', 'El comprobante almacenado no es válido.');
        }

        $path = membership_receipt_directory() . DIRECTORY_SEPARATOR . $filename;
        if (!is_file($path) || !is_readable($path)) {
            api_throw(404, 'RECEIPT_NOT_FOUND', 'El archivo del comprobante no está disponible.');
        }

        $actualHash = hash_file('sha256', $path);
        if ($actualHash === false
            || !hash_equals((string)$receipt['comprobante_sha256'], $actualHash)) {
            api_throw(500, 'RECEIPT_INTEGRITY_ERROR', 'El comprobante no superó la verificación de integridad.');
        }

        $mime = (string)$receipt['comprobante_mime'];
        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            api_throw(500, 'INVALID_RECEIPT_TYPE', 'El tipo del comprobante almacenado no es válido.');
        }

        $size = filesize($path);
        if ($size === false) {
            api_throw(500, 'RECEIPT_READ_ERROR', 'No fue posible leer el comprobante.');
        }

        http_response_code(200);
        header('Content-Type: ' . $mime);
        header('Content-Length: ' . (string)$size);
        header('Content-Disposition: inline; filename="comprobante-' . $paymentId . '"');
        header('Cache-Control: private, no-store');
        header('X-Content-Type-Options: nosniff');
        header("Content-Security-Policy: default-src 'none'; sandbox");

        readfile($path);
        exit;
    } finally {
        if (isset($db) && $db instanceof mysqli) {
            try {
                $db->close();
            } catch (Throwable $ignored) {
            }
        }
    }
});
