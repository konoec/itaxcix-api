/**
 * Controlador para reportes de calificaciones con filtros funcionales y paginación avanzada tipo "..."
 */
class RatingReportsController {
    constructor() {
        console.log('⭐ Inicializando RatingReportsController...');

        // Filtros y paginación por defecto
        this.filters = {
            sortBy: 'id',
            sortDirection: 'DESC'
        };
        this.currentPage = 1;
        this.perPage = 20;
        this.totalPages = 1;
        this.totalResults = 0;

        // Servicio de datos (ya está global)
        this.service = window.ratingReportsService;

        // Elementos del DOM
        this.incidentsTableBody = document.getElementById('incidents-table-body');
        this.pageSizeSelect     = document.getElementById('page-size-select');
        this.currentPageSpan    = document.getElementById('pagination-current-page');
        this.paginationInfo     = document.getElementById('pagination-info-ratings');
        this.paginationContainer = document.querySelector('.card-footer ul.pagination');

        // Filtros
        this.filterRaterId      = document.getElementById('filter-rater-id');
        this.filterRatedId      = document.getElementById('filter-rated-id');
        this.filterTravelId     = document.getElementById('filter-travel-id');
        this.filterMinScore     = document.getElementById('filter-min-score');
        this.filterMaxScore     = document.getElementById('filter-max-score');
        this.filterComment      = document.getElementById('filter-comment');
        this.ratingSortBy       = document.getElementById('rating-sort-by');
        this.ratingSortDirection= document.getElementById('rating-sort-direction');
        this.applyFiltersBtn    = document.getElementById('apply-filters-btn-ratings');
        this.clearFiltersBtn    = document.getElementById('clear-filters-btn-ratings');
        this.refreshDataBtn     = document.getElementById('refresh-data-btn-ratings');

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
            this.filterRaterId,
            this.filterRatedId,
            this.filterTravelId,
            this.filterMinScore,
            this.filterMaxScore,
            this.filterComment
        ].forEach(input => {
            if (input) {
                input.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') this.applyFilters();
                });
            }
        });
        // Cuando cambie "Ordenar por" o "Dirección", aplicar filtros
        if (this.ratingSortBy) {
            this.ratingSortBy.addEventListener('change', () => this.applyFilters());
        }
        if (this.ratingSortDirection) {
            this.ratingSortDirection.addEventListener('change', () => this.applyFilters());
        }
        // Paginación avanzada: los clicks los maneja _renderPaginationButtons()
    }

    applyFilters() {
        // Lee los valores de los filtros del DOM
        this.filters = {
            sortBy: this.ratingSortBy?.value || 'id',
            sortDirection: this.ratingSortDirection?.value || 'DESC'
        };

        const raterId = this.filterRaterId?.value;
        if (raterId) this.filters.raterId = parseInt(raterId);

        const ratedId = this.filterRatedId?.value;
        if (ratedId) this.filters.ratedId = parseInt(ratedId);

        const travelId = this.filterTravelId?.value;
        if (travelId) this.filters.travelId = parseInt(travelId);

        const minScore = this.filterMinScore?.value;
        if (minScore) this.filters.minScore = parseInt(minScore);

        const maxScore = this.filterMaxScore?.value;
        if (maxScore) this.filters.maxScore = parseInt(maxScore);

        const comment = this.filterComment?.value;
        if (comment) this.filters.comment = comment;

        this.currentPage = 1; // reset a la primera página
        this.loadReports();
    }

    clearFilters() {
        // Limpia todos los filtros visuales y de lógica
        if (this.filterRaterId)   this.filterRaterId.value = '';
        if (this.filterRatedId)   this.filterRatedId.value = '';
        if (this.filterTravelId)  this.filterTravelId.value = '';
        if (this.filterMinScore)  this.filterMinScore.value = '';
        if (this.filterMaxScore)  this.filterMaxScore.value = '';
        if (this.filterComment)   this.filterComment.value = '';
        if (this.ratingSortBy)    this.ratingSortBy.value = 'id';
        if (this.ratingSortDirection) this.ratingSortDirection.value = 'DESC';
        if (this.pageSizeSelect)  this.pageSizeSelect.value = '20';

        this.filters = {
            sortBy: 'id',
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
            if (this.incidentsTableBody) this.incidentsTableBody.innerHTML = '<tr><td colspan="8">Cargando...</td></tr>';
            const params = {
                page: this.currentPage,
                perPage: this.perPage,
                ...this.filters
            };
            const response = await this.service.getRatingReports(params);
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
                this.incidentsTableBody.innerHTML = `<tr><td colspan="8">Error cargando datos: ${error.message}</td></tr>`;
            if (this.paginationInfo) this.paginationInfo.innerText = '';
        }
    }

    renderTable(data) {
    if (!this.incidentsTableBody) return;
    this.incidentsTableBody.innerHTML = '';
    if (!data || data.length === 0) {
        this.incidentsTableBody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No se encontraron calificaciones.</td></tr>';
        return;
    }
    data.forEach(rating => {
        // Badge color según score
        let badgeColor = this.getScoreColor(rating.score);

        // Fila con estrella dorada y número compactos
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${rating.id || ''}</td>
            <td>${rating.raterId || ''}</td>
            <td>${rating.raterName || 'N/A'}</td>
            <td>${rating.ratedId || ''}</td>
            <td>${rating.ratedName || 'N/A'}</td>
            <td>${rating.travelId || ''}</td>
            <td>
                <span class="badge bg-${badgeColor}-lt d-inline-flex align-items-center px-2 py-1" style="gap:2px;min-width:54px;">
                    <i class="fa-solid fa-star" style="color:#ffc107; font-size:1em; margin-right:2px;"></i>
                    <span class="fw-bold text-dark" style="font-size:1em; margin-left:0;">${rating.score ?? ''}</span>
                </span>
            </td>
            <td title="${rating.comment || ''}">
                ${(rating.comment && rating.comment.length > 60) 
                    ? rating.comment.slice(0, 60) + '...' 
                    : (rating.comment || 'Sin comentario')}
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

    getScoreColor(score) {
        if (score >= 4.5) return 'success';
        if (score >= 3.5) return 'warning';
        if (score >= 2.5) return 'secondary';
        return 'danger';
    }
}

// Hacer global para debug y para inicialización
window.RatingReportsController = RatingReportsController;

// Instanciar solo cuando exista la tabla en el DOM
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        if (document.getElementById('incidents-table-body')) {
            window.ratingReportsController = new RatingReportsController();
        }
    }, 400);
});
