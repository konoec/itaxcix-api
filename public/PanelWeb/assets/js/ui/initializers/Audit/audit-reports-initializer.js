/**
 * Inicializador del módulo de Reportes de Auditoría
 * Coordina la inicialización                     // Configurar permisos DESPUÉS de que los controladores estén listos
                    setTimeout(() => {
                        if (window.PermissionsService) {
                            console.log('🔧 Inicializando sistema de permisos...');
                            window.PermissionsService.initializePermissions();
                        }os los componentes del módulo
 */
class AuditReportsInitializer {
    static async init() {
        console.log('� Inicializando módulo de Reportes de Auditoría...');
        
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
                    activeSection: window.pageConfig?.activeSection || 'auditoria'
                });
                
                // Cargar topbar
                await componentLoader.loadComponent('topbar', '#topbar-container', {
                    pageTitle: window.pageConfig?.pageTitle || { icon: 'fas fa-chart-line', text: 'Reportes de Auditoría' }
                });
                
                // Cargar profile modal
                await componentLoader.loadComponent('profile-modal', '#modal-container');
                
                console.log('✅ Todos los componentes HTML cargados');
                
                // Esperar más tiempo para que el DOM se actualice completamente
                setTimeout(() => {
                    // Inicializar controladores base
                    if (!window.sidebarControllerInstance) {
                        window.sidebarControllerInstance = new SidebarController();
                        console.log('📁 SidebarController inicializado');
                    }
                    
                    // Inicializar TopBarController DESPUÉS del sidebar con delay adicional
                    setTimeout(() => {
                        if (!window.topBarControllerInstance) {
                            window.topBarControllerInstance = new TopBarController();
                            console.log('� TopBarController inicializado');
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
                    
                    // Inicializar servicios de reportes si no existen
                    if (!window.incidentReportsService && window.IncidentReportsService) {
                        window.incidentReportsService = new window.IncidentReportsService();
                        console.log('✅ IncidentReportsService inicializado');
                    }
                    
                    if (!window.vehicleReportsService && window.VehicleReportsService) {
                        window.vehicleReportsService = new window.VehicleReportsService();
                        console.log('✅ VehicleReportsService inicializado');
                    }
                    
                    if (!window.infractionReportsService && window.InfractionReportsService) {
                        window.infractionReportsService = new window.InfractionReportsService();
                        console.log('✅ InfractionReportsService inicializado');
                    }
                    
                    if (!window.travelReportsService && window.TravelReportsService) {
                        window.travelReportsService = new window.TravelReportsService();
                        console.log('✅ TravelReportsService inicializado');
                    }
                    
                    // Inicializar controlador principal de reportes de auditoría específico de esta página
                    if (!window.auditReportsMainControllerInstance && window.AuditReportsMainController) {
                        window.auditReportsMainControllerInstance = new AuditReportsMainController();
                        console.log('📊 AuditReportsMainController inicializado');
                    }

                    // Configurar permisos DESPUÉS de que todos los controladores estén listos
                    setTimeout(() => {
                        if (window.PermissionsService) {
                            console.log('🔧 Inicializando sistema de permisos...');
                            window.PermissionsService.initializePermissions();
                        }
                        
                        console.log('✅ Reportes de Auditoría inicializados completamente');
                        
                        // Notificar que este módulo ha terminado de cargar
                        LoadingScreenUtil.notifyModuleLoaded('AuditReports');
                    }, 100);
                    
                }, 500);
                
            } catch (error) {
                console.error('❌ Error cargando componentes:', error);
            }
            
        } else {
            console.log('❌ Usuario no autenticado, redirigiendo...');
        }
    }
}

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM cargado, iniciando AuditReportsInitializer...');
    
    // Pequeño delay para asegurar que todos los scripts estén cargados
    setTimeout(() => {
        AuditReportsInitializer.init();
    }, 500);
});

console.log('📝 AuditReportsInitializer definido y configurado');

