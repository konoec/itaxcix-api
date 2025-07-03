/**
 * Inicializador para el módulo de gestión de usuarios
 * Versión simplificada y limpia
 */
(function() {
    console.log('🚀 Iniciando users-initializer...');

    // Mostrar overlay de carga
    const loadingOverlay = document.getElementById('permissions-loading');
    if (loadingOverlay) {
        loadingOverlay.classList.remove('hidden');
        loadingOverlay.style.display = 'flex';
        
        // Actualizar textos para usuarios
        const loadingTitle = loadingOverlay.querySelector('.loading-title');
        const loadingMessage = loadingOverlay.querySelector('.loading-message');
        const loadingStep = document.getElementById('loading-step');
        
        if (loadingTitle) loadingTitle.textContent = 'Configurando Usuarios';
        if (loadingMessage) loadingMessage.textContent = 'Estamos configurando tu sesión y verificando los usuarios del sistema...';
        if (loadingStep) loadingStep.textContent = 'Cargando usuarios...';
    }

    // Esperar a que el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        console.log('📄 DOM cargado, inicializando controladores...');
        
        // Esperar un poco más para que todos los servicios se carguen
        setTimeout(() => {
            // Inicializar controladores base
            initializeBaseControllers();
            
            // Inicializar controlador de usuarios
            initializeUsersController();
            
            // Ocultar overlay después de un tiempo
            setTimeout(hideLoadingOverlay, 2000);
        }, 100);
    });

    /**
     * Inicializa los controladores base (sidebar, topbar, profile)
     */
    function initializeBaseControllers() {
        console.log('⚙️ Inicializando controladores base...');
        
        // Sidebar
        if (typeof SidebarController !== 'undefined' && !window.sidebarControllerInstance) {
            window.sidebarControllerInstance = new SidebarController();
            console.log('✅ SidebarController inicializado');
        }
        
        // TopBar
        if (typeof TopBarController !== 'undefined' && !window.topBarControllerInstance) {
            window.topBarControllerInstance = new TopBarController();
            console.log('✅ TopBarController inicializado');
        }
        
        // Profile
        if (typeof ProfileController !== 'undefined' && !window.profileControllerInstance) {
            window.profileControllerInstance = new ProfileController();
            console.log('✅ ProfileController inicializado');
        }
    }

    /**
     * Inicializa el controlador de usuarios
     */
    function initializeUsersController() {
        console.log('👥 Inicializando UsersController...');
        
        // Verificar servicios disponibles
        console.log('🔍 Verificando servicios disponibles...');
        console.log('UserService disponible:', typeof UserService !== 'undefined');
        console.log('RoleService disponible:', typeof RoleService !== 'undefined');
        
        // Verificar que la clase esté disponible
        if (typeof UsersController === 'undefined') {
            console.error('❌ UsersController no está disponible');
            console.error('🔍 Clases disponibles:', Object.keys(window).filter(key => key.includes('Controller')));
            return;
        }
        
        // Crear instancia si no existe
        if (!window.usersController) {
            try {
                window.usersController = new UsersController();
                console.log('✅ UsersController inicializado correctamente');
            } catch (error) {
                console.error('❌ Error al crear UsersController:', error);
                console.error('📋 Stack trace:', error.stack);
            }
        } else {
            console.log('⚠️ UsersController ya existe');
        }
    }

    /**
     * Oculta el overlay de carga
     */
    function hideLoadingOverlay() {
        const loadingOverlay = document.getElementById('permissions-loading');
        if (loadingOverlay) {
            loadingOverlay.classList.add('hidden');
            setTimeout(() => {
                loadingOverlay.style.display = 'none';
            }, 500);
        }
        console.log('✅ Inicialización completa');
    }

})();
