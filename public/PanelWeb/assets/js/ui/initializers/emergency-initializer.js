// Inicializador dedicado para el módulo de emergencia
(function() {
    // Mostrar overlay de carga si existe
    const loadingOverlay = document.getElementById('permissions-loading');
    if (loadingOverlay) {
        loadingOverlay.classList.remove('hidden');
        loadingOverlay.style.display = 'flex';
    }

    // Cambiar textos para emergencia
    const loadingTitle = loadingOverlay?.querySelector('.loading-title');
    if (loadingTitle) loadingTitle.textContent = 'Configurando Emergencia';
    const loadingMsg = loadingOverlay?.querySelector('.loading-message');
    if (loadingMsg) loadingMsg.textContent = 'Estamos configurando tu sesión y verificando la configuración de emergencia...';
    const loadingStep = document.getElementById('loading-step');
    if (loadingStep) loadingStep.textContent = 'Validando credenciales...';

    // Inicializar controladores base y específico al cargar el DOM
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
        if (!window.emergencyContactControllerInstance) {
            window.emergencyContactControllerInstance = new EmergencyContactController();
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
