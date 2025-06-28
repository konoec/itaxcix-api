/**
 * Inicializador específico para la página de Inicio
 * Maneja solo los controladores necesarios para esta página específica
 */
class InicioInitializer {
    static async init() {
        console.log('🏠 Inicializando página de Inicio...');
        
        if (authChecker.checkAuthentication()) {
            authChecker.updateUserDisplay();
            authChecker.setupLogoutButton();
            
            // Inicializar controladores base necesarios
            if (!window.sidebarControllerInstance) {
                window.sidebarControllerInstance = new SidebarController();
                console.log('📁 SidebarController inicializado');
            }            // Inicializar TopBarController
            if (!window.topBarControllerInstance) {
                window.topBarControllerInstance = new TopBarController();
                console.log('📊 TopBarController inicializado');
            }

            // Inicializar ProfileController de forma INDEPENDIENTE
            if (!window.profileControllerInstance) {
                window.profileControllerInstance = new ProfileController();
                console.log('👤 ProfileController inicializado');
            }            // Inicializar InicioController específico de esta página
            if (!window.inicioController) {
                window.inicioController = new InicioController();
                console.log('🏠 InicioController inicializado');
            }            // Configurar permisos inmediatamente con pantalla de carga
            if (window.PermissionsService) {
                console.log('🔧 Inicializando sistema de permisos...');
                window.PermissionsService.initializePermissions();
            }

            // Configurar verificación de sesión
            setInterval(authChecker.checkTokenExpiration, 60000);
            
            // Esperar un poco para asegurar que todo esté inicializado
            setTimeout(() => {
                console.log('🔄 Verificación final de inicialización...');
                if (window.topBarControllerInstance && window.profileControllerInstance) {
                    console.log('✅ Todos los controladores están disponibles');
                } else {
                    console.warn('⚠️ Algunos controladores pueden no estar completamente inicializados');
                }
            }, 100);
            
            console.log('✅ Página de Inicio completamente inicializada');
        } else {
            console.log('❌ Usuario no autenticado, redirigiendo...');
        }
    }
}

// Auto-inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', InicioInitializer.init);
