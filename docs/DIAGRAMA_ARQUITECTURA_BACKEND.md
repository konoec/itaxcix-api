# 🏗️ Diagrama de Arquitectura General del Backend - iTaxCix API

Este documento describe la arquitectura global del backend de iTaxCix API, incluyendo la organización de módulos, capas y los principales patrones utilizados.

---

## 📋 Vista General

```
┌──────────────────────────────────────────────────────────────────────────────┐
│                                 CLIENTES                                    │
│ ┌───────────────┐   ┌───────────────┐   ┌───────────────┐                   │
│ │  PanelWeb     │   │  App Móvil    │   │  Otros        │                   │
│ └──────┬────────┘   └──────┬────────┘   └──────┬────────┘                   │
└────────┼───────────────────┼───────────────────┼────────────────────────────┘
         │                   │                   │
         ▼                   ▼                   ▼
┌──────────────────────────────────────────────────────────────────────────────┐
│                             API GATEWAY / NGINX                             │
└──────────────────────────────────────────────────────────────────────────────┘
         │                   │
         │                   │
         ▼                   ▼
┌────────────────────────────┬─────────────────────────────────────────────────┐
│         API REST          │                WebSocket Server                  │
│   (FastRoute, PSR-7/11)   │         (Ratchet, AsyncAPI)                     │
└────────────┬───────────────┴──────────────────────────────┬──────────────────┘
             │                                              │
             ▼                                              ▼
┌──────────────────────────────────────────────────────────────────────────────┐
│                                 CORE                                        │
│  - Domain (Entidades, Agregados, Repositorios, Servicios de Dominio)         │
│  - UseCases (Casos de uso, lógica de aplicación)                             │
│  - Handler (Comandos, eventos, queries)                                      │
│  - Interfaces (Contratos para infraestructura)                               │
└──────────────────────────────────────────────────────────────────────────────┘
             │                                              │
             ▼                                              ▼
┌──────────────────────────────────────────────────────────────────────────────┐
│                             INFRASTRUCTURE                                  │
│  - Database (Doctrine, PostgreSQL)                                          │
│  - Auth (JWT, OAuth)                                                        │
│  - Cache (Redis, etc.)                                                      │
│  - Notifications (Correo, SMS)                                              │
│  - WebSocket (Ratchet)                                                      │
│  - Web (servicios externos, APIs)                                           │
└──────────────────────────────────────────────────────────────────────────────┘
             │
             ▼
┌──────────────────────────────────────────────────────────────────────────────┐
│                                 SHARED                                      │
│  - DTOs, Validadores, Utilidades comunes                                    │
└──────────────────────────────────────────────────────────────────────────────┘
```

---

## 🧩 Patrones y Principios

- **DDD (Domain-Driven Design):** Separación clara entre dominio, casos de uso y adaptadores.
- **Inyección de Dependencias (PSR-11):** Desacoplamiento y testabilidad.
- **Event-driven:** Handlers para comandos, eventos y queries.
- **DTOs y Validadores:** Integridad y seguridad de datos.
- **Infraestructura desacoplada:** Implementaciones técnicas separadas del dominio.

---

## 📂 Estructura de Carpetas

- `src/Core/Domain/`         → Entidades, repositorios, servicios de dominio
- `src/Core/UseCases/`       → Casos de uso
- `src/Core/Handler/`        → Handlers de comandos, eventos, queries
- `src/Core/Interfaces/`     → Contratos para infraestructura
- `src/Infrastructure/`      → Adaptadores técnicos (DB, Auth, Cache, WebSocket, etc.)
- `src/Shared/`              → DTOs, validadores, utilidades
- `public/`                  → Entrypoints, documentación, PanelWeb
- `bin/`                     → Scripts CLI y servidores
- `config/`                  → Configuración de contenedores y dependencias
- `database/`                → Scripts SQL
- `templates/`               → Plantillas de email
- `uploads/`                 → Archivos subidos

---

## 📡 Flujo de Comunicación

1. **Cliente** (PanelWeb, App móvil, etc.) realiza petición HTTP(S) o WebSocket.
2. **API Gateway/NGINX** enruta la petición al servicio correspondiente.
3. **API REST** procesa la petición usando casos de uso y lógica de dominio.
4. **WebSocket Server** gestiona eventos en tiempo real (ej: localización de conductores).
5. **Infraestructura** accede a base de datos, servicios externos, caché, etc.
6. **Respuestas** y eventos se devuelven al cliente.

---

## 📑 Referencias

- [README.md](../README.md)
- [ARQUITECTURA_Y_PATRONES.md (PanelWeb)](../public/PanelWeb/ARQUITECTURA_Y_PATRONES.md)
- [DIAGRAMA_ARQUITECTURA.md (PanelWeb)](../public/PanelWeb/DIAGRAMA_ARQUITECTURA.md)

---

*Este documento puede ser extendido con diagramas visuales (PlantUML, Mermaid, etc.) según necesidad.*

