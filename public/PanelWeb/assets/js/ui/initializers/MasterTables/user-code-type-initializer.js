/**
 * Inicializador específico para la página de Gestión de Tipos de Código Usuario
 * Maneja solo los componentes y controladores necesarios para esta página específica
 */
class UserCodeTypeInitializer {
    static async init() {
        console.log('👤 Inicializando página de Gestión de Tipos de Código Usuario...');
        
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
                    pageTitle: window.pageConfig?.pageTitle || { icon: 'fas fa-user-tag', text: 'Gestión de Tipos de Código Usuario' }
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
                    
                    // Inicializar el controlador de lista de tipos de código de usuario después de los controladores base
                    setTimeout(() => {
                        try {
                            if (typeof UserCodeTypeListController === 'undefined') {
                                throw new Error('UserCodeTypeListController no está disponible');
                            }
                            window.UserCodeTypeListController = new UserCodeTypeListController();
                            console.log('🗂️ UserCodeTypeListController inicializado por el inicializador');
                        } catch (error) {
                            console.error('❌ Error al inicializar UserCodeTypeListController:', error);
                        }
                    }, 200);
                    
                    // Inicializar el controlador de creación de tipo de código de usuario
                    setTimeout(() => {
                        try {
                            if (typeof CreateUserCodeTypeController === 'undefined') {
                                throw new Error('CreateUserCodeTypeController no está disponible');
                            }
                            window.createUserCodeTypeController = new CreateUserCodeTypeController(
                                'createUserCodeTypeModal',
                                'createUserCodeTypeForm',
                                function(newType) {
                                    // Refrescar la lista si existe el controlador de lista
                                    if (window.UserCodeTypeListController && typeof window.UserCodeTypeListController.load === 'function') {
                                        window.UserCodeTypeListController.load();
                                    }
                                }
                            );
                            // Asignar evento al botón + para abrir el modal
                            const btn = document.getElementById('createUserCodeTypeBtn');
                            if (btn) {
                                btn.addEventListener('click', () => window.createUserCodeTypeController.open());
                            }
                            console.log('➕ CreateUserCodeTypeController inicializado');
                        } catch (error) {
                            console.error('❌ Error al inicializar CreateUserCodeTypeController:', error);
                        }
                    }, 200);
                

                    // Inicializar el controlador de actualización de tipo de código de usuario
                    setTimeout(() => {
                        try {
                            if (typeof UpdateUserCodeTypeController === 'undefined') {
                                throw new Error('UpdateUserCodeTypeController no está disponible');
                            }
                            window.updateUserCodeTypeController = new UpdateUserCodeTypeController(
                                function(updatedType) {
                                    // Refrescar la lista si existe el controlador de lista
                                    if (window.UserCodeTypeListController && typeof window.UserCodeTypeListController.load === 'function') {
                                        window.UserCodeTypeListController.load();
                                    }
                                }
                            );
                            console.log('✏️ UpdateUserCodeTypeController inicializado');
                        } catch (error) {
                            console.error('❌ Error al inicializar UpdateUserCodeTypeController:', error);
                        }
                    }, 250);

                    // Inicializar el controlador de eliminación de tipo de código de usuario
                    setTimeout(() => {
                        try {
                            if (typeof DeleteUserCodeTypeController === 'undefined') {
                                throw new Error('DeleteUserCodeTypeController no está disponible');
                            }
                            window.deleteUserCodeTypeController = new DeleteUserCodeTypeController();
                            console.log('🗑️ DeleteUserCodeTypeController inicializado');
                        } catch (error) {
                            console.error('❌ Error al inicializar DeleteUserCodeTypeController:', error);
                        }
                    }, 300);
                }, 200);
                    
                    // Configurar permisos DESPUÉS de que los controladores estén listos
                    setTimeout(() => {
                        if (window.PermissionsService) {
                            console.log('🔧 Inicializando sistema de permisos...');
                            window.PermissionsService.initializePermissions();
                        }
                        
                        // Notificar que el módulo está listo
                        LoadingScreenUtil.notifyModuleLoaded('UserCodeType');
                        
                        console.log('✅ Tipos de Código Usuario inicializado completamente');
                    }, 400);                    
                
            } catch (error) {
                console.error('❌ Error cargando componentes:', error);
                
                // Notificar que el módulo está listo (incluso con error)
                LoadingScreenUtil.notifyModuleLoaded('UserCodeType');
            }
            
        } else {
            console.log('❌ Usuario no autenticado, redirigiendo...');
        }
    }
}

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM cargado, iniciando UserCodeTypeInitializer...');
    
    // Pequeño delay para asegurar que todos los scripts estén cargados
    setTimeout(() => {
        UserCodeTypeInitializer.init();
    }, 500);
});

console.log('📝 UserCodeTypeInitializer definido y configurado');

