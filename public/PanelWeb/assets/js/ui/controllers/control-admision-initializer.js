/**
 * Inicializador específico para la página de Control de Admisión de Conductores
 * Maneja solo los controladores necesarios para esta página específica
 */
class ControlAdmisionInitializer {
    static async init() {
        console.log('🚗 Inicializando página de Control de Admisión de Conductores...');
        
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
            }            // Inicializar ControlAdmisionController específico para control de admisión
            if (!window.controlAdmisionControllerInstance) {
                // Verificar que ControlAdmisionController existe antes de instanciarlo
                if (typeof ControlAdmisionController !== 'undefined') {
                    const app = new ControlAdmisionController();
                    // NO ESPERAR - cargar conductores independientemente del perfil
                    app.init().catch(error => {
                        console.error('❌ Error cargando conductores:', error);
                    });
                    window.controlAdmisionControllerInstance = app;
                    console.log('🚗 ControlAdmisionController inicializado');
                } else {
                    console.warn('⚠️ ControlAdmisionController no está definido en esta página');
                }
            }

            // Configurar verificación de sesión
            setInterval(authChecker.checkTokenExpiration, 60000);
            
            console.log('✅ Página de Control de Admisión completamente inicializada');
        } else {
            console.log('❌ Usuario no autenticado, redirigiendo...');
        }
    }
}

// Auto-inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', ControlAdmisionInitializer.init);
