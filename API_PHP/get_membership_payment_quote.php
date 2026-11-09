<?php

declare(strict_types=1);

require_once __DIR__ . '/membership_service.php';

api_run(function (): void {
    api_require_method('GET');
    $db = api_db();

    try {
        $user = auth_require_user($db);
        $status = membership_status($db, $user['idUsuario']);

        if (!$status['canSubmitPayment']) {
            api_throw(409, 'PAYMENT_NOT_AVAILABLE', 'El usuario no puede registrar un pago en su estado actual.', [
                'state' => $status['state'],
            ]);
        }

        $methodCode = trim((string)($_GET['metodoPago'] ?? ''));
        if ($methodCode === '') {
            api_throw(422, 'VALIDATION_ERROR', 'Debe indicar el método de pago.', ['field' => 'metodoPago']);
        }

        $quote = membership_payment_quote($db, $methodCode);

        json_response([
            'success' => true,
            'data' => $quote,
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
