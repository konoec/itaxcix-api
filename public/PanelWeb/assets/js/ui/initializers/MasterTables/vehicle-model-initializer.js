/**
 * Inicializador específico para la página de Gestión de Modelos de Vehículo
 * Maneja solo los componentes y controladores necesarios para esta página específica
 */
class VehicleModelInitializer {
    static async init() {
        console.log('🚗 Inicializando página de Gestión de Modelos de Vehículo...');
        if (authChecker.checkAuthentication()) {
            authChecker.updateUserDisplay();
            authChecker.setupLogoutButton();
            const componentLoader = new ComponentLoader();
            try {
                // Cargar componentes HTML dinámicamente ANTES de inicializar controladores
                console.log('🔄 Cargando componentes HTML...');
                await componentLoader.loadComponent('sidebar', '#sidebar-container', {
                    activeSection: window.pageConfig?.activeSection || 'tablas'
                });
                await componentLoader.loadComponent('topbar', '#topbar-container', {
                    pageTitle: window.pageConfig?.pageTitle || { icon: 'fas fa-car-side', text: 'Gestión de Modelos de Vehículo' }
                });
                await componentLoader.loadComponent('profile-modal', '#modal-container');
                console.log('✅ Todos los componentes HTML cargados');
                setTimeout(() => {
                    if (!window.sidebarControllerInstance) {
                        window.sidebarControllerInstance = new SidebarController();
                        console.log('📁 SidebarController inicializado');
                    }
                    setTimeout(() => {
                        if (!window.topBarControllerInstance) {
                            window.topBarControllerInstance = new TopBarController();
                            console.log('🔝 TopBarController inicializado');
                        }
                        setTimeout(() => {
                            if (!window.vehicleModelListControllerInstance) {
                                window.vehicleModelListControllerInstance = new VehicleModelListController();
                                window.vehicleModelListController = window.vehicleModelListControllerInstance;
                                // Usar load si no existe init
                                if (typeof window.vehicleModelListControllerInstance.init === 'function') {
                                    window.vehicleModelListControllerInstance.init();
                                } else if (typeof window.vehicleModelListControllerInstance.load === 'function') {
                                    window.vehicleModelListControllerInstance.load();
                                }
                                console.log('📋 VehicleModelListController inicializado');
                            }
                        }, 300);
                    }, 200);

                    // Inicializar VehicleModelEditController
                       if (!window.vehicleModelEditControllerInstance) {
                        window.vehicleModelEditControllerInstance = new VehicleModelEditController();
                        window.vehicleModelEditController = window.vehicleModelEditControllerInstance;
                         console.log('📝 VehicleModelEditController inicializado');
                         }
                         
                    // Inicializar ProfileController
                    if (!window.profileControllerInstance) {
                        window.profileControllerInstance = new ProfileController();
                        console.log('👤 ProfileController inicializado');
                        if (window.topBarControllerInstance) {
                            window.topBarControllerInstance.profileController = window.profileControllerInstance;
                            console.log('🔗 Referencia profile-topbar establecida');
                        }
                    }
                    setTimeout(() => {
                        if (window.PermissionsService) {
                            console.log('🔧 Inicializando sistema de permisos...');
                            window.PermissionsService.initializePermissions();
                        }
                        LoadingScreenUtil.notifyModuleLoaded('VehicleModel');
                        console.log('✅ Modelos de Vehículo inicializados completamente');
                    }, 100);
                }, 500);
            } catch (error) {
                console.error('❌ Error cargando componentes:', error);
                LoadingScreenUtil.notifyModuleLoaded('VehicleModel');
            }
        } else {
            console.log('❌ Usuario no autenticado, redirigiendo...');
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM cargado, iniciando VehicleModelInitializer...');
    setTimeout(() => {
        VehicleModelInitializer.init();
    }, 500);
});

console.log('📝 VehicleModelInitializer definido y configurado');

new VehicleModelCreateController();