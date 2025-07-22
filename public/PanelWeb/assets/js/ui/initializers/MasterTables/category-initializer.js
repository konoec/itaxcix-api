/**
 * Inicializador específico para la página de Gestión de Categorías
 * Maneja solo los componentes y controladores necesarios para esta página específica
 */
class CategoryInitializer {
    static async init() {
        console.log('📁 Inicializando página de Gestión de Categorías...');

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
                    pageTitle: window.pageConfig?.pageTitle || { icon: 'fas fa-folder', text: 'Gestión de Categorías' }
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

                    // Inicializar TopBarController DESPUÉS del sidebar
                    setTimeout(() => {
                        if (!window.topBarControllerInstance) {
                            window.topBarControllerInstance = new TopBarController();
                            console.log('🔝 TopBarController inicializado');
                        }

                        // Inicializar ProfileController y enlazar con topbar
                        if (!window.profileControllerInstance) {
                            window.profileControllerInstance = new ProfileController();
                            console.log('👤 ProfileController inicializado');
                            window.topBarControllerInstance.profileController = window.profileControllerInstance;
                            console.log('🔗 Referencia profile-topbar establecida');
                        }
                    }, 200);

                    // Inicializar CategoryListController tras los demás
                    setTimeout(() => {
                        if (!window.categoryListControllerInstance && typeof CategoryListController !== 'undefined') {
                            window.categoryListControllerInstance = new CategoryListController();
                            console.log('📂 CategoryListController inicializado');
                        }
                        // Inicializar CategoryCreateController
                        if (!window.categoryCreateControllerInstance && typeof CategoryCreateController !== 'undefined') {
                            window.categoryCreateControllerInstance = new CategoryCreateController();
                            console.log('🟩 CategoryCreateController inicializado');
                        }
                        // Enlazar botón de crear categoría
                        const createBtn = document.getElementById('createCategoryBtn');
                        if (createBtn) {
                            createBtn.setAttribute('data-action', 'create-category');
                        }
                        // --- INICIO: Integración DeleteCategoryController ---
                        // Instanciar el service y el controller de eliminación de categoría
                        window.DeleteCategoryService = window.DeleteCategoryService || new DeleteCategoryService();
                        window.deleteCategoryControllerInstance = new DeleteCategoryController();
                        window.deleteCategoryController = window.deleteCategoryControllerInstance;
                        // Registrar evento global para botones de eliminar categoría
                        document.addEventListener('click', function(e) {
                            const btn = e.target.closest('[data-action="delete-category"]');
                            if (btn) {
                                const categoryId = parseInt(btn.getAttribute('data-category-id'), 10);
                                const categoryName = btn.getAttribute('data-category-name') || '';
                                const categoryData = { id: categoryId, name: categoryName };
                                window.deleteCategoryController.handleDeleteButtonClick(btn, categoryData);
                            }
                        });
                        console.log('🗑️ DeleteCategoryController inicializado y eventos registrados');
                        // --- FIN: Integración DeleteCategoryController ---
                    }, 300);

                    // Inicializar CategoryEditController después de la lista
                    if (!window.CategoryEditController) {
                        if (window.CategoryEditControllerClass) {
                            window.CategoryEditController = new window.CategoryEditControllerClass();
                            window.categoryEditController = window.CategoryEditController;
                            console.log('✏️ CategoryEditController inicializado desde el inicializador');
                        } else {
                            console.error('❌ No se encontró la clase CategoryEditControllerClass');
                        }
                    }

                    // Configurar permisos DESPUÉS de que los controladores estén listos
                    setTimeout(() => {
                        if (window.PermissionsService) {
                            console.log('🔧 Inicializando sistema de permisos...');
                            window.PermissionsService.initializePermissions();
                        }

                        console.log('✅ Categorías inicializadas completamente');

                        // Notificar que este módulo ha terminado de cargar
                        LoadingScreenUtil.notifyModuleLoaded('Category');
                    }, 400);

                }, 500);

            } catch (error) {
                console.error('❌ Error cargando componentes:', error);

                // En caso de error, también ocultar la pantalla de carga
                LoadingScreenUtil.notifyModuleLoaded('Category');
            }

        } else {
            console.log('❌ Usuario no autenticado, redirigiendo...');
        }
    }
}

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM cargado, iniciando CategoryInitializer...');

    // Pequeño delay para asegurar que todos los scripts estén cargados
    setTimeout(() => {
        CategoryInitializer.init();
    }, 500);
});

console.log('📝 CategoryInitializer definido y configurado');
