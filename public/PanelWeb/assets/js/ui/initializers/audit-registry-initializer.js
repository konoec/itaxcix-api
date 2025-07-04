/**
 * Inicializador básico para el módulo de Registro de Auditoría
 * Solo maneja sidebar, topbar y perfil
 */

// Función de inicialización básica del módulo de auditoría
function initializeAuditRegistry() {
    console.log('🔍 Inicializando módulo básico de Auditoría...');
    
    try {
        // Verificar dependencias básicas
        console.log('=== VERIFICACIÓN DE DEPENDENCIAS ===');
        console.log('- GlobalToast:', typeof window.GlobalToast !== 'undefined' ? '✅ Disponible' : '❌ No disponible');
        console.log('- ProfileService:', typeof window.ProfileService !== 'undefined' ? '✅ Disponible' : '❌ No disponible');
        console.log('- SidebarController:', typeof window.SidebarController !== 'undefined' ? '✅ Disponible' : '❌ No disponible');
        console.log('- TopBarController:', typeof window.TopBarController !== 'undefined' ? '✅ Disponible' : '❌ No disponible');
        console.log('- ProfileController:', typeof window.ProfileController !== 'undefined' ? '✅ Disponible' : '❌ No disponible');
        
        // Verificar elementos del DOM
        console.log('=== VERIFICACIÓN DE ELEMENTOS DOM ===');
        console.log('- Sidebar:', document.getElementById('sidebar') ? '✅ Encontrado' : '❌ No encontrado');
        console.log('- Topbar:', document.querySelector('.top-bar') ? '✅ Encontrado' : '❌ No encontrado');
        console.log('- Profile container:', document.getElementById('profile-container') ? '✅ Encontrado' : '❌ No encontrado');
        console.log('- Close sidebar btn:', document.getElementById('close-sidebar') ? '✅ Encontrado' : '❌ No encontrado');
        console.log('- Open sidebar btn:', document.getElementById('open-sidebar') ? '✅ Encontrado' : '❌ No encontrado');
        
        // Inicializar controladores básicos
        initializeBaseControllers();
        
        // Inicializar controlador específico de auditoría
        initializeAuditController();
        
        console.log('✅ Módulo básico de Auditoría inicializado correctamente');
        
    } catch (error) {
        console.error('❌ Error al inicializar módulo básico de auditoría:', error);
        console.error('Stack trace:', error.stack);
    }
}

/**
 * Inicializa los controladores base (sidebar, topbar, profile)
 */
function initializeBaseControllers() {
    console.log('⚙️ Inicializando controladores base...');
    
    // Sidebar
    if (typeof SidebarController !== 'undefined' && !window.sidebarControllerInstance) {
        window.sidebarControllerInstance = new SidebarController();
        console.log('✅ SidebarController inicializado');
    } else if (window.sidebarControllerInstance) {
        console.log('ℹ️ SidebarController ya estaba inicializado');
    } else {
        console.error('❌ SidebarController no disponible');
    }
    
    // Topbar
    if (typeof TopBarController !== 'undefined' && !window.topbarControllerInstance) {
        window.topbarControllerInstance = new TopBarController();
        console.log('✅ TopBarController inicializado');
    } else if (window.topbarControllerInstance) {
        console.log('ℹ️ TopBarController ya estaba inicializado');
    } else {
        console.error('❌ TopBarController no disponible');
    }
    
    // Profile
    if (typeof ProfileController !== 'undefined' && !window.profileControllerInstance) {
        window.profileControllerInstance = new ProfileController();
        console.log('✅ ProfileController inicializado');
    } else if (window.profileControllerInstance) {
        console.log('ℹ️ ProfileController ya estaba inicializado');
    } else {
        console.error('❌ ProfileController no disponible');
    }
}

/**
 * Inicializa el controlador específico de auditoría
 */
function initializeAuditController() {
    console.log('⚙️ Inicializando controlador de auditoría...');
    
    // Audit Registry Controller
    if (typeof AuditRegistryController !== 'undefined' && !window.auditController) {
        window.auditController = new AuditRegistryController();
        console.log('✅ AuditRegistryController inicializado');
    } else if (window.auditController) {
        console.log('ℹ️ AuditRegistryController ya estaba inicializado');
    } else {
        console.error('❌ AuditRegistryController no disponible');
    }
}

// Auto-inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOM cargado para módulo básico de auditoría');
    
    // Solo inicializar si estamos en la página de auditoría
    if (window.location.pathname.includes('AuditRegistry.html')) {
        console.log('✅ Página de auditoría detectada, iniciando módulo básico...');
        
        // Esperar un momento para que se carguen las dependencias básicas
        setTimeout(initializeAuditRegistry, 200);
    }
});

console.log('✅ Audit Registry Initializer cargado');
