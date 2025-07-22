// Servicio para manejar permisos y módulos
class PermissionsService {    constructor() {
        // Mapeo de permisos a módulos/rutas
        this.modulePermissions = {            'ADMISIÓN DE CONDUCTORES': {
                permission: 'ADMISIÓN DE CONDUCTORES',
                route: '/pages/Admission/AdmissionControl.html',
                menuId: 'menu-admission',
                title: 'Admisión de Conductores',
                icon: 'fas fa-user-plus'
            },           
            'TABLAS MAESTRAS': {
                permission: 'TABLAS MAESTRAS',
                route: '/pages/MasterTables/',
                menuId: 'menu-tablas',
                title: 'Tablas Maestras',
                icon: 'fas fa-table'
            },
            'TABLAS MAESTRAS - EMPRESAS': {
                permission: 'TABLAS MAESTRAS',
                route: '/pages/MasterTables/Companies.html',
                menuId: 'menu-tablas-empresas',
                title: 'Empresas',
                icon: 'fas fa-building'
            },
            'TABLAS MAESTRAS - CONFIGURACIÓN': {
                permission: 'TABLAS MAESTRAS',
                route: '/pages/MasterTables/Configuration.html',
                menuId: 'menu-tablas-configuracion',
                title: 'Configuración',
                icon: 'fas fa-cogs'
            },
            'TABLAS MAESTRAS - DISTRITO': {
                permission: 'TABLAS MAESTRAS',
                route: '/pages/MasterTables/District.html',
                menuId: 'menu-tablas-distrito',
                title: 'Distrito',
                icon: 'fas fa-map-marked-alt'
            },
            
            // Nuevos módulos de Tablas Maestras - Ubicación Geográfica
            'TABLAS MAESTRAS - DEPARTAMENTOS': {
                permission: 'TABLAS MAESTRAS',
                route: '/pages/MasterTables/Departaments.html',
                menuId: 'menu-tablas-departamentos',
                title: 'Departamentos',
                icon: 'fas fa-map'
            },
            'TABLAS MAESTRAS - PROVINCIAS': {
                permission: 'TABLAS MAESTRAS',
                route: '/pages/MasterTables/Province.html',
                menuId: 'menu-tablas-provincias',
                title: 'Provincias',
                icon: 'fas fa-map-marker-alt'
            },
            
            // Nuevos módulos de Tablas Maestras - Usuarios y Conductores
            'TABLAS MAESTRAS - ESTADO DE USUARIOS': {
                permission: 'TABLAS MAESTRAS',
                route: '/pages/MasterTables/UserStatus.html',
                menuId: 'menu-tablas-user-status',
                title: 'Estado de Usuarios',
                icon: 'fas fa-user-circle'
            },
            'TABLAS MAESTRAS - TIPOS DE CÓDIGO USUARIO': {
                permission: 'TABLAS MAESTRAS',
                route: '/pages/MasterTables/UserCodeType.html',
                menuId: 'menu-tablas-user-code-type',
                title: 'Tipos de Código Usuario',
                icon: 'fas fa-user-tag'
            },
            'TABLAS MAESTRAS - ESTADO DE CONDUCTORES': {
                permission: 'TABLAS MAESTRAS',
                route: '/pages/MasterTables/DriverStatus.html',
                menuId: 'menu-tablas-driver-status',
                title: 'Estado de Conductores',
                icon: 'fas fa-user-check'
            },
            'TABLAS MAESTRAS - TIPOS DE CONTACTO': {
                permission: 'TABLAS MAESTRAS',
                route: '/pages/MasterTables/ContactTypes.html',
                menuId: 'menu-tablas-contact-types',
                title: 'Tipos de Contacto',
                icon: 'fas fa-address-book'
            },
            'TABLAS MAESTRAS - TIPOS DE DOCUMENTOS': {
                permission: 'TABLAS MAESTRAS',
                route: '/pages/MasterTables/DocumentTypes.html',
                menuId: 'menu-tablas-document-types',
                title: 'Tipos de Documentos',
                icon: 'fas fa-file-alt'
            },
            
            // Nuevos módulos de Tablas Maestras - Vehículos
            'TABLAS MAESTRAS - MARCAS': {
                permission: 'TABLAS MAESTRAS',
                route: '/pages/MasterTables/Brand.html',
                menuId: 'menu-tablas-brands',
                title: 'Marcas',
                icon: 'fas fa-tags'
            },
            'TABLAS MAESTRAS - MODELOS DE VEHÍCULOS': {
                permission: 'TABLAS MAESTRAS',
                route: '/pages/MasterTables/VehicleModel.html',
                menuId: 'menu-tablas-vehicle-model',
                title: 'Modelos de Vehículos',
                icon: 'fas fa-car'
            },
            'TABLAS MAESTRAS - CLASES DE VEHÍCULOS': {
                permission: 'TABLAS MAESTRAS',
                route: '/pages/MasterTables/VehicleClass.html',
                menuId: 'menu-tablas-vehicle-class',
                title: 'Clases de Vehículos',
                icon: 'fas fa-car-side'
            },
            'TABLAS MAESTRAS - COLORES': {
                permission: 'TABLAS MAESTRAS',
                route: '/pages/MasterTables/Color.html',
                menuId: 'menu-tablas-colors',
                title: 'Colores',
                icon: 'fas fa-palette'
            },
            'TABLAS MAESTRAS - TIPOS DE COMBUSTIBLE': {
                permission: 'TABLAS MAESTRAS',
                route: '/pages/MasterTables/FuelType.html',
                menuId: 'menu-tablas-fuel-type',
                title: 'Tipos de Combustible',
                icon: 'fas fa-gas-pump'
            },
            'TABLAS MAESTRAS - CATEGORÍAS': {
                permission: 'TABLAS MAESTRAS',
                route: '/pages/MasterTables/Category.html',
                menuId: 'menu-tablas-categories',
                title: 'Categorías',
                icon: 'fas fa-folder'
            },
            
            // Nuevos módulos de Tablas Maestras - Servicios y Procedimientos
            'TABLAS MAESTRAS - TIPOS DE SERVICIO': {
                permission: 'TABLAS MAESTRAS',
                route: '/pages/MasterTables/ServiceType.html',
                menuId: 'menu-tablas-service-type',
                title: 'Tipos de Servicio',
                icon: 'fas fa-concierge-bell'
            },
            'TABLAS MAESTRAS - TIPOS DE PROCEDIMIENTOS': {
                permission: 'TABLAS MAESTRAS',
                route: '/pages/MasterTables/ProcedureTypes.html',
                menuId: 'menu-tablas-procedure-types',
                title: 'Tipos de Procedimientos',
                icon: 'fas fa-clipboard-list'
            },
            'TABLAS MAESTRAS - ESTADO DE VIAJES': {
                permission: 'TABLAS MAESTRAS',
                route: '/pages/MasterTables/TravelStatus.html',
                menuId: 'menu-tablas-travel-status',
                title: 'Estado de Viajes',
                icon: 'fas fa-route'
            },
            
            // Nuevos módulos de Tablas Maestras - TUC e Infracciones
            'TABLAS MAESTRAS - MODALIDADES TUC': {
                permission: 'TABLAS MAESTRAS',
                route: '/pages/MasterTables/TucModality.html',
                menuId: 'menu-tablas-tuc-modality',
                title: 'Modalidades TUC',
                icon: 'fas fa-id-card'
            },
            'TABLAS MAESTRAS - ESTADO TUC': {
                permission: 'TABLAS MAESTRAS',
                route: '/pages/MasterTables/TucStatus.html',
                menuId: 'menu-tablas-tuc-status',
                title: 'Estado TUC',
                icon: 'fas fa-id-badge'
            },
            'TABLAS MAESTRAS - TIPOS DE INCIDENTES': {
                permission: 'TABLAS MAESTRAS',
                route: '/pages/MasterTables/IncidentType.html',
                menuId: 'menu-tablas-incident-type',
                title: 'Tipos de Incidentes',
                icon: 'fas fa-exclamation-triangle'
            },
            'TABLAS MAESTRAS - SEVERIDAD DE INFRACCIONES': {
                permission: 'TABLAS MAESTRAS',
                route: '/pages/MasterTables/InfractionSeverity.html',
                menuId: 'menu-tablas-infraction-severity',
                title: 'Severidad de Infracciones',
                icon: 'fas fa-gavel'
            },
            'TABLAS MAESTRAS - ESTADO DE INFRACCIONES': {
                permission: 'TABLAS MAESTRAS',
                route: '/pages/MasterTables/InfractionStatus.html',
                menuId: 'menu-tablas-infraction-status',
                title: 'Estado de Infracciones',
                icon: 'fas fa-clipboard-check'
            },
            'AUDITORIA': {
                permission: 'AUDITORIA',
                route: '/pages/Audit/',
                menuId: 'menu-auditoria',
                title: 'Auditoría',
                icon: 'fas fa-clipboard-list'
            },
            'AUDITORIA - REGISTRO': {
                permission: 'AUDITORIA',
                route: '/pages/Audit/AuditRegistry.html',
                menuId: 'menu-auditoria-registro',
                title: 'Registro de Auditoría',
                icon: 'fas fa-history'
            },
            'REPORTE-INFRACCIONES': {
                permission: 'AUDITORIA',
                route: '/pages/Audit/InfractionsReports.html',
                menuId: 'menu-auditoria-infracciones',
                title: 'Reporte de Infracciones',
                icon: 'fas fa-gavel'
            },
            'REPORTE-INCIDENTES': {
                permission: 'AUDITORIA',
                route: '/pages/Audit/IncidentsReports.html',
                menuId: 'menu-auditoria-incidentes',
                title: 'Reporte de Incidentes',
                icon: 'fas fa-exclamation-triangle'
            },
            'REPORTE-VIAJES': {
                permission: 'AUDITORIA',
                route: '/pages/Audit/TravelsReports.html',
                menuId: 'menu-auditoria-viajes',
                title: 'Reporte de Viajes',
                icon: 'fas fa-taxi'
            },
            'REPORTE-USUARIOS': {
                permission: 'AUDITORIA',
                route: '/pages/Audit/UsersReports.html',
                menuId: 'menu-auditoria-usuarios',
                title: 'Reporte de Usuarios',
                icon: 'fas fa-users'
            },
            'REPORTE-VEHICULOS': {
                permission: 'AUDITORIA',
                route: '/pages/Audit/VehicleReports.html',
                menuId: 'menu-auditoria-vehiculos',
                title: 'Reporte de Vehículos',
                icon: 'fas fa-car'
            },
            'REPORTE-CALIFICACIONES': {
                permission: 'AUDITORIA',
                route: '/pages/Audit/RatingReports.html',
                menuId: 'menu-auditoria-calificaciones',
                title: 'Reporte de Calificaciones',
                icon: 'fas fa-list-alt'
            },
              
            
            'CONFIGURACIÓN': {
                permission: 'CONFIGURACIÓN',
                route: '/pages/Configuration/Configuration.html',
                menuId: 'menu-configuracion',
                title: 'Configuración',
                icon: 'fas fa-cog'
            },
            'CONFIGURACIÓN - PERMISOS': {
                permission: 'CONFIGURACIÓN',
                route: '/pages/Configuration/PermissionsManagement.html',
                menuId: 'menu-configuracion-permisos',
                title: 'Permisos',
                icon: 'fas fa-shield-alt'
            },
            'CONFIGURACIÓN - ROLES': {
                permission: 'CONFIGURACIÓN',
                route: '/pages/Configuration/RolesManagement.html',
                menuId: 'menu-configuracion-roles',
                title: 'Roles',
                icon: 'fas fa-user-tag'
            },
            'CONFIGURACIÓN - CENTRO DE AYUDA': {
                permission: 'CONFIGURACIÓN',
                route: '/pages/Configuration/HelpCenter.html',
                menuId: 'menu-configuracion-ayuda',
                title: 'Centro de Ayuda',
                icon: 'fas fa-question-circle'
            },
            'CONFIGURACIÓN - USUARIOS': {
                permission: 'CONFIGURACIÓN',
                route: '/pages/Configuration/UsersManagement.html',
                menuId: 'menu-configuracion-usuarios',
                title: 'Usuarios',
                icon: 'fas fa-users'
            }
        };        // Página por defecto (sin permisos requeridos)
        this.defaultRoute = '/pages/Inicio/Inicio.html';

