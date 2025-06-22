# Sistema de Permisos Dinámico v2.0 - Robusto y Consistente

Sistema completo de permisos basado en API para el panel administrativo web. Esta versión mejorada **deshabilita** (en lugar de ocultar) los elementos del menú para proporcionar una mejor experiencia de usuario manteniendo la consistencia visual.

## 🎯 Características Principales

- **Elementos deshabilitados**: Los módulos sin permisos se mantienen visibles pero deshabilitados
- **Feedback visual rico**: Iconos de candado, tooltips informativos y animaciones sutiles
- **Protección de rutas**: Validación automática de acceso a páginas específicas
- **Redirección inteligente**: Navegación automática al primer módulo disponible
- **Debug y testing**: Herramientas de desarrollo para simular permisos
- **Totalmente responsive**: Funciona correctamente en todos los dispositivos
- **Accesible**: Soporte para lectores de pantalla y alto contraste

## 📁 Estructura de Archivos

```
assets/
├── js/
│   └── auth/
│       ├── permissions.js          # Servicio principal de permisos
│       └── route-guard.js         # Protección de rutas
├── css/
│   └── permissions.css            # Estilos para elementos deshabilitados
└── debug/
    └── test-permissions.js        # Herramientas de testing (desarrollo)
```

## 🔧 Configuración

### 1. Mapeo de Permisos

Los permisos se mapean en `permissions.js`:

```javascript
this.modulePermissions = {
    'ADMISIÓN DE CONDUCTORES': {
        permission: 'ADMISIÓN DE CONDUCTORES',
        route: '/pages/Admision/ControlAdmisionConductores.html',
        menuId: 'menu-admision',
        title: 'Admisión de Conductores',
        icon: 'fas fa-user-plus'
    },
    // ... más módulos
};
```

### 2. IDs de Menú

Cada elemento del menú debe tener un ID único:

```html
<li id="menu-admision">
    <a href="../Admision/ControlAdmisionConductores.html">
        <i class="fas fa-user-plus"></i>
        <span>Admisión de Conductores</span>
    </a>
</li>
```

### 3. Inclusión de Archivos

En cada página HTML:

```html
<!-- CSS de permisos -->
<link rel="stylesheet" href="../../assets/css/permissions.css">

<!-- Scripts de permisos -->
<script src="../../assets/js/auth/permissions.js"></script>
<script src="../../assets/js/auth/route-guard.js"></script>

<!-- Configuración automática del menú -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.PermissionsService) {
        window.PermissionsService.configureMenuPermissions();
    }
});
</script>
```

## 🎨 Estados Visuales

### Elemento Habilitado
- **Apariencia**: Normal, completamente funcional
- **Interacción**: Click funciona normalmente
- **Indicadores**: Ninguno

### Elemento Deshabilitado
- **Apariencia**: Opacidad reducida (50%), color gris
- **Interacción**: Click bloqueado, cursor "not-allowed"
- **Indicadores**: 
  - Icono de candado 🔒
  - Tooltip informativo al hacer click
  - Animación sutil del candado

## 🛠️ API del PermissionsService

### Métodos Principales

#### `configureMenuPermissions()`
Configura el estado de todos los elementos del menú según los permisos del usuario.

```javascript
// Configurar menú automáticamente
window.PermissionsService.configureMenuPermissions();
```

#### `hasPermission(permission)`
Verifica si el usuario tiene un permiso específico.

```javascript
const hasAccess = window.PermissionsService.hasPermission('ADMISIÓN DE CONDUCTORES');
```

#### `getAvailableModules()`
Obtiene todos los módulos a los que el usuario tiene acceso.

```javascript
const modules = window.PermissionsService.getAvailableModules();
```

#### `getFirstAvailableRoute()`
Obtiene la ruta del primer módulo disponible para redirección.

```javascript
const route = window.PermissionsService.getFirstAvailableRoute();
```

### Métodos de Control de Elementos

#### `enableMenuItem(menuElement, moduleInfo)`
Habilita un elemento del menú específico.

#### `disableMenuItem(menuElement, moduleInfo)`
Deshabilita un elemento del menú específico.

