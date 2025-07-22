// Función debounce reutilizable
function debounce(func, wait = 300) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

// Controlador para la lista de modelos de vehículos (VehicleModel)
class VehicleModelListController {
    constructor() {
        console.log('🏗️ Inicializando VehicleModelListController');
        this.tableBody   = document.getElementById('vehicleModelTableBody');
        this.stats       = {
            total:       document.getElementById('totalVehicleModels'),
            filtered:    document.getElementById('filteredResults'),
            page:        document.getElementById('currentPage'),
            range:       document.getElementById('itemsRange'),
            totalFooter: document.getElementById('totalVehicleModelsFooter'),
            rangeFooter: document.getElementById('itemsRangeFooter')
        };
        this.filters     = {
            search:   document.getElementById('searchInput'),
            name:     document.getElementById('nameFilter'),
            brandId:  document.getElementById('brandIdFilter'),
            active:   document.getElementById('activeFilter'),
            perPage:  document.getElementById('perPageSelect'),
            sortBy:   document.getElementById('sortBySelect'),
            sortOrder:document.getElementById('sortOrderSelect')
        };
        this.pagination  = document.getElementById('paginationContainer');
        this.refreshBtn  = document.getElementById('refreshBtn');
        this.clearBtn    = document.getElementById('clearFiltersBtn');

        // **Nuevo**: almacenar los datos cargados
        this.items = [];

        this.initEvents();
        setTimeout(() => this.load(1), 100); // DOM listo
    }

    initEvents() {
        // Inputs de texto usan debounce
        ['search','name'].forEach(key => {
            const el = this.filters[key];
            if (el) el.addEventListener('input', debounce(() => this.load(1), 300));
        });
        // Selects usan change directo
        ['brandId','active','perPage','sortBy','sortOrder'].forEach(key => {
            const el = this.filters[key];
            if (el) el.addEventListener('change', () => this.load(1));
        });
        if (this.refreshBtn) this.refreshBtn.addEventListener('click', () => this.load(this.currentPage || 1));
        if (this.clearBtn)   this.clearBtn.addEventListener('click', () => this.clearFilters());

        const clearSearchBtn = document.getElementById('clearSearchBtn');
        if (clearSearchBtn && this.filters.search) {
            clearSearchBtn.addEventListener('click', () => {
                this.filters.search.value = '';
                this.load(1);
            });
        }
    }

    clearFilters() {
        Object.values(this.filters).forEach(input => { if (input) input.value = ''; });
        if (this.filters.perPage)  this.filters.perPage.value  = '15';
        if (this.filters.sortBy)   this.filters.sortBy.value   = 'name';
        if (this.filters.sortOrder)this.filters.sortOrder.value= 'ASC';
        this.load(1);
    }

    async load(page = 1) {
        try {
            this.showLoading();
            const params = {
                page,
                perPage: this.filters.perPage?.value || 15,
                search:  this.filters.search?.value || '',
                name:    this.filters.name?.value   || '',
                sortBy:  this.filters.sortBy?.value || 'name',
                sortOrder: this.filters.sortOrder?.value || 'ASC'
            };
            if (this.filters.brandId?.value) params.brandId = parseInt(this.filters.brandId.value, 10);
            if (this.filters.active?.value  !== '') params.active  = this.filters.active.value === 'true';

            const response = await VehicleModelService.getVehicleModels(params);

            // Detectar meta
            const meta  = (response.data && (response.data.meta || response.data.pagination)) || {};
            const items = response.data ? response.data.data : [];

            // **Nuevo**: guardar items para edición
            this.items = items;

            this.render(items);
            this.updateStats(items, meta);
            this.renderPagination(meta);
        } catch (error) {
            this.showError(error.message);
            window.GlobalToast?.show('Error al cargar modelos de vehículos: ' + error.message, 'error');
        }
    }

