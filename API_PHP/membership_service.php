<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/exchange_rate_service.php';

const MEMBERSHIP_PAYMENT_REJECTED = 0;
const MEMBERSHIP_PAYMENT_APPROVED = 1;
const MEMBERSHIP_PAYMENT_PENDING = 2;

// Alias temporales para no romper código antiguo mientras termina la migración.
const MEMBERSHIP_PAYMENT_NOT_PAID = MEMBERSHIP_PAYMENT_REJECTED;
const MEMBERSHIP_PAYMENT_PAID = MEMBERSHIP_PAYMENT_APPROVED;

function membership_utc(?string $databaseDate): ?string
{
    if ($databaseDate === null || trim($databaseDate) === '') {
        return null;
    }

    return str_replace(' ', 'T', $databaseDate) . 'Z';
}

function membership_nullable_string(mixed $value): ?string
{
    if ($value === null) {
        return null;
    }

    $text = trim((string)$value);
    return $text === '' ? null : $text;
}

function membership_sync_expired(mysqli $db, ?int $userId = null): void
{
    $whereUserHist = $userId === null ? '' : ' AND h.id_usuario = ?';
    $toNotifyStmt = $db->prepare(
        "SELECT h.id_historial, h.id_usuario, h.tipo_asignacion, l.Correo, i.nya
         FROM historial_membresias h
         INNER JOIN logdata l ON l.ID = h.id_usuario
         LEFT JOIN infoper i ON i.ID_Foranea = l.ID
         WHERE h.estado = 'ACTIVA'
           AND h.fecha_fin <= UTC_TIMESTAMP(){$whereUserHist}"
    );
    if ($userId !== null) {
        $toNotifyStmt->bind_param('i', $userId);
    }
    $toNotifyStmt->execute();
    $toNotifyResult = $toNotifyStmt->get_result();
    $expiringUsers = [];
    while ($row = $toNotifyResult->fetch_assoc()) {
        $expiringUsers[] = $row;
    }
    $toNotifyStmt->close();

    $whereUser = $userId === null ? '' : ' AND id_usuario = ?';
    $stmt = $db->prepare(
        "UPDATE historial_membresias
         SET estado = 'VENCIDA', fecha_cambio_estado = UTC_TIMESTAMP()
         WHERE estado = 'ACTIVA'
           AND fecha_fin <= UTC_TIMESTAMP(){$whereUser}"
    );
    if ($userId !== null) {
        $stmt->bind_param('i', $userId);
    }
    $stmt->execute();
    $stmt->close();

    $whereLogdata = $userId === null ? '' : ' AND l.ID = ?';

    $promote = $db->prepare(
        "UPDATE logdata l
         SET l.num_rol_data = " . AUTH_ROLE_PREMIUM . "
         WHERE l.num_rol_data <> " . AUTH_ROLE_ADMIN . "
           AND EXISTS (
               SELECT 1
               FROM historial_membresias h
               WHERE h.id_usuario = l.ID
                 AND h.estado = 'ACTIVA'
                 AND h.fecha_inicio <= UTC_TIMESTAMP()
                 AND h.fecha_fin > UTC_TIMESTAMP()
           ){$whereLogdata}"
    );
    if ($userId !== null) {
        $promote->bind_param('i', $userId);
    }
    $promote->execute();
    $promote->close();

    $demote = $db->prepare(
        "UPDATE logdata l
         SET l.num_rol_data = " . AUTH_ROLE_NO_PREMIUM . "
         WHERE l.num_rol_data = " . AUTH_ROLE_PREMIUM . "
           AND NOT EXISTS (
               SELECT 1
               FROM historial_membresias h
               WHERE h.id_usuario = l.ID
                 AND h.estado = 'ACTIVA'
                 AND h.fecha_inicio <= UTC_TIMESTAMP()
                 AND h.fecha_fin > UTC_TIMESTAMP()
           ){$whereLogdata}"
    );
    if ($userId !== null) {
        $demote->bind_param('i', $userId);
    }
    $demote->execute();
    $demote->close();

    if (!empty($expiringUsers)) {
        if (!function_exists('sendVerificationEmail')) {
            require_once __DIR__ . '/mailer.php';
        }
        foreach ($expiringUsers as $exp) {
            $userEmail = (string)($exp['Correo'] ?? '');
            if ($userEmail !== '') {
                $userName = (string)($exp['nya'] ?? 'Usuario');
                $tipoAsig = (string)($exp['tipo_asignacion'] ?? '');
                $esPrueba = ($tipoAsig === 'PRUEBA_GRATIS');
                $tipoTexto = $esPrueba ? 'prueba gratuita de 3 meses' : 'membresía Premium';
                $subject = "Tu {$tipoTexto} en Clara ha finalizado";
                $body = "Hola {$userName},\n\n" .
                        "Te informamos que tu período de {$tipoTexto} en Clara ha culminado y tu cuenta ha pasado al plan gratuito.\n\n" .
                        "Para continuar gestionando clientes de manera ilimitada, consultar todos los terminales del calendario fiscal del SENIAT y recibir alertas de vencimiento, te invitamos a ingresar en la app a la sección 'Mi Membresía' para activar tu suscripción mensual.\n\n" .
                        "Atentamente,\nEl equipo de Clara";
                try {
                    sendVerificationEmail($userEmail, $subject, $body);
                } catch (Throwable $e) {
                    error_log('Error sending expiration email: ' . $e->getMessage());
                }
            }
        }
    }
}

function membership_config(mysqli $db): array
{
    $result = $db->query(
        'SELECT precio_usd, meses_vigencia, meses_prueba_gratis,
                limite_empresas_no_premium, dias_recordatorio_vencimiento,
                minutos_recordar_oferta,
                modo_pruebas,
                minutos_prueba_gratis_test,
                minutos_membresia_pagada_test
         FROM membresia_configuracion
         WHERE id_configuracion = 1 AND activo = 1
         LIMIT 1'
    );

    $row = $result->fetch_assoc();

    if (!$row) {
        api_throw(
            503,
            'MEMBERSHIP_NOT_CONFIGURED',
            'La membresía no está configurada.'
        );
    }

    return [
        'precioUsd' =>
            number_format(
                (float)$row['precio_usd'],
                2,
                '.',
                ''
            ),

        'mesesVigencia' =>
            (int)$row['meses_vigencia'],

        'mesesPrueba' =>
            (int)$row['meses_prueba_gratis'],

        'limiteEmpresasNoPremium' =>
            (int)$row['limite_empresas_no_premium'],

        'diasRecordatorioVencimiento' =>
            (int)$row['dias_recordatorio_vencimiento'],

        'minutosRecordarOferta' =>
            (int)$row['minutos_recordar_oferta'],

        /*
         * Estos tres campos son exclusivamente para pruebas.
         * Cuando modoPruebas = false se ignoran completamente.
         */
        'modoPruebas' =>
            (bool)$row['modo_pruebas'],

        'minutosPruebaGratisTest' =>
            max(
                1,
                (int)$row['minutos_prueba_gratis_test']
            ),

        'minutosMembresiaPagadaTest' =>
            max(
                1,
                (int)$row['minutos_membresia_pagada_test']
            ),
    ];
}

/**
 * Devuelve la fecha_fin UTC que debe guardarse en historial_membresias.
 *
 * En modo de pruebas:
 * - PRUEBA_GRATIS -> minutosPruebaGratisTest (3 min por defecto).
 * - PAGO_APROBADO -> minutosMembresiaPagadaTest (1 min por defecto).
 *
 * ASIGNACION_ADMIN mantiene su duración normal porque no forma parte
 * de la prueba solicitada.
 *
 * Con modoPruebas desactivado:
 * - PRUEBA_GRATIS -> mesesPrueba.
 * - PAGO_APROBADO / ASIGNACION_ADMIN -> mesesVigencia.
 */
