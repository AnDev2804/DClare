# Fase 1 — Base de datos y servicios PHP de membresías

Este paquete implementa la base necesaria para el flujo de membresía Premium y para la futura pantalla `mainAdmin`. No incluye todavía las pantallas de Kotlin Multiplatform.

## Qué queda implementado

- Búsqueda administrativa por correo o por `infoper.nya`.
- Detalle del usuario sin exponer contraseña ni sesiones: perfil, rol, empresas, preferencias tributarias, membresía, historial y pagos.
- Prueba Premium de tres meses una sola vez.
- Solicitud de pago por Pago Móvil o transferencia, con imagen del comprobante.
- Bandeja de pagos pendientes y detalle protegido del comprobante.
- Aprobación/rechazo transaccional por un administrador.
- Activación Premium por tres meses desde el momento de aprobación.
- Snapshot de nombre y correo al pagar/activar, para que el historial no cambie si luego se edita el perfil.
- Desactivación manual y vencimiento automático de roles Premium.
- Inicio de sesión con token Bearer almacenado como hash en la base de datos.
- Endpoint V2 de Google que valida el ID token real, no un correo suministrado libremente.
- Texto y enlace de WhatsApp listos para que la app abra la conversación cuando se configure el número de la empresa.

## Reglas de negocio aplicadas

1. `roles.Num_Rol` se conserva: 1 = No Premium, 2 = Premium y 3 = Admin.
2. `historial_membresias` es la fuente de verdad; `logdata.num_rol_data` refleja el estado vigente para mantener compatibilidad con la app actual.
3. Si el usuario nunca aparece en `historial_membresias`, el administrador puede activarle una `PRUEBA_GRATIS` por tres meses.
4. Cuando ya existe cualquier historial, una nueva activación gratuita devuelve `PAYMENT_REQUIRED`.
5. Un pago aprobado crea una vigencia `PAGO_APROBADO` de tres meses desde la aprobación y cambia el rol a Premium dentro de la misma transacción.
6. No se permite enviar otro pago mientras exista uno pendiente ni mientras la membresía siga activa.
7. Los administradores no pueden cambiar el rol de otro administrador desde estos endpoints.

### Significado de los tres estados solicitados

| ID | Código | Uso |
|---:|---|---|
| 0 | `NO_CANCELADO` | Solicitud rechazada/no aprobada |
| 1 | `CANCELADO` | Pago verificado y aprobado |
| 2 | `PENDIENTE_APROBACION` | Esperando revisión administrativa |

En el uso venezolano, “cancelado” se interpreta aquí como “pagado”. Un usuario que jamás ha enviado pago no necesita una fila artificial con estado 0.

## Instalación

1. Haga una copia de seguridad de `declaraconclara`.
2. Ejecute `database/001_membresias.sql` en esa base de datos y luego las consultas de solo lectura de `database/002_verificar_instalacion.sql`.
3. Copie el contenido de `api/` al mismo directorio de los PHP actuales. La versión incluida de `connect.php` conserva todos los métodos existentes y añade configuración mediante variables de entorno.
4. Reemplace el `login.php` actual por el incluido. Publique `GoogleSignInV2.php` en paralelo; no retire todavía el `GoogleSignIn.php` usado por la app actual. En la fase 2 cambiaremos Kotlin al contrato V2 y entonces se podrá eliminar el endpoint inseguro anterior.
5. Configure las variables de `.env.example` mediante el panel del hosting, Apache/PHP-FPM o el mecanismo de secretos del servidor. No publique un archivo `.env` dentro del directorio web.
6. Verifique que PHP tenga habilitadas las extensiones `mysqli`, `fileinfo` y `openssl`.
7. Desde la raíz de este paquete ejecute `composer install --no-dev --optimize-autoloader` para instalar la librería oficial `google/auth` usada al validar ID tokens. Si el proyecto no usa login con Google, el resto de los endpoints funciona sin esa dependencia.
8. Programe el vencimiento periódico, por ejemplo cada hora:

   ```cron
   7 * * * * /usr/bin/php /ruta/privada/api/cron_expire_memberships.php
   ```