#### `showPermissionTooltip(element, moduleInfo)`
Muestra un tooltip informativo sobre restricciones de permisos.

## 🔍 Debugging y Testing

### Simular Permisos (Desarrollo)

```javascript
// Simular diferentes conjuntos de permisos
window.PermissionsService.setTestPermissions(['ADMISIÓN DE CONDUCTORES']);
window.PermissionsService.setTestPermissions(['TABLAS MAESTRAS', 'CONFIGURACIÓN']);
window.PermissionsService.setTestPermissions([]); // Sin permisos

// Reconfigurar menú después del cambio
window.PermissionsService.configureMenuPermissions();
```

### Debug de Estado

```javascript
// Información detallada del estado actual
window.PermissionsService.debugCurrentState();

// Información básica de permisos
window.PermissionsService.debugPermissions();
```

### Herramientas de Testing

Incluir el script de testing para desarrollo:

```html
<!-- Solo en desarrollo -->
<script src="../../assets/js/debug/test-permissions.js"></script>
```

## 📱 Responsive y Accesibilidad

### Responsive Design
- **Móvil**: Tooltips adaptativos, iconos más pequeños
- **Tablet**: Tamaños intermedios
- **Desktop**: Experiencia completa

### Accesibilidad
- **Alto contraste**: Estilos específicos para usuarios con necesidades visuales
- **Lectores de pantalla**: Atributos ARIA apropiados
- **Teclado**: Navegación completa por teclado
- **Modo oscuro**: Soporte automático

## 🎭 Temas y Personalización

### CSS Custom Properties

```css
:root {
    --permission-disabled-opacity: 0.5;
    --permission-lock-color: #dc3545;
    --permission-tooltip-bg: rgba(33, 37, 41, 0.95);
}
```

### Clases CSS Principales

- `.permission-disabled`: Elemento deshabilitado
- `.permission-indicator`: Contenedor del icono de candado
- `.permission-tooltip`: Tooltip informativo
- `.debug-permissions`: Modo debug visual

## 🚀 Implementación en Nuevas Páginas

### Pasos Rápidos

1. **Agregar IDs únicos** a elementos del menú
2. **Incluir CSS y JS** de permisos
3. **Configurar inicialización** automática
4. **Probar** con diferentes permisos

### Ejemplo Completo

```html
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../../assets/css/permissions.css">
</head>
<body>
    <!-- Menú con IDs -->
    <nav>
        <ul>
            <li id="menu-admision">
                <a href="admision.html">Admisión</a>
            </li>
            <li id="menu-tablas">
                <a href="tablas.html">Tablas Maestras</a>
            </li>
        </ul>
    </nav>

    <!-- Scripts -->
    <script src="../../assets/js/auth/permissions.js"></script>
    <script src="../../assets/js/auth/route-guard.js"></script>
    
    <!-- Inicialización -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.PermissionsService) {
            window.PermissionsService.configureMenuPermissions();
        }
    });
    </script>
</body>
</html>
```

## 🛡️ Seguridad

- **Solo frontend**: Los permisos se validan también en el backend
- **SessionStorage**: Los permisos se almacenan localmente
- **Validación de rutas**: Protección automática de páginas
- **Limpieza de sesión**: Auto-logout en casos de error

## 🔄 Migración desde v1.0

Si ya tienes implementada la versión anterior:

1. **Actualizar llamadas**: Cambiar `hideUnauthorizedMenuItems()` por `configureMenuPermissions()`
2. **Actualizar CSS**: El nuevo CSS es compatible pero más completo
3. **Probar funcionalidad**: Los elementos ahora se deshabilitan en lugar de ocultarse

```javascript
// Antes (v1.0)
window.PermissionsService.hideUnauthorizedMenuItems();

// Ahora (v2.0)
window.PermissionsService.configureMenuPermissions();
```

## 📞 Soporte

Para problemas o preguntas:
1. Revisar la consola del navegador para logs detallados
2. Usar las herramientas de debug incluidas
3. Verificar que los IDs del menú coincidan con la configuración

---

**Versión**: 2.0  
**Última actualización**: Junio 2025  
**Compatibilidad**: Navegadores modernos (ES6+)
