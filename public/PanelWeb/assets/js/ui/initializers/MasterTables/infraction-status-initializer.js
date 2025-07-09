/**
 * Inicializador específico para la página de Gestión de Estado de Infracciones
 * Maneja solo los componentes y controladores necesarios para esta página específica
 */
class InfractionStatusInitializer {
    static async init() {
        console.log('📋 Inicializando página de Gestión de Estado de Infracciones...');
        
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
                    pageTitle: window.pageConfig?.pageTitle || { icon: 'fas fa-clipboard-check', text: 'Gestión de Estado de Infracciones' }
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
                                console.log('🔗 Referencia profile-topbar establecida');
                            }
                        }
                    }, 200);
                    
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
                        
                        console.log('✅ Estado de Infracciones inicializado completamente');
                    }, 400);
                    
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
    console.log('📄 DOM cargado, iniciando InfractionStatusInitializer...');
    
    // Pequeño delay para asegurar que todos los scripts estén cargados
    setTimeout(() => {
        InfractionStatusInitializer.init();
    }, 500);
});

console.log('📝 InfractionStatusInitializer definido y configurado');
