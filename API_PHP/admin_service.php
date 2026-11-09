<?php

declare(strict_types=1);

require_once __DIR__ . '/membership_service.php';

function admin_payment_from_row(array $row, bool $includeSensitive = true): array
{
    $payment = [
        'idPago' => (int)$row['id_pago'],
        'idUsuario' => (int)$row['id_usuario'],

        'nombre' => (string)($row['nya'] ?? ''),
        'correo' => (string)($row['Correo'] ?? ''),

        'usuarioSnapshot' => [
            'nombre' => (string)($row['nombre_usuario_snapshot'] ?? ''),
            'correo' => (string)$row['correo_usuario_snapshot'],
        ],

        'fechaPago' => membership_utc($row['fecha_pago']),
        'zonaHorariaPago' => (string)$row['zona_horaria_pago'],

        'numeroReferencia' => (string)$row['num_referencia'],

        'montoPago' => (string)$row['monto_pago'],
        'montoRequerido' => $row['monto_requerido'] === null
            ? null
            : (string)$row['monto_requerido'],

        'moneda' => (string)$row['moneda'],
        'precioUsdBase' => (string)$row['precio_usd_base'],

        'tasaCambio' => $row['tasa_cambio_aplicada'] === null
            ? null
            : (string)$row['tasa_cambio_aplicada'],

        'fuenteTasa' => $row['fuente_tasa'] ?? null,
        'fechaTasa' => membership_utc($row['fecha_tasa'] ?? null),

        'metodoPago' => [
            'id' => (int)$row['id_metodo_pago'],
            'codigo' => (string)$row['metodo_pago_snapshot'],
            'codigoActual' => (string)($row['metodo_codigo'] ?? ''),
            'nombre' => (string)($row['metodo_nombre'] ?? ''),
            'tipo' => (string)($row['metodo_tipo'] ?? ''),
            'monedaPrincipal' => $row['metodo_moneda'] ?? null,
        ],

        'bancoEmisor' => $row['banco_emisor'] === null
            ? null
            : (string)$row['banco_emisor'],

        'identificadorEmisor' => $row['identificador_emisor'] === null
            ? null
            : (string)$row['identificador_emisor'],

        'statusId' => (int)$row['id_membresia_status'],
        'statusCode' => (string)$row['codigo_status'],
        'statusDescription' => (string)$row['descripcion_status'],

        'observacionUsuario' => $row['observacion_usuario'],
        'observacionAdmin' => $row['observacion_admin'],

        'fechaSolicitud' => membership_utc($row['fecha_solicitud']),
        'fechaRevision' => membership_utc($row['fecha_revision']),

        'comprobanteDisponible' =>
            !empty($row['comprobante_archivo']),

        'comprobanteEndpoint' =>
            !empty($row['comprobante_archivo'])
                ? 'membership_payment_receipt.php?idPago=' . (int)$row['id_pago']
                : null,
    ];

    if ($includeSensitive) {
        $payment['numeroCuentaEmisora'] =
            $row['numero_cuenta_emisora'] === null
                ? null
                : (string)$row['numero_cuenta_emisora'];

        $payment['nombreTitular'] =
            $row['nombre_titular'] === null
                ? null
                : (string)$row['nombre_titular'];

        $payment['comprobanteNombreOriginal'] =
            $row['comprobante_nombre_original'] ?? null;

        $payment['comprobanteMime'] =
            $row['comprobante_mime'] ?? null;

        $payment['comprobanteTamano'] =
            isset($row['comprobante_tamano'])
                ? (int)$row['comprobante_tamano']
                : null;

        $payment['comprobanteSha256'] =
            $row['comprobante_sha256'] ?? null;

        $payment['idAdminRevision'] =
            $row['id_admin_revision'] === null
                ? null
                : (int)$row['id_admin_revision'];
    }

    return $payment;
}

