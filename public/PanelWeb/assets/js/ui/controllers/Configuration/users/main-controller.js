/**
 * Controlador principal para manejar la inicialización de componentes base
 * Se encarga de cargar sidebar, topbar, perfil y conectarlos correctamente
 */
class MainController {
    constructor() {
        this.isInitialized = false;
        this.componentLoader = null;
        this.loadedComponents = [];
        
        console.log('🎯 MainController creado');
    }

    /**
     * Inicializa todos los componentes base del sistema
     * @param {Object} config - Configuración para los componentes
     * @param {string} config.activeSection - Sección activa del sidebar
     * @param {Object} config.pageTitle - Título de la página para el topbar
     */
    async init(config = {}) {
        if (this.isInitialized) {
            console.log('ℹ️ MainController ya está inicializado');
            return;
        }

        console.log('🎯 Inicializando MainController...');
        console.log('🔍 Verificando dependencias:');
        console.log('- authChecker:', typeof authChecker);
        console.log('- ComponentLoader:', typeof ComponentLoader);
        console.log('- this.componentLoader:', this.componentLoader);
        
        try {
            // Verificar autenticación
            if (!authChecker.checkAuthentication()) {
                console.log('❌ Usuario no autenticado');
                return;
            }

            authChecker.updateUserDisplay();
            authChecker.setupLogoutButton();

            // Inicializar ComponentLoader
            this.componentLoader = new ComponentLoader();

            // Cargar componentes HTML dinámicamente
            console.log('🔄 Cargando componentes base...');
            
            await this.loadSidebar(config.activeSection);
            await this.loadTopbar(config.pageTitle);
            await this.loadProfileModal();

            console.log('✅ Componentes HTML cargados');

            // Inicializar controladores inmediatamente
            console.log('🎛️ Iniciando controladores inmediatamente...');
            this.initializeControllers();

        } catch (error) {
            console.error('❌ Error en MainController init:', error);
            console.error('❌ Error stack:', error.stack);
            console.error('❌ Error message:', error.message);
            // Fallback sin componentes dinámicos
            this.initializeFallback();
        }
    }

    /**
     * Carga el componente sidebar
     */
    async loadSidebar(activeSection = 'configuracion') {
        try {
            await this.componentLoader.loadComponent('sidebar', '#sidebar-container', {
                activeSection: activeSection
            });
            this.loadedComponents.push('sidebar');
            console.log('✅ Sidebar cargado');
        } catch (error) {
            console.error('❌ Error cargando sidebar:', error);
            throw error;
        }
    }

    /**
     * Carga el componente topbar
     */
    async loadTopbar(pageTitle = { icon: 'fas fa-home', text: 'Panel de Control' }) {
        try {
            await this.componentLoader.loadComponent('topbar', '#topbar-container', {
                pageTitle: pageTitle
            });
            this.loadedComponents.push('topbar');
            console.log('✅ Topbar cargado');
        } catch (error) {
            console.error('❌ Error cargando topbar:', error);
            throw error;
        }
    }

    /**
     * Carga el modal de perfil
     */
    async loadProfileModal() {
        try {
            await this.componentLoader.loadComponent('profile-modal', '#modal-container');
            this.loadedComponents.push('profile-modal');
            console.log('✅ Profile modal cargado');
        } catch (error) {
            console.error('❌ Error cargando profile modal:', error);
            throw error;
        }
    }

    /**
     * Inicializa todos los controladores base
     */
    initializeControllers() {
        console.log('🎛️ Inicializando controladores base...');

        // Inicializar SidebarController
        this.initializeSidebarController();
        
        // Inicializar TopBarController
        this.initializeTopBarController();
        
        // Inicializar ProfileController
        this.initializeProfileController();

        // Conectar controladores
        this.connectControllers();

        // Configurar permisos
        this.initializePermissions();

        // Marcar como inicializado
        this.isInitialized = true;
        
        console.log('✅ MainController completamente inicializado');
    }

    /**
     * Inicializa el SidebarController
     */
    initializeSidebarController() {
        if (typeof SidebarController !== 'undefined') {
            if (!window.sidebarControllerInstance) {
                try {
                    window.sidebarControllerInstance = new SidebarController();
                    console.log('📁 SidebarController ✅');
                } catch (error) {
                    console.error('❌ Error en SidebarController:', error);
                }
            }
        } else {
            console.warn('⚠️ SidebarController no disponible');
        }
    }

