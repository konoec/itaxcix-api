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
     * Renderiza las estadísticas principales usando componentes Tabler modernos
     */
    renderMainStats() {
        const statsGrid = document.getElementById('dashboard-stats-grid');
        if (!statsGrid) return;

        // Calcular porcentajes y tendencias
        const userActivityRate = (this.statsData.activeUsers / this.statsData.totalUsers) * 100;
        const webAccessRate = this.statsData.webAccessPercentage || ((this.statsData.usersWithWebAccess / this.statsData.totalUsers) * 100);
        const roleUtilization = (this.statsData.webRoles / this.statsData.totalRoles) * 100;
        const permissionDistribution = (this.statsData.webPermissions / this.statsData.totalPermissions) * 100;

        const stats = [
            {
                icon: 'users',
                color: 'blue',
                value: this.dashboardService.formatNumber(this.statsData.totalUsers),
                label: 'Total de Usuarios',
                subtitle: `${this.statsData.activeUsers} activos`,
                chart: {
                    percentage: userActivityRate,
                    color: 'blue'
                },
                trend: {
                    value: `${userActivityRate.toFixed(0)}%`,
                    type: this.getPercentageTrendType(userActivityRate),
                    label: 'Tasa de actividad'
                }
            },
            {
                icon: 'activity',
                color: 'green',
                value: this.dashboardService.formatNumber(this.statsData.activeUsers),
                label: 'Usuarios Activos',
                subtitle: `De ${this.statsData.totalUsers} usuarios`,
                chart: {
                    percentage: userActivityRate,
                    color: 'green'
                },
                trend: {
                    value: `${userActivityRate.toFixed(0)}%`,
                    type: this.getPercentageTrendType(userActivityRate),
                    label: 'Tasa de actividad'
                }
            },
            {
                icon: 'globe',
                color: 'cyan',
                value: this.dashboardService.formatNumber(this.statsData.usersWithWebAccess || Math.floor(this.statsData.totalUsers * 0.75)),
                label: 'Acceso Web',
                subtitle: `${webAccessRate.toFixed(0)}% del total`,
                chart: {
                    percentage: webAccessRate,
                    color: 'cyan'
                },
                trend: {
                    value: this.dashboardService.formatPercentage(webAccessRate),
                    type: this.getPercentageTrendType(webAccessRate),
                    label: 'Acceso web'
                }
            },
            {
                icon: 'shield-check',
                color: 'purple',
                value: this.statsData.totalRoles,
                label: 'Roles del Sistema',
                subtitle: `${this.statsData.webRoles} para web`,
                chart: {
                    percentage: roleUtilization,
                    color: 'purple'
                },
                trend: {
                    value: `${this.statsData.totalPermissions}`,
                    type: 'neutral',
                    label: 'permisos totales'
                }
            }
        ];

        statsGrid.innerHTML = stats.map(stat => `
            <div class="col-sm-6 col-lg-3">
                <div class="card card-sm">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <span class="bg-${stat.color} text-white avatar avatar-rounded">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        ${this.getTablerIcon(stat.icon)}
                                    </svg>
                                </span>
                            </div>
                            <div class="col">
                                <div class="font-weight-medium">
                                    ${stat.value}
                                </div>
                                <div class="text-muted">${stat.label}</div>
                                <div class="text-muted small">${stat.subtitle}</div>
                            </div>
                        </div>
                        <div class="d-flex align-items-baseline mt-3">
                            <div class="subheader me-2">${stat.trend.label}</div>
                            <div class="ms-auto">
                                <span class="text-${this.getTrendColor(stat.trend.type)} d-inline-flex align-items-center lh-1">
                                    ${stat.trend.value}
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1 icon-sm" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        ${this.getTrendArrow(stat.trend.type)}
                                    </svg>
                                </span>
                            </div>
                        </div>
                        <div class="progress progress-sm">
                            <div class="progress-bar bg-${stat.chart.color}" style="width: ${Math.min(stat.chart.percentage, 100)}%" role="progressbar" aria-valuenow="${stat.chart.percentage}" aria-valuemin="0" aria-valuemax="100">
                                <span class="visually-hidden">${stat.chart.percentage.toFixed(0)}% Complete</span>
                            </div>
                        </div>
                    </div>
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
     * Renderiza métricas de acceso web usando gráficos modernos de Tabler
     */
    renderWebAccessMetrics() {
        const container = document.getElementById('web-access-metrics');
        if (!container) return;

        const webAccessPercentage = this.statsData.webAccessPercentage || ((this.statsData.usersWithWebAccess / this.statsData.totalUsers) * 100);
        const usersWithWebAccess = this.statsData.usersWithWebAccess || Math.floor(this.statsData.totalUsers * 0.75);
        const activityPercentage = this.statsData.userActivityPercentage || ((this.statsData.activeUsers / this.statsData.totalUsers) * 100);

        // Generar datos simulados para el mini gráfico de líneas
        const generateMiniChart = (baseValue) => {
            const points = [];
            for (let i = 0; i < 7; i++) {
                const variation = (Math.random() - 0.5) * 20; // ±10% variation
                points.push(Math.max(10, Math.min(90, baseValue + variation)));
            }
            return points;
        };

        const webAccessData = generateMiniChart(webAccessPercentage);
        const activityData = generateMiniChart(activityPercentage);

        container.innerHTML = `
            <div class="row">
                <div class="col-12">
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">${this.dashboardService.formatPercentage(webAccessPercentage)}</div>
                        <div class="me-auto">
                            <span class="text-muted d-inline-flex align-items-center lh-1">
                                ${usersWithWebAccess} usuarios
                            </span>
                        </div>
                    </div>
                    <div class="subheader mb-3">Usuarios con acceso web activo</div>
                </div>
            </div>

            <!-- Mini gráfico de líneas -->
            <div class="chart-sm mb-3">
                <svg width="100%" height="40" style="overflow: visible;">
                    <polyline
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1"
                        points="${webAccessData.map((point, index) => `${(index * 100 / (webAccessData.length - 1))},${40 - (point * 0.4)}`).join(' ')}"
                        opacity="0.3"/>
                    <polyline
                        fill="none"
                        stroke="#206bc4"
                        stroke-width="2"
                        points="${webAccessData.map((point, index) => `${(index * 100 / (webAccessData.length - 1))},${40 - (point * 0.4)}`).join(' ')}"/>
                    ${webAccessData.map((point, index) => 
                        `<circle cx="${(index * 100 / (webAccessData.length - 1))}" cy="${40 - (point * 0.4)}" r="2" fill="#206bc4"/>`
                    ).join('')}
                </svg>
            </div>

            <!-- Detalles adicionales -->
            <div class="row">
                <div class="col-6">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-primary"></span>
                        <span class="h3 ms-2 mb-0">${usersWithWebAccess}</span>
                    </div>
                    <div class="text-muted">Con acceso web</div>
                </div>
                <div class="col-6">
                    <div class="d-flex align-items-center">
                        <span class="badge bg-green"></span>
                        <span class="h3 ms-2 mb-0">${this.statsData.activeUsers}</span>
                    </div>
                    <div class="text-muted">Usuarios activos</div>
                </div>
            </div>

            <!-- Barra de progreso de actividad -->
            <div class="mt-3">
                <div class="d-flex mb-2">
                    <div class="subheader">Actividad general</div>
                    <div class="ms-auto text-muted">${this.dashboardService.formatPercentage(activityPercentage)}</div>
                </div>
                <div class="progress progress-sm">
                    <div class="progress-bar bg-primary" style="width: ${Math.min(activityPercentage, 100)}%" role="progressbar">
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Renderiza la distribución de roles usando gráficos modernos de Tabler
     */
    renderRoleDistribution() {
        const container = document.getElementById('role-distribution');
        if (!container) return;

        const webRolePercentage = (this.statsData.webRoles / this.statsData.totalRoles) * 100;
        const webPermissionPercentage = (this.statsData.webPermissions / this.statsData.totalPermissions) * 100;
        
        // Generar datos para mini gráfico de barras
        const generateBarChart = (mainValue) => {
            const bars = [];
            for (let i = 0; i < 12; i++) {
                const height = Math.random() * 30 + 10;
                bars.push(height);
            }
            // Hacer que las últimas barras reflejen la tendencia
            bars[bars.length - 1] = mainValue * 0.6;
            bars[bars.length - 2] = mainValue * 0.5;
            return bars;
        };

        const roleData = generateBarChart(webRolePercentage);
        const permissionData = generateBarChart(webPermissionPercentage);

        container.innerHTML = `
            <div class="row">
                <div class="col-12">
                    <div class="d-flex align-items-baseline">
                        <div class="h1 mb-0 me-2">${this.statsData.totalRoles}</div>
                        <div class="me-auto">
                            <span class="text-${this.getTrendColor('neutral')} d-inline-flex align-items-center lh-1">
                                ${this.dashboardService.formatPercentage(webRolePercentage)}
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon ms-1 icon-sm" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                </svg>
                            </span>
                        </div>
                    </div>
                    <div class="subheader mb-3">Roles del sistema configurados</div>
                </div>
            </div>

            <!-- Mini gráfico de barras -->
            <div class="chart-sm mb-3">
                <svg width="100%" height="40" style="overflow: visible;">
                    ${roleData.map((height, index) => 
                        `<rect x="${index * 8}" y="${40 - height}" width="6" height="${height}" fill="${index === roleData.length - 1 ? '#206bc4' : '#f1f5f9'}" rx="1"/>`
                    ).join('')}
                </svg>
            </div>

            <!-- Grid de estadísticas -->
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-blue"></span>
                                <span class="h3 ms-2 mb-0">${this.statsData.webRoles}</span>
                            </div>
                            <div class="text-muted">Roles web</div>
                            <div class="progress progress-sm mt-2">
                                <div class="progress-bar bg-blue" style="width: ${webRolePercentage}%" role="progressbar">
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-green"></span>
                                <span class="h3 ms-2 mb-0">${this.statsData.webPermissions}</span>
                            </div>
                            <div class="text-muted">Permisos web</div>
                            <div class="progress progress-sm mt-2">
                                <div class="progress-bar bg-green" style="width: ${webPermissionPercentage}%" role="progressbar">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="mt-3">
                <div class="row">
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Total permisos</div>
                            <div class="ms-auto text-muted">${this.statsData.totalPermissions}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <div class="subheader">Cobertura web</div>
                            <div class="ms-auto text-muted">${this.dashboardService.formatPercentage((this.statsData.webPermissions / this.statsData.totalPermissions) * 100)}</div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Renderiza el dashboard de bienvenida para usuarios sin permisos de configuración usando Tabler
     */
    renderWelcomeDashboard() {
        const statsGrid = document.getElementById('dashboard-stats-grid');
        if (!statsGrid) return;

        console.log('🏠 Renderizando dashboard de bienvenida');

        // Crear contenido de bienvenida usando componentes Tabler
        statsGrid.innerHTML = `
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center mb-4">
                            <div class="col-auto">
                                <span class="avatar avatar-lg bg-blue text-white">
                                    <i class="fas fa-home"></i>
                                </span>
                            </div>
                            <div class="col">
                                <h2 class="card-title mb-1">${this.statsData.welcomeMessage}</h2>
                                <p class="text-muted mb-0">${this.statsData.subtitle}</p>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card card-sm">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Información del Sistema
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="datagrid">
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Sistema</div>
                                                <div class="datagrid-content">${this.statsData.systemInfo.name}</div>
                                            </div>
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Versión</div>
                                                <div class="datagrid-content">${this.statsData.systemInfo.version}</div>
                                            </div>
                                            <div class="datagrid-item">
                                                <div class="datagrid-title">Estado</div>
                                                <div class="datagrid-content">
                                                    <span class="badge bg-${this.getSystemStatusColor(this.statsData.systemInfo.status)} text-white">
                                                        ${this.statsData.systemInfo.status}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="text-muted mt-3">${this.statsData.systemInfo.description}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card card-sm">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-th-large me-2"></i>
                                            Módulos Disponibles
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="list-group list-group-flush">
                                            ${this.statsData.availableModules.map(module => `
                                                <div class="list-group-item">
                                                    <div class="row align-items-center">
                                                        <div class="col-auto">
                                                            <span class="avatar avatar-sm bg-blue-lt">
                                                                <i class="${module.icon}"></i>
                                                            </span>
                                                        </div>
                                                        <div class="col text-truncate">
                                                            <strong class="text-body d-block">${module.name}</strong>
                                                            <div class="text-muted text-truncate mt-n1">${module.description}</div>
                                                        </div>
                                                        <div class="col-auto">
                                                            <span class="badge bg-${this.getModuleStatusColor(module.status)}">
                                                                ${module.status}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            `).join('')}
                                        </div>
                                        <div class="alert alert-info mt-3" role="alert">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <i class="fas fa-info-circle"></i>
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    ${this.statsData.userMessage}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Limpiar métricas secundarias para el modo bienvenida
        const webAccessMetrics = document.getElementById('web-access-metrics');
        const roleDistribution = document.getElementById('role-distribution');
        
        if (webAccessMetrics && webAccessMetrics.parentElement) {
            webAccessMetrics.parentElement.parentElement.style.display = 'none';
        }
        if (roleDistribution && roleDistribution.parentElement) {
            roleDistribution.parentElement.parentElement.style.display = 'none';
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
        } else if (typeof window.showRecoveryToast === 'function') {
            window.showRecoveryToast(message, type);
        } else {
            console.log(`Toast ${type}: ${message}`);
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
     * Obtiene el color de badge de tendencia para Tabler
     */
    getTrendBadgeColor(type) {
        switch (type) {
            case 'positive': return 'success';
            case 'negative': return 'danger';
            default: return 'warning';
        }
    }

    /**
     * Convierte colores de indicador a colores de Tabler
     */
    getIndicatorColorTabler(percentage) {
        if (percentage >= 80) return 'success';
        if (percentage >= 50) return 'warning';
        return 'danger';
    }

    /**
     * Obtiene el color de estado del sistema para Tabler
     */
    getSystemStatusColor(status) {
        switch (status.toLowerCase()) {
            case 'activo':
            case 'online':
            case 'operativo': return 'success';
            case 'mantenimiento':
            case 'warning': return 'warning';
            case 'inactivo':
            case 'offline':
            case 'error': return 'danger';
            default: return 'info';
        }
    }

    /**
     * Obtiene el color de estado del módulo para Tabler
     */
    getModuleStatusColor(status) {
        switch (status.toLowerCase()) {
            case 'disponible':
            case 'activo':
            case 'habilitado': return 'success';
            case 'limitado':
            case 'parcial': return 'warning';
            case 'no disponible':
            case 'deshabilitado':
            case 'inactivo': return 'danger';
            default: return 'info';
        }
    }

    /**
     * Obtiene iconos SVG de Tabler
     */
    getTablerIcon(iconName) {
        const icons = {
            'users': '<path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0"></path><path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path><path d="M21 21v-2a4 4 0 0 0 -3 -3.85"></path>',
            'activity': '<path d="M3 12h4l3 8l4 -16l3 8h4"></path>',
            'globe': '<path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path><path d="M3.6 9h16.8"></path><path d="M3.6 15h16.8"></path><path d="M11.5 3a17 17 0 0 0 0 18"></path><path d="M12.5 3a17 17 0 0 1 0 18"></path>',
            'shield-check': '<path d="M11.46 20.846a12 12 0 0 1 -2.21 -2.317c-2.582 -3.14 -3.25 -7.27 -3.25 -8.529a12 12 0 0 1 7.707 -2.293a12 12 0 0 1 7.293 2.293c0 1.259 -.668 5.389 -3.25 8.529a11.986 11.986 0 0 1 -2.21 2.317l-1.039 .87l-1.039 -.87z"></path><path d="M9 12l2 2l4 -4"></path>'
        };
        return icons[iconName] || icons['users'];
    }

    /**
     * Obtiene el color de tendencia para Tabler
     */
    getTrendColor(type) {
        switch (type) {
            case 'positive': return 'success';
            case 'negative': return 'danger';
            default: return 'warning';
        }
    }

    /**
     * Obtiene la flecha de tendencia SVG
     */
    getTrendArrow(type) {
        switch (type) {
            case 'positive': return '<path d="M7 7l10 0"></path><path d="M13 3l4 4l-4 4"></path>';
            case 'negative': return '<path d="M7 17l10 0"></path><path d="M13 21l4 -4l-4 -4"></path>';
            default: return '<path d="M5 12l14 0"></path>';
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
