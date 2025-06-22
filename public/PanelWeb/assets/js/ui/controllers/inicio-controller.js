/**
 * Controlador UI para la página de Inicio
 * Maneja el dashboard principal y la visualización de módulos según permisos
 */
class InicioController {
    constructor() {
        this.isInitialized = false;
        this.userPermissions = [];
        this.allModules = [            {
                permission: 'ADMISIÓN DE CONDUCTORES',
                title: 'Admisión de Conductores',
                description: 'Gestionar el proceso de admisión y aprobación de nuevos conductores',
                icon: 'fas fa-user-check',
                route: '../Admission/AdmissionControl.html'
            },            {
                permission: 'TABLAS MAESTRAS',
                title: 'Tablas Maestras',
                description: 'Administrar las tablas maestras del sistema',
                icon: 'fas fa-table',
                route: null // Es un menú desplegable, no tiene página específica
            },
            {
                permission: 'AUDITORIA',
                title: 'Auditoría',
                description: 'Revisar logs y actividades del sistema',
                icon: 'fas fa-search',
                route: '#'
            },
            {
                permission: 'CONFIGURACIÓN',
                title: 'Configuración',
                description: 'Configurar parámetros generales del sistema',
                icon: 'fas fa-cog',
                route: '../Configuration/Configuration.html'
            }
        ];
        
        // Inicializar
        this.init();
    }    /**
     * Inicializa el controlador
     */
    async init() {
        console.log('🏠 Inicializando InicioController...');
        try {
            // Esperar a que los permisos estén disponibles
            await this.waitForPermissions();
            await this.loadUserPermissions();
            this.generateModuleCards();
            this.generateQuickActions();
            this.updatePermissionsInfo();
            this.updateWelcomeInfo();
            this.isInitialized = true;
            console.log('✅ InicioController inicializado correctamente');
        } catch (error) {
            console.error('❌ Error al inicializar InicioController:', error);
        }
    }

    /**
     * Espera a que el sistema de permisos esté disponible
     */
    async waitForPermissions() {
        let attempts = 0;
        const maxAttempts = 10;
        
        while (attempts < maxAttempts) {
            if (window.PermissionsService || window.userPermissions) {
                console.log('🔐 Sistema de permisos disponible');
                return;
            }
            
            console.log(`⏳ Esperando sistema de permisos... intento ${attempts + 1}`);
            await new Promise(resolve => setTimeout(resolve, 100));
            attempts++;
        }
        
        console.warn('⚠️ Sistema de permisos no disponible después de esperar');
    }/**
     * Carga los permisos del usuario actual
     */
    async loadUserPermissions() {
        if (window.PermissionsService) {
            this.userPermissions = window.PermissionsService.getUserPermissions();
            console.log('🔐 Permisos cargados desde PermissionsService:', this.userPermissions);
        } else if (typeof window.userPermissions !== 'undefined') {
            this.userPermissions = window.userPermissions;
            console.log('🔐 Permisos cargados desde permissions.js:', this.userPermissions);
        } else {
            console.warn('⚠️ Sistema de permisos no disponible, usando permisos por defecto');
            this.userPermissions = ['CONFIGURACIÓN']; // Permiso por defecto para pruebas
        }
    }

    /**
     * Actualiza la información de bienvenida
     */
    updateWelcomeInfo() {
        const userData = JSON.parse(localStorage.getItem('userData') || '{}');
        const welcomeElement = document.getElementById('welcome-message');
        
        if (welcomeElement) {
            const userName = userData.name || userData.username || 'Usuario';
            welcomeElement.textContent = `¡Bienvenido, ${userName}!`;
        }

        // Actualizar estadísticas generales
        const totalModules = this.allModules.length;
        const availableModules = this.allModules.filter(module => 
            this.userPermissions.includes(module.permission)
        ).length;

        const statsElement = document.getElementById('dashboard-stats');
        if (statsElement) {
            statsElement.innerHTML = `
                <div class="stat-item">
                    <i class="fas fa-th-large"></i>
                    <span>Módulos Totales: ${totalModules}</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Disponibles: ${availableModules}</span>
                </div>
                <div class="stat-item">
                    <i class="fas fa-lock"></i>
                    <span>Restringidos: ${totalModules - availableModules}</span>
                </div>
            `;
        }
    }

