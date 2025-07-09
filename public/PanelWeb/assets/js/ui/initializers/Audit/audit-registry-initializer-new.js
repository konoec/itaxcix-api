/**
 * Inicializador para el módulo de Registro de Auditoría
 * Sigue el patrón de roles-initializer.js
 */

console.log('🔍 🚨 AUDIT-REGISTRY-INITIALIZER.JS SE ESTÁ CARGANDO 🚨');

// Inicializador dedicado para el módulo de Registro de Auditoría
(function() {
    // Inicializar controladores base (sidebar, topbar, perfil)
    document.addEventListener('DOMContentLoaded', async () => {
        try {
            console.log('🔍 Inicializando módulo de Registro de Auditoría...');
            
            // Inicializar ComponentLoader y cargar componentes
            const componentLoader = new ComponentLoader();
            
            // Cargar componentes HTML dinámicamente ANTES de inicializar controladores
            console.log('🔄 Cargando componentes HTML...');
            
            // Cargar sidebar
            await componentLoader.loadComponent('sidebar', '#sidebar-container', {
                activeSection: window.pageConfig?.activeSection || 'auditoria'
            });
            
            // Cargar topbar
            await componentLoader.loadComponent('topbar', '#topbar-container', {
                pageTitle: window.pageConfig?.pageTitle || { icon: 'fas fa-history', text: 'Registro de Auditoría' }
            });
            
            // Cargar profile modal
            await componentLoader.loadComponent('profile-modal', '#modal-container');
            
            console.log('✅ Todos los componentes HTML cargados');
            
            // Esperar un momento para que el DOM se actualice
            setTimeout(async () => {
                // Inicializar controladores base
                if (typeof SidebarController !== 'undefined') {
                    if (!window.sidebarControllerInstance) {
                        window.sidebarControllerInstance = new SidebarController();
                    }
                    console.log('✅ SidebarController inicializado');
                }
                
                if (typeof TopBarController !== 'undefined') {
                    if (!window.topBarControllerInstance) {
                        window.topBarControllerInstance = new TopBarController();
                    }
                    console.log('✅ TopBarController inicializado');
                }
                
                if (typeof ProfileController !== 'undefined') {
                    if (!window.profileControllerInstance) {
                        window.profileControllerInstance = new ProfileController();
                    }
                    console.log('✅ ProfileController inicializado');
                }
                
                // Inicializar controlador específico de Registro de Auditoría
                if (typeof AuditRegistryController !== 'undefined') {
                    if (!window.auditRegistryController) {
                        window.auditRegistryController = new AuditRegistryController();
                    }
                    console.log('✅ AuditRegistryController inicializado');
                }
                
                console.log('🔍 ✅ Módulo de Registro de Auditoría completamente inicializado');
                
            }, 200);
            
            // Verificación única de token al cargar
            if (typeof window.authChecker !== 'undefined') {
                window.authChecker.checkTokenExpiration();
                console.log('✅ Verificación inicial de token realizada');
            }
            
            // Configurar permisos DESPUÉS de que los controladores estén listos
            setTimeout(() => {
                if (window.PermissionsService) {
                    console.log('🔧 Inicializando sistema de permisos...');
                    window.PermissionsService.initializePermissions();
                }
            }, 400);
            
        } catch (error) {
            console.error('❌ Error inicializando módulo de Registro de Auditoría:', error);
        }
    });
})();

console.log('✅ Audit Registry Initializer cargado');
