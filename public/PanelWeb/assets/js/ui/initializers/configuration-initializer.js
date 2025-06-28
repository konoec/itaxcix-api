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
                console.log('👤 ProfileController inicializado');            }            // Los controladores específicos de configuración se auto-inicializan
            // EmergencyContactController, PermissionsController y RolesController
            // se inicializan automáticamente si detectan sus elementos en el DOM
            console.log('⚙️ Controladores de configuración se inicializarán automáticamente');

            // Configurar funcionalidad adicional para permisos
            ConfigurationInitializer.setupPermissionsFeatures();

            // Configurar permisos inmediatamente con pantalla de carga
            if (window.PermissionsService) {
                console.log('🔧 Inicializando sistema de permisos...');
                window.PermissionsService.initializePermissions();
            }

            // Configurar verificación de sesión
            setInterval(authChecker.checkTokenExpiration, 60000);
            
            console.log('✅ Página de Configuration completamente inicializada');
        } else {
            console.log('❌ Usuario no autenticado, redirigiendo...');
        }
    }

    /**
     * Configura funcionalidades adicionales para permisos
     */
    static setupPermissionsFeatures() {
        console.log('📋 Configurando funcionalidades de permisos...');
        
        // Verificar que el servicio de configuración esté disponible
        const checkServices = () => {
            if (window.ConfigurationService) {
                console.log('✅ ConfigurationService disponible');
                
                // Verificar que el controlador esté disponible
                setTimeout(() => {
                    if (window.permissionsController) {
                        console.log('✅ PermissionsController disponible y configurado');
                    } else {
                        console.log('⚠️ PermissionsController no disponible aún');
                    }
                }, 500);
            } else {
                console.log('⚠️ ConfigurationService no disponible, esperando...');
                setTimeout(checkServices, 100);
            }
        };
        
        checkServices();
    }
}

// Auto-inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', ConfigurationInitializer.init);
