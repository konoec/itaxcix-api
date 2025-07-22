/**
 * Controlador para reportes de vehículos con filtros funcionales y paginación avanzada tipo Tabler
 */
class VehicleReportsController {
    constructor() {
        console.log('🚗 Inicializando VehicleReportsController...');

        // Filtros y paginación por defecto
        this.filters = {
            sortBy: 'licensePlate',
            sortDirection: 'ASC'
        };
        this.currentPage = 1;
        this.perPage = 20;
        this.totalPages = 1;
        this.totalResults = 0;

        // Servicio de datos (ya está global)
        this.service = window.vehicleReportsService;

        // Elementos del DOM
        this.vehiclesTableBody    = document.getElementById('vehicles-table-body');
        this.pageSizeSelect       = document.getElementById('page-size-select');
        this.paginationInfo       = document.getElementById('pagination-info-vehicles');
        this.paginationContainer  = document.querySelector('.card-footer ul.pagination');

        // Filtros del DOM
        this.filterLicensePlate       = document.getElementById('filter-license-plate');
        this.filterBrandId            = document.getElementById('filter-brand-id');
        this.filterModelId            = document.getElementById('filter-model-id');
        this.filterColorId            = document.getElementById('filter-color-id');
        this.filterYearFrom           = document.getElementById('filter-manufacture-year-from');
        this.filterYearTo             = document.getElementById('filter-manufacture-year-to');
        this.filterSeatCount          = document.getElementById('filter-seat-count');
        this.filterPassengerCount     = document.getElementById('filter-passenger-count');
        this.filterFuelTypeId         = document.getElementById('filter-fuel-type-id');
        this.filterVehicleClassId     = document.getElementById('filter-vehicle-class-id');
        this.filterCategoryId         = document.getElementById('filter-category-id');
        this.filterActive             = document.getElementById('filter-active');
        this.filterCompanyId          = document.getElementById('filter-company-id');
        this.filterDistrictId         = document.getElementById('filter-district-id');
        this.filterStatusId           = document.getElementById('filter-status-id');
        this.filterProcedureTypeId    = document.getElementById('filter-procedure-type-id');
        this.filterModalityId         = document.getElementById('filter-modality-id');
        this.vehicleSortBy            = document.getElementById('vehicle-sort-by');
        this.vehicleSortDirection     = document.getElementById('vehicle-sort-direction');
        this.applyFiltersBtn          = document.getElementById('apply-filters-btn-vehicles');
        this.clearFiltersBtn          = document.getElementById('clear-filters-btn-vehicles');
        this.refreshDataBtn           = document.getElementById('refresh-data-btn-vehicles');

        // Listeners
        this._bindEvents();

        // Cargar datos al instanciar
        this.loadReports();
    }

