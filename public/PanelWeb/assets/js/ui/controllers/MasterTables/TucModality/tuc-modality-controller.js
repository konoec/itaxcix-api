/**
 * Controlador para el módulo de Gestión de Modalidades TUC
 * Maneja la carga, filtrado, paginación y visualización de modalidades TUC
 */
class TucModalityController {
    constructor() {
        console.log('🚛 Inicializando TucModalityController...');

        // Configuración inicial
        this.currentPage = 1;
        this.perPage = 15;
        this.totalPages = 1;
        this.totalItems = 0;
        this.currentData = [];
        this.sortBy = 'name';
        this.sortDirection = 'ASC';
        this.isLoading = false;

        // Filtros actuales
        this.filters = {
            search: '',
            name: '',
            active: 'all'
        };

        // Elementos del DOM
        this.initializeElements();

        // Event listeners
        this.initializeEventListeners();

        // Cargar datos iniciales
        this.loadTucModalities();
    }

    /**
     * Inicializa las referencias a elementos del DOM
     */
    initializeElements() {
        // Contenedores principales
        this.loadingContainer = document.getElementById('tuc-modalities-loading');
        this.contentContainer = document.getElementById('tuc-modalities-content');

        // Filtros y búsqueda
        this.searchInput = document.getElementById('tuc-modality-search-input');
        this.statusFilter = document.getElementById('tuc-modality-status-filter');
        this.nameFilter = document.getElementById('tuc-modality-name-filter');
        this.sortBySelect = document.getElementById('sort-by-select');
        this.sortDirectionSelect = document.getElementById('sort-direction-select');

        // Botones de filtro
        this.searchBtn = document.getElementById('search-btn');
        this.clearFiltersBtn = document.getElementById('clear-filters-btn');

        // Botones de acción
        this.refreshBtn = document.getElementById('refresh-tuc-modalities-btn');

        // Tabla
        this.tableBody = document.getElementById('tuc-modalities-table-body');

        // Paginación
        this.showingStart = document.getElementById('showing-start');
        this.showingEnd = document.getElementById('showing-end');
        this.totalRecords = document.getElementById('total-records');
        this.currentPageInfo = document.getElementById('current-page-info');
        this.prevPageBtn = document.getElementById('prev-page-btn');
        this.nextPageBtn = document.getElementById('next-page-btn');

        // Estadísticas
        this.totalTucModalitiesEl = document.getElementById('total-tuc-modalities');
        this.activeTucModalitiesEl = document.getElementById('active-tuc-modalities');
        this.inactiveTucModalitiesEl = document.getElementById('inactive-tuc-modalities');
        this.growthPercentageEl = document.getElementById('growth-percentage');

        // Botones de exportación
        this.exportExcelBtn = document.getElementById('export-excel-btn');
        this.exportPdfBtn = document.getElementById('export-pdf-btn');

        console.log('✅ Elementos del DOM inicializados');
    }