function membership_calculate_end_at(
    array $config,
    string $assignmentType
): string {
    $type = strtoupper(trim($assignmentType));

    $now = new DateTimeImmutable(
        'now',
        new DateTimeZone('UTC')
    );

    if ((bool)($config['modoPruebas'] ?? false)) {
        if ($type === 'PRUEBA_GRATIS') {
            $minutes = max(
                1,
                (int)$config['minutosPruebaGratisTest']
            );

            return $now
                ->modify("+{$minutes} minutes")
                ->format('Y-m-d H:i:s');
        }

        if ($type === 'PAGO_APROBADO') {
            $minutes = max(
                1,
                (int)$config['minutosMembresiaPagadaTest']
            );

            return $now
                ->modify("+{$minutes} minutes")
                ->format('Y-m-d H:i:s');
        }
    }

    $months =
        $type === 'PRUEBA_GRATIS'
            ? max(1, (int)$config['mesesPrueba'])
            : max(1, (int)$config['mesesVigencia']);

    return $now
        ->modify("+{$months} months")
        ->format('Y-m-d H:i:s');
}

function membership_test_minutes_for_type(
    array $config,
    string $assignmentType
): ?int {
    if (!(bool)($config['modoPruebas'] ?? false)) {
        return null;
    }

    return match (strtoupper(trim($assignmentType))) {
        'PRUEBA_GRATIS' =>
            max(
                1,
                (int)$config['minutosPruebaGratisTest']
            ),

        'PAGO_APROBADO' =>
            max(
                1,
                (int)$config['minutosMembresiaPagadaTest']
            ),

        default => null,
    };
}

function membership_active(mysqli $db, int $userId): ?array
{
    $stmt = $db->prepare(
        "SELECT
            id_historial,
            id_pago,
            tipo_asignacion,
            estado,
            fecha_inicio,
            fecha_fin,
            id_admin,
            observacion
         FROM historial_membresias
         WHERE id_usuario = ?
           AND estado = 'ACTIVA'
           AND fecha_inicio <= UTC_TIMESTAMP()
           AND fecha_fin > UTC_TIMESTAMP()
         ORDER BY fecha_fin DESC, id_historial DESC
         LIMIT 1"
    );
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row) {
        return null;
    }

    return [
        'idHistorial' => (int)$row['id_historial'],
        'idPago' => $row['id_pago'] === null ? null : (int)$row['id_pago'],
        'tipoAsignacion' => (string)$row['tipo_asignacion'],
        'estado' => (string)$row['estado'],
        'fechaInicio' => membership_utc($row['fecha_inicio']),
        'fechaFin' => membership_utc($row['fecha_fin']),
        'idAdmin' => $row['id_admin'] === null ? null : (int)$row['id_admin'],
        'observacion' => $row['observacion'],
    ];
}

function membership_has_history(mysqli $db, int $userId): bool
{
    $stmt = $db->prepare('SELECT 1 FROM historial_membresias WHERE id_usuario = ? LIMIT 1');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $exists = (bool)$stmt->get_result()->fetch_row();
    $stmt->close();
    return $exists;
}

function membership_trial_used(mysqli $db, int $userId): bool
{
    $stmt = $db->prepare(
        "SELECT
            CASE
                WHEN EXISTS (
                    SELECT 1
                    FROM historial_membresias h
                    WHERE h.id_usuario = ?
                      AND h.tipo_asignacion = 'PRUEBA_GRATIS'
                )
                OR EXISTS (
                    SELECT 1
                    FROM membresia_prueba_datos d
                    WHERE d.id_usuario = ?
                )
                THEN 1 ELSE 0
            END AS trial_used"
    );
    $stmt->bind_param('ii', $userId, $userId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return isset($row['trial_used']) && (int)$row['trial_used'] === 1;
}

function membership_payment_method(mysqli $db, string $code, bool $activeOnly = true): array
{
    $code = strtoupper(trim($code));

    $sql = 'SELECT id_metodo_pago, codigo, nombre, tipo, moneda_principal,
                   requiere_comprobante, requiere_referencia, activo, orden
            FROM membresia_metodos_pago
            WHERE codigo = ?';
    if ($activeOnly) {
        $sql .= ' AND activo = 1';
    }
    $sql .= ' LIMIT 1';

    $stmt = $db->prepare($sql);
    $stmt->bind_param('s', $code);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row) {
        api_throw(422, 'INVALID_PAYMENT_METHOD', 'El método de pago seleccionado no está disponible.', [
            'field' => 'metodoPago',
        ]);
    }

    return [
        'idMetodoPago' => (int)$row['id_metodo_pago'],
        'codigo' => (string)$row['codigo'],
        'nombre' => (string)$row['nombre'],
        'tipo' => (string)$row['tipo'],
        'monedaPrincipal' => $row['moneda_principal'] === null ? null : (string)$row['moneda_principal'],
        'requiereComprobante' => (bool)$row['requiere_comprobante'],
        'requiereReferencia' => (bool)$row['requiere_referencia'],
        'activo' => (bool)$row['activo'],
        'orden' => (int)$row['orden'],
    ];
}

function membership_payment_methods(mysqli $db): array
{
    $result = $db->query(
        'SELECT id_metodo_pago, codigo, nombre, tipo, moneda_principal,
                requiere_comprobante, requiere_referencia, activo, orden
         FROM membresia_metodos_pago
         WHERE activo = 1
         ORDER BY orden ASC, id_metodo_pago ASC'
    );

    $methods = [];
    $indexById = [];
    while ($row = $result->fetch_assoc()) {
        $id = (int)$row['id_metodo_pago'];
        $indexById[$id] = count($methods);
        $methods[] = [
            'idMetodoPago' => $id,
            'codigo' => (string)$row['codigo'],
            'nombre' => (string)$row['nombre'],
            'tipo' => (string)$row['tipo'],
            'monedaPrincipal' => $row['moneda_principal'] === null ? null : (string)$row['moneda_principal'],
            'requiereComprobante' => (bool)$row['requiere_comprobante'],
            'requiereReferencia' => (bool)$row['requiere_referencia'],
            'orden' => (int)$row['orden'],
            'configuracionPublica' => [],
        ];
    }

    if ($methods === []) {
        return [];
    }

    $configResult = $db->query(
        'SELECT id_metodo_pago, clave, etiqueta, valor, orden
         FROM membresia_metodo_pago_configuracion
         WHERE activo = 1
         ORDER BY id_metodo_pago ASC, orden ASC, id_configuracion_pago ASC'
    );

    while ($row = $configResult->fetch_assoc()) {
        $methodId = (int)$row['id_metodo_pago'];
        if (!isset($indexById[$methodId])) {
            continue;
        }
        $methods[$indexById[$methodId]]['configuracionPublica'][] = [
            'clave' => (string)$row['clave'],
            'etiqueta' => (string)$row['etiqueta'],
            'valor' => (string)$row['valor'],
            'orden' => (int)$row['orden'],
        ];
    }

    return $methods;
}

function membership_current_exchange_rate(mysqli $db): array
{
    try {
        return exchange_rate_get_current_bcv($db);
    } catch (Throwable $e) {
        error_log(
            '[membership_current_exchange_rate] ' .
            get_class($e) .
            ': ' .
            $e->getMessage()
        );

        api_throw(
            503,
            'EXCHANGE_RATE_NOT_AVAILABLE',
            'No fue posible obtener la tasa oficial USD/VES vigente.'
        );
    }
}