    /**
     * Genera las tarjetas de módulos según permisos
     */
    generateModuleCards() {
        const modulesGrid = document.getElementById('modules-grid');
        if (!modulesGrid) {
            console.warn('⚠️ Elemento modules-grid no encontrado');
            return;
        }

        let cardsHTML = '';
        
        this.allModules.forEach(module => {
            const hasPermission = this.userPermissions.includes(module.permission);
            const statusClass = hasPermission ? 'available' : 'restricted';
            const statusText = hasPermission ? 'Disponible' : 'Acceso Restringido';
            const statusBadgeClass = hasPermission ? 'status-available' : 'status-restricted';
            
            cardsHTML += `
                <div class="stat-card ${statusClass}">
                    <div class="stat-card-header">
                        <i class="${module.icon} stat-card-icon"></i>
                        <h4 class="stat-card-title">${module.title}</h4>
                    </div>
                    <p class="stat-card-description">${module.description}</p>
                    <span class="stat-card-status ${statusBadgeClass}">
                        ${hasPermission ? '✅' : '🔒'} ${statusText}
                    </span>
                </div>
            `;
        });
        
        modulesGrid.innerHTML = cardsHTML;
        console.log('📋 Tarjetas de módulos generadas');
    }

    /**
     * Genera las acciones rápidas según módulos disponibles
     */
    generateQuickActions() {
        const actionsGrid = document.getElementById('quick-actions-grid');
        if (!actionsGrid) {
            console.warn('⚠️ Elemento quick-actions-grid no encontrado');
            return;
        }

        const availableModules = this.allModules.filter(module => 
            this.userPermissions.includes(module.permission)
        );
        
        let actionsHTML = '';
        
        // Acción: Ver Perfil (siempre disponible)
        actionsHTML += `
            <button class="action-button" onclick="document.getElementById('profile-container').click()">
                <i class="fas fa-user"></i>
                Ver Mi Perfil
            </button>
        `;
          // Acciones según módulos disponibles
        availableModules.forEach(module => {
            if (module.route && module.route !== '#' && module.route !== null) {
                actionsHTML += `
                    <a href="${module.route}" class="action-button">
                        <i class="${module.icon}"></i>
                        Ir a ${module.title}
                    </a>
                `;
            }
        });
        
        // Si no hay módulos, mostrar mensaje
        if (availableModules.length === 0) {
            actionsHTML += `
                <div class="action-button disabled">
                    <i class="fas fa-lock"></i>
                    No hay acciones disponibles
                </div>
            `;
        }
        
        actionsGrid.innerHTML = actionsHTML;
        console.log('⚡ Acciones rápidas generadas');
    }

    /**
     * Actualiza la información de permisos del usuario
     */
    updatePermissionsInfo() {
        const permissionsInfo = document.getElementById('user-permissions-info');
        if (!permissionsInfo) {
            console.warn('⚠️ Elemento user-permissions-info no encontrado');
            return;
        }

        if (this.userPermissions.length > 0) {
            permissionsInfo.innerHTML = `
                <strong>Permisos asignados:</strong> ${this.userPermissions.length} módulo(s)<br>
                <small style="color: #666;">
                    ${this.userPermissions.join(', ')}
                </small>
            `;
        } else {
            permissionsInfo.innerHTML = `
                <strong style="color: #dc3545;">Sin permisos asignados</strong><br>
                <small style="color: #666;">
                    Contacta al administrador para solicitar acceso a los módulos.
                </small>
            `;
        }
        
        console.log('ℹ️ Información de permisos actualizada');
    }

    /**
     * Refresca el dashboard (útil para cuando cambien los permisos)
     */
    refresh() {
        console.log('🔄 Refrescando dashboard...');
        this.loadUserPermissions().then(() => {
            this.generateModuleCards();
            this.generateQuickActions();
            this.updatePermissionsInfo();
            this.updateWelcomeInfo();
        });
    }

    /**
     * Verifica si el controlador está inicializado
     */
    isReady() {
        return this.isInitialized;
    }
}
