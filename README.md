# iTaxCix API

La API de **iTaxCix** es una solución integral para la formalización y mejora del transporte urbano en Chiclayo. Provee servicios digitales confiables para ciudadanos, conductores y la Municipalidad, abordando la informalidad del sector.

---

## 📖 Descripción General

**iTaxCix API** está en desarrollo activo y ofrece:
- Autenticación y autorización segura (JWT)
- Registro y gestión de usuarios
- Gestión de perfiles y roles
- Disponibilidad y localización de conductores en tiempo real
- Notificaciones por correo y SMS
- Integración con aplicaciones móviles y PanelWeb de administración

Utiliza tecnologías modernas para garantizar escalabilidad, seguridad y facilidad de uso.

---

## 🏗️ Arquitectura y Patrones

El proyecto está dividido en dos grandes bloques: **Backend** (API REST, WebSocket, lógica de dominio) y **PanelWeb** (frontend de administración).

### Arquitectura General del Backend

El backend sigue una arquitectura en capas inspirada en DDD (Domain-Driven Design), separando claramente la lógica de dominio, infraestructura y servicios compartidos:

- **Core/**: Dominios, casos de uso, interfaces y handlers (lógica de negocio)
- **Infrastructure/**: Implementaciones técnicas (base de datos, autenticación, caché, notificaciones, WebSocket, servicios web)
- **Shared/**: DTOs, validadores y utilidades comunes

**Exposición de servicios:**
- **API REST**: Gestión de recursos y operaciones principales
- **WebSocket**: Localización de conductores y notificaciones en tiempo real

**Patrones y buenas prácticas:**
- DDD (Domain-Driven Design)
- Inyección de dependencias (PSR-11)
- DTOs y validadores
- Event-driven y handlers

**Diagrama sugerido:**
- [`docs/DIAGRAMA_ARQUITECTURA_BACKEND.md`](docs/DIAGRAMA_ARQUITECTURA_BACKEND.md)

### Arquitectura del PanelWeb

El frontend de administración está documentado en:
- [`public/PanelWeb/ARQUITECTURA_Y_PATRONES.md`](public/PanelWeb/ARQUITECTURA_Y_PATRONES.md)
- [`public/PanelWeb/DIAGRAMA_ARQUITECTURA.md`](public/PanelWeb/DIAGRAMA_ARQUITECTURA.md)

---

## 🚀 Tecnologías Utilizadas

**Backend:**
- PHP 8.2
- Composer
- Doctrine ORM
- FastRoute
- JWT (firebase/php-jwt)
- PSR-7, PSR-11
- PHPMailer, Vonage API (SMS)
- Ratchet (WebSocket)
- Docker, NGINX

**Base de datos:**
- PostgreSQL 14

**Frontend (PanelWeb):**
- JavaScript vanilla modular
- HTML5, CSS3

---

## 📚 Documentación y Endpoints

- **Swagger/OpenAPI:** Documentación de la API REST
  - Local: [http://localhost/swagger-ui](http://localhost/swagger-ui)
  - Producción: [https://api.itaxcix.com/swagger-ui](https://api.itaxcix.com/swagger-ui)
- **AsyncAPI:** Documentación de WebSocket
  - Local: [http://localhost/asyncapi-docs](http://localhost/asyncapi-docs)

---

## 📂 Estructura Principal del Proyecto

- **src/Core/**: Lógica de dominio, casos de uso, interfaces
- **src/Infrastructure/**: Adaptadores técnicos (DB, WebSocket, Auth, etc.)
- **src/Shared/**: DTOs, validadores, utilidades
- **public/**: Entrypoints, documentación, PanelWeb
- **bin/**: Scripts CLI y servidores
- **config/**: Configuración de contenedores y dependencias
- **database/**: Scripts SQL
- **templates/**: Plantillas de email
- **uploads/**: Archivos subidos
- **docs/**: Documentación técnica (sugerido)

---

## 🤝 Contribuciones

¡Las contribuciones son bienvenidas! Para colaborar:
1. Haz un fork del repositorio
2. Crea una rama: `git checkout -b feature/nueva-funcionalidad`
3. Realiza tus cambios y haz commit
4. Envía un pull request

---

## 📜 Licencia

Este proyecto está bajo la licencia MIT. Consulta el archivo `LICENSE` para más información.

---

## 📬 Contacto

- **Autor:** konoe
- **Email:** soporte@itaxcix.com
- **GitHub:** [konoe](https://github.com/konoe)

---

¡Bienvenido a iTaxCix API!
