/**
 * Inicializador específico para la página de Gestión de Distritos
 * Maneja solo los componentes y controladores necesarios para esta página específica
 */
class DistrictInitializer {
    static async init() {
        console.log('🗺️ Inicializando página de Gestión de Distritos...');
        
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
                    pageTitle: window.pageConfig?.pageTitle || { icon: 'fas fa-map-marked-alt', text: 'Gestión de Distritos' }
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

                        // Inicializar el controlador de lista de distritos después de los controladores base
                        setTimeout(() => {
                            try {
                                if (typeof DistrictsListController === 'undefined') {
                                    throw new Error('DistrictsListController no está disponible');
                                }
                                
                                // Verificar si ya existe la instancia principal, si no, crearla
                                if (!window.districtsController) {
                                    if (typeof DistrictsListController === 'undefined') {
                                        throw new Error('DistrictsListController no está disponible');
                                    }
                                    window.districtsController = new DistrictsListController();
                                    console.log('🗺️ districtsController (instancia principal) inicializado desde initializer');
                                } else {
                                    console.log('🗺️ districtsController ya existe, usando instancia existente');
                                }
                                
                                // También mantener la referencia a la clase para compatibilidad
                                window.DistrictsListController = DistrictsListController;
                                
                                // Verificar que el controlador de eliminación ya esté disponible (se crea automáticamente)
                                if (window.districtDeleteController) {
                                    console.log('✅ DistrictDeleteController ya disponible automáticamente');
                                } else {
                                    console.warn('⚠️ DistrictDeleteController no está disponible');
                                }
                                
                                // Inicializar el controlador de actualización de distritos si existe
                                if (typeof DistrictUpdateController !== 'undefined') {
                                    window.districtUpdateController = new DistrictUpdateController();
                                    console.log('✏️ DistrictUpdateController inicializado');
                                } else {
                                    console.warn('⚠️ DistrictUpdateController no está disponible');
                                }
                                
                            } catch (error) {
                                console.error('❌ Error al inicializar controladores de distrito:', error);
                            }
                        }, 200);
                    }, 200);

                    // Configurar permisos DESPUÉS de que los controladores estén listos
                    setTimeout(() => {
                        if (window.PermissionsService) {
                            console.log('🔧 Inicializando sistema de permisos...');
                            window.PermissionsService.initializePermissions();
                        }

                        console.log('✅ Distritos inicializado completamente');
                        
                        // Notificar que este módulo ha terminado de cargar
                        LoadingScreenUtil.notifyModuleLoaded('District');
                    }, 600);

                }, 500);
                
            } catch (error) {
                console.error('❌ Error cargando componentes:', error);
                
                // En caso de error, también ocultar la pantalla de carga
                LoadingScreenUtil.notifyModuleLoaded('District');
            }
            
        } else {
            console.log('❌ Usuario no autenticado, redirigiendo...');
        }
    }
}

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM cargado, iniciando DistrictInitializer...');
    
    // Pequeño delay para asegurar que todos los scripts estén cargados
    setTimeout(() => {
        DistrictInitializer.init();
    }, 500);
});

console.log('📝 DistrictInitializer definido y configurado');

