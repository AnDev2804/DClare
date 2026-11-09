<?php

declare(strict_types=1);

if (!function_exists('clara_private_config')) {
    require_once __DIR__ . '/connect.php';
}

$autoload =
    __DIR__ . '/vendor/autoload.php';

if (is_file($autoload)) {
    require_once $autoload;
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendVerificationEmail(
    string $to,
    string $subject,
    string $body
): bool {

    /*
     * Evita un Fatal Error si Composer/vendor queda
     * incompleto en algún despliegue.
     */
    if (
        !class_exists(
            PHPMailer::class
        )
    ) {
        error_log(
            'PHPMailer no está disponible. ' .
            'Ejecute Composer e instale phpmailer/phpmailer.'
        );

        return false;
    }
    
    $config = clara_private_config();

    $username = trim(
        (string)(
            getenv('SMTP_USERNAME')
            ?: ($config['SMTP_USERNAME'] ?? '')
        )
    );
    
    $password =
        (string)(
            getenv('SMTP_PASSWORD')
            ?: ($config['SMTP_PASSWORD'] ?? '')
        );
    
    $fromEmail = trim(
        (string)(
            getenv('SMTP_FROM_EMAIL')
            ?: ($config['SMTP_FROM_EMAIL'] ?? $username)
        )
    );
    
    $fromName = trim(
        (string)(
            getenv('SMTP_FROM_NAME')
            ?: ($config['SMTP_FROM_NAME'] ?? 'Clara')
        )
    );

    if (
        $username === '' ||
        $password === ''
    ) {
        error_log(
            'SMTP no configurado: faltan ' .
            'SMTP_USERNAME o SMTP_PASSWORD.'
        );

        return false;
    }

    if (
        $fromEmail === '' ||
        !filter_var(
            $fromEmail,
            FILTER_VALIDATE_EMAIL
        )
    ) {
        error_log(
            'SMTP_FROM_EMAIL tiene un valor inválido.'
        );

        return false;
    }

    try {
        $mail =
            new PHPMailer(true);

        $mail->isSMTP();

        $mail->Host =
            'smtp.gmail.com';

        $mail->SMTPAuth =
            true;

        $mail->Username =
            $username;

        $mail->Password =
            $password;

        $mail->SMTPSecure =
            PHPMailer::ENCRYPTION_STARTTLS;

        $mail->Port =
            587;

        $mail->CharSet =
            'UTF-8';

        $mail->setFrom(
            $fromEmail,
            $fromName
        );

        $mail->addAddress(
            $to
        );

        $mail->Subject =
            $subject;

        $mail->Body =
            $body;

        $mail->isHTML(false);

        return $mail->send();

    } catch (Throwable $e) {

        error_log(
            'PHPMailer error: ' .
            get_class($e) .
            ': ' .
            $e->getMessage()
        );

        return false;
    }
}