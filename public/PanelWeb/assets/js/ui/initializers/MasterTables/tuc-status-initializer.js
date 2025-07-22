/**
 * Inicializador específico para la página de Gestión de Estado TUC
 * Maneja solo los componentes y controladores necesarios para esta página específica
 */
class TucStatusInitializer {
    static async init() {
        console.log('🏷️ Inicializando página de Gestión de Estado TUC...');

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
                    pageTitle: window.pageConfig?.pageTitle || { icon: 'fas fa-id-badge', text: 'Gestión de Estado TUC' }
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

                    // Inicializar TucStatusListController tras cargar UI básico
                    setTimeout(() => {
                        if (!window.tucStatusListControllerInstance && typeof TucStatusListController !== 'undefined') {
                            window.tucStatusListControllerInstance = new TucStatusListController();
                            console.log('🏷️ TucStatusListController inicializado');
                        }
                        // Inicializar TucStatusCreateController
                        if (!window.tucStatusCreateController && typeof TucStatusCreateController !== 'undefined') {
                            window.tucStatusCreateController = new TucStatusCreateController();
                            console.log('📝 TucStatusCreateController inicializado');
                        }
                        // Inicializar TucModalityCreateController
                        if (!window.tucModalityCreateController && typeof TucModalityCreateController !== 'undefined') {
                            window.tucModalityCreateController = new TucModalityCreateController();
                            console.log('📝 TucModalityCreateController inicializado');
                        }

                        // Inicializar TucStatusEditController después de la lista
                        if (!window.TucStatusEditController) {
                            if (window.TucStatusEditControllerClass) {
                                window.TucStatusEditController = new window.TucStatusEditControllerClass();
                                window.tucStatusEditController = window.TucStatusEditController;
                                console.log('✏️ TucStatusEditController inicializado desde el inicializador');
                            } else {
                                console.error('❌ No se encontró la clase TucStatusEditControllerClass');
                            }
                        }
                    }, 300);

                    // --- INICIO: Integración DeleteTucStatusController ---
            if (window.DeleteTucStatusController) {
                window.TucStatusDeleteController = new window.DeleteTucStatusController();
            }
                    window.deleteTucStatusService = window.DeleteTucStatusService; // Ya instanciado como singleton
                    window.deleteTucStatusController = new window.DeleteTucStatusController();
                    document.addEventListener('click', function(e) {
                        const btn = e.target.closest('[data-action="delete-tuc-status"]');
                        if (btn) {
                            const tucStatusId = parseInt(btn.getAttribute('data-tuc-status-id'), 10);
                            const tucStatusName = btn.getAttribute('data-tuc-status-name') || '';
                            const tucStatusData = { id: tucStatusId, name: tucStatusName };
                            window.deleteTucStatusController.handleDeleteButtonClick(btn, tucStatusData);
                        }
                    });
                    window.DeleteTucStatusController = window.deleteTucStatusController;
                    console.log('🗑️ DeleteTucStatusController inicializado y eventos registrados');
                    // --- FIN: Integración DeleteTucStatusController ---

                    // Configurar permisos DESPUÉS de que los controladores estén listos
                    setTimeout(() => {
                        if (window.PermissionsService) {
                            console.log('🔧 Inicializando sistema de permisos...');
                            window.PermissionsService.initializePermissions();
                        }

                        // Notificar que el módulo está listo
                        LoadingScreenUtil.notifyModuleLoaded('TucStatus');

                        console.log('✅ Estado TUC inicializado completamente');
                    }, 400);

                }, 500);

            } catch (error) {
                console.error('❌ Error cargando componentes:', error);

                // Notificar que el módulo está listo (incluso con error)
                LoadingScreenUtil.notifyModuleLoaded('TucStatus');
            }

        } else {
            console.log('❌ Usuario no autenticado, redirigiendo...');
        }
    }
}

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM cargado, iniciando TucStatusInitializer...');

    // Pequeño delay para asegurar que todos los scripts estén cargados
    setTimeout(() => {
        TucStatusInitializer.init();
    }, 500);
});

console.log('📝 TucStatusInitializer definido y configurado');
