# Script para actualizar todos los inicializadores restantes
# Corrección masiva para seguir el patrón de Configuration

# Lista de módulos y sus configuraciones
$modules = @{
    "incident-type" = @{
        "emoji" = "⚠️"
        "name" = "Gestión de Tipos de Incidentes"
        "icon" = "fas fa-exclamation-triangle"
        "className" = "IncidentTypeInitializer"
        "logMessage" = "Tipos de Incidentes"
    }
    "infraction-severity" = @{
        "emoji" = "⚖️"
        "name" = "Gestión de Severidad de Infracciones"
        "icon" = "fas fa-gavel"
        "className" = "InfractionSeverityInitializer"
        "logMessage" = "Severidad de Infracciones"
    }
    "infraction-status" = @{
        "emoji" = "📋"
        "name" = "Gestión de Estado de Infracciones"
        "icon" = "fas fa-clipboard-check"
        "className" = "InfractionStatusInitializer"
        "logMessage" = "Estado de Infracciones"
    }
    "procedure-types" = @{
        "emoji" = "📑"
        "name" = "Gestión de Tipos de Procedimientos"
        "icon" = "fas fa-clipboard-list"
        "className" = "ProcedureTypesInitializer"
        "logMessage" = "Tipos de Procedimientos"
    }
    "province" = @{
        "emoji" = "🏞️"
        "name" = "Gestión de Provincias"
        "icon" = "fas fa-map-marker-alt"
        "className" = "ProvinceInitializer"
        "logMessage" = "Provincias"
    }
    "service-type" = @{
        "emoji" = "🛎️"
        "name" = "Gestión de Tipos de Servicio"
        "icon" = "fas fa-concierge-bell"
        "className" = "ServiceTypeInitializer"
        "logMessage" = "Tipos de Servicio"
    }
    "travel-status" = @{
        "emoji" = "🚗"
        "name" = "Gestión de Estado de Viajes"
        "icon" = "fas fa-route"
        "className" = "TravelStatusInitializer"
        "logMessage" = "Estado de Viajes"
    }
    "tuc-modality" = @{
        "emoji" = "🆔"
        "name" = "Gestión de Modalidades TUC"
        "icon" = "fas fa-id-card"
        "className" = "TucModalityInitializer"
        "logMessage" = "Modalidades TUC"
    }
    "tuc-status" = @{
        "emoji" = "🏷️"
        "name" = "Gestión de Estado TUC"
        "icon" = "fas fa-id-badge"
        "className" = "TucStatusInitializer"
        "logMessage" = "Estado TUC"
    }
    "user-code-type" = @{
        "emoji" = "👤"
        "name" = "Gestión de Tipos de Código Usuario"
        "icon" = "fas fa-user-tag"
        "className" = "UserCodeTypeInitializer"
        "logMessage" = "Tipos de Código Usuario"
    }
    "user-status" = @{
        "emoji" = "👥"
        "name" = "Gestión de Estado de Usuarios"
        "icon" = "fas fa-user-circle"
        "className" = "UserStatusInitializer"
        "logMessage" = "Estado de Usuarios"
    }
    "vehicle-class" = @{
        "emoji" = "🚙"
        "name" = "Gestión de Clases de Vehículos"
        "icon" = "fas fa-car-side"
        "className" = "VehicleClassInitializer"
        "logMessage" = "Clases de Vehículos"
    }
    "vehicle-model" = @{
        "emoji" = "🚗"
        "name" = "Gestión de Modelos de Vehículos"
        "icon" = "fas fa-car"
        "className" = "VehicleModelInitializer"
        "logMessage" = "Modelos de Vehículos"
    }
}

Write-Host "🔧 Iniciando corrección masiva de inicializadores..." -ForegroundColor Green