function membership_payment_quote(mysqli $db, string $methodCode): array
{
    $method = membership_payment_method($db, $methodCode, true);
    $config = membership_config($db);
    $currency = strtoupper((string)($method['monedaPrincipal'] ?? ''));
    $priceUsd = (float)$config['precioUsd'];
    $exchangeRate = null;
    $requiredAmount = null;

    if ($currency === 'VES') {
        $exchangeRate = membership_current_exchange_rate($db);
        $requiredAmount = number_format(
            round($priceUsd * (float)$exchangeRate['tasa'], 2),
            2,
            '.',
            ''
        );
    } elseif (in_array($currency, ['USD', 'USDT'], true)) {
        $requiredAmount = number_format($priceUsd, 2, '.', '');
    } else {
        api_throw(503, 'PAYMENT_METHOD_CONFIGURATION_ERROR', 'El método de pago tiene una moneda no soportada.');
    }

    return [
        'metodo' => $method,
        'precioUsd' => $config['precioUsd'],
        'moneda' => $currency,
        'montoRequerido' => $requiredAmount,
        'tasaCambio' => $exchangeRate,
    ];
}

function membership_pending_payment(mysqli $db, int $userId): ?array
{
    $stmt = $db->prepare(
        'SELECT p.id_pago, p.fecha_pago, p.num_referencia, p.monto_pago,
                p.monto_requerido, p.moneda, p.id_metodo_pago,
                p.metodo_pago_snapshot, p.banco_emisor,
                p.numero_cuenta_emisora, p.identificador_emisor,
                p.nombre_titular, p.fecha_solicitud,
                m.nombre AS metodo_nombre
         FROM pagos_membresia p
         INNER JOIN membresia_metodos_pago m ON m.id_metodo_pago = p.id_metodo_pago
         WHERE p.id_usuario = ? AND p.id_membresia_status = ?
         ORDER BY p.fecha_solicitud DESC, p.id_pago DESC
         LIMIT 1'
    );
    $pending = MEMBERSHIP_PAYMENT_PENDING;
    $stmt->bind_param('ii', $userId, $pending);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row) {
        return null;
    }

    return [
        'idPago' => (int)$row['id_pago'],
        'fechaPago' => membership_utc($row['fecha_pago']),
        'numeroReferencia' => (string)$row['num_referencia'],
        'montoPago' => (string)$row['monto_pago'],
        'montoRequerido' => $row['monto_requerido'] === null ? null : (string)$row['monto_requerido'],
        'moneda' => (string)$row['moneda'],
        'metodoPago' => [
            'id' => (int)$row['id_metodo_pago'],
            'codigo' => (string)$row['metodo_pago_snapshot'],
            'nombre' => (string)$row['metodo_nombre'],
        ],
        'bancoEmisor' => membership_nullable_string($row['banco_emisor']),
        'numeroCuentaEmisora' => membership_nullable_string($row['numero_cuenta_emisora']),
        'identificadorEmisor' => membership_nullable_string($row['identificador_emisor']),
        'nombreTitular' => membership_nullable_string($row['nombre_titular']),
        'fechaSolicitud' => membership_utc($row['fecha_solicitud']),
    ];
}

function membership_latest_payment(mysqli $db, int $userId): ?array
{
    $stmt = $db->prepare(
        'SELECT p.id_pago, p.fecha_pago, p.num_referencia, p.monto_pago,
                p.monto_requerido, p.moneda, p.id_metodo_pago,
                p.metodo_pago_snapshot, p.banco_emisor,
                p.numero_cuenta_emisora, p.identificador_emisor,
                p.nombre_titular, p.fecha_solicitud,
                p.fecha_revision, p.observacion_admin,
                s.id_membresia_status, s.codigo_status, s.descripcion_status,
                m.nombre AS metodo_nombre
         FROM pagos_membresia p
         INNER JOIN pagos_membresia_status s
                 ON s.id_membresia_status = p.id_membresia_status
         INNER JOIN membresia_metodos_pago m
                 ON m.id_metodo_pago = p.id_metodo_pago
         WHERE p.id_usuario = ?
         ORDER BY p.fecha_solicitud DESC, p.id_pago DESC
         LIMIT 1'
    );
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row) {
        return null;
    }

    return [
        'idPago' => (int)$row['id_pago'],
        'fechaPago' => membership_utc($row['fecha_pago']),
        'numeroReferencia' => (string)$row['num_referencia'],
        'montoPago' => (string)$row['monto_pago'],
        'montoRequerido' => $row['monto_requerido'] === null ? null : (string)$row['monto_requerido'],
        'moneda' => (string)$row['moneda'],
        'metodoPago' => [
            'id' => (int)$row['id_metodo_pago'],
            'codigo' => (string)$row['metodo_pago_snapshot'],
            'nombre' => (string)$row['metodo_nombre'],
        ],
        'bancoEmisor' => membership_nullable_string($row['banco_emisor']),
        'numeroCuentaEmisora' => membership_nullable_string($row['numero_cuenta_emisora']),
        'identificadorEmisor' => membership_nullable_string($row['identificador_emisor']),
        'nombreTitular' => membership_nullable_string($row['nombre_titular']),
        'statusId' => (int)$row['id_membresia_status'],
        'statusCode' => (string)$row['codigo_status'],
        'statusDescription' => (string)$row['descripcion_status'],
        'fechaSolicitud' => membership_utc($row['fecha_solicitud']),
        'fechaRevision' => membership_utc($row['fecha_revision']),
        'observacionAdmin' => $row['observacion_admin'],
        'comprobanteEndpoint' => 'membership_payment_receipt.php?idPago=' . (int)$row['id_pago'],
    ];
}

function membership_user_role(mysqli $db, int $userId): int
{
    $stmt = $db->prepare('SELECT num_rol_data FROM logdata WHERE ID = ? LIMIT 1');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row) {
        api_throw(404, 'USER_NOT_FOUND', 'Usuario no encontrado.');
    }

    return (int)$row['num_rol_data'];
}

function membership_status(mysqli $db, int $userId): array
{
    membership_sync_expired($db, $userId);

    $role = membership_user_role($db, $userId);
    $config = membership_config($db);

    if ($role === AUTH_ROLE_ADMIN) {
        return [
            'state' => 'ADMIN',
            'isPremium' => false,
            'eligibleForFreeTrial' => false,
            'trialUsed' => false,
            'requiresPayment' => false,
            'canSubmitPayment' => false,
            'companyLimit' => null,
            'unlimitedCompanies' => true,
            'membership' => null,
            'pendingPayment' => null,
            'latestPayment' => null,
            'plan' => $config,
        ];
    }

    $active = membership_active($db, $userId);
    $trialUsed = membership_trial_used($db, $userId);
    $pendingPayment = membership_pending_payment($db, $userId);
    $latestPayment = membership_latest_payment($db, $userId);

    if ($active !== null) {
        $type = strtoupper((string)$active['tipoAsignacion']);
        if ($type === 'PRUEBA_GRATIS') {
            $state = 'TRIAL_ACTIVE';
        } elseif ($type === 'PAGO_APROBADO') {
            $state = 'PAID_ACTIVE';
        } else {
            $state = 'PREMIUM_ACTIVE';
        }
    } elseif ($pendingPayment !== null) {
        $state = 'PAYMENT_PENDING';
    } elseif (!$trialUsed) {
        $state = 'TRIAL_AVAILABLE';
    } else {
        $state = 'PAYMENT_REQUIRED';
    }

    $eligibleForFreeTrial = $active === null && $pendingPayment === null && !$trialUsed;
    $requiresPayment = $active === null && $pendingPayment === null && $trialUsed;
    $canSubmitPayment = $requiresPayment;
    $companyLimit = $active === null ? $config['limiteEmpresasNoPremium'] : null;

    $isExpired = $active === null && $trialUsed && $pendingPayment === null;
    $daysRemaining = null;
    $expiringSoon = false;
    if ($active !== null && !empty($active['fechaFin'])) {
        $endTs = strtotime((string)$active['fechaFin']);
        if ($endTs !== false) {
            $diffSeconds = $endTs - time();
            $daysRemaining = max(0, (int)ceil($diffSeconds / 86400));
            $expiringSoon = ($daysRemaining <= 5);
        }
    }

    $lastExpired = null;
    if ($isExpired) {
        $expStmt = $db->prepare("SELECT tipo_asignacion, fecha_fin FROM historial_membresias WHERE id_usuario = ? AND estado = 'VENCIDA' ORDER BY id_historial DESC LIMIT 1");
        $expStmt->bind_param('i', $userId);
        $expStmt->execute();
        $expRow = $expStmt->get_result()->fetch_assoc();
        $expStmt->close();
        if ($expRow) {
            $lastExpired = [
                'tipoAsignacion' => (string)$expRow['tipo_asignacion'],
                'fechaFin' => membership_utc($expRow['fecha_fin']),
            ];
        }
    }

    return [
        'state' => $state,
        'isPremium' => $active !== null,
        'eligibleForFreeTrial' => $eligibleForFreeTrial,
        'trialUsed' => $trialUsed,
        'requiresPayment' => $requiresPayment,
        'canSubmitPayment' => $canSubmitPayment,
        'companyLimit' => $companyLimit,
        'unlimitedCompanies' => $companyLimit === null,
        'membership' => $active,
        'pendingPayment' => $pendingPayment,
        'latestPayment' => $latestPayment,
        'plan' => $config,
        'isExpired' => $isExpired,
        'expiringSoon' => $expiringSoon,
        'daysRemaining' => $daysRemaining,
        'lastExpired' => $lastExpired,
    ];
}

