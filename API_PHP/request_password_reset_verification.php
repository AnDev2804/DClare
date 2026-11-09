<?php

declare(strict_types=1);

require_once __DIR__ . '/api_bootstrap.php';
require_once __DIR__ . '/mailer.php';

api_run(function (): void {
    api_require_method('POST');
    $data = api_request_data();
    $email = strtolower((string)api_string($data, 'email', 225));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        api_throw(422, 'INVALID_EMAIL', 'El correo no es válido.', ['field' => 'email']);
    }

    $db = api_db();
    try {
        $userStmt = $db->prepare('SELECT 1 FROM logdata WHERE Correo = ? LIMIT 1');
        $userStmt->bind_param('s', $email);
        $userStmt->execute();
        $exists = (bool)$userStmt->get_result()->fetch_row();
        $userStmt->close();

        if (!$exists) {
            api_throw(404, 'USER_NOT_FOUND', 'Correo no existe.');
        }

        $pendingStmt = $db->prepare(
            "SELECT id, code_expires_at, updated_at, verified
             FROM pending_email_verifications
             WHERE email = ? AND purpose = 'password_reset'
             LIMIT 1"
        );
        $pendingStmt->bind_param('s', $email);
        $pendingStmt->execute();
        $pending = $pendingStmt->get_result()->fetch_assoc();
        $pendingStmt->close();

        if ($pending && (int)$pending['verified'] === 0) {
            $updatedAt = new DateTimeImmutable((string)$pending['updated_at'], new DateTimeZone('UTC'));
            $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
            if (($now->getTimestamp() - $updatedAt->getTimestamp()) < 60) {
                api_throw(429, 'RESET_CODE_COOLDOWN', 'Ya se envió un código recientemente. Espere un momento antes de solicitar otro.');
            }
        }

        $code = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $codeHash = password_hash($code, PASSWORD_DEFAULT);
        if ($codeHash === false) {
            api_throw(500, 'CODE_HASH_ERROR', 'No se pudo procesar la verificación.');
        }

        $expiresAt = (new DateTimeImmutable('now', new DateTimeZone('UTC')))
            ->modify('+10 minutes')
            ->format('Y-m-d H:i:s');

        $upsert = $db->prepare(
            "INSERT INTO pending_email_verifications
                (email, name, password_hash, verification_code_hash, code_expires_at,
                 attempts, max_attempts, verified, purpose)
             VALUES (?, '', '', ?, ?, 0, 5, 0, 'password_reset')
             ON DUPLICATE KEY UPDATE
                verification_code_hash = VALUES(verification_code_hash),
                code_expires_at = VALUES(code_expires_at),
                attempts = 0,
                max_attempts = 5,
                verified = 0,
                updated_at = CURRENT_TIMESTAMP"
        );
        $upsert->bind_param('sss', $email, $codeHash, $expiresAt);
        $upsert->execute();
        $upsert->close();

        if (!sendVerificationEmail(
            $email,
            'Solicitud de cambio de contraseña',
            'Tu código de confirmación es: ' . $code
        )) {
            $delete = $db->prepare(
                "DELETE FROM pending_email_verifications
                 WHERE email = ? AND purpose = 'password_reset'"
            );
            $delete->bind_param('s', $email);
            $delete->execute();
            $delete->close();
            api_throw(500, 'EMAIL_SEND_FAILED', 'Falló el envío del correo desde el servidor.');
        }

        json_response([
            'success' => true,
            'message' => 'Código enviado.',
            'email' => $email,
            'expires_in_seconds' => 600,
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
