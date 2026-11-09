<?php

declare(strict_types=1);

require_once __DIR__ . '/admin_service.php';

api_run(function (): void {
    api_require_method('POST');

    $db = api_db();

    try {
        $admin = auth_require_admin($db);

        $data = api_request_data();

        $query = api_string(
            $data,
            'query',
            225,
            false
        ) ?? '';

        $limit = isset($data['limit'])
            ? (int)$data['limit']
            : 100;

        $safeLimit = max(1, min($limit, 100));

        $searchLimit = min(100, $safeLimit + 1);

        $users = admin_search_users(
            $db,
            $query,
            $searchLimit
        );

        $adminId = (int)$admin['idUsuario'];

        $users = array_values(
            array_filter(
                $users,
                static function (array $user) use ($adminId): bool {
                    return (int)($user['idUsuario'] ?? 0) !== $adminId;
                }
            )
        );

        $users = array_slice(
            $users,
            0,
            $safeLimit
        );

        json_response([
            'success' => true,
            'data' => $users,
            'count' => count($users),
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
