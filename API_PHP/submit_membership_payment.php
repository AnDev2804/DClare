<?php

declare(strict_types=1);

require_once __DIR__ . '/membership_service.php';

api_run(function (): void {
    api_require_method('POST');
    $db = api_db();

    try {
        $user = auth_require_user($db);
        $data = api_request_data();
        $receipt = $_FILES['comprobante'] ?? [];

        $payment = membership_submit_payment(
            $db,
            $user['idUsuario'],
            $data,
            $receipt
        );

        $whatsApp = membership_whatsapp_payload(
            $db,
            $user['idUsuario'],
            $payment['idPago']
        );

        json_response([
            'success' => true,
            'message' => $payment['reused']
                ? 'La solicitud ya había sido registrada.'
                : 'Pago enviado y pendiente por revisión.',
            'data' => $payment + ['whatsApp' => $whatsApp],
        ], $payment['reused'] ? 200 : 201);
    } finally {
        if (isset($db) && $db instanceof mysqli) {
            try {
                $db->close();
            } catch (Throwable $ignored) {
            }
        }
    }
});
