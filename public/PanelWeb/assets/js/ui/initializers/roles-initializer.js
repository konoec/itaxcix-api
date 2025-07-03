// Inicializador dedicado para el módulo de roles
(function() {
    // Mostrar overlay de carga
    const loadingOverlay = document.getElementById('permissions-loading');
    if (loadingOverlay) {
        loadingOverlay.classList.remove('hidden');
        loadingOverlay.style.display = 'flex';
    }

    // Cambiar textos para roles
    const loadingTitle = loadingOverlay?.querySelector('.loading-title');
    if (loadingTitle) loadingTitle.textContent = 'Configurando Roles';
    const loadingMsg = loadingOverlay?.querySelector('.loading-message');
    if (loadingMsg) loadingMsg.textContent = 'Estamos configurando tu sesión y verificando los roles asignados...';
    const loadingStep = document.getElementById('loading-step');
    if (loadingStep) loadingStep.textContent = 'Cargando roles...';

    // Inicializar controladores base (sidebar, topbar, perfil)
    document.addEventListener('DOMContentLoaded', () => {
        if (!window.sidebarControllerInstance) {
            window.sidebarControllerInstance = new SidebarController();
        }
        if (!window.topBarControllerInstance) {
            window.topBarControllerInstance = new TopBarController();
        }
        if (!window.profileControllerInstance) {
            window.profileControllerInstance = new ProfileController();
        }
        // Inicializar controlador específico de roles
        console.log('🔐 Inicializando RolesController desde roles-initializer...');
        if (!window.rolesControllerInstance && typeof RolesController !== 'undefined') {
            console.log('✅ Creando nueva instancia de RolesController');
            window.rolesControllerInstance = RolesController;
            // También crear referencia global para compatibilidad
            window.rolesController = RolesController;
            
            if (typeof RolesController.init === 'function') {
                RolesController.init();
                console.log('✅ RolesController inicializado correctamente');
            }
        } else if (window.rolesControllerInstance) {
            console.log('⚠️ RolesController ya existe, no se crea nueva instancia');
        } else {
            console.error('❌ RolesController no está disponible');
        }
        
        // NO iniciar monitoreo local - el sistema global se encarga del monitoreo
        // El GlobalUserMonitor ya se encarga de verificar el estado del usuario
        // if (typeof initUserStatusMonitoring === 'function') {
        //     initUserStatusMonitoring();
        // }
        
        // Ocultar overlay tras 1200ms
        setTimeout(() => {
            if (loadingOverlay) {
                loadingOverlay.classList.add('hidden');
                setTimeout(() => loadingOverlay.style.display = 'none', 500);
            }
        }, 1200);
    });
})();
