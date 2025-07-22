/**
 * Inicializador específico para la página de Gestión de Marcas
 * Maneja solo los componentes y controladores necesarios para esta página específica
 */
class BrandInitializer {
    static async init() {
        console.log('🏷️ Inicializando página de Gestión de Marcas...');
        
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
                    pageTitle: window.pageConfig?.pageTitle || { icon: 'fas fa-tags', text: 'Gestión de Marcas' }
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
                    
                    // Inicializar BrandListController específico del módulo
                    if (!window.brandListControllerInstance && typeof BrandListController !== 'undefined') {
                        window.brandListControllerInstance = new BrandListController();
                        window.brandListController = window.brandListControllerInstance;
                        if (typeof window.brandListControllerInstance.init === 'function') {
                            window.brandListControllerInstance.init();
                            console.log('📋 BrandListController inicializado con init()');
                        } else {
                            console.log('📋 BrandListController inicializado (sin método init)');
                        }
                    }
                    // Inicializar BrandEditController
                    if (!window.brandEditControllerInstance) {
                        window.brandEditControllerInstance = new BrandEditController();
                        window.brandEditController = window.brandEditControllerInstance;
                        console.log('📝 BrandEditController inicializado');
                    }
                    
                    // Configurar permisos DESPUÉS de que los controladores estén listos
                    setTimeout(() => {
                        if (window.PermissionsService) {
                            console.log('🔧 Inicializando sistema de permisos...');
                            window.PermissionsService.initializePermissions();
                        }
                        
                        // Ocultar pantalla de carga
                        const loadingOverlay = document.getElementById('permissions-loading');
                        if (loadingOverlay) {
                            loadingOverlay.style.display = 'none';
                        }
                        
                        console.log('✅ Marcas inicializadas completamente');
                        
                        // Notificar que este módulo ha terminado de cargar
                        LoadingScreenUtil.notifyModuleLoaded('Brand');
                    }, 100);
                    
                }, 500);
                
            } catch (error) {
                console.error('❌ Error cargando componentes:', error);
                
                // Ocultar pantalla de carga en caso de error
                const loadingOverlay = document.getElementById('permissions-loading');
                if (loadingOverlay) {
                    loadingOverlay.style.display = 'none';
                }
            }
            
        } else {
            console.log('❌ Usuario no autenticado, redirigiendo...');
        }
    }
}

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM cargado, iniciando BrandInitializer...');
    
    // Pequeño delay para asegurar que todos los scripts estén cargados
    setTimeout(() => {
        BrandInitializer.init();
    }, 500);
});

                        // --- INICIO: Integración DeleteBrandController ---
                        // Instanciar el controlador de eliminación de marca
                        window.deleteBrandControllerInstance = new DeleteBrandController();
                        // Registrar evento global para botones de eliminar marca
                        document.addEventListener('click', function(e) {
                            const btn = e.target.closest('[data-action="delete-brand"]');
                            if (btn) {
                                const brandId = parseInt(btn.getAttribute('data-brand-id'), 10);
                                const brandName = btn.getAttribute('data-brand-name') || '';
                                const brandData = { id: brandId, name: brandName };
                                window.deleteBrandControllerInstance.handleDeleteButtonClick(btn, brandData);
                            }
                        });
                        window.DeleteBrandController = window.deleteBrandControllerInstance;
                        console.log('🗑️ DeleteBrandController inicializado y eventos registrados');
                        // --- FIN: Integración DeleteBrandController ---
console.log('📝 BrandInitializer definido y configurado');

