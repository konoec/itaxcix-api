# Dashboard del Sistema - Documentación

## Resumen
Se ha implementado un dashboard completo en la página de inicio que muestra estadísticas del sistema obtenidas desde el endpoint `/api/v1/dashboard/stats`.

## Componentes Implementados

### 1. **DashboardService** (`dashboard-service.js`)
Servicio para gestionar las estadísticas del dashboard.

#### Métodos principales:
- `getStats()`: Obtiene estadísticas desde el endpoint
- `formatNumber()`: Formatea números grandes (K, M)
- `formatPercentage()`: Formatea porcentajes
- `getIndicatorColor()`: Obtiene color basado en porcentaje

#### Manejo de errores:
- **401**: Token inválido/expirado → Limpia tokens y redirige
- **403**: Sin permisos de CONFIGURACIÓN
- **500**: Error interno del servidor

### 2. **DashboardController** (`dashboard-controller.js`)
Controlador principal del dashboard.

#### Funcionalidades:
- Carga automática de estadísticas al inicializar
- Refrescado manual con botón
- Auto-refresh cada 5 minutos
- Renderizado dinámico de métricas
- Manejo de estados (loading, error, success)

#### Estados visuales:
- **Loading**: Spinner mientras carga datos
- **Error**: Mensaje de error con botón de reintento
- **Success**: Dashboard completo con métricas

### 3. **Estilos CSS** (`dashboard.css`)
Diseño moderno y responsivo.

#### Características:
- Cards con gradientes y sombras
- Animaciones suaves (fadeInUp)
- Barras de progreso animadas
- Responsive design (mobile-first)
- Color coding por performance

### 4. **Estructura HTML**
Dashboard integrado en la página de inicio.

#### Secciones:
- **Header**: Título, subtítulo y botón de actualizar
- **Stats Grid**: 4 métricas principales en cards
- **Metrics Grid**: 2 métricas secundarias con barras de progreso

## Datos del Dashboard

### Estadísticas Principales (Cards)
1. **Total de Usuarios**
   - Valor: `totalUsers`
   - Tendencia: Porcentaje de usuarios activos

2. **Usuarios Activos**
   - Valor: `activeUsers`
   - Tendencia: `userActivityPercentage`

3. **Total de Roles**
   - Valor: `totalRoles`
   - Detalle: Roles web vs total

4. **Total de Permisos**
   - Valor: `totalPermissions`
   - Detalle: Permisos web vs total

### Métricas Secundarias (Barras de Progreso)

#### Acceso Web:
- **Usuarios con Acceso Web**: `usersWithWebAccess` (`webAccessPercentage`)
- **Actividad de Usuarios**: `userActivityPercentage`

#### Distribución del Sistema:
- **Roles Web**: `webRoles/totalRoles`
- **Permisos Web**: `webPermissions/totalPermissions`

## Integración

### Archivos modificados:
1. **`Inicio.html`**: 
   - Agregado CSS del dashboard
   - Estructura HTML del dashboard
   - Scripts del servicio y controlador

2. **`inicio-initializer.js`**:
   - Inicialización del DashboardController
   - Limpieza del código duplicado

### Dependencias:
- `DashboardService`: Manejo de API
- `DashboardController`: Lógica de UI
- Sistema de toasts existente
- Sistema de autenticación existente

## Características Destacadas

### 🎨 **Diseño Visual**
- Cards con gradientes modernos
- Iconos temáticos por categoría
- Animaciones CSS suaves
- Responsive design completo

### 📊 **Métricas Inteligentes**
- Formateo automático de números (K/M)
- Colores dinámicos por performance
- Indicadores de tendencia
- Barras de progreso animadas

### 🔄 **Funcionalidad**
- Carga automática al inicializar
- Refrescado manual con feedback visual
- Auto-refresh cada 5 minutos
- Manejo robusto de errores

### 🛡️ **Seguridad**
- Manejo completo de tokens expirados
- Verificación de permisos
- Redirección automática en caso de errores de auth

## Uso

### Carga Automática:
El dashboard se carga automáticamente al acceder a la página de inicio.

### Refrescado Manual:
Hacer clic en el botón "Actualizar" en el header del dashboard.

### Auto-refresh:
Se actualiza automáticamente cada 5 minutos en segundo plano.

### Debugging:
Logs detallados en consola con prefijo `📊` para fácil identificación.

## Estructura de Respuesta API

```javascript
{
  "success": true,
  "message": "OK",
  "data": {
    "totalUsers": 1250,
    "activeUsers": 1100,
    "totalRoles": 8,
    "webRoles": 5,
    "totalPermissions": 25,
    "webPermissions": 15,
    "usersWithWebAccess": 45,
    "userActivityPercentage": 88,
    "webAccessPercentage": 3.6
  }
}
```

## Próximas Mejoras

### Posibles funcionalidades adicionales:
1. **Gráficos**: Integrar Chart.js para visualizaciones
2. **Histórico**: Mostrar tendencias temporales
3. **Filtros**: Por fecha, tipo de usuario, etc.
4. **Exportación**: PDF/Excel de reportes
5. **Alertas**: Notificaciones por umbrales críticos
