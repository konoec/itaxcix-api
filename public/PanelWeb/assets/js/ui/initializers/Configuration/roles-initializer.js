/**
 * Inicializador específico para la página de Gestión de Roles
 * Maneja solo los componentes y controladores necesarios para esta página específica
 */
class RolesInitializer {
    static async init() {
        console.log('🔐 Inicializando página de Gestión de Roles...');
        
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
                    activeSection: window.pageConfig?.activeSection || 'configuracion'
                });
                
                // Cargar topbar
                await componentLoader.loadComponent('topbar', '#topbar-container', {
                    pageTitle: window.pageConfig?.pageTitle || { icon: 'fas fa-user-tag', text: 'Gestión de Roles' }
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
                    
                    // Inicializar controlador específico del módulo
                    if (typeof RolesController !== 'undefined') {
                        if (!window.rolesController) {
                            window.rolesController = RolesController;
                            console.log('🔐 RolesController inicializado');
                            
                            // Inicializar el controlador
                            if (typeof RolesController.init === 'function') {
                                RolesController.init();
                                console.log('🚀 RolesController init() ejecutado');
                            }
                        }
                    }
                    
                    // Configurar permisos DESPUÉS de que los controladores estén listos
                    setTimeout(() => {
                        if (window.PermissionsService) {
                            console.log('� Inicializando sistema de permisos...');
                            window.PermissionsService.initializePermissions();
                        }
                        
                        console.log('✅ Gestión de Roles inicializada completamente');
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
    console.log('📄 DOM cargado, iniciando RolesInitializer...');
    
    // Pequeño delay para asegurar que todos los scripts estén cargados
    setTimeout(() => {
        RolesInitializer.init();
    }, 500);
});

console.log('📝 RolesInitializer definido y configurado');