        console.log('🔐 PermissionsService inicializado');
    }

    /**
     * Obtiene los permisos del usuario desde sessionStorage
     * @returns {Array} - Array de permisos
     */
    getUserPermissions() {
        try {
            const permissions = sessionStorage.getItem("userPermissions");
            return permissions ? JSON.parse(permissions) : [];
        } catch (error) {
            console.error('❌ Error al obtener permisos:', error);
            return [];
        }
    }

    /**
     * Verifica si el usuario tiene un permiso específico
     * @param {string} permission - Permiso a verificar
     * @returns {boolean}
     */
    hasPermission(permission) {
        const userPermissions = this.getUserPermissions();
        const hasAccess = userPermissions.includes(permission);
        
        console.log(`🔍 Verificando permiso "${permission}":`, hasAccess);
        return hasAccess;
    }

    /**
     * Obtiene todos los módulos a los que el usuario tiene acceso
     * @returns {Array} - Array de módulos disponibles
     */
    getAvailableModules() {
        const userPermissions = this.getUserPermissions();
        const availableModules = [];

        for (const [permission, moduleInfo] of Object.entries(this.modulePermissions)) {
            if (userPermissions.includes(permission)) {
                availableModules.push({
                    ...moduleInfo,
                    hasAccess: true
                });
            }
        }

        console.log('📋 Módulos disponibles:', availableModules);
        return availableModules;
    }    /**
     * Obtiene la ruta del primer módulo disponible para redirección
     * @returns {string} - Siempre retorna la página de inicio por defecto
     */
    getFirstAvailableRoute() {
        console.log('🏠 Redirigiendo siempre a página de inicio por defecto');
        return this.defaultRoute;
    }/**
     * Configura elementos del menú basado en permisos
     * Los elementos sin permisos se deshabilitan pero permanecen visibles
     */
    configureMenuPermissions() {
        const userPermissions = this.getUserPermissions();
        let disabledCount = 0;
        let enabledCount = 0;

        console.log('🔒 Configurando estado del menú según permisos...');
        console.log('👤 Permisos del usuario:', userPermissions);

        for (const [permission, moduleInfo] of Object.entries(this.modulePermissions)) {
            const menuElement = document.getElementById(moduleInfo.menuId);
            // CAMBIO: Usar moduleInfo.permission para la verificación
            const requiredPermission = moduleInfo.permission;
            if (menuElement) {
                const hasPermission = userPermissions.includes(requiredPermission);
                if (hasPermission) {
                    // Habilitar elemento
                    this.enableMenuItem(menuElement, moduleInfo);
                    enabledCount++;
                    console.log(`✅ Habilitando menú: ${moduleInfo.title}`);
                } else {
                    // Deshabilitar elemento
                    this.disableMenuItem(menuElement, moduleInfo);
                    disabledCount++;
                    console.log(`🔒 Deshabilitando menú: ${moduleInfo.title}`);
                }
            } else {
                // Solo mostrar warning si hay elementos presentes en el DOM pero este específico no se encuentra
                const sidebarExists = !!document.getElementById('sidebar');
                if (sidebarExists) {
                    console.warn(`⚠️ Elemento del menú no encontrado: ${moduleInfo.menuId}`);
                } else {
                    console.log(`⏳ Sidebar no cargado aún, omitiendo ${moduleInfo.menuId}`);
                }
            }
        }

        console.log(`📊 Menú configurado: ${enabledCount} habilitado(s), ${disabledCount} deshabilitado(s)`);
        
        // Solo mostrar mensaje de error si hay elementos en el DOM pero no se configuró ninguno
        const sidebarExists = !!document.getElementById('sidebar');
        if (enabledCount === 0 && userPermissions.length === 0) {
            console.log('ℹ️ Usuario sin permisos específicos, permitiendo acceso a inicio por defecto');
        } else if (enabledCount === 0 && userPermissions.length > 0 && sidebarExists) {
            console.warn('⚠️ Usuario tiene permisos pero no hay elementos de menú correspondientes');
        } else if (!sidebarExists) {
            console.log('⏳ Configuración de menú pendiente - sidebar no cargado');
        }
    }

