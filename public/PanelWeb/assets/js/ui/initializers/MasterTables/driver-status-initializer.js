/**
 * Inicializador específico para la página de Gestión de Estado de Conductores
 * Maneja solo los componentes y controladores necesarios para esta página específica
 */
class DriverStatusInitializer {
    static async init() {
        console.log('� Inicializando página de Gestión de Estado de Conductores...');
        
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
                    pageTitle: window.pageConfig?.pageTitle || { icon: 'fas fa-user-check', text: 'Gestión de Estado de Conductores' }
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

                        // Inicializar DriverStatusListController después de todos los componentes
                        setTimeout(() => {
                            if (!window.driverStatusListControllerInstance) {
                                window.driverStatusListControllerInstance = new DriverStatusListController();
                                window.driverStatusListController = window.driverStatusListControllerInstance; // Alias para compatibilidad
                                window.driverStatusListControllerInstance.init();
                                console.log('📋 DriverStatusListController inicializado');
                            }
                        // Inicializar DriverStatusEditController después de la lista
                        if (!window.DriverStatusEditController) {
                            if (window.DriverStatusEditControllerClass) {
                                window.DriverStatusEditController = new window.DriverStatusEditControllerClass();
                                window.driverStatusEditController = window.DriverStatusEditController;
                                console.log('✏️ DriverStatusEditController inicializado desde el inicializador');
                            } else {
                                console.error('❌ No se encontró la clase DriverStatusEditControllerClass');
                            }
                        }
                        }, 300);
                    }, 200);
                    
                    // Inicializar ProfileController
                    if (!window.profileControllerInstance) {
                        window.profileControllerInstance = new ProfileController();
                        console.log('👤 ProfileController inicializado');
                        
                        // Establecer referencia al profile controller en topbar
                        if (window.topBarControllerInstance) {
                            window.topBarControllerInstance.profileController = window.profileControllerInstance;
                            console.log('� Referencia profile-topbar establecida');
                        }
                    }
                    
                    // Configurar permisos DESPUÉS de que los controladores estén listos
                    setTimeout(() => {
                        if (window.PermissionsService) {
                            console.log('🔧 Inicializando sistema de permisos...');
                            window.PermissionsService.initializePermissions();
                        }
                        
                        // Notificar que el módulo está listo
                        LoadingScreenUtil.notifyModuleLoaded('DriverStatus');
                        
                        console.log('✅ Estado de Conductores inicializados completamente');
                    }, 100);
                    
                }, 500);
                
            } catch (error) {
                console.error('❌ Error cargando componentes:', error);
                
                // Notificar que el módulo está listo (incluso con error)
                LoadingScreenUtil.notifyModuleLoaded('DriverStatus');
            }
            
        } else {
            console.log('❌ Usuario no autenticado, redirigiendo...');
        }
    }
}

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM cargado, iniciando DriverStatusInitializer...');
    
    // Pequeño delay para asegurar que todos los scripts estén cargados
    setTimeout(() => {
        DriverStatusInitializer.init();
    }, 500);
});

console.log('📝 DriverStatusInitializer definido y configurado');

