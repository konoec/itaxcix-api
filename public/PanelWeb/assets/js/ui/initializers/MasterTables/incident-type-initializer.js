/**
 * Inicializador específico para la página de Gestión de Tipos de Incidentes
 * Maneja solo los componentes y controladores necesarios para esta página específica
 */
class IncidentTypeInitializer {
    static async init() {
        console.log('🚨 Inicializando página de Gestión de Tipos de Incidentes...');

        if (!authChecker.checkAuthentication()) {
            console.log('❌ Usuario no autenticado, redirigiendo...');
            return;
        }

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
                pageTitle: window.pageConfig?.pageTitle || {
                    icon: 'fas fa-exclamation-triangle',
                    text: 'Gestión de Tipos de Incidentes'
                }
            });

            // Cargar profile modal sin sobrescribir otros modales
            await componentLoader.loadComponent('profile-modal', '#modal-container', { append: true });

            console.log('✅ Todos los componentes HTML cargados');

            // Esperar más tiempo para que el DOM se actualice completamente
            setTimeout(() => {
                // Inicializar SidebarController
                if (!window.sidebarControllerInstance) {
                    window.sidebarControllerInstance = new SidebarController();
                    console.log('📁 SidebarController inicializado');
                }

                // Inicializar TopBarController y ProfileController
                setTimeout(() => {
                    if (!window.topBarControllerInstance) {
                        window.topBarControllerInstance = new TopBarController();
                        console.log('🔝 TopBarController inicializado');
                    }
                    if (!window.profileControllerInstance) {
                        window.profileControllerInstance = new ProfileController();
                        console.log('👤 ProfileController inicializado');
                        window.topBarControllerInstance.profileController = window.profileControllerInstance;
                        console.log('🔗 Referencia profile-topbar establecida');
                    }
                }, 200);

                // Inicializar IncidentTypeListController tras la UI básica
                setTimeout(() => {
                    if (
                        !window.incidentTypeListControllerInstance &&
                        typeof IncidentTypeListController !== 'undefined'
                    ) {
                        window.incidentTypeListControllerInstance = new IncidentTypeListController();
                        console.log('🚨 IncidentTypeListController inicializado');
                    }
                    // Inicializar IncidentTypeEditController después de la lista
                    setTimeout(() => {
                        if (!window.IncidentTypeEditController) {
                            if (window.IncidentTypeEditControllerClass) {
                                window.IncidentTypeEditController = new window.IncidentTypeEditControllerClass();
                                window.incidentTypeEditController = window.IncidentTypeEditController;
                                console.log('✏️ IncidentTypeEditController inicializado desde el inicializador');
                            } else {
                                console.error('❌ No se encontró la clase IncidentTypeEditControllerClass');
                            }
                        }
                    }, 200);
                }, 300);


                // Instanciar el controlador de creación de tipo de incidencia solo cuando el modal esté en el DOM
                const tryInitCreateIncidentTypeModalController = () => {
                    const modal = document.getElementById('incidentTypeCreateModal');
                    const btn = document.getElementById('createIncidentTypeBtn');
                    if (typeof window.CreateIncidentTypeModalController === 'function' && modal && btn) {
                        window.createIncidentTypeModalController = new window.CreateIncidentTypeModalController('incidentTypeCreateModal');
                        console.log('🆕 CreateIncidentTypeModalController inicializado');
                        if (window.bootstrap) {
                            // Eliminar todos los event listeners previos usando cloneNode, pero mantener el botón en el DOM
                            const newBtn = btn.cloneNode(true);
                            btn.parentNode.replaceChild(newBtn, btn);
                            newBtn.addEventListener('click', function() {
                                var modalInstance = window.bootstrap.Modal.getOrCreateInstance(modal);
                                modalInstance.show();
                            });
                        }
                    } else {
                        // Intentar de nuevo en 100ms si el modal o el botón aún no están
                        setTimeout(tryInitCreateIncidentTypeModalController, 100);
                    }
                };
                tryInitCreateIncidentTypeModalController();

                // --- INICIO: Integración DeleteIncidentTypeController ---
                // Instanciar el controlador de eliminación de tipo de incidencia
                window.deleteIncidentTypeControllerInstance = new DeleteIncidentTypeController();

                // Registrar evento global para botones de eliminar tipo de incidencia
                document.addEventListener('click', function(e) {
                    const btn = e.target.closest('[data-action="delete-incident-type"]');
                    if (btn) {
                        const incidentTypeId = parseInt(btn.getAttribute('data-incident-type-id'), 10);
                        const incidentTypeName = btn.getAttribute('data-incident-type-name') || '';
                        const incidentTypeData = { id: incidentTypeId, name: incidentTypeName };
                        window.deleteIncidentTypeControllerInstance.handleDeleteButtonClick(btn, incidentTypeData);
                    }
                });

                // Exponer el controlador globalmente si se requiere en otros módulos
                window.DeleteIncidentTypeController = window.deleteIncidentTypeControllerInstance;
                console.log('🗑️ DeleteIncidentTypeController inicializado y eventos registrados');
                // --- FIN: Integración DeleteIncidentTypeController ---

                // Configurar permisos y notificar que el módulo está listo
                setTimeout(() => {
                    if (window.PermissionsService) {
                        console.log('🔧 Inicializando sistema de permisos...');
                        window.PermissionsService.initializePermissions();
                    }

                    LoadingScreenUtil.notifyModuleLoaded('IncidentType');
                    console.log('✅ Tipos de Incidentes inicializado completamente');
                }, 400);
            }, 500);

        } catch (error) {
            console.error('❌ Error cargando componentes:', error);
            // Notificar que el módulo está listo (incluso con error)
            LoadingScreenUtil.notifyModuleLoaded('IncidentType');
        }
    }
}

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM cargado, iniciando IncidentTypeInitializer...');
    setTimeout(() => {
        IncidentTypeInitializer.init();
    }, 500);
});

console.log('📝 IncidentTypeInitializer definido y configurado');