    /**
     * Método de compatibilidad - alias para configureMenuPermissions
     * @deprecated Use configureMenuPermissions() instead
     */
    hideUnauthorizedMenuItems() {
        console.warn('⚠️ hideUnauthorizedMenuItems() está obsoleto. Use configureMenuPermissions()');
        return this.configureMenuPermissions();
    }    /**
     * Habilita un elemento del menú
     * @param {HTMLElement} menuElement - Elemento del menú
     * @param {Object} moduleInfo - Información del módulo
     */
    enableMenuItem(menuElement, moduleInfo) {
        console.log(`✅ Habilitando elemento: ${moduleInfo.title}`, menuElement);
        
        // Remover clases de deshabilitado
        menuElement.classList.remove('disabled', 'permission-disabled', 'menu-disabled');
        
        // Restaurar estilos del elemento padre
        menuElement.style.opacity = '';
        menuElement.style.pointerEvents = '';
        menuElement.style.transition = '';
        
        // Restaurar enlaces y eventos
        const linkElement = menuElement.querySelector('a');
        if (linkElement) {
            console.log(`📎 Habilitando enlace en: ${moduleInfo.title}`, linkElement);
            
            // Remover estilos inline forzados
            linkElement.style.removeProperty('pointer-events');
            linkElement.style.removeProperty('cursor');
            linkElement.style.removeProperty('color');
            linkElement.style.removeProperty('opacity');
            linkElement.style.removeProperty('text-decoration');
            
            // Remover clases de deshabilitado
            linkElement.classList.remove('disabled-link', 'permission-disabled-link');
            
            // Remover atributo disabled
            linkElement.removeAttribute('disabled');
            linkElement.removeAttribute('aria-disabled');
            
            // Remover evento de prevención si existe
            if (linkElement._preventClickHandler) {
                linkElement.removeEventListener('click', linkElement._preventClickHandler, true);
                delete linkElement._preventClickHandler;
            }
            
            // Restaurar título original si existe
            if (linkElement.dataset.originalTitle) {
                linkElement.title = linkElement.dataset.originalTitle;
                delete linkElement.dataset.originalTitle;
            } else {
                linkElement.removeAttribute('title');
            }
            
            // Restaurar iconos también
            const iconElement = linkElement.querySelector('i');
            if (iconElement) {
                iconElement.style.removeProperty('color');
                iconElement.style.removeProperty('opacity');
            }
        }
        
        // Remover indicador de permisos si existe
        const indicator = menuElement.querySelector('.permission-indicator');
        if (indicator) {
            indicator.remove();
        }
        
        // Remover datos de debugging
        delete menuElement.dataset.permissionStatus;
        delete menuElement.dataset.requiredPermission;
        
        console.log(`✅ Elemento habilitado completamente: ${moduleInfo.title}`);
    }/**
     * Deshabilita un elemento del menú
     * @param {HTMLElement} menuElement - Elemento del menú
     * @param {Object} moduleInfo - Información del módulo
     */
    disableMenuItem(menuElement, moduleInfo) {
        console.log(`🔒 Deshabilitando elemento: ${moduleInfo.title}`, menuElement);
        
        // Agregar clases de deshabilitado con múltiples variantes
        menuElement.classList.add('disabled', 'permission-disabled', 'menu-disabled');
        
        // Aplicar estilos directos al elemento padre para garantizar visibilidad
        menuElement.style.opacity = '0.5';
        menuElement.style.transition = 'opacity 0.3s ease';
        
        // Deshabilitar enlaces y eventos
        const linkElement = menuElement.querySelector('a');
        if (linkElement) {
            console.log(`📎 Deshabilitando enlace en: ${moduleInfo.title}`, linkElement);
            
            // Guardar título original
            if (linkElement.title && !linkElement.dataset.originalTitle) {
                linkElement.dataset.originalTitle = linkElement.title;
            }
            
            // Aplicar estilos de deshabilitado con !important para forzar aplicación
            linkElement.style.setProperty('pointer-events', 'none', 'important');
            linkElement.style.setProperty('cursor', 'not-allowed', 'important');
            linkElement.style.setProperty('color', '#999', 'important');
            linkElement.style.setProperty('opacity', '0.6', 'important');
            linkElement.style.setProperty('text-decoration', 'none', 'important');
            
            // Agregar clases adicionales al enlace
            linkElement.classList.add('disabled-link', 'permission-disabled-link');
            
            // Agregar atributo disabled
            linkElement.setAttribute('disabled', 'true');
            linkElement.setAttribute('aria-disabled', 'true');
            
            // Prevenir clicks - usar función arrow para mantener contexto
            const preventClick = (event) => this.preventDefaultClick(event, moduleInfo);
            linkElement.addEventListener('click', preventClick, true); // true para captura
            
            // Guardar referencia para poder removerla después
            linkElement._preventClickHandler = preventClick;
            
            // Actualizar título para mostrar motivo
            linkElement.title = `🔒 Sin permisos para acceder a ${moduleInfo.title}`;
            
            // Deshabilitar iconos también
            const iconElement = linkElement.querySelector('i');
            if (iconElement) {
                iconElement.style.setProperty('color', '#999', 'important');
                iconElement.style.setProperty('opacity', '0.7', 'important');
            }
        }
        
        // Agregar indicador visual adicional
        this.addPermissionIndicator(menuElement, moduleInfo);
        
        // Agregar datos para debugging
        menuElement.dataset.permissionStatus = 'disabled';
        menuElement.dataset.requiredPermission = moduleInfo.permission;
          console.log(`✅ Elemento deshabilitado completamente: ${moduleInfo.title}`);
    }

