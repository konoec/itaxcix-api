/**
 * Inicializador para el módulo de Registro de Auditoría
 * Usa AuditMainController para inicializar componentes base
 */

console.log('🔍 🚨 AUDIT-REGISTRY-INITIALIZER.JS SE ESTÁ CARGANDO 🚨');

class AuditRegistryInitializer {
    static async init() {
        console.log('🔍 === INICIANDO MÓDULO DE REGISTRO DE AUDITORÍA ===');
        
        // Verificar dependencias críticas
        console.log('🔍 Verificando dependencias...');
        console.log('- authChecker:', typeof authChecker);
        console.log('- AuditMainController:', typeof AuditMainController);
        console.log('- window.auditMainController:', !!window.auditMainController);
        console.log('- AuditService:', typeof AuditService);
        console.log('- AuditRegistryController:', typeof AuditRegistryController);
        
        if (typeof authChecker === 'undefined') {
            console.error('❌ authChecker no está disponible - deteniendo inicialización');
            return;
        }
        
        if (authChecker.checkAuthentication()) {
            console.log('✅ Usuario autenticado - continuando inicialización');
            
            // Usar AuditMainController para inicializar componentes base
            if (typeof AuditMainController !== 'undefined' && window.auditMainController) {
                console.log('🎯 Usando AuditMainController para inicializar componentes base...');
                await window.auditMainController.init({
                    activeSection: 'auditoria',
                    activeSubSection: 'registro',
                    pageTitle: { icon: 'fas fa-history', text: 'Registro de Auditoría' }
                });
                
                // Esperar a que los controladores base estén listos
                setTimeout(() => {
                    AuditRegistryInitializer.initializeAuditRegistryControllers();
                }, 300);
            } else {
                console.warn('⚠️ AuditMainController no disponible, usando inicialización directa...');
                await AuditRegistryInitializer.initializeControllersDirectly();
            }
            
        } else {
            console.log('❌ Usuario no autenticado - redirigiendo...');
        }
    }

    /**
     * Inicializa solo los controladores específicos de registro de auditoría
     */
    static initializeAuditRegistryControllers() {
        console.log('📋 === INICIALIZANDO CONTROLADORES DE REGISTRO DE AUDITORÍA ===');
        
        // Verificar que los controladores base estén listos
        if (window.auditMainController && window.auditMainController.areBaseControllersReady()) {
            console.log('✅ Controladores base listos, inicializando controladores de auditoría...');
        } else {
            console.warn('⚠️ Controladores base no están completamente listos');
        }
        
        // Inicializar AuditRegistryController específico
        console.log('🔍 Inicializando AuditRegistryController...');
        if (typeof AuditRegistryController !== 'undefined') {
            try {
                if (!window.auditRegistryController) {
                    window.auditRegistryController = new AuditRegistryController();
                    console.log('🔍 AuditRegistryController creado e inicializado');
                } else {
                    console.log('ℹ️ AuditRegistryController ya existe');
                }
            } catch (error) {
                console.error('❌ Error creando AuditRegistryController:', error);
            }
        } else {
            console.error('❌ AuditRegistryController no disponible');
        }
        
        // Verificación final
        console.log('🔍 === VERIFICACIÓN FINAL DE AUDITORÍA ===');
        console.log('- sidebarController:', !!window.sidebarController);
        console.log('- topbarController:', !!window.topbarController);
        console.log('- profileController:', !!window.profileController);
        console.log('- auditRegistryController:', !!window.auditRegistryController);
        console.log('✅ Controladores de auditoría inicializados');
    }

    /**
     * Fallback: inicializa controladores directamente si AuditMainController no está disponible
     */
    static async initializeControllersDirectly() {
        console.log('� Inicialización directa (sin AuditMainController)...');
        
        try {
            // Cargar componentes usando ComponentLoader directamente
            if (typeof ComponentLoader !== 'undefined') {
                const componentLoader = new ComponentLoader();
                
                // Cargar sidebar
                await componentLoader.loadComponent('sidebar', '#sidebar-container', {
                    activeSection: 'auditoria',
                    activeSubSection: 'registro'
                });
                
                // Cargar topbar
                await componentLoader.loadComponent('topbar', '#topbar-container', {
                    pageTitle: { icon: 'fas fa-history', text: 'Registro de Auditoría' }
                });
                
                // Cargar modal de perfil
                await componentLoader.loadComponent('profile-modal', '#profile-modal-container');
                
                console.log('✅ Componentes cargados directamente');
                
                // Inicializar controladores base
                setTimeout(() => {
                    this.initializeBaseControllers();
                    
                    // Inicializar el controlador específico de auditoría
                    if (typeof AuditRegistryController !== 'undefined' && !window.auditRegistryController) {
                        window.auditRegistryController = new AuditRegistryController();
                        console.log('✅ AuditRegistryController inicializado (fallback)');
                    }
                }, 200);
                
            } else {
                console.error('❌ ComponentLoader no está disponible');
            }
            
        } catch (error) {
            console.error('❌ Error en inicialización directa:', error);
        }
    }

    /**
     * Inicializa los controladores base (sidebar, topbar, profile) - solo para fallback
     */
    static initializeBaseControllers() {
        console.log('⚙️ Inicializando controladores base...');
        
        // Sidebar
        if (typeof SidebarController !== 'undefined' && !window.sidebarController) {
            window.sidebarController = new SidebarController();
            console.log('✅ SidebarController inicializado');
        }
        
        // Topbar - Nota: es TopBarController (con B mayúscula)
        if (typeof TopBarController !== 'undefined' && !window.topbarController) {
            window.topbarController = new TopBarController();
            console.log('✅ TopBarController inicializado');
        }
        
        // Profile
        if (typeof ProfileController !== 'undefined' && !window.profileController) {
            window.profileController = new ProfileController();
            console.log('✅ ProfileController inicializado');
        }
    }
}

// Auto-inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOM cargado para módulo de auditoría');
    
    // Solo inicializar si estamos en la página de auditoría
    if (window.location.pathname.includes('AuditRegistry.html')) {
        console.log('✅ Página de auditoría detectada, iniciando módulo...');
        
        // Esperar un momento para que se carguen las dependencias
        setTimeout(() => {
            AuditRegistryInitializer.init();
        }, 200);
    }
});

console.log('✅ Audit Registry Initializer cargado');
