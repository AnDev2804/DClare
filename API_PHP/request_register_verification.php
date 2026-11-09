<?php
date_default_timezone_set('UTC');
require_once('connect.php');
require_once('response.php');
require_once('mailer.php');

ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    json_response(["success" => false, "message" => "Método no permitido"], 405);
}

$name = trim($_POST["name"] ?? "");
$email = strtolower(trim($_POST["email"] ?? ""));
$pass = $_POST["pass"] ?? "";

if ($name === "" || $email === "" || $pass === "") {
    json_response(["success" => false, "message" => "Faltan datos obligatorios"], 400);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    json_response(["success" => false, "message" => "Correo inválido"], 400);
}

$obj = new conexion();
$lockKey = "register_verification_" . md5($email);
$lockAcquired = false;

try {
    $lockAcquired = $obj->acquireNamedLock($lockKey, 5);

    if (!$lockAcquired) {
        json_response([
            "success" => false,
            "message" => "No se pudo procesar la solicitud en este momento. Intenta nuevamente."
        ], 429);
    }

    if ($obj->emailExists($email)) {
        json_response(["success" => false, "message" => "Correo ya registrado"], 409);
    }

    $pending = $obj->getPendingRegisterByEmail($email);

    if ($pending && (int)$pending["verified"] === 0) {

        $utc = new DateTimeZone('UTC');

        $expiresAtUtc =
            DateTimeImmutable::createFromFormat(
                'Y-m-d H:i:s',
                trim((string)$pending['code_expires_at']),
                $utc
            );

        $updatedAtUtc =
            DateTimeImmutable::createFromFormat(
                'Y-m-d H:i:s',
                trim((string)$pending['updated_at']),
                $utc
            );

        $now = time();

        $notExpired =
            $expiresAtUtc !== false &&
            $expiresAtUtc->getTimestamp() > $now;

        $cooldownActive =
            $updatedAtUtc !== false &&
            ($now - $updatedAtUtc->getTimestamp()) < 60;

        if ($notExpired && $cooldownActive) {
            json_response([
                'success' => false,
                'code' => 'REGISTER_CODE_ALREADY_SENT',
                'message' =>
                    'Ya se envió un código recientemente. Revisa tu correo o espera un momento antes de solicitar otro.'
            ], 429);
        }
    }

    $code = str_pad(strval(random_int(0, 999999)), 6, "0", STR_PAD_LEFT);
    $codeHash = password_hash($code, PASSWORD_DEFAULT);
    $passwordHash = password_hash($pass, PASSWORD_DEFAULT);
    $expiresAt = gmdate(
        'Y-m-d H:i:s',
        time() + 600
    );

    $ok = $obj->createOrUpdatePendingRegister($name, $email, $passwordHash, $codeHash, $expiresAt);

    if (!$ok) {
        error_log("No se pudo crear/update pending register para: " . $email);
        json_response(["success" => false, "message" => "No se pudo crear la verificación"], 500);
    }

    $sent = sendVerificationEmail(
        $email,
        "Confirmación de correo electrónico",
        "Tu código de confirmación es: $code"
    );

    error_log("Resultado sendVerificationEmail(register): " . ($sent ? "true" : "false"));

    if (!$sent) {
        // Esto evita dejar un pending activo si el correo no salió
        $obj->deletePendingRegister($email);

        error_log("Falló sendVerificationEmail para register: " . $email);
        json_response([
            "success" => false,
            "message" => "Falló el envío del correo desde el servidor"
        ], 500);
    }

    json_response([
        "success" => true,
        "message" => "Código enviado",
        "email" => $email,
        "expires_in_seconds" => 600
    ]);

} finally {
    if ($lockAcquired) {
        $obj->releaseNamedLock($lockKey);
    }
}
?>