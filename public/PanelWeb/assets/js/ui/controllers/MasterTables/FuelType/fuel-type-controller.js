/**
 * Controlador para el módulo de Gestión de Tipos de Combustible
 * Maneja la carga, filtrado, paginación y visualización de tipos de combustible
 */
class FuelTypeController {
    constructor() {
        console.log('⛽ Inicializando FuelTypeController...');

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

        // Servicios
        this.fuelTypeService = new FuelTypeService();

        // Elementos del DOM
        this.initializeElements();

        // Event listeners
        this.initializeEventListeners();

        // Cargar datos iniciales
        setTimeout(() => this.load(1), 100); // Delay para DOM ready
    }

    initializeElements() {
        this.loadingContainer = document.getElementById('fuel-types-loading');
        this.contentContainer = document.getElementById('fuel-types-content');
        this.searchInput = document.getElementById('fuel-type-search-input');
        this.statusFilter = document.getElementById('fuel-type-status-filter');
        this.nameFilter = document.getElementById('fuel-type-name-filter');
        this.sortBySelect = document.getElementById('sort-by-select');
        this.sortDirectionSelect = document.getElementById('sort-direction-select');
        this.perPageSelect = document.getElementById('fuel-type-per-page-select');
        this.searchBtn = document.getElementById('search-btn');
        this.clearFiltersBtn = document.getElementById('clear-filters-btn');
        this.refreshBtn = document.getElementById('refresh-fuel-types-btn');
        this.tableBody = document.getElementById('fuel-types-table-body');
        this.showingStart = document.getElementById('showing-start');
        this.showingEnd = document.getElementById('showing-end');
        this.totalRecords = document.getElementById('total-records');
        this.currentPageInfo = document.getElementById('current-page-info');
        this.pagination = document.getElementById('paginationContainer');
        this.totalFuelTypesEl = document.getElementById('total-fuel-types');
        this.activeFuelTypesEl = document.getElementById('active-fuel-types');
        this.inactiveFuelTypesEl = document.getElementById('inactive-fuel-types');
        this.growthPercentageEl = document.getElementById('growth-percentage');
        this.exportExcelBtn = document.getElementById('export-excel-btn');
        this.exportPdfBtn = document.getElementById('export-pdf-btn');
        console.log('✅ Elementos del DOM inicializados');
    }

    initializeEventListeners() {
        if (this.searchBtn) this.searchBtn.addEventListener('click', () => this.handleSearch());
        if (this.clearFiltersBtn) this.clearFiltersBtn.addEventListener('click', () => this.clearFilters());
        if (this.searchInput) this.searchInput.addEventListener('keypress', (e) => { if (e.key === 'Enter') this.handleSearch(); });
        if (this.statusFilter) this.statusFilter.addEventListener('change', () => this.handleSearch());
        if (this.nameFilter) this.nameFilter.addEventListener('keypress', (e) => { if (e.key === 'Enter') this.handleSearch(); });
        if (this.sortBySelect) this.sortBySelect.addEventListener('change', (e) => { this.sortBy = e.target.value; this.handleSearch(); });
        if (this.sortDirectionSelect) this.sortDirectionSelect.addEventListener('change', (e) => { this.sortDirection = e.target.value; this.handleSearch(); });
        if (this.perPageSelect) {
            this.perPageSelect.addEventListener('change', () => {
                this.perPage = parseInt(this.perPageSelect.value, 10) || 15;
                this.currentPage = 1;
                this.load(1);
            });
        }
        if (this.refreshBtn) this.refreshBtn.addEventListener('click', () => this.refresh());
        if (this.exportExcelBtn) this.exportExcelBtn.addEventListener('click', () => this.handleExportExcel());
        if (this.exportPdfBtn) this.exportPdfBtn.addEventListener('click', () => this.handleExportPDF());
        // Paginación simple: flecha - página - flecha
        if (this.pagination) {
            this.pagination.addEventListener('click', (e) => {
                const btn = e.target.closest('button.page-link');
                if (btn && !btn.disabled) {
                    const pageNum = parseInt(btn.dataset.page);
                    if (pageNum && pageNum !== this.currentPage) {
                        this.load(pageNum);
                    }
                }
            });
        }
        console.log('✅ Event listeners inicializados');
    }

