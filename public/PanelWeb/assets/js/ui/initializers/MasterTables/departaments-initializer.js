/**
 * Inicializador específico para la página de Gestión de Departamentos
 * Maneja los componentes y controladores necesarios para esta página específica
 */
class DepartamentsInitializer {
    static async init() {
        console.log('🗺️ Inicializando página de Gestión de Departamentos...');
        
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
                    pageTitle: window.pageConfig?.pageTitle || { icon: 'fas fa-map', text: 'Gestión de Departamentos' }
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
                        
                        // Inicializar el controlador de departamentos
                        setTimeout(async () => {
                            try {
                                if (window.departmentsController) {
                                    await window.departmentsController.init();
                                    console.log('🗺️ Controlador de departamentos inicializado');
                                }
                                
                                if (window.departmentCreateController) {
                                    window.departmentCreateController.init();
                                    console.log('🆕 Controlador de creación de departamentos inicializado');
                                }

                                // Inicializar controlador de actualización de departamentos
                                if (window.DepartmentUpdateController) {
                                    window.departmentUpdateController = new DepartmentUpdateController();
                                    console.log('✏️ Controlador de actualización de departamentos inicializado');
                                }

                                // El modal global de confirmación se inicializa automáticamente
                                console.log('🗑️ Modal global de confirmación disponible para departamentos');
                            } catch (error) {
                                console.error('❌ Error al inicializar controladores de departamentos:', error);
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
                        
                        console.log('✅ Departamentos inicializados completamente');
                        
                        // Notificar que este módulo ha terminado de cargar
                        LoadingScreenUtil.notifyModuleLoaded('Departaments');
                    }, 100);
                    
                }, 500);
                
            } catch (error) {
                console.error('❌ Error cargando componentes:', error);
                
                // En caso de error, también ocultar la pantalla de carga
                LoadingScreenUtil.notifyModuleLoaded('Departaments');
            }
            
        } else {
            console.log('❌ Usuario no autenticado, redirigiendo...');
        }
    }
}

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM cargado, iniciando DepartamentsInitializer...');
    
    // Pequeño delay para asegurar que todos los scripts estén cargados
    setTimeout(() => {
        DepartamentsInitializer.init();
    }, 500);
});

console.log('📝 DepartamentsInitializer definido y configurado');