function admin_search_users(mysqli $db, string $query, int $limit): array
{
    $query = preg_replace('/\s+/', ' ', trim($query));
    $limit = max(1, min($limit, 200));
    membership_sync_expired($db);

    if ($query === '') {
        $stmt = $db->prepare(
            "SELECT
                l.ID AS id_usuario,
                l.Correo,
                i.nya,
                l.reg_state,
                l.num_rol_data,
                r.Rol,
                (SELECT MAX(h.fecha_fin)
                 FROM historial_membresias h
                 WHERE h.id_usuario = l.ID
                   AND h.estado = 'ACTIVA'
                   AND h.fecha_inicio <= UTC_TIMESTAMP()
                   AND h.fecha_fin > UTC_TIMESTAMP()) AS premium_hasta,
                (SELECT COUNT(*) FROM rif rf WHERE rf.ID_Foranea = l.ID) AS empresas_count,
                (SELECT COUNT(*)
                 FROM pagos_membresia p
                 WHERE p.id_usuario = l.ID AND p.id_membresia_status = 2) AS pagos_pendientes
             FROM logdata l
             LEFT JOIN infoper i ON i.ID_Foranea = l.ID
             INNER JOIN roles r ON r.Num_Rol = l.num_rol_data
             ORDER BY l.ID DESC
             LIMIT ?"
        );
        $stmt->bind_param('i', $limit);
    } else {
        $pattern = '%' . $query . '%';
        $stmt = $db->prepare(
            "SELECT
                l.ID AS id_usuario,
                l.Correo,
                i.nya,
                l.reg_state,
                l.num_rol_data,
                r.Rol,
                (SELECT MAX(h.fecha_fin)
                 FROM historial_membresias h
                 WHERE h.id_usuario = l.ID
                   AND h.estado = 'ACTIVA'
                   AND h.fecha_inicio <= UTC_TIMESTAMP()
                   AND h.fecha_fin > UTC_TIMESTAMP()) AS premium_hasta,
                (SELECT COUNT(*) FROM rif rf WHERE rf.ID_Foranea = l.ID) AS empresas_count,
                (SELECT COUNT(*)
                 FROM pagos_membresia p
                 WHERE p.id_usuario = l.ID AND p.id_membresia_status = 2) AS pagos_pendientes
             FROM logdata l
             LEFT JOIN infoper i ON i.ID_Foranea = l.ID
             INNER JOIN roles r ON r.Num_Rol = l.num_rol_data
             WHERE l.Correo LIKE ? OR i.nya LIKE ?
             ORDER BY
                CASE WHEN l.Correo = ? THEN 0 WHEN i.nya = ? THEN 1 ELSE 2 END,
                i.nya ASC,
                l.Correo ASC
             LIMIT ?"
        );
        $stmt->bind_param('ssssi', $pattern, $pattern, $query, $query, $limit);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = [
            'idUsuario' => (int)$row['id_usuario'],
            'nombre' => (string)($row['nya'] ?? ''),
            'correo' => (string)$row['Correo'],
            'regState' => (int)$row['reg_state'],
            'numRol' => (int)$row['num_rol_data'],
            'rol' => (string)$row['Rol'],
            'isPremium' => (int)$row['num_rol_data'] === AUTH_ROLE_PREMIUM,
            'premiumHasta' => membership_utc($row['premium_hasta']),
            'empresasCount' => (int)$row['empresas_count'],
            'pagosPendientes' => (int)$row['pagos_pendientes'],
        ];
    }
    $stmt->close();
    return $users;
}

