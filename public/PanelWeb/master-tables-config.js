/**
 * Configuración de los módulos de tablas maestras
 * Cada módulo debe tener un archivo HTML y un inicializador JS
 */

const masterTablesModules = [
    {
        name: 'Brand',
        title: 'Marcas',
        icon: 'fas fa-tags',
        fileName: 'brand',
        className: 'BrandInitializer',
        console: '🏷️'
    },
    {
        name: 'Category',
        title: 'Categorías',
        icon: 'fas fa-th-large',
        fileName: 'category',
        className: 'CategoryInitializer',
        console: '📂'
    },
    {
        name: 'Color',
        title: 'Colores',
        icon: 'fas fa-palette',
        fileName: 'color',
        className: 'ColorInitializer',
        console: '🎨'
    },
    {
        name: 'ContactTypes',
        title: 'Tipos de Contacto',
        icon: 'fas fa-address-book',
        fileName: 'contact-types',
        className: 'ContactTypesInitializer',
        console: '📞'
    },
    {
        name: 'Departaments',
        title: 'Departamentos',
        icon: 'fas fa-building',
        fileName: 'departaments',
        className: 'DepartamentsInitializer',
        console: '🏢'
    },
    {
        name: 'DocumentTypes',
        title: 'Tipos de Documento',
        icon: 'fas fa-file-alt',
        fileName: 'document-types',
        className: 'DocumentTypesInitializer',
        console: '📄'
    },
    {
        name: 'DriverStatus',
        title: 'Estados de Conductor',
        icon: 'fas fa-user-tie',
        fileName: 'driver-status',
        className: 'DriverStatusInitializer',
        console: '🚗'
    },
    {
        name: 'FuelType',
        title: 'Tipos de Combustible',
        icon: 'fas fa-gas-pump',
        fileName: 'fuel-type',
        className: 'FuelTypeInitializer',
        console: '⛽'
    },
    {
        name: 'IncidentType',
        title: 'Tipos de Incidente',
        icon: 'fas fa-exclamation-triangle',
        fileName: 'incident-type',
        className: 'IncidentTypeInitializer',
        console: '⚠️'
    },
    {
        name: 'InfractionSeverity',
        title: 'Severidad de Infracciones',
        icon: 'fas fa-balance-scale',
        fileName: 'infraction-severity',
        className: 'InfractionSeverityInitializer',
        console: '⚖️'
    },
    {
        name: 'InfractionStatus',
        title: 'Estados de Infracción',
        icon: 'fas fa-gavel',
        fileName: 'infraction-status',
        className: 'InfractionStatusInitializer',
        console: '🔨'
    },
    {
        name: 'ProcedureTypes',
        title: 'Tipos de Procedimiento',
        icon: 'fas fa-clipboard-list',
        fileName: 'procedure-types',
        className: 'ProcedureTypesInitializer',
        console: '📋'
    },
    {
        name: 'Province',
        title: 'Provincias',
        icon: 'fas fa-map',
        fileName: 'province',
        className: 'ProvinceInitializer',
        console: '🗺️'
    },
    {
        name: 'ServiceType',
        title: 'Tipos de Servicio',
        icon: 'fas fa-concierge-bell',
        fileName: 'service-type',
        className: 'ServiceTypeInitializer',
        console: '🛎️'
    },
    {
        name: 'TravelStatus',
        title: 'Estados de Viaje',
        icon: 'fas fa-route',
        fileName: 'travel-status',
        className: 'TravelStatusInitializer',
        console: '🛣️'
    },
    {
        name: 'TucModality',
        title: 'Modalidades TUC',
        icon: 'fas fa-id-card',
        fileName: 'tuc-modality',
        className: 'TucModalityInitializer',
        console: '🆔'
    },
    {
        name: 'TucStatus',
        title: 'Estados TUC',
        icon: 'fas fa-id-badge',
        fileName: 'tuc-status',
        className: 'TucStatusInitializer',
        console: '🏷️'
    },
    {
        name: 'UserCodeType',
        title: 'Tipos de Código de Usuario',
        icon: 'fas fa-user-tag',
        fileName: 'user-code-type',
        className: 'UserCodeTypeInitializer',
        console: '🏷️'
    },
    {
        name: 'UserStatus',
        title: 'Estados de Usuario',
        icon: 'fas fa-user-check',
        fileName: 'user-status',
        className: 'UserStatusInitializer',
        console: '👤'
    },
    {
        name: 'VehicleClass',
        title: 'Clases de Vehículo',
        icon: 'fas fa-car',
        fileName: 'vehicle-class',
        className: 'VehicleClassInitializer',
        console: '🚙'
    },
    {
        name: 'VehicleModel',
        title: 'Modelos de Vehículo',
        icon: 'fas fa-car-side',
        fileName: 'vehicle-model',
        className: 'VehicleModelInitializer',
        console: '🚗'
    }
];

