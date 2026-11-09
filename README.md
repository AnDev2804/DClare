# 🛡️ Dclare — TaxTrack & Fiscal Shield

<p align="center">
  <img src="https://img.shields.io/badge/Platform-Android_Native-3DDC84?style=for-the-badge&logo=android&logoColor=white" alt="Platform Android"/>
  <img src="https://img.shields.io/badge/Kotlin-2.1.0-7F52FF?style=for-the-badge&logo=kotlin&logoColor=white" alt="Kotlin"/>
  <img src="https://img.shields.io/badge/UI-Jetpack_Compose_Material_3-4285F4?style=for-the-badge&logo=jetpackcompose&logoColor=white" alt="Jetpack Compose"/>
  <img src="https://img.shields.io/badge/Architecture-Clean_Architecture_+_MVVM_+_UDF-009688?style=for-the-badge" alt="Architecture"/>
  <img src="https://img.shields.io/badge/Networking-Retrofit_+_OkHttp_+_Kotlinx_Serialization-F59E0B?style=for-the-badge" alt="Networking"/>
  <img src="https://img.shields.io/badge/Build-Gradle_9_+_AGP_9-02303A?style=for-the-badge&logo=gradle&logoColor=white" alt="Gradle"/>
</p>

---

## 🌟 Resumen del Proyecto / Executive Overview

**Dclare** es una solución móvil fintech nativa para Android diseñada para **profesionales independientes, contadores y contribuyentes**. La aplicación resuelve la complejidad de los calendarios tributarios multifrecuencia (como el régimen oficial del SENIAT con 13 obligaciones periódicas), calculando de forma reactiva las fechas límites según el dígito terminal del RIF/Tax ID y cotizando estimaciones impositivas en divisas en tiempo real (USD, EUR, VES).

Este repositorio está concebido con estándares de **Senior Android Engineer**, demostrando patrones modernos de arquitectura reactiva, flujo unidireccional de datos (UDF), componentes visuales personalizados en Canvas y compatibilidad dual (Mock Data determinista para evaluación inmediata + API REST en PHP lista para despliegue).

---

## 📐 Arquitectura del Sistema (Clean Architecture + MVVM + UDF)

```
com.andev.dclare
├── data
│   ├── api          # Retrofit interfaces (Frankfurter API + Dclare Backend)
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

## 🚀 Características Principales

### 1. ⏱️ Hero Card de Próximo Vencimiento y Cuenta Regresiva
- Identifica instantáneamente el impuesto más próximo a vencer para la entidad activa.
- Píldora de urgencia con cambio de color semántico:
  - 🔴 **Crítico** (≤ 3 días restantes o ¡Vence Hoy!)
  - 🟡 **Próximo** (≤ 7 días)
  - 🟢 **En Plazo** (> 7 días)
- Cálculo dual de estimación fiscal en USD y moneda local convertida en tiempo real.

### 2. 📊 Gráfico Donut Financiero Personalizado (`Custom Compose Canvas`)
- Gráfico circular reactivo de distribución impositiva dibujado a bajo nivel con `androidx.compose.foundation.Canvas`.
- Animación de entrada con interpolación suave (`FastOutSlowInEasing`).
- Proporciones dinámicas según los tributos activos de cada empresa (% IVA, % ISLR, % IGTF, % Pensiones).

### 3. 📅 Calendario Fiscal Interactivo (Matriz 2026 de 13 Obligaciones)
- Selector de terminales del 0 al 9:
  - Terminales **0, 1 y 2** habilitados en el plan gratuito (*Free Tier*).
  - Terminales **3 al 9** protegidos bajo *Fiscal Shield Pro*.
- Grilla mensual con marcadores circulares en las fechas de corte.
- Al pulsar un día, un panel contextual lista todas las obligaciones que vencen en dicha fecha.

### 4. 💱 Ticker de Divisas en Tiempo Real (Public API)
- Consumo de **Frankfurter API** mediante Retrofit + OkHttp.
- Caché local reactivo con degradación suave (*graceful fallback* offline).
- Conversión instantánea de bases imponibles entre USD, EUR y VES.

### 5. 🏢 Gestión Multiempresa y Configuración de Tributos
- Soporte para contribuyentes Ordinarios y Especiales.
- Catálogo completo de las 13 obligaciones del SENIAT con interruptores (*switches*) en tiempo real:
  - `ANTI_ISLR`, `DD_ISLR`, `DEC_AUTO_ENTDES`, `DEC_DEF_ISLREI`, `DEC_GRA_PATRI`, `DEC_IGTF`, `DEC_IVA`, `DEC_PROT_PEN`, `ENVITE_AZAR`, `EST_ISLR_RENT`, `RET_ISLR_LOT`, `RET_ISLR_RENT`, `RET_IVA`.

### 6. 🛡️ Fiscal Shield & Modelo de Membresías
- Control de cuotas de empresas activas y terminales accesibles.
- Flujo de simulación de ascenso a Pro para pruebas de portafolio sin requerir pasarelas de pago reales.

---

## 💻 Tech Stack & Librerías

| Componente | Tecnología |
|---|---|
| **Lenguaje** | Kotlin 2.1.0 (SDK 35, Android 15+) |
| **UI Toolkit** | 100% Jetpack Compose (BOM 2024.11.00) + Material 3 |
| **Navegación** | Navigation Compose (`androidx.navigation:navigation-compose:2.8.4`) |
| **Concurrencia** | Kotlin Coroutines + StateFlow / SharedFlow (UDF) |
| **Networking** | Retrofit 2.11.0 + OkHttp 4.12.0 + HttpLoggingInterceptor |
| **Serialización** | Kotlinx Serialization JSON 1.7.3 |
| **Build Tools** | Android Gradle Plugin 9.0.1 + Gradle 9.1.0 + JDK 21 |
| **Backend API** | PHP 8.1+ REST API (incluida en `/API_PHP`) con MySQL / MariaDB |

---

## ⚡ Acceso Rápido para Evaluadores (Portfolio Mode)

Para facilitar la revisión por parte de reclutadores y contratistas:
1. Al abrir la app, puedes ingresar directamente con el botón **"⚡ Acceso Rápido Demo (Evaluador GitHub)"**.
2. La app viene precargada con datos ficticios realistas (perfiles como `TAX-00123-X`, empresas ordinarias y especiales, y fechas del calendario 2026).
3. **No requiere configurar bases de datos ni servidores externos para funcionar**.

---

## 🖥️ Backend en PHP (`/API_PHP`)

El repositorio incluye la API REST en PHP que respalda la versión web/móvil:
- Ubicación: carpeta [`/API_PHP`](API_PHP/)
- Configuración segura: [`API_PHP/config.example.php`](API_PHP/config.example.php) sin claves reales expuestas.
- Esquema de base de datos listo para importar: `actualizar_calendario_seniat_2026.sql`.
- Instrucciones de despliegue local con `php -S 127.0.0.1:8000` en [`API_PHP/README.md`](API_PHP/README.md).

---

## 🛠️ Cómo Compilar y Ejecutar

1. Clona el repositorio:
   ```bash
   git clone https://github.com/tu-usuario/Dclare.git
   ```
2. Abre la carpeta raíz en **Android Studio Ladybug (o superior)** con soporte para JDK 21 y AGP 9.
3. Permite la sincronización de Gradle:
   ```bash
   ./gradlew assembleDebug
   ```
4. Ejecuta en tu emulador o dispositivo físico Android (API 24+).
