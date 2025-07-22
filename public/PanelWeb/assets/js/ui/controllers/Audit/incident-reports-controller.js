/**
 * Controlador para reportes de incidentes con filtros y paginación tipo Tabler
 */
class IncidentReportsController {
    constructor() {
        console.log('🚨 Inicializando IncidentReportsController...');

        // Estado de filtros y paginación
        this.filters = {
            sortBy: 'id',
            sortDirection: 'DESC'
        };
        this.currentPage = 1;
        this.perPage = 20;
        this.totalPages = 1;
        this.totalResults = 0;

        // Servicio global (asegúrate que esté en window.incidentReportsService)
        this.service = window.incidentReportsService;

        // Elementos del DOM
        this.incidentsTableBody   = document.getElementById('incidents-table-body');
        this.pageSizeSelect       = document.getElementById('page-size-select');
        this.paginationInfo       = document.getElementById('pagination-info-incidents');
        this.paginationContainer  = document.querySelector('.card-footer ul.pagination');

        // Filtros
        this.filterUserId     = document.getElementById('filter-user-id');
        this.filterTravelId   = document.getElementById('filter-travel-id');
        this.filterTypeId     = document.getElementById('filter-type-id');
        this.filterActive     = document.getElementById('filter-active');
        this.filterComment    = document.getElementById('filter-comment');
        this.incidentSortBy   = document.getElementById('incident-sort-by');
        this.incidentSortDirection = document.getElementById('incident-sort-direction');
        this.applyFiltersBtn  = document.getElementById('apply-filters-btn-incidents');
        this.clearFiltersBtn  = document.getElementById('clear-filters-btn-incidents');
        this.refreshDataBtn   = document.getElementById('refresh-data-btn-incidents');

        // Listeners
        this._bindEvents();

        // Cargar los reportes al instanciar
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
        // Enter en campos de texto
        [
            this.filterUserId,
            this.filterTravelId,
            this.filterTypeId,
            this.filterComment
        ].forEach(input => {
            if (input) {
                input.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') this.applyFilters();
                });
            }
        });
        // Cambios de orden
        if (this.incidentSortBy) {
            this.incidentSortBy.addEventListener('change', () => this.applyFilters());
        }
        if (this.incidentSortDirection) {
            this.incidentSortDirection.addEventListener('change', () => this.applyFilters());
        }
    }

    applyFilters() {
        // Lee todos los valores de filtros del DOM
        this.filters = {
            sortBy: this.incidentSortBy?.value || 'id',
            sortDirection: this.incidentSortDirection?.value || 'DESC'
        };
        if (this.filterUserId?.value)    this.filters.userId = parseInt(this.filterUserId.value);
        if (this.filterTravelId?.value)  this.filters.travelId = parseInt(this.filterTravelId.value);
        if (this.filterTypeId?.value)    this.filters.typeId = parseInt(this.filterTypeId.value);
        if (this.filterActive?.value)    this.filters.active = this.filterActive.value; // string: 'true', 'false', ''
        if (this.filterComment?.value)   this.filters.comment = this.filterComment.value;

        this.currentPage = 1;
        this.loadReports();
    }

    clearFilters() {
        if (this.filterUserId)        this.filterUserId.value = '';
        if (this.filterTravelId)      this.filterTravelId.value = '';
        if (this.filterTypeId)        this.filterTypeId.value = '';
        if (this.filterActive)        this.filterActive.value = '';
        if (this.filterComment)       this.filterComment.value = '';
        if (this.incidentSortBy)      this.incidentSortBy.value = 'id';
        if (this.incidentSortDirection) this.incidentSortDirection.value = 'DESC';
        if (this.pageSizeSelect)      this.pageSizeSelect.value = '20';

        this.filters = { sortBy: 'id', sortDirection: 'DESC' };
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
            if (this.incidentsTableBody) this.incidentsTableBody.innerHTML = '<tr><td colspan="7">Cargando...</td></tr>';
            const params = {
                page: this.currentPage,
                perPage: this.perPage,
                ...this.filters
            };
            const response = await this.service.getIncidentReports(params);
            let data = [];
            if (response && response.success && response.data && Array.isArray(response.data.data)) {
                data = response.data.data;
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
            if (this.incidentsTableBody)
                this.incidentsTableBody.innerHTML = `<tr><td colspan="7">Error cargando datos: ${error.message}</td></tr>`;
            if (this.paginationInfo) this.paginationInfo.innerText = '';
        }
    }

    renderTable(data) {
        if (!this.incidentsTableBody) return;
        this.incidentsTableBody.innerHTML = '';
        if (!data || data.length === 0) {
            this.incidentsTableBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No se encontraron incidentes.</td></tr>';
            return;
        }
        data.forEach(incident => {
            const status = incident.active
                ? '<span class="badge bg-success-lt"><i class="fa fa-check-circle text-success me-1"></i>Activo</span>'
                : '<span class="badge bg-danger-lt"><i class="fa fa-times-circle text-danger me-1"></i>Inactivo</span>';

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${incident.id ?? ''}</td>
                <td>${incident.userName ? `${incident.userName} <span class="text-muted">(ID: ${incident.userId})</span>` : incident.userId ?? ''}</td>
                <td>${incident.travelId ?? ''}</td>
                <td>${incident.typeName ? `${incident.typeName} <span class="text-muted">(ID: ${incident.typeId})</span>` : incident.typeId ?? ''}</td>
                <td>${status}</td>
                <td title="${incident.comment || ''}">
                    ${(incident.comment && incident.comment.length > 60) 
                        ? incident.comment.slice(0, 60) + '...' 
                        : (incident.comment || 'Sin comentario')}
                </td>
            `;
            this.incidentsTableBody.appendChild(row);
        });
    }

    updatePaginationUI() {
        // Info textual de paginación
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

    // Primera página <<
    ul.appendChild(makeItem('<i class="fas fa-angle-double-left"></i>', 1, currentPage === 1));
    // Anterior <
    ul.appendChild(makeItem('<i class="fas fa-angle-left"></i>', currentPage - 1, currentPage === 1));

    // Números y puntos suspensivos
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

    // Siguiente >
    ul.appendChild(makeItem('<i class="fas fa-angle-right"></i>', currentPage + 1, currentPage === totalPages));
    // Última página >>
    ul.appendChild(makeItem('<i class="fas fa-angle-double-right"></i>', totalPages, currentPage === totalPages));
}
}

// Exponer en window para depuración y para el inicializador
window.IncidentReportsController = IncidentReportsController;

// Instanciar solo cuando exista la tabla en el DOM
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        if (document.getElementById('incidents-table-body')) {
            window.incidentReportsController = new IncidentReportsController();
        }
    }, 400);
});
