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
        this.editModalController = null;
        this.deleteModalController = null;
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
            this.initializeEditModal();
            this.initializeDeleteModal();
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
     * Inicializa el controlador del modal de edición
     */
    initializeEditModal() {
        try {
            this.editModalController = new EditConfigurationModalController();
            this.editModalController.setParentController(this);
            console.log('✅ Controlador de modal de edición inicializado');
        } catch (error) {
            console.error('❌ Error inicializando controlador de modal de edición:', error);
        }
    }

    /**
     * Inicializa el controlador del modal de eliminación
     */
    initializeDeleteModal() {
        try {
            this.deleteModalController = new DeleteConfigurationModalController();
            this.deleteModalController.setParentController(this);
            console.log('✅ Controlador de modal de eliminación inicializado');
        } catch (error) {
            console.error('❌ Error inicializando controlador de modal de eliminación:', error);
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
            if (e.target.matches('.edit-config-btn') || e.target.closest('.edit-config-btn')) {
                e.preventDefault();
                const btn = e.target.matches('.edit-config-btn') ? e.target : e.target.closest('.edit-config-btn');
                const configId = parseInt(btn.dataset.id);
                console.log('🔧 Click en botón editar, ID:', configId);
                this.showEditModal(configId);
            }
        });

        // Eliminar configuración
        document.addEventListener('click', (e) => {
            if (e.target.matches('.delete-config-btn') || e.target.closest('.delete-config-btn')) {
                e.preventDefault();
                const btn = e.target.matches('.delete-config-btn') ? e.target : e.target.closest('.delete-config-btn');
                const configId = parseInt(btn.dataset.id);
                console.log('🗑️ Click en botón eliminar, ID:', configId);
                this.showDeleteModal(configId);
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
        const container = document.getElementById('configurations-table-body');
        if (!container) {
            console.error('Elemento configurations-table-body no encontrado');
            return;
        }

        const configurations = data.items || [];
        
        if (configurations.length === 0) {
            container.innerHTML = this.renderEmptyState();
            return;
        }

        // Solo renderizar las filas del tbody
        const rowsHTML = configurations.map(config => this.renderConfigurationRow(config)).join('');
        container.innerHTML = rowsHTML;
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
                    <span class="badge bg-${formattedConfig.statusBadge}-lt">
                        ${formattedConfig.statusText}
                    </span>
                </td>
                <td>
                    <span class="text-muted">${formattedConfig.createdAt || '-'}</span>
                </td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-primary edit-config-btn" 
                                data-id="${config.id}"
                                title="Editar configuración">
                            <i class="fas fa-edit"></i>
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
            <tr>
                <td colspan="6" class="text-center py-5">
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
                </td>
            </tr>
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
        // Actualizar los spans de información
        const showingStart = document.getElementById('showing-start');
        const showingEnd = document.getElementById('showing-end');
        const totalRecords = document.getElementById('total-records');
        const currentPageInfo = document.getElementById('current-page-info');

        if (showingStart && showingEnd && totalRecords) {
            const start = (meta.currentPage - 1) * meta.perPage + 1;
            const end = Math.min(meta.currentPage * meta.perPage, meta.total);

            showingStart.textContent = meta.total > 0 ? start : 0;
            showingEnd.textContent = meta.total > 0 ? end : 0;
            totalRecords.textContent = meta.total;
        }

        if (currentPageInfo) {
            currentPageInfo.textContent = meta.currentPage;
        }

        // Actualizar botones de navegación
        const prevBtn = document.getElementById('prev-page-btn');
        const nextBtn = document.getElementById('next-page-btn');

        if (prevBtn) {
            prevBtn.disabled = meta.currentPage <= 1;
        }

        if (nextBtn) {
            nextBtn.disabled = meta.currentPage >= meta.lastPage;
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
        console.log('📝 Intentando abrir modal de edición para ID:', configId);
        console.log('📝 Controlador de edición disponible:', !!this.editModalController);
        
        if (this.editModalController) {
            this.editModalController.openModal(configId);
        } else {
            console.error('❌ Controlador de modal de edición no inicializado');
            this.showError('No se pudo abrir el modal de edición');
        }
    }

    /**
     * Muestra modal de eliminación
     */
    showDeleteModal(configId) {
        console.log('🗑️ Intentando abrir modal de eliminación para ID:', configId);
        console.log('🗑️ Controlador de eliminación disponible:', !!this.deleteModalController);
        
        if (!this.deleteModalController) {
            console.error('❌ Controlador de modal de eliminación no inicializado');
            this.showError('No se pudo abrir el modal de eliminación');
            return;
        }

        // Buscar los datos de la configuración en los datos actuales
        const configData = this.currentData?.find(config => config.id == configId);
        
        if (configData) {
            console.log('📋 Datos de configuración encontrados:', configData);
            this.deleteModalController.openModalWithData(configData);
        } else {
            console.warn('⚠️ No se encontraron datos de configuración localmente, usando método tradicional');
            this.deleteModalController.openModal(configId);
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
