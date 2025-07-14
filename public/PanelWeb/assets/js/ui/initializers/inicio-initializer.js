/**
 * Inicializador específico para la página de Inicio
 * Maneja solo los controladores necesarios para esta página específica
 */
class InicioInitializer {
    static async init() {
        console.log('🏠 Inicializando página de Inicio...');
        
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
                    activeSection: window.pageConfig?.activeSection || 'inicio'
                });
                
                // Cargar topbar
                await componentLoader.loadComponent('topbar', '#topbar-container', {
                    pageTitle: window.pageConfig?.pageTitle || { icon: 'fas fa-home', text: 'Inicio' }
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
                    
                    // Inicializar controlador específico de inicio
                    setTimeout(() => {
                        InicioInitializer.initializeInicioModule(() => {
                            // Configurar permisos DESPUÉS de que el módulo específico esté listo
                            if (window.PermissionsService) {
                                console.log('🔧 Inicializando sistema de permisos...');
                                window.PermissionsService.initializePermissions();
                            }
                            
                            console.log('✅ Inicio inicializado completamente');
                            
                            // Notificar que este módulo ha terminado de cargar
                            LoadingScreenUtil.notifyModuleLoaded('Inicio');
                        });
                    }, 100);
                    
                }, 500);
                
            } catch (error) {
                console.error('❌ Error cargando componentes:', error);
            }
            
        } else {
            console.log('❌ Usuario no autenticado, redirigiendo...');
        }
    }

    /**
     * Función de inicialización principal del módulo de inicio
     * @param {Function} callback - Función a ejecutar cuando termine la inicialización
     */
    static initializeInicioModule(callback = null) {
        console.log('🏠 Inicializando módulo de inicio...');
        
        try {
            // Verificar que las dependencias estén disponibles
            if (typeof DashboardController === 'undefined') {
                throw new Error('DashboardController no está disponible');
            }
            
            // Verificar que el DOM tenga los elementos necesarios
            if (!document.getElementById('dashboard-content')) {
                throw new Error('Elementos del DOM no encontrados. Verifique que la página esté completamente cargada.');
            }
            
            // Agregar un pequeño delay para asegurar que todos los elementos DOM estén listos
            setTimeout(() => {
                // Inicializar DashboardController específico de esta página
                if (!window.dashboardControllerInstance) {
                    window.dashboardControllerInstance = new DashboardController();
                    console.log('📊 DashboardController inicializado');
                }
                
                // Configurar eventos globales específicos del módulo
                InicioInitializer.setupGlobalEvents();
                
                // Configurar atajos de teclado
                InicioInitializer.setupKeyboardShortcuts();
                
                // Inicializar tooltips de Tabler
                InicioInitializer.initializeTooltips();
                
                console.log('✅ Módulo de inicio inicializado correctamente');
                
                // Notificar que el módulo está listo
                if (window.showToast) {
                    window.showToast('Dashboard cargado correctamente', 'success');
                }
                
                // Ejecutar callback si se proporcionó
                if (callback && typeof callback === 'function') {
                    callback();
                }
            }, 100);
            
        } catch (error) {
            console.error('❌ Error al inicializar módulo de inicio:', error);
            
            // Mostrar error al usuario
            if (window.showToast) {
                window.showToast('Error al cargar el dashboard: ' + error.message, 'error');
            }
            
            // Ejecutar callback incluso en caso de error
            if (callback && typeof callback === 'function') {
                callback();
            }
        }
    }

    /**
     * Configurar eventos globales específicos del módulo
     */
    static setupGlobalEvents() {
        console.log('🔧 Configurando eventos globales del módulo de inicio...');
        
        // Configurar botón de actualización del dashboard
        const refreshBtn = document.getElementById('dashboard-refresh-btn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => {
                if (window.dashboardControllerInstance) {
                    window.dashboardControllerInstance.refreshDashboard();
                }
            });
        }
        
        // Configurar botón de reintento en caso de error
        const retryBtn = document.getElementById('dashboard-error-retry');
        if (retryBtn) {
            retryBtn.addEventListener('click', () => {
                if (window.dashboardControllerInstance) {
                    window.dashboardControllerInstance.loadDashboard();
                }
            });
        }
    }

    /**
     * Configurar atajos de teclado específicos del módulo
     */
    static setupKeyboardShortcuts() {
        console.log('⌨️ Configurando atajos de teclado del módulo de inicio...');
        
        document.addEventListener('keydown', (event) => {
            // Ctrl+R para actualizar dashboard
            if (event.ctrlKey && event.key === 'r') {
                event.preventDefault();
                if (window.dashboardControllerInstance) {
                    window.dashboardControllerInstance.refreshDashboard();
                }
            }
        });
    }

    /**
     * Inicializar tooltips de Tabler
     */
    static initializeTooltips() {
        console.log('💡 Inicializando tooltips...');
        
        // Inicializar tooltips de Tabler si están disponibles
        if (window.bootstrap && window.bootstrap.Tooltip) {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new window.bootstrap.Tooltip(tooltipTriggerEl);
            });
        }
    }
}

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM cargado, iniciando InicioInitializer...');
    
    // Pequeño delay para asegurar que todos los scripts estén cargados
    setTimeout(() => {
        InicioInitializer.init();
    }, 500);
});

console.log('📝 InicioInitializer definido y configurado');

