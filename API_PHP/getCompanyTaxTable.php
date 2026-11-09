<?php

declare(strict_types=1);

require_once __DIR__ . '/membership_service.php';
require_once __DIR__ . '/connect.php';

/*
 * Tabla fiscal de una empresa concreta.
 *
 * Diferencia con getTable.php:
 *
 * getTable.php
 *   -> Calendario Fiscal general.
 *   -> recibe digit.
 *   -> No Premium solo puede consultar 0, 1 y 2.
 *
 * getCompanyTaxTable.php
 *   -> Detalle/configuración fiscal de una empresa.
 *   -> recibe rif.
 *   -> valida que la empresa pertenezca al usuario y esté
 *      accesible con su plan.
 *   -> deriva el terminal en el servidor.
 *
 * De esta forma un usuario gratuito NO puede usar este
 * endpoint para consultar arbitrariamente terminales 3..9.
 */
api_run(function (): void {
    api_require_method('POST');

    $db = api_db();

    try {
        $user =
            auth_require_user($db);

        $userId =
            (int)$user['idUsuario'];

        /*
         * Puede degradar Premium -> No Premium si venció.
         */
        membership_sync_expired(
            $db,
            $userId
        );

        $data =
            api_request_data();

        $rif =
            strtoupper(
                trim(
                    (string)api_string(
                        $data,
                        'rif',
                        30
                    )
                )
            );

        if ($rif === '') {
            api_throw(
                422,
                'INVALID_RIF',
                'El RIF de la empresa es obligatorio.',
                [
                    'field' => 'rif',
                ]
            );
        }

        /*
         * Esta es la autoridad de seguridad.
         *
         * - comprueba que la empresa pertenezca al usuario;
         * - si es No Premium, solo acepta la empresa que forma
         *   parte de su conjunto accesible;
         * - si quedó oculta por el límite del plan responde 403;
         * - si no pertenece responde 404.
         */
        $company =
            membership_require_accessible_company(
                $db,
                $userId,
                $rif
            );

        $authorizedRif =
            strtoupper(
                trim(
                    (string)$company['rif']
                )
            );

        /*
         * El cliente NO suministra el terminal.
         * Se deriva exclusivamente del RIF autorizado.
         */
        if (
            !preg_match(
                '/([0-9])$/',
                $authorizedRif,
                $matches
            )
        ) {
            api_throw(
                422,
                'INVALID_COMPANY_RIF_TERMINAL',
                'El RIF de la empresa no contiene un terminal numérico válido.'
            );
        }

        $digit =
            (string)$matches[1];

        /*
         * Se conserva la lógica histórica de conexion::getTable()
         * y el mismo JSONArray esperado por Android.
         */
        $obj =
            new conexion();

        $result =
            $obj->getTable(
                $digit
            );

        if ($result === false) {
            api_throw(
                500,
                'COMPANY_TAX_TABLE_QUERY_FAILED',
                'No se pudo consultar la tabla fiscal de la empresa.'
            );
        }

        json_response(
            $result
        );

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
