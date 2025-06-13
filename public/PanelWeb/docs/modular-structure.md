# Estructura Modular del Sistema iTaxCix Panel

## Descripción General
### 5. `emergency-config-controller.js` - Controlador de Configuración de Emergencia
**Ubicación:** `assets/js/ui/controllers/emergency-config-controller.js`
**Propósito:** Controlador específico para la página de configuración de emergencia.

### 6. `control-admision-controller.js` - Controlador de Control de Admisión
**Ubicación:** `assets/js/ui/controllers/control-admision-controller.js`
**Propósito:** Controlador específico para la página de control de admisión de conductores.

## Inicializadores Específicos

### 1. `app-initializer.js` - Inicializador Base
**Ubicación:** `assets/js/ui/controllers/app-initializer.js`
**Propósito:** Inicializador genérico que solo carga controladores base comunes (sidebar, topbar, profile).

### 2. `emergency-config-initializer.js` - Inicializador de Configuración de Emergencia
**Ubicación:** `assets/js/ui/controllers/emergency-config-initializer.js`
**Propósito:** Inicializador específico para la página de configuración de emergencia.

### 3. `control-admision-initializer.js` - Inicializador de Control de Admisión
**Ubicación:** `assets/js/ui/controllers/control-admision-initializer.js`
**Propósito:** Inicializador específico para la página de control de admisión de conductores.ste documento describe la estructura modular del sistema iTaxCix Panel, que permite reutilizar estilos y funcionalidades en todas las páginas del sistema de manera consistente.

## Archivos CSS Modulares

### 1. `base.css` - Estilos Base Reutilizables
**Ubicación:** `assets/css/base.css`
**Propósito:** Contiene todos los estilos generales y reutilizables para el sistema.

**Incluye:**
- Reset CSS y estilos generales del body
- Layout principal (sidebar + main-content)
- Estilos para botones globales
- Estilos para tablas generales
- Estilos para avatares
- Estilos para modales básicos
- Grid system
- Loading components
- Toast notifications
- Responsive design

### 2. `sidebar.css` - Barra Lateral
**Ubicación:** `assets/css/sidebar.css`
**Propósito:** Estilos específicos para la barra lateral de navegación.

### 3. `topbar.css` - Barra Superior
**Ubicación:** `assets/css/topbar.css`
**Propósito:** Estilos específicos para la barra superior con información del usuario.

### 4. `profile.css` - Perfil de Usuario
**Ubicación:** `assets/css/profile.css`
**Propósito:** Estilos para el modal y funcionalidades del perfil de usuario.

### 5. `confirmation-modal.css` - Modales de Confirmación
**Ubicación:** `assets/css/confirmation-modal.css`
**Propósito:** Estilos específicos para modales de confirmación de acciones.

### 6. `drivers-table.css` - Tabla de Conductores
**Ubicación:** `assets/css/drivers-table.css`
**Propósito:** Estilos específicos para la tabla de conductores y sus acciones.

### 7. `emergency-config.css` - Configuración de Emergencia
**Ubicación:** `assets/css/emergency-config.css`
**Propósito:** Estilos específicos para la página de configuración de emergencia.

## Archivos JavaScript Modulares

### 1. `app-initializer.js` - Inicializador Base
**Ubicación:** `assets/js/ui/controllers/app-initializer.js`
**Propósito:** Inicializa solo los controladores base comunes (sidebar, topbar, profile). Se recomienda usar inicializadores específicos por página.

### 2. `emergency-config-initializer.js` - Inicializador de Configuración de Emergencia
**Ubicación:** `assets/js/ui/controllers/emergency-config-initializer.js`
**Propósito:** Inicializador específico para la página de configuración de emergencia.

### 3. `control-admision-initializer.js` - Inicializador de Control de Admisión
**Ubicación:** `assets/js/ui/controllers/control-admision-initializer.js`
**Propósito:** Inicializador específico para la página de control de admisión de conductores.

### 4. `sidebar-controller.js` - Controlador del Sidebar
**Ubicación:** `assets/js/ui/controllers/sidebar-controller.js`
**Propósito:** Maneja la funcionalidad del sidebar (colapsar, navegación, etc.).

