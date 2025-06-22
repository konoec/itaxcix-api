/**
 * Inicializador específico para la página de Configuration
 * Maneja solo los controladores necesarios para esta página específica
 */
class ConfigurationInitializer {
    static async init() {
        console.log('⚙️ Inicializando página de Configuration...');
        
        if (authChecker.checkAuthentication()) {
            authChecker.updateUserDisplay();
            authChecker.setupLogoutButton();
            
            // Inicializar controladores base necesarios
            if (!window.sidebarControllerInstance) {
                window.sidebarControllerInstance = new SidebarController();
                console.log('📁 SidebarController inicializado');
            }

            // Inicializar TopBarController
            if (!window.topBarControllerInstance) {
                window.topBarControllerInstance = new TopBarController();
                console.log('📊 TopBarController inicializado');
            }

            // Inicializar ProfileController de forma INDEPENDIENTE
            if (!window.profileControllerInstance) {
                window.profileControllerInstance = new ProfileController();
                console.log('👤 ProfileController inicializado');            }            // Inicializar ConfigurationController específico de esta página
            if (!window.configurationController) {
                window.configurationController = new ConfigurationController();
                console.log('⚙️ ConfigurationController inicializado');
            }

            // Configurar verificación de sesión
            setInterval(authChecker.checkTokenExpiration, 60000);
            
            console.log('✅ Página de Configuration completamente inicializada');
        } else {
            console.log('❌ Usuario no autenticado, redirigiendo...');
        }
    }
}

// Auto-inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', ConfigurationInitializer.init);