    /**
     * Previene el comportamiento por defecto de clicks en elementos deshabilitados
     * @param {Event} event - Evento de click
     * @param {Object} moduleInfo - Información del módulo
     */
    preventDefaultClick(event, moduleInfo) {
        event.preventDefault();
        event.stopPropagation();
        
        // Mostrar mensaje informativo
        console.log(`🚫 Acceso denegado: Sin permisos para ${moduleInfo?.title || 'este módulo'}`);
        
        // Mostrar tooltip informativo
        this.showPermissionTooltip(event.target, moduleInfo);
        
        return false;
    }

    /**
     * Agrega indicador visual de falta de permisos
     * @param {HTMLElement} menuElement - Elemento del menú
     * @param {Object} moduleInfo - Información del módulo
     */
    addPermissionIndicator(menuElement, moduleInfo) {
        // Verificar si ya existe el indicador
        if (menuElement.querySelector('.permission-indicator')) {
            return;
        }
        
        // Crear indicador visual
        const indicator = document.createElement('span');
        indicator.className = 'permission-indicator';
        indicator.innerHTML = '<i class="fas fa-lock" style="font-size: 12px; margin-left: 5px; color: #dc3545;"></i>';
        indicator.title = `Sin permisos para ${moduleInfo.title}`;
        
        // Agregar al final del enlace
        const linkElement = menuElement.querySelector('a');
        if (linkElement) {
            linkElement.appendChild(indicator);
        }
    }