function membership_company_limit(mysqli $db, int $userId): ?int
{
    $status = membership_status($db, $userId);
    return $status['companyLimit'] === null ? null : (int)$status['companyLimit'];
}

function membership_accessible_companies(mysqli $db, int $userId): array
{
    $limit = membership_company_limit($db, $userId);

    if ($limit === null) {
        $stmt = $db->prepare(
            'SELECT id_rif, RIF, tipo_de_cliente, nombre_empresa, created_at
             FROM rif
             WHERE ID_Foranea = ?
             ORDER BY id_rif ASC'
        );
        $stmt->bind_param('i', $userId);
    } else {
        $stmt = $db->prepare(
            'SELECT id_rif, RIF, tipo_de_cliente, nombre_empresa, created_at
             FROM rif
             WHERE ID_Foranea = ?
             ORDER BY id_rif ASC
             LIMIT ?'
        );
        $stmt->bind_param('ii', $userId, $limit);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $companies = [];
    while ($row = $result->fetch_assoc()) {
        $companies[] = [
            'idRif' => (int)$row['id_rif'],
            'rif' => (string)$row['RIF'],
            'tipo' => (int)$row['tipo_de_cliente'],
            'nombre' => (string)$row['nombre_empresa'],
            'createdAt' => membership_utc($row['created_at']),
        ];
    }
    $stmt->close();

    return $companies;
}

function membership_require_accessible_company(mysqli $db, int $userId, string $rif): array
{
    $rif = trim($rif);
    $companies = membership_accessible_companies($db, $userId);

    foreach ($companies as $company) {
        if (hash_equals((string)$company['rif'], $rif)) {
            return $company;
        }
    }

    $ownerStmt = $db->prepare('SELECT 1 FROM rif WHERE ID_Foranea = ? AND RIF = ? LIMIT 1');
    $ownerStmt->bind_param('is', $userId, $rif);
    $ownerStmt->execute();
    $belongsToUser = (bool)$ownerStmt->get_result()->fetch_row();
    $ownerStmt->close();

    if ($belongsToUser) {
        api_throw(403, 'COMPANY_NOT_AVAILABLE_ON_PLAN', 'Esta empresa no está disponible con el plan actual.');
    }

    api_throw(404, 'COMPANY_NOT_FOUND', 'La empresa no existe o no pertenece al usuario.');
}

function membership_assert_can_register_company(mysqli $db, int $userId): void
{
    $limit = membership_company_limit($db, $userId);
    if ($limit === null) {
        return;
    }

    $stmt = $db->prepare('SELECT COUNT(*) AS total FROM rif WHERE ID_Foranea = ?');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $total = (int)($row['total'] ?? 0);
    if ($total >= $limit) {
        api_throw(409, 'COMPANY_LIMIT_REACHED', 'El plan actual permite registrar un máximo de ' . $limit . ' empresa(s).', [
            'companyLimit' => $limit,
            'currentCompanies' => $total,
        ]);
    }
}

function membership_activate_free_trial(mysqli $db, int $userId, array $data): array
{
    $document = preg_replace('/\s+/', ' ', (string)api_string($data, 'documento', 30));
    $fullName = preg_replace('/\s+/', ' ', (string)api_string($data, 'nombreCompleto', 180));
    $address = preg_replace('/\s+/', ' ', (string)api_string($data, 'direccion', 500));
    $phone = preg_replace('/\s+/', ' ', (string)api_string($data, 'telefono', 30));

    if (!preg_match('/^[\p{L}0-9.\- ]{5,30}$/u', $document)) {
        api_throw(422, 'VALIDATION_ERROR', 'La cédula o RIF tiene un formato inválido.', ['field' => 'documento']);
    }
    if (mb_strlen($fullName, 'UTF-8') < 3) {
        api_throw(422, 'VALIDATION_ERROR', 'El nombre completo es demasiado corto.', ['field' => 'nombreCompleto']);
    }
    if (mb_strlen($address, 'UTF-8') < 8) {
        api_throw(422, 'VALIDATION_ERROR', 'La dirección completa es demasiado corta.', ['field' => 'direccion']);
    }
    if (!preg_match('/^[0-9+().\- ]{7,30}$/', $phone)) {
        api_throw(422, 'VALIDATION_ERROR', 'El número telefónico tiene un formato inválido.', ['field' => 'telefono']);
    }

    mysqli_begin_transaction($db);
    try {
        $lock = $db->prepare(
            'SELECT l.ID, l.Correo, l.num_rol_data, i.nya
             FROM logdata l
             LEFT JOIN infoper i ON i.ID_Foranea = l.ID
             WHERE l.ID = ?
             FOR UPDATE'
        );
        $lock->bind_param('i', $userId);
        $lock->execute();
        $user = $lock->get_result()->fetch_assoc();
        $lock->close();

        if (!$user) {
            api_throw(404, 'USER_NOT_FOUND', 'Usuario no encontrado.');
        }
        if ((int)$user['num_rol_data'] === AUTH_ROLE_ADMIN) {
            api_throw(409, 'ADMIN_ROLE_PROTECTED', 'La cuenta administradora no utiliza la prueba gratuita.');
        }

        membership_sync_expired($db, $userId);

        if (membership_active($db, $userId) !== null) {
            api_throw(409, 'ALREADY_PREMIUM', 'La membresía Premium ya está activa.');
        }
        if (membership_trial_used($db, $userId)) {
            api_throw(409, 'FREE_TRIAL_ALREADY_USED', 'La prueba gratuita ya fue utilizada.');
        }
        if (membership_pending_payment($db, $userId) !== null) {
            api_throw(409, 'PAYMENT_ALREADY_PENDING', 'Existe un pago pendiente de revisión.');
        }

        $config = membership_config($db);

        $trialEndsAt =
            membership_calculate_end_at(
                $config,
                'PRUEBA_GRATIS'
            );

        $emailSnapshot = (string)$user['Correo'];
        $nameSnapshot =
            $user['nya'] === null
                ? null
                : (string)$user['nya'];

        $history = $db->prepare(
            "INSERT INTO historial_membresias
                (id_usuario, correo_usuario_snapshot, nombre_usuario_snapshot,
                 tipo_asignacion, estado, fecha_inicio, fecha_fin, observacion)
             VALUES (?, ?, ?, 'PRUEBA_GRATIS', 'ACTIVA', UTC_TIMESTAMP(), ?,
                     'Prueba gratuita activada por el usuario.')"
        );

        $history->bind_param(
            'isss',
            $userId,
            $emailSnapshot,
            $nameSnapshot,
            $trialEndsAt
        );
        $history->execute();
        $historyId = (int)$db->insert_id;
        $history->close();

        $trial = $db->prepare(
            'INSERT INTO membresia_prueba_datos
                (id_usuario, id_historial, documento, nombre_completo, direccion, telefono)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $trial->bind_param('iissss', $userId, $historyId, $document, $fullName, $address, $phone);
        $trial->execute();
        $trial->close();

        $role = $db->prepare('UPDATE logdata SET num_rol_data = ? WHERE ID = ?');
        $premiumRole = AUTH_ROLE_PREMIUM;
        $role->bind_param('ii', $premiumRole, $userId);
        $role->execute();
        $role->close();

        mysqli_commit($db);
    } catch (Throwable $e) {
        mysqli_rollback($db);
        throw $e;
    }

    return membership_status($db, $userId);
}

function membership_parse_payment_date(
    string $value,
    string $timezoneName,
    string $field = 'fechaPago'
): string {
    if (!preg_match(
        '/^\d{4}-\d{2}-\d{2}(?:[T ]\d{2}:\d{2}(?::\d{2})?(?:Z|[+-]\d{2}:\d{2})?)?$/',
        trim($value)
    )) {
        api_throw(422, 'VALIDATION_ERROR', 'La fecha debe usar formato ISO 8601.', [
            'field' => $field,
        ]);
    }

    try {
        $timezone = new DateTimeZone($timezoneName);
        $date = new DateTimeImmutable($value, $timezone);
        $dateErrors = DateTimeImmutable::getLastErrors();
        if (is_array($dateErrors)
            && ((int)$dateErrors['warning_count'] > 0 || (int)$dateErrors['error_count'] > 0)) {
            throw new InvalidArgumentException('Invalid date.');
        }
    } catch (Throwable $e) {
        api_throw(422, 'VALIDATION_ERROR', 'La fecha o la zona horaria no es válida.', [
            'field' => $field,
        ]);
    }

    $utc = $date->setTimezone(new DateTimeZone('UTC'));
    $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));

    if ($utc > $now->modify('+1 day') || $utc < $now->modify('-180 days')) {
        api_throw(422, 'VALIDATION_ERROR', 'La fecha está fuera del rango permitido.', [
            'field' => $field,
        ]);
    }

    return $utc->format('Y-m-d H:i:s');
}

