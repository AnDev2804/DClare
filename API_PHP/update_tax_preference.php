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
        $taxCode = strtoupper((string)api_string($data, 'taxCode', 40));
        $applies = api_bool($data['applies'] ?? null, 'applies');

        // No basta con que el RIF pertenezca al usuario: también debe estar
        // disponible con su plan actual.
        membership_require_accessible_company($db, $user['idUsuario'], $rif);

        $columns = [
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

        if (!isset($columns[$taxCode])) {
            api_throw(422, 'INVALID_TAX_CODE', 'El código de impuesto no es válido.', [
                'field' => 'taxCode',
            ]);
        }

        $ensure = $db->prepare('INSERT IGNORE INTO rif_tax_preferences (rif) VALUES (?)');
        $ensure->bind_param('s', $rif);
        $ensure->execute();
        $ensure->close();

        $column = $columns[$taxCode];
        $value = $applies ? 1 : 0;
        $stmt = $db->prepare("UPDATE rif_tax_preferences SET `{$column}` = ? WHERE rif = ? LIMIT 1");
        $stmt->bind_param('is', $value, $rif);
        $stmt->execute();
        $stmt->close();

        json_response([
            'success' => true,
            'message' => 'Preferencia actualizada correctamente.',
            'data' => [
                'rif' => $rif,
                'taxCode' => $taxCode,
                'applies' => $applies,
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
