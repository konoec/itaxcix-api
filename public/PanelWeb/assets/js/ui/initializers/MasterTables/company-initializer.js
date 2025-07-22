/**
 * Inicializador específico para la página de Gestión de Empresas
 * Maneja solo los componentes, servicios y controladores necesarios para esta página
 */
class CompanyInitializer {
  static async init() {
    console.log('🏢 Inicializando página de Gestión de Empresas...');

    if (!authChecker.checkAuthentication()) {
      console.log('❌ Usuario no autenticado, redirigiendo...');
      return;
    }
    authChecker.updateUserDisplay();
    authChecker.setupLogoutButton();

    const loader = new ComponentLoader();
    try {
      console.log('🔄 Cargando componentes HTML...');
      await loader.loadComponent('sidebar', '#sidebar-container', {
        activeSection: window.pageConfig?.activeSection || 'tablas'
      });
      await loader.loadComponent('topbar', '#topbar-container', {
        pageTitle: window.pageConfig?.pageTitle || { icon: 'fas fa-building', text: 'Gestión de Empresas' }
      });
      await loader.loadComponent('profile-modal', '#modal-container');
      console.log('✅ Componentes HTML cargados');
    } catch (err) {
      console.error('❌ Error cargando componentes:', err);
    }

    // Pequeño delay para asegurar que todo el DOM y componentes estén listos
    setTimeout(() => {
      this._initBaseControllers();
      this._initServices();
      this._initModuleControllers();
      this._initGlobalEvents();
      this._initTooltips();
      this._initShortcuts();
      console.log('✅ Gestión de Empresas inicializada completamente');
      LoadingScreenUtil.notifyModuleLoaded('Companies');
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
        console.log('🔝 TopBarController inicializado');
        window.topBarControllerInstance.profileController = window.profileControllerInstance;
      }
    }, 200);
    if (!window.profileControllerInstance) {
      window.profileControllerInstance = new ProfileController();
      console.log('👤 ProfileController inicializado');
    }
  }

  static _initServices() {
    window.companyServiceInstance       = new CompanyService();
    window.companyCreateServiceInstance = new CompanyCreateService();
    window.companyUpdateServiceInstance = new CompanyUpdateService();
    window.companyDeleteServiceInstance = new CompanyDeleteService();
    console.log('🌐 Servicios de Company inicializados');
  }

  static _initModuleControllers() {
    if (!window.companyController) {
      window.companyController = new CompanyController(
        window.companyServiceInstance,
        window.companyCreateServiceInstance,
        window.companyUpdateServiceInstance,
        window.companyDeleteServiceInstance
      );
      window.companyController.loadCompanies();
      console.log('📋 CompanyController inicializado y lista cargada');
    }

    if (!window.editCompanyController) {
      window.editCompanyController = new EditCompanyController(
        window.companyUpdateServiceInstance,
        'edit-company-modal',
        'edit-company-form'
      );
      console.log('✏️ EditCompanyController inicializado');
      window.addEventListener('company:updated', () => {
        window.companyController.loadCompanies();
      });
    }
  }

  static _initGlobalEvents() {
    console.log('🏢 Configurando eventos globales...');
    window.addEventListener('error', event => {
      if (event.filename && event.filename.includes('company')) {
        console.error('❌ Error no capturado en módulo de compañías:', event.error);
        window.showToast?.('Error inesperado en el sistema', 'error');
      }
    });
    window.addEventListener('online', () => {
      console.log('🌐 Conexión restaurada');
      window.companyController?.loadCompanies();
      window.showToast?.('Conexión restaurada', 'success');
    });
    window.addEventListener('offline', () => {
      console.log('🌐 Conexión perdida');
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
    console.log('🏢 Configurando atajos de teclado...');
    document.addEventListener('keydown', e => {
  if (['INPUT','TEXTAREA'].includes(e.target.tagName)) return;

  // Ctrl+N o Cmd+N: Nueva empresa
  if ((e.ctrlKey||e.metaKey) && e.key.toLowerCase()==='n') {
    e.preventDefault();
    window.companyController?.handleAddCompany();
  }
  // Ctrl+R o Cmd+R: Recargar tabla
  if ((e.ctrlKey||e.metaKey) && e.key.toLowerCase()==='r') {
    e.preventDefault();
    window.companyController?.loadCompanies();
  }
  // SOLO F5 normal, NO Ctrl+F5 ni Shift+F5
  if (e.key==='F5' && !e.ctrlKey && !e.shiftKey && !e.metaKey) {
    e.preventDefault();
    window.companyController?.loadCompanies();
  }
  // Escape: limpiar filtros
  if (e.key==='Escape') {
    window.companyController?.clearFilters();
  }
  // Ctrl+F: buscar
  if ((e.ctrlKey||e.metaKey) && e.key.toLowerCase()==='f') {
    e.preventDefault();
    const input = document.getElementById('company-search-input');
    input?.focus();
    input?.select();
  }
});

    console.log('✅ Atajos de teclado configurados');
  }

  static checkModuleHealth() {
    const health = {
      controllers: {
        sidebar:     !!window.sidebarControllerInstance,
        topbar:      !!window.topBarControllerInstance,
        profile:     !!window.profileControllerInstance,
        company:     !!window.companyController,
        editCompany: !!window.editCompanyController
      },
      services: {
        list:   !!window.companyServiceInstance,
        create: !!window.companyCreateServiceInstance,
        update: !!window.companyUpdateServiceInstance,
        delete: !!window.companyDeleteServiceInstance
      },
      dom: {
        loading: !!document.getElementById('companies-loading'),
        content: !!document.getElementById('companies-content'),
        table:   !!document.getElementById('companies-table-body')
      }
    };
    console.log('🔍 Estado del módulo:', health);
    return health;
  }

  static cleanupCompanyModule() {
    console.log('🏢 Limpiando módulo de compañías...');
  }
}

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
  console.log('📄 DOM listo — iniciando CompanyInitializer...');
  setTimeout(() => CompanyInitializer.init(), 500);
});

// Limpiar al salir
window.addEventListener('beforeunload', () => {
  CompanyInitializer.cleanupCompanyModule();
});

console.log('📝 CompanyInitializer cargado y configurado');