function membership_decimal(mixed $value, string $field, bool $required = true): ?string
{
    if ($value !== null && !is_string($value) && !is_int($value) && !is_float($value)) {
        api_throw(422, 'VALIDATION_ERROR', "El campo {$field} tiene un formato inválido.", [
            'field' => $field,
        ]);
    }

    $normalized = str_replace(',', '.', trim((string)$value));
    if ($normalized === '') {
        if ($required) {
            api_throw(422, 'VALIDATION_ERROR', "El campo {$field} es obligatorio.", ['field' => $field]);
        }
        return null;
    }

    if (!preg_match('/^(?:0|[1-9][0-9]{0,9})(?:\.[0-9]{1,8})?$/', $normalized)
        || (float)$normalized <= 0) {
        api_throw(422, 'VALIDATION_ERROR', "El campo {$field} debe ser un monto positivo válido.", [
            'field' => $field,
        ]);
    }

    return $normalized;
}

function membership_receipt_directory(): string
{
    $config = clara_private_config();

    $configured = trim(
        (string)(
            getenv('MEMBERSHIP_RECEIPT_DIR')
            ?: ($config['MEMBERSHIP_RECEIPT_DIR'] ?? '')
        )
    );

    $isAbsolute = str_starts_with($configured, DIRECTORY_SEPARATOR)
        || preg_match('~^[A-Za-z]:[\\\\/]~', $configured) === 1;
    if (!$isAbsolute) {
        api_throw(503, 'INVALID_RECEIPT_CONFIGURATION', 'La ruta de comprobantes debe ser absoluta.');
    }

    return rtrim($configured, "\\/");
}

