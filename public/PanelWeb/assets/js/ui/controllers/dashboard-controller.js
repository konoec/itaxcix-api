/**
 * Controlador para el dashboard de inicio
 */
class DashboardController {
    constructor() {
        console.log('📊 DashboardController constructor ejecutado');
        this.isInitialized = false;
        this.dashboardService = null;
        this.statsData = null;
        this.refreshTimeout = null;
        
        // Auto-inicializar
        this.init();
    }

    /**
     * Inicializa el controlador
     */
    async init() {
        console.log('📊 Inicializando DashboardController...');
        try {
            // Esperar a que el servicio esté disponible
            await this.waitForService();
            
            // Configurar eventos
            this.setupEventListeners();
            
            // Cargar estadísticas iniciales
            await this.loadStats();
            
            this.isInitialized = true;
            console.log('✅ DashboardController inicializado correctamente');
        } catch (error) {
            console.error('❌ Error al inicializar DashboardController:', error);
            this.showError('Error al inicializar el dashboard: ' + error.message);
        }
    }

    /**
     * Espera a que el servicio esté disponible
     */
    async waitForService() {
        let attempts = 0;
        const maxAttempts = 20;
        
        while (typeof DashboardService === 'undefined' && attempts < maxAttempts) {
            console.log(`⏳ Esperando DashboardService... intento ${attempts + 1}/${maxAttempts}`);
            await new Promise(resolve => setTimeout(resolve, 250));
            attempts++;
        }
        
        if (typeof DashboardService === 'undefined') {
            throw new Error('DashboardService no está disponible después de esperar');
        }
        
        this.dashboardService = new DashboardService();
        console.log('✅ DashboardService está disponible');
    }

    /**
     * Configura los event listeners
     */
    setupEventListeners() {
        // Botón de refrescar
        const refreshBtn = document.getElementById('dashboard-refresh-btn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => this.handleRefresh());
        }

        // Auto-refresh cada 5 minutos
        this.refreshTimeout = setInterval(() => {
            this.loadStats(true); // true = silencioso
        }, 300000); // 5 minutos
    }

    /**
     * Maneja el click del botón de refrescar
     */
    async handleRefresh() {
        console.log('🔄 Refrescando estadísticas manualmente...');
        await this.loadStats();
    }

    /**
     * Carga las estadísticas del dashboard
     */
    async loadStats(silent = false) {
        if (!this.dashboardService) {
            console.error('❌ DashboardService no está disponible');
            return;
        }

        try {
            if (!silent) {
                this.showLoading();
            }

            // Actualizar estado del botón
            this.updateRefreshButton(true);

            const response = await this.dashboardService.getStats();

            if (response.success) {
                this.statsData = response.data;
                this.renderDashboard();
                
                if (!silent) {
                    console.log('✅ Estadísticas cargadas exitosamente');
                    this.showToast('Dashboard actualizado correctamente', 'success');
                }
            } else {
                throw new Error(response.message);
            }

        } catch (error) {
            console.error('❌ Error al cargar estadísticas:', error);
            this.showError(error.message);
        } finally {
            this.hideLoading();
            this.updateRefreshButton(false);
        }
    }

    /**
     * Renderiza el dashboard completo
     */
    renderDashboard() {
        if (!this.statsData) {
            console.error('❌ No hay datos para renderizar');
            return;
        }

        console.log('🎨 Renderizando dashboard con datos:', this.statsData);

        // Verificar si es modo bienvenida o estadísticas completas
        if (this.statsData.isWelcomeMode) {
            this.renderWelcomeDashboard();
        } else {
            // Renderizar estadísticas principales
            this.renderMainStats();
            
            // Renderizar métricas secundarias
            this.renderSecondaryMetrics();
        }

        // Mostrar el dashboard
        this.showDashboard();
    }

    /**
     * Renderiza las estadísticas principales
     */
    renderMainStats() {
        const statsGrid = document.getElementById('dashboard-stats-grid');
        if (!statsGrid) return;

        const stats = [
            {
                icon: 'fas fa-users',
                iconClass: 'users',
                value: this.dashboardService.formatNumber(this.statsData.totalUsers),
                label: 'Total de Usuarios',
                trend: this.calculateTrend(this.statsData.activeUsers, this.statsData.totalUsers)
            },
            {
                icon: 'fas fa-user-check',
                iconClass: 'users',
                value: this.dashboardService.formatNumber(this.statsData.activeUsers),
                label: 'Usuarios Activos',
                trend: {
                    value: this.dashboardService.formatPercentage(this.statsData.userActivityPercentage),
                    type: this.getPercentageTrendType(this.statsData.userActivityPercentage)
                }
            },
            {
                icon: 'fas fa-user-tag',
                iconClass: 'roles',
                value: this.statsData.totalRoles,
                label: 'Total de Roles',
                trend: {
                    value: `${this.statsData.webRoles} web`,
                    type: 'neutral'
                }
            },
            {
                icon: 'fas fa-shield-alt',
                iconClass: 'permissions',
                value: this.statsData.totalPermissions,
                label: 'Total de Permisos',
                trend: {
                    value: `${this.statsData.webPermissions} web`,
                    type: 'neutral'
                }
            }
        ];

        statsGrid.innerHTML = stats.map(stat => `
            <div class="dashboard-stat-card">
                <div class="dashboard-stat-header">
                    <div class="dashboard-stat-icon ${stat.iconClass}">
                        <i class="${stat.icon}"></i>
                    </div>
                </div>
                <div class="dashboard-stat-value">${stat.value}</div>
                <div class="dashboard-stat-label">${stat.label}</div>
                <div class="dashboard-stat-trend ${stat.trend.type}">
                    <i class="fas fa-${this.getTrendIcon(stat.trend.type)}"></i>
                    ${stat.trend.value}
                </div>
            </div>
        `).join('');
    }