### 5. `topbar-controller.js` - Controlador de la Barra Superior
**Ubicación:** `assets/js/ui/controllers/topbar-controller.js`
**Propósito:** Maneja la funcionalidad de la barra superior.

### 6. `profile-controller.js` - Controlador del Perfil
**Ubicación:** `assets/js/ui/controllers/profile-controller.js`
**Propósito:** Maneja la funcionalidad del perfil de usuario.

### 7. `emergency-config-controller.js` - Controlador de Configuración de Emergencia
**Ubicación:** `assets/js/ui/controllers/emergency-config-controller.js`
**Propósito:** Controlador específico para la página de configuración de emergencia.

## Cómo Crear una Nueva Página

### 1. Estructura HTML Base
```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Configuración de caché -->
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <title>Panel de Administración - [Nombre de la Página]</title>
    
    <!-- CSS OBLIGATORIO para todas las páginas -->
    <link rel="stylesheet" href="../../assets/css/base.css">
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/topbar.css">
    <link rel="stylesheet" href="../../assets/css/profile.css">
      <!-- CSS OPCIONAL según funcionalidad -->
    <link rel="stylesheet" href="../../assets/css/confirmation-modal.css">
    <link rel="stylesheet" href="../../assets/css/drivers-table.css">
    <link rel="stylesheet" href="../../assets/css/emergency-config.css">
    <link rel="stylesheet" href="../../assets/css/[nombre-pagina].css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="layout">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <!-- Contenido del sidebar aquí -->
        </div>
        
        <!-- Contenido principal -->
        <main class="main-content">
            <!-- Topbar -->
            <header class="topbar">
                <!-- Contenido del topbar aquí -->
            </header>
            
            <!-- Contenido de la página -->
            <div class="content">
                <!-- Tu contenido específico aquí -->
            </div>
        </main>
    </div>
    
    <!-- JavaScript OBLIGATORIO -->
    <script src="../../assets/js/utils/auth-checker.js"></script>
    <script src="../../assets/js/utils/route-guard.js"></script>
    <script src="../../assets/js/api/services/profile-service.js"></script>
    <script src="../../assets/js/ui/controllers/sidebar-controller.js"></script>
    <script src="../../assets/js/ui/controllers/topbar-controller.js"></script>
    <script src="../../assets/js/ui/controllers/profile-controller.js"></script>
      <!-- JavaScript ESPECÍFICO de la página -->
    <script src="../../assets/js/ui/controllers/[nombre-pagina]-controller.js"></script>
    
    <!-- Inicializador específico de la página (recomendado) -->
    <script src="../../assets/js/ui/controllers/[nombre-pagina]-initializer.js"></script>
    
    <!-- O inicializador base genérico (solo si no hay inicializador específico) -->
    <!-- <script src="../../assets/js/ui/controllers/app-initializer.js"></script> -->
    <script src="../../assets/js/utils/session-cleaner.js"></script>
</body>
</html>
```

### 2. Crear Controlador Específico de la Página
```javascript
/**
 * Controlador UI para [Nombre de la Página]
 */
class [NombrePagina]Controller {
    constructor() {
        this.isInitialized = false;
        this.init();
    }

    async init() {
        console.log('🎯 Inicializando [NombrePagina]Controller...');
        
        try {
            this.findDOMElements();
            this.setupEventListeners();
            
            this.isInitialized = true;
            console.log('✅ [NombrePagina]Controller inicializado correctamente');
        } catch (error) {
            console.error('❌ Error al inicializar [NombrePagina]Controller:', error);
        }
    }

    findDOMElements() {
        // Buscar elementos específicos del DOM
    }

    setupEventListeners() {
        // Configurar eventos específicos
    }
}

// Auto-inicialización si la página está lista
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.[nombrePagina]ControllerInstance = new [NombrePagina]Controller();
    });
} else {
    window.[nombrePagina]ControllerInstance = new [NombrePagina]Controller();
}
```

