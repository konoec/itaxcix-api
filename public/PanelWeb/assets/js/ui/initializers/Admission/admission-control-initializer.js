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
                
                // Esperar menos tiempo para que el DOM se actualice
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
                    
                    // Inicializar controlador específico de admisión
                    setTimeout(() => {
                        ControlAdmisionInitializer.initializeAdmissionModule(() => {
                            // Configurar permisos DESPUÉS de que el módulo específico esté listo
                            if (window.PermissionsService) {
                                console.log('🔧 Inicializando sistema de permisos...');
                                window.PermissionsService.initializePermissions();
                            }
                            
                            console.log('✅ Control de Admisión inicializado completamente');
                            
                            // Notificar que este módulo ha terminado de cargar
                            LoadingScreenUtil.notifyModuleLoaded('AdmissionControl');
                        });
                    }, 100);
                    
                }, 500);
                
            } catch (error) {
                console.error('❌ Error cargando componentes:', error);
                
                // Notificar que el módulo está listo (incluso con error)
                LoadingScreenUtil.notifyModuleLoaded('AdmissionControl');
            }
            
        } else {
            console.log('❌ Usuario no autenticado, redirigiendo...');
        }
    }

    /**
     * Función de inicialización principal del módulo de admisión
     * @param {Function} callback - Función a ejecutar cuando termine la inicialización
     */
    static initializeAdmissionModule(callback = null) {
        console.log('🚗 Inicializando módulo de admisión...');
        
        try {
            // Verificar que las dependencias estén disponibles
            if (typeof AdmissionControlController === 'undefined') {
                console.warn('⚠️ AdmissionControlController no está definido en esta página');
                if (callback && typeof callback === 'function') {
                    callback();
                }
                return;
            }
            
            // Agregar un pequeño delay para asegurar que todos los elementos DOM estén listos
            setTimeout(() => {
                // Inicializar AdmissionControlController específico para admission control
                if (!window.admissionControllerInstance) {
                    const app = new AdmissionControlController();
                    
                    // ESPERAR a que app.init() termine antes de ejecutar el callback
                    app.init().then(() => {
                        console.log('✅ Módulo de admisión inicializado correctamente');
                        
                        // Ejecutar callback DESPUÉS de que la inicialización termine
                        if (callback && typeof callback === 'function') {
                            callback();
                        }
                    }).catch(error => {
                        console.error('❌ Error cargando conductores:', error);
                        
                        // Ejecutar callback incluso si hay error para no bloquear la UI
                        if (callback && typeof callback === 'function') {
                            callback();
                        }
                    });
                    
                    window.admissionControllerInstance = app;
                    console.log('🚗 AdmissionControlController inicializado');
                } else {
                    console.warn('⚠️ AdmissionControlController ya está definido en esta página');
                    
                    // Ejecutar callback si el controller ya existe
                    if (callback && typeof callback === 'function') {
                        callback();
                    }
                }
            }, 100);
            
        } catch (error) {
            console.error('❌ Error al inicializar módulo de admisión:', error);
            
            // Ejecutar callback aún con error para no bloquear la UI
            if (callback && typeof callback === 'function') {
                callback();
            }
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
