/**
 * Inicializador específico para la página de Gestión de Clases de Vehículos
 * Maneja solo los componentes y controladores necesarios para esta página específica
 */
class VehicleClassInitializer {
  static async init() {
    console.log('🚙 Inicializando página de Gestión de Clases de Vehículos...');

    if (!authChecker.checkAuthentication()) {
      console.log('❌ Usuario no autenticado, redirigiendo...');
      return;
    }

    authChecker.updateUserDisplay();
    authChecker.setupLogoutButton();

    // Inicializar ComponentLoader
    const componentLoader = new ComponentLoader();

    try {
      console.log('🔄 Cargando componentes HTML...');

      // Cargar sidebar
      await componentLoader.loadComponent('sidebar', '#sidebar-container', {
        activeSection: window.pageConfig?.activeSection || 'tablas'
      });

      // Cargar topbar
      await componentLoader.loadComponent('topbar', '#topbar-container', {
        pageTitle: window.pageConfig?.pageTitle || {
          icon: 'fas fa-car-side',
          text: 'Gestión de Clases de Vehículos'
        }
      });

      // Cargar profile modal
      await componentLoader.loadComponent('profile-modal', '#modal-container');

      console.log('✅ Todos los componentes HTML cargados');

      // Esperar a que el DOM termine de actualizarse
      setTimeout(() => {
        // Inicializar SidebarController
        if (!window.sidebarControllerInstance) {
          window.sidebarControllerInstance = new SidebarController();
          console.log('📁 SidebarController inicializado');
        }

        // Inicializar TopBarController y ProfileController
        setTimeout(() => {
          if (!window.topBarControllerInstance) {
            window.topBarControllerInstance = new TopBarController();
            console.log('🔝 TopBarController inicializado');
          }

          if (!window.profileControllerInstance) {
            window.profileControllerInstance = new ProfileController();
            console.log('👤 ProfileController inicializado');

            // Enlazar ProfileController con TopBarController
            window.topBarControllerInstance.profileController =
              window.profileControllerInstance;
            console.log('🔗 Referencia profile-topbar establecida');
          }
        }, 200);

        // Inicializar VehicleClassListController
        setTimeout(() => {
          if (
            !window.vehicleClassListControllerInstance &&
            typeof VehicleClassListController !== 'undefined'
          ) {
            window.vehicleClassListControllerInstance =
              new VehicleClassListController();
            window.vehicleClassListController = window.vehicleClassListControllerInstance;
            console.log('🚗 VehicleClassListController inicializado');
          }
          // El controlador de creación de clase de vehículo ya se instancia globalmente en su propio archivo.

          // Inicializar VehicleClassEditController
          if (!window.vehicleClassEditControllerInstance && typeof VehicleClassEditController !== 'undefined') {
            window.vehicleClassEditControllerInstance = new VehicleClassEditController();
            window.vehicleClassEditController = window.vehicleClassEditControllerInstance;
            console.log('✏️ VehicleClassEditController inicializado');
          }

          // Inicializar y exponer el controlador de eliminación de clase de vehículo
          if (!window.deleteVehicleClassControllerInstance) {
            window.deleteVehicleClassControllerInstance = new DeleteVehicleClassController();
            window.deleteVehicleClassController = window.deleteVehicleClassControllerInstance;
            console.log('🗑️ DeleteVehicleClassController inicializado');
          }

          // Enlazar el evento click de los botones de eliminar clase de vehículo
          document.body.addEventListener('click', function(e) {
            const btn = e.target.closest('[data-action="delete-vehicle-class"]');
            if (btn) {
              const classId = parseInt(btn.getAttribute('data-vehicle-class-id'), 10);
              const className = btn.getAttribute('data-vehicle-class-name') || '';
              const vehicleClassData = { id: classId, name: className };
              window.deleteVehicleClassController.handleDeleteButtonClick(btn, vehicleClassData);
            }
          });
        }, 300);

        // Configurar permisos y notificar carga completa
        setTimeout(() => {
          if (window.PermissionsService) {
            console.log('🔧 Inicializando sistema de permisos...');
            window.PermissionsService.initializePermissions();
          }

          console.log('✅ Clases de Vehículos inicializado completamente');
          LoadingScreenUtil.notifyModuleLoaded('VehicleClass');
        }, 400);
      }, 500);

    } catch (error) {
      console.error('❌ Error cargando componentes:', error);
      // Asegurar que la pantalla de carga se oculte incluso tras error
      LoadingScreenUtil.notifyModuleLoaded('VehicleClass');
    }
  }
}

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
  console.log('📄 DOM cargado, iniciando VehicleClassInitializer...');
  setTimeout(() => VehicleClassInitializer.init(), 500);
});

console.log('📝 VehicleClassInitializer definido y configurado');
