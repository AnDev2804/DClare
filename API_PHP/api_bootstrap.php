<?php

declare(strict_types=1);

date_default_timezone_set('UTC');
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/response.php';

final class ApiHttpException extends RuntimeException
{
    private int $httpStatus;
    private string $errorCode;
    private array $details;

    public function __construct(
        int $httpStatus,
        string $errorCode,
        string $message,
        array $details = []
    ) {
        parent::__construct($message);
        $this->httpStatus = $httpStatus;
        $this->errorCode = $errorCode;
        $this->details = $details;
    }

    public function getHttpStatus(): int
    {
        return $this->httpStatus;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getDetails(): array
    {
        return $this->details;
    }
}

/*
 * PHP 8.1+ permite `never`. Además de documentar correctamente el flujo,
 * esto hace que Intelephense entienda que después de api_throw() no existe
 * un camino de ejecución que necesite retornar un valor.
 */
function api_throw(int $status, string $code, string $message, array $details = []): never
{
    throw new ApiHttpException($status, $code, $message, $details);
}

function api_require_method(string $method): void
{
    $actual = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? ''));
    if ($actual !== strtoupper($method)) {
        header('Allow: ' . strtoupper($method));
        api_throw(405, 'METHOD_NOT_ALLOWED', 'Método no permitido.');
    }
}

function api_request_data(): array
{
    if (!empty($_POST)) {
        return $_POST;
    }

    $raw = file_get_contents('php://input');
    if ($raw === false || trim($raw) === '') {
        return [];
    }

    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        api_throw(400, 'INVALID_JSON', 'El cuerpo JSON no es válido.');
    }

    return $decoded;
}

function api_db(): mysqli
{
    try {
        $wrapper = new conexion();
        $db = $wrapper->getConex();

        if (!$db instanceof mysqli) {
            api_throw(503, 'DATABASE_UNAVAILABLE', 'No fue posible conectar con la base de datos.');
        }

        $db->set_charset('utf8mb4');
        $db->query("SET time_zone = '+00:00'");
        return $db;
    } catch (ApiHttpException $e) {
        throw $e;
    } catch (Throwable $e) {
        error_log('Database connection error: ' . $e->getMessage());
        api_throw(503, 'DATABASE_UNAVAILABLE', 'No fue posible conectar con la base de datos.');
    }
}

function api_string(array $data, string $key, int $maxLength, bool $required = true): ?string
{
    if (array_key_exists($key, $data)
        && !is_string($data[$key])
        && !is_int($data[$key])
        && !is_float($data[$key])) {
        api_throw(422, 'VALIDATION_ERROR', "El campo {$key} tiene un formato inválido.", ['field' => $key]);
    }

    $value = isset($data[$key]) ? trim((string)$data[$key]) : '';

    if ($value === '') {
        if ($required) {
            api_throw(422, 'VALIDATION_ERROR', "El campo {$key} es obligatorio.", ['field' => $key]);
        }
        return null;
    }

    if (mb_strlen($value, 'UTF-8') > $maxLength) {
        api_throw(422, 'VALIDATION_ERROR', "El campo {$key} es demasiado largo.", ['field' => $key]);
    }

    return $value;
}

function api_positive_int(mixed $value, string $field): int
{
    $filtered = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($filtered === false) {
        api_throw(422, 'VALIDATION_ERROR', "El campo {$field} debe ser un entero positivo.", ['field' => $field]);
    }
    return (int)$filtered;
}

function api_bool(mixed $value, string $field): bool
{
    if (is_bool($value)) {
        return $value;
    }

    if ($value === 1 || $value === '1' || $value === 'true' || $value === 'TRUE') {
        return true;
    }

    if ($value === 0 || $value === '0' || $value === 'false' || $value === 'FALSE') {
        return false;
    }

    api_throw(422, 'VALIDATION_ERROR', "El campo {$field} debe ser booleano.", ['field' => $field]);
}

function api_run(callable $handler): void
{
    try {
        $handler();
    } catch (ApiHttpException $e) {
        $payload = [
            'success' => false,
            'code' => $e->getErrorCode(),
            'message' => $e->getMessage(),
        ];

        if ($e->getDetails() !== []) {
            $payload['details'] = $e->getDetails();
        }

        json_response($payload, $e->getHttpStatus());
    } catch (Throwable $e) {
        error_log('Unhandled API error: ' . $e->getMessage());
        json_response([
            'success' => false,
            'code' => 'INTERNAL_ERROR',
            'message' => 'Ocurrió un error interno.',
        ], 500);
    }
}
