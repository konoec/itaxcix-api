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

                    // 6.1️⃣ Inicializar controlador de eliminación de tipo de contacto
                    // Inicializar DeleteContactTypeController solo cuando el modal global esté disponible
                    if (typeof DeleteContactTypeController !== 'undefined' && !window.deleteContactTypeController) {
                        if (window.globalConfirmationModal) {
                            window.deleteContactTypeController = new DeleteContactTypeController();
                            console.log('🗑️ DeleteContactTypeController inicializado');
                        } else {
                            // Esperar a que el modal global esté disponible
                            const observer = new MutationObserver(() => {
                                if (window.globalConfirmationModal) {
                                    window.deleteContactTypeController = new DeleteContactTypeController();
                                    console.log('🗑️ DeleteContactTypeController inicializado (diferido)');
                                    observer.disconnect();
                                }
                            });
                            observer.observe(document.body, { childList: true, subtree: true });
                        }
                    }

                    // 6.2️⃣ Delegación de eventos para los botones de eliminar
                    document.body.addEventListener('click', function(e) {
                        const btn = e.target.closest('[data-action="delete-contact-type"]');
                        if (btn) {
                            const id = Number(btn.getAttribute('data-contact-type-id'));
                            const name = btn.getAttribute('data-contact-type-name') || '';
                            if (id > 0 && window.deleteContactTypeController) {
                                window.deleteContactTypeController.handleDeleteButtonClick(btn, { id, name });
                            }
                        }
                    });
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
