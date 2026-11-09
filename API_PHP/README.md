# 🛡️ Dclare Backend API (PHP / MySQL)

Servicio RESTful en PHP para la gestión de calendarios fiscales, cálculo de próximas fechas de corte tributario, control de carteras de clientes (RIF / Tax IDs) y motor de membresías por niveles.

---

## 🚀 Pila Tecnológica
- **PHP:** 8.1+ (Modo estricto `declare(strict_types=1);`, tipado estricto)
- **Base de Datos:** MySQL / MariaDB 10.4+ con `mysqli` transaccional
- **Autenticación:** Tokens Bearer de 64 caracteres hex (`auth_sessions` con hash SHA-256)
- **Integraciones:**
  - Cotizaciones cambiarias en tiempo real y caché histórico
  - Generación de comprobantes y auditoría de cambios
  - Notificaciones por correo vía PHPMailer transaccional

---

## 🛠️ Puesta en Marcha Local

### Opción 1: Servidor Integrado de PHP
```bash
cd API_PHP
# Copiar configuración de ejemplo si no existe
cp config.example.php config.php

# Iniciar servidor local
php -S 127.0.0.1:8000
```

### Opción 2: XAMPP / WampServer / Docker
1. Importa el archivo `actualizar_calendario_seniat_2026.sql` y `hgrqrrte_clara_test.sql` en tu base de datos MySQL local (`dclare_db`).
2. Configura los parámetros en `config.php`:
```php
'DB_HOST' => '127.0.0.1',
'DB_PORT' => 3306,
'DB_NAME' => 'dclare_db',
'DB_USER' => 'root',
'DB_PASSWORD' => '',
```

---

## 📡 Endpoints Principales

| Endpoint | Método | Auth | Descripción |
|---|---|---|---|
| `/login.php` | POST | Público | Inicio de sesión con email y contraseña, retorna Bearer token |
| `/register.php` | POST | Público | Registro de nuevos usuarios y envío de código de verificación |
| `/getDeclaracion.php` | POST | Bearer | **Cálculo dinámico inteligente:** Cruza terminales de RIF y preferencias contra el calendario oficial para devolver el próximo vencimiento exacto y días restantes |
| `/getTable.php` | POST | Bearer | Obtiene la matriz completa del calendario fiscal para un dígito terminal (0 al 9) |
| `/getRif.php` | POST | Bearer | Lista de empresas/clientes asignados al usuario en sesión |
| `/regRIF.php` | POST | Bearer | Registro de nueva empresa o cliente tributario |
| `/get_tax_preferences.php` | POST | Bearer | Consulta de los 13 impuestos configurados para una empresa |
| `/update_tax_preference.php`| POST | Bearer | Activa/desactiva una obligación fiscal específica para un cliente |
| `/get_memberships_status.php`| GET | Bearer | Estado de la suscripción (Free Tier vs Fiscal Shield Pro) |

---

## 🔒 Buenas Prácticas de Seguridad
- Contraseñas procesadas exclusivamente con algoritmos `PASSWORD_DEFAULT` (Bcrypt/Argon2).
- Todas las consultas a base de datos utilizan sentencias preparadas (`prepare` + `bind_param`) para mitigar inyecciones SQL.
- Tokens de sesión almacenados en formato hash SHA-256 en base de datos.

