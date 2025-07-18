/**
 * Inicializador específico para la página de Gestión de Tipos de Contacto
 * Maneja solo los componentes y controladores necesarios para esta página específica
 */
class ContactTypesInitializer {
    static async init() {
        console.log('📞 Inicializando página de Gestión de Tipos de Contacto...');
        
        // 1️⃣ Autenticación
        if (!authChecker.checkAuthentication()) {
            console.log('❌ Usuario no autenticado, redirigiendo...');
            return;
        }
        authChecker.updateUserDisplay();
        authChecker.setupLogoutButton();
        
        const componentLoader = new ComponentLoader();
        try {
            // 2️⃣ Carga de componentes HTML
            console.log('🔄 Cargando componentes HTML...');
            await componentLoader.loadComponent('sidebar', '#sidebar-container', {
                activeSection: window.pageConfig?.activeSection || 'tablas'
            });
            await componentLoader.loadComponent('topbar', '#topbar-container', {
                pageTitle: window.pageConfig?.pageTitle || {
                    icon: 'fas fa-address-book',
                    text: 'Gestión de Tipos de Contacto'
                }
            });
            // modal de perfil y modal de creación
            await componentLoader.loadComponent('profile-modal', '#modal-container', { append: true });
            console.log('✅ Todos los componentes HTML cargados');
            
            // 3️⃣ Esperar a que el DOM se actualice antes de instanciar controladores
            setTimeout(() => {
                // Sidebar
                if (typeof SidebarController !== 'undefined' && !window.sidebarControllerInstance) {
                    window.sidebarControllerInstance = new SidebarController();
                    console.log('📁 SidebarController inicializado');
                }
                
                // 4️⃣ TopBar y Profile
                setTimeout(() => {
                    if (typeof TopBarController !== 'undefined' && !window.topBarControllerInstance) {
                        window.topBarControllerInstance = new TopBarController();
                        console.log('🔝 TopBarController inicializado');
                    }
                    if (typeof ProfileController !== 'undefined' && !window.profileControllerInstance) {
                        window.profileControllerInstance = new ProfileController();
                        console.log('👤 ProfileController inicializado');
                        window.topBarControllerInstance.profileController = window.profileControllerInstance;
                        console.log('🔗 Referencia profile-topbar establecida');
                    }
                }, 200);
                
                // 5️⃣ Lista de Tipos de Contacto
                setTimeout(async () => {
                    if (typeof ContactTypeListController !== 'undefined' && !window.contactTypeListControllerInstance) {
                        window.contactTypeListControllerInstance = new ContactTypeListController();
                        if (typeof window.contactTypeListControllerInstance.init === 'function') {
                            await window.contactTypeListControllerInstance.init();
                        }
                        console.log('📋 ContactTypeListController inicializado');
                    }
                    // Inicializar controlador de edición
                    if (typeof ContactTypeEditController !== 'undefined') {
                        window.contactTypeEditControllerInstance = new ContactTypeEditController();
                        console.log('✏️ ContactTypeEditController inicializado');
                    }
                }, 300);
                
                // 6️⃣ Modal de Creación
                setTimeout(() => {
                    if (typeof CreateContactTypeController !== 'undefined' && !window.createContactTypeController) {
                        window.createContactTypeController = new CreateContactTypeController(
                            'create-contact-type-modal',
                            'create-contact-type-form',
                            (newType) => {
                                if (window.contactTypeListControllerInstance?.loadContactTypes) {
                                    window.contactTypeListControllerInstance.loadContactTypes();
                                }
                            }
                        );
                        const btn = document.getElementById('btn-create-contact-type');
                        if (btn) {
                            btn.addEventListener('click', () => window.createContactTypeController.open());
                        }
                        console.log('➕ CreateContactTypeController inicializado');
                    }
                }, 400);
                
                // 7️⃣ Permisos y fin de carga
                setTimeout(() => {
                    if (window.PermissionsService) {
                        console.log('🔧 Inicializando sistema de permisos...');
                        window.PermissionsService.initializePermissions();
                    }
                    console.log('✅ Tipos de Contacto inicializados completamente');
                    LoadingScreenUtil.notifyModuleLoaded('ContactTypes');
                }, 500);
            }, 500);
            
        } catch (error) {
            console.error('❌ Error cargando componentes:', error);
            LoadingScreenUtil.notifyModuleLoaded('ContactTypes');
        }
    }
}

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    console.log('📄 DOM cargado, iniciando ContactTypesInitializer...');
    setTimeout(() => {
        ContactTypesInitializer.init();
    }, 1000);
});

console.log('📝 ContactTypesInitializer definido y configurado');