// Función para generar el HTML template
function generateHTMLTemplate(module) {
    return `<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Configuración básica del documento -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Configuración de caché para prevenir almacenamiento de datos sensibles -->
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="../../assets/Recourse/Logo/1.png">
    <link rel="shortcut icon" href="../../assets/Recourse/Logo/1.png">
    
    <title>Panel de Administración - ${module.title}</title>
    
    <!-- Tabler CSS Framework -->
    <link rel="stylesheet" href="../../assets/tabler/css/tabler.min.css">
    <link rel="stylesheet" href="../../assets/tabler/css/tabler-vendors.min.css">
    
    <!-- Global toast styles -->
    <link rel="stylesheet" href="../../assets/css/global-toast.css">
    
    <!-- Component specific styles -->
    <link rel="stylesheet" href="../../assets/css/sidebar.css">
    <link rel="stylesheet" href="../../assets/css/topbar.css">
    <link rel="stylesheet" href="../../assets/css/profile.css">
    
    <!-- ${module.name} specific styles -->
    <link rel="stylesheet" href="../../assets/css/mastertables/companies.css">
    
    <!-- Font Awesome para iconografía -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Configuración de la página -->
    <script>
        window.pageConfig = {
            activeSection: 'tablas', // Sección activa en el sidebar
            pageTitle: {
                icon: '${module.icon}',
                text: 'Gestión de ${module.title}'
            }
        };
    </script>
</head>
<body>
    <!-- Pantalla de carga para configuración de permisos -->
    <div id="permissions-loading" class="permissions-loading-overlay">
        <div class="loading-content">
            <div class="loading-car">🚕</div>
            <h2 class="loading-title">Cargando Panel</h2>
            <p class="loading-message">
                Estamos configurando tu sesión y verificando los permisos asignados...
            </p>
            <p class="loading-steps">
                <span id="loading-step">Validando credenciales...</span>
            </p>
        </div>
    </div>

    <!-- Layout principal usando estructura Tabler -->
    <div class="page">
        <!-- Contenedor del sidebar - se carga dinámicamente -->
        <div id="sidebar-container"></div>

        <!-- Contenido principal usando estructura Tabler -->
        <div class="page-wrapper">
            <!-- Contenedor del topbar - se carga dinámicamente -->
            <div id="topbar-container"></div>
            
            <!-- Page content area - Se implementará contenido específico aquí -->
            <div class="page-body">
                <div class="container-xl">
                    <div class="text-center py-5">
                        <h2 class="text-muted">Gestión de ${module.title}</h2>
                        <p class="text-muted">El contenido específico se implementará aquí</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenedor del modal - se carga dinámicamente -->
    <div id="modal-container"></div>

    <!-- Carga de scripts en orden específico para manejar dependencias -->
    <!-- 1. Component Loader (debe ir primero) -->
    <script src="../../assets/js/ui/component-loader.js"></script>
    
    <!-- 2. Utilidades y herramientas base -->
    <script src="../../assets/js/global-initializer.js"></script>
    <script src="../../assets/js/ui/global-toast.js"></script>
    <script src="../../assets/js/auth/permissions.js"></script>
    <script src="../../assets/js/auth/auth-checker.js"></script>
    <script src="../../assets/js/auth/route-guard.js"></script>
    
    <script>
        // Inicializa RouteGuard para proteger la página
        window.routeGuard = new RouteGuard("../../index.html");
    </script>
    
    <!-- 2. Servicios de API básicos -->
    <script src="../../assets/js/api/services/profile-service.js"></script>

    <!-- 3. Controladores de UI -->
    <script src="../../assets/js/ui/controllers/Components/sidebar-controller.js"></script>
    <script src="../../assets/js/ui/controllers/Components/topbar-controller.js"></script>
    <script src="../../assets/js/ui/controllers/Components/profile-controller.js"></script>

    <!-- 4. Inicializador específico de la página -->
    <script src="../../assets/js/ui/initializers/MasterTables/${module.fileName}-initializer.js"></script>

    <!-- 5. Scripts de Tabler para funcionalidad completa -->
    <script src="../../assets/tabler/js/tabler.min.js"></script>

    <!-- Toast notification for profile operations -->
    <div id="recovery-toast" class="recovery-toast">
        <div class="recovery-toast-content">
            <div class="recovery-toast-header">
                <i class="fas fa-info-circle"></i>
                <span class="recovery-toast-title">Información</span>
            </div>
            <div class="recovery-toast-body">
                <span id="recovery-toast-message">Mensaje de operación</span>
            </div>
        </div>
    </div>

    <!-- Configuración de toast para operaciones -->
    <script>
        function showToast(message, type = 'info') {
            const toast = document.getElementById('recovery-toast');
            const messageElement = document.getElementById('recovery-toast-message');
            
            messageElement.textContent = message;
            toast.className = \`recovery-toast \${type}\`;
            toast.style.display = 'block';
            
            setTimeout(() => {
                toast.style.display = 'none';
            }, 3000);
        }
    </script>
</body>
</html>`;
}

