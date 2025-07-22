class ServiceTypeInitializer {
    static async init() {
        console.log('🔔 Inicializando página de Gestión de Tipos de Servicio...');
        
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
                    pageTitle: window.pageConfig?.pageTitle || { icon: 'fas fa-concierge-bell', text: 'Gestión de Tipos de Servicio' }
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
                    }, 200);
                    
                    // Configurar permisos DESPUÉS de que los controladores estén listos
                    setTimeout(() => {
                        if (window.PermissionsService) {
                            console.log('🔧 Inicializando sistema de permisos...');
                            window.PermissionsService.initializePermissions();
                        }
                        
                        // *** FALTABA ESTO: Inicializar ServiceTypeController ***
                        console.log('🚕 Inicializando ServiceTypeController...');
                        try {
                            if (!window.serviceTypeController) {
                                window.serviceTypeController = new ServiceTypeController();
                                console.log('✅ ServiceTypeController inicializado correctamente');
                            }
                        } catch (error) {
                            console.error('❌ Error inicializando ServiceTypeController:', error);
                        }
                        // Inicializar ServiceTypeEditController después de la lista
                        if (!window.ServiceTypeEditController) {
                            if (window.ServiceTypeEditControllerClass) {
                                window.ServiceTypeEditController = new window.ServiceTypeEditControllerClass();
                                window.serviceTypeEditController = window.ServiceTypeEditController;
                                console.log('✏️ ServiceTypeEditController inicializado desde el inicializador');
                            } else {
                                console.error('❌ No se encontró la clase ServiceTypeEditControllerClass');
                            }
                        }
                        // Notificar que el módulo está listo
                        LoadingScreenUtil.notifyModuleLoaded('ServiceType');
                        console.log('✅ Tipos de Servicio inicializado completamente');
                    }, 400);
                    
                }, 500);
                
            } catch (error) {
                console.error('❌ Error cargando componentes:', error);
                
                // Notificar que el módulo está listo (incluso con error)
                LoadingScreenUtil.notifyModuleLoaded('ServiceType');
            }
            
        } else {
            console.log('❌ Usuario no autenticado, redirigiendo...');
        }
    }
}

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM cargado, iniciando ServiceTypeInitializer...');
    
    // Pequeño delay para asegurar que todos los scripts estén cargados
    setTimeout(() => {
        ServiceTypeInitializer.init();
    }, 500);
});

console.log('📝 ServiceTypeInitializer definido y configurado');

