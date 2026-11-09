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
$pass = (string)($_POST['pass'] ?? '');
$resetToken = strtolower(trim((string)($_POST['resetToken'] ?? '')));

if (
    $email === '' ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    $pass === '' ||
    !preg_match('/^[a-f0-9]{64}$/', $resetToken)
) {
    json_response([
        'success' => false,
        'code' => 'INVALID_INPUT',
        'message' => 'Faltan datos válidos para cambiar la contraseña.',
    ], 422);
}

$passwordPattern =
    '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[*\/\-\+\.,])[a-zA-Z\d*\/\-\+\.,]{8,128}$/';

if (!preg_match($passwordPattern, $pass)) {
    json_response([
        'success' => false,
        'code' => 'INVALID_PASSWORD',
        'message' => 'La contraseña no cumple los requisitos de seguridad.',
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
            p.id,
            p.password_hash AS reset_token_hash,
            p.code_expires_at,
            p.verified,
            l.ID AS id_usuario
         FROM pending_email_verifications p
         INNER JOIN logdata l
            ON l.Correo = p.email
         WHERE p.email = ?
           AND p.purpose = 'password_reset'
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

    if ((int)$pending['verified'] !== 1) {
        mysqli_rollback($db);

        json_response([
            'success' => false,
            'code' => 'RESET_NOT_VERIFIED',
            'message' => 'Primero debe validar el código enviado a su correo.',
        ], 403);
    }

    /*
     * code_expires_at se guarda en UTC. Hay que interpretarlo
     * explícitamente como UTC para evitar falsos vencimientos.
     */
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
        $pendingId = (int)$pending['id'];

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
            'code' => 'RESET_TOKEN_EXPIRED',
            'message' => 'La autorización para cambiar la contraseña expiró. Solicite un nuevo código.',
        ], 410);
    }

    $expectedHash = strtolower(
        trim(
            (string)$pending['reset_token_hash']
        )
    );

    $providedHash = hash(
        'sha256',
        $resetToken
    );

    if (
        $expectedHash === '' ||
        !hash_equals(
            $expectedHash,
            $providedHash
        )
    ) {
        mysqli_rollback($db);

        json_response([
            'success' => false,
            'code' => 'INVALID_RESET_TOKEN',
            'message' => 'La autorización para cambiar la contraseña no es válida.',
        ], 403);
    }

    $newPasswordHash = password_hash(
        $pass,
        PASSWORD_DEFAULT
    );

    if ($newPasswordHash === false) {
        throw new RuntimeException(
            'No se pudo generar el hash de la nueva contraseña.'
        );
    }

    $userId = (int)$pending['id_usuario'];

    $update = $db->prepare(
        'UPDATE logdata
         SET Clave = ?
         WHERE ID = ?'
    );

    $update->bind_param(
        'si',
        $newPasswordHash,
        $userId
    );

    $update->execute();
    $update->close();

    /*
     * Revocamos las sesiones Clara existentes después
     * de cambiar la contraseña.
     */
    $revoke = $db->prepare(
        'DELETE FROM auth_sessions
         WHERE id_usuario = ?'
    );

    $revoke->bind_param(
        'i',
        $userId
    );

    $revoke->execute();
    $revoke->close();

    /*
     * El resetToken queda consumido: eliminamos la solicitud
     * temporal para impedir reutilización.
     */
    $pendingId = (int)$pending['id'];

    $cleanup = $db->prepare(
        'DELETE FROM pending_email_verifications
         WHERE id = ?'
    );

    $cleanup->bind_param(
        'i',
        $pendingId
    );

    $cleanup->execute();
    $cleanup->close();

    mysqli_commit($db);

    json_response([
        'success' => true,
        'code' => 'PASSWORD_RESET',
        'message' => 'Contraseña actualizada correctamente.',
    ]);
} catch (Throwable $e) {
    error_log(
        '[reset_password] ' .
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
        'code' => 'PASSWORD_RESET_FAILED',
        'message' => 'No fue posible cambiar la contraseña.',
    ], 500);
} finally {
    try {
        $db->close();
    } catch (Throwable $ignored) {
    }
}
