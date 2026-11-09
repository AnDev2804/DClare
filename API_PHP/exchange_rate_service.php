<?php

declare(strict_types=1);

/*
 * Servicio interno de tasa USD/VES.
 *
 * Fuente:
 *   DolarApi Venezuela -> Dólar Oficial (BCV)
 *   https://ve.dolarapi.com/v1/dolares/oficial
 *
 * No es un endpoint público. membership_service.php lo consume internamente.
 */

const EXCHANGE_RATE_DOLARAPI_URL =
    'https://ve.dolarapi.com/v1/dolares/oficial';

const EXCHANGE_RATE_SOURCE =
    'BCV via DolarApi';

/**
 * Devuelve la fecha actual de Venezuela.
 */
function exchange_rate_today_caracas(): string
{
    return (new DateTimeImmutable(
        'now',
        new DateTimeZone('America/Caracas')
    ))->format('Y-m-d');
}

/**
 * Consulta DolarApi y valida la respuesta.
 *
 * Usa el campo "promedio", que corresponde a la cotización oficial
 * expuesta por el endpoint de Dólar Oficial.
 */
function exchange_rate_fetch_dolarapi_bcv(): array
{
    if (!function_exists('curl_init')) {
        throw new RuntimeException(
            'La extensión cURL de PHP no está habilitada.'
        );
    }

    $ch = curl_init(
        EXCHANGE_RATE_DOLARAPI_URL
    );

    if ($ch === false) {
        throw new RuntimeException(
            'No fue posible inicializar cURL.'
        );
    }

    curl_setopt_array(
        $ch,
        [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 10,

            CURLOPT_HTTPHEADER => [
                'Accept: application/json',
                'User-Agent: ClaraMembership/1.0',
            ],

            /*
             * No desactivar estas validaciones.
             * Si XAMPP reporta un error de CA, configure curl.cainfo /
             * openssl.cafile en php.ini.
             */
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]
    );

    $body = curl_exec($ch);

    $curlError =
        curl_error($ch);

    $curlErrno =
        curl_errno($ch);

    $httpStatus =
        (int)curl_getinfo(
            $ch,
            CURLINFO_RESPONSE_CODE
        );

    curl_close($ch);

    if ($body === false) {
        throw new RuntimeException(
            'DolarApi no pudo ser consultada. cURL #' .
            $curlErrno .
            ': ' .
            $curlError
        );
    }

    if (
        $httpStatus < 200 ||
        $httpStatus >= 300
    ) {
        throw new RuntimeException(
            'DolarApi respondió HTTP ' .
            $httpStatus .
            '.'
        );
    }

    try {
        $json =
            json_decode(
                (string)$body,
                true,
                512,
                JSON_THROW_ON_ERROR
            );
    } catch (JsonException $e) {
        throw new RuntimeException(
            'DolarApi devolvió JSON inválido.',
            0,
            $e
        );
    }

    if (!is_array($json)) {
        throw new RuntimeException(
            'DolarApi devolvió una respuesta inesperada.'
        );
    }

    $rateRaw =
        $json['promedio']
        ?? null;

    if (
        !is_numeric($rateRaw) ||
        (float)$rateRaw <= 0
    ) {
        throw new RuntimeException(
            'DolarApi no devolvió una tasa oficial válida.'
        );
    }

    $sourceCode =
        strtolower(
            trim(
                (string)(
                    $json['fuente']
                    ?? ''
                )
            )
        );

    /*
     * El endpoint /oficial debe devolver fuente=oficial.
     */
    if (
        $sourceCode !== '' &&
        $sourceCode !== 'oficial'
    ) {
        throw new RuntimeException(
            'La respuesta no corresponde a la cotización oficial.'
        );
    }

    $providerUpdatedAt = null;

    $providerDateRaw =
        trim(
            (string)(
                $json['fechaActualizacion']
                ?? ''
            )
        );

    if ($providerDateRaw !== '') {
        try {
            $providerUpdatedAt =
                (new DateTimeImmutable(
                    $providerDateRaw
                ))
                ->setTimezone(
                    new DateTimeZone(
                        'America/Caracas'
                    )
                )
                ->format(
                    DateTimeInterface::ATOM
                );
        } catch (Throwable $ignored) {
            /*
             * La tasa sigue siendo utilizable; la fecha del proveedor
             * se trata como metadata opcional.
             */
            $providerUpdatedAt = null;
        }
    }

    return [
        'tasa' =>
            number_format(
                (float)$rateRaw,
                8,
                '.',
                ''
            ),

        'fuente' =>
            EXCHANGE_RATE_SOURCE,

        'fechaActualizacionFuente' =>
            $providerUpdatedAt,
    ];
}

