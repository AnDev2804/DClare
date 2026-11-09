<?php

declare(strict_types=1);

/*
 * Endpoint TEMPORAL de diagnóstico.
 *
 * Requiere cuenta Admin.
 * Eliminar cuando terminen las pruebas.
 */

require_once __DIR__ . '/membership_service.php';

api_run(function (): void {
    api_require_method('GET');

    $db = api_db();

    try {
        auth_require_admin($db);

        $config =
            membership_config($db);

        $rate =
            membership_current_exchange_rate(
                $db
            );

        $priceUsd =
            (float)$config['precioUsd'];

        $amountVes =
            number_format(
                round(
                    $priceUsd *
                    (float)$rate['tasa'],
                    2
                ),
                2,
                '.',
                ''
            );

        json_response([
            'success' => true,
            'data' => [
                'precioUsd' =>
                    $config['precioUsd'],

                'tasaUsdVes' =>
                    $rate,

                'montoVes' =>
                    $amountVes,

                'modoPruebas' =>
                    $config['modoPruebas'],

                'pruebaGratisMinutos' =>
                    $config[
                        'minutosPruebaGratisTest'
                    ],

                'membresiaPagadaMinutos' =>
                    $config[
                        'minutosMembresiaPagadaTest'
                    ],
            ],
        ]);
    } finally {
        if (
            isset($db) &&
            $db instanceof mysqli
        ) {
            try {
                $db->close();
            } catch (
                Throwable $ignored
            ) {
            }
        }
    }
});