function membership_validate_receipt(array $file): array
{
    $error = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($error !== UPLOAD_ERR_OK) {
        api_throw(422, 'INVALID_RECEIPT', 'Debe adjuntar un comprobante de pago válido.', [
            'field' => 'comprobante',
            'uploadError' => $error,
        ]);
    }

    $tmpName = (string)($file['tmp_name'] ?? '');
    $size = (int)($file['size'] ?? 0);
    if ($tmpName === '' || !is_uploaded_file($tmpName) || $size < 1 || $size > 5 * 1024 * 1024) {
        api_throw(422, 'INVALID_RECEIPT', 'El comprobante debe ser una imagen de hasta 5 MB.', [
            'field' => 'comprobante',
        ]);
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = (string)$finfo->file($tmpName);
    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    if (!isset($allowed[$mime])) {
        api_throw(422, 'INVALID_RECEIPT', 'El comprobante debe ser JPG, PNG o WEBP.', [
            'field' => 'comprobante',
        ]);
    }

    return [
        'tmpName' => $tmpName,
        'size' => $size,
        'mime' => $mime,
        'extension' => $allowed[$mime],
        'originalName' => mb_substr(basename((string)($file['name'] ?? 'comprobante')), 0, 255, 'UTF-8'),
    ];
}

function membership_validate_payment_fields(array $data, array $method): array
{
    $code = (string)$method['codigo'];

    $bank = api_string($data, 'bancoEmisor', 120, false);
    $account = api_string($data, 'numeroCuentaEmisora', 180, false);
    $identifier = api_string($data, 'identificadorEmisor', 180, false);
    $holder = api_string($data, 'nombreTitular', 180, false);

    $bank = $bank === null ? null : preg_replace('/\s+/', ' ', $bank);
    $account = $account === null ? null : preg_replace('/\s+/', ' ', $account);
    $identifier = $identifier === null ? null : preg_replace('/\s+/', ' ', $identifier);
    $holder = $holder === null ? null : preg_replace('/\s+/', ' ', $holder);

    if ($code === 'PAGO_MOVIL') {
        if ($bank === null) {
            api_throw(422, 'VALIDATION_ERROR', 'Debe indicar el banco emisor.', ['field' => 'bancoEmisor']);
        }
        $identifier ??= $account;
        if ($identifier === null || !preg_match('/^[0-9+().\- ]{7,30}$/', $identifier)) {
            api_throw(422, 'VALIDATION_ERROR', 'Debe indicar un teléfono emisor válido.', ['field' => 'identificadorEmisor']);
        }
        if ($holder === null) {
            api_throw(422, 'VALIDATION_ERROR', 'Debe indicar el nombre del titular.', ['field' => 'nombreTitular']);
        }
    } elseif ($code === 'TRANSFERENCIA') {
        if ($bank === null) {
            api_throw(422, 'VALIDATION_ERROR', 'Debe indicar el banco emisor.', ['field' => 'bancoEmisor']);
        }
        if ($account === null || mb_strlen($account, 'UTF-8') < 4) {
            api_throw(422, 'VALIDATION_ERROR', 'Debe indicar la cuenta emisora.', ['field' => 'numeroCuentaEmisora']);
        }
        $identifier ??= $account;
        if ($holder === null) {
            api_throw(422, 'VALIDATION_ERROR', 'Debe indicar el nombre del titular.', ['field' => 'nombreTitular']);
        }
    } elseif ($code === 'PAYPAL') {
        if ($identifier === null) {
            api_throw(422, 'VALIDATION_ERROR', 'Debe indicar el correo o identificador de la cuenta PayPal.', [
                'field' => 'identificadorEmisor',
            ]);
        }
    } elseif ($code === 'BINANCE') {
        if ($identifier === null) {
            api_throw(422, 'VALIDATION_ERROR', 'Debe indicar el Pay ID, UID o identificador de Binance.', [
                'field' => 'identificadorEmisor',
            ]);
        }
    } else {
        api_throw(422, 'INVALID_PAYMENT_METHOD', 'El método de pago seleccionado no está soportado.');
    }

    return [
        'bancoEmisor' => $bank,
        'numeroCuentaEmisora' => $account,
        'identificadorEmisor' => $identifier,
        'nombreTitular' => $holder,
    ];
}

function membership_amount_matches(string $provided, string $required, string $currency): bool
{
    $providedFloat = round((float)$provided, 2);
    $requiredFloat = round((float)$required, 2);
    $tolerance = strtoupper($currency) === 'VES' ? 0.01 : 0.001;
    return abs($providedFloat - $requiredFloat) <= $tolerance;
}

function membership_submit_payment(mysqli $db, int $userId, array $data, array $file): array
{
    $idempotencyKey = api_string($data, 'idempotencyKey', 36, false);
    if ($idempotencyKey !== null
        && !preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $idempotencyKey)) {
        api_throw(422, 'VALIDATION_ERROR', 'idempotencyKey debe ser un UUID válido.', ['field' => 'idempotencyKey']);
    }

    if ($idempotencyKey !== null) {
        $existing = $db->prepare(
            'SELECT p.id_pago, s.codigo_status
             FROM pagos_membresia p
             INNER JOIN pagos_membresia_status s
                     ON s.id_membresia_status = p.id_membresia_status
             WHERE p.idempotency_key = ? AND p.id_usuario = ?
             LIMIT 1'
        );
        $existing->bind_param('si', $idempotencyKey, $userId);
        $existing->execute();
        $row = $existing->get_result()->fetch_assoc();
        $existing->close();
        if ($row) {
            return [
                'idPago' => (int)$row['id_pago'],
                'status' => (string)$row['codigo_status'],
                'reused' => true,
            ];
        }
    }

    $timezoneName = api_string($data, 'zonaHorariaPago', 64, false) ?? 'America/Caracas';
    $paymentDate = membership_parse_payment_date(
        (string)api_string($data, 'fechaPago', 50),
        $timezoneName
    );

    $methodCode = strtoupper((string)api_string($data, 'metodoPago', 30));
    $method = membership_payment_method($db, $methodCode, true);

    $reference = (string)api_string($data, 'numeroReferencia', 50);
    if (!preg_match('/^[A-Za-z0-9._\-]{4,50}$/', $reference)) {
        api_throw(422, 'VALIDATION_ERROR', 'El número de referencia tiene un formato inválido.', [
            'field' => 'numeroReferencia',
        ]);
    }

    $amount = (string)membership_decimal($data['montoPago'] ?? null, 'montoPago');
    $requestedCurrency = api_string($data, 'moneda', 10, false);
    $currency = strtoupper((string)$method['monedaPrincipal']);
    if ($requestedCurrency !== null && strtoupper($requestedCurrency) !== $currency) {
        api_throw(422, 'VALIDATION_ERROR', 'La moneda no corresponde con el método de pago seleccionado.', [
            'field' => 'moneda',
            'expected' => $currency,
        ]);
    }

    $payer = membership_validate_payment_fields($data, $method);
    $userObservation = api_string($data, 'observacion', 500, false);
    $receipt = membership_validate_receipt($file);

    $storedFilename = bin2hex(random_bytes(24)) . '.' . $receipt['extension'];
    $storedPath = membership_receipt_directory() . DIRECTORY_SEPARATOR . $storedFilename;
    $moved = false;

    mysqli_begin_transaction($db);
    try {
        $lock = $db->prepare(
            'SELECT l.ID, l.Correo, l.num_rol_data, i.nya
             FROM logdata l
             LEFT JOIN infoper i ON i.ID_Foranea = l.ID
             WHERE l.ID = ?
             FOR UPDATE'
        );
        $lock->bind_param('i', $userId);
        $lock->execute();
        $userRow = $lock->get_result()->fetch_assoc();
        $lock->close();

        if (!$userRow) {
            api_throw(404, 'USER_NOT_FOUND', 'Usuario no encontrado.');
        }
        if ((int)$userRow['num_rol_data'] === AUTH_ROLE_ADMIN) {
            api_throw(409, 'ADMIN_ROLE_PROTECTED', 'La cuenta administradora no utiliza pagos de membresía.');
        }

        if ($idempotencyKey !== null) {
            $retry = $db->prepare(
                'SELECT p.id_pago, s.codigo_status
                 FROM pagos_membresia p
                 INNER JOIN pagos_membresia_status s
                         ON s.id_membresia_status = p.id_membresia_status
                 WHERE p.idempotency_key = ? AND p.id_usuario = ?
                 LIMIT 1'
            );
            $retry->bind_param('si', $idempotencyKey, $userId);
            $retry->execute();
            $retryRow = $retry->get_result()->fetch_assoc();
            $retry->close();
            if ($retryRow) {
                mysqli_commit($db);
                return [
                    'idPago' => (int)$retryRow['id_pago'],
                    'status' => (string)$retryRow['codigo_status'],
                    'reused' => true,
                ];
            }
        }

        membership_sync_expired($db, $userId);

        if (membership_active($db, $userId) !== null) {
            api_throw(409, 'ALREADY_PREMIUM', 'La membresía Premium todavía se encuentra activa.');
        }
        if (!membership_trial_used($db, $userId)) {
            api_throw(409, 'FREE_TRIAL_AVAILABLE', 'El usuario todavía puede activar sus tres meses de prueba gratuita.');
        }
        if (membership_pending_payment($db, $userId) !== null) {
            api_throw(409, 'PAYMENT_ALREADY_PENDING', 'Ya existe un pago pendiente por revisión.');
        }

        $quote = membership_payment_quote($db, $methodCode);
        $requiredAmount = (string)$quote['montoRequerido'];
        if (!membership_amount_matches($amount, $requiredAmount, $currency)) {
            api_throw(422, 'PAYMENT_AMOUNT_MISMATCH', 'El monto enviado no coincide con el monto requerido.', [
                'field' => 'montoPago',
                'moneda' => $currency,
                'montoRequerido' => $requiredAmount,
            ]);
        }

        $basePrice = (string)$quote['precioUsd'];
        $exchangeRate = $quote['tasaCambio'];
        $exchangeRateValue = $exchangeRate === null ? null : (string)$exchangeRate['tasa'];
        $rateSource = $exchangeRate === null ? null : (string)$exchangeRate['fuente'];
        $rateDate = $exchangeRate === null ? null : ((string)$exchangeRate['fechaTasa'] . ' 00:00:00');

        $directory = membership_receipt_directory();
        if (!is_dir($directory) && !mkdir($directory, 0750, true) && !is_dir($directory)) {
            api_throw(500, 'RECEIPT_STORAGE_ERROR', 'No fue posible guardar el comprobante.');
        }
        if (!move_uploaded_file($receipt['tmpName'], $storedPath)) {
            api_throw(500, 'RECEIPT_STORAGE_ERROR', 'No fue posible guardar el comprobante.');
        }
        $moved = true;
        @chmod($storedPath, 0640);
        $sha256 = hash_file('sha256', $storedPath);
        if ($sha256 === false) {
            api_throw(500, 'RECEIPT_STORAGE_ERROR', 'No fue posible verificar el comprobante.');
        }

        $emailSnapshot = (string)$userRow['Correo'];
        $nameSnapshot = $userRow['nya'] === null ? null : (string)$userRow['nya'];
        $methodId = (int)$method['idMetodoPago'];
        $methodSnapshot = (string)$method['codigo'];
        $bank = $payer['bancoEmisor'];
        $account = $payer['numeroCuentaEmisora'];
        $identifier = $payer['identificadorEmisor'];
        $holder = $payer['nombreTitular'];
        $pendingStatus = MEMBERSHIP_PAYMENT_PENDING;
        $receiptSize = (int)$receipt['size'];
        $receiptOriginalName = (string)$receipt['originalName'];
        $receiptMime = (string)$receipt['mime'];

        $stmt = $db->prepare(
            'INSERT INTO pagos_membresia (
                id_usuario, correo_usuario_snapshot, nombre_usuario_snapshot,
                fecha_pago, zona_horaria_pago, num_referencia,
                monto_pago, monto_requerido, moneda, precio_usd_base,
                tasa_cambio_aplicada, fuente_tasa, fecha_tasa,
                id_metodo_pago, metodo_pago_snapshot,
                banco_emisor, numero_cuenta_emisora, identificador_emisor,
                nombre_titular, id_membresia_status,
                comprobante_archivo, comprobante_nombre_original, comprobante_mime,
                comprobante_tamano, comprobante_sha256, idempotency_key,
                observacion_usuario
             ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        $types = 'i'
            . str_repeat('s', 12)
            . 'i'
            . str_repeat('s', 5)
            . 'i'
            . str_repeat('s', 3)
            . 'i'
            . str_repeat('s', 3);

        $stmt->bind_param(
            $types,
            $userId,
            $emailSnapshot,
            $nameSnapshot,
            $paymentDate,
            $timezoneName,
            $reference,
            $amount,
            $requiredAmount,
            $currency,
            $basePrice,
            $exchangeRateValue,
            $rateSource,
            $rateDate,
            $methodId,
            $methodSnapshot,
            $bank,
            $account,
            $identifier,
            $holder,
            $pendingStatus,
            $storedFilename,
            $receiptOriginalName,
            $receiptMime,
            $receiptSize,
            $sha256,
            $idempotencyKey,
            $userObservation
        );
        $stmt->execute();
        $paymentId = (int)$db->insert_id;
        $stmt->close();

        mysqli_commit($db);
    } catch (mysqli_sql_exception $e) {
        mysqli_rollback($db);
        if ($moved && is_file($storedPath)) {
            @unlink($storedPath);
        }
        if ((int)$e->getCode() === 1062) {
            api_throw(409, 'DUPLICATE_PAYMENT_REFERENCE', 'Ese número de referencia o solicitud ya fue registrado.');
        }
        throw $e;
    } catch (Throwable $e) {
        mysqli_rollback($db);
        if ($moved && is_file($storedPath)) {
            @unlink($storedPath);
        }
        throw $e;
    }

    return [
        'idPago' => $paymentId,
        'status' => 'PENDIENTE_APROBACION',
        'reused' => false,
        'moneda' => $currency,
        'montoRequerido' => $requiredAmount,
    ];
}

function membership_whatsapp_payload(mysqli $db, int $userId, int $paymentId): array
{
    $stmt = $db->prepare(
        'SELECT p.correo_usuario_snapshot, p.nombre_usuario_snapshot,
                p.fecha_pago, p.num_referencia, p.monto_pago,
                p.monto_requerido, p.moneda, p.metodo_pago_snapshot,
                p.banco_emisor, p.numero_cuenta_emisora,
                p.identificador_emisor, p.nombre_titular,
                m.nombre AS metodo_nombre
         FROM pagos_membresia p
         INNER JOIN membresia_metodos_pago m ON m.id_metodo_pago = p.id_metodo_pago
         WHERE p.id_pago = ? AND p.id_usuario = ?
         LIMIT 1'
    );
    $stmt->bind_param('ii', $paymentId, $userId);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row) {
        return ['message' => null, 'url' => null];
    }

    $lines = [
        'SOLICITUD DE MEMBRESÍA PREMIUM',
        'Solicitud: #' . $paymentId,
        'Usuario: ' . (string)($row['nombre_usuario_snapshot'] ?? ''),
        'Correo: ' . (string)$row['correo_usuario_snapshot'],
        'Fecha del pago: ' . (string)$row['fecha_pago'] . ' UTC',
        'Referencia: ' . (string)$row['num_referencia'],
        'Monto reportado: ' . (string)$row['monto_pago'] . ' ' . (string)$row['moneda'],
        'Monto requerido: ' . (string)$row['monto_requerido'] . ' ' . (string)$row['moneda'],
        'Método: ' . (string)$row['metodo_nombre'] . ' (' . (string)$row['metodo_pago_snapshot'] . ')',
    ];

    $optional = [
        'Banco emisor' => membership_nullable_string($row['banco_emisor']),
        'Cuenta emisora' => membership_nullable_string($row['numero_cuenta_emisora']),
        'Identificador emisor' => membership_nullable_string($row['identificador_emisor']),
        'Titular' => membership_nullable_string($row['nombre_titular']),
    ];

    foreach ($optional as $label => $value) {
        if ($value !== null) {
            $lines[] = $label . ': ' . $value;
        }
    }

    $lines[] = 'El comprobante quedó registrado para revisión administrativa.';
    $message = implode("\n", $lines);

    $config = clara_private_config();

    $phone = preg_replace(
        '/\D+/',
        '',
        (string)(
            getenv('MEMBERSHIP_WHATSAPP_NUMBER')
            ?: ($config['MEMBERSHIP_WHATSAPP_NUMBER'] ?? '')
        )
    );
    $url = $phone === '' ? null : 'https://wa.me/' . $phone . '?text=' . rawurlencode($message);

    return ['message' => $message, 'url' => $url];
}

