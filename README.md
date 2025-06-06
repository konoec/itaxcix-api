# iTaxCix API

La API de **iTaxCix** es una solución integral diseñada para formalizar y mejorar los servicios de transporte urbano en Chiclayo. Este proyecto busca resolver la informalidad en el transporte mediante herramientas digitales accesibles y confiables para ciudadanos, conductores y la Municipalidad.

---

## 📌 Descripción del Proyecto

Actualmente, en desarrollo activo, **iTaxCix API** implementa funcionalidades clave como autenticación, registro de usuarios, gestión de perfiles y disponibilidad de conductores. Este proyecto utiliza tecnologías modernas para ofrecer un servicio escalable, seguro y fácil de usar.

---

## 🛠️ Tecnologías Utilizadas

**Backend:**
- **PHP 8.2**: Lenguaje principal del proyecto, con extensiones como `pdo_pgsql` y `opcache` para rendimiento.
- **Composer**: Gestión de dependencias.
- **Doctrine ORM**: Abstracción de base de datos y mapeo objeto-relacional (ORM).
- **FastRoute**: Enrutador rápido y eficiente.
- **Swagger/OpenAPI**: Generación de documentación para los endpoints de la API.
- **JWT (JSON Web Tokens)**: Manejo de autenticación segura.

**Base de Datos:**
- **PostgreSQL 14**: Base de datos relacional utilizada para almacenar la información.

**Infraestructura:**
- **Docker**: Contenerización para facilitar el despliegue y la configuración del entorno.
- **NGINX**: Servidor web para manejar solicitudes HTTP y HTTPS.

**Otros:**
- **PHPMailer**: Envío de correos electrónicos.
- **Vonage API**: Envío de SMS.
- **PSR-7 y PSR-11**: Estándares utilizados para la gestión de peticiones HTTP y contenedores de dependencias.

---

## 🛠️ Estructura del Proyecto

El proyecto está organizado en una estructura modular que facilita el mantenimiento y la escalabilidad. Aquí tienes una descripción general de las carpetas principales:

- **`bin`**: Scripts de configuración y migraciones.
- **`certs`**: Certificados SSL para HTTPS.
- **`database`**: Scripts de migración y datos de prueba.
- **`public`**: Archivos públicos accesibles por el navegador.
- **`src`**: Código fuente principal del proyecto.
    - **`config`**: Configuración global de la API.
    - **`controllers`**: Controladores HTTP.
    - **`middleware`**: Middleware personalizados.
    - **`mocks`**: Datos ficticios para pruebas.
    - **`models`**: DTOs y entidades de negocio.
    - **`repositories`**: Interacción con la base de datos.
    - **`services`**: Lógica de negocio.
    - **`utils`**: Funciones útiles y validaciones.
    - **`validators`**: Validadores de entrada.
- **`templates`**: Plantillas para correos electrónicos.
- **`vendor`**: Dependencias gestionadas por Composer.

---

## 🚀 Endpoints y Documentación

La documentación completa de los endpoints de la API, incluyendo detalles de los parámetros requeridos, respuestas y ejemplos, está disponible en Swagger UI.

Accede a Swagger UI en:
- **Entorno Local:** [http://localhost/swagger-ui](http://localhost/swagger-ui)
- **Producción:** [https://api.itaxcix.com/swagger-ui](https://api.itaxcix.com/swagger-ui)

---

## 📝 Contribuciones

¡Las contribuciones son bienvenidas! Sigue estos pasos para colaborar:

1. Haz un fork del repositorio.
2. Crea una nueva rama:
   ```bash
   git checkout -b feature/nueva-funcionalidad
   ```
3. Realiza tus cambios y crea un commit:
   ```bash
   git commit -m "Añadir nueva funcionalidad"
   ```
4. Envía un pull request.

---

## 📝 Licencia

Este proyecto está bajo la licencia **MIT**. Consulta el archivo `LICENSE` para más información.

---

## 📧 Contacto

- **Autor:** konoec
- **Email:** soporte@itaxcix.com
- **GitHub:** [konoec](https://github.com/konoec)
