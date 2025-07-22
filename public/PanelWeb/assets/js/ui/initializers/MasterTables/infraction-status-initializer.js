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
            const componentLoader = new ComponentLoader();
            try {
                // Cargar componentes HTML dinámicamente ANTES de inicializar controladores
                console.log('🔄 Cargando componentes HTML...');
                await componentLoader.loadComponent('sidebar', '#sidebar-container', {
                    activeSection: window.pageConfig?.activeSection || 'tablas'
                });
                await componentLoader.loadComponent('topbar', '#topbar-container', {
                    pageTitle: window.pageConfig?.pageTitle || { icon: 'fas fa-clipboard-check', text: 'Gestión de Estado de Infracciones' }
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
                            if (!window.infractionStatusListControllerInstance) {
                                window.infractionStatusListControllerInstance = new InfractionStatusListController();
                                window.infractionStatusListController = window.infractionStatusListControllerInstance;
                                window.infractionStatusListControllerInstance.init();
                                console.log('📋 InfractionStatusListController inicializado');
                            }
                            // Inicializar controlador de edición de estado de infracción
                            if (!window.infractionStatusEditController) {
                                if (window.InfractionStatusEditControllerClass) {
                                    window.infractionStatusEditController = new window.InfractionStatusEditControllerClass();
                                    console.log('✏️ InfractionStatusEditController inicializado desde el inicializador');
                                } else {
                                    console.error('❌ No se encontró la clase InfractionStatusEditControllerClass');
                                }
                            }
                        }, 300);
                    }, 200);
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
                        LoadingScreenUtil.notifyModuleLoaded('InfractionStatus');
                        console.log('✅ Estado de Infracciones inicializados completamente');
                    }, 100);
                }, 500);
            } catch (error) {
                console.error('❌ Error cargando componentes:', error);
                LoadingScreenUtil.notifyModuleLoaded('InfractionStatus');
            }
        } else {
            console.log('❌ Usuario no autenticado, redirigiendo...');
        }
    }
}

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM cargado, iniciando InfractionStatusInitializer...');
    setTimeout(() => {
        InfractionStatusInitializer.init();
        // Agregar evento para abrir el modal de creación
        const createBtn = document.getElementById('createInfractionStatusBtn');
        if (createBtn && window.infractionStatusCreateController) {
            createBtn.addEventListener('click', function(e) {
                e.preventDefault();
                window.infractionStatusCreateController.openCreateModal();
            });
        }
    }, 500);
});

console.log('📝 InfractionStatusInitializer definido y configurado');
