/**
 * Controlador principal para el módulo de Auditoría
 * Se encarga de cargar sidebar, topbar, perfil y conectarlos correctamente
 */
class AuditMainController {
    constructor() {
        this.isInitialized = false;
        this.componentLoader = null;
        this.loadedComponents = [];
        
        console.log('🎯 AuditMainController creado');
    }

    /**
     * Inicializa todos los componentes base del sistema
     * @param {Object} config - Configuración para los componentes
     * @param {string} config.activeSection - Sección activa del sidebar
     * @param {string} config.activeSubSection - Subsección activa del sidebar
     * @param {Object} config.pageTitle - Título de la página para el topbar
     */
    async init(config = {}) {
        if (this.isInitialized) {
            console.log('ℹ️ AuditMainController ya está inicializado');
            return;
        }

        console.log('🎯 Inicializando AuditMainController...');
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
            
            await this.loadSidebar(config.activeSection, config.activeSubSection);
            await this.loadTopbar(config.pageTitle);
            await this.loadProfileModal();

            console.log('✅ Componentes HTML cargados');

            // Inicializar controladores inmediatamente
            console.log('🎛️ Iniciando controladores inmediatamente...');
            this.initializeControllers();

        } catch (error) {
            console.error('❌ Error en AuditMainController init:', error);
            console.error('❌ Error stack:', error.stack);
            console.error('❌ Error message:', error.message);
            // Fallback sin componentes dinámicos
            this.initializeFallback();
        }
    }

    /**
     * Carga el componente sidebar
     */
    async loadSidebar(activeSection = 'auditoria', activeSubSection = 'registro') {
        try {
            await this.componentLoader.loadComponent('sidebar', '#sidebar-container', {
                activeSection: activeSection,
                activeSubSection: activeSubSection
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
    async loadTopbar(pageTitle = { icon: 'fas fa-history', text: 'Registro de Auditoría' }) {
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
            await this.componentLoader.loadComponent('profile-modal', '#profile-modal-container');
            this.loadedComponents.push('profile-modal');
            console.log('✅ Profile modal cargado');
        } catch (error) {
            console.error('❌ Error cargando profile modal:', error);
            throw error;
        }
    }

    /**
     * Inicializa todos los controladores después de cargar los componentes
     */
    initializeControllers() {
        console.log('🎛️ === INICIALIZANDO CONTROLADORES ===');
        
        this.initializeSidebarController();
        this.initializeTopBarController();
        this.initializeProfileController();
        
        // Inicializar controladores específicos del módulo de auditoría
        this.initializeAuditControllers();
        
        this.isInitialized = true;
        console.log('✅ Todos los controladores inicializados');
    }

    /**
     * Inicializa el SidebarController
     */
    initializeSidebarController() {
        if (typeof SidebarController !== 'undefined') {
            if (!window.sidebarController) {
                try {
                    window.sidebarController = new SidebarController();
                    console.log('📋 SidebarController ✅');
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
            if (!window.topbarController) {
                try {
                    window.topbarController = new TopBarController();
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
        console.log('- window.profileController existe:', !!window.profileController);
        
        if (typeof ProfileController !== 'undefined') {
            if (!window.profileController) {
                try {
                    console.log('👤 Creando nueva instancia de ProfileController...');
                    window.profileController = new ProfileController();
                    console.log('👤 ProfileController ✅ - Instancia creada correctamente');
                    console.log('👤 ProfileController métodos disponibles:', Object.getOwnPropertyNames(window.profileController));
                } catch (error) {
                    console.error('❌ Error detallado en ProfileController:', error);
                    console.error('❌ Stack trace:', error.stack);
                    console.log('ℹ️ Continuando sin ProfileController...');
                }
            } else {
                console.log('ℹ️ ProfileController ya existe, reutilizando instancia');
            }
        } else {
            console.warn('⚠️ ProfileController no disponible');
        }
    }

    /**
     * Inicializa los controladores específicos del módulo de auditoría
     */
    initializeAuditControllers() {
        console.log('🔍 === INICIALIZANDO CONTROLADORES DE AUDITORÍA ===');
        
        // AuditRegistryController
        if (typeof AuditRegistryController !== 'undefined') {
            if (!window.auditRegistryController) {
                try {
                    console.log('🔍 Creando nueva instancia de AuditRegistryController...');
                    window.auditRegistryController = new AuditRegistryController();
                    console.log('🔍 AuditRegistryController ✅ - Instancia creada correctamente');
                } catch (error) {
                    console.error('❌ Error detallado en AuditRegistryController:', error);
                    console.error('❌ Stack trace:', error.stack);
                    console.log('ℹ️ Continuando sin AuditRegistryController...');
                }
            } else {
                console.log('ℹ️ AuditRegistryController ya existe, reutilizando instancia');
            }
        } else {
            console.warn('⚠️ AuditRegistryController no disponible');
        }
    }

    /**
     * Fallback si no se pueden cargar los componentes dinámicamente
     */
    initializeFallback() {
        console.warn('⚠️ Usando modo fallback - inicializando controladores sin cargar componentes dinámicos');
        
        setTimeout(() => {
            this.initializeControllers();
        }, 500);
    }

    /**
     * Verifica si los controladores base están listos
     */
    areBaseControllersReady() {
        return !!(window.sidebarController && 
                  window.topbarController &&
                  window.profileController);
    }

    /**
     * Verifica si está inicializado
     */
    getInitializationStatus() {
        return {
            isInitialized: this.isInitialized,
            loadedComponents: this.loadedComponents,
            hasComponentLoader: !!this.componentLoader
        };
    }
}

// Crear instancia global
if (typeof window !== 'undefined') {
    window.auditMainController = new AuditMainController();
    window.AuditMainController = AuditMainController;
    console.log('✅ AuditMainController disponible globalmente');
}

console.log('✅ AuditMainController loaded');