    showLoading() {
        if (!this.tableBody) return;
        this.tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-4">
                    <div class="d-flex justify-content-center align-items-center">
                        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                        <span>Cargando modelos de vehículos...</span>
                    </div>
                </td>
            </tr>`;
    }

    showError(message) {
        if (!this.tableBody) return;
        this.tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-4">
                    <div class="text-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Error: ${message}
                    </div>
                </td>
            </tr>`;
    }

    render(items) {
        if (!this.tableBody) return;
        if (!items.length) {
            this.showError('No se encontraron modelos de vehículos con los filtros aplicados');
            return;
        }
        this.tableBody.innerHTML = items.map(this.renderVehicleModelRow).join('');
    }

    renderVehicleModelRow(model) {
        return `
            <tr>
                <td class="text-center text-muted">${model.id}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-sm me-2 bg-primary-lt">
                            <i class="fas fa-car-side text-primary"></i>
                        </span>
                        <div class="font-weight-medium">${model.name}</div>
                    </div>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <span class="avatar avatar-sm me-2 bg-info-lt">
                            <i class="fas fa-tag text-info"></i>
                        </span>
                        <div>
                            <div class="font-weight-medium">${model.brandName}</div>
                            <div class="text-muted small">ID: ${model.brandId}</div>
                        </div>
                    </div>
                </td>
                <td class="text-center">
                    <span class="badge ${model.active ? 'bg-success-lt' : 'bg-danger-lt'}">
                        <i class="fas ${model.active ? 'fa-check-circle' : 'fa-times-circle'} me-1"></i>
                        ${model.active ? 'Activo' : 'Inactivo'}
                    </span>
                </td>
                <td class="text-center">
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-outline-warning" title="Editar"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-danger" title="Eliminar"
                            data-action="delete-model"
                            data-model-id="${model.id}"
                            data-model-name="${model.name}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>`;
    }

    updateStats(items, meta) {
        const total   = typeof meta.total === 'number' ? meta.total : items.length;
        const page    = typeof meta.page  === 'number' ? meta.page  : 1;
        const perPage = typeof meta.perPage === 'number' ? meta.perPage : 15;
        const start   = total > 0 ? (page - 1) * perPage + 1 : 0;
        const end     = total > 0 ? Math.min(start + items.length - 1, total) : 0;
        const range   = total === 0 ? '0-0' : `${start}-${end}`;

        this.stats.total.textContent      = total;
        this.stats.filtered.textContent   = items.length;
        this.stats.page.textContent       = page;
        this.stats.range.textContent      = range;
        this.stats.totalFooter.textContent = total;
        this.stats.rangeFooter.textContent = range;
    }

    renderPagination(meta) {
        if (!this.pagination) return;
        const totalPages  = meta.totalPages || meta.lastPage || 1;
        const currentPage = meta.page || meta.currentPage || 1;
        if (totalPages <= 1) {
            this.pagination.innerHTML = '';
            return;
        }

        let html = `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage - 1}">
                    <i class="fas fa-chevron-left"></i>
                </a>
            </li>`;
        const range = 2;
        const start = Math.max(1, currentPage - range);
        const end   = Math.min(totalPages, currentPage + range);

        if (start > 1) {
            html += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
            if (start > 2) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
        }
        for (let p = start; p <= end; p++) {
            html += `
                <li class="page-item ${p === currentPage ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${p}">${p}</a>
                </li>`;
        }
        if (end < totalPages) {
            if (end < totalPages - 1) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
            html += `<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`;
        }
        html += `
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage + 1}">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </li>`;

        this.pagination.innerHTML = html;
        this.pagination.querySelectorAll('a[data-page]').forEach(a => {
            a.addEventListener('click', e => {
                e.preventDefault();
                const pg = parseInt(a.dataset.page, 10);
                if (!isNaN(pg) && pg >= 1 && pg <= totalPages && pg !== currentPage) {
                    this.load(pg);
                }
            });
        });
    }

    refresh() {
        this.load(this.currentPage || 1);
    }
}

// Inicializador para la página de modelos de vehículos
window.VehicleModelListController = VehicleModelListController;