function admin_user_detail(mysqli $db, int $userId): array
{
    membership_sync_expired($db, $userId);

    $stmt = $db->prepare(
        'SELECT l.ID AS id_usuario, l.Correo, l.reg_state, l.num_rol_data,
                i.nya, r.Rol
         FROM logdata l
         LEFT JOIN infoper i ON i.ID_Foranea = l.ID
         INNER JOIN roles r ON r.Num_Rol = l.num_rol_data
         WHERE l.ID = ?
         LIMIT 1'
    );
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $profile = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$profile) {
        api_throw(404, 'USER_NOT_FOUND', 'Usuario no encontrado.');
    }

    $companyStmt = $db->prepare(
        'SELECT
                rf.id_rif,
                rf.RIF,
                rf.tipo_de_cliente,
                tc.desc_tc,
                rf.nombre_empresa,
                rf.created_at,
                p.anti_islr, p.dd_islr, p.dec_auto_entdes, p.dec_def_islrei,
                p.dec_gra_patri, p.dec_igtf, p.dec_iva, p.dec_prot_pen,
                p.envite_azar, p.est_islr_rent, p.ret_islr_lot,
                p.ret_islr_rent, p.ret_iva
         FROM rif rf
         LEFT JOIN tipo_cliente tc ON tc.id_tipo_cliente = rf.tipo_de_cliente
         LEFT JOIN rif_tax_preferences p ON p.rif = rf.RIF
         WHERE rf.ID_Foranea = ?
         ORDER BY rf.id_rif ASC'
    );
    $companyStmt->bind_param('i', $userId);
    $companyStmt->execute();
    $companyResult = $companyStmt->get_result();
    $companies = [];
    $preferenceColumns = [
        'ANTI_ISLR' => 'anti_islr',
        'DD_ISLR' => 'dd_islr',
        'DEC_AUTO_ENTDES' => 'dec_auto_entdes',
        'DEC_DEF_ISLREI' => 'dec_def_islrei',
        'DEC_GRA_PATRI' => 'dec_gra_patri',
        'DEC_IGTF' => 'dec_igtf',
        'DEC_IVA' => 'dec_iva',
        'DEC_PROT_PEN' => 'dec_prot_pen',
        'ENVITE_AZAR' => 'envite_azar',
        'EST_ISLR_RENT' => 'est_islr_rent',
        'RET_ISLR_LOT' => 'ret_islr_lot',
        'RET_ISLR_RENT' => 'ret_islr_rent',
        'RET_IVA' => 'ret_iva',
    ];
    while ($row = $companyResult->fetch_assoc()) {
        $preferences = [];
        foreach ($preferenceColumns as $code => $column) {
            $preferences[$code] = $row[$column] === null ? true : (bool)$row[$column];
        }
        $companies[] = [
            'idRif' => (int)$row['id_rif'],
            'rif' => (string)$row['RIF'],
            'tipoId' => (int)$row['tipo_de_cliente'],
            'tipo' => (string)($row['desc_tc'] ?? ''),
            'nombre' => (string)$row['nombre_empresa'],
            'createdAt' => membership_utc($row['created_at']),
            'taxPreferences' => $preferences,
        ];
    }
    $companyStmt->close();

    $paymentStmt = $db->prepare(
        'SELECT
            p.*,
            s.codigo_status,
            s.descripcion_status,

            mp.codigo AS metodo_codigo,
            mp.nombre AS metodo_nombre,
            mp.tipo AS metodo_tipo,
            mp.moneda_principal AS metodo_moneda,

            l.Correo,
            i.nya

        FROM pagos_membresia p

        INNER JOIN pagos_membresia_status s
                ON s.id_membresia_status = p.id_membresia_status

        INNER JOIN membresia_metodos_pago mp
                ON mp.id_metodo_pago = p.id_metodo_pago

        INNER JOIN logdata l
                ON l.ID = p.id_usuario

        LEFT JOIN infoper i
                ON i.ID_Foranea = l.ID

        WHERE p.id_usuario = ?

        ORDER BY p.fecha_solicitud DESC'
    );
    $paymentStmt->bind_param('i', $userId);
    $paymentStmt->execute();
    $paymentResult = $paymentStmt->get_result();
    $payments = [];
    while ($row = $paymentResult->fetch_assoc()) {
        $payments[] = admin_payment_from_row($row, true);
    }
    $paymentStmt->close();

    $historyStmt = $db->prepare(
        'SELECT h.*, la.Correo AS admin_correo, ia.nya AS admin_nya,
                lr.Correo AS revocado_por_correo, ir.nya AS revocado_por_nya
         FROM historial_membresias h
         LEFT JOIN logdata la ON la.ID = h.id_admin
         LEFT JOIN infoper ia ON ia.ID_Foranea = la.ID
         LEFT JOIN logdata lr ON lr.ID = h.id_admin_revocacion
         LEFT JOIN infoper ir ON ir.ID_Foranea = lr.ID
         WHERE h.id_usuario = ?
         ORDER BY h.fecha_inicio DESC, h.id_historial DESC'
    );
    $historyStmt->bind_param('i', $userId);
    $historyStmt->execute();
    $historyResult = $historyStmt->get_result();
    $history = [];
    while ($row = $historyResult->fetch_assoc()) {
        $history[] = [
            'idHistorial' => (int)$row['id_historial'],
            'idPago' => $row['id_pago'] === null ? null : (int)$row['id_pago'],
            'tipoAsignacion' => (string)$row['tipo_asignacion'],
            'estado' => (string)$row['estado'],
            'usuarioSnapshot' => [
                'nombre' => (string)($row['nombre_usuario_snapshot'] ?? ''),
                'correo' => (string)$row['correo_usuario_snapshot'],
            ],
            'fechaInicio' => membership_utc($row['fecha_inicio']),
            'fechaFin' => membership_utc($row['fecha_fin']),
            'fechaCambioEstado' => membership_utc($row['fecha_cambio_estado']),
            'admin' => $row['id_admin'] === null ? null : [
                'idUsuario' => (int)$row['id_admin'],
                'nombre' => (string)($row['admin_nya'] ?? ''),
                'correo' => (string)($row['admin_correo'] ?? ''),
            ],
            'observacion' => $row['observacion'],
            'revocacion' => $row['fecha_revocacion'] === null ? null : [
                'fecha' => membership_utc($row['fecha_revocacion']),
                'motivo' => $row['motivo_revocacion'],
                'admin' => $row['id_admin_revocacion'] === null ? null : [
                    'idUsuario' => (int)$row['id_admin_revocacion'],
                    'nombre' => (string)($row['revocado_por_nya'] ?? ''),
                    'correo' => (string)($row['revocado_por_correo'] ?? ''),
                ],
            ],
        ];
    }
    $historyStmt->close();


    $trialStmt = $db->prepare(
        'SELECT
            id_prueba,
            id_historial,
            documento,
            nombre_completo,
            direccion,
            telefono,
            fecha_solicitud,
            created_at,
            updated_at
        FROM membresia_prueba_datos
        WHERE id_usuario = ?
        LIMIT 1'
    );

    $trialStmt->bind_param('i', $userId);
    $trialStmt->execute();

    $trialRow = $trialStmt->get_result()->fetch_assoc();

    $trialStmt->close();

    $trialData = null;

    if ($trialRow) {
        $trialData = [
            'idPrueba' => (int)$trialRow['id_prueba'],
            'idHistorial' => (int)$trialRow['id_historial'],
            'documento' => (string)$trialRow['documento'],
            'nombreCompleto' => (string)$trialRow['nombre_completo'],
            'direccion' => (string)$trialRow['direccion'],
            'telefono' => (string)$trialRow['telefono'],
            'fechaSolicitud' => membership_utc($trialRow['fecha_solicitud']),
            'createdAt' => membership_utc($trialRow['created_at']),
            'updatedAt' => membership_utc($trialRow['updated_at']),
        ];
    }
    $membershipStatus = membership_status($db, $userId);
    return [
        'profile' => [
            'idUsuario' => (int)$profile['id_usuario'],
            'nombre' => (string)($profile['nya'] ?? ''),
            'correo' => (string)$profile['Correo'],
            'regState' => (int)$profile['reg_state'],
            'numRol' => (int)$profile['num_rol_data'],
            'rol' => (string)$profile['Rol'],
            'isPremium' => (int)$profile['num_rol_data'] === AUTH_ROLE_PREMIUM,
        ],
        'membership' => [
            'state' => $membershipStatus['state'],
            'current' => $membershipStatus['membership'],
            'isPremium' => $membershipStatus['isPremium'],
            'eligibleForFreeTrial' => $membershipStatus['eligibleForFreeTrial'],
            'trialUsed' => $membershipStatus['trialUsed'],
            'requiresPayment' => $membershipStatus['requiresPayment'],
            'canSubmitPayment' => $membershipStatus['canSubmitPayment'],
            'companyLimit' => $membershipStatus['companyLimit'],
            'unlimitedCompanies' => $membershipStatus['unlimitedCompanies'],
            'plan' => $membershipStatus['plan'],
            'history' => $history,
        ],
        'companies' => $companies,
        'payments' => $payments,
        'trialData' => $trialData,
    ];
}

