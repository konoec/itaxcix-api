# 🎨 Diagrama de Arquitectura y Patrones - PanelWeb

## 🏗️ Vista General con Patrones Principales

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                           🖥️ PRESENTATION LAYER                                │
│                                                                                 │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐                │
│  │   📄 HTML/CSS   │  │  🎮 Controllers │  │   🖼️ Views      │                │
│  │                 │  │                 │  │  (Templates)    │                │
│  │ ControlAdmision │  │ LoginController │  │ Sidebar/Modal   │                │
│  │ Login/Profile   │  │ UIController    │  │ Tables/Forms    │                │
│  │ Sidebar         │  │ SidebarControl  │  │ DOM Elements    │                │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘                │
│                                  ↑                                             │
│                          🎯 1. MVC PATTERN ◄──────────────────────────────────┤
│                                  ↕️                                            │
└─────────────────────────────────────────────────────────────────────────────────┘
                                  ↕️
┌─────────────────────────────────────────────────────────────────────────────────┐
│                            💼 BUSINESS LAYER                                   │
│                                                                                 │
│  🔧 Services              📦 Models              🛡️ Utils                      │
│  ┌─────────────────────┐ ┌─────────────────────┐ ┌─────────────────────────┐   │
│  │ ConductorService    │ │ 👤 Conductor.js     │ │ 🚪 RouteGuard           │   │
│  │ LoginService        │ │ ┌─────────────────┐ │ │ 🔍 AuthChecker          │   │
│  │ ProfileService      │ │ │ fromApiData()   │ │ │ 🧹 SessionCleaner       │   │
│  │ PasswordRecovery    │ │ │ getNombre()     │ │ │                         │   │
│  │                     │ │ │ getResumen()    │ │ │ ◄── MODELO PRINCIPAL    │   │
│  │ 📡 API Comm.        │ │ │ isAprobado()    │ │ │                         │   │
│  └─────────────────────┘ │ │ isDatosCompletos│ │ │                         │   │
│           ↑             │ └─────────────────┘ │ └─────────────────────────┘   │
│   🔧 2. SERVICE         │  ◄── CAPA MODELO   │          🛡️ 4. GUARD          │
│      LAYER              │ 🏭 3. FACTORY       │             PATTERN           │
│                         │    PATTERN          │                               │
│                                  ↕️                                            │
└─────────────────────────────────────────────────────────────────────────────────┘
                                  ↕️
┌─────────────────────────────────────────────────────────────────────────────────┐
│                             💾 DATA LAYER                                      │
│                                                                                 │
│  ┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐                │
│  │  🌐 External    │  │  💾 Browser     │  │  🏪 Singleton   │                │
│  │     API         │  │    Storage      │  │    Instances    │                │
│  │                 │  │                 │  │                 │                │
│  │ 149.130.161.148 │  │ sessionStorage  │  │ globalInstances │                │
│  │ /api/v1/...     │  │ localStorage    │  │ Controllers     │                │
│  └─────────────────┘  └─────────────────┘  └─────────────────┘                │
│                                                     ↑                          │
│                                          🏠 5. SINGLETON                       │
│                                             PATTERN                            │
└─────────────────────────────────────────────────────────────────────────────────┘
```

---

## 🎯 Los 5 Patrones Principales

### 🎯 **1. MVC (Model-View-Controller)** 
- **Ubicación**: Presentation Layer
- **Archivos**: Controllers, HTML/CSS, Models
- **Propósito**: Separación de responsabilidades entre presentación, lógica y datos

### 🔧 **2. Service Layer Pattern**
- **Ubicación**: Business Layer - Services
- **Archivos**: `/api/services/*.js`
- **Propósito**: Abstrae la comunicación con APIs y encapsula lógica de negocio

### 🏭 **3. Factory Pattern**
- **Ubicación**: Business Layer - Models
- **Archivos**: `conductor.js`, `user.js`
- **Propósito**: Crea objetos de dominio a partir de datos de API

### 🛡️ **4. Guard Pattern**
- **Ubicación**: Business Layer - Utils
- **Archivos**: `route-guard.js`, `auth-checker.js`
- **Propósito**: Protege rutas y valida autenticación

### 🏠 **5. Singleton Pattern**
- **Ubicación**: Data Layer - Global Instances
- **Archivos**: `app-initializer.js`, `window.globalInstances`
- **Propósito**: Garantiza una sola instancia de controladores críticos

---

## 🔄 Flujo de Datos Simplificado

```
👤 User ──► 🖥️ View ──► 🎮 Controller ──► 🔧 Service ──► 🌐 API
                                             │
                                             ▼
👁️ View ◄── 🎮 Controller ◄── 📦 Model ◄── 🏭 Factory
```

**Protegido por**: 🛡️ Guard Pattern en cada paso
**Gestionado por**: 🏠 Singleton Pattern para instancias únicas

---

## 📁 Archivos Clave por Patrón

```
🏗️ MVC:
├── Controllers: /ui/controllers/*.js
├── Views: *.html, *.css
└── Models: /api/models/*.js

🔧 Service Layer:
└── Services: /api/services/*.js

🏭 Factory:
└── Models: conductor.js (fromApiData method)

🛡️ Guard:
├── route-guard.js
└── auth-checker.js

🏠 Singleton:
├── app-initializer.js
└── Global instances management
```

---

*Diagrama de Arquitectura Simplificado - PanelWeb 2025* 🎯

