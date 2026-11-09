<?php

declare(strict_types=1);

require_once __DIR__ . '/membership_service.php';

api_run(function (): void {
    api_require_method('POST');
    $data = api_request_data();
    $email = strtolower(api_string($data, 'email', 225));
    $password = is_string($data['pass'] ?? null) ? $data['pass'] : '';

    if ($password === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        api_throw(400, 'MISSING_CREDENTIALS', 'Correo y contraseña son obligatorios.');
    }

    $db = api_db();
    try {
        $userStmt = $db->prepare('SELECT ID, Clave FROM logdata WHERE Correo = ? LIMIT 1');
        $userStmt->bind_param('s', $email);
        $userStmt->execute();
        $userRow = $userStmt->get_result()->fetch_assoc();
        $userStmt->close();
        if (!$userRow || !password_verify($password, (string)$userRow['Clave'])) {
            api_throw(401, 'INVALID_CREDENTIALS', 'Correo o contraseña inválidos.');
        }

        $userId = (int)$userRow['ID'];
        membership_sync_expired($db, $userId);

        $profileStmt = $db->prepare(
            'SELECT l.Correo, l.reg_state, l.num_rol_data, i.nya, r.Rol
             FROM logdata l
             LEFT JOIN infoper i ON i.ID_Foranea = l.ID
             INNER JOIN roles r ON r.Num_Rol = l.num_rol_data
             WHERE l.ID = ? LIMIT 1'
        );
        $profileStmt->bind_param('i', $userId);
        $profileStmt->execute();
        $profile = $profileStmt->get_result()->fetch_assoc();
        $profileStmt->close();

        $session = auth_issue_session($db, $userId);
        $membership = membership_status($db, $userId);

        json_response([
            'success' => true,
            'idUsuario' => $userId,
            'email' => (string)$profile['Correo'],
            'name' => (string)($profile['nya'] ?? ''),
            'state' => (int)$profile['reg_state'],
            'numRol' => (int)$profile['num_rol_data'],
            'rol' => (string)$profile['Rol'],
            'token' => $session['token'],
            'tokenType' => $session['tokenType'],
            'tokenExpiresAt' => $session['expiresAt'],
            'membership' => $membership,
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
