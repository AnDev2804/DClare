<?php

declare(strict_types=1);

function json_response(array $payload, int $status = 200): void
{
    http_response_code($status);
    if ($status === 401) {
        header('WWW-Authenticate: Bearer error="unauthorized"');
    }
    header('Content-Type: application/json; charset=UTF-8');
    header('Cache-Control: no-store');

    echo json_encode(
        $payload,
        JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE
    );
    exit;
}