El login, la consulta de estado y las búsquedas administrativas también sincronizan vencimientos. El cron evita que un usuario inactivo conserve en `logdata` un rol Premium vencido hasta su próxima petición.

### Comprobantes

La opción recomendada es definir `MEMBERSHIP_RECEIPT_DIR` con una ruta fuera de `public_html`, `www` o cualquier directorio público. Si no se configura, se usa `api/storage/membership_receipts`; el paquete incluye un `.htaccess` que bloquea el acceso directo en Apache. En Nginx se debe denegar esa ruta expresamente.

Los archivos admitidos son JPG, PNG y WEBP, con un máximo de 5 MB. Se renombran aleatoriamente y se registra su SHA-256. Se descargan únicamente mediante `membership_payment_receipt.php`, que verifica que el solicitante sea el propietario o un administrador.

## Autenticación

`login.php` mantiene los campos anteriores (`email`, `name`, `state`, `numRol`) y añade:

```json
{
  "token": "<token de 64 caracteres>",
  "tokenType": "Bearer",
  "tokenExpiresAt": "2026-10-09T12:00:00Z"
}
```

Todos los servicios nuevos, salvo login, requieren:

```http
Authorization: Bearer <token>
```

No se debe autorizar una operación administrativa enviando el correo del administrador en el body. Un correo se puede falsificar; el token se valida contra `auth_sessions` y luego se comprueba que el rol actual sea 3.

`GoogleSignInV2.php` recibe un objeto con el ID token emitido por Google:

```json
{"idToken":"<google-id-token>"}
```

El servidor debe tener en `GOOGLE_CLIENT_IDS` todos los Client ID legítimos de Android, iOS o Web. La firma se valida localmente mediante la librería oficial, además de comprobar audiencia, emisor y vencimiento. La versión anterior que aceptaba solo un correo no autentica realmente al usuario y no debe usarse para el módulo administrativo.

## Endpoints

| Archivo | Método | Acceso | Propósito |
|---|---|---|---|
| `login.php` | POST JSON | Público | Login y emisión de sesión |
| `GoogleSignInV2.php` | POST JSON | Público | Login Google verificado y emisión de sesión |
| `logout.php` | POST | Usuario | Revocar la sesión actual |
| `get_membership_status.php` | GET | Usuario | Estado, vencimiento, prueba y pago pendiente |
| `submit_membership_payment.php` | POST multipart | Usuario | Crear solicitud con comprobante |
| `membership_payment_receipt.php?idPago=...` | GET | Propietario/Admin | Mostrar el comprobante |
| `admin_validate_access.php` | GET | Admin | Validar el acceso antes de cargar `mainAdmin` |
| `admin_search_users.php` | POST JSON | Admin | Buscar por correo o `nya` |
| `admin_get_user.php` | POST JSON | Admin | Perfil administrativo completo |
| `admin_list_pending_payments.php` | GET | Admin | Bandeja paginada de pendientes |
| `admin_get_payment.php?idPago=...` | GET | Admin | Datos completos de un pago |
| `admin_review_payment.php` | POST JSON | Admin | Aprobar o rechazar |
| `admin_update_membership.php` | POST JSON | Admin | Dar prueba inicial o desactivar Premium |

## Contratos principales

### Buscar usuarios

`POST admin_search_users.php`

```json
{"query":"Luis", "limit":25}
```

La misma consulta puede ser un fragmento del correo.

### Ver un usuario

`POST admin_get_user.php`

```json
{"idUsuario":15}
```

Devuelve `profile`, `membership`, `companies` y `payments`. “Todo” excluye deliberadamente `logdata.Clave`, tokens y metadatos internos de autenticación.

### Activar la prueba o desactivar Premium

`POST admin_update_membership.php`

```json
{
  "idUsuario":15,
  "premium":true,
  "confirmed":true,
  "observacion":"Prueba gratuita inicial"
}
```

La pantalla de la fase 3 debe mostrar antes el texto exacto: **“¿SEGURO QUE QUIERE ACTUALIZAR LA MEMBRESÍA DEL USUARIO?”**. Además del diálogo, el backend exige `confirmed=true`.