// Función para generar el JS initializer template
function generateJSInitializerTemplate(module) {
    return `/**
 * Inicializador específico para la página de Gestión de ${module.title}
 * Maneja solo los componentes y controladores necesarios para esta página específica
 */
class ${module.className} {
    static async init() {
        console.log('${module.console} Inicializando página de Gestión de ${module.title}...');
        
        if (authChecker.checkAuthentication()) {
            authChecker.updateUserDisplay();
            authChecker.setupLogoutButton();
            
            // Inicializar ComponentLoader
            const componentLoader = new ComponentLoader();
            
            try {
                // Cargar componentes HTML dinámicamente ANTES de inicializar controladores
                console.log('🔄 Cargando componentes HTML...');
                
                // Cargar sidebar
                await componentLoader.loadComponent('sidebar', '#sidebar-container', {
                    activeSection: window.pageConfig?.activeSection || 'tablas'
                });
                
                // Cargar topbar
                await componentLoader.loadComponent('topbar', '#topbar-container', {
                    pageTitle: window.pageConfig?.pageTitle || { icon: '${module.icon}', text: 'Gestión de ${module.title}' }
                });
                
                // Cargar profile modal
                await componentLoader.loadComponent('profile-modal', '#modal-container');
                
                console.log('✅ Todos los componentes HTML cargados');
                
                // Esperar más tiempo para que el DOM se actualice completamente
                setTimeout(() => {
                    // Ahora inicializar controladores que necesitan los elementos del DOM
                    if (!window.sidebarControllerInstance) {
                        window.sidebarControllerInstance = new SidebarController();
                        console.log('📁 SidebarController inicializado');
                    }
                    
                    // Inicializar TopBarController DESPUÉS del sidebar con delay adicional
                    setTimeout(() => {
                        if (!window.topBarControllerInstance) {
                            window.topBarControllerInstance = new TopBarController();
                            console.log('🔝 TopBarController inicializado');
                        }
                        
                        // Inicializar ProfileController
                        if (!window.profileControllerInstance) {
                            window.profileControllerInstance = new ProfileController();
                            console.log('👤 ProfileController inicializado');
                            
                            // Establecer referencia al profile controller en topbar
                            if (window.topBarControllerInstance) {
                                window.topBarControllerInstance.profileController = window.profileControllerInstance;
                                console.log('🔗 Referencia profile-topbar establecida');
                            }
                        }
                    }, 200);
                    
                    // Configurar permisos DESPUÉS de que los controladores estén listos
                    setTimeout(() => {
                        if (window.PermissionsService) {
                            console.log('🔧 Inicializando sistema de permisos...');
                            window.PermissionsService.initializePermissions();
                        }
                        
                        // Ocultar pantalla de carga
                        const loadingOverlay = document.getElementById('permissions-loading');
                        if (loadingOverlay) {
                            loadingOverlay.style.display = 'none';
                        }
                        
                        console.log('✅ ${module.title} inicializado completamente');
                    }, 400);
                    
                }, 500);
                
            } catch (error) {
                console.error('❌ Error cargando componentes:', error);
                
                // Ocultar pantalla de carga en caso de error
                const loadingOverlay = document.getElementById('permissions-loading');
                if (loadingOverlay) {
                    loadingOverlay.style.display = 'none';
                }
            }
            
        } else {
            console.log('❌ Usuario no autenticado, redirigiendo...');
        }
    }
}

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM cargado, iniciando ${module.className}...');
    
    // Pequeño delay para asegurar que todos los scripts estén cargados
    setTimeout(() => {
        ${module.className}.init();
    }, 500);
});

console.log('📝 ${module.className} definido y configurado');
`;
}

console.log('Configuración de módulos de tablas maestras cargada');
