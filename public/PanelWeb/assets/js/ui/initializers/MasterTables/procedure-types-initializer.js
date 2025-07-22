/**
 * Inicializador específico para la página de Gestión de Tipos de Procedimientos
 * Maneja solo los componentes y controladores necesarios para esta página específica
 */
class ProcedureTypesInitializer {
    static async init() {
        console.log('📋 Inicializando página de Gestión de Tipos de Procedimientos...');

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
                    icon: 'fas fa-clipboard-list',
                    text: 'Gestión de Tipos de Procedimientos'
                }
            });

            // Cargar profile modal
            await componentLoader.loadComponent('profile-modal', '#modal-container');

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

                // Inicializar ProcedureTypeListController
                setTimeout(() => {
                    if (!window.procedureTypeListControllerInstance &&
                        typeof ProcedureTypeListController !== 'undefined') {
                        window.procedureTypeListControllerInstance = new ProcedureTypeListController();
                        console.log('📋 ProcedureTypeListController inicializado');
                    }
                    // Inicializar ProcedureTypeCreateController
                    if (!window.procedureTypeCreateController && typeof ProcedureTypeCreateController !== 'undefined') {
                        window.procedureTypeCreateController = new ProcedureTypeCreateController();
                        console.log('📝 ProcedureTypeCreateController inicializado');
                    }
                    // Inicializar ProcedureTypeEditController después de la lista
                    if (!window.procedureTypeEditController && typeof ProcedureTypeEditControllerClass !== 'undefined') {
                        window.procedureTypeEditController = new window.ProcedureTypeEditControllerClass();
                        console.log('✏️ ProcedureTypeEditController inicializado desde el inicializador');
                    }
                }, 300);

                // --- INICIO: Integración DeleteProcedureTypeController ---
                // Instanciar el service y el controller de eliminación de tipo de trámite
                window.DeleteProcedureTypeService = new DeleteProcedureTypeService();
                window.deleteProcedureTypeControllerInstance = new DeleteProcedureTypeController();

                // Registrar evento global para botones de eliminar tipo de trámite
                document.addEventListener('click', function(e) {
                    const btn = e.target.closest('[data-action="delete-procedure-type"]');
                    if (btn) {
                        const procedureTypeId = parseInt(btn.getAttribute('data-procedure-type-id'), 10);
                        const procedureTypeName = btn.getAttribute('data-procedure-type-name') || '';
                        const procedureTypeData = { id: procedureTypeId, name: procedureTypeName };
                        window.deleteProcedureTypeControllerInstance.handleDeleteButtonClick(btn, procedureTypeData);
                    }
                });
                // Exponer el controller globalmente si lo necesitas en otros módulos
                window.DeleteProcedureTypeController = window.deleteProcedureTypeControllerInstance;
                console.log('🗑️ DeleteProcedureTypeController inicializado y eventos registrados');
                // --- FIN: Integración DeleteProcedureTypeController ---

                // Configurar permisos y notificar que el módulo está listo
                setTimeout(() => {
                    if (window.PermissionsService) {
                        console.log('🔧 Inicializando sistema de permisos...');
                        window.PermissionsService.initializePermissions();
                    }
                    LoadingScreenUtil.notifyModuleLoaded('ProcedureTypes');
                    console.log('✅ Tipos de Procedimientos inicializado completamente');
                }, 400);
            }, 500);

        } catch (error) {
            console.error('❌ Error cargando componentes:', error);
            // Notificar que el módulo está listo (incluso con error)
            LoadingScreenUtil.notifyModuleLoaded('ProcedureTypes');
        }
    }
}

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM cargado, iniciando ProcedureTypesInitializer...');
    // Pequeño delay para asegurar que todos los scripts estén cargados
    setTimeout(() => {
        ProcedureTypesInitializer.init();
    }, 500);
});

console.log('📝 ProcedureTypesInitializer definido y configurado');