foreach ($moduleKey in $modules.Keys) {
    $module = $modules[$moduleKey]
    $filePath = "..\..\assets\js\ui\initializers\MasterTables\$moduleKey-initializer.js"
    
    if (Test-Path $filePath) {
        Write-Host "📝 Actualizando: $($module.name)" -ForegroundColor Yellow
        
        $template = @"
/**
 * Inicializador específico para la página de $($module.name)
 * Maneja solo los componentes y controladores necesarios para esta página específica
 */
class $($module.className) {
    static async init() {
        console.log('$($module.emoji) Inicializando página de $($module.name)...');
        
        if (authChecker.checkAuthentication()) {
            authChecker.updateUserDisplay();
            authChecker.setupLogoutButton();
            
            // Inicializar ComponentLoader
            const componentLoader = new ComponentLoader();
            
            try {
                // Cargar componentes HTML dinámicamente ANTES de inicializar controladores
                console.log('🔄 Cargando componentes HTML...');
                
                // Cargar sidebar
                await componentLoader.loadComponent('sidebar', '#sidebar-container', {
                    activeSection: window.pageConfig?.activeSection || 'tablas'
                });
                
                // Cargar topbar
                await componentLoader.loadComponent('topbar', '#topbar-container', {
                    pageTitle: window.pageConfig?.pageTitle || { icon: '$($module.icon)', text: '$($module.name)' }
                });
                
                // Cargar profile modal
                await componentLoader.loadComponent('profile-modal', '#modal-container');
                
                console.log('✅ Todos los componentes HTML cargados');
                
                // Esperar más tiempo para que el DOM se actualice completamente
                setTimeout(() => {
                    // Ahora inicializar controladores que necesitan los elementos del DOM
                    if (!window.sidebarControllerInstance) {
                        window.sidebarControllerInstance = new SidebarController();
                        console.log('📁 SidebarController inicializado');
                    }
                    
                    // Inicializar TopBarController DESPUÉS del sidebar con delay adicional
                    setTimeout(() => {
                        if (!window.topBarControllerInstance) {
                            window.topBarControllerInstance = new TopBarController();
                            console.log('🔝 TopBarController inicializado');
                        }
                    }, 200);
                    
                    // Inicializar ProfileController
                    if (!window.profileControllerInstance) {
                        window.profileControllerInstance = new ProfileController();
                        console.log('👤 ProfileController inicializado');
                        
                        // Establecer referencia al profile controller en topbar
                        if (window.topBarControllerInstance) {
                            window.topBarControllerInstance.profileController = window.profileControllerInstance;
                            console.log('🔗 Referencia profile-topbar establecida');
                        }
                    }
                    
                    // Configurar permisos DESPUÉS de que los controladores estén listos
                    setTimeout(() => {
                        if (window.PermissionsService) {
                            console.log('🔧 Inicializando sistema de permisos...');
                            window.PermissionsService.initializePermissions();
                        }
                        
                        // Ocultar pantalla de carga
                        const loadingOverlay = document.getElementById('permissions-loading');
                        if (loadingOverlay) {
                            loadingOverlay.style.display = 'none';
                        }
                        
                        console.log('✅ $($module.logMessage) inicializados completamente');
                    }, 100);
                    
                }, 500);
                
            } catch (error) {
                console.error('❌ Error cargando componentes:', error);
                
                // Ocultar pantalla de carga en caso de error
                const loadingOverlay = document.getElementById('permissions-loading');
                if (loadingOverlay) {
                    loadingOverlay.style.display = 'none';
                }
            }
            
        } else {
            console.log('❌ Usuario no autenticado, redirigiendo...');
        }
    }
}

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM cargado, iniciando $($module.className)...');
    
    // Pequeño delay para asegurar que todos los scripts estén cargados
    setTimeout(() => {
        $($module.className).init();
    }, 500);
});

console.log('📝 $($module.className) definido y configurado');
"@
        
        # Escribir el archivo completo
        Set-Content -Path $filePath -Value $template -Encoding UTF8
        Write-Host "✅ Completado: $($module.name)" -ForegroundColor Green
    } else {
        Write-Host "❌ No encontrado: $filePath" -ForegroundColor Red
    }
}

Write-Host "🎉 ¡Corrección masiva completada!" -ForegroundColor Green
Write-Host "📊 Se actualizaron $($modules.Count) inicializadores" -ForegroundColor Cyan
