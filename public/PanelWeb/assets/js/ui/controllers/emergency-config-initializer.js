/**
 * Inicializador específico para la página de Configuración de Emergencia
 * Maneja solo los controladores necesarios para esta página específica
 */
class EmergencyConfigInitializer {
    static async init() {
        console.log('🚨 Inicializando página de Configuración de Emergencia...');
        
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
                console.log('👤 ProfileController inicializado');
            }

            // Inicializar EmergencyConfigController específico de esta página
            if (!window.emergencyConfigControllerInstance) {
                window.emergencyConfigControllerInstance = new EmergencyConfigController();
                console.log('🚨 EmergencyConfigController inicializado');
            }

            // Configurar verificación de sesión
            setInterval(authChecker.checkTokenExpiration, 60000);
            
            console.log('✅ Página de Configuración de Emergencia completamente inicializada');
        } else {
            console.log('❌ Usuario no autenticado, redirigiendo...');
        }
    }
}

// Auto-inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', EmergencyConfigInitializer.init);