/**
 * Busca primero el snapshot del día en BD.
 * Si no existe, consulta DolarApi, lo guarda y lo devuelve.
 *
 * La tabla ya posee:
 * uq_tasa_dia_fuente
 * (moneda_base, moneda_destino, fuente, fecha_tasa)
 *
 * por lo que no se requiere una tabla nueva.
 */
function exchange_rate_get_current_bcv(
    mysqli $db
): array {
    $today =
        exchange_rate_today_caracas();

    $source =
        EXCHANGE_RATE_SOURCE;

    $stmt = $db->prepare(
        "SELECT
            id_tasa,
            tasa,
            fuente,
            fecha_tasa,
            fecha_consulta
         FROM membresia_tasas_cambio
         WHERE moneda_base = 'USD'
           AND moneda_destino = 'VES'
           AND fuente = ?
           AND activo = 1
           AND fecha_tasa = ?
         ORDER BY id_tasa DESC
         LIMIT 1"
    );

    $stmt->bind_param(
        'ss',
        $source,
        $today
    );

    $stmt->execute();

    $existing =
        $stmt
            ->get_result()
            ->fetch_assoc();

    $stmt->close();

    if ($existing) {
        return [
            'idTasa' =>
                (int)$existing['id_tasa'],

            'tasa' =>
                (string)$existing['tasa'],

            'fuente' =>
                (string)$existing['fuente'],

            'fechaTasa' =>
                (string)$existing['fecha_tasa'],

            'fechaConsulta' =>
                membership_utc(
                    $existing['fecha_consulta']
                ),

            'fechaActualizacionFuente' =>
                null,

            'cached' =>
                true,
        ];
    }

    $external =
        exchange_rate_fetch_dolarapi_bcv();

    $rate =
        (string)$external['tasa'];

    /*
     * fecha_tasa representa el día para el cual Clara utiliza
     * este snapshot de la cotización oficial vigente.
     *
     * fecha_consulta conserva cuándo se consultó el proveedor.
     */
    $insert = $db->prepare(
        "INSERT INTO membresia_tasas_cambio
            (
                moneda_base,
                moneda_destino,
                tasa,
                fuente,
                fecha_tasa,
                fecha_consulta,
                activo
            )
         VALUES
            (
                'USD',
                'VES',
                ?,
                ?,
                ?,
                UTC_TIMESTAMP(),
                1
            )
         ON DUPLICATE KEY UPDATE
            tasa = VALUES(tasa),
            fecha_consulta = UTC_TIMESTAMP(),
            activo = 1,
            updated_at = CURRENT_TIMESTAMP"
    );

    $insert->bind_param(
        'sss',
        $rate,
        $source,
        $today
    );

    $insert->execute();
    $insert->close();

    /*
     * Reconsultamos para obtener id_tasa y el timestamp real guardado.
     */
    $read = $db->prepare(
        "SELECT
            id_tasa,
            tasa,
            fuente,
            fecha_tasa,
            fecha_consulta
         FROM membresia_tasas_cambio
         WHERE moneda_base = 'USD'
           AND moneda_destino = 'VES'
           AND fuente = ?
           AND activo = 1
           AND fecha_tasa = ?
         ORDER BY id_tasa DESC
         LIMIT 1"
    );

    $read->bind_param(
        'ss',
        $source,
        $today
    );

    $read->execute();

    $row =
        $read
            ->get_result()
            ->fetch_assoc();

    $read->close();

    if (!$row) {
        throw new RuntimeException(
            'La tasa fue consultada pero no pudo guardarse.'
        );
    }

    return [
        'idTasa' =>
            (int)$row['id_tasa'],

        'tasa' =>
            (string)$row['tasa'],

        'fuente' =>
            (string)$row['fuente'],

        'fechaTasa' =>
            (string)$row['fecha_tasa'],

        'fechaConsulta' =>
            membership_utc(
                $row['fecha_consulta']
            ),

        'fechaActualizacionFuente' =>
            $external[
                'fechaActualizacionFuente'
            ],

        'cached' =>
            false,
    ];
}
