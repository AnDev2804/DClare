<?php

declare(strict_types=1);

require_once __DIR__ . '/api_bootstrap.php';

const AUTH_ROLE_NO_PREMIUM = 1;
const AUTH_ROLE_PREMIUM = 2;
const AUTH_ROLE_ADMIN = 3;

function auth_issue_session(mysqli $db, int $userId): array
{
    /*
     * Duraci¨®n de las sesiones:
     * 1. Variable de entorno, si existe.
     * 2. Archivo privado config.php.
     * 3. Valor predeterminado de 30 d¨ªas.
     */
    $config = clara_private_config();

    $envTtl = getenv('API_SESSION_TTL_DAYS');

    $ttlDays = (int)(
        $envTtl !== false && $envTtl !== ''
            ? $envTtl
            : ($config['API_SESSION_TTL_DAYS'] ?? 30)
    );

    $ttlDays = max(1, min($ttlDays, 365));

    $plainToken = bin2hex(random_bytes(32));

    $plainToken = bin2hex(random_bytes(32));
    $tokenHash = hash('sha256', $plainToken);
    $expiresAt = (new DateTimeImmutable('now', new DateTimeZone('UTC')))
        ->modify("+{$ttlDays} days")
        ->format('Y-m-d H:i:s');

    $remoteIp = trim((string)($_SERVER['REMOTE_ADDR'] ?? ''));
    $packedIp = $remoteIp !== '' ? @inet_pton($remoteIp) : false;
    $ipCreated = $packedIp === false ? null : $packedIp;
    $userAgent = mb_substr(trim((string)($_SERVER['HTTP_USER_AGENT'] ?? '')), 0, 255, 'UTF-8');
    $userAgent = $userAgent === '' ? null : $userAgent;

    $stmt = $db->prepare(
        'INSERT INTO auth_sessions
            (id_usuario, token_hash, expires_at, ip_created, user_agent)
         VALUES (?, ?, ?, ?, ?)'
    );
    $stmt->bind_param('issss', $userId, $tokenHash, $expiresAt, $ipCreated, $userAgent);
    $stmt->execute();
    $stmt->close();

    $cleanup = $db->prepare(
        'DELETE FROM auth_sessions
         WHERE id_usuario = ?
           AND (expires_at < DATE_SUB(UTC_TIMESTAMP(), INTERVAL 7 DAY)
                OR revoked_at < DATE_SUB(UTC_TIMESTAMP(), INTERVAL 7 DAY))'
    );
    $cleanup->bind_param('i', $userId);
    $cleanup->execute();
    $cleanup->close();

    return [
        'token' => $plainToken,
        'tokenType' => 'Bearer',
        'expiresAt' => str_replace(' ', 'T', $expiresAt) . 'Z',
    ];
}

function auth_extract_bearer_token(): string
{
    $authorization = trim((string)($_SERVER['HTTP_AUTHORIZATION'] ?? ''));

    if ($authorization === '' && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        $authorization = trim((string)$_SERVER['REDIRECT_HTTP_AUTHORIZATION']);
    }

    if ($authorization === '' && function_exists('apache_request_headers')) {
        $headers = apache_request_headers();
        if (is_array($headers)) {
            $authorization = trim((string)($headers['Authorization'] ?? $headers['authorization'] ?? ''));
        }
    }

    if (!preg_match('/^Bearer\s+([A-Fa-f0-9]{64})$/', $authorization, $matches)) {
        api_throw(401, 'AUTH_REQUIRED', 'Debe iniciar sesiÃ³n para continuar.');
    }

    return strtolower($matches[1]);
}

function auth_require_user(mysqli $db): array
{
    $plainToken = auth_extract_bearer_token();
    $tokenHash = hash('sha256', $plainToken);

    $stmt = $db->prepare(
        'SELECT
            s.id_session,
            l.ID AS id_usuario,
            l.Correo AS correo,
            l.reg_state,
            l.num_rol_data,
            i.nya,
            r.Rol AS rol
         FROM auth_sessions s
         INNER JOIN logdata l ON l.ID = s.id_usuario
         LEFT JOIN infoper i ON i.ID_Foranea = l.ID
         INNER JOIN roles r ON r.Num_Rol = l.num_rol_data
         WHERE s.token_hash = ?
           AND s.revoked_at IS NULL
           AND s.expires_at > UTC_TIMESTAMP()
         LIMIT 1'
    );
    $stmt->bind_param('s', $tokenHash);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row) {
        api_throw(401, 'INVALID_SESSION', 'La sesiÃ³n es invÃ¡lida o ha vencido.');
    }

    $sessionId = (int)$row['id_session'];
    $touch = $db->prepare('UPDATE auth_sessions SET last_used_at = UTC_TIMESTAMP() WHERE id_session = ?');
    $touch->bind_param('i', $sessionId);
    $touch->execute();
    $touch->close();

    return [
        'sessionId' => $sessionId,
        'idUsuario' => (int)$row['id_usuario'],
        'correo' => (string)$row['correo'],
        'nombre' => (string)($row['nya'] ?? ''),
        'regState' => (int)$row['reg_state'],
        'numRol' => (int)$row['num_rol_data'],
        'rol' => (string)$row['rol'],
    ];
}

function auth_require_admin(mysqli $db): array
{
    $user = auth_require_user($db);
    if ($user['numRol'] !== AUTH_ROLE_ADMIN) {
        api_throw(403, 'ADMIN_REQUIRED', 'No tiene permisos para acceder a esta funciÃ³n.');
    }
    return $user;
}

function auth_revoke_session(mysqli $db, int $sessionId): void
{
    $stmt = $db->prepare(
        'UPDATE auth_sessions
         SET revoked_at = COALESCE(revoked_at, UTC_TIMESTAMP())
         WHERE id_session = ?'
    );
    $stmt->bind_param('i', $sessionId);
    $stmt->execute();
    $stmt->close();
}