    _bindEvents() {
        if (this.pageSizeSelect) {
            this.pageSizeSelect.addEventListener('change', (e) => {
                this.perPage = parseInt(e.target.value);
                this.currentPage = 1;
                this.loadReports();
            });
        }
        if (this.applyFiltersBtn) {
            this.applyFiltersBtn.addEventListener('click', () => this.applyFilters());
        }
        if (this.clearFiltersBtn) {
            this.clearFiltersBtn.addEventListener('click', () => this.clearFilters());
        }
        if (this.refreshDataBtn) {
            this.refreshDataBtn.addEventListener('click', () => this.loadReports());
        }
        // Enter en los inputs = aplicar filtros
        [
            this.filterLicensePlate, this.filterBrandId, this.filterModelId, this.filterColorId,
            this.filterYearFrom, this.filterYearTo, this.filterSeatCount, this.filterPassengerCount,
            this.filterFuelTypeId, this.filterVehicleClassId, this.filterCategoryId, this.filterActive,
            this.filterCompanyId, this.filterDistrictId, this.filterStatusId, this.filterProcedureTypeId,
            this.filterModalityId
        ].forEach(input => {
            if (input) {
                input.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') this.applyFilters();
                });
            }
        });
        // Ordenamiento
        if (this.vehicleSortBy) {
            this.vehicleSortBy.addEventListener('change', () => this.applyFilters());
        }
        if (this.vehicleSortDirection) {
            this.vehicleSortDirection.addEventListener('change', () => this.applyFilters());
        }
    }

    applyFilters() {
        // Lee los valores del DOM y arma el objeto filters
        this.filters = {
            sortBy: this.vehicleSortBy?.value || 'licensePlate',
            sortDirection: this.vehicleSortDirection?.value || 'ASC'
        };

        // Mapeo rápido de inputs numéricos e IDs
        const getIntValue = (el) => el && el.value !== '' ? parseInt(el.value) : undefined;
        const getStrValue = (el) => el && el.value !== '' ? el.value.trim() : undefined;

        if (getStrValue(this.filterLicensePlate))     this.filters.licensePlate     = getStrValue(this.filterLicensePlate);
        if (getIntValue(this.filterBrandId))          this.filters.brandId          = getIntValue(this.filterBrandId);
        if (getIntValue(this.filterModelId))          this.filters.modelId          = getIntValue(this.filterModelId);
        if (getIntValue(this.filterColorId))          this.filters.colorId          = getIntValue(this.filterColorId);
        if (getIntValue(this.filterYearFrom))         this.filters.manufactureYearFrom = getIntValue(this.filterYearFrom);
        if (getIntValue(this.filterYearTo))           this.filters.manufactureYearTo = getIntValue(this.filterYearTo);
        if (getIntValue(this.filterSeatCount))        this.filters.seatCount        = getIntValue(this.filterSeatCount);
        if (getIntValue(this.filterPassengerCount))   this.filters.passengerCount   = getIntValue(this.filterPassengerCount);
        if (getIntValue(this.filterFuelTypeId))       this.filters.fuelTypeId       = getIntValue(this.filterFuelTypeId);
        if (getIntValue(this.filterVehicleClassId))   this.filters.vehicleClassId   = getIntValue(this.filterVehicleClassId);
        if (getIntValue(this.filterCategoryId))       this.filters.categoryId       = getIntValue(this.filterCategoryId);
        if (this.filterActive && this.filterActive.value !== '') {
            this.filters.active = this.filterActive.value === 'true';
        }
        if (getIntValue(this.filterCompanyId))        this.filters.companyId        = getIntValue(this.filterCompanyId);
        if (getIntValue(this.filterDistrictId))       this.filters.districtId       = getIntValue(this.filterDistrictId);
        if (getIntValue(this.filterStatusId))         this.filters.statusId         = getIntValue(this.filterStatusId);
        if (getIntValue(this.filterProcedureTypeId))  this.filters.procedureTypeId  = getIntValue(this.filterProcedureTypeId);
        if (getIntValue(this.filterModalityId))       this.filters.modalityId       = getIntValue(this.filterModalityId);

        this.currentPage = 1;
        this.loadReports();
    }

    clearFilters() {
        [
            this.filterLicensePlate, this.filterBrandId, this.filterModelId, this.filterColorId,
            this.filterYearFrom, this.filterYearTo, this.filterSeatCount, this.filterPassengerCount,
            this.filterFuelTypeId, this.filterVehicleClassId, this.filterCategoryId, this.filterActive,
            this.filterCompanyId, this.filterDistrictId, this.filterStatusId, this.filterProcedureTypeId,
            this.filterModalityId
        ].forEach(input => {
            if (input) input.value = '';
        });
        if (this.vehicleSortBy)         this.vehicleSortBy.value = 'licensePlate';
        if (this.vehicleSortDirection)  this.vehicleSortDirection.value = 'ASC';
        if (this.pageSizeSelect)        this.pageSizeSelect.value = '20';

        this.filters = {
            sortBy: 'licensePlate',
            sortDirection: 'ASC'
        };
        this.perPage = 20;
        this.currentPage = 1;
        this.loadReports();
    }

    async loadReports() {
        if (!this.service) {
            this.renderTable([]);
            return;
        }
        try {
            if (this.vehiclesTableBody) this.vehiclesTableBody.innerHTML = '<tr><td colspan="17">Cargando...</td></tr>';
            const params = {
                page: this.currentPage,
                perPage: this.perPage,
                ...this.filters
            };
            const response = await this.service.getVehicleReports(params);
            console.log('🟢 Respuesta API:', response);

            let data = [];
            if (response && response.success && response.data && Array.isArray(response.data.data)) {
                data = response.data.data;
                // Actualiza paginación
                const pagination = response.data.pagination || {};
                this.totalPages = pagination.total_pages || 1;
                this.currentPage = pagination.current_page || 1;
                this.perPage = pagination.per_page || this.perPage;
                this.totalResults = pagination.total_items || data.length;
            } else {
                this.totalPages = 1;
                this.currentPage = 1;
                this.totalResults = 0;
            }
            this.renderTable(data);
            this.updatePaginationUI();
        } catch (error) {
            if (this.vehiclesTableBody)
                this.vehiclesTableBody.innerHTML = `<tr><td colspan="17">Error cargando datos: ${error.message}</td></tr>`;
            if (this.paginationInfo) this.paginationInfo.innerText = '';
        }
    }

    renderTable(data) {
        if (!this.vehiclesTableBody) return;
        this.vehiclesTableBody.innerHTML = '';
        if (!data || data.length === 0) {
            this.vehiclesTableBody.innerHTML = '<tr><td colspan="17" class="text-center text-muted">No se encontraron vehículos.</td></tr>';
            return;
        }
        data.forEach(vehicle => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${vehicle.id || ''}</td>
                <td>${vehicle.licensePlate || ''}</td>
                <td>${vehicle.brandName || ''}</td>
                <td>${vehicle.modelName || ''}</td>
                <td>${vehicle.colorName || ''}</td>
                <td>${vehicle.manufactureYear || ''}</td>
                <td>${vehicle.seatCount || ''}</td>
                <td>${vehicle.passengerCount || ''}</td>
                <td>${vehicle.fuelTypeName || ''}</td>
                <td>${vehicle.vehicleClassName || ''}</td>
                <td>${vehicle.categoryName || ''}</td>
                <td>
                    <span class="badge bg-${vehicle.active ? 'success' : 'danger'}-lt">
                        <i class="fa-solid fa-${vehicle.active ? 'check' : 'times'}"></i>
                        ${vehicle.active ? 'Activo' : 'Inactivo'}
                    </span>
                </td>
                <td>${vehicle.companyName || ''}</td>
                <td>${vehicle.districtName || ''}</td>
                <td>${vehicle.statusName || ''}</td>
                <td>${vehicle.procedureTypeName || ''}</td>
                <td>${vehicle.modalityName || ''}</td>
            `;
            this.vehiclesTableBody.appendChild(row);
        });
    }

    updatePaginationUI() {
        if (this.paginationInfo) {
            let start = (this.currentPage - 1) * this.perPage + 1;
            let end = Math.min(this.currentPage * this.perPage, this.totalResults);
            this.paginationInfo.innerText = (this.totalResults > 0)
                ? `Mostrando ${start} a ${end} de ${this.totalResults} registros`
                : '';
        }
        this._renderPaginationButtons();
    }

    _renderPaginationButtons() {
        if (!this.paginationContainer) return;
        const ul = this.paginationContainer;
        ul.innerHTML = '';
        const totalPages = this.totalPages || 1;
        const currentPage = this.currentPage || 1;
        const delta = 2;

        const makeItem = (html, p, disabled = false, active = false, isDots = false) => {
            const li = document.createElement('li');
            li.className = `page-item${disabled ? ' disabled' : ''}${active ? ' active' : ''}${isDots ? ' disabled' : ''}`;
            if (isDots) {
                li.innerHTML = `<span class="page-link">…</span>`;
                return li;
            }
            const a = document.createElement('a');
            a.className = 'page-link';
            a.href = '#';
            a.innerHTML = html;
            if (!disabled && !active) {
                a.addEventListener('click', e => {
                    e.preventDefault();
                    this.currentPage = p;
                    this.loadReports();
                });
            }
            li.appendChild(a);
            return li;
        };

        ul.appendChild(makeItem('<i class="fas fa-chevron-left"></i>', currentPage - 1, currentPage === 1));
        let start = Math.max(1, currentPage - delta);
        let end = Math.min(totalPages, currentPage + delta);

        if (start > 1) {
            ul.appendChild(makeItem('1', 1, false, currentPage === 1));
            if (start > 2) ul.appendChild(makeItem('', null, false, false, true));
        }
        for (let p = start; p <= end; p++) {
            ul.appendChild(makeItem(p, p, false, p === currentPage));
        }
        if (end < totalPages) {
            if (end < totalPages - 1) ul.appendChild(makeItem('', null, false, false, true));
            ul.appendChild(makeItem(totalPages, totalPages, false, currentPage === totalPages));
        }
        ul.appendChild(makeItem('<i class="fas fa-chevron-right"></i>', currentPage + 1, currentPage === totalPages));
    }
}

// Hacer global para debug y para inicialización
window.VehicleReportsController = VehicleReportsController;

// Instanciar solo cuando exista la tabla en el DOM
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        if (document.getElementById('vehicles-table-body')) {
            window.vehicleReportsController = new VehicleReportsController();
        }
    }, 400);
});
