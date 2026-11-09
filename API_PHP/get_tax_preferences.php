<?php

declare(strict_types=1);

require_once __DIR__ . '/membership_service.php';

api_run(function (): void {
    api_require_method('POST');
    $db = api_db();

    try {
        $user = auth_require_user($db);
        $data = api_request_data();
        $rif = (string)api_string($data, 'rif', 225);

        membership_require_accessible_company($db, $user['idUsuario'], $rif);

        $ensure = $db->prepare('INSERT IGNORE INTO rif_tax_preferences (rif) VALUES (?)');
        $ensure->bind_param('s', $rif);
        $ensure->execute();
        $ensure->close();

        $stmt = $db->prepare(
            'SELECT anti_islr, dd_islr, dec_auto_entdes, dec_def_islrei,
                    dec_gra_patri, dec_igtf, dec_iva, dec_prot_pen,
                    envite_azar, est_islr_rent, ret_islr_lot,
                    ret_islr_rent, ret_iva
             FROM rif_tax_preferences
             WHERE rif = ?
             LIMIT 1'
        );
        $stmt->bind_param('s', $rif);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$row) {
            api_throw(404, 'TAX_PREFERENCES_NOT_FOUND', 'No se encontraron preferencias tributarias.');
        }

        json_response([
            'success' => true,
            'preferences' => [
                'ANTI_ISLR' => (bool)$row['anti_islr'],
                'DD_ISLR' => (bool)$row['dd_islr'],
                'DEC_AUTO_ENTDES' => (bool)$row['dec_auto_entdes'],
                'DEC_DEF_ISLREI' => (bool)$row['dec_def_islrei'],
                'DEC_GRA_PATRI' => (bool)$row['dec_gra_patri'],
                'DEC_IGTF' => (bool)$row['dec_igtf'],
                'DEC_IVA' => (bool)$row['dec_iva'],
                'DEC_PROT_PEN' => (bool)$row['dec_prot_pen'],
                'ENVITE_AZAR' => (bool)$row['envite_azar'],
                'EST_ISLR_RENT' => (bool)$row['est_islr_rent'],
                'RET_ISLR_LOT' => (bool)$row['ret_islr_lot'],
                'RET_ISLR_RENT' => (bool)$row['ret_islr_rent'],
                'RET_IVA' => (bool)$row['ret_iva'],
            ],
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
