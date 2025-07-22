/**
 * Inicializador específico para la página de Gestión de Colores
 * Maneja solo los componentes y controladores necesarios para esta página específica
 */
class ColorInitializer {
    static async init() {
        console.log('🎨 Inicializando página de Gestión de Colores...');
        
        if (!authChecker.checkAuthentication()) {
            console.log('❌ Usuario no autenticado, redirigiendo...');
            return;
        }

        authChecker.updateUserDisplay();
        authChecker.setupLogoutButton();
        
        // Inicializar ComponentLoader
        const componentLoader = new ComponentLoader();
        
        try {
            console.log('🔄 Cargando componentes HTML...');

            // Cargar sidebar
            await componentLoader.loadComponent('sidebar', '#sidebar-container', {
                activeSection: window.pageConfig?.activeSection || 'tablas'
            });

            // Cargar topbar
            await componentLoader.loadComponent('topbar', '#topbar-container', {
                pageTitle: window.pageConfig?.pageTitle || {
                    icon: 'fas fa-palette',
                    text: 'Gestión de Colores'
                }
            });

            // Cargar profile modal
            await componentLoader.loadComponent('profile-modal', '#modal-container');

            console.log('✅ Todos los componentes HTML cargados');

            // Esperar a que el DOM termine de actualizarse
            setTimeout(() => {
                // Inicializar SidebarController
                if (!window.sidebarControllerInstance) {
                    window.sidebarControllerInstance = new SidebarController();
                    console.log('📁 SidebarController inicializado');
                }

                // Inicializar TopBarController
                setTimeout(() => {
                    if (!window.topBarControllerInstance) {
                        window.topBarControllerInstance = new TopBarController();
                        console.log('🔝 TopBarController inicializado');
                    }

                    // Inicializar ProfileController y enlazarlo
                    if (!window.profileControllerInstance) {
                        window.profileControllerInstance = new ProfileController();
                        console.log('👤 ProfileController inicializado');
                        window.topBarControllerInstance.profileController =
                            window.profileControllerInstance;
                        console.log('🔗 Referencia profile-topbar establecida');
                    }
                }, 200);

                // Inicializar ColorListController
                setTimeout(() => {
                    if (!window.colorListController &&
                        typeof ColorListController !== 'undefined') {
                        window.colorListController = new ColorListController();
                        console.log('🎨 ColorListController inicializado');
                    }
                    // Inicializar ColorCreateController
                    if (!window.colorCreateControllerInstance && typeof ColorCreateController !== 'undefined') {
                        window.colorCreateControllerInstance = new ColorCreateController();
                        console.log('🟩 ColorCreateController inicializado');
                    }
                    // Enlazar botón de crear color
                    const createBtn = document.getElementById('createColorBtn');
                    if (createBtn) {
                        createBtn.setAttribute('data-action', 'create-color');
                    }
                    // Inicializar ColorEditController
                    if (!window.colorEditControllerInstance && typeof ColorEditController !== 'undefined') {
                        window.colorEditControllerInstance = new ColorEditController();
                        console.log('🖌️ ColorEditController inicializado');
                    }
            // Inicializar y exponer DeleteColorController
            if (!window.deleteColorController && typeof DeleteColorController !== 'undefined') {
                window.deleteColorController = new DeleteColorController();
                console.log('🗑️ DeleteColorController inicializado');
            }
            // Enlazar evento click a los botones de eliminar color en la tabla
            const tableBody = document.getElementById('colorTableBody');
            if (tableBody) {
                tableBody.addEventListener('click', function (e) {
                    const btn = e.target.closest('[data-action="delete-color"]');
                    if (btn) {
                        const colorId = parseInt(btn.getAttribute('data-color-id'), 10);
                        const colorName = btn.getAttribute('data-color-name') || btn.dataset.colorName || '';
                        const colorData = { id: colorId, name: colorName };
                        if (window.deleteColorController) {
                            window.deleteColorController.handleDeleteButtonClick(btn, colorData);
                        }
                    }
                });
                console.log('🗑️ Evento de eliminación de color enlazado a la tabla');
            }
                }, 300);

                // Configurar permisos y notificar carga completa
                setTimeout(() => {
                    if (window.PermissionsService) {
                        console.log('🔧 Inicializando sistema de permisos...');
                        window.PermissionsService.initializePermissions();
                    }

                    console.log('✅ Colores inicializados completamente');
                    LoadingScreenUtil.notifyModuleLoaded('Color');
                }, 400);
            }, 500);
            
        } catch (error) {
            console.error('❌ Error cargando componentes:', error);
            // Asegurar que la pantalla de carga se oculte incluso tras error
            LoadingScreenUtil.notifyModuleLoaded('Color');
        }
    }
}

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM cargado, iniciando ColorInitializer...');
    setTimeout(() => {
        ColorInitializer.init();
    }, 500);
});

console.log('📝 ColorInitializer definido y configurado');
