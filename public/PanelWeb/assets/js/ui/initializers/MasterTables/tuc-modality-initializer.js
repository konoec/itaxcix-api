/**
 * Inicializador específico para la página de Gestión de Modalidades TUC
 * Maneja solo los componentes y controladores necesarios para esta página específica
 */
class TucModalityInitializer {
    static async init() {
        console.log('🆔 Inicializando página de Gestión de Modalidades TUC...');
        
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
                    pageTitle: window.pageConfig?.pageTitle || { icon: 'fas fa-id-card', text: 'Gestión de Modalidades TUC' }
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
                        
                        // *** INICIALIZAR TucModalityController ***
                        console.log('🚛 Inicializando TucModalityController...');
                        try {
                            if (!window.tucModalityController) {
                                window.tucModalityController = new TucModalityController();
                                console.log('✅ TucModalityController inicializado correctamente');
                            }

                            // Inicializar TucModalityEditController después de la lista
                            if (!window.TucModalityEditController) {
                                if (window.TucModalityEditControllerClass) {
                                    window.TucModalityEditController = new window.TucModalityEditControllerClass();
                                    window.tucModalityEditController = window.TucModalityEditController;
                                    console.log('✏️ TucModalityEditController inicializado desde el inicializador');
                                } else {
                                    console.error('❌ No se encontró la clase TucModalityEditControllerClass');
                                }
                            }

                            // Inicializar y exponer el controlador de eliminación de Modalidad TUC
                            if (!window.deleteTucModalityController) {
                                if (window.DeleteTucModalityController) {
                                    window.deleteTucModalityController = new window.DeleteTucModalityController();
                                    console.log('🗑️ DeleteTucModalityController inicializado y expuesto globalmente');
                                } else {
                                    console.error('❌ No se encontró la clase DeleteTucModalityController');
                                }
                            }
                        } catch (error) {
                            console.error('❌ Error inicializando TucModalityController:', error);
                        }

                        // Notificar que el módulo está listo
                        LoadingScreenUtil.notifyModuleLoaded('TucModality');

                        console.log('✅ Modalidades TUC inicializado completamente');
                    }, 400);
                    
                }, 500);
                
            } catch (error) {
                console.error('❌ Error cargando componentes:', error);
                
                // Notificar que el módulo está listo (incluso con error)
                LoadingScreenUtil.notifyModuleLoaded('TucModality');
            }
            
        } else {
            console.log('❌ Usuario no autenticado, redirigiendo...');
        }
    }
}

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM cargado, iniciando TucModalityInitializer...');
    
    // Pequeño delay para asegurar que todos los scripts estén cargados
    setTimeout(() => {
        TucModalityInitializer.init();
    }, 500);
});

console.log('📝 TucModalityInitializer definido y configurado');

