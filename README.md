# 🛡️ Dclare — TaxTrack & Fiscal Shield

<p align="center">
  <img src="https://img.shields.io/badge/Platform-Android_Native-3DDC84?style=for-the-badge&logo=android&logoColor=white" alt="Platform Android"/>
  <img src="https://img.shields.io/badge/Kotlin-2.1.0-7F52FF?style=for-the-badge&logo=kotlin&logoColor=white" alt="Kotlin"/>
  <img src="https://img.shields.io/badge/UI-Jetpack_Compose_Material_3-4285F4?style=for-the-badge&logo=jetpackcompose&logoColor=white" alt="Jetpack Compose"/>
  <img src="https://img.shields.io/badge/Architecture-Clean_Architecture_+_MVVM_+_UDF-009688?style=for-the-badge" alt="Architecture"/>
  <img src="https://img.shields.io/badge/Networking-Retrofit_+_OkHttp_+_Kotlinx_Serialization-F59E0B?style=for-the-badge" alt="Networking"/>
  <img src="https://img.shields.io/badge/Build-Gradle_9_+_AGP_9-02303A?style=for-the-badge&logo=gradle&logoColor=white" alt="Gradle"/>
</p>

<p align="center">
  <b>Language / Idioma:</b>
  <a href="#-english-version">English</a> •
  <a href="#-versión-en-español">Español</a>
</p>

---

## 🇺🇸 English Version

### 🌟 Executive Overview
**Dclare** is a modern native Android fintech application built for **independent contractors, accountants, and businesses**. The app tackles the complexity of multi-frequency fiscal calendars (such as the official SENIAT regime with 13 periodic tax obligations), calculating reactive due dates based on taxpayer identification terminal digits (RIF/Tax ID) and providing real-time currency conversions against official benchmark rates (USD, EUR, VES) powered by **DolarApi**.

Engineered to **Senior Android Engineer** standards, it demonstrates reactive architectural patterns, Unidirectional Data Flow (UDF), low-level Custom Compose Canvas charts, and dual compatibility (deterministic mock engine for instant portfolio evaluation + sanitized reference PHP REST API).

---

### 📐 System Architecture (Clean Architecture + MVVM + UDF)

```
com.andev.dclare
├── data
│   ├── api          # Retrofit interfaces (DolarApi Venezuela & Fallback APIs)
│   ├── dto          # Serializable DTOs with Kotlinx Serialization
│   ├── mappers      # Pure mappers: DTO -> Domain Entity -> UI State
│   ├── mock         # Official 2026 tax calendar matrix (13 tables, digits 0-9)
│   └── repository   # Reactive StateFlow (Auth, Exchange Rates, Fiscal Obligations)
├── domain
│   ├── model        # Immutable domain models (FiscalDeadline, TaxObligation, CompanyProfile)
│   └── usecase      # Atomic use cases (GetNextDeadline, ConvertCurrency, ToggleTax)
├── presentation
│   ├── auth         # Login & Register + 1-Click Reviewer Demo Access
│   ├── dashboard    # Hero Countdown, Donut Chart Canvas, Currency Tickers, and Tax Switches
│   ├── calendar     # 7x5 Interactive monthly matrix with terminal digit selector 0..9
│   ├── clients      # Company portfolios (Ordinary/Special) & 13-tax configuration switches
│   ├── shield       # Fiscal Shield Memberships (Free, Pro Shield, Enterprise)
│   ├── common       # Reusable UI components (TopBar, BottomNav, Custom Canvas Donut Chart)
│   ├── navigation   # Navigation Compose with Single Activity
│   └── theme        # Fintech Dark Theme (Slate/Graphite #0B0F17, Emerald #10B981, Amber #F59E0B)
└── di               # Dependency Injection Container (Service Locator Pattern)
```

---

### 🚀 Key Features

#### 1. ⏱️ Next Tax Deadline Hero Card & Dynamic Countdown
- Automatically identifies the most urgent upcoming tax deadline for the active entity.
- Semantic urgency pill badge:
  - 🔴 **Critical** (≤ 3 days left or Due Today!)
  - 🟡 **Upcoming** (≤ 7 days left)
  - 🟢 **On Track** (> 7 days left)
- Real-time dual valuation of estimated fiscal obligations in USD and converted local currency (VES).

#### 2. 📊 Custom Financial Donut Chart (`Jetpack Compose Canvas`)
- Low-level vector rendering using `androidx.compose.foundation.Canvas`.
- Smooth animated arc interpolation (`FastOutSlowInEasing`).
- Dynamic proportions adjusted in real time based on active obligations (% VAT/IVA, % Income Tax/ISLR, % IGTF, % Pension Protection).

#### 3. 📅 Interactive Fiscal Calendar (13-Tax 2026 Matrix)
- Taxpayer Terminal Digit Selector (0 through 9):
  - Digits **0, 1, and 2** unlocked in the Free Tier.
  - Digits **3 through 9** secured under *Fiscal Shield Pro*.
