/**
 * Inicializador específico para la página de Gestión de Tipos de Combustible
 * Maneja solo los componentes y controladores necesarios para esta página específica
 */
class FuelTypeInitializer {
    static async init() {
        console.log('⛽ Inicializando página de Gestión de Tipos de Combustible...');
        
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
                    pageTitle: window.pageConfig?.pageTitle || { icon: 'fas fa-gas-pump', text: 'Gestión de Tipos de Combustible' }
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
                    
                    // Inicializar FuelTypeController
                    setTimeout(() => {
                        if (!window.fuelTypeController) {
                            window.fuelTypeController = new FuelTypeController();
                            console.log('⛽ FuelTypeController inicializado');
                        }
                        // Inicializar FuelTypeCreateController
                        if (!window.fuelTypeCreateControllerInstance && typeof FuelTypeCreateController !== 'undefined') {
                            window.fuelTypeCreateControllerInstance = new FuelTypeCreateController();
                            console.log('🟩 FuelTypeCreateController inicializado');
                        }
                        // Enlazar botón de crear tipo de combustible
                        const createBtn = document.getElementById('createFuelTypeBtn');
                        if (createBtn) {
                            createBtn.setAttribute('data-action', 'create-fuel-type');
                        }
                    // Inicializar y exponer DeleteFuelTypeController
                    if (!window.deleteFuelTypeController && typeof DeleteFuelTypeController !== 'undefined') {
                        window.deleteFuelTypeController = new DeleteFuelTypeController();
                        console.log('🗑️ DeleteFuelTypeController inicializado');
                    }
                    // Enlazar evento click a los botones de eliminar tipo de combustible en la tabla
                    const tableBody = document.getElementById('fuel-types-table-body');
                    if (tableBody) {
                        tableBody.addEventListener('click', function (e) {
                            const btn = e.target.closest('[data-action="delete-fuel-type"]');
                            if (btn) {
                                const fuelTypeId = parseInt(btn.getAttribute('data-fuel-type-id'), 10);
                                const fuelTypeName = btn.getAttribute('data-fuel-type-name') || btn.dataset.fuelTypeName || '';
                                const fuelTypeData = { id: fuelTypeId, name: fuelTypeName };
                                if (window.deleteFuelTypeController) {
                                    window.deleteFuelTypeController.handleDeleteButtonClick(btn, fuelTypeData);
                                }
                            }
                        });
                        console.log('🗑️ Evento de eliminación de tipo de combustible enlazado a la tabla');
                    }
                    }, 300);
                    
                    // Inicializar FuelTypeEditController después de la lista
                    if (!window.FuelTypeEditController) {
                        if (window.FuelTypeEditControllerClass) {
                            window.FuelTypeEditController = new window.FuelTypeEditControllerClass();
                            window.fuelTypeEditController = window.FuelTypeEditController;
                            console.log('✏️ FuelTypeEditController inicializado desde el inicializador');
                        } else {
                            console.error('❌ No se encontró la clase FuelTypeEditControllerClass');
                        }
                    }
                    
                    // Configurar permisos DESPUÉS de que los controladores estén listos
                    setTimeout(() => {
                        if (window.PermissionsService) {
                            console.log('🔧 Inicializando sistema de permisos...');
                            window.PermissionsService.initializePermissions();
                        }
                        
                        console.log('✅ Tipos de Combustible inicializados completamente');
                        
                        // Notificar que este módulo ha terminado de cargar
                        LoadingScreenUtil.notifyModuleLoaded('FuelType');
                    }, 100);
                    
                }, 500);
                
            } catch (error) {
                console.error('❌ Error cargando componentes:', error);
                
                // En caso de error, también ocultar la pantalla de carga
                LoadingScreenUtil.notifyModuleLoaded('FuelType');
            }
            
        } else {
            console.log('❌ Usuario no autenticado, redirigiendo...');
        }
    }
}

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM cargado, iniciando FuelTypeInitializer...');
    
    // Pequeño delay para asegurar que todos los scripts estén cargados
    setTimeout(() => {
        FuelTypeInitializer.init();
    }, 500);
});

console.log('📝 FuelTypeInitializer definido y configurado');

