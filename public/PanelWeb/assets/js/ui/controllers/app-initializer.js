class AppInitializer {
    static async init() {
        if (authChecker.checkAuthentication()) {
            authChecker.updateUserDisplay();
            authChecker.setupLogoutButton();

            // Inicializar controladores solo si no están inicializados
            if (!window.sidebarControllerInstance) {
                window.sidebarControllerInstance = new SidebarController();
            }            // Inicializar ProfileController de forma INDEPENDIENTE (no bloquea nada)
            if (!window.profileControllerInstance) {
                window.profileControllerInstance = new ProfileController();
                // Ya no necesitamos llamar init() aquí porque se llama automáticamente en el constructor
                console.log('📋 ProfileController creado independientemente');
            }

            // Inicializar UIController de forma INDEPENDIENTE
            if (!window.uiControllerInstance) {
                const app = new UIController();
                // NO ESPERAR - cargar conductores independientemente del perfil
                app.init().catch(error => {
                    console.error('❌ Error cargando conductores:', error);
                });
                window.uiControllerInstance = app;
            }

            // Configurar verificación de sesión
            setInterval(authChecker.checkTokenExpiration, 60000);
        }
    }
}

document.addEventListener('DOMContentLoaded', AppInitializer.init);