    async load(page = 1) {
        if (this.isLoading) return;
        this.isLoading = true;
        this.currentPage = page;
        this.showLoading();
        try {
            const params = {
                page: this.currentPage,
                perPage: this.perPage,
                search: this.filters.search,
                name: this.filters.name,
                active: this.filters.active === 'all' ? undefined : this.filters.active,
                sortBy: this.sortBy,
                sortDirection: this.sortDirection
            };
            const response = await this.fuelTypeService.getFuelTypes(params);
            if (response && response.success && response.data) {
                this.currentData = Array.isArray(response.data.data) ? response.data.data : [];
                let pagination = response.data.pagination || response.data.meta || {};
                this.totalItems = typeof pagination.total_items === 'number' ? pagination.total_items : (typeof pagination.total === 'number' ? pagination.total : this.currentData.length);
                this.totalPages = typeof pagination.total_pages === 'number' ? pagination.total_pages : (typeof pagination.lastPage === 'number' ? pagination.lastPage : 1);
                let apiPerPage = pagination.per_page || pagination.perPage;
                if (typeof apiPerPage === 'number' && apiPerPage > 0) {
                    if (this.perPageSelect && parseInt(this.perPageSelect.value, 10) !== apiPerPage) {
                        this.perPageSelect.value = apiPerPage;
                    }
                    this.perPage = apiPerPage;
                }
                this.renderTable();
                this.renderPagination(pagination);
                if (this.showingStart && this.showingEnd && this.totalRecords) {
                    let start = ((this.currentPage - 1) * this.perPage) + 1;
                    let end = start + this.currentData.length - 1;
                    if (this.currentData.length === 0) start = end = 0;
                    this.showingStart.textContent = start;
                    this.showingEnd.textContent = end;
                    this.totalRecords.textContent = this.totalItems;
                }
                await this.updateStatistics();
            } else {
                throw new Error(response?.message || 'Error al cargar tipos de combustible');
            }
        } catch (error) {
            this.showError('Error al cargar los tipos de combustible. Intente nuevamente.');
            this.currentData = [];
        } finally {
            this.isLoading = false;
            this.hideLoading();
        }
    }