    /**
     * Inicializa el TopBarController
     */
    initializeTopBarController() {
        if (typeof TopBarController !== 'undefined') {
            if (!window.topBarControllerInstance) {
                try {
                    window.topBarControllerInstance = new TopBarController();
                    console.log('🔝 TopBarController ✅');
                } catch (error) {
                    console.error('❌ Error en TopBarController:', error);
                }
            }
        } else {
            console.warn('⚠️ TopBarController no disponible');
        }
    }

    /**
     * Inicializa el ProfileController
     */
    initializeProfileController() {
        console.log('👤 === INICIALIZANDO PROFILE CONTROLLER ===');
        console.log('- ProfileController definido:', typeof ProfileController);
        console.log('- ProfileService definido:', typeof ProfileService);
        console.log('- window.profileControllerInstance existe:', !!window.profileControllerInstance);
        
        if (typeof ProfileController !== 'undefined') {
            if (!window.profileControllerInstance) {
                try {
                    console.log('👤 Creando nueva instancia de ProfileController...');
                    window.profileControllerInstance = new ProfileController();
                    console.log('👤 ProfileController ✅ - Instancia creada correctamente');
                    console.log('👤 ProfileController métodos disponibles:', Object.getOwnPropertyNames(window.profileControllerInstance));
                } catch (error) {
                    console.error('❌ Error detallado en ProfileController:', error);
                    console.error('❌ Stack trace:', error.stack);
                    console.log('ℹ️ Continuando sin ProfileController...');
                }
            } else {
                console.log('ℹ️ ProfileController ya existe, reutilizando instancia');
            }
        } else {
            console.warn('⚠️ ProfileController no disponible - clase no definida');
            console.log('🔍 Verificando si el script se cargó...');
            console.log('- window.ProfileController:', window.ProfileController);
        }
        
        console.log('👤 === FIN INICIALIZACIÓN PROFILE CONTROLLER ===');
    }

    /**
     * Conecta los controladores entre sí
     */
    connectControllers() {
        // Conectar ProfileController con TopBarController
        if (window.topBarControllerInstance && window.profileControllerInstance) {
            window.topBarControllerInstance.profileController = window.profileControllerInstance;
            console.log('🔗 TopBar-Profile conectados');
        }

        // Configurar event listener para el botón de perfil
        setTimeout(() => {
            this.setupProfileButton();
        }, 200);
    }

    /**
     * Configura el botón de perfil en el topbar
     */
    setupProfileButton() {
        const viewProfileBtn = document.getElementById('view-profile');
        if (viewProfileBtn && window.profileControllerInstance) {
            viewProfileBtn.addEventListener('click', (e) => {
                e.preventDefault();
                console.log('👤 Abriendo modal de perfil...');
                
                if (typeof window.profileControllerInstance.openModal === 'function') {
                    window.profileControllerInstance.openModal();
                } else {
                    console.warn('⚠️ ProfileController.openModal no disponible');
                }
            });
            console.log('✅ Botón de perfil configurado');
        } else {
            console.warn('⚠️ Botón de perfil o ProfileController no encontrado');
        }
    }

    /**
     * Inicializa el sistema de permisos
     */
    initializePermissions() {
        setTimeout(() => {
            if (window.PermissionsService) {
                try {
                    window.PermissionsService.initializePermissions();
                    console.log('🔧 Permisos ✅');
                } catch (error) {
                    console.error('❌ Error en permisos:', error);
                }
            } else {
                console.warn('⚠️ PermissionsService no disponible');
            }
        }, 300);
    }

    /**
     * Inicialización de respaldo sin componentes dinámicos
     */
    initializeFallback() {
        console.log('🔄 Modo fallback - inicializando sin componentes dinámicos...');
        
        setTimeout(() => {
            this.initializeSidebarController();
            this.initializeTopBarController();
            this.initializeProfileController();
            this.connectControllers();
            this.initializePermissions();
            
            this.isInitialized = true;
            console.log('✅ MainController inicializado en modo fallback');
        }, 100);
    }

    /**
     * Verifica si los controladores base están listos
     */
    areBaseControllersReady() {
        return !!(window.sidebarControllerInstance && 
                  window.topBarControllerInstance  &&   
                  window.profileControllerInstance);
    }

    /**
     * Obtiene el estado de inicialización
     */
    getStatus() {
        return {
            isInitialized: this.isInitialized,
            loadedComponents: this.loadedComponents,
            controllers: {
                sidebar: !!window.sidebarControllerInstance,
                topbar: !!window.topBarControllerInstance,
                profile: !!window.profileControllerInstance
            }
        };
    }
}

// Crear instancia global
try {
    window.mainController = new MainController();
    console.log('🎯 MainController creado exitosamente');
} catch (error) {
    console.error('❌ Error creando MainController:', error);
}

console.log('🎯 MainController definido y listo');
