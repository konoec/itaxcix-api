/**
 * Inicializador del módulo de Reportes de Auditoría
 * Coordina la inicialización de todos los componentes del módulo
 */
class AuditReportsInitializer {
    static async init() {
        console.log('� Inicializando módulo de Reportes de Auditoría...');
        
        // Verificar autenticación usando el sistema global
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
            
            // Inicializar servicios de reportes si no existen
            if (!window.incidentReportsService && window.IncidentReportsService) {
                window.incidentReportsService = new window.IncidentReportsService();
                console.log('✅ IncidentReportsService inicializado');
            }
            
            if (!window.vehicleReportsService && window.VehicleReportsService) {
                window.vehicleReportsService = new window.VehicleReportsService();
                console.log('✅ VehicleReportsService inicializado');
            }
            
            if (!window.infractionReportsService && window.InfractionReportsService) {
                window.infractionReportsService = new window.InfractionReportsService();
                console.log('✅ InfractionReportsService inicializado');
            }
            
            if (!window.travelReportsService && window.TravelReportsService) {
                window.travelReportsService = new window.TravelReportsService();
                console.log('✅ TravelReportsService inicializado');
            }
            
            // Inicializar controlador principal de reportes de auditoría específico de esta página
            if (!window.auditReportsMainControllerInstance && window.AuditReportsMainController) {
                window.auditReportsMainControllerInstance = new AuditReportsMainController();
                console.log('📊 AuditReportsMainController inicializado');
            }

            // Configurar permisos inmediatamente con pantalla de carga
            if (window.PermissionsService) {
                console.log('🔧 Inicializando sistema de permisos...');
                window.PermissionsService.initializePermissions();
            }

            // Configurar verificación de sesión
            setInterval(authChecker.checkTokenExpiration, 60000);
            
            // Esperar un poco para asegurar que todo esté inicializado
            setTimeout(() => {
                console.log('🎉 Módulo de Reportes de Auditoría completamente inicializado');
            }, 500);
        }
    }
}

// Verificar dependencias críticas antes de inicializar
const dependencies = [
    { name: 'authChecker', obj: window.authChecker },
    { name: 'SidebarController', obj: window.SidebarController },
    { name: 'TopBarController', obj: window.TopBarController },
    { name: 'ProfileController', obj: window.ProfileController },
    { name: 'AuditReportsMainController', obj: window.AuditReportsMainController }
];

let missingDependencies = [];
dependencies.forEach(dep => {
    if (!dep.obj) {
        missingDependencies.push(dep.name);
    }
});

if (missingDependencies.length > 0) {
    console.warn('⚠️ Dependencias faltantes en Reportes de Auditoría:', missingDependencies);
}

// Inicialización automática cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM completamente cargado para Reportes de Auditoría');
    
    // Pequeño delay para asegurar que todos los scripts se hayan cargado
    setTimeout(() => {
        AuditReportsInitializer.init();
    }, 100);
});

console.log('✅ Inicializador de Reportes de Auditoría cargado');
