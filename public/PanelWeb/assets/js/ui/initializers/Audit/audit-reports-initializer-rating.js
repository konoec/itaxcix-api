/**
 * Inicializador para la página de Reporte de Incidentes
 * SOLO carga componentes visuales y controladores base.
 */
class IncidentReportsInitializer {
  static async init() {
    console.log('🚨 Inicializando Reporte de Incidentes...');

    // 1. Validar autenticación básica
    if (!authChecker.checkAuthentication()) {
      console.log('❌ Usuario no autenticado, redirigiendo...');
      return;
    }
    authChecker.updateUserDisplay();
    authChecker.setupLogoutButton();

    // 2. Carga de componentes globales (sidebar, topbar, profile-modal)
    const loader = new ComponentLoader();
    try {
      console.log('🔄 Cargando componentes HTML (sidebar, topbar, profile)...');
      await loader.loadComponent('sidebar', '#sidebar-container', {
        activeSection: window.pageConfig?.activeSection || 'reportes'
      });
      await loader.loadComponent('topbar', '#topbar-container', {
        pageTitle: window.pageConfig?.pageTitle || { icon: 'fas fa-list-alt', text: 'Reporte de Incidentes' }
      });
      await loader.loadComponent('profile-modal', '#modal-container');
      console.log('✅ Componentes HTML cargados');
    } catch (err) {
      console.error('❌ Error cargando componentes:', err);
    }

    // 3. Inicializar controladores base tras carga del DOM
    setTimeout(() => {
      this._initBaseControllers();
      this._initGlobalEvents();
      this._initTooltips();
      this._initShortcuts();
      console.log('✅ Página de Reporte de Incidentes inicializada');
      LoadingScreenUtil.notifyModuleLoaded('IncidentReports');
    }, 500);
  }

  static _initBaseControllers() {
    if (!window.sidebarControllerInstance) {
      window.sidebarControllerInstance = new SidebarController();
      console.log('📁 SidebarController inicializado');
    }
    setTimeout(() => {
      if (!window.topBarControllerInstance) {
        window.topBarControllerInstance = new TopBarController();
        window.topBarControllerInstance.profileController = window.profileControllerInstance;
        console.log('🔝 TopBarController inicializado');
      }
    }, 200);
    if (!window.profileControllerInstance) {
      window.profileControllerInstance = new ProfileController();
      console.log('👤 ProfileController inicializado');
    }
  }

  static _initGlobalEvents() {
    // Eventos globales como errores, offline/online, etc.
    window.addEventListener('error', event => {
      if (event.filename && event.filename.includes('incident')) {
        console.error('❌ Error en módulo de incidentes:', event.error);
        window.showToast?.('Error inesperado en incidentes', 'error');
      }
    });
    window.addEventListener('online', () => {
      window.showToast?.('Conexión restaurada', 'success');
    });
    window.addEventListener('offline', () => {
      window.showToast?.('Sin conexión a internet', 'warning');
    });
    console.log('✅ Eventos globales configurados');
  }

  static _initTooltips() {
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
      document.querySelectorAll('[title]').forEach(el => new bootstrap.Tooltip(el));
      console.log('✅ Tooltips inicializados');
    }
  }

  static _initShortcuts() {
    // Puedes personalizar atajos aquí
    document.addEventListener('keydown', e => {
      if (['INPUT', 'TEXTAREA'].includes(e.target.tagName)) return;
      if (e.key === 'Escape') {
        // Por ejemplo: limpiar filtros (opcional)
      }
    });
    console.log('✅ Atajos básicos configurados');
  }

  static cleanupIncidentReportsModule() {
    console.log('🧹 Limpiando módulo de incidentes...');
    // Aquí podrías limpiar timers/eventos si fuera necesario
  }
}

// Inicializar al cargar el DOM
document.addEventListener('DOMContentLoaded', () => {
  setTimeout(() => IncidentReportsInitializer.init(), 500);
});

// Limpiar antes de salir
window.addEventListener('beforeunload', () => {
  IncidentReportsInitializer.cleanupIncidentReportsModule();
});

console.log('📝 IncidentReportsInitializer cargado y listo');
