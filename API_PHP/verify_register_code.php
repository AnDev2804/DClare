<?php

require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/response.php';

ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response([
        'success' => false,
        'code' => 'METHOD_NOT_ALLOWED',
        'message' => 'Método no permitido'
    ], 405);
}

$email = strtolower(trim((string)($_POST['email'] ?? '')));
$code = trim((string)($_POST['code'] ?? ''));

if ($email === '' || $code === '') {
    json_response([
        'success' => false,
        'code' => 'MISSING_DATA',
        'message' => 'Faltan datos obligatorios'
    ], 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response([
        'success' => false,
        'code' => 'INVALID_EMAIL',
        'message' => 'Correo inválido'
    ], 422);
}

if (!preg_match('/^\d{6}$/', $code)) {
    json_response([
        'success' => false,
        'code' => 'INVALID_CODE_FORMAT',
        'message' => 'El código debe tener 6 dígitos'
    ], 422);
}

/*
 * No registrar el código de verificación en logs.
 * Es una credencial temporal y no debe quedar expuesta en error.log.
 */

$obj = new conexion();
$pending = $obj->getPendingRegisterByEmail($email);

if (!$pending) {
    json_response([
        'success' => false,
        'code' => 'NO_PENDING_VERIFICATION',
        'message' => 'No existe una verificación pendiente'
    ], 404);
}

if ((int)$pending['verified'] === 1) {
    json_response([
        'success' => false,
        'code' => 'VERIFICATION_ALREADY_USED',
        'message' => 'La verificación ya fue usada'
    ], 409);
}

if ((int)$pending['attempts'] >= (int)$pending['max_attempts']) {
    $obj->deletePendingRegister($email);

    json_response([
        'success' => false,
        'code' => 'ATTEMPTS_EXHAUSTED',
        'message' => 'Se agotaron los intentos'
    ], 429);
}

$expiresAtRaw = trim(
    (string)$pending['code_expires_at']
);

$expiresAtUtc =
    DateTimeImmutable::createFromFormat(
        'Y-m-d H:i:s',
        $expiresAtRaw,
        new DateTimeZone('UTC')
    );

if (
    $expiresAtUtc === false ||
    $expiresAtUtc->getTimestamp() < time()
) {
    $obj->deletePendingRegister($email);

    json_response([
        'success' => false,
        'code' => 'CODE_EXPIRED',
        'message' => 'El código expiró'
    ], 410);
}

if (!password_verify($code, (string)$pending['verification_code_hash'])) {
    $obj->incrementPendingAttempts($email);

    $remaining = max(
        0,
        (int)$pending['max_attempts'] -
            ((int)$pending['attempts'] + 1)
    );

    json_response([
        'success' => false,
        'code' => 'INVALID_CODE',
        'message' => 'Código incorrecto',
        'remaining_attempts' => $remaining
    ], 401);
}

if ($obj->emailExists($email)) {
    $obj->deletePendingRegister($email);

    json_response([
        'success' => false,
        'code' => 'EMAIL_ALREADY_REGISTERED',
        'message' => 'Correo ya registrado'
    ], 409);
}

$registered = $obj->registerVerifiedUser(
    $pending['name'],
    $pending['email'],
    $pending['password_hash']
);

if (!$registered) {
    json_response([
        'success' => false,
        'code' => 'REGISTRATION_FAILED',
        'message' => 'No se pudo completar el registro'
    ], 500);
}

$obj->deletePendingRegister($email);

json_response([
    'success' => true,
    'code' => 'REGISTERED',
    'message' => 'Usuario registrado exitosamente'
]);
