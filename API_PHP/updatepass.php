<?php

declare(strict_types=1);

require_once __DIR__ . '/api_bootstrap.php';

api_run(function (): void {
    api_throw(
        410,
        'LEGACY_ENDPOINT_DISABLED',
        'Este endpoint fue deshabilitado. Utilice el flujo verificado de recuperación de contraseña.'
    );
});