    /**
     * Renderiza las métricas secundarias
     */
    renderSecondaryMetrics() {
        // Renderizar acceso web
        this.renderWebAccessMetrics();
        
        // Renderizar distribución de roles
        this.renderRoleDistribution();
    }

    /**
     * Renderiza métricas de acceso web
     */
    renderWebAccessMetrics() {
        const container = document.getElementById('web-access-metrics');
        if (!container) return;

        const webAccessPercentage = this.statsData.webAccessPercentage;
        const usersWithWebAccess = this.statsData.usersWithWebAccess;

        container.innerHTML = `
            <div class="dashboard-metric-title">
                <i class="fas fa-globe"></i>
                Acceso Web
            </div>
            <div class="dashboard-progress-item">
                <div class="dashboard-progress-label">
                    <span class="dashboard-progress-label-text">Usuarios con Acceso Web</span>
                    <span class="dashboard-progress-label-value">${usersWithWebAccess} (${this.dashboardService.formatPercentage(webAccessPercentage)})</span>
                </div>
                <div class="dashboard-progress-bar">
                    <div class="dashboard-progress-fill ${this.dashboardService.getIndicatorColor(webAccessPercentage)}" 
                         style="width: ${Math.min(webAccessPercentage, 100)}%"></div>
                </div>
            </div>
            <div class="dashboard-progress-item">
                <div class="dashboard-progress-label">
                    <span class="dashboard-progress-label-text">Actividad de Usuarios</span>
                    <span class="dashboard-progress-label-value">${this.dashboardService.formatPercentage(this.statsData.userActivityPercentage)}</span>
                </div>
                <div class="dashboard-progress-bar">
                    <div class="dashboard-progress-fill ${this.dashboardService.getIndicatorColor(this.statsData.userActivityPercentage)}" 
                         style="width: ${this.statsData.userActivityPercentage}%"></div>
                </div>
            </div>
        `;
    }

    /**
     * Renderiza la distribución de roles
     */
    renderRoleDistribution() {
        const container = document.getElementById('role-distribution');
        if (!container) return;

        const webRolePercentage = (this.statsData.webRoles / this.statsData.totalRoles) * 100;
        const mobileRolePercentage = 100 - webRolePercentage;
        
        const webPermissionPercentage = (this.statsData.webPermissions / this.statsData.totalPermissions) * 100;
        const mobilePermissionPercentage = 100 - webPermissionPercentage;

        container.innerHTML = `
            <div class="dashboard-metric-title">
                <i class="fas fa-chart-pie"></i>
                Distribución del Sistema
            </div>
            <div class="dashboard-progress-item">
                <div class="dashboard-progress-label">
                    <span class="dashboard-progress-label-text">Roles Web</span>
                    <span class="dashboard-progress-label-value">${this.statsData.webRoles}/${this.statsData.totalRoles} (${this.dashboardService.formatPercentage(webRolePercentage)})</span>
                </div>
                <div class="dashboard-progress-bar">
                    <div class="dashboard-progress-fill info" style="width: ${webRolePercentage}%"></div>
                </div>
            </div>
            <div class="dashboard-progress-item">
                <div class="dashboard-progress-label">
                    <span class="dashboard-progress-label-text">Permisos Web</span>
                    <span class="dashboard-progress-label-value">${this.statsData.webPermissions}/${this.statsData.totalPermissions} (${this.dashboardService.formatPercentage(webPermissionPercentage)})</span>
                </div>
                <div class="dashboard-progress-bar">
                    <div class="dashboard-progress-fill success" style="width: ${webPermissionPercentage}%"></div>
                </div>
            </div>
        `;
    }