- Monthly grid with circular markers on cut-off dates.
- Tapping any date opens a contextual drawer detailing all obligations due on that day.

#### 4. 💱 Real-Time Currency Exchange Ticker (DolarApi Venezuela)
- Consumes the public API from **[DolarApi Venezuela](https://dolarapi.com/docs/venezuela/)** (`https://ve.dolarapi.com/v1/dolares/oficial`) via Retrofit + OkHttp.
- Displays official Central Bank of Venezuela (BCV) rates with interactive refresh.
- Graceful offline fallback to benchmark cache when no connection is available.

#### 5. 🏢 Multi-Company Portfolio & Tax Configuration
- Supports both Ordinary and Special Taxpayer profiles.
- Full catalog of all 13 SENIAT tax obligations with live toggle switches:
  - `ANTI_ISLR`, `DD_ISLR`, `DEC_AUTO_ENTDES`, `DEC_DEF_ISLREI`, `DEC_GRA_PATRI`, `DEC_IGTF`, `DEC_IVA`, `DEC_PROT_PEN`, `ENVITE_AZAR`, `EST_ISLR_RENT`, `RET_ISLR_LOT`, `RET_ISLR_RENT`, `RET_IVA`.

#### 6. 🛡️ Fiscal Shield & Membership Tiers
- Tier visualizer (Free Tier vs Fiscal Shield Pro at $1.99).
- Quota indicators for registered active companies and accessible terminals.
- Instant simulated upgrade workflow for portfolio testing.

---

### 💻 Tech Stack

| Layer / Component | Technology |
|---|---|
| **Language** | Kotlin 2.1.0 (SDK 35, Android 15+) |
| **UI Toolkit** | 100% Jetpack Compose (BOM 2024.11.00) + Material 3 |
| **Navigation** | Navigation Compose (`androidx.navigation:navigation-compose:2.8.4`) |
| **Concurrency** | Kotlin Coroutines + StateFlow / SharedFlow (UDF) |
| **Networking** | Retrofit 2.11.0 + OkHttp 4.12.0 + HttpLoggingInterceptor |
| **Serialization** | Kotlinx Serialization JSON 1.7.3 |
| **Build Tools** | Android Gradle Plugin 9.0.1 + Gradle 9.1.0 + JDK 21 |
| **Backend API** | PHP 8.1+ REST API (included in `/API_PHP`) with MySQL / MariaDB |

---

### ⚡ 1-Click Reviewer Access (Portfolio Mode)
Recruiters and hiring managers can immediately test the app without setting up backend servers or typing credentials:
1. Tap **"⚡ Acceso Rápido Demo (Evaluador GitHub)"** on the login screen.
2. The app loads with rich fictional datasets (`TAX-00123-X`, company profiles, and 2026 calendar deadlines).

---

### 🛠️ How to Build and Run

1. Clone the repository:
   ```bash
   git clone https://github.com/AnDev2804/DClare.git
   ```
2. Open the project in **Android Studio Ladybug (or newer)** configured with JDK 21.
3. Sync Gradle and run tests or build the APK:
   ```bash
   ./gradlew test
   ./gradlew assembleDebug
   ```

---

## 🇪🇸 Versión en Español

### 🌟 Resumen Ejecutivo
**Dclare** es una solución móvil fintech nativa para Android diseñada para **profesionales independientes, contadores y contribuyentes**. La aplicación resuelve la complejidad de los calendarios tributarios multifrecuencia (como el régimen oficial del SENIAT con 13 obligaciones periódicas), calculando de forma reactiva las fechas límites según el dígito terminal del RIF/Tax ID y cotizando estimaciones impositivas en divisas en tiempo real (USD, EUR, VES) mediante **DolarApi**.

Concebido con estándares de **Senior Android Engineer**, demuestra patrones modernos de arquitectura reactiva, flujo unidireccional de datos (UDF), componentes visuales personalizados en Canvas y compatibilidad dual (Mock Data determinista para evaluación inmediata + API REST en PHP lista para despliegue).

---

### 📐 Arquitectura del Sistema (Clean Architecture + MVVM + UDF)

```
com.andev.dclare
├── data
│   ├── api          # Interfaces Retrofit (DolarApi Venezuela & Fallback APIs)
│   ├── dto          # DTOs serializables con Kotlinx Serialization
│   ├── mappers      # Mappers puros: DTO -> Domain Entity -> UI State
│   ├── mock         # Matriz tributaria 2026 oficial (13 tablas, terminales 0-9)
│   └── repository   # StateFlow reactivo (Auth, Divisas, Obligaciones Fiscales)
├── domain
│   ├── model        # Entidades inmutables de dominio (FiscalDeadline, TaxObligation, CompanyProfile)
│   └── usecase      # Casos de uso atómicos (GetNextDeadline, ConvertCurrency, ToggleTax)
├── presentation
│   ├── auth         # Login & Registro con acceso demo de 1 clic para evaluadores
│   ├── dashboard    # Hero countdown, Donut Chart Canvas, tickers de divisas y toggles
│   ├── calendar     # Matriz mensual interactiva 7x5 con selector de terminales 0..9
│   ├── clients      # Cartera de empresas (Ordinarios/Especiales) y switch de los 13 tributos
│   ├── shield       # Membresías Fiscal Shield (Niveles Free, Pro Shield y Enterprise)
│   ├── common       # Componentes reutilizables (TopBar, BottomNav, Custom Canvas Chart)
│   ├── navigation   # Navigation Compose con Single Activity
│   └── theme        # Fintech Dark Theme (Slate/Graphite #0B0F17, Emerald #10B981, Amber #F59E0B)
└── di               # Contenedor de Inyección de Dependencias (Service Locator)
```

---

### 🚀 Características Principales

#### 1. ⏱️ Hero Card de Próximo Vencimiento y Cuenta Regresiva
- Identifica instantáneamente el impuesto más próximo a vencer para la entidad activa.
- Píldora de urgencia con cambio de color semántico:
  - 🔴 **Crítico** (≤ 3 días restantes o ¡Vence Hoy!)
  - 🟡 **Próximo** (≤ 7 días)
  - 🟢 **En Plazo** (> 7 días)
- Cálculo dual de estimación fiscal en USD y moneda local convertida en tiempo real.

#### 2. 📊 Gráfico Donut Financiero Personalizado (`Custom Compose Canvas`)
- Gráfico circular reactivo de distribución impositiva dibujado a bajo nivel con `androidx.compose.foundation.Canvas`.
- Animación de entrada con interpolación suave (`FastOutSlowInEasing`).
- Proporciones dinámicas según los tributos activos de cada empresa (% IVA, % ISLR, % IGTF, % Pensiones).

#### 3. 📅 Calendario Fiscal Interactivo (Matriz 2026 de 13 Obligaciones)
- Selector de terminales del 0 al 9:
  - Terminales **0, 1 y 2** habilitados en el plan gratuito (*Free Tier*).
  - Terminales **3 al 9** protegidos bajo *Fiscal Shield Pro*.
- Grilla mensual con marcadores circulares en las fechas de corte.
- Al pulsar un día, un panel contextual lista todas las obligaciones que vencen en dicha fecha.

#### 4. 💱 Ticker de Divisas en Tiempo Real (DolarApi Venezuela)
- Consumo directo de la API pública oficial de **[DolarApi Venezuela](https://dolarapi.com/docs/venezuela/)** (`https://ve.dolarapi.com/v1/dolares/oficial`) mediante Retrofit + OkHttp.
- Cotización oficial del BCV en tiempo real con botón de actualización manual y cálculo de bases imponibles en Bolívares (VES).
- Caché local reactivo con degradación suave (*graceful fallback* offline a cotización benchmark).

#### 5. 🏢 Gestión Multiempresa y Configuración de Tributos
- Soporte para contribuyentes Ordinarios y Especiales.
- Catálogo completo de las 13 obligaciones del SENIAT con interruptores (*switches*) en tiempo real:
  - `ANTI_ISLR`, `DD_ISLR`, `DEC_AUTO_ENTDES`, `DEC_DEF_ISLREI`, `DEC_GRA_PATRI`, `DEC_IGTF`, `DEC_IVA`, `DEC_PROT_PEN`, `ENVITE_AZAR`, `EST_ISLR_RENT`, `RET_ISLR_LOT`, `RET_ISLR_RENT`, `RET_IVA`.

#### 6. 🛡️ Fiscal Shield & Modelo de Membresías
- Control de cuotas de empresas activas y terminales accesibles.
- Flujo de simulación de ascenso a Pro para pruebas de portafolio sin requerir pasarelas de pago reales.

---

### 🖥️ Backend en PHP (`/API_PHP`)
El repositorio incluye la API REST en PHP que respalda el sistema:
- Ubicación: carpeta [`/API_PHP`](API_PHP/)
- Configuración segura: [`API_PHP/config.example.php`](API_PHP/config.example.php) sin claves reales expuestas.
- Esquema de base de datos listo para importar: `actualizar_calendario_seniat_2026.sql`.
- Instrucciones de despliegue local con `php -S 127.0.0.1:8000` en [`API_PHP/README.md`](API_PHP/README.md).

---

### 🛠️ Cómo Compilar y Ejecutar

1. Clona el repositorio:
   ```bash
   git clone https://github.com/AnDev2804/DClare.git
   ```
2. Abre la carpeta raíz en **Android Studio Ladybug (o superior)** con soporte para JDK 21 y AGP 9.
3. Permite la sincronización de Gradle:
   ```bash
   ./gradlew test
   ./gradlew assembleDebug
   ```
4. Ejecuta en tu emulador o dispositivo físico Android (API 24+).
