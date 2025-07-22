/**
 * Controlador para reportes de viajes con filtros y paginación avanzada
 */
class TravelReportsController {
    constructor() {
        console.log('🚕 Inicializando TravelReportsController...');

        // Filtros y paginación por defecto
        this.filters = {
            sortBy: 'creationDate',
            sortDirection: 'DESC'
        };
        this.currentPage = 1;
        this.perPage = 20;
        this.totalPages = 1;
        this.totalResults = 0;

        // Servicio de datos (ya está global)
        this.service = window.travelReportsService;

        // Elementos del DOM
        this.incidentsTableBody  = document.getElementById('incidents-table-body');
        this.pageSizeSelect      = document.getElementById('page-size-select');
        this.paginationInfo      = document.getElementById('pagination-info-travels');
        this.paginationContainer = document.querySelector('.card-footer ul.pagination') || document.getElementById('pagination-container');

        // Filtros
        this.filterCitizenId      = document.getElementById('filter-citizen-id');
        this.filterDriverId       = document.getElementById('filter-driver-id');
        this.filterTravelStatusId = document.getElementById('filter-travel-status-id');
        this.filterStartDate      = document.getElementById('filter-start-date');
        this.filterEndDate        = document.getElementById('filter-end-date');
        this.filterOrigin         = document.getElementById('filter-origin');
        this.filterDestination    = document.getElementById('filter-destination');
        this.travelSortBy         = document.getElementById('travel-sort-by');
        this.travelSortDirection  = document.getElementById('travel-sort-direction');
        this.applyFiltersBtn      = document.getElementById('apply-filters-btn-travels');
        this.clearFiltersBtn      = document.getElementById('clear-filters-btn-travels');
        this.refreshDataBtn       = document.getElementById('refresh-data-btn-travels');

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
        // Enter en los inputs de filtros = aplicar
        [
            this.filterCitizenId,
            this.filterDriverId,
            this.filterTravelStatusId,
            this.filterStartDate,
            this.filterEndDate,
            this.filterOrigin,
            this.filterDestination
        ].forEach(input => {
            if (input) {
                input.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') this.applyFilters();
                });
            }
        });
        // Cuando cambie "Ordenar por" o "Dirección", aplicar filtros
        if (this.travelSortBy) {
            this.travelSortBy.addEventListener('change', () => this.applyFilters());
        }
        if (this.travelSortDirection) {
            this.travelSortDirection.addEventListener('change', () => this.applyFilters());
        }
        // Paginación avanzada: los clicks los maneja _renderPaginationButtons()
    }

    applyFilters() {
        // Lee los valores de los filtros del DOM
        this.filters = {
            sortBy: this.travelSortBy?.value || 'creationDate',
            sortDirection: this.travelSortDirection?.value || 'DESC'
        };

        const citizenId = this.filterCitizenId?.value;
        if (citizenId) this.filters.citizenId = parseInt(citizenId);

        const driverId = this.filterDriverId?.value;
        if (driverId) this.filters.driverId = parseInt(driverId);

        const statusId = this.filterTravelStatusId?.value;
        if (statusId) this.filters.statusId = parseInt(statusId);

        const startDate = this.filterStartDate?.value;
        if (startDate) this.filters.startDate = startDate;

        const endDate = this.filterEndDate?.value;
        if (endDate) this.filters.endDate = endDate;

        const origin = this.filterOrigin?.value;
        if (origin) this.filters.origin = origin;

        const destination = this.filterDestination?.value;
        if (destination) this.filters.destination = destination;

        this.currentPage = 1; // reset a la primera página
        this.loadReports();
    }

    clearFilters() {
        // Limpia todos los filtros visuales y de lógica
        if (this.filterCitizenId)      this.filterCitizenId.value = '';
        if (this.filterDriverId)       this.filterDriverId.value = '';
        if (this.filterTravelStatusId) this.filterTravelStatusId.value = '';
        if (this.filterStartDate)      this.filterStartDate.value = '';
        if (this.filterEndDate)        this.filterEndDate.value = '';
        if (this.filterOrigin)         this.filterOrigin.value = '';
        if (this.filterDestination)    this.filterDestination.value = '';
        if (this.travelSortBy)         this.travelSortBy.value = 'creationDate';
        if (this.travelSortDirection)  this.travelSortDirection.value = 'DESC';
        if (this.pageSizeSelect)       this.pageSizeSelect.value = '20';

        this.filters = {
            sortBy: 'creationDate',
            sortDirection: 'DESC'
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
            // Limpia la tabla antes de cargar
            if (this.incidentsTableBody) this.incidentsTableBody.innerHTML = '<tr><td colspan="9">Cargando...</td></tr>';
            const params = {
                page: this.currentPage,
                perPage: this.perPage,
                ...this.filters
            };
            const response = await this.service.getTravelReports(params);
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
            if (this.incidentsTableBody)
                this.incidentsTableBody.innerHTML = `<tr><td colspan="9">Error cargando datos: ${error.message}</td></tr>`;
            if (this.paginationInfo) this.paginationInfo.innerText = '';
        }
    }

    renderTable(data) {
        if (!this.incidentsTableBody) return;
        this.incidentsTableBody.innerHTML = '';
        if (!data || data.length === 0) {
            this.incidentsTableBody.innerHTML = '<tr><td colspan="9" class="text-center text-muted">No se encontraron viajes.</td></tr>';
            return;
        }
        data.forEach(travel => {
            // Badge color según estado
            let badgeHtml = this.getStatusBadge(travel.status);

            // Fechas formateadas
            let formattedStartDate = travel.startDate ? this.formatDate(travel.startDate) : '';
            let formattedEndDate   = travel.endDate ? this.formatDate(travel.endDate) : '';
            let formattedCreationDate = travel.creationDate ? this.formatDate(travel.creationDate) : '';

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${travel.id || ''}</td>
                <td>${travel.citizenName || ''}</td>
                <td>${travel.driverName || ''}</td>
                <td title="${travel.origin || ''}">${this.truncateText(travel.origin, 30)}</td>
                <td title="${travel.destination || ''}">${this.truncateText(travel.destination, 30)}</td>
                <td>${formattedStartDate}</td>
                <td>${formattedEndDate}</td>
                <td>${formattedCreationDate}</td>
                <td>${badgeHtml}</td>
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
                ? `Mostrando ${start} a ${end} de ${this.totalResults} viajes`
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
        const delta = 2; // Cantidad de botones antes y después

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

        // Prev arrow
        ul.appendChild(makeItem('<i class="fas fa-chevron-left"></i>', currentPage - 1, currentPage === 1));

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

        // Next arrow
        ul.appendChild(makeItem('<i class="fas fa-chevron-right"></i>', currentPage + 1, currentPage === totalPages));
    }

    // Utilidades de renderizado y formato
    getStatusBadge(status) {
    if (!status)
        return `<span class="badge bg-secondary-lt">
            <span style="display:inline-block;width:10px;height:10px;background:#adb5bd;border-radius:50%;margin-right:6px;"></span>
            Sin estado
        </span>`;

    const st = status.toString().toUpperCase();

    if (st.includes('FINALIZADO') || st.includes('COMPLETADO'))
        return `<span class="badge bg-success-lt text-success">
            <span style="display:inline-block;width:10px;height:10px;background:#51cf66;border-radius:50%;margin-right:6px;"></span>
            Finalizado
        </span>`;

    if (st.includes('CANCELADO'))
        return `<span class="badge bg-danger-lt text-danger">
            <span style="display:inline-block;width:10px;height:10px;background:#ff6b6b;border-radius:50%;margin-right:6px;"></span>
            Cancelado
        </span>`;

    if (st.includes('PROGRESO'))
        return `<span class="badge bg-warning-lt text-warning">
            <span style="display:inline-block;width:10px;height:10px;background:#ffe066;border-radius:50%;margin-right:6px;"></span>
            En progreso
        </span>`;

    if (st.includes('ACEPTADO'))
        return `<span class="badge bg-info-lt text-info">
            <span style="display:inline-block;width:10px;height:10px;background:#66d9e8;border-radius:50%;margin-right:6px;"></span>
            Aceptado
        </span>`;

    if (st.includes('SOLICITADO'))
        return `<span class="badge bg-warning-lt text-warning">
            <span style="display:inline-block;width:10px;height:10px;background:#fcc419;border-radius:50%;margin-right:6px;"></span>
            Solicitado
        </span>`;

    if (st.includes('RECHAZADO'))
        return `<span class="badge bg-danger-lt text-danger">
            <span style="display:inline-block;width:10px;height:10px;background:#fa5252;border-radius:50%;margin-right:6px;"></span>
            Rechazado
        </span>`;

    return `<span class="badge bg-secondary-lt">
        <span style="display:inline-block;width:10px;height:10px;background:#adb5bd;border-radius:50%;margin-right:6px;"></span>
        ${status}
    </span>`;
}


    truncateText(text, max = 40) {
        if (!text) return '';
        if (text.length > max) return text.slice(0, max) + '…';
        return text;
    }

    formatDate(dateStr) {
        if (!dateStr) return '';
        // Soporta tanto "2024-06-01 08:00:00" como ISO
        const dateObj = new Date(dateStr.replace(' ', 'T'));
        if (isNaN(dateObj.getTime())) return dateStr;
        return dateObj.toLocaleString('es-PE', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
}

// Hacer global para debug y para inicialización
window.TravelReportsController = TravelReportsController;

// Instanciar solo cuando exista la tabla en el DOM
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        if (document.getElementById('incidents-table-body')) {
            window.travelReportsController = new TravelReportsController();
        }
    }, 400);
});