function admin_pending_payments(
    mysqli $db,
    int $page,
    int $limit
): array {
    $page = max(1, $page);
    $limit = max(1, min($limit, 50));
    $offset = ($page - 1) * $limit;

    $countResult = $db->query(
        'SELECT COUNT(*) AS total
         FROM pagos_membresia
         WHERE id_membresia_status = 2'
    );

    if (!$countResult) {
        throw new RuntimeException(
            'No se pudo contar los pagos pendientes: ' .
            $db->error
        );
    }

    $countRow = $countResult->fetch_assoc();

    $total = (int)($countRow['total'] ?? 0);

    /*
     * $limit y $offset son enteros previamente validados y limitados.
     * Por eso podemos incorporarlos directamente y evitamos problemas
     * de drivers con parámetros en LIMIT/OFFSET.
     */
    $sql = "
        SELECT
            p.*,
            s.codigo_status,
            s.descripcion_status,
            l.Correo,
            i.nya
        FROM pagos_membresia p
        INNER JOIN pagos_membresia_status s
            ON s.id_membresia_status =
               p.id_membresia_status
        INNER JOIN logdata l
            ON l.ID = p.id_usuario
        LEFT JOIN infoper i
            ON i.ID_Foranea = l.ID
        WHERE p.id_membresia_status = 2
        ORDER BY
            p.fecha_solicitud ASC,
            p.id_pago ASC
        LIMIT {$limit}
        OFFSET {$offset}
    ";

    $result = $db->query($sql);

    if (!$result) {
        throw new RuntimeException(
            'No se pudieron consultar los pagos pendientes: ' .
            $db->error
        );
    }

    $payments = [];

    while ($row = $result->fetch_assoc()) {
        $payments[] =
            admin_payment_from_row(
                $row,
                false
            );
    }

    return [
        'items' => $payments,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'totalPages' =>
                $total === 0
                    ? 0
                    : (int)ceil(
                        $total / $limit
                    ),
        ],
    ];
}

