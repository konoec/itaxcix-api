/**
 * Inicializador específico para la página de Gestión de Empresas
 * Maneja solo los componentes y controladores necesarios para esta página específica
 */
class CompanyInitializer {
    static async init() {
        console.log('🏢 Inicializando página de Gestión de Empresas...');
        
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
                    pageTitle: window.pageConfig?.pageTitle || { icon: 'fas fa-building', text: 'Gestión de Empresas' }
                });
                
                // Cargar profile modal
                await componentLoader.loadComponent('profile-modal', '#modal-container');
                
                console.log('✅ Todos los componentes HTML cargados');
                
                // Esperar más tiempo para que el DOM se actualice completamente
                setTimeout(() => {
                    // Inicializar controladores base
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
                    }, 200);
                    
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
                    
                    // Inicializar controlador específico de empresas
                    setTimeout(() => {
                        CompanyInitializer.initializeCompanyModule();
                    }, 100);
                    
                    // Configurar permisos DESPUÉS de que los controladores estén listos
                    setTimeout(() => {
                        if (window.PermissionsService) {
                            console.log('🔧 Inicializando sistema de permisos...');
                            window.PermissionsService.initializePermissions();
                        }
                        
                        console.log('✅ Gestión de Empresas inicializada completamente');
                    }, 200);
                    
                }, 500);
                
            } catch (error) {
                console.error('❌ Error cargando componentes:', error);
            }
            
        } else {
            console.log('❌ Usuario no autenticado, redirigiendo...');
        }
    }

    /**
     * Función de inicialización principal del módulo de empresas
     */
    static initializeCompanyModule() {
    console.log('🏢 Inicializando módulo de empresas...');
    
    try {
        // Verificar que las dependencias estén disponibles
        if (typeof CompanyService === 'undefined') {
            throw new Error('CompanyService no está disponible');
        }
        
        if (typeof CompanyController === 'undefined') {
            throw new Error('CompanyController no está disponible');
        }
        
        // Verificar que el DOM tenga los elementos necesarios
        if (!document.getElementById('companies-content')) {
            throw new Error('Elementos del DOM no encontrados. Verifique que la página esté completamente cargada.');
        }
        
        // Agregar un pequeño delay para asegurar que todos los elementos DOM estén listos
        setTimeout(() => {
            // Los controladores base ya están inicializados, solo inicializar el controlador específico
            
            // Inicializar el controlador principal
            window.companyController = new CompanyController();
            
            // Configurar eventos globales específicos del módulo
            CompanyInitializer.setupGlobalEvents();
            
            // Configurar atajos de teclado
            CompanyInitializer.setupKeyboardShortcuts();
            
            // Inicializar tooltips de Tabler
            CompanyInitializer.initializeTooltips();
            
            console.log('✅ Módulo de compañías inicializado correctamente');
            
            // Notificar que el módulo está listo
            if (window.showToast) {
                window.showToast('Sistema de gestión de compañías cargado', 'success');
            }
        }, 100);
        
    } catch (error) {
        console.error('❌ Error al inicializar módulo de compañías:', error);
        
        // Mostrar error al usuario
        if (window.showToast) {
            window.showToast('Error al cargar el módulo: ' + error.message, 'error');
        }
        
        // Intentar recuperación básica
        CompanyInitializer.attemptRecovery();
    }
    }

    /**
     * Configura eventos globales específicos del módulo
     */
    static setupGlobalEvents() {
    console.log('🏢 Configurando eventos globales...');
    
    // Manejar errores no capturados en el módulo
    window.addEventListener('error', (event) => {
        if (event.filename && event.filename.includes('company')) {
            console.error('❌ Error no capturado en módulo de compañías:', event.error);
            
            if (window.showToast) {
                window.showToast('Error inesperado en el sistema', 'error');
            }
        }
    });
    
    // Manejar cambios de conectividad
    window.addEventListener('online', () => {
        console.log('🌐 Conexión restaurada');
        if (window.companyController) {
            window.companyController.refreshData();
        }
        if (window.showToast) {
            window.showToast('Conexión restaurada', 'success');
        }
    });
    
    window.addEventListener('offline', () => {
        console.log('🌐 Conexión perdida');
        if (window.showToast) {
            window.showToast('Sin conexión a internet', 'warning');
        }
    });
    
    // Manejar visibilidad de la página
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden && window.companyController) {
            // Refrescar datos cuando la página vuelve a ser visible
            setTimeout(() => {
                if (window.companyController && !window.companyController.isLoading) {
                    window.companyController.refreshData();
                }
            }, 1000);
        }
    });
    
    console.log('✅ Eventos globales configurados');
}

    /**
     * Configura atajos de teclado para el módulo
     */
    static setupKeyboardShortcuts() {
    console.log('🏢 Configurando atajos de teclado...');
    
    document.addEventListener('keydown', (event) => {
        // Solo procesar si no estamos en un input o textarea
        if (event.target.tagName === 'INPUT' || event.target.tagName === 'TEXTAREA') {
            return;
        }
        
        // Ctrl/Cmd + R: Refrescar datos
        if ((event.ctrlKey || event.metaKey) && event.key === 'r') {
            event.preventDefault();
            if (window.companyController) {
                window.companyController.refreshData();
            }
        }
        
        // Ctrl/Cmd + N: Nueva compañía
        if ((event.ctrlKey || event.metaKey) && event.key === 'n') {
            event.preventDefault();
            if (window.companyController) {
                window.companyController.handleAddCompany();
            }
        }
        
        // F5: Refrescar página
        if (event.key === 'F5') {
            event.preventDefault();
            if (window.companyController) {
                window.companyController.refreshData();
            }
        }
        
        // Escape: Limpiar filtros
        if (event.key === 'Escape') {
            if (window.companyController) {
                window.companyController.clearFilters();
            }
        }
        
        // Ctrl/Cmd + F: Enfocar búsqueda
        if ((event.ctrlKey || event.metaKey) && event.key === 'f') {
            event.preventDefault();
            const searchInput = document.getElementById('company-search-input');
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }
    });
    
    console.log('✅ Atajos de teclado configurados');
}

    /**
     * Inicializa tooltips de Tabler
     */
    static initializeTooltips() {
    console.log('🏢 Inicializando tooltips...');
    
    try {
        // Verificar si Bootstrap está disponible
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            // Inicializar tooltips
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            console.log('✅ Tooltips inicializados');
        } else {
            console.log('⚠️ Bootstrap no disponible, saltando inicialización de tooltips');
        }
    } catch (error) {
        console.warn('⚠️ Error al inicializar tooltips:', error);
    }
}

    /**
     * Intenta recuperarse de errores críticos
     */
    static attemptRecovery() {
    console.log('🏢 Intentando recuperación...');
    
    try {
        // Ocultar loading si está visible
        const loadingContainer = document.getElementById('companies-loading');
        if (loadingContainer) {
            loadingContainer.style.display = 'none';
        }
        
        // Mostrar contenido con mensaje de error
        const contentContainer = document.getElementById('companies-content');
        if (contentContainer) {
            contentContainer.style.display = 'block';
            
            // Mostrar mensaje de error en la tabla
            const tableBody = document.getElementById('companies-table-body');
            if (tableBody) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <div class="empty">
                                <div class="empty-icon">
                                    <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                                </div>
                                <p class="empty-title">Error al cargar el módulo</p>
                                <p class="empty-subtitle text-muted">
                                    Ha ocurrido un error al inicializar el sistema de gestión de compañías
                                </p>
                                <div class="empty-action">
                                    <button class="btn btn-primary" onclick="location.reload()">
                                        <i class="fas fa-sync me-2"></i>
                                        Recargar página
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                `;
            }
        }
        
        console.log('✅ Recuperación básica completada');
        
    } catch (recoveryError) {
        console.error('❌ Error en recuperación:', recoveryError);
        
        // Última opción: recargar la página
        if (confirm('El sistema ha encontrado un error crítico. ¿Desea recargar la página?')) {
            location.reload();
        }
    }
}

    /**
     * Función para verificar el estado del módulo
     */
    static checkModuleHealth() {
    const health = {
        controller: !!window.companyController,
        service: !!window.CompanyService,
        dom: {
            loading: !!document.getElementById('companies-loading'),
            content: !!document.getElementById('companies-content'),
            table: !!document.getElementById('companies-table-body')
        }
    };
    
    console.log('🏢 Estado del módulo:', health);
    return health;
}

    /**
     * Función de limpieza para cuando se salga del módulo
     */
    static cleanupCompanyModule() {
    console.log('🏢 Limpiando módulo de compañías...');
    
    try {
        // Limpiar timers si existen
        if (window.companyController && window.companyController.filterTimeout) {
            clearTimeout(window.companyController.filterTimeout);
        }
        
        // Limpiar cache del servicio
        if (window.companyController && window.companyController.companyService) {
            window.companyController.companyService.clearCache();
        }
        
        console.log('✅ Módulo de compañías limpiado');
        
    } catch (error) {
        console.warn('⚠️ Error al limpiar módulo:', error);
    }
}

    /**
     * Función de diagnóstico para verificar el estado de todos los controladores
     */
    static diagnoseControllers() {
    console.log('🔍 DIAGNÓSTICO DE CONTROLADORES');
    console.log('─'.repeat(50));
    
    // Verificar disponibilidad de clases
    console.log('📚 CLASES DISPONIBLES:');
    console.log('  SidebarController:', typeof SidebarController !== 'undefined' ? '✅' : '❌');
    console.log('  TopBarController:', typeof TopBarController !== 'undefined' ? '✅' : '❌');
    console.log('  ProfileController:', typeof ProfileController !== 'undefined' ? '✅' : '❌');
    console.log('  CompanyController:', typeof CompanyController !== 'undefined' ? '✅' : '❌');
    console.log('  CompanyService:', typeof CompanyService !== 'undefined' ? '✅' : '❌');
    
    // Verificar instancias
    console.log('\n🏗️ INSTANCIAS CREADAS:');
    console.log('  window.sidebarControllerInstance:', window.sidebarControllerInstance ? '✅' : '❌');
    console.log('  window.topBarControllerInstance:', window.topBarControllerInstance ? '✅' : '❌');
    console.log('  window.profileControllerInstance:', window.profileControllerInstance ? '✅' : '❌');
    console.log('  window.companyController:', window.companyController ? '✅' : '❌');
    
    // Verificar elementos DOM críticos
    console.log('\n🏠 ELEMENTOS DOM:');
    console.log('  .sidebar:', document.querySelector('.sidebar') ? '✅' : '❌');
    console.log('  .top-bar:', document.querySelector('.top-bar') ? '✅' : '❌');
    console.log('  #profile-container:', document.getElementById('profile-container') ? '✅' : '❌');
    console.log('  #profile-image:', document.getElementById('profile-image') ? '✅' : '❌');
    console.log('  #user-display:', document.getElementById('user-display') ? '✅' : '❌');
    console.log('  #companies-content:', document.getElementById('companies-content') ? '✅' : '❌');
    
    // Verificar servicios globales
    console.log('\n🌐 SERVICIOS GLOBALES:');
    console.log('  window.PermissionsService:', window.PermissionsService ? '✅' : '❌');
    console.log('  window.GlobalInitializer:', window.GlobalInitializer ? '✅' : '❌');
    console.log('  window.routeGuard:', window.routeGuard ? '✅' : '❌');
    
    console.log('─'.repeat(50));
    console.log('✅ Diagnóstico completado');
    }
}

// Exponer funciones globalmente para debugging
window.checkCompanyModuleHealth = CompanyInitializer.checkModuleHealth;
window.cleanupCompanyModule = CompanyInitializer.cleanupCompanyModule;
window.diagnoseControllers = CompanyInitializer.diagnoseControllers;

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('📄 DOM cargado, iniciando CompanyInitializer...');
    
    // Pequeño delay para asegurar que todos los scripts estén cargados
    setTimeout(() => {
        CompanyInitializer.init();
    }, 500);
});

// Limpiar al salir de la página
window.addEventListener('beforeunload', CompanyInitializer.cleanupCompanyModule);

console.log('📝 CompanyInitializer definido y configurado');