    renderTable() {
        if (!this.tableBody) return;
        if (!Array.isArray(this.currentData)) this.currentData = [];
        if (this.currentData.length === 0) {
            this.tableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-gas-pump fs-1 mb-3"></i>
                            <p>No se encontraron tipos de combustible</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }
        const rows = this.currentData.map((fuelType) => {
            const statusBadge = fuelType.active 
                ? `<span class="badge bg-success-lt"><i class="fas fa-check-circle me-1"></i>Activo</span>`
                : `<span class="badge bg-danger-lt"><i class="fas fa-times-circle me-1"></i>Inactivo</span>`;
            return `
                <tr>
                    <td class="text-muted">${fuelType.id}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-xs bg-cyan text-white me-2">
                                <i class="fas fa-gas-pump"></i>
                            </div>
                            <div>
                                <strong>${this.escapeHtml(fuelType.name)}</strong>
                            </div>
                        </div>
                    </td>
                    <td>${statusBadge}</td>
                    <td>
                        <div class="btn-list">
                            <button class="btn btn-sm btn-outline-primary" 
                                    onclick="fuelTypeController.viewDetails(${fuelType.id})"
                                    title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
        this.tableBody.innerHTML = rows;
    }

    /**
     * Paginación solo flecha-actual-flecha
     */
    /**
 * Paginación moderna: flechas, páginas cercanas y puntos suspensivos ("...")
 */
renderPagination(meta) {
    if (!this.pagination || !meta) return;
    let currentPage = meta.currentPage || meta.page || this.currentPage || 1;
    let totalPages = meta.lastPage || meta.total_pages || this.totalPages || 1;
    let html = '';

    // Flecha anterior
    html += `<li class="page-item${currentPage === 1 ? ' disabled' : ''}">
        <button class="page-link" data-page="${currentPage - 1}" ${currentPage === 1 ? 'disabled' : ''}>
            <i class="fas fa-chevron-left"></i>
        </button>
    </li>`;

    // Lógica moderna: primeras, cercanas, últimas y puntos suspensivos
    const range = 1; // Cuántas páginas alrededor de la actual mostrar
    let start = Math.max(1, currentPage - range);
    let end = Math.min(totalPages, currentPage + range);

    // Primera página y puntos suspensivos al inicio
    if (start > 1) {
        html += `<li class="page-item"><button class="page-link" data-page="1">1</button></li>`;
        if (start > 2) {
            html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
        }
    }

    // Páginas del rango visible
    for (let i = start; i <= end; i++) {
        html += `<li class="page-item${i === currentPage ? ' active' : ''}">
            <button class="page-link" data-page="${i}">${i}</button>
        </li>`;
    }

    // Última página y puntos suspensivos al final
    if (end < totalPages) {
        if (end < totalPages - 1) {
            html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
        }
        html += `<li class="page-item"><button class="page-link" data-page="${totalPages}">${totalPages}</button></li>`;
    }

    // Flecha siguiente
    html += `<li class="page-item${currentPage === totalPages ? ' disabled' : ''}">
        <button class="page-link" data-page="${currentPage + 1}" ${currentPage === totalPages ? 'disabled' : ''}>
            <i class="fas fa-chevron-right"></i>
        </button>
    </li>`;

    this.pagination.innerHTML = html;
}

    async updateStatistics() {
        try {
            const stats = await this.fuelTypeService.getFuelTypesStats();
            if (this.totalFuelTypesEl) this.totalFuelTypesEl.textContent = stats.total;
            if (this.activeFuelTypesEl) this.activeFuelTypesEl.textContent = stats.active;
            if (this.inactiveFuelTypesEl) this.inactiveFuelTypesEl.textContent = stats.inactive;
            if (this.growthPercentageEl) this.growthPercentageEl.textContent = `${stats.growth}%`;
        } catch (error) {
            console.error('❌ Error al actualizar estadísticas:', error);
        }
    }

    handleSearch() {
        const searchValue = this.searchInput?.value?.trim() || '';
        const nameValue = this.nameFilter?.value?.trim() || '';
        let activeValue = this.statusFilter?.value;
        if (activeValue === 'all' || activeValue === undefined) {
            activeValue = undefined;
        } else if (activeValue === 'true') {
            activeValue = true;
        } else if (activeValue === 'false') {
            activeValue = false;
        }
        this.filters = {
            search: searchValue,
            name: nameValue,
            active: activeValue
        };
        this.currentPage = 1;
        this.load(1);
    }

    clearFilters() {
        this.filters = { search: '', name: '', active: 'all' };
        if (this.searchInput) this.searchInput.value = '';
        if (this.nameFilter) this.nameFilter.value = '';
        if (this.statusFilter) this.statusFilter.value = 'all';
        if (this.sortBySelect) this.sortBySelect.value = 'name';
        if (this.sortDirectionSelect) this.sortDirectionSelect.value = 'ASC';
        this.sortBy = 'name';
        this.sortDirection = 'ASC';
        this.currentPage = 1;
        this.load(1);
        if (window.showRecoveryToast) window.showRecoveryToast('Filtros limpiados correctamente', 'info');
    }

    refresh() {
        this.fuelTypeService.clearCache?.();
        this.load(this.currentPage);
        if (window.showRecoveryToast) window.showRecoveryToast('Datos actualizados correctamente', 'success');
    }

    viewDetails(id) {
        const fuelType = this.currentData.find(item => item.id === id);
        if (!fuelType) {
            this.showError('Tipo de combustible no encontrado.');
            return;
        }
        if (window.showRecoveryToast) window.showRecoveryToast(`Viendo detalles de: ${fuelType.name}`, 'info');
    }

    async handleExportExcel() {
        try {
            this.setExportButtonLoading(this.exportExcelBtn, true);
            await this.fuelTypeService.exportToExcel(this.filters);
            if (window.showRecoveryToast) window.showRecoveryToast('Exportación a Excel completada', 'success');
        } catch (error) {
            if (window.showRecoveryToast) window.showRecoveryToast('Error al exportar a Excel', 'error');
        } finally {
            this.setExportButtonLoading(this.exportExcelBtn, false);
        }
    }

    async handleExportPDF() {
        try {
            this.setExportButtonLoading(this.exportPdfBtn, true);
            await this.fuelTypeService.exportToPDF(this.filters);
            if (window.showRecoveryToast) window.showRecoveryToast('Exportación a PDF completada', 'success');
        } catch (error) {
            if (window.showRecoveryToast) window.showRecoveryToast('Error al exportar a PDF', 'error');
        } finally {
            this.setExportButtonLoading(this.exportPdfBtn, false);
        }
    }

    setExportButtonLoading(button, isLoading) {
        if (!button) return;
        if (isLoading) {
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Exportando...';
        } else {
            button.disabled = false;
            if (button === this.exportExcelBtn) button.innerHTML = '<i class="fas fa-file-excel me-1"></i> Excel';
            else if (button === this.exportPdfBtn) button.innerHTML = '<i class="fas fa-file-pdf me-1"></i> PDF';
        }
    }

    showLoading() {
        if (this.loadingContainer) this.loadingContainer.style.display = 'block';
        if (this.contentContainer) this.contentContainer.style.display = 'none';
    }
    hideLoading() {
        if (this.loadingContainer) this.loadingContainer.style.display = 'none';
        if (this.contentContainer) this.contentContainer.style.display = 'block';
    }
    showError(message) {
        if (window.showRecoveryToast) window.showRecoveryToast(message, 'error');
        if (this.tableBody) {
            this.tableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center py-4">
                        <div class="text-danger">
                            <i class="fas fa-exclamation-triangle fs-1 mb-3"></i>
                            <p>${this.escapeHtml(message)}</p>
                            <button class="btn btn-outline-primary btn-sm" onclick="fuelTypeController.refresh()">
                                <i class="fas fa-sync-alt me-1"></i> Reintentar
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }
    }
    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Variable global para acceso desde HTML
let fuelTypeController;
