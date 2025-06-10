class AppInitializer {
    static init() {
        if (authChecker.checkAuthentication()) {
            authChecker.updateUserDisplay();
            authChecker.setupLogoutButton();

            // Inicializar controladores solo si no están inicializados
            if (!window.sidebarControllerInstance) {
                window.sidebarControllerInstance = new SidebarController();
            }

            // Inicializar ProfileController
            if (!window.profileControllerInstance) {
                window.profileControllerInstance = new ProfileController();
            }

            if (!window.uiControllerInstance) {
                const app = new UIController();
                app.init();
                window.uiControllerInstance = app;
            }

            // Configurar verificación de sesión
            setInterval(authChecker.checkTokenExpiration, 60000);
        }
    }
}

document.addEventListener('DOMContentLoaded', AppInitializer.init);