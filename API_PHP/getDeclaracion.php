<?php

declare(strict_types=1);

require_once __DIR__ . '/membership_service.php';

function declaration_next_for_user(mysqli $db, int $userId): array
{
    $tz = new DateTimeZone('America/Caracas');
    $today = new DateTimeImmutable('today', $tz);

    $companies = membership_accessible_companies($db, $userId);
    if ($companies === []) {
        return [
            'success' => true,
            'today' => $today->format('Y-m-d'),
            'next' => null,
            'ties' => [],
            'message' => 'Sin empresas registradas.',
        ];
    }

    $accessibleRifs = [];
    foreach ($companies as $company) {
        $accessibleRifs[(string)$company['rif']] = true;
    }

    $prefsByRif = [];
    $prefsStmt = $db->prepare(
        'SELECT p.rif, p.anti_islr, p.dd_islr, p.dec_auto_entdes,
                p.dec_def_islrei, p.dec_gra_patri, p.dec_igtf, p.dec_iva,
                p.dec_prot_pen, p.envite_azar, p.est_islr_rent,
                p.ret_islr_lot, p.ret_islr_rent, p.ret_iva
         FROM rif_tax_preferences p
         INNER JOIN rif r ON r.RIF = p.rif
         WHERE r.ID_Foranea = ?'
    );
    $prefsStmt->bind_param('i', $userId);
    $prefsStmt->execute();
    $prefsResult = $prefsStmt->get_result();
    while ($row = $prefsResult->fetch_assoc()) {
        $rif = (string)$row['rif'];
        if (isset($accessibleRifs[$rif])) {
            $prefsByRif[$rif] = $row;
        }
    }
    $prefsStmt->close();

    $byDigit = [];
    foreach ($companies as $company) {
        $rifDigits = preg_replace('/\D/', '', (string)$company['rif']);
        if ($rifDigits === '') {
            continue;
        }

        $digit = substr($rifDigits, -1);
        $byDigit[$digit] ??= [];
        $byDigit[$digit][] = [
            'rif' => (string)$company['rif'],
            'tipo' => (int)$company['tipo'],
            'nombre' => (string)$company['nombre'],
        ];
    }

    if ($byDigit === []) {
        return [
            'success' => true,
            'today' => $today->format('Y-m-d'),
            'next' => null,
            'ties' => [],
            'message' => 'RIF inválidos.',
        ];
    }

    $taxTables = [
        'ANTI_ISLR' => ['code' => 'ANTI_ISLR', 'name' => 'ANTICIPO DE I.S.L.R.', 'pref' => 'anti_islr'],
        'DD_ISLR' => ['code' => 'DD_ISLR', 'name' => 'DECLARACIONES DEFINITIVAS DEL I.S.L.R.', 'pref' => 'dd_islr'],
        'DEC_AUTO_ENTDES' => ['code' => 'DEC_AUTO_ENTDES', 'name' => 'DECLARACIONES DE LOS SERVICIOS Y ENTES DESCENTRALIZADOS', 'pref' => 'dec_auto_entdes'],
        'DEC_DEF_ISLREI' => ['code' => 'DEC_DEF_ISLREI', 'name' => 'DECLARACIONES DEFINITIVAS DEL I.S.R.L.', 'pref' => 'dec_def_islrei'],
        'DEC_GRA_PATRI' => ['code' => 'DEC_GRA_PATRI', 'name' => 'DECLARACIONES A LOS GRANDES PATRIMONIOS', 'pref' => 'dec_gra_patri'],
        'DEC_IGTF' => ['code' => 'DEC_IGTF', 'name' => 'DECLARACIONES DE I.G.T.F.', 'pref' => 'dec_igtf'],
        'DEC_IVA' => ['code' => 'DEC_IVA', 'name' => 'DECLARACIONES DE I.V.A.', 'pref' => 'dec_iva'],
        'DEC_PROT_PEN' => ['code' => 'DEC_PROT_PEN', 'name' => 'DECLARACIONES DE PROTECCIÓN DE LAS PENSIONES', 'pref' => 'dec_prot_pen'],
        'ENVITE_AZAR' => ['code' => 'ENVITE_AZAR', 'name' => 'DECLARACIONES DE JUEGOS DE ENVITE Y AZAR', 'pref' => 'envite_azar'],
        'EST_ISLR_RENT' => ['code' => 'EST_ISLR_RENT', 'name' => 'ESTIMADAS DE I.S.L.R.', 'pref' => 'est_islr_rent'],
        'RET_ISLR_LOT' => ['code' => 'RET_ISLR_LOT', 'name' => 'RETENCIONES ISLR (PREMIOS LOTERÍA)', 'pref' => 'ret_islr_lot'],
        'RET_ISLR_RENT' => ['code' => 'RET_ISLR_RENT', 'name' => 'RETENCIONES DE I.S.L.R.', 'pref' => 'ret_islr_rent'],
        'RET_IVA' => ['code' => 'RET_IVA', 'name' => 'RETENCIONES DE I.V.A.', 'pref' => 'ret_iva'],
    ];

    $months = [
        'enero' => 1,
        'febrero' => 2,
        'marzo' => 3,
        'abril' => 4,
        'mayo' => 5,
        'junio' => 6,
        'julio' => 7,
        'agosto' => 8,
        'septiembre' => 9,
        'octubre' => 10,
        'noviembre' => 11,
        'diciembre' => 12,
    ];

    $computeNext = static function (int $month, int $day) use ($today, $tz): ?DateTimeImmutable {
        $year = (int)$today->format('Y');
        if (!checkdate($month, $day, $year)) {
            return null;
        }

        $date = DateTimeImmutable::createFromFormat(
            '!Y-n-j',
            $year . '-' . $month . '-' . $day,
            $tz
        );
        if ($date === false) {
            return null;
        }

        return $date < $today ? $date->modify('+1 year') : $date;
    };

    $best = null;
    $ties = [];

    foreach ($byDigit as $digitString => $companyList) {
        $digit = (int)$digitString;

        foreach ($taxTables as $table => $meta) {
            $filteredCompanies = array_values(array_filter(
                $companyList,
                static function (array $company) use ($prefsByRif, $meta): bool {
                    $rif = (string)$company['rif'];
                    if (!isset($prefsByRif[$rif])) {
                        return true;
                    }
                    return (int)($prefsByRif[$rif][$meta['pref']] ?? 1) === 1;
                }
            ));

            if ($filteredCompanies === []) {
                continue;
            }

            $calendarStmt = $db->prepare("SELECT * FROM `{$table}` WHERE ID = ? LIMIT 1");
            $calendarStmt->bind_param('i', $digit);
            $calendarStmt->execute();
            $calendarRow = $calendarStmt->get_result()->fetch_assoc();
            $calendarStmt->close();

            if (!$calendarRow) {
                continue;
            }

            foreach ($months as $monthName => $monthNumber) {
                if (!array_key_exists($monthName, $calendarRow)) {
                    continue;
                }

                $raw = $calendarRow[$monthName];
                if ($raw === null || trim((string)$raw) === '' || strtolower((string)$raw) === 'null') {
                    continue;
                }

                foreach (explode(';', (string)$raw) as $part) {
                    $day = (int)trim($part);
                    if ($day < 1 || $day > 31) {
                        continue;
                    }

                    $nextDate = $computeNext($monthNumber, $day);
                    if ($nextDate === null) {
                        continue;
                    }

                    $candidate = [
                        'date' => $nextDate->format('Y-m-d'),
                        'digit' => (string)$digitString,
                        'impuesto' => [
                            'code' => $meta['code'],
                            'name' => $meta['name'],
                        ],
                        'empresas' => $filteredCompanies,
                        'source' => [
                            'table' => $table,
                            'month' => $monthName,
                            'day' => $day,
                        ],
                    ];

                    if ($best === null) {
                        $best = $candidate;
                        continue;
                    }

                    $bestDate = new DateTimeImmutable($best['date'], $tz);
                    if ($nextDate < $bestDate) {
                        $best = $candidate;
                        $ties = [];
                    } elseif ($nextDate == $bestDate) {
                        $ties[] = $candidate;
                    }
                }
            }
        }
    }

    return [
        'success' => true,
        'today' => $today->format('Y-m-d'),
        'next' => $best,
        'ties' => $ties,
    ];
}

api_run(function (): void {
    api_require_method('POST');
    $db = api_db();

    try {
        $user = auth_require_user($db);
        $result = declaration_next_for_user($db, $user['idUsuario']);
        json_response($result);
    } finally {
        if (isset($db) && $db instanceof mysqli) {
            try {
                $db->close();
            } catch (Throwable $ignored) {
            }
        }
    }
});