    /**
     * Muestra tooltip informativo sobre permisos
     * @param {HTMLElement} element - Elemento que disparó el evento
     * @param {Object} moduleInfo - Información del módulo
     */
    showPermissionTooltip(element, moduleInfo) {
        // Remover tooltip existente si hay uno
        const existingTooltip = document.querySelector('.permission-tooltip');
        if (existingTooltip) {
            existingTooltip.remove();
        }
        
        // Crear tooltip temporal
        const tooltip = document.createElement('div');
        tooltip.className = 'permission-tooltip';
        tooltip.style.cssText = `
            position: fixed;
            background: #333;
            color: white;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 12px;
            z-index: 10000;
            pointer-events: none;
            max-width: 250px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
            border-left: 3px solid #dc3545;
        `;
        
        tooltip.innerHTML = `
            <div style="font-weight: bold; margin-bottom: 4px;">
                🔒 Acceso Restringido
            </div>
            <div style="font-size: 11px; color: #ccc;">
                Módulo: <strong>${moduleInfo?.title || 'N/A'}</strong><br>
                Permiso requerido: <strong>${moduleInfo?.permission || 'N/A'}</strong>
            </div>
        `;
        
        // Calcular posición
        const rect = element.getBoundingClientRect();
        const tooltipX = rect.right + 10;
        const tooltipY = rect.top + window.scrollY;
        
        // Ajustar si se sale de la pantalla
        const maxX = window.innerWidth - 270; // 250px + padding
        const finalX = Math.min(tooltipX, maxX);
        
        tooltip.style.left = finalX + 'px';
        tooltip.style.top = tooltipY + 'px';
        
        document.body.appendChild(tooltip);
        
        // Remover tooltip después de 3 segundos
        setTimeout(() => {
            if (tooltip.parentNode) {
                tooltip.parentNode.removeChild(tooltip);
            }
        }, 3000);
        
        // También remover al hacer click en cualquier lugar
        const removeTooltip = () => {
            if (tooltip.parentNode) {
                tooltip.parentNode.removeChild(tooltip);
            }
            document.removeEventListener('click', removeTooltip);
        };
        setTimeout(() => document.addEventListener('click', removeTooltip), 100);
    }    /**
     * Valida si el usuario puede acceder a la ruta actual
     * @param {string} currentPath - Ruta actual
     * @returns {boolean}
     */
    validateCurrentRoute(currentPath) {
        // Normalizar ruta
        const normalizedPath = currentPath.toLowerCase();
        console.log(`🔍 Validando acceso a ruta: ${currentPath}`);
        if (normalizedPath.includes('/inicio/') || normalizedPath.includes('inicio.html')) {
            console.log(`✅ Acceso permitido al inicio (página por defecto)`);
            return true;
        }
        for (const [permission, moduleInfo] of Object.entries(this.modulePermissions)) {
            if (!moduleInfo.route) {
                continue;
            }
            const modulePath = moduleInfo.route.toLowerCase();
            
            // Verificar si la ruta actual corresponde a este módulo
            // Usar múltiples formas de matching para ser más flexible
            const isThisModule = 
                normalizedPath.includes(modulePath.replace(/^\//, '')) || 
                normalizedPath.includes(modulePath) ||
                normalizedPath.includes(moduleInfo.title.toLowerCase().replace(/\s+/g, '')) ||
                normalizedPath.includes(moduleInfo.menuId.replace('menu-', ''));
            
            if (isThisModule) {
                // CAMBIO: Usar moduleInfo.permission para la verificación
                const requiredPermission = moduleInfo.permission;
                const hasAccess = this.hasPermission(requiredPermission);
                if (!hasAccess) {
                    console.warn(`🚫 Acceso denegado a: ${moduleInfo.title} (permiso requerido: ${requiredPermission})`);
                    this.redirectToAuthorizedRoute();
                    return false;
                }
                console.log(`✅ Acceso autorizado a: ${moduleInfo.title} (permiso: ${requiredPermission})`);
                return true;
            }
        }

        // Si no coincide con ningún módulo protegido, permitir acceso
        // (pueden ser páginas generales, assets, etc.)
        console.log(`ℹ️ Ruta no protegida, permitiendo acceso: ${currentPath}`);
        return true;
    }    /**
     * Redirige al usuario a una ruta autorizada
     */
    redirectToAuthorizedRoute() {
        const firstRoute = this.getFirstAvailableRoute();
        
        // Siempre hay una ruta disponible (siempre es la página de inicio)
        const baseUrl = window.location.hostname.includes('github.io') ? '/PanelWeb' : '';
        console.log(`🔄 Redirigiendo a ruta autorizada: ${firstRoute}`);
        window.location.replace(`${baseUrl}${firstRoute}`);
    }

    /**
     * Muestra mensaje cuando el usuario no tiene acceso a ningún módulo
     */
    showNoAccessMessage() {
        const message = document.createElement('div');
        message.id = 'no-access-message';
        message.style.cssText = `
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border: 2px solid #dc3545;
            border-radius: 10px;
            padding: 30px;
            max-width: 500px;
            z-index: 10000;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            text-align: center;
        `;
        
        message.innerHTML = `
            <div style="color: #dc3545; font-size: 48px; margin-bottom: 20px;">
                🚫
            </div>
            <h3 style="color: #dc3545; margin: 0 0 15px 0;">Acceso Denegado</h3>
            <p style="margin: 0 0 20px 0; color: #666;">
                Tu cuenta no tiene permisos para acceder a ningún módulo del sistema.
            </p>
            <p style="margin: 0 0 25px 0; color: #666;">
                Contacta al administrador para solicitar los permisos necesarios.
            </p>
            <button onclick="window.location.href='${window.location.origin}/index.html'" 
                    style="background: #dc3545; color: white; border: none; padding: 12px 24px; 
                           border-radius: 5px; cursor: pointer; font-size: 16px;">
                Volver al Login
            </button>
        `;
        
        document.body.appendChild(message);
    }

    /**
     * Cierra sesión y redirige al login
     */
    logout() {
        console.log('🚪 Cerrando sesión por falta de permisos...');
        
        // Limpiar sessionStorage
        sessionStorage.clear();
        
        // Redirigir al login
        const baseUrl = window.location.hostname.includes('github.io') ? '/PanelWeb' : '';
        setTimeout(() => {
            window.location.replace(`${baseUrl}/index.html`);
        }, 3000);
    }    /**
     * Inicializa el sistema de permisos
     * Debe llamarse después del login exitoso
     */
    initializePermissions() {
        console.log('🚀 Inicializando sistema de permisos...');
        
        // Mostrar mensaje de progreso
        this.updateLoadingStep('Cargando permisos del usuario...');
        
        const userPermissions = this.getUserPermissions();
        console.log('👤 Permisos del usuario:', userPermissions);
        
        // Actualizar mensaje de progreso
        this.updateLoadingStep('Configurando menú de navegación...');
        
        // Configurar elementos del menú con reintentos para asegurar que el DOM esté listo
        this.configureMenuPermissionsWithRetry();
        
        // Actualizar mensaje de progreso
        this.updateLoadingStep('Validando ruta actual...');
        
        // Validar ruta actual solo si estamos en una página específica de módulo
        const currentPath = window.location.pathname;
        const isModulePage = Object.values(this.modulePermissions).some(module => 
            module.route && currentPath.toLowerCase().includes(module.route.toLowerCase().replace(/^\//, ''))
        );
        
        if (isModulePage) {
            console.log('📍 Detectada página de módulo, validando permisos...');
            this.validateCurrentRoute(currentPath);
        } else {
            console.log('📍 Página general o inicio, omitiendo validación de ruta específica');
        }
        
        // Finalizar configuración
        setTimeout(() => {
            this.updateLoadingStep('Finalizando configuración...');
            setTimeout(() => {
                this.hidePermissionsLoading();
                console.log('✅ Sistema de permisos inicializado');
            }, 300);
        }, 500);
    }

    /**
     * Actualiza el mensaje de progreso en la pantalla de carga
     * @param {string} message - Mensaje a mostrar
     */
    updateLoadingStep(message) {
        const stepElement = document.getElementById('loading-step');
        if (stepElement) {
            stepElement.textContent = message;
        }
    }    /**
     * Oculta la pantalla de carga y muestra el contenido
     */
    hidePermissionsLoading() {
        const loadingOverlay = document.getElementById('permissions-loading');
        const sidebarMenu = document.querySelector('.sidebar-menu');
        const mainContent = document.querySelector('.inicio-content') || 
                           document.querySelector('.main-content .content');
        
        if (loadingOverlay) {
            loadingOverlay.classList.add('hidden');
            
            // Remover completamente después de la animación
            setTimeout(() => {
                if (loadingOverlay.parentNode) {
                    loadingOverlay.parentNode.removeChild(loadingOverlay);
                }
            }, 500);
        }
        
        // Mostrar elementos con animación suave
        if (sidebarMenu) {
            sidebarMenu.classList.add('permissions-ready');
        }
        
        if (mainContent) {
            setTimeout(() => {
                mainContent.classList.add('permissions-ready');
            }, 200);
        }
        
        console.log('🎉 Interfaz lista y permisos configurados');
    }

    /**
     * Debug: Muestra información de permisos en consola
     */
    debugPermissions() {
        console.log('🐛 DEBUG - Información de permisos:');
        console.log('👤 Permisos del usuario:', this.getUserPermissions());
        console.log('📋 Módulos disponibles:', this.getAvailableModules());
        console.log('🎯 Primera ruta disponible:', this.getFirstAvailableRoute());
    }

    /**
     * Debug temporal: Simula permisos para testing
     * REMOVER EN PRODUCCIÓN
     */
    setTestPermissions(permissions) {
        console.log('🧪 MODO TEST: Estableciendo permisos temporales:', permissions);
        sessionStorage.setItem("userPermissions", JSON.stringify(permissions));
    }    /**
     * Debug temporal: Muestra información detallada para diagnóstico
     */
    debugCurrentState() {
        console.log('🐛 DEBUG DETALLADO - Estado actual del sistema:');
        console.log('─'.repeat(50));
        
        const currentPath = window.location.pathname;
        const userPermissions = this.getUserPermissions();
        
        console.log('📍 Ruta actual:', currentPath);
        console.log('👤 Permisos del usuario:', userPermissions);
        console.log('🔗 Módulos configurados:');
        
        Object.entries(this.modulePermissions).forEach(([permission, moduleInfo]) => {
            const hasPermission = userPermissions.includes(permission);
            const menuElement = document.getElementById(moduleInfo.menuId);
            
            console.log(`  ${hasPermission ? '✅' : '❌'} ${permission}:`);
            console.log(`      Ruta: ${moduleInfo.route}`);
            console.log(`      Menú ID: ${moduleInfo.menuId}`);
            console.log(`      Elemento encontrado: ${!!menuElement}`);
            
            if (menuElement) {
                const linkElement = menuElement.querySelector('a');
                const hasDisabledClass = menuElement.classList.contains('disabled') || 
                                        menuElement.classList.contains('permission-disabled');
                const opacity = window.getComputedStyle(menuElement).opacity;
                const pointerEvents = linkElement ? window.getComputedStyle(linkElement).pointerEvents : 'N/A';
                
                console.log(`      Clases CSS: ${Array.from(menuElement.classList).join(', ')}`);
                console.log(`      Deshabilitado: ${hasDisabledClass}`);
                console.log(`      Opacidad: ${opacity}`);
                console.log(`      Pointer Events: ${pointerEvents}`);
                console.log(`      Data Status: ${menuElement.dataset.permissionStatus || 'N/A'}`);
                
                if (linkElement) {
                    console.log(`      Enlace disabled attr: ${linkElement.getAttribute('disabled')}`);
                    console.log(`      Enlace color: ${window.getComputedStyle(linkElement).color}`);
                }
                
                const indicator = menuElement.querySelector('.permission-indicator');
                console.log(`      Indicador presente: ${!!indicator}`);
            }
            console.log('');        });
        
        console.log('─'.repeat(50));
        console.log('🎯 Primera ruta disponible:', this.getFirstAvailableRoute());
        console.log('📋 Módulos disponibles:', this.getAvailableModules().length);
        
        // Debug info completed
    }/**
     * Configura permisos del menú con reintentos para asegurar que el DOM esté listo
     */
    configureMenuPermissionsWithRetry(attempt = 1, maxAttempts = 5) {
        console.log(`🔄 Intento ${attempt}/${maxAttempts} de configuración de menú...`);
        
        // Verificar si el sidebar está cargado
        const sidebar = document.getElementById('sidebar');
        if (!sidebar && attempt < maxAttempts) {
            console.log(`⏳ Sidebar no encontrado, reintentando en ${attempt * 500}ms...`);
            setTimeout(() => {
                this.configureMenuPermissionsWithRetry(attempt + 1, maxAttempts);
            }, attempt * 500);
            return;
        }
        
        if (!sidebar && attempt >= maxAttempts) {
            console.warn('⚠️ Sidebar no se cargó después de varios intentos, continuando sin configuración de menú');
            return;
        }
        
        // Verificar que al menos algunos elementos del menú estén presentes
        const menuItems = Object.values(this.modulePermissions).map(m => m.menuId);
        const foundItems = menuItems.filter(id => document.getElementById(id));
        
        if (foundItems.length === 0 && attempt < maxAttempts) {
            console.log(`⏳ Elementos del menú no encontrados (${foundItems.length}/${menuItems.length}), reintentando...`);
            setTimeout(() => {
                this.configureMenuPermissionsWithRetry(attempt + 1, maxAttempts);
            }, attempt * 500);
            return;
        }
        
        console.log(`✅ DOM listo, elementos encontrados: ${foundItems.length}/${menuItems.length}`);
        
        // Configurar permisos del menú
        this.configureMenuPermissions();
    }
}

// Exportar para uso global
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PermissionsService;
} else {
    window.PermissionsService = new PermissionsService();
    console.log('✅ PermissionsService exportado globalmente');
}
