<?php

declare(strict_types=1);

require_once __DIR__ . '/membership_service.php';

/*
 * DEBUG TEMPORAL:
 * Estos logs sirven para localizar exactamente dónde falla Google Sign-In.
 * No registrar nunca el contenido del idToken ni el token Bearer de Clara.
 */
error_log('[GoogleSignInV2] 0 - ARCHIVO CARGADO');

$autoload = __DIR__ . '/vendor/autoload.php';

if (is_file($autoload)) {
    require_once $autoload;

    error_log(
        '[GoogleSignInV2] 0.1 - COMPOSER AUTOLOAD CARGADO'
    );
} else {
    error_log(
        '[GoogleSignInV2] 0.1 - ERROR: vendor/autoload.php NO EXISTE'
    );
}

api_run(function (): void {

    error_log(
        '[GoogleSignInV2] 1 - ENTRO EN API_RUN'
    );

    api_require_method('POST');

    error_log(
        '[GoogleSignInV2] 2 - METODO POST OK'
    );

    /*
     * GoogleSignInV2 recibe JSON:
     *
     * {
     *   "idToken": "..."
     * }
     */
    $data = api_request_data();

    error_log(
        '[GoogleSignInV2] 3 - BODY LEIDO'
    );

    $idToken = api_string(
        $data,
        'idToken',
        8192
    );

    /*
     * Solo registramos longitud.
     * NO registrar el token.
     */
    error_log(
        '[GoogleSignInV2] 4 - ID TOKEN RECIBIDO, longitud=' .
        strlen($idToken)
    );
    
    $config = clara_private_config();

    $googleClientIds =
        trim(
            (string)(
                getenv('GOOGLE_CLIENT_IDS')
                ?: ($config['GOOGLE_CLIENT_IDS'] ?? '')
            )
        );

    /*
     * GOOGLE_CLIENT_IDS puede contener uno o varios Client IDs
     * separados por coma.
     */
    $allowedClientIds = array_values(
        array_filter(
            array_map(
                'trim',
                explode(
                    ',',
                    $googleClientIds
                )
            )
        )
    );

    error_log(
        '[GoogleSignInV2] 5 - GOOGLE_CLIENT_IDS=' .
        count($allowedClientIds)
    );

    if (count($allowedClientIds) === 0) {
        api_throw(
            503,
            'GOOGLE_AUTH_NOT_CONFIGURED',
            'El acceso con Google no está configurado en el servidor.'
        );
    }

    /*
     * Google Auth Library:
     *
     * Google\Auth\AccessToken
     */
    $accessTokenClass =
        'Google\\Auth\\AccessToken';

    $googleClassAvailable =
        class_exists($accessTokenClass);

    error_log(
        '[GoogleSignInV2] 6 - clase Google=' .
        (
            $googleClassAvailable
                ? 'SI'
                : 'NO'
        )
    );

    if (!$googleClassAvailable) {
        api_throw(
            503,
            'GOOGLE_AUTH_LIBRARY_MISSING',
            'Falta instalar la librería oficial de autenticación de Google.'
        );
    }

    /*
     * Verificamos criptográficamente el ID token.
     */
    $verifyStarted = microtime(true);

    error_log(
        '[GoogleSignInV2] 7 - ANTES DE VERIFY'
    );

    try {
        $verifier =
            new $accessTokenClass();

        $google =
            $verifier->verify(
                $idToken
            );

        $verifySeconds =
            round(
                microtime(true) -
                $verifyStarted,
                3
            );

        error_log(
            '[GoogleSignInV2] 8 - VERIFY TERMINADO en ' .
            $verifySeconds .
            ' segundos. result=' .
            (
                is_array($google)
                    ? 'OK'
                    : 'INVALID'
            )
        );
    } catch (Throwable $e) {

        $verifySeconds =
            round(
                microtime(true) -
                $verifyStarted,
                3
            );

        error_log(
            '[GoogleSignInV2] 8 - VERIFY ERROR después de ' .
            $verifySeconds .
            ' segundos: ' .
            get_class($e) .
            ': ' .
            $e->getMessage()
        );

        api_throw(
            401,
            'INVALID_GOOGLE_TOKEN',
            'El token de Google no pudo ser verificado.'
        );
    }

    /*
     * verify() devuelve array si el token pudo verificarse.
     */
    if (!is_array($google)) {
        api_throw(
            401,
            'INVALID_GOOGLE_TOKEN',
            'El token de Google no es válido.'
        );
    }

    error_log(
        '[GoogleSignInV2] 9 - TOKEN GOOGLE VALIDO'
    );

    /*
     * Claims relevantes.
     */
    $audience =
        (string)(
            $google['aud']
            ?? ''
        );

    $issuer =
        (string)(
            $google['iss']
            ?? ''
        );

    $emailVerified =
        filter_var(
            $google['email_verified']
            ?? false,
            FILTER_VALIDATE_BOOLEAN
        );

    $expiresAt =
        (int)(
            $google['exp']
            ?? 0
        );

    $email =
        strtolower(
            trim(
                (string)(
                    $google['email']
                    ?? ''
                )
            )
        );

    /*
     * No escribimos email/aud/token en logs.
     * Solo indicamos si cada claim pasó.
     */
    $audienceOk =
        in_array(
            $audience,
            $allowedClientIds,
            true
        );

    $issuerOk =
        in_array(
            $issuer,
            [
                'accounts.google.com',
                'https://accounts.google.com',
            ],
            true
        );

    $expirationOk =
        $expiresAt > time();

    $emailOk =
        filter_var(
            $email,
            FILTER_VALIDATE_EMAIL
        ) !== false;

    error_log(
        '[GoogleSignInV2] 9.1 - CLAIMS: ' .
        'aud=' . ($audienceOk ? 'OK' : 'FAIL') .
        ', iss=' . ($issuerOk ? 'OK' : 'FAIL') .
        ', email_verified=' . ($emailVerified ? 'OK' : 'FAIL') .
        ', exp=' . ($expirationOk ? 'OK' : 'FAIL') .
        ', email=' . ($emailOk ? 'OK' : 'FAIL')
    );

    if (
        !$audienceOk ||
        !$issuerOk ||
        !$emailVerified ||
        !$expirationOk ||
        !$emailOk
    ) {
        api_throw(
            401,
            'INVALID_GOOGLE_TOKEN',
            'El token de Google no es válido para esta aplicación.'
        );
    }

    error_log(
        '[GoogleSignInV2] 10 - CLAIMS GOOGLE VALIDOS'
    );

    /*
     * Desde aquí ya terminó la autenticación con Google.
     * Ahora trabajamos con la cuenta existente en Clara.
     */
    error_log(
        '[GoogleSignInV2] 10.1 - ANTES DE CONECTAR BD'
    );

    $db = api_db();

    error_log(
        '[GoogleSignInV2] 10.2 - BD CONECTADA'
    );

    try {

        /*
         * Google Sign-In NO registra automáticamente usuarios.
         *
         * El correo debe existir previamente en Clara.
         */
        error_log(
            '[GoogleSignInV2] 10.3 - BUSCANDO USUARIO'
        );

        $stmt = $db->prepare(
            'SELECT
                l.ID,
                l.Correo,
                l.reg_state,
                l.num_rol_data,
                i.nya,
                r.Rol

             FROM logdata l

             LEFT JOIN infoper i
                ON i.ID_Foranea = l.ID

             INNER JOIN roles r
                ON r.Num_Rol = l.num_rol_data

             WHERE l.Correo = ?

             LIMIT 1'
        );

        $stmt->bind_param(
            's',
            $email
        );

        $stmt->execute();

        $profile =
            $stmt
                ->get_result()
                ->fetch_assoc();

        $stmt->close();

        if (!$profile) {
            error_log(
                '[GoogleSignInV2] 10.4 - USUARIO NO REGISTRADO'
            );

            if (isset($db) && $db instanceof mysqli) {
                try {
                    $db->close();
                } catch (Throwable $ignored) {
                }
            }

            json_response([
                'success' => false,
                'code' => 'USER_NOT_REGISTERED',
                'message' => 'El correo de Google no se encuentra registrado en el sistema. Por favor, regístrese primero.',
            ], 200);
        }

        error_log(
            '[GoogleSignInV2] 11 - USUARIO ENCONTRADO'
        );

        $userId =
            (int)$profile['ID'];

        if ($userId <= 0) {
            throw new RuntimeException(
                'El usuario encontrado tiene un ID inválido.'
            );
        }

        /*
         * Sincroniza membresías vencidas antes de generar
         * la sesión/respuesta.
         */
        error_log(
            '[GoogleSignInV2] 11.1 - ANTES DE MEMBERSHIP SYNC'
        );

        membership_sync_expired(
            $db,
            $userId
        );

        error_log(
            '[GoogleSignInV2] 12 - MEMBERSHIP SYNC OK'
        );

        /*
         * Volvemos a consultar el perfil porque
         * membership_sync_expired() puede cambiar el rol.
         */
        error_log(
            '[GoogleSignInV2] 12.1 - REFRESCANDO PERFIL'
        );

        $refresh = $db->prepare(
            'SELECT
                l.Correo,
                l.reg_state,
                l.num_rol_data,
                i.nya,
                r.Rol

             FROM logdata l

             LEFT JOIN infoper i
                ON i.ID_Foranea = l.ID

             INNER JOIN roles r
                ON r.Num_Rol = l.num_rol_data

             WHERE l.ID = ?

             LIMIT 1'
        );

        $refresh->bind_param(
            'i',
            $userId
        );

        $refresh->execute();

        $profile =
            $refresh
                ->get_result()
                ->fetch_assoc();

        $refresh->close();

        if (!$profile) {
            throw new RuntimeException(
                'No fue posible refrescar el perfil del usuario.'
            );
        }

        error_log(
            '[GoogleSignInV2] 12.2 - PERFIL REFRESCADO'
        );

        /*
         * Generamos el Bearer propio de Clara.
         */
        error_log(
            '[GoogleSignInV2] 12.3 - ANTES DE CREAR SESION'
        );

        $session =
            auth_issue_session(
                $db,
                $userId
            );

        error_log(
            '[GoogleSignInV2] 13 - SESION CREADA'
        );

        /*
         * Obtenemos estado completo de membresía.
         */
        error_log(
            '[GoogleSignInV2] 13.1 - ANTES DE MEMBERSHIP STATUS'
        );

        $membership =
            membership_status(
                $db,
                $userId
            );

        error_log(
            '[GoogleSignInV2] 14 - MEMBERSHIP STATUS OK'
        );

        /*
         * Validaciones mínimas antes de responder.
         */
        if (
            !isset($session['token']) ||
            !is_string($session['token']) ||
            !preg_match(
                '/^[a-f0-9]{64}$/i',
                $session['token']
            )
        ) {
            throw new RuntimeException(
                'La sesión generada no contiene un token válido.'
            );
        }

        if (
            !isset($session['expiresAt']) ||
            trim(
                (string)$session['expiresAt']
            ) === ''
        ) {
            throw new RuntimeException(
                'La sesión generada no contiene fecha de expiración.'
            );
        }

        error_log(
            '[GoogleSignInV2] 15 - RESPUESTA LISTA'
        );

        /*
         * IMPORTANTE:
         * no registrar $session['token'] en error.log.
         */
        json_response([
            'success' => true,

            'idUsuario' =>
                $userId,

            'email' =>
                (string)$profile['Correo'],

            'name' =>
                (string)(
                    $profile['nya']
                    ?? ''
                ),

            'state' =>
                (int)$profile['reg_state'],

            'numRol' =>
                (int)$profile['num_rol_data'],

            'rol' =>
                (string)$profile['Rol'],

            'token' =>
                $session['token'],

            'tokenType' =>
                $session['tokenType'],

            'tokenExpiresAt' =>
                $session['expiresAt'],

            'membership' =>
                $membership,
        ]);

    } finally {

        error_log(
            '[GoogleSignInV2] 16 - CERRANDO BD'
        );

        if (
            isset($db) &&
            $db instanceof mysqli
        ) {
            try {
                $db->close();
            } catch (Throwable $ignored) {
            }
        }
    }
});