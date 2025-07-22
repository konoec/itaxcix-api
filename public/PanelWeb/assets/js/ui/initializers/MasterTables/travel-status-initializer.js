/**
 * Inicializador específico para la página de Gestión de Estado de Viajes
 * Maneja solo los componentes y controladores necesarios para esta página específica
 */
class TravelStatusInitializer {
    static async init() {
        console.log('🚗 Inicializando página de Gestión de Estado de Viajes...');
        
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
                    pageTitle: window.pageConfig?.pageTitle || { icon: 'fas fa-route', text: 'Gestión de Estado de Viajes' }
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
                        
                        // Inicializar controlador principal de TravelStatus
                        if (!window.travelStatusListController) {
                            window.travelStatusListController = new TravelStatusListController();
                            console.log('🚗 TravelStatusListController inicializado');
                        }
                        // Inicializar TravelStatusCreateController
                        if (!window.travelStatusCreateController && typeof TravelStatusCreateController !== 'undefined') {
                            window.travelStatusCreateController = new TravelStatusCreateController();
                            console.log('📝 TravelStatusCreateController inicializado');
                        }
                        // Inicializar y exponer controlador de eliminación modular
                        if (!window.deleteTravelStatusController && typeof DeleteTravelStatusController !== 'undefined') {
                            window.deleteTravelStatusController = new DeleteTravelStatusController();
                            console.log('🗑️ DeleteTravelStatusController inicializado');
                        }
                        // Inicializar controlador de edición de TravelStatus
                        if (!window.updateTravelStatusController && typeof TravelStatusEditController !== 'undefined') {
                            window.updateTravelStatusController = new TravelStatusEditController();
                            console.log('✏️ TravelStatusEditController inicializado');
                        }

                        // Delegar evento de eliminación en la tabla usando el controlador modular
                        const tableBody = document.getElementById('travelStatusTableBody');
                        if (tableBody && window.deleteTravelStatusController) {
                            tableBody.addEventListener('click', function(e) {
                                const btn = e.target.closest('[data-action="delete-travel-status"]');
                                if (btn) {
                                    const id = parseInt(btn.getAttribute('data-travel-status-id'));
                                    const name = btn.getAttribute('data-travel-status-name') || btn.getAttribute('data-name');
                                    if (!isNaN(id)) {
                                        window.deleteTravelStatusController.handleDeleteButtonClick(btn, { id, name });
                                    }
                                }
                            });
                        }
                    }, 200);
                    
                    // Configurar permisos DESPUÉS de que los controladores estén listos
                    setTimeout(() => {
                        if (window.PermissionsService) {
                            console.log('🔧 Inicializando sistema de permisos...');
                            window.PermissionsService.initializePermissions();
                        }
                        
                        // Notificar que el módulo está listo
                        LoadingScreenUtil.notifyModuleLoaded('TravelStatus');
                        
                        console.log('✅ Estado de Viajes inicializado completamente');
                    }, 400);
                    
                }, 500);
                
            } catch (error) {
                console.error('❌ Error cargando componentes:', error);
                
                // Notificar que el módulo está listo (incluso con error)
                LoadingScreenUtil.notifyModuleLoaded('TravelStatus');
            }
            
        } else {
            console.log('❌ Usuario no autenticado, redirigiendo...');
        }
    }
}

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM cargado, iniciando TravelStatusInitializer...');
    
    // Pequeño delay para asegurar que todos los scripts estén cargados
    setTimeout(() => {
        TravelStatusInitializer.init();
    }, 500);
});

console.log('📝 TravelStatusInitializer definido y configurado');

