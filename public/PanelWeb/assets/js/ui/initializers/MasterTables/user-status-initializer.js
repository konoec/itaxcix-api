/**
 * Inicializador específico para la página de Gestión de Estado de Usuarios
 * Maneja solo los componentes y controladores necesarios para esta página específica
 */
class UserStatusInitializer {
    static async init() {
        console.log('👥 Inicializando página de Gestión de Estado de Usuarios...');
        
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
                    pageTitle: window.pageConfig?.pageTitle || { icon: 'fas fa-user-circle', text: 'Gestión de Estado de Usuarios' }
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
                        
                        // Inicializar el controlador de lista de estados de usuario después de los controladores base
                        setTimeout(() => {
                            try {
                                if (typeof UserStatusListController === 'undefined') {
                                    throw new Error('UserStatusListController no está disponible');
                                }
                                window.userStatusListController = new UserStatusListController();
                                console.log('🗂️ userStatusListController inicializado por el inicializador');
                            } catch (error) {
                                console.error('❌ Error al inicializar UserStatusListController:', error);
                            }
                        }, 200);
                    }, 200);
                    
                    // Inicializar el controlador de creación de estado de usuario
                    setTimeout(() => {
                        try {
                            if (typeof CreateUserStatusController === 'undefined') {
                                throw new Error('CreateUserStatusController no está disponible');
                            }
                            window.createUserStatusController = new CreateUserStatusController(
                                'createUserStatusModal',
                                'createUserStatusForm',
                                function(newStatus) {
                                    // Refrescar la lista si existe el controlador de lista
                                    if (window.userStatusListController && typeof window.userStatusListController.load === 'function') {
                                        window.userStatusListController.load();
                                    }
                                }
                            );
                            // Asignar evento al botón + para abrir el modal
                            const btn = document.getElementById('createUserStatusBtn');
                            if (btn) {
                                btn.addEventListener('click', () => window.createUserStatusController.open());
                            }
                            console.log('➕ CreateUserStatusController inicializado');
                        } catch (error) {
                            console.error('❌ Error al inicializar CreateUserStatusController:', error);
                        }
                    }, 200);

                    // Inicializar el controlador de actualización de estado de usuario
                    setTimeout(() => {
                        try {
                            if (typeof UpdateUserStatusController === 'undefined') {
                                throw new Error('UpdateUserStatusController no está disponible');
                            }
                            window.updateUserStatusController = new UpdateUserStatusController(
                                function(updatedStatus) {
                                    // Refrescar la lista si existe el controlador de lista
                                    if (window.userStatusListController && typeof window.userStatusListController.load === 'function') {
                                        window.userStatusListController.load();
                                    }
                                }
                            );
                            console.log('✏️ UpdateUserStatusController inicializado');
                        } catch (error) {
                            console.error('❌ Error al inicializar UpdateUserStatusController:', error);
                        }
                    }, 250);
                    
                    // Inicializar el controlador de eliminación de estado de usuario
                    setTimeout(() => {
                        try {
                            if (typeof DeleteUserStatusController === 'undefined') {
                                throw new Error('DeleteUserStatusController no está disponible');
                            }
                            window.deleteUserStatusController = new DeleteUserStatusController();
                            console.log('🗑️ DeleteUserStatusController inicializado');
                        } catch (error) {
                            console.error('❌ Error al inicializar DeleteUserStatusController:', error);
                        }
                    }, 400);

                    // Inicializar el modal de confirmación global
                    setTimeout(() => {
                        try {
                            if (typeof GlobalConfirmationModalController === 'undefined') {
                                console.warn('⚠️ GlobalConfirmationModalController no está disponible');
                            } else if (!window.globalConfirmationModalController) {
                                window.globalConfirmationModalController = new GlobalConfirmationModalController();
                                console.log('🗑️ GlobalConfirmationModalController inicializado');
                            }
                        } catch (error) {
                            console.error('❌ Error al inicializar GlobalConfirmationModalController:', error);
                        }
                    }, 150);

                    // Configurar permisos DESPUÉS de que los controladores estén listos
                    setTimeout(() => {
                        if (window.PermissionsService) {
                            console.log('🔧 Inicializando sistema de permisos...');
                            window.PermissionsService.initializePermissions();
                        }
                        
                        console.log('✅ Estado de Usuarios inicializado completamente');
                        
                        // Notificar que este módulo ha terminado de cargar
                        LoadingScreenUtil.notifyModuleLoaded('UserStatus');
                    }, 400);
                    
                }, 500);
                
            } catch (error) {
                console.error('❌ Error cargando componentes:', error);
                
                // En caso de error, también ocultar la pantalla de carga
                LoadingScreenUtil.notifyModuleLoaded('UserStatus');
            }
            
        } else {
            console.log('❌ Usuario no autenticado, redirigiendo...');
        }
    }
}

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM cargado, iniciando UserStatusInitializer...');
    
    // Pequeño delay para asegurar que todos los scripts estén cargados
    setTimeout(() => {
        UserStatusInitializer.init();
    }, 500);
});

console.log('📝 UserStatusInitializer definido y configurado');

