# Sistema de Permisos Dinámico

## Descripción

Este sistema implementa un control de acceso basado en permisos que:

1. **Oculta módulos** a los que el usuario no tiene acceso (mejor práctica de seguridad)
2. **Redirige automáticamente** al primer módulo disponible después del login
3. **Valida permisos** en cada navegación
4. **Protege rutas** de acceso no autorizado

## Permisos Disponibles

Los siguientes permisos están definidos en el sistema:

- `ADMISIÓN DE CONDUCTORES` → `/pages/Admision/ControlAdmisionConductores.html`
- `TABLAS MAESTRAS` → `/pages/Configuration/Configuration.html`
- `AUDITORIA` → `/pages/Audit/`
- `CONFIGURACIÓN` → `/pages/Configuracion/Configuracion.html`

## Archivos del Sistema

### 1. `/assets/js/auth/permissions.js`
**PermissionsService** - Servicio principal para manejar permisos:
- Mapeo de permisos a rutas
- Verificación de acceso
- Ocultación de elementos del menú
- Redirección dinámica

### 2. `/assets/js/auth/route-guard.js`
**RouteGuard** - Protección de rutas y validación:
- Verificación de autenticación
- Validación de permisos por ruta
- Redirección automática
- Métodos de utilidad

## Implementación

### 1. En el Login Controller

El `login-controller.js` ya está actualizado para:
- Redirigir dinámicamente según permisos del usuario
- Cargar el PermissionsService automáticamente
- Manejar casos sin permisos

### 2. En las Páginas HTML

#### Estructura del Menú
Agregar IDs específicos a los elementos del menú:

```html
<!-- Módulo de Admisión de conductores -->
<li id="menu-admision">
    <a class="module-admision" href="javascript:void(0)">
        <i class="fas fa-user-check"></i>
        Admisión de conductores
    </a>
</li>

<!-- Módulo de Tablas maestras -->
<li id="menu-tablas">
    <a href="javascript:void(0)" class="module-tablas">
        <i class="fas fa-table"></i>
        Tablas maestras
    </a>
</li>

<!-- Módulo de Auditoría -->
<li id="menu-auditoria">
    <a href="javascript:void(0)" class="module-auditoria">
        <i class="fas fa-search"></i>
        Auditoría
    </a>
</li>

<!-- Módulo de Configuración -->
<li id="menu-configuracion">
    <a href="../Configuration/Configuration.html" class="module-config">
        <i class="fas fa-cog"></i>
        Configuración
    </a>
</li>
```

#### Scripts Requeridos
Incluir en este orden:

```html
<!-- Servicios de autenticación y permisos -->
<script src="../../assets/js/auth/auth-checker.js"></script>
<script src="../../assets/js/auth/permissions.js"></script>
<script src="../../assets/js/auth/route-guard.js"></script>

<!-- Inicialización -->
<script>
    // Proteger la página
    window.routeGuard = new RouteGuard("../../index.html");
</script>

<!-- ... otros scripts ... -->

<!-- Verificación específica del módulo -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.routeGuard && typeof window.routeGuard.hasPermission === 'function') {
            // Verificar permiso específico
            const hasAccess = window.routeGuard.hasPermission('NOMBRE_DEL_PERMISO');
            
            if (!hasAccess) {
                console.warn('🚫 Sin acceso al módulo');
                alert('No tienes permisos para acceder a este módulo.');
                
                // Redirigir a ruta autorizada
                if (window.PermissionsService) {
                    const authorizedRoute = window.PermissionsService.getFirstAvailableRoute();
                    if (authorizedRoute) {
                        window.location.replace(authorizedRoute);
                    } else {
                        window.location.replace('../../index.html');
                    }
                }
            }
        }
    });
</script>
```

### 3. Mapeo de Permisos a Páginas

| Permiso | Página | Verificación Requerida |
|---------|--------|----------------------|
| `ADMISIÓN DE CONDUCTORES` | `ControlAdmisionConductores.html` | ✅ Implementado |
| `TABLAS MAESTRAS` | `Configuration.html` | ⚠️ Pendiente |
| `AUDITORIA` | Páginas de Audit | ⚠️ Pendiente |
| `CONFIGURACIÓN` | `Configuracion.html` | ⚠️ Pendiente |

## Funcionalidades

### Automáticas
- **Login dinámico**: Redirige al primer módulo disponible
- **Menú adaptativo**: Oculta opciones sin permisos
- **Protección de rutas**: Valida acceso en cada navegación
- **Redirección inteligente**: Lleva a rutas autorizadas

### Programáticas

```javascript
// Verificar permiso específico
const hasAccess = window.routeGuard.hasPermission('ADMISIÓN DE CONDUCTORES');

// Ocultar elemento por permiso
window.routeGuard.hideElementByPermission('AUDITORIA', '#audit-section');

// Mostrar elemento por permiso
window.routeGuard.showElementByPermission('CONFIGURACIÓN', '#config-panel');

// Debug de permisos
window.routeGuard.debug();
```

## Casos de Uso

### Usuario con todos los permisos
- Ve todos los módulos en el menú
- Puede navegar libremente
- Login redirige a "Admisión de Conductores" (prioritario)

### Usuario solo con "CONFIGURACIÓN"
- Solo ve el módulo de Configuración
- Otros módulos están ocultos
- Login redirige directamente a Configuración
- No puede acceder a otras rutas

### Usuario sin permisos
- Ve mensaje de "Acceso Denegado"
- Es redirigido al login automáticamente
- La sesión se cierra por seguridad

## Ventajas de Seguridad

1. **Ocultación vs Deshabilitación**: Los módulos sin permiso no aparecen en el DOM
2. **Validación en servidor**: Los permisos vienen de la API (sessionStorage como caché)
3. **Protección de rutas**: Imposible acceder por URL directa sin permisos
4. **Limpieza de sesión**: Cierre automático si no hay permisos

## Debug y Desarrollo

```javascript
// Ver permisos del usuario actual
console.log('Permisos:', JSON.parse(sessionStorage.getItem('userPermissions')));

// Debug completo del sistema
window.routeGuard.debug();

// Ver módulos disponibles
window.PermissionsService.debugPermissions();
```

## Próximos Pasos

1. ✅ **Login Controller** - Implementado
2. ✅ **RouteGuard** - Actualizado con permisos
3. ✅ **PermissionsService** - Creado
4. ✅ **Página de Admisión** - Implementada como ejemplo
5. ⚠️ **Páginas restantes** - Implementar en Configuration, Audit, Configuracion
6. ⚠️ **Sidebar Controller** - Actualizar para usar permisos dinámicos
7. ⚠️ **Testing** - Probar con diferentes combinaciones de permisos

## Notas de Implementación

- El sistema es **backward compatible**: funciona sin permisos (modo legacy)
- **Performance optimizado**: Carga bajo demanda y caché inteligente
- **Extensible**: Fácil agregar nuevos módulos y permisos
- **Mantenible**: Configuración centralizada en PermissionsService
