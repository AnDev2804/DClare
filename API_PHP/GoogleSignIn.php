<?php
/*
 * Compatibilidad segura: el endpoint antiguo ya NO acepta un correo sin prueba.
 * Mantener este archivo evita que una URL vieja quede como bypass de autenticación.
 * El cliente Android de la fase 2 usa directamente GoogleSignInV2.php.
 */
require_once __DIR__ . '/GoogleSignInV2.php';
