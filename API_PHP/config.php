<?php

declare(strict_types=1);

/**
 * Dclare API - Fictional / Local Development Configuration
 * 
 * Replace with your local environment variables or database credentials.
 * NEVER commit production credentials to version control.
 */
return [
    // Base de datos (Local / Test)
    'DB_HOST' => getenv('DB_HOST') ?: '127.0.0.1',
    'DB_PORT' => (int)(getenv('DB_PORT') ?: 3306),
    'DB_NAME' => getenv('DB_NAME') ?: 'dclare_db',
    'DB_USER' => getenv('DB_USER') ?: 'dclare_user',
    'DB_PASSWORD' => getenv('DB_PASSWORD') ?: 'dclare_pass123',

    // Correo Transaccional (SMTP Dummy para pruebas)
    'SMTP_USERNAME' => getenv('SMTP_USERNAME') ?: 'notifications@dclare.mock',
    'SMTP_PASSWORD' => getenv('SMTP_PASSWORD') ?: 'dummy_smtp_secret_mock',
    'SMTP_FROM_EMAIL' => getenv('SMTP_FROM_EMAIL') ?: 'no-reply@dclare.mock',
    'SMTP_FROM_NAME' => 'Dclare Fiscal Shield',

    // Google Sign-In (OAuth Client ID Simulado)
    'GOOGLE_CLIENT_IDS' => getenv('GOOGLE_CLIENT_IDS') ?: 'dummy-oauth-client-id-dclare.apps.googleusercontent.com',

    // Sesiones Dclare (Vigencia en días)
    'API_SESSION_TTL_DAYS' => 30,

    // Comprobantes de membresía (Carpeta local de prueba)
    'MEMBERSHIP_RECEIPT_DIR' => __DIR__ . '/storage/receipts',

    // Soporte al cliente / WhatsApp simulado
    'MEMBERSHIP_WHATSAPP_NUMBER' => '15550192834',
];

