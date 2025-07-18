/**
 * Inicializador específico para la página de Gestión de Tipos de Documentos
 * Maneja solo los componentes y controladores necesarios para esta página específica
 */
class DocumentTypesInitializer {
    static async init() {
        console.log('📄 Inicializando página de Gestión de Tipos de Documentos...');
        
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
                    pageTitle: window.pageConfig?.pageTitle || { icon: 'fas fa-file-alt', text: 'Gestión de Tipos de Documentos' }
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
                    setTimeout(() => {
                        if (!window.profileControllerInstance) {
                            window.profileControllerInstance = new ProfileController();
                            console.log('👤 ProfileController inicializado');
                            
                            // Establecer referencia al profile controller en topbar
                            if (window.topBarControllerInstance) {
                                window.topBarControllerInstance.profileController = window.profileControllerInstance;
                                console.log('🔗 Referencia profile-topbar establecida');
                            }
                        }
                    }, 300);
                    
                    // Inicializar controlador principal de DocumentTypes
                    setTimeout(() => {
                        if (!window.documentTypesListController) {
                            window.documentTypesListController = new DocumentTypesListController();
                            console.log('📋 DocumentTypesListController inicializado');
                        }
                    }, 400);
                    
                    // Configurar permisos DESPUÉS de que los controladores estén listos
                    setTimeout(() => {
                        if (window.PermissionsService) {
                            console.log('🔧 Inicializando sistema de permisos...');
                            window.PermissionsService.initializePermissions();
                        }
                        
                        console.log('✅ Tipos de Documentos inicializados completamente');
                        
                        // Notificar que este módulo ha terminado de cargar
                        LoadingScreenUtil.notifyModuleLoaded('DocumentTypes');
                    }, 500);
                    
                }, 500);
                
            } catch (error) {
                console.error('❌ Error cargando componentes:', error);
                
                // En caso de error, también ocultar la pantalla de carga
                LoadingScreenUtil.notifyModuleLoaded('DocumentTypes');
            }
            
        } else {
            console.log('❌ Usuario no autenticado, redirigiendo...');
        }
    }
}

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM cargado, iniciando DocumentTypesInitializer...');
    
    // Pequeño delay para asegurar que todos los scripts estén cargados
    setTimeout(() => {
        DocumentTypesInitializer.init();
    }, 500);
});