Si `premium=true` y el usuario ya tuvo Premium, el servicio responde HTTP 409 con `code=PAYMENT_REQUIRED`. Para desactivar se envía `premium=false`.

### Enviar un pago

`POST submit_membership_payment.php` como `multipart/form-data`:

| Campo | Requerido | Ejemplo |
|---|---:|---|
| `fechaPago` | Sí | `2026-09-09 08:30:00` |
| `zonaHorariaPago` | No | `America/Caracas` |
| `numeroReferencia` | Sí | `00123456` |
| `montoPago` | Sí | `73.50` |
| `moneda` | Sí | `VES` o `USDT` |
| `metodoPago` | Sí | `PAGO_MOVIL` o `TRANSFERENCIA` |
| `bancoEmisor` | Sí | `Banco de Venezuela` |
| `numeroCuentaEmisora` | Sí | Número/teléfono de la cuenta emisora |
| `nombreTitular` | Sí | Nombre y apellido del titular |
| `comprobante` | Sí | JPG, PNG o WEBP, máximo 5 MB |
| `tasaCambio` | No | Tasa aplicada al pago |
| `fuenteTasa` | No | `BCV` u otra fuente acordada |
| `fechaTasa` | No | Fecha de la tasa |
| `observacion` | No | Nota del usuario |
| `idempotencyKey` | No | UUID generado por la app para reintentos |

El endpoint devuelve el texto de WhatsApp y, si `MEMBERSHIP_WHATSAPP_NUMBER` está configurado, un enlace `wa.me`. Un enlace de WhatsApp no puede adjuntar automáticamente una imagen; en la fase 2 la app debe usar el share sheet si se desea enviar también la captura.

### Revisar un pago

`POST admin_review_payment.php`

```json
{
  "idPago":82,
  "decision":"APROBAR",
  "confirmed":true,
  "observacion":"Referencia verificada"
}
```

Para rechazar, use `decision=RECHAZAR`; en ese caso `observacion` es obligatorio. Aprobar el mismo pago dos veces devuelve conflicto y nunca crea dos períodos.

## Precio y tasas

El precio base queda en `membresia_configuracion.precio_usd = 1.99`, con vigencia y prueba configurables en meses. El esquema conserva monto, moneda, tasa, fuente y fecha usadas en cada solicitud para auditoría.

No se conectó automáticamente una tasa BCV/USDT porque todavía no se indicó el proveedor autorizado ni la regla comercial exacta. En esta fase el monto y la captura quedan pendientes de validación humana, coherente con el maquetado sin pasarela de pago.

## Migración de Premium existentes

Si al ejecutar la migración ya existen usuarios con `num_rol_data=2`, se crea para cada uno un período `MIGRACION` por tres meses desde ese momento. Así no quedan marcados luego como elegibles para una primera prueba. Si el negocio necesita conservar otra fecha de vencimiento, edite ese bloque antes de ejecutar la migración.

## Orden de prueba recomendado

1. Crear o confirmar un usuario con rol 3 directamente en la base de datos.
2. Iniciar sesión como admin y guardar el token.
3. Buscar un usuario y consultar su detalle.
4. Activar su primera prueba y verificar que `fechaFin` sea tres meses posterior.
5. Desactivar o esperar el vencimiento; comprobar que una segunda activación manual devuelva `PAYMENT_REQUIRED`.
6. Enviar un pago, verlo en pendientes, abrir su comprobante y aprobarlo.
7. Verificar en una sola consulta que el pago esté en estado 1, exista el historial y `logdata.num_rol_data` sea 2.

## Referencias técnicas

- [Google: validar un ID token en el backend](https://developers.google.com/identity/gsi/web/guides/verify-google-id-token)
- [Google Auth Library oficial para PHP](https://github.com/googleapis/google-auth-library-php)
- [MariaDB: restricciones PRIMARY KEY, FOREIGN KEY, UNIQUE y CHECK](https://mariadb.com/docs/server/reference/sql-statements/data-definition/constraint)
