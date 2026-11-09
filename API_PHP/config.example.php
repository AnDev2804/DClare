<?php

declare(strict_types=1);

/**
 * Dclare API - Configuration Template
 */
return [
    'DB_HOST' => '127.0.0.1',
    'DB_PORT' => 3306,
    'DB_NAME' => 'dclare_db',
    'DB_USER' => 'dclare_user',
    'DB_PASSWORD' => 'your_secure_password',

    'SMTP_USERNAME' => 'notifications@dclare.mock',
    'SMTP_PASSWORD' => 'your_smtp_password',
    'SMTP_FROM_EMAIL' => 'no-reply@dclare.mock',
    'SMTP_FROM_NAME' => 'Dclare Fiscal Shield',

    'GOOGLE_CLIENT_IDS' => 'your-google-oauth-client-id.apps.googleusercontent.com',
    'API_SESSION_TTL_DAYS' => 30,
    'MEMBERSHIP_RECEIPT_DIR' => __DIR__ . '/storage/receipts',
    'MEMBERSHIP_WHATSAPP_NUMBER' => '15550192834',
];

