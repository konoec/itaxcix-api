/**
 * Inicializador específico para la página de Gestión de Severidad de Infracciones
 * Maneja solo los componentes y controladores necesarios para esta página específica
 */
class InfractionSeverityInitializer {
    static async init() {
        console.log('⚖️ Inicializando página de Gestión de Severidad de Infracciones...');
        
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
                    pageTitle: window.pageConfig?.pageTitle || { icon: 'fas fa-gavel', text: 'Gestión de Severidad de Infracciones' }
                });
                
                // Cargar profile modal en su propio contenedor
                if (!document.getElementById('profile-modal-container')) {
                    const modalRoot = document.getElementById('modal-container');
                    if (modalRoot) {
                        const profileDiv = document.createElement('div');
                        profileDiv.id = 'profile-modal-container';
                        modalRoot.appendChild(profileDiv);
                    }
                }
                await componentLoader.loadComponent('profile-modal', '#profile-modal-container');
                
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
                        
                        // Inicializar controlador principal de InfractionSeverity
                        if (!window.infractionSeverityListController) {
                            window.infractionSeverityListController = new InfractionSeverityListController();
                            window.infractionSeverityController = window.infractionSeverityListController;
                            console.log('⚠️ InfractionSeverityListController inicializado');
                        }
                        // Inicializar controlador de edición de severidad de infracción
                        if (!window.infractionSeverityEditController) {
                            if (window.InfractionSeverityEditControllerClass) {
                                window.infractionSeverityEditController = new window.InfractionSeverityEditControllerClass();
                                console.log('✏️ InfractionSeverityEditController inicializado desde el inicializador');
                            } else {
                                console.error('❌ No se encontró la clase InfractionSeverityEditControllerClass');
                            }
                        }
                        // --- INTEGRACIÓN ELIMINACIÓN ---
                        // Instanciar el service y el controller de eliminación si no existen
                        if (!window.DeleteInfractionSeverityService) {
                            window.DeleteInfractionSeverityService = new DeleteInfractionSeverityService();
                        }
                        window.deleteInfractionSeverityController = new DeleteInfractionSeverityController();

                        // Enlazar evento a los botones de eliminar
                        document.body.addEventListener('click', function(e) {
                            const btn = e.target.closest('[data-action="delete-infraction-severity"]');
                            if (btn) {
                                const id = Number(btn.getAttribute('data-infraction-severity-id'));
                                const name = btn.getAttribute('data-infraction-severity-name') || '';
                                const infractionSeverityData = { id, name };
                                window.deleteInfractionSeverityController.handleDeleteButtonClick(btn, infractionSeverityData);
                            }
                        });
                        // --- FIN INTEGRACIÓN ELIMINACIÓN ---
                    }, 200);
                    
                    // Configurar permisos DESPUÉS de que los controladores estén listos
                    setTimeout(() => {
                        if (window.PermissionsService) {
                            console.log('🔧 Inicializando sistema de permisos...');
                            window.PermissionsService.initializePermissions();
                        }
                        
                        // Notificar que el módulo está listo
                        LoadingScreenUtil.notifyModuleLoaded('InfractionSeverity');
                        
                        console.log('✅ Severidad de Infracciones inicializado completamente');
                    }, 400);
                    
                }, 500);
                
            } catch (error) {
                console.error('❌ Error cargando componentes:', error);
                
                // Notificar que el módulo está listo (incluso con error)
                LoadingScreenUtil.notifyModuleLoaded('InfractionSeverity');
            }
            
        } else {
            console.log('❌ Usuario no autenticado, redirigiendo...');
        }
    }
}

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM cargado, iniciando InfractionSeverityInitializer...');
    
    // Pequeño delay para asegurar que todos los scripts estén cargados
    setTimeout(() => {
        InfractionSeverityInitializer.init();
    }, 500);
});

console.log('📝 InfractionSeverityInitializer definido y configurado');