function membership_admin_set_premium(
    mysqli $db,
    int $adminId,
    int $targetUserId,
    bool $premium,
    ?string $observation
): array {
    mysqli_begin_transaction($db);
    try {
        $stmt = $db->prepare(
            'SELECT l.ID, l.num_rol_data, l.Correo, i.nya
             FROM logdata l
             LEFT JOIN infoper i ON i.ID_Foranea = l.ID
             WHERE l.ID = ?
             FOR UPDATE'
        );
        $stmt->bind_param('i', $targetUserId);
        $stmt->execute();
        $target = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$target) {
            api_throw(404, 'USER_NOT_FOUND', 'Usuario no encontrado.');
        }
        if ((int)$target['num_rol_data'] === AUTH_ROLE_ADMIN) {
            api_throw(409, 'ADMIN_ROLE_PROTECTED', 'No se puede modificar la membresía de otro administrador.');
        }

        membership_sync_expired($db, $targetUserId);
        $active = membership_active($db, $targetUserId);

        if ($premium) {
            if ($active !== null) {
                mysqli_commit($db);
                return [
                    'changed' => false,
                    'membership' => $active,
                    'monthsGranted' => 0,
                ];
            }

            if (membership_pending_payment($db, $targetUserId) !== null) {
                api_throw(409, 'PAYMENT_ALREADY_PENDING', 'El usuario tiene un pago pendiente por revisión.');
            }

            $months = (int)membership_config($db)['mesesVigencia'];
            $emailSnapshot = (string)$target['Correo'];
            $nameSnapshot = $target['nya'] === null ? null : (string)$target['nya'];

            $insert = $db->prepare(
                "INSERT INTO historial_membresias
                    (id_usuario, correo_usuario_snapshot, nombre_usuario_snapshot,
                     tipo_asignacion, estado, fecha_inicio, fecha_fin, id_admin, observacion)
                 VALUES (?, ?, ?, 'ASIGNACION_ADMIN', 'ACTIVA', UTC_TIMESTAMP(),
                         DATE_ADD(UTC_TIMESTAMP(), INTERVAL {$months} MONTH), ?, ?)"
            );
            $insert->bind_param(
                'issis',
                $targetUserId,
                $emailSnapshot,
                $nameSnapshot,
                $adminId,
                $observation
            );
            $insert->execute();
            $insert->close();

            $premiumRole = AUTH_ROLE_PREMIUM;
            $update = $db->prepare('UPDATE logdata SET num_rol_data = ? WHERE ID = ?');
            $update->bind_param('ii', $premiumRole, $targetUserId);
            $update->execute();
            $update->close();

            mysqli_commit($db);

            return [
                'changed' => true,
                'membership' => membership_active($db, $targetUserId),
                'monthsGranted' => $months,
            ];
        }

        $revoke = $db->prepare(
            "UPDATE historial_membresias
             SET estado = 'REVOCADA', fecha_cambio_estado = UTC_TIMESTAMP(),
                 id_admin_revocacion = ?, fecha_revocacion = UTC_TIMESTAMP(),
                 motivo_revocacion = ?
             WHERE id_usuario = ? AND estado = 'ACTIVA'"
        );
        $revoke->bind_param('isi', $adminId, $observation, $targetUserId);
        $revoke->execute();
        $revokedRows = $revoke->affected_rows;
        $revoke->close();

        $freeRole = AUTH_ROLE_NO_PREMIUM;
        $update = $db->prepare('UPDATE logdata SET num_rol_data = ? WHERE ID = ?');
        $update->bind_param('ii', $freeRole, $targetUserId);
        $update->execute();
        $roleChanged = $update->affected_rows > 0;
        $update->close();

        mysqli_commit($db);

        return [
            'changed' => $revokedRows > 0 || $roleChanged || $active !== null,
            'membership' => null,
            'monthsGranted' => 0,
        ];
    } catch (Throwable $e) {
        mysqli_rollback($db);
        throw $e;
    }
}