function admin_payment_detail(
    mysqli $db,
    int $paymentId
): array {
    $stmt = $db->prepare(
        'SELECT
            p.*,
            s.codigo_status,
            s.descripcion_status,

            mp.codigo AS metodo_codigo,
            mp.nombre AS metodo_nombre,
            mp.tipo AS metodo_tipo,
            mp.moneda_principal AS metodo_moneda,

            l.Correo,
            i.nya

        FROM pagos_membresia p

        INNER JOIN pagos_membresia_status s
            ON s.id_membresia_status =
               p.id_membresia_status

        INNER JOIN membresia_metodos_pago mp
            ON mp.id_metodo_pago =
               p.id_metodo_pago

        INNER JOIN logdata l
            ON l.ID = p.id_usuario

        LEFT JOIN infoper i
            ON i.ID_Foranea = l.ID

        WHERE p.id_pago = ?

        LIMIT 1'
    );

    $stmt->bind_param(
        'i',
        $paymentId
    );

    $stmt->execute();

    $row =
        $stmt
            ->get_result()
            ->fetch_assoc();

    $stmt->close();

    if (!$row) {
        api_throw(
            404,
            'PAYMENT_NOT_FOUND',
            'Solicitud de pago no encontrada.'
        );
    }

    return admin_payment_from_row(
        $row,
        true
    );
}