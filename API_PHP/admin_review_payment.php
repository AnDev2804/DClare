<?php

declare(strict_types=1);

require_once __DIR__ . '/membership_service.php';

api_run(function (): void {
    api_require_method('POST');
    $db = api_db();
    try {
        $admin = auth_require_admin($db);
        $data = api_request_data();
        $paymentId = api_positive_int($data['idPago'] ?? null, 'idPago');
        if (!api_bool($data['confirmed'] ?? null, 'confirmed')) {
            api_throw(409, 'CONFIRMATION_REQUIRED', 'Debe confirmar la revisión del pago.');
        }
        $decision = api_string($data, 'decision', 10);
        $observation = api_string($data, 'observacion', 500, false);
        $result = membership_admin_review_payment(
            $db,
            $admin['idUsuario'],
            $paymentId,
            $decision,
            $observation
        );
        $db->close();
        json_response([
            'success' => true,
            'message' => strtoupper($decision) === 'APROBAR'
                ? 'Pago aprobado y membresía Premium activada correctamente.'
                : 'Pago rechazado.',
            'data' => $result,
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
