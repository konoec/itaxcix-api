/**
 * Inicializador específico para la página de Control de Admisión de Conductores
 * Maneja solo los componentes y controladores necesarios para esta página específica
 */
class ControlAdmisionInitializer {
    static async init() {
        console.log('🚗 Inicializando página de Control de Admisión de Conductores...');
        
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
                    activeSection: window.pageConfig?.activeSection || 'admission'
                });
                
                // Cargar topbar
                await componentLoader.loadComponent('topbar', '#topbar-container', {
                    pageTitle: window.pageConfig?.pageTitle || { icon: 'fas fa-user-check', text: 'Admisión de conductores' }
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
                            console.log('� TopBarController inicializado');
                        }
                    }, 200);
                    
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
                    
                    // Inicializar AdmissionControlController específico para admission control
                    if (!window.admissionControllerInstance) {
                        // Verificar que AdmissionControlController existe antes de instanciarlo
                        if (typeof AdmissionControlController !== 'undefined') {
                            const app = new AdmissionControlController();
                            // NO ESPERAR - cargar conductores independientemente del perfil
                            app.init().catch(error => {
                                console.error('❌ Error cargando conductores:', error);
                            });
                            window.admissionControllerInstance = app;
                            console.log('🚗 AdmissionControlController inicializado');
                        } else {
                            console.warn('⚠️ AdmissionControlController no está definido en esta página');
                        }
                    }
                    
                    // Configurar permisos DESPUÉS de que los controladores estén listos
                    setTimeout(() => {
                        if (window.PermissionsService) {
                            console.log('🔧 Inicializando sistema de permisos...');
                            window.PermissionsService.initializePermissions();
                        }
                        
                        console.log('✅ Control de Admisión inicializado completamente');
                    }, 100);
                    
                }, 500);
                
            } catch (error) {
                console.error('❌ Error cargando componentes:', error);
            }
            
        } else {
            console.log('❌ Usuario no autenticado, redirigiendo...');
        }
    }
}

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('� DOM cargado, iniciando ControlAdmisionInitializer...');
    
    // Pequeño delay para asegurar que todos los scripts estén cargados
    setTimeout(() => {
        ControlAdmisionInitializer.init();
    }, 500);
});

console.log('� ControlAdmisionInitializer definido y configurado');