    /**
     * Inicializa los event listeners
     */
    initializeEventListeners() {
        // Búsqueda y filtros
        if (this.searchBtn) {
            this.searchBtn.addEventListener('click', () => this.handleSearch());
        }

        if (this.clearFiltersBtn) {
            this.clearFiltersBtn.addEventListener('click', () => this.clearFilters());
        }

        // Filtros en tiempo real
        if (this.searchInput) {
            this.searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    this.handleSearch();
                }
            });
        }

        if (this.statusFilter) {
            this.statusFilter.addEventListener('change', () => this.handleFilterChange());
        }

        if (this.nameFilter) {
            this.nameFilter.addEventListener('input', debounce(() => this.handleFilterChange(), 500));
        }

        // Ordenamiento
        if (this.sortBySelect) {
            this.sortBySelect.addEventListener('change', () => this.handleSortChange());
        }

        if (this.sortDirectionSelect) {
            this.sortDirectionSelect.addEventListener('change', () => this.handleSortChange());
        }

        // Paginación
        if (this.prevPageBtn) {
            this.prevPageBtn.addEventListener('click', () => this.goToPreviousPage());
        }

        if (this.nextPageBtn) {
            this.nextPageBtn.addEventListener('click', () => this.goToNextPage());
        }

        // Botón de actualizar
        if (this.refreshBtn) {
            this.refreshBtn.addEventListener('click', () => this.refreshData());
        }

        // Exportación
        if (this.exportExcelBtn) {
            this.exportExcelBtn.addEventListener('click', () => this.exportToExcel());
        }

        if (this.exportPdfBtn) {
            this.exportPdfBtn.addEventListener('click', () => this.exportToPdf());
        }

        console.log('✅ Event listeners inicializados');
    }

    /**
     * Carga las modalidades TUC con los filtros actuales
     */
    async loadTucModalities() {
        if (this.isLoading) {
            console.log('⚠️ Carga ya en progreso, omitiendo...');
            return;
        }

        this.isLoading = true;
        this.showLoading();

        try {
            console.log('🚛 Cargando modalidades TUC...', {
                page: this.currentPage,
                perPage: this.perPage,
                filters: this.filters,
                sortBy: this.sortBy,
                sortDirection: this.sortDirection
            });

            const params = {
                page: this.currentPage,
                perPage: this.perPage,
                search: this.filters.search,
                name: this.filters.name,
                active: this.filters.active === 'all' ? undefined : this.filters.active === 'true',
                sortBy: this.sortBy,
                sortDirection: this.sortDirection
            };

            // Ahora la llamada es a la clase estática
            const response = await TucModalityService.getTucModalities(params);

            if (response.success && response.data) {
                this.currentData = Array.isArray(response.data.data) ? response.data.data : [];
                this.totalItems = response.data.pagination?.total_items || 0;
                this.totalPages = response.data.pagination?.total_pages || 1;

                console.log('📊 Datos procesados:', {
                    currentData: this.currentData.length,
                    totalItems: this.totalItems,
                    totalPages: this.totalPages
                });

                this.renderTable();
                this.updatePagination();
                await this.updateStatistics();

                console.log('✅ Modalidades TUC cargadas exitosamente');
            } else {
                console.warn('⚠️ Respuesta no exitosa:', response);
                this.showError(response.message || 'Error al cargar modalidades TUC');
            }

        } catch (error) {
            console.error('❌ Error al cargar modalidades TUC:', error);
            this.showError(error.message || 'Error al cargar modalidades TUC');
        } finally {
            this.isLoading = false;
            this.hideLoading();
        }
    }

    /**
     * Renderiza la tabla con los datos actuales
     */
    renderTable() {
        if (!this.tableBody) {
            console.error('❌ Elemento tableBody no encontrado');
            return;
        }

        // Limpiar contenido anterior
        this.tableBody.innerHTML = '';

        if (!Array.isArray(this.currentData) || this.currentData.length === 0) {
            this.showEmptyState();
            return;
        }

        // Renderizar filas
        this.currentData.forEach((modality, index) => {
            const row = this.createTableRow(modality, index);
            this.tableBody.appendChild(row);
        });

        console.log(`✅ Tabla renderizada con ${this.currentData.length} filas`);
    }

    /**
     * Crea una fila de la tabla para una modalidad TUC
     * @param {Object} modality - Datos de la modalidad TUC
     * @param {number} index - Índice de la fila
     * @returns {HTMLElement} - Elemento TR de la tabla
     */
    createTableRow(modality, index) {
        const row = document.createElement('tr');

        // Calcular número de fila global
        const globalIndex = ((this.currentPage - 1) * this.perPage) + index + 1;

        row.innerHTML = `
            <td class="text-muted">${globalIndex}</td>
            <td>
                <div class="d-flex align-items-center">
                    <span class="avatar avatar-sm me-2 bg-blue-lt">
                        <i class="fas fa-id-card"></i>
                    </span>
                    <div>
                        <div class="fw-bold">${this.escapeHtml(modality.name || 'Sin nombre')}</div>
                        <div class="text-muted small">ID: ${modality.id || 'N/A'}</div>
                    </div>
                </div>
            </td>
            <td>
                <span class="badge ${modality.active ? 'bg-success-lt' : 'bg-danger-lt'} badge-outline">
                    <i class="fas ${modality.active ? 'fa-check-circle' : 'fa-times-circle'} me-1"></i>
                    ${modality.active ? 'Activo' : 'Inactivo'}
                </span>
            </td>
            <td class="text-center">
                <button class="btn btn-outline-warning btn-sm me-1 edit-tuc-modality-btn"
                        title="Editar" data-id="${modality.id}" data-name="${this.escapeHtml(modality.name)}" data-active="${modality.active}">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-outline-red delete-tuc-modality-btn"
                        title="Eliminar" data-id="${modality.id}" data-name="${this.escapeHtml(modality.name)}">
                    <i class="fas fa-trash text-red"></i>
                </button>
            </td>
        `;

        return row;
    }

    /**
     * Muestra el estado vacío cuando no hay datos
     */
    showEmptyState() {
        this.tableBody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center py-5">
                    <div class="empty">
                        <div class="empty-img">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-database-off" width="128" height="128" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="m0 0h24v24H0z" fill="none"></path>
                                <path d="M12.983 8.978c3.955 -.182 7.017 -1.446 7.017 -2.978c0 -1.657 -3.582 -3 -8 -3s-8 1.343 -8 3c0 1.532 3.062 2.796 7.017 2.978"></path>
                                <path d="M4 6v6c0 1.657 3.582 3 8 3c.478 0 .947 -.016 1.402 -.046"></path>
                                <path d="M4 12v6c0 1.657 3.582 3 8 3c1.146 0 2.208 -.069 3.202 -.19"></path>
                                <path d="M20 12v6"></path>
                                <path d="M3 3l18 18"></path>
                            </svg>
                        </div>
                        <p class="empty-title">No se encontraron modalidades TUC</p>
                    </div>
                </td>
            </tr>
        `;
    }

    /**
     * Muestra el estado de carga
     */
    showLoading() {
        if (this.loadingContainer) {
            this.loadingContainer.style.display = 'block';
        }

        if (this.contentContainer) {
            this.contentContainer.style.opacity = '0.6';
        }

        // Mostrar spinner en la tabla
        if (this.tableBody) {
            this.tableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center py-4">
                        <div class="text-muted">
                            <div class="spinner-border mb-3" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <p>Cargando modalidades TUC...</p>
                        </div>
                    </td>
                </tr>
            `;
        }
    }

    /**
     * Oculta el estado de carga
     */
    hideLoading() {
        if (this.loadingContainer) {
            this.loadingContainer.style.display = 'none';
        }

        if (this.contentContainer) {
            this.contentContainer.style.opacity = '1';
        }
    }

    /**
     * Muestra un mensaje de error
     * @param {string} message - Mensaje de error
     */
    showError(message) {
        if (this.tableBody) {
            this.tableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center py-5">
                        <div class="empty">
                            <div class="empty-img">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-triangle" width="128" height="128" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="m0 0h24v24H0z" fill="none"></path>
                                    <path d="m12 9v2m0 4v.01"></path>
                                    <path d="m5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75"></path>
                                </svg>
                            </div>
                            <p class="empty-title text-danger">Error al cargar datos</p>
                            <p class="empty-subtitle text-muted">${this.escapeHtml(message)}</p>
                            <div class="empty-action">
                                <button class="btn btn-primary" onclick="window.tucModalityController.refreshData()">
                                    <i class="fas fa-sync-alt me-1"></i>
                                    Reintentar
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        }

        console.error('❌ Error mostrado al usuario:', message);
    }

    /**
     * Actualiza la información de paginación
     */
    updatePagination() {
        const start = this.totalItems === 0 ? 0 : ((this.currentPage - 1) * this.perPage) + 1;
        const end = Math.min(this.currentPage * this.perPage, this.totalItems);

        if (this.showingStart) this.showingStart.textContent = start;
        if (this.showingEnd) this.showingEnd.textContent = end;
        if (this.totalRecords) this.totalRecords.textContent = this.totalItems;
        if (this.currentPageInfo) this.currentPageInfo.textContent = `Página ${this.currentPage} de ${this.totalPages}`;

        // Habilitar/deshabilitar botones
        if (this.prevPageBtn) {
            this.prevPageBtn.disabled = this.currentPage <= 1;
        }

        if (this.nextPageBtn) {
            this.nextPageBtn.disabled = this.currentPage >= this.totalPages;
        }
    }

    /**
     * Actualiza las estadísticas (simple, usando los datos ya cargados)
     */
    async updateStatistics() {
        try {
            const total = this.totalItems;
            const active = this.currentData.filter(item => item.active).length;
            const inactive = total - active;
            const growthPercentage = '0%';

            if (this.totalTucModalitiesEl) this.totalTucModalitiesEl.textContent = total;
            if (this.activeTucModalitiesEl) this.activeTucModalitiesEl.textContent = active;
            if (this.inactiveTucModalitiesEl) this.inactiveTucModalitiesEl.textContent = inactive;
            if (this.growthPercentageEl) this.growthPercentageEl.textContent = growthPercentage;

        } catch (error) {
            console.error('❌ Error actualizando estadísticas:', error);
        }
    }

    /**
     * Maneja la búsqueda
     */
    handleSearch() {
        if (this.searchInput) {
            this.filters.search = this.searchInput.value.trim();
        }

        this.currentPage = 1;
        this.loadTucModalities();
    }

    /**
     * Maneja cambios en los filtros
     */
    handleFilterChange() {
        if (this.statusFilter) {
            this.filters.active = this.statusFilter.value;
        }

        if (this.nameFilter) {
            this.filters.name = this.nameFilter.value.trim();
        }

        this.currentPage = 1;
        this.loadTucModalities();
    }

    /**
     * Maneja cambios en el ordenamiento
     */
    handleSortChange() {
        if (this.sortBySelect) {
            this.sortBy = this.sortBySelect.value;
        }

        if (this.sortDirectionSelect) {
            this.sortDirection = this.sortDirectionSelect.value;
        }

        this.currentPage = 1;
        this.loadTucModalities();
    }

    /**
     * Va a la página anterior
     */
    goToPreviousPage() {
        if (this.currentPage > 1) {
            this.currentPage--;
            this.loadTucModalities();
        }
    }

    /**
     * Va a la página siguiente
     */
    goToNextPage() {
        if (this.currentPage < this.totalPages) {
            this.currentPage++;
            this.loadTucModalities();
        }
    }

    /**
     * Limpia todos los filtros
     */
    clearFilters() {
        this.filters = {
            search: '',
            name: '',
            active: 'all'
        };

        if (this.searchInput) this.searchInput.value = '';
        if (this.nameFilter) this.nameFilter.value = '';
        if (this.statusFilter) this.statusFilter.value = 'all';

        this.currentPage = 1;
        this.loadTucModalities();
    }

    /**
     * Actualiza los datos (recarga desde la API)
     */
    refreshData() {
        // Si tu servicio tuviera cache, aquí lo limpiarías:
        // TucModalityService.clearCache();
        this.loadTucModalities();
    }

    /**
     * Exporta datos a Excel
     */
    exportToExcel() {
        console.log('📊 Exportando a Excel...');
        alert('Funcionalidad de exportación a Excel en desarrollo');
    }

    /**
     * Exporta datos a PDF
     */
    exportToPdf() {
        console.log('📄 Exportando a PDF...');
        alert('Funcionalidad de exportación a PDF en desarrollo');
    }

    /**
     * Ver detalles de una modalidad TUC
     * @param {number} modalityId - ID de la modalidad TUC
     */
    viewDetails(modalityId) {
        console.log('👀 Ver detalles de modalidad TUC:', modalityId);
        alert(`Ver detalles de modalidad TUC ${modalityId} - En desarrollo`);
    }

    /**
     * Escapa HTML para prevenir XSS
     * @param {string} text - Texto a escapar
     * @returns {string} - Texto escapado
     */
    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }
}

// Función debounce para optimizar filtros
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Asegurar que la clase esté disponible globalmente
window.tucModalityController = new TucModalityController();
window.TucModalityController = TucModalityController;

