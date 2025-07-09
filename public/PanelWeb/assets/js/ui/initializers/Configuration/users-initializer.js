/**
 * Inicializador simplificado para controladores modulares de usuarios
 * Usa MainController para inicializar componentes base
 */

console.log('📝 🚨 USERS-INITIALIZER.JS SE ESTÁ CARGANDO 🚨');

class UsersInitializer {
    static async init() {
        console.log('👥 === INICIANDO MÓDULO DE GESTIÓN DE USUARIOS ===');
        
        // Verificar dependencias críticas
        console.log('🔍 Verificando dependencias...');
        console.log('- authChecker:', typeof authChecker);
        console.log('- MainController:', typeof MainController);
        console.log('- window.mainController:', !!window.mainController);
        console.log('- UserService:', typeof UserService);
        console.log('- UsersListController:', typeof UsersListController);
        console.log('- UserDetailsController:', typeof UserDetailsController);
        
        if (typeof authChecker === 'undefined') {
            console.error('❌ authChecker no está disponible - deteniendo inicialización');
            return;
        }
        
        if (authChecker.checkAuthentication()) {
            console.log('✅ Usuario autenticado - continuando inicialización');
            
            authChecker.updateUserDisplay();
            authChecker.setupLogoutButton();
            
            // Inicializar ComponentLoader
            const componentLoader = new ComponentLoader();
            
            try {
                // Cargar componentes HTML dinámicamente ANTES de inicializar controladores
                console.log('🔄 Cargando componentes HTML...');
                
                // Cargar sidebar
                await componentLoader.loadComponent('sidebar', '#sidebar-container', {
                    activeSection: window.pageConfig?.activeSection || 'configuracion'
                });
                
                // Cargar topbar
                await componentLoader.loadComponent('topbar', '#topbar-container', {
                    pageTitle: window.pageConfig?.pageTitle || { icon: 'fas fa-users', text: 'Gestión de Usuarios' }
                });
                
                // Cargar profile modal
                await componentLoader.loadComponent('profile-modal', '#modal-container');
                
                console.log('✅ Todos los componentes HTML cargados');
                
                // Esperar más tiempo para que el DOM se actualice completamente
                setTimeout(() => {
                    // Inicializar controladores base
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
                    
                    // Inicializar controladores específicos de usuarios
                    setTimeout(() => {
                        UsersInitializer.initializeUsersControllers();
                    }, 100);
                    
                    // Configurar permisos DESPUÉS de que los controladores estén listos
                    setTimeout(() => {
                        if (window.PermissionsService) {
                            console.log('🔧 Inicializando sistema de permisos...');
                            window.PermissionsService.initializePermissions();
                        }
                        
                        console.log('✅ Gestión de Usuarios inicializada completamente');
                    }, 200);
                    
                }, 500);
                
            } catch (error) {
                console.error('❌ Error cargando componentes:', error);
            }
            
        } else {
            console.log('❌ Usuario no autenticado - redirigiendo...');
        }
    }

    /**
     * Inicializa solo los controladores específicos de usuarios
     */
    static initializeUsersControllers() {
        console.log('📋 === INICIALIZANDO CONTROLADORES DE USUARIOS ===');
        
        // Verificar que los controladores base estén listos
        if (window.mainController && window.mainController.areBaseControllersReady()) {
            console.log('✅ Controladores base listos, inicializando controladores de usuarios...');
        } else {
            console.warn('⚠️ Controladores base no están completamente listos');
        }
        
        // Inicializar UsersListController
        console.log('📋 Inicializando UsersListController...');
        if (typeof UsersListController !== 'undefined') {
            try {
                if (!window.usersListController) {
                    window.usersListController = new UsersListController();
                    console.log('📋 UsersListController creado, iniciando...');
                    
                    window.usersListController.init()
                        .then(() => {
                            console.log('✅ UsersListController inicializado completamente');
                        })
                        .catch(error => {
                            console.error('❌ Error inicializando UsersListController:', error);
                        });
                } else {
                    console.log('ℹ️ UsersListController ya existe');
                }
            } catch (error) {
                console.error('❌ Error creando UsersListController:', error);
            }
        } else {
            console.error('❌ UsersListController no disponible');
        }
        
        // Inicializar UserDetailsController
        console.log('👁️ Inicializando UserDetailsController...');
        if (typeof UserDetailsController !== 'undefined') {
            try {
                if (!window.UserDetailsController) {
                    window.UserDetailsController = new UserDetailsController();
                    console.log('✅ UserDetailsController inicializado');
                } else {
                    console.log('ℹ️ UserDetailsController ya existe');
                }
            } catch (error) {
                console.error('❌ Error en UserDetailsController:', error);
            }
        } else {
            console.error('❌ UserDetailsController no disponible');
        }
        
        // Inicializar UserCreateController
        console.log('➕ Inicializando UserCreateController...');
        console.log('➕ Verificando disponibilidad de UserCreateController:', typeof UserCreateController);
        console.log('➕ Estado actual de window.UserCreateController:', !!window.UserCreateController);
        console.log('➕ UserService disponible:', typeof UserService);
        console.log('➕ Bootstrap disponible:', typeof bootstrap);
        
        if (typeof UserCreateController !== 'undefined') {
            try {
                if (!window.userCreateController) {
                    console.log('➕ Creando nueva instancia de UserCreateController...');
                    window.userCreateController = new UserCreateController();
                    console.log('✅ UserCreateController creado exitosamente');
                    console.log('➕ Verificando que se puede acceder a openModal:', typeof window.userCreateController.openModal);
                } else {
                    console.log('ℹ️ userCreateController ya existe');
                }
            } catch (error) {
                console.error('❌ Error creando UserCreateController:', error);
                console.error('❌ Stack trace:', error.stack);
                console.error('❌ Dependencias faltantes:');
                console.error('   - UserService:', typeof UserService);
                console.error('   - bootstrap.Modal:', typeof bootstrap?.Modal);
                console.error('   - document.getElementById:', typeof document?.getElementById);
            }
        } else {
            console.error('❌ UserCreateController no disponible - clase no definida');
            console.error('❌ Verificando scripts cargados...');
            console.error('❌ UserService:', typeof UserService);
            console.error('❌ Bootstrap Modal:', typeof bootstrap?.Modal);
        }
        
        // Verificación final
        console.log('🔍 === VERIFICACIÓN FINAL DE USUARIOS ===');
        console.log('- UsersListController:', !!window.usersListController);
        console.log('- UserDetailsController:', !!window.UserDetailsController);
        console.log('- userCreateController:', !!window.userCreateController);
        console.log('✅ Controladores de usuarios inicializados');
    }

    /**
     * Fallback: inicializa controladores básicos si hay algún error
     */
    static initializeFallback() {
        console.log('🔄 Modo fallback...');
        
        if (typeof SidebarController !== 'undefined') {
            window.sidebarControllerInstance = new SidebarController();
        }
        
        if (typeof TopBarController !== 'undefined') {
            window.topBarControllerInstance = new TopBarController();
        }
        
        if (typeof UserDetailsController !== 'undefined') {
            window.UserDetailsController = new UserDetailsController();
        }
    }
}


// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('� DOM cargado, iniciando UsersInitializer...');
    
    // Pequeño delay para asegurar que todos los scripts estén cargados
    setTimeout(() => {
        UsersInitializer.init();
    }, 500);
});

console.log('📝 UsersInitializer definido y configurado');
