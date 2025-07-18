/**
 * Inicializador específico para la página de Centro de Ayuda
 * Maneja solo los componentes y controladores necesarios para esta página específica
 */
class HelpCenterInitializer {
    static async init() {
        // Evitar inicialización múltiple
        if (window.helpCenterInitialized) {
            console.log('⚠️ HelpCenterInitializer ya fue inicializado, evitando duplicación');
            return;
        }
        window.helpCenterInitialized = true;
        
        console.log('🆘 Inicializando página de Centro de Ayuda...');
        
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
                    pageTitle: window.pageConfig?.pageTitle || { icon: 'fas fa-question-circle', text: 'Centro de Ayuda' }
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
                    
                    // Inicializar controladores específicos del Centro de Ayuda
                    if (typeof EmergencyNumberController !== 'undefined') {
                        if (!window.emergencyNumberController) {
                            window.emergencyNumberController = new EmergencyNumberController();
                            console.log('✅ EmergencyNumberController inicializado');
                        }
                    }
                    
                    // Inicializar controlador de elementos del centro de ayuda
                    if (typeof HelpCenterController !== 'undefined') {
                        if (!window.helpCenterController) {
                            console.log('🔧 Creando nueva instancia de HelpCenterController...');
                            window.helpCenterController = new HelpCenterController();
                            console.log('✅ HelpCenterController inicializado');
                            
                            // Inicializar la carga de datos después de un pequeño delay
                            setTimeout(() => {
                                console.log('🚀 Iniciando carga de datos del centro de ayuda...');
                                window.helpCenterController.initialize();
                            }, 200);
                        } else {
                            console.log('⚠️ HelpCenterController ya existe, evitando duplicación');
                        }
                    }

                    // Inicializar controlador de creación de elementos
                    if (typeof CreateHelpCenterController !== 'undefined') {
                        if (!window.createHelpCenterController) {
                            console.log('🔧 Creando nueva instancia de CreateHelpCenterController...');
                            window.createHelpCenterController = new CreateHelpCenterController(
                                function(newItem) {
                                    // Callback cuando se crea un nuevo elemento
                                    console.log('✅ Nuevo elemento creado, refrescando lista...');
                                    if (window.helpCenterController && typeof window.helpCenterController.loadHelpCenterItems === 'function') {
                                        window.helpCenterController.loadHelpCenterItems();
                                    }
                                }
                            );
                            console.log('✅ CreateHelpCenterController inicializado');
                        } else {
                            console.log('⚠️ CreateHelpCenterController ya existe, evitando duplicación');
                        }
                    }

                    // Inicializar controlador de eliminación de elementos
                    if (typeof DeleteHelpCenterController !== 'undefined') {
                        if (!window.deleteHelpCenterController) {
                            console.log('🔧 Creando nueva instancia de DeleteHelpCenterController...');
                            window.deleteHelpCenterController = new DeleteHelpCenterController(
                                function(deletedItem) {
                                    // Callback cuando se elimina un elemento
                                    console.log('✅ Elemento eliminado, refrescando lista...');
                                    if (window.helpCenterController && typeof window.helpCenterController.loadHelpCenterItems === 'function') {
                                        window.helpCenterController.loadHelpCenterItems();
                                    }
                                }
                            );
                            console.log('✅ DeleteHelpCenterController inicializado');
                        } else {
                            console.log('⚠️ DeleteHelpCenterController ya existe, evitando duplicación');
                        }
                    }
                    
                    // Configurar permisos DESPUÉS de que los controladores estén listos
                    setTimeout(() => {
                        if (window.PermissionsService) {
                            console.log('🔧 Inicializando sistema de permisos...');
                            window.PermissionsService.initializePermissions();
                        }
                        
                        console.log('✅ Centro de Ayuda inicializado completamente');
                        
                        // Notificar que este módulo ha terminado de cargar
                        LoadingScreenUtil.notifyModuleLoaded('HelpCenter');
                    }, 100);
                    
                }, 500);
                
            } catch (error) {
                console.error('❌ Error cargando componentes:', error);
                // En caso de error, ocultar la pantalla de carga para no dejar al usuario colgado
                if (window.LoadingScreenUtil) {
                    LoadingScreenUtil.notifyModuleLoaded('HelpCenter');
                }
            }
            
        } else {
            console.log('❌ Usuario no autenticado, redirigiendo...');
        }
    }
}

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM cargado, iniciando HelpCenterInitializer...');
    
    // Pequeño delay para asegurar que todos los scripts estén cargados
    setTimeout(() => {
        HelpCenterInitializer.init().catch(error => {
            console.error('❌ Error crítico en HelpCenterInitializer:', error);
            // En caso de error crítico, ocultar la pantalla de carga
            if (window.LoadingScreenUtil) {
                LoadingScreenUtil.notifyModuleLoaded('HelpCenter');
            }
        });
    }, 500);
});

console.log('📝 HelpCenterInitializer definido y configurado');
