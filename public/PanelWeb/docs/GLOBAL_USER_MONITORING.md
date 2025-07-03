# Sistema Global de Monitoreo de Usuario

## Descripción

Este sistema implementa un monitoreo global automático del estado del usuario que funciona en todas las páginas del sistema, permitiendo logout automático cuando el usuario es desactivado o su sesión expira.

## Características

### ✅ Monitoreo Global
- Se ejecuta en **todas** las páginas del sistema
- Verifica el estado del usuario cada 30 segundos
- Funciona independientemente del módulo actual (Inicio, Configuración, Admisión, etc.)

### ✅ Detección Automática
- Detecta usuarios desactivados por administrador
- Detecta tokens expirados o inválidos  
- Detecta cuando el usuario es eliminado del sistema

### ✅ Logout Inteligente
- Muestra mensaje apropiado según el tipo de logout
- Cierra sesión automáticamente
- Redirige al login con la ruta correcta
- Limpia todos los datos de sesión

### ✅ Múltiples Sesiones
- Funciona correctamente con múltiples pestañas/ventanas
- Si se cierra sesión en una pestaña, afecta a todas las demás
- Sincronización mediante eventos de storage

## Implementación

### Archivos Principales

#### `global-initializer.js`
- **GlobalUserMonitor**: Clase principal de monitoreo
- **GlobalInitializer**: Orquestador del sistema global
- Auto-inicialización en todas las páginas

#### Páginas Actualizadas
Todas las páginas principales incluyen el global-initializer:
- ✅ `Inicio.html`
- ✅ `AdmissionControl.html` 
- ✅ `UsersManagement.html`
- ✅ `RolesManagement.html`
- ✅ `PermissionsManagement.html`
- ✅ `EmergencyConfiguration.html`
- ✅ `MasterTable1.html`

### Flujo de Funcionamiento

1. **Inicialización**
   - Al cargar cualquier página, se inicia `GlobalUserMonitor`
   - Verifica autenticación básica antes de iniciar monitoreo
   - Configura intervalo de verificación cada 30 segundos

2. **Verificación Periódica**
   - Llama a `UserService.getCurrentUserStatusLight()`
   - Verifica estado activo del usuario
   - No verifica roles para evitar logout innecesario

3. **Detección de Problemas**
   - Usuario desactivado (`active: false`)
   - Token inválido (401)
   - Usuario no encontrado (404)

4. **Logout Automático**
   - Muestra toast con mensaje apropiado
   - Espera 3 segundos para mostrar mensaje
   - Limpia sesión completamente
   - Redirige al login

### Tipos de Mensajes

```javascript
// Usuario desactivado
"Tu cuenta ha sido desactivada. Serás redirigido al login."

// Sesión expirada
"Tu sesión ha expirado. Serás redirigido al login."

// Sesión cerrada en otra pestaña
"Sesión cerrada en otra pestaña"
```

## Configuración

### Intervalos de Verificación
```javascript
checkInterval: 30 * 1000  // 30 segundos
```

### Tiempos de Espera
```javascript
// Delay inicial para cargar servicios
setTimeout(() => this.checkUserStatus(), 5000); // 5s

// Tiempo de mostrar mensaje antes de redirigir  
setTimeout(() => this.redirectToLogin(), 3000); // 3s
```

## Funciones de Utilidad

### Verificación Manual
```javascript
// Forzar verificación inmediata
window.forceUserStatusCheck();
```

### Control del Sistema
```javascript
// Detener monitoreo
window.GlobalInitializer.shutdown();

// Reiniciar sistema
window.GlobalInitializer.initialize();
```

## Compatibilidad

### Sistemas de Toast
El sistema es compatible con múltiples implementaciones:
- `window.showToast()`
- `window.GlobalToast.show()`
- Toast básico con ID `toast`
- Fallback a `alert()`

### Rutas Dinámicas
Auto-detección de rutas de login según ubicación:
- Páginas en `/pages/`: `../../index.html`
- Otros casos: `index.html`

## Eventos Monitoreados

### Visibilidad de Página
- Verifica estado cuando la página vuelve a ser visible
- Útil cuando el usuario cambia de pestaña y regresa

### Foco de Ventana  
- Verifica estado cuando la ventana obtiene foco
- Detecta cambios mientras el usuario estaba en otra aplicación

### Storage Changes
- Monitorea cambios en `authToken` y `isLoggedIn`
- Sincroniza logout entre múltiples pestañas

## Depuración

### Logs en Consola
El sistema genera logs detallados:
```
🌐 GlobalUserMonitor: Iniciando monitoreo global del usuario
🌐 GlobalUserMonitor: Verificando estado del usuario...
✅ GlobalUserMonitor: Usuario activo
🚫 GlobalUserMonitor: Usuario requiere login - Usuario desactivado por administrador
🔄 GlobalUserMonitor: Redirigiendo a login: ../../index.html
```

### Testing
```javascript
// Probar sistema de toast
window.testToast();

// Verificar estado actual
window.forceUserStatusCheck();
```

## Migración desde Sistema Anterior

### Cambios Realizados
- ❌ Removido `initUserStatusMonitoring()` de inicializadores específicos
- ✅ Agregado `global-initializer.js` a todas las páginas
- ✅ Sistema unificado y global

### Beneficios
- **Consistencia**: Mismo comportamiento en todas las páginas
- **Confiabilidad**: No depende de inicializadores específicos
- **Mantenimiento**: Un solo punto de control
- **Escalabilidad**: Fácil agregar nuevas páginas