    /**
     * Renderiza el dashboard de bienvenida para usuarios sin permisos de configuración
     */
    renderWelcomeDashboard() {
        const statsGrid = document.getElementById('dashboard-stats-grid');
        if (!statsGrid) return;

        console.log('🏠 Renderizando dashboard de bienvenida');

        // Crear contenido de bienvenida
        statsGrid.innerHTML = `
            <div class="welcome-card">
                <div class="welcome-header">
                    <div class="welcome-icon">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="welcome-text">
                        <h2>${this.statsData.welcomeMessage}</h2>
                        <p class="welcome-subtitle">${this.statsData.subtitle}</p>
                    </div>
                </div>
                <div class="welcome-content">
                    <div class="system-info-card">
                        <h3><i class="fas fa-info-circle"></i> Información del Sistema</h3>
                        <div class="system-details">
                            <div class="detail-item">
                                <span class="detail-label">Sistema:</span>
                                <span class="detail-value">${this.statsData.systemInfo.name}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Versión:</span>
                                <span class="detail-value">${this.statsData.systemInfo.version}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Estado:</span>
                                <span class="status-badge status-${this.statsData.systemInfo.status.toLowerCase()}">
                                    ${this.statsData.systemInfo.status}
                                </span>
                            </div>
                        </div>
                        <p class="system-description">${this.statsData.systemInfo.description}</p>
                    </div>
                    
                    <div class="modules-info-card">
                        <h3><i class="fas fa-th-large"></i> Módulos Disponibles</h3>
                        <div class="modules-list">
                            ${this.statsData.availableModules.map(module => `
                                <div class="module-item">
                                    <div class="module-icon">
                                        <i class="${module.icon}"></i>
                                    </div>
                                    <div class="module-details">
                                        <span class="module-name">${module.name}</span>
                                        <span class="module-description">${module.description}</span>
                                    </div>
                                    <span class="module-status status-${module.status.toLowerCase()}">
                                        ${module.status}
                                    </span>
                                </div>
                            `).join('')}
                        </div>
                        <div class="user-message">
                            <i class="fas fa-info-circle"></i>
                            <span>${this.statsData.userMessage}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Limpiar métricas secundarias para el modo bienvenida
        const secondaryMetrics = document.getElementById('dashboard-secondary-metrics');
        if (secondaryMetrics) {
            secondaryMetrics.innerHTML = '';
        }
    }

    /**
     * Calcula la tendencia entre dos valores
     */
    calculateTrend(current, total) {
        const percentage = (current / total) * 100;
        return {
            value: this.dashboardService.formatPercentage(percentage),
            type: this.getPercentageTrendType(percentage)
        };
    }

    /**
     * Obtiene el tipo de tendencia basado en porcentaje
     */
    getPercentageTrendType(percentage) {
        if (percentage >= 80) return 'positive';
        if (percentage >= 50) return 'neutral';
        return 'negative';
    }

    /**
     * Obtiene el icono de tendencia
     */
    getTrendIcon(type) {
        switch (type) {
            case 'positive': return 'arrow-up';
            case 'negative': return 'arrow-down';
            default: return 'minus';
        }
    }

    /**
     * Actualiza el estado del botón de refrescar
     */
    updateRefreshButton(loading) {
        const btn = document.getElementById('dashboard-refresh-btn');
        if (!btn) return;

        btn.disabled = loading;
        if (loading) {
            btn.innerHTML = '<i class="fas fa-sync-alt fa-spin"></i> Actualizando...';
        } else {
            btn.innerHTML = '<i class="fas fa-sync-alt"></i> Actualizar';
        }
    }

    /**
     * Muestra el estado de carga
     */
    showLoading() {
        const loading = document.getElementById('dashboard-loading');
        const content = document.getElementById('dashboard-content');
        const error = document.getElementById('dashboard-error');

        if (loading) loading.style.display = 'flex';
        if (content) content.style.display = 'none';
        if (error) error.style.display = 'none';
    }

    /**
     * Oculta el estado de carga
     */
    hideLoading() {
        const loading = document.getElementById('dashboard-loading');
        if (loading) loading.style.display = 'none';
    }

    /**
     * Muestra el dashboard
     */
    showDashboard() {
        const content = document.getElementById('dashboard-content');
        const error = document.getElementById('dashboard-error');

        if (content) content.style.display = 'block';
        if (error) error.style.display = 'none';
    }

    /**
     * Muestra un error
     */
    showError(message) {
        const loading = document.getElementById('dashboard-loading');
        const content = document.getElementById('dashboard-content');
        const error = document.getElementById('dashboard-error');
        const errorMessage = document.getElementById('dashboard-error-message');

        if (loading) loading.style.display = 'none';
        if (content) content.style.display = 'none';
        if (error) error.style.display = 'block';
        if (errorMessage) errorMessage.textContent = message;

        // Configurar botón de reintento
        const retryBtn = document.getElementById('dashboard-error-retry');
        if (retryBtn) {
            retryBtn.onclick = () => this.loadStats();
        }
    }

    /**
     * Muestra una notificación toast
     */
    showToast(message, type = 'info') {
        if (typeof window.showToast === 'function') {
            window.showToast(message, type);
        } else {
            console.log(`Toast ${type}: ${message}`);
        }
    }

    /**
     * Destructor para limpiar recursos
     */
    destroy() {
        if (this.refreshTimeout) {
            clearInterval(this.refreshTimeout);
            this.refreshTimeout = null;
        }
        console.log('🗑️ DashboardController destruido');
    }
}

// Exportar globalmente
if (typeof window !== 'undefined') {
    window.DashboardController = DashboardController;
    console.log('✅ DashboardController exportado globalmente');
}
