<?php

declare(strict_types=1);

require_once __DIR__ . '/membership_service.php';

api_run(function (): void {
    api_require_method(['GET', 'POST']);
    $db = api_db();

    try {
        $user = auth_require_user($db);
        $userId = (int)$user['idUsuario'];

        membership_sync_expired($db, $userId);
        $role = membership_user_role($db, $userId);
        $isPremiumOrAdmin = ($role === AUTH_ROLE_PREMIUM || $role === AUTH_ROLE_ADMIN);

        $stmt = $db->prepare('SELECT COUNT(*) AS total FROM empresas_eliminadas WHERE ID_Foranea = ?');
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        $deletedCount = (int)($row['total'] ?? 0);

        json_response([
            'success' => true,
            'isPremium' => $isPremiumOrAdmin,
            'deletedCount' => $deletedCount,
            'maxAllowed' => 3,
            'remainingDeletions' => $isPremiumOrAdmin ? null : max(0, 3 - $deletedCount),
            'canDelete' => $isPremiumOrAdmin || ($deletedCount < 3),
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