function membership_admin_review_payment(
    mysqli $db,
    int $adminId,
    int $paymentId,
    string $decision,
    ?string $observation
): array {
    $decision = strtoupper(trim($decision));
    if (!in_array($decision, ['APROBAR', 'RECHAZAR'], true)) {
        api_throw(422, 'VALIDATION_ERROR', 'La decisión debe ser APROBAR o RECHAZAR.', ['field' => 'decision']);
    }
    if ($decision === 'RECHAZAR' && ($observation === null || trim($observation) === '')) {
        api_throw(422, 'VALIDATION_ERROR', 'Debe indicar el motivo del rechazo.', ['field' => 'observacion']);
    }

    mysqli_begin_transaction($db);
    try {
        $stmt = $db->prepare(
            'SELECT id_pago, id_usuario, id_membresia_status,
                    correo_usuario_snapshot, nombre_usuario_snapshot
             FROM pagos_membresia
             WHERE id_pago = ?
             FOR UPDATE'
        );
        $stmt->bind_param('i', $paymentId);
        $stmt->execute();
        $payment = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$payment) {
            api_throw(404, 'PAYMENT_NOT_FOUND', 'Solicitud de pago no encontrada.');
        }
        if ((int)$payment['id_membresia_status'] !== MEMBERSHIP_PAYMENT_PENDING) {
            api_throw(409, 'PAYMENT_ALREADY_REVIEWED', 'La solicitud ya fue revisada.');
        }

        $targetUserId = (int)$payment['id_usuario'];
        $userLock = $db->prepare('SELECT ID, num_rol_data FROM logdata WHERE ID = ? FOR UPDATE');
        $userLock->bind_param('i', $targetUserId);
        $userLock->execute();
        $target = $userLock->get_result()->fetch_assoc();
        $userLock->close();

        if (!$target) {
            api_throw(404, 'USER_NOT_FOUND', 'Usuario no encontrado.');
        }
        if ((int)$target['num_rol_data'] === AUTH_ROLE_ADMIN) {
            api_throw(409, 'ADMIN_ROLE_PROTECTED', 'No se puede asignar Premium a otro administrador.');
        }

        $monthsGranted = 0;
        $testMinutesGranted = null;

        if ($decision === 'APROBAR') {
            membership_sync_expired($db, $targetUserId);
            if (membership_active($db, $targetUserId) !== null) {
                api_throw(409, 'ALREADY_PREMIUM', 'El usuario ya tiene una membresía Premium activa.');
            }

            $config = membership_config($db);

            $monthsGranted =
                (int)$config['mesesVigencia'];

            $testMinutesGranted =
                membership_test_minutes_for_type(
                    $config,
                    'PAGO_APROBADO'
                );

            $paidEndsAt =
                membership_calculate_end_at(
                    $config,
                    'PAGO_APROBADO'
                );

            $emailSnapshot = (string)$payment['correo_usuario_snapshot'];
            $nameSnapshot = $payment['nombre_usuario_snapshot'] === null
                ? null
                : (string)$payment['nombre_usuario_snapshot'];

            $approvedStatus = MEMBERSHIP_PAYMENT_APPROVED;
            $updatePayment = $db->prepare(
                'UPDATE pagos_membresia
                 SET id_membresia_status = ?,
                     id_admin_revision = ?, observacion_admin = ?, fecha_revision = UTC_TIMESTAMP()
                 WHERE id_pago = ?'
            );
            $updatePayment->bind_param('iisi', $approvedStatus, $adminId, $observation, $paymentId);
            $updatePayment->execute();
            $updatePayment->close();

            $history = $db->prepare(
                "INSERT INTO historial_membresias
                    (id_usuario, correo_usuario_snapshot, nombre_usuario_snapshot,
                     id_pago, tipo_asignacion, estado, fecha_inicio, fecha_fin,
                     id_admin, observacion)
                 VALUES (?, ?, ?, ?, 'PAGO_APROBADO', 'ACTIVA', UTC_TIMESTAMP(),
                         ?, ?, ?)"
            );

            $history->bind_param(
                'issisis',
                $targetUserId,
                $emailSnapshot,
                $nameSnapshot,
                $paymentId,
                $paidEndsAt,
                $adminId,
                $observation
            );
            $history->execute();
            $history->close();

            $premiumRole = AUTH_ROLE_PREMIUM;
            $role = $db->prepare('UPDATE logdata SET num_rol_data = ? WHERE ID = ?');
            $role->bind_param('ii', $premiumRole, $targetUserId);
            $role->execute();
            $role->close();
        } else {
            $rejectedStatus = MEMBERSHIP_PAYMENT_REJECTED;
            $updatePayment = $db->prepare(
                'UPDATE pagos_membresia
                 SET id_membresia_status = ?,
                     id_admin_revision = ?, observacion_admin = ?, fecha_revision = UTC_TIMESTAMP()
                 WHERE id_pago = ?'
            );
            $updatePayment->bind_param('iisi', $rejectedStatus, $adminId, $observation, $paymentId);
            $updatePayment->execute();
            $updatePayment->close();
        }

        mysqli_commit($db);
    } catch (Throwable $e) {
        mysqli_rollback($db);
        throw $e;
    }

    return [
        'idPago' => $paymentId,
        'decision' => $decision,
        'idUsuario' => $targetUserId,
        'monthsGranted' => $monthsGranted,
        'testMinutesGranted' => $testMinutesGranted,
        'membership' =>
            $decision === 'APROBAR'
                ? membership_active($db, $targetUserId)
                : null,
    ];
}
