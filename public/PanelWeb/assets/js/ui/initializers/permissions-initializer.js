// assets/js/ui/initializers/permissions-initializer.js
// Inicializador dedicado para el módulo de gestión de permisos
// Muestra la pantalla de carga y delega la inicialización al controlador de permisos y los componentes base

(function() {
    // Mostrar overlay de carga
    const loadingOverlay = document.getElementById('permissions-loading');
    if (loadingOverlay) {
        loadingOverlay.classList.remove('hidden');
        loadingOverlay.style.display = 'flex';
    }

    // Cambiar textos para permisos
    const loadingTitle = loadingOverlay?.querySelector('.loading-title');
    if (loadingTitle) loadingTitle.textContent = 'Configurando Permisos';
    const loadingMsg = loadingOverlay?.querySelector('.loading-message');
    if (loadingMsg) loadingMsg.textContent = 'Estamos configurando tu sesión y verificando los permisos asignados...';
    const loadingStep = document.getElementById('loading-step');
    if (loadingStep) loadingStep.textContent = 'Cargando permisos...';

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
        // Inicializar controlador específico de permisos
        console.log('🔐 Inicializando PermissionsController desde permissions-initializer...');
        if (!window.permissionsControllerInstance) {
            window.permissionsControllerInstance = new PermissionsController();
            // Crear referencia global para compatibilidad con onclick en HTML
            window.permissionsController = window.permissionsControllerInstance;
        } else {
            console.log('⚠️ PermissionsController ya existe, no se crea nueva instancia');
            // Asegurar que la referencia global existe
            if (!window.permissionsController) {
                window.permissionsController = window.permissionsControllerInstance;
            }
        }
        // Ocultar overlay tras 1200ms
        setTimeout(() => {
            if (loadingOverlay) {
                loadingOverlay.classList.add('hidden');
                setTimeout(() => loadingOverlay.style.display = 'none', 500);
            }
        }, 1200);
    });
})();
