/**
 * Inicializador específico para la página de Gestión de Permisos
 * Maneja la carga dinámica de componentes y controladores siguiendo el patrón modular
 */
class PermissionsInitializer {
    static async init() {
        console.log('🔐 Inicializando página de Gestión de Permisos...');
        
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
                    activeSection: window.pageConfig?.activeSection || 'configuracion'
                });
                
                // Cargar topbar
                await componentLoader.loadComponent('topbar', '#topbar-container', {
                    pageTitle: window.pageConfig?.pageTitle || { icon: 'fas fa-shield-alt', text: 'Gestión de Permisos' }
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
                            console.log('� TopBarController inicializado');
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
                    
                    // Inicializar PermissionsController específico para gestión de permisos
                    if (!window.permissionsControllerInstance) {
                        // Verificar que PermissionsController existe antes de instanciarlo
                        if (typeof PermissionsController !== 'undefined') {
                            window.permissionsControllerInstance = new PermissionsController();
                            // Crear referencia global para compatibilidad
                            window.permissionsController = window.permissionsControllerInstance;
                            console.log('🔐 PermissionsController inicializado');
                        } else {
                            console.warn('⚠️ PermissionsController no está definido en esta página');
                        }
                    }
                    
                    // Configurar permisos DESPUÉS de que todos los controladores estén listos
                    setTimeout(() => {
                        if (window.PermissionsService) {
                            console.log('🔧 Inicializando sistema de permisos...');
                            window.PermissionsService.initializePermissions();
                        }
                        
                        console.log('✅ Gestión de Permisos inicializada completamente');
                        
                        // Notificar que este módulo ha terminado de cargar
                        LoadingScreenUtil.notifyModuleLoaded('PermissionsManagement');
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
     * Muestra la pantalla de carga específica para permisos
     */
    static showLoadingScreen() {
        const loadingOverlay = document.getElementById('permissions-loading');
        if (loadingOverlay) {
            loadingOverlay.style.display = 'flex';
            loadingOverlay.classList.remove('hidden');
            
            // Actualizar textos específicos para permisos
            const loadingTitle = loadingOverlay.querySelector('.loading-title');
            if (loadingTitle) loadingTitle.textContent = 'Configurando Permisos';
            
            const loadingMsg = loadingOverlay.querySelector('.loading-message');
            if (loadingMsg) loadingMsg.textContent = 'Estamos configurando tu sesión y verificando los permisos asignados...';
            
            const loadingStep = document.getElementById('loading-step');
            if (loadingStep) loadingStep.textContent = 'Cargando componentes...';
        }
    }

    /**
     * Oculta la pantalla de carga
     */
    static hideLoadingScreen() {
        const loadingOverlay = document.getElementById('permissions-loading');
        if (loadingOverlay) {
            loadingOverlay.classList.add('hidden');
            setTimeout(() => {
                loadingOverlay.style.display = 'none';
            }, 500);
        }
    }

    /**
     * Método fallback para inicializar controladores sin carga dinámica
     */
    static initializeFallback() {
        console.log('🔄 Iniciando modo fallback sin componentes dinámicos...');
        
        // Inicializar controladores básicos
        if (!window.sidebarControllerInstance) {
            window.sidebarControllerInstance = new SidebarController();
            console.log('📁 SidebarController inicializado (fallback)');
        }

        if (!window.topBarControllerInstance) {
            window.topBarControllerInstance = new TopBarController();
            console.log('📊 TopBarController inicializado (fallback)');
        }

        if (!window.profileControllerInstance) {
            window.profileControllerInstance = new ProfileController();
            console.log('👤 ProfileController inicializado (fallback)');
        }

        // Inicializar PermissionsController
        if (!window.permissionsControllerInstance && typeof PermissionsController !== 'undefined') {
            window.permissionsControllerInstance = new PermissionsController();
            window.permissionsController = window.permissionsControllerInstance;
            console.log('🔐 PermissionsController inicializado (fallback)');
        }

        // Configurar permisos
        if (window.PermissionsService) {
            console.log('🔧 Inicializando sistema de permisos (fallback)...');
            window.PermissionsService.initializePermissions();
        }

        // Ocultar pantalla de carga
        PermissionsInitializer.hideLoadingScreen();
    }
}

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM cargado, iniciando PermissionsInitializer...');
    
    // Pequeño delay para asegurar que todos los scripts estén cargados
    setTimeout(() => {
        PermissionsInitializer.init();
    }, 500);
});

console.log('📝 PermissionsInitializer definido y configurado');

