/**
 * Configuration Controller
 * Controlador para gestionar la interfaz de configuraciones del sistema
 * 
 * @author Sistema
 * @version 1.0.0
 */

class ConfigurationController {
    constructor() {
        this.configurationService = new ConfigurationService();
        this.createModalController = null;
        this.currentPage = 1;
        this.perPage = 15;
        this.currentFilters = {};
        this.sortBy = 'key';
        this.sortDirection = 'asc';
        this.searchTimeout = null;
        
        this.init();
    }

    /**
     * Inicializa el controlador
     */
    async init() {
        try {
            this.setupEventListeners();
            this.initializeCreateModal();
            await this.loadConfigurations();
            this.setupTooltips();
        } catch (error) {
            console.error('Error initializing configuration controller:', error);
            this.showError('Error al inicializar el controlador de configuraciones');
        }
    }

    /**
     * Configura los event listeners
     */
    setupEventListeners() {
        // Búsqueda global
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    this.handleSearch(e.target.value);
                }, 300);
            });
        }

        // Filtros
        const filterForm = document.getElementById('filter-form');
        if (filterForm) {
            filterForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleFilters();
            });
            
            // Auto-aplicar filtros al cambiar selects
            const autoApplySelects = ['filter-sort-by', 'filter-sort-direction', 'filter-per-page'];
            autoApplySelects.forEach(id => {
                const select = document.getElementById(id);
                if (select) {
                    select.addEventListener('change', () => {
                        this.handleFilters();
                    });
                }
            });
        }

        // Botón limpiar filtros
        const clearFiltersBtn = document.getElementById('clear-filters');
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', () => {
                this.clearFilters();
            });
        }

        // Botón refresh
        const refreshBtn = document.getElementById('refresh-btn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => {
                this.refreshData();
            });
        }

        // Botón toggle de filtros (sin Bootstrap)
        const toggleFiltersBtn = document.getElementById('toggle-filters-btn');
        const filtersSection = document.getElementById('filters-section');
        const filtersChevron = document.getElementById('filters-chevron');
        
        if (toggleFiltersBtn && filtersSection) {
            toggleFiltersBtn.addEventListener('click', () => {
                const isVisible = filtersSection.style.display !== 'none';
                
                if (isVisible) {
                    // Ocultar filtros
                    filtersSection.style.display = 'none';
                    toggleFiltersBtn.classList.remove('active');
                    toggleFiltersBtn.setAttribute('aria-expanded', 'false');
                    
                    // Cambiar chevron hacia abajo
                    if (filtersChevron) {
                        filtersChevron.innerHTML = `
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <polyline points="6,9 12,15 18,9"></polyline>
                        `;
                    }
                } else {
                    // Mostrar filtros
                    filtersSection.style.display = 'block';
                    toggleFiltersBtn.classList.add('active');
                    toggleFiltersBtn.setAttribute('aria-expanded', 'true');
                    
                    // Cambiar chevron hacia arriba
                    if (filtersChevron) {
                        filtersChevron.innerHTML = `
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                            <polyline points="18,15 12,9 6,15"></polyline>
                        `;
                    }
                }
            });
        }

        // Botón de crear configuración
        const createBtn = document.getElementById('btn-create-config');
        if (createBtn) {
            createBtn.addEventListener('click', () => {
                this.openCreateModal();
            });
        }

        // Paginación
        this.setupPaginationEvents();

        // Ordenamiento
        this.setupSortingEvents();

        // Modales
        this.setupModalEvents();
    }

    /**
     * Configura eventos de paginación
     */
    setupPaginationEvents() {
        document.addEventListener('click', (e) => {
            if (e.target.matches('.page-link')) {
                e.preventDefault();
                const page = parseInt(e.target.dataset.page);
                if (page && page !== this.currentPage) {
                    this.currentPage = page;
                    this.loadConfigurations();
                }
            }
        });
    }

    /**
     * Configura eventos de ordenamiento
     */
    setupSortingEvents() {
        document.addEventListener('click', (e) => {
            if (e.target.matches('.sortable-header')) {
                e.preventDefault();
                const sortBy = e.target.dataset.sort;
                
                if (this.sortBy === sortBy) {
                    this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                } else {
                    this.sortBy = sortBy;
                    this.sortDirection = 'asc';
                }
                
                this.loadConfigurations();
            }
        });
    }

    /**
     * Inicializa el controlador del modal de creación
     */
    initializeCreateModal() {
        try {
            this.createModalController = new CreateConfigurationModalController();
            console.log('✅ Controlador de modal de creación inicializado');
        } catch (error) {
            console.error('❌ Error inicializando controlador de modal:', error);
        }
    }

    /**
     * Abre el modal de creación
     */
    openCreateModal() {
        if (this.createModalController) {
            this.createModalController.openModal();
        } else {
            console.error('Controlador de modal no inicializado');
            this.showError('No se pudo abrir el modal de creación');
        }
    }

    /**
     * Método público para refrescar la tabla (llamado desde el modal)
     */
    refreshTable() {
        this.loadConfigurations();
    }

    /**
     * Configura eventos de modales
     */
    setupModalEvents() {
        // Crear configuración
        const createBtn = document.getElementById('create-config-btn');
        if (createBtn) {
            createBtn.addEventListener('click', () => {
                this.showCreateModal();
            });
        }

        // Editar configuración
        document.addEventListener('click', (e) => {
            if (e.target.matches('.edit-config-btn')) {
                const configId = parseInt(e.target.dataset.id);
                this.showEditModal(configId);
            }
        });

        // Eliminar configuración
        document.addEventListener('click', (e) => {
            if (e.target.matches('.delete-config-btn')) {
                const configId = parseInt(e.target.dataset.id);
                this.showDeleteModal(configId);
            }
        });

        // Toggle estado
        document.addEventListener('click', (e) => {
            if (e.target.matches('.toggle-status-btn')) {
                const configId = parseInt(e.target.dataset.id);
                const currentStatus = e.target.dataset.status === 'true';
                this.toggleConfigurationStatus(configId, !currentStatus);
            }
        });
    }

    /**
     * Carga configuraciones desde el servidor
     */
    async loadConfigurations() {
        try {
            this.showLoadingState();

            const options = {
                page: this.currentPage,
                perPage: this.perPage,
                sortBy: this.sortBy,
                sortDirection: this.sortDirection,
                ...this.currentFilters
            };

            const response = await this.configurationService.getConfigurations(options);
            
            if (response.success) {
                this.renderConfigurations(response.data);
                this.updatePaginationInfo(response.data.meta);
            } else {
                throw new Error(response.message || 'Error al cargar configuraciones');
            }
        } catch (error) {
            console.error('Error loading configurations:', error);
            this.showError('Error al cargar las configuraciones');
        } finally {
            this.hideLoadingState();
        }
    }

    /**
     * Renderiza la tabla de configuraciones
     */
    renderConfigurations(data) {
        const container = document.getElementById('configurations-table');
        if (!container) return;

        const configurations = data.items || [];
        
        if (configurations.length === 0) {
            container.innerHTML = this.renderEmptyState();
            return;
        }

        const tableHTML = `
            <div class="table-responsive">
                <table class="table table-vcenter table-striped">
                    <thead>
                        <tr>
                            <th class="sortable-header" data-sort="id">
                                ID
                                ${this.renderSortIcon('id')}
                            </th>
                            <th class="sortable-header" data-sort="key">
                                Clave
                                ${this.renderSortIcon('key')}
                            </th>
                            <th class="sortable-header" data-sort="value">
                                Valor
                                ${this.renderSortIcon('value')}
                            </th>
                            <th>Descripción</th>
                            <th>Categoría</th>
                            <th class="sortable-header" data-sort="active">
                                Estado
                                ${this.renderSortIcon('active')}
                            </th>
                            <th class="w-1">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${configurations.map(config => this.renderConfigurationRow(config)).join('')}
                    </tbody>
                </table>
            </div>
        `;

        container.innerHTML = tableHTML;
    }

    /**
     * Renderiza una fila de configuración
     */
    renderConfigurationRow(config) {
        const formattedConfig = this.configurationService.formatConfigurationData(config);
        
        return `
            <tr>
                <td>
                    <span class="text-muted">${config.id}</span>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <code class="text-primary">${config.key}</code>
                    </div>
                </td>
                <td>
                    <div class="text-truncate" style="max-width: 200px;" title="${config.value}">
                        ${formattedConfig.formattedValue}
                    </div>
                </td>
                <td>
                    <div class="text-truncate" style="max-width: 250px;" title="${config.description}">
                        ${config.description}
                    </div>
                </td>
                <td>
                    <span class="badge bg-azure-lt">${config.category}</span>
                </td>
                <td>
                    <span class="badge bg-${formattedConfig.statusBadge}-lt">
                        ${formattedConfig.statusText}
                    </span>
                </td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary edit-config-btn" 
                                data-id="${config.id}"
                                title="Editar configuración">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-${config.active ? 'warning' : 'success'} toggle-status-btn" 
                                data-id="${config.id}"
                                data-status="${config.active}"
                                title="${config.active ? 'Desactivar' : 'Activar'} configuración">
                            <i class="fas fa-${config.active ? 'eye-slash' : 'eye'}"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger delete-config-btn" 
                                data-id="${config.id}"
                                title="Eliminar configuración">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }

    /**
     * Renderiza el estado vacío
     */
    renderEmptyState() {
        return `
            <div class="empty">
                <div class="empty-img">
                    <i class="fas fa-cogs fa-3x text-muted"></i>
                </div>
                <p class="empty-title">No hay configuraciones</p>
                <p class="empty-subtitle text-muted">
                    No se encontraron configuraciones con los filtros aplicados
                </p>
                <div class="empty-action">
                    <button class="btn btn-primary" id="create-config-btn">
                        <i class="fas fa-plus"></i>
                        Crear configuración
                    </button>
                </div>
            </div>
        `;
    }

    /**
     * Renderiza el icono de ordenamiento
     */
    renderSortIcon(field) {
        if (this.sortBy !== field) {
            return '<i class="fas fa-sort text-muted"></i>';
        }
        
        return this.sortDirection === 'asc' 
            ? '<i class="fas fa-sort-up text-primary"></i>'
            : '<i class="fas fa-sort-down text-primary"></i>';
    }

    /**
     * Actualiza la información de paginación
     */
    updatePaginationInfo(meta) {
        this.renderPagination(meta);
        this.renderMetaInfo(meta);
    }

    /**
     * Renderiza la paginación
     */
    renderPagination(meta) {
        const container = document.getElementById('pagination-container');
        if (!container) return;

        if (meta.lastPage <= 1) {
            container.innerHTML = '';
            return;
        }

        const pagination = this.generatePaginationHTML(meta);
        container.innerHTML = pagination;
    }

    /**
     * Genera HTML de paginación
     */
    generatePaginationHTML(meta) {
        const { currentPage, lastPage } = meta;
        let html = '<ul class="pagination justify-content-center">';

        // Botón anterior
        html += `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage - 1}">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>
        `;

        // Páginas
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(lastPage, currentPage + 2);

        for (let i = startPage; i <= endPage; i++) {
            html += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }

        // Botón siguiente
        html += `
            <li class="page-item ${currentPage === lastPage ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage + 1}">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        `;

        html += '</ul>';
        return html;
    }

    /**
     * Renderiza información de meta datos
     */
    renderMetaInfo(meta) {
        const container = document.getElementById('meta-info');
        if (!container) return;

        const start = (meta.currentPage - 1) * meta.perPage + 1;
        const end = Math.min(meta.currentPage * meta.perPage, meta.total);

        container.innerHTML = `
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Mostrando ${start} a ${end} de ${meta.total} registros
                </small>
                <div class="d-flex align-items-center">
                    <label class="me-2">Mostrar:</label>
                    <select class="form-select form-select-sm" style="width: auto;" id="per-page-select">
                        <option value="10" ${meta.perPage === 10 ? 'selected' : ''}>10</option>
                        <option value="15" ${meta.perPage === 15 ? 'selected' : ''}>15</option>
                        <option value="25" ${meta.perPage === 25 ? 'selected' : ''}>25</option>
                        <option value="50" ${meta.perPage === 50 ? 'selected' : ''}>50</option>
                    </select>
                </div>
            </div>
        `;

        // Event listener para cambio de perPage
        const perPageSelect = document.getElementById('per-page-select');
        if (perPageSelect) {
            perPageSelect.addEventListener('change', (e) => {
                this.perPage = parseInt(e.target.value);
                this.currentPage = 1;
                this.loadConfigurations();
            });
        }
    }

    /**
     * Maneja la búsqueda global
     */
    handleSearch(searchTerm) {
        if (searchTerm.trim() === '') {
            delete this.currentFilters.search;
        } else {
            this.currentFilters.search = searchTerm.trim();
        }
        
        this.currentPage = 1;
        this.loadConfigurations();
    }

    /**
     * Maneja los filtros del formulario
     */
    handleFilters() {
        const form = document.getElementById('filter-form');
        if (!form) return;

        const formData = new FormData(form);
        this.currentFilters = {};

        // Procesar todos los filtros
        for (const [key, value] of formData.entries()) {
            if (value.trim() !== '') {
                this.currentFilters[key] = value.trim();
            }
        }

        // Manejar filtros especiales
        const sortBy = document.getElementById('filter-sort-by')?.value;
        const sortDirection = document.getElementById('filter-sort-direction')?.value;
        const perPage = document.getElementById('filter-per-page')?.value;

        if (sortBy) {
            this.sortBy = sortBy;
        }
        if (sortDirection) {
            this.sortDirection = sortDirection;
        }
        if (perPage) {
            this.perPage = parseInt(perPage);
        }

        this.currentPage = 1;
        this.loadConfigurations();
    }

    /**
     * Limpia todos los filtros
     */
    clearFilters() {
        this.currentFilters = {};
        this.currentPage = 1;
        this.sortBy = 'key';
        this.sortDirection = 'asc';
        this.perPage = 15;
        
        // Limpiar formulario
        const form = document.getElementById('filter-form');
        if (form) {
            form.reset();
            
            // Restaurar valores por defecto
            const sortBySelect = document.getElementById('filter-sort-by');
            if (sortBySelect) sortBySelect.value = 'key';
            
            const sortDirectionSelect = document.getElementById('filter-sort-direction');
            if (sortDirectionSelect) sortDirectionSelect.value = 'asc';
            
            const perPageSelect = document.getElementById('filter-per-page');
            if (perPageSelect) perPageSelect.value = '15';
            
            const onlyActiveSelect = document.getElementById('filter-only-active');
            if (onlyActiveSelect) onlyActiveSelect.value = 'false';
        }
        
        // Limpiar búsqueda
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            searchInput.value = '';
        }
        
        this.loadConfigurations();
    }

    /**
     * Muestra modal de creación
     */
    showCreateModal() {
        // Implementar modal de creación
        console.log('Showing create modal');
    }

    /**
     * Muestra modal de edición
     */
    showEditModal(configId) {
        // Implementar modal de edición
        console.log('Showing edit modal for ID:', configId);
    }

    /**
     * Muestra modal de eliminación
     */
    showDeleteModal(configId) {
        // Implementar modal de eliminación
        console.log('Showing delete modal for ID:', configId);
    }

    /**
     * Cambia el estado de una configuración
     */
    async toggleConfigurationStatus(configId, newStatus) {
        try {
            const response = await this.configurationService.toggleConfigurationStatus(configId, newStatus);
            
            if (response.success) {
                this.showSuccess(`Estado de configuración ${newStatus ? 'activado' : 'desactivado'} correctamente`);
                this.loadConfigurations();
            } else {
                throw new Error(response.message || 'Error al cambiar estado');
            }
        } catch (error) {
            console.error('Error toggling configuration status:', error);
            this.showError('Error al cambiar el estado de la configuración');
        }
    }

    /**
     * Refresca los datos
     */
    async refreshData() {
        try {
            // Limpiar cache
            this.configurationService.clearCache();
            
            // Mostrar botón de refresh como cargando
            const refreshBtn = document.getElementById('refresh-btn');
            if (refreshBtn) {
                const icon = refreshBtn.querySelector('i');
                if (icon) {
                    icon.classList.add('fa-spin');
                }
                refreshBtn.disabled = true;
            }
            
            // Recargar configuraciones
            await this.loadConfigurations();
            
            this.showSuccess('Datos actualizados correctamente');
        } catch (error) {
            console.error('Error refreshing data:', error);
            this.showError('Error al actualizar los datos');
        } finally {
            // Restaurar botón de refresh
            const refreshBtn = document.getElementById('refresh-btn');
            if (refreshBtn) {
                const icon = refreshBtn.querySelector('i');
                if (icon) {
                    icon.classList.remove('fa-spin');
                }
                refreshBtn.disabled = false;
            }
        }
    }

    /**
     * Actualiza el contador de resultados
     */
    
    /**
     * Muestra estado de carga
     */
    showLoadingState() {
        const container = document.getElementById('configurations-table');
        if (container) {
            container.innerHTML = `
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2 text-muted">Cargando configuraciones...</p>
                </div>
            `;
        }
    }

    /**
     * Oculta estado de carga
     */
    hideLoadingState() {
        // La tabla se actualiza automáticamente al cargar los datos
    }

    /**
     * Configura tooltips
     */
    setupTooltips() {
        // Inicializar tooltips de Tabler
        if (window.bootstrap && window.bootstrap.Tooltip) {
            document.querySelectorAll('[title]').forEach(el => {
                new window.bootstrap.Tooltip(el);
            });
        }
    }

    /**
     * Muestra mensaje de éxito
     */
    showSuccess(message) {
        if (window.globalToast) {
            window.globalToast.show(message, 'success');
        }
    }

    /**
     * Muestra mensaje de error
     */
    showError(message) {
        if (window.globalToast) {
            window.globalToast.show(message, 'error');
        }
    }
}

// Exportar controlador
window.ConfigurationController = ConfigurationController;
