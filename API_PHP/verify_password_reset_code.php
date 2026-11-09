<?php

declare(strict_types=1);

require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/response.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response([
        'success' => false,
        'code' => 'METHOD_NOT_ALLOWED',
        'message' => 'Método no permitido.',
    ], 405);
}

$email = strtolower(trim((string)($_POST['email'] ?? '')));
$code = trim((string)($_POST['code'] ?? ''));

if (
    $email === '' ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    !preg_match('/^\d{6}$/', $code)
) {
    json_response([
        'success' => false,
        'code' => 'INVALID_INPUT',
        'message' => 'Correo o código de verificación inválido.',
    ], 422);
}

$obj = new conexion();
$db = $obj->getConex();

if (!$db instanceof mysqli) {
    json_response([
        'success' => false,
        'code' => 'DATABASE_UNAVAILABLE',
        'message' => 'No fue posible conectar con el servidor.',
    ], 500);
}

try {
    mysqli_begin_transaction($db);

    $stmt = $db->prepare(
        "SELECT
            id,
            verification_code_hash,
            code_expires_at,
            attempts,
            max_attempts,
            verified
         FROM pending_email_verifications
         WHERE email = ?
           AND purpose = 'password_reset'
         LIMIT 1
         FOR UPDATE"
    );

    $stmt->bind_param('s', $email);
    $stmt->execute();
    $pending = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$pending) {
        mysqli_rollback($db);
        json_response([
            'success' => false,
            'code' => 'NO_PENDING_RESET',
            'message' => 'No existe una recuperación de contraseña pendiente.',
        ], 404);
    }

    if ((int)$pending['verified'] === 1) {
        mysqli_rollback($db);
        json_response([
            'success' => false,
            'code' => 'RESET_ALREADY_VERIFIED',
            'message' => 'Este código ya fue validado. Solicite un nuevo código si necesita reiniciar el proceso.',
        ], 409);
    }

    $attempts = (int)$pending['attempts'];
    $maxAttempts = (int)$pending['max_attempts'];

    if ($attempts >= $maxAttempts) {
        $delete = $db->prepare(
            "DELETE FROM pending_email_verifications
             WHERE email = ?
               AND purpose = 'password_reset'"
        );
        $delete->bind_param('s', $email);
        $delete->execute();
        $delete->close();

        mysqli_commit($db);

        json_response([
            'success' => false,
            'code' => 'ATTEMPTS_EXHAUSTED',
            'message' => 'Se agotaron los intentos. Solicite un nuevo código.',
        ], 429);
    }

    $expiresAtRaw = trim(
        (string)$pending['code_expires_at']
    );

    $expiresAtUtc = DateTimeImmutable::createFromFormat(
        'Y-m-d H:i:s',
        $expiresAtRaw,
        new DateTimeZone('UTC')
    );

    if (
        $expiresAtUtc === false ||
        $expiresAtUtc->getTimestamp() < time()
    ) {
        $delete = $db->prepare(
            "DELETE FROM pending_email_verifications
            WHERE email = ?
            AND purpose = 'password_reset'"
        );

        $delete->bind_param('s', $email);
        $delete->execute();
        $delete->close();

        mysqli_commit($db);

        json_response([
            'success' => false,
            'code' => 'CODE_EXPIRED',
            'message' => 'El código expiró. Solicite uno nuevo.',
        ], 410);
    }

    if (
        !password_verify(
            $code,
            (string)$pending['verification_code_hash']
        )
    ) {
        $newAttempts = $attempts + 1;
        $pendingId = (int)$pending['id'];

        $increment = $db->prepare(
            "UPDATE pending_email_verifications
             SET attempts = ?
             WHERE id = ?"
        );
        $increment->bind_param(
            'ii',
            $newAttempts,
            $pendingId
        );
        $increment->execute();
        $increment->close();

        $remaining = max(
            0,
            $maxAttempts - $newAttempts
        );

        if ($remaining === 0) {
            $delete = $db->prepare(
                "DELETE FROM pending_email_verifications
                 WHERE id = ?"
            );
            $delete->bind_param(
                'i',
                $pendingId
            );
            $delete->execute();
            $delete->close();

            mysqli_commit($db);

            json_response([
                'success' => false,
                'code' => 'ATTEMPTS_EXHAUSTED',
                'message' => 'Se agotaron los intentos. Solicite un nuevo código.',
                'remaining_attempts' => 0,
            ], 429);
        }

        mysqli_commit($db);

        json_response([
            'success' => false,
            'code' => 'INVALID_CODE',
            'message' => 'Código incorrecto.',
            'remaining_attempts' => $remaining,
        ], 401);
    }

    /*
     * No requiere migración de BD:
     * para purpose='password_reset', password_hash se reutiliza
     * únicamente para guardar el hash SHA-256 del resetToken.
     * code_expires_at pasa a ser la expiración del resetToken.
     */
    $resetToken = bin2hex(random_bytes(32));
    $resetTokenHash = hash('sha256', $resetToken);

    $tokenExpiresAtSql = gmdate(
        'Y-m-d H:i:s',
        time() + 600
    );

    $pendingId = (int)$pending['id'];

    $update = $db->prepare(
        "UPDATE pending_email_verifications
         SET verified = 1,
             password_hash = ?,
             verification_code_hash = '',
             code_expires_at = ?
         WHERE id = ?"
    );

    $update->bind_param(
        'ssi',
        $resetTokenHash,
        $tokenExpiresAtSql,
        $pendingId
    );
    $update->execute();
    $update->close();

    mysqli_commit($db);

    json_response([
        'success' => true,
        'code' => 'RESET_CODE_VERIFIED',
        'message' => 'Código verificado correctamente.',
        'resetToken' => $resetToken,
        'resetTokenExpiresAt' =>
            str_replace(' ', 'T', $tokenExpiresAtSql) . 'Z',
    ]);
} catch (Throwable $e) {
    error_log(
        '[verify_password_reset_code] ' .
        get_class($e) .
        ': ' .
        $e->getMessage() .
        ' en ' .
        $e->getFile() .
        ':' .
        $e->getLine()
    );

    try {
        mysqli_rollback($db);
    } catch (Throwable $ignored) {
    }

    json_response([
        'success' => false,
        'code' => 'RESET_VERIFICATION_FAILED',
        'message' => 'No fue posible validar el código.',
    ], 500);
} finally {
    try {
        $db->close();
    } catch (Throwable $ignored) {
    }
}