### 3. Crear Inicializador Específico de la Página (Recomendado)
```javascript
/**
 * Inicializador específico para [Nombre de la Página]
 */
class [NombrePagina]Initializer {
    static async init() {
        console.log('🎯 Inicializando página de [Nombre de la Página]...');
        
        if (authChecker.checkAuthentication()) {
            authChecker.updateUserDisplay();
            authChecker.setupLogoutButton();
            
            // Inicializar controladores base necesarios
            if (!window.sidebarControllerInstance) {
                window.sidebarControllerInstance = new SidebarController();
                console.log('📁 SidebarController inicializado');
            }

            if (!window.topBarControllerInstance) {
                window.topBarControllerInstance = new TopBarController();
                console.log('📊 TopBarController inicializado');
            }

            if (!window.profileControllerInstance) {
                window.profileControllerInstance = new ProfileController();
                console.log('👤 ProfileController inicializado');
            }

            // Inicializar controlador específico de la página
            if (!window.[nombrePagina]ControllerInstance) {
                window.[nombrePagina]ControllerInstance = new [NombrePagina]Controller();
                console.log('🎯 [NombrePagina]Controller inicializado');
            }

            // Configurar verificación de sesión
            setInterval(authChecker.checkTokenExpiration, 60000);
            
            console.log('✅ Página de [Nombre de la Página] completamente inicializada');
        } else {
            console.log('❌ Usuario no autenticado, redirigiendo...');
        }
    }
}

// Auto-inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', [NombrePagina]Initializer.init);
```

## Clases CSS Reutilizables Importantes

### Botones
- `.btn` - Botón base
- `.btn-primary` - Botón primario
- `.btn-secondary` - Botón secundario
- `.btn-success` - Botón de éxito
- `.btn-danger` - Botón de peligro
- `.btn-warning` - Botón de advertencia

### Layout
- `.layout` - Contenedor principal
- `.main-content` - Contenido principal
- `.content` - Área de contenido
- `.sidebar` - Barra lateral
- `.topbar` - Barra superior

### Tablas
- `.table` - Tabla base
- `.table-container` - Contenedor de tabla
- `.actions` - Contenedor de acciones

### Modales
- `.modal` - Modal base
- `.modal-content` - Contenido del modal
- `.confirmation-modal` - Modal de confirmación específico

### Utilidades
- `.loading` - Indicador de carga
- `.toast` - Notificación toast
- `.grid` - Sistema de grid
- `.avatar` - Avatar de usuario

## Consideraciones Importantes

1. **Orden de Importación:** Siempre importar `base.css` primero, luego los demás CSS modulares.

2. **JavaScript:** Los controladores deben cargarse antes del `app-initializer.js`.

3. **Dependencias:** Verificar que todas las dependencias (Font Awesome, servicios, etc.) estén incluidas.

4. **Responsive:** Los estilos ya incluyen responsive design, pero verificar en casos específicos.

5. **Consistencia:** Usar las clases predefinidas en lugar de crear nuevos estilos cuando sea posible.

## Ejemplos de Páginas Implementadas

- **Control de Admisión:** `pages/Admision/ControlAdmisionConductores.html`
  - Controlador: `control-admision-controller.js`
  - Inicializador: `control-admision-initializer.js`
  - CSS específico: `drivers-table.css`

- **Configuración de Emergencia:** `pages/Configuracion/Emergency-config.html`
  - Controlador: `emergency-config-controller.js`
  - Inicializador: `emergency-config-initializer.js`
  - CSS específico: `emergency-config.css`

## Mantenimiento

Para mantener la consistencia:

1. Nuevos estilos globales van en `base.css`
2. Estilos específicos de componentes van en archivos modulares
3. Nunca duplicar estilos entre archivos
4. Testear en todas las páginas cuando se modifiquen estilos base
5. Documentar nuevos componentes en este archivo
6. Cada página debe tener su propio controlador específico
7. Usar inicializadores específicos para cada página
8. No reutilizar controladores entre páginas diferentes

## Archivos Obsoletos (NO USAR)

⚠️ **IMPORTANTE:** Los siguientes archivos han sido refactorizados y NO deben usarse:

- ~~`ui-controller.js`~~ → Reemplazado por `control-admision-controller.js`
- ~~Estilos CSS inline en HTML~~ → Usar archivos CSS específicos

## Migración de Páginas Existentes

Si encuentras páginas que aún usan archivos obsoletos:

1. Crear un controlador específico basado en la funcionalidad de la página
2. Crear un inicializador específico para esa página
3. Extraer estilos CSS inline a un archivo CSS específico
4. Actualizar las referencias en el HTML
5. Testear la funcionalidad
