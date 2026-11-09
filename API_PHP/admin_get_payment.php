<?php

declare(strict_types=1);

require_once __DIR__ . '/admin_service.php';

api_run(function (): void {
    api_require_method('GET');
    $paymentId = filter_input(INPUT_GET, 'idPago', FILTER_VALIDATE_INT);
    if ($paymentId === false || $paymentId === null || $paymentId < 1) {
        api_throw(422, 'INVALID_PAYMENT_ID', 'El identificador del pago no es válido.');
    }

    $db = api_db();
    try {
        auth_require_admin($db);
        $payment = admin_payment_detail($db, (int)$paymentId);
        json_response(['success' => true, 'data' => $payment]);
    } finally {
        if (isset($db) && $db instanceof mysqli) {
            try {
                $db->close();
            } catch (Throwable $ignored) {
            }
        }
    }
});

