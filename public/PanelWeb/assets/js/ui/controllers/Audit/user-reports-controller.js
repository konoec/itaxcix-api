/**
 * Controlador para reportes de usuarios con filtros funcionales y paginación avanzada tipo "..."
 */
class UserReportsController {
    constructor() {
        console.log('👥 Inicializando UserReportsController...');

        // Filtros y paginación por defecto
        this.filters = {
            sortBy: 'name',
            sortDirection: 'ASC'
        };
        this.currentPage = 1;
        this.perPage = 20;
        this.totalPages = 1;
        this.totalResults = 0;

        // Servicio de datos (ya está global)
        this.service = window.userReportsService;

        // Elementos del DOM
        this.usersTableBody       = document.getElementById('users-table-body');
        this.pageSizeSelect       = document.getElementById('page-size-select');
        this.paginationInfo       = document.getElementById('pagination-info-users');
        this.paginationContainer  = document.querySelector('.card-footer ul.pagination') || document.getElementById('pagination-container');

        // Filtros
        this.filterName           = document.getElementById('filter-user-name');
        this.filterLastName       = document.getElementById('filter-user-lastname');
        this.filterDocument       = document.getElementById('filter-user-document');
        this.filterDocumentTypeId = document.getElementById('filter-user-document-type-id');
        this.filterStatusId       = document.getElementById('filter-user-status-id');
        this.filterEmail          = document.getElementById('filter-user-email');
        this.filterPhone          = document.getElementById('filter-user-phone');
        this.filterActive         = document.getElementById('filter-user-active');
        this.filterValidationStartDate = document.getElementById('filter-user-validation-start-date');
        this.filterValidationEndDate   = document.getElementById('filter-user-validation-end-date');
        this.userSortBy           = document.getElementById('user-sort-by');
        this.userSortDirection    = document.getElementById('user-sort-direction');
        this.applyFiltersBtn      = document.getElementById('apply-filters-btn-users');
        this.clearFiltersBtn      = document.getElementById('clear-filters-btn-users');
        this.refreshDataBtn       = document.getElementById('refresh-data-btn-users');

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
            this.filterName,
            this.filterLastName,
            this.filterDocument,
            this.filterEmail,
            this.filterPhone
        ].forEach(input => {
            if (input) {
                input.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') this.applyFilters();
                });
            }
        });
        // Cuando cambie "Ordenar por" o "Dirección", aplicar filtros
        if (this.userSortBy) {
            this.userSortBy.addEventListener('change', () => this.applyFilters());
        }
        if (this.userSortDirection) {
            this.userSortDirection.addEventListener('change', () => this.applyFilters());
        }
        // Paginación avanzada: los clicks los maneja _renderPaginationButtons()
    }

    applyFilters() {
        // Lee los valores de los filtros del DOM
        this.filters = {
            sortBy: this.userSortBy?.value || 'name',
            sortDirection: this.userSortDirection?.value || 'ASC'
        };

        const name = this.filterName?.value?.trim();
        if (name) this.filters.name = name;

        const lastName = this.filterLastName?.value?.trim();
        if (lastName) this.filters.lastName = lastName;

        const document = this.filterDocument?.value?.trim();
        if (document) this.filters.document = document;

        const email = this.filterEmail?.value?.trim();
        if (email) this.filters.email = email;

        const phone = this.filterPhone?.value?.trim();
        if (phone) this.filters.phone = phone;

        const documentTypeId = this.filterDocumentTypeId?.value;
        if (documentTypeId) this.filters.documentTypeId = parseInt(documentTypeId);

        const statusId = this.filterStatusId?.value;
        if (statusId) this.filters.statusId = parseInt(statusId);

        const active = this.filterActive?.value;
        if (active !== "") {
            if (active === "true" || active === "1" || active === "activo" || active === "active") {
                this.filters.active = true;
            } else if (active === "false" || active === "0" || active === "inactivo" || active === "inactive") {
                this.filters.active = false;
            }
        }

        const validationStartDate = this.filterValidationStartDate?.value;
        if (validationStartDate) this.filters.validationStartDate = validationStartDate;

        const validationEndDate = this.filterValidationEndDate?.value;
        if (validationEndDate) this.filters.validationEndDate = validationEndDate;

        this.currentPage = 1; // reset a la primera página
        this.loadReports();
    }

    clearFilters() {
        // Limpia todos los filtros visuales y de lógica
        if (this.filterName) this.filterName.value = '';
        if (this.filterLastName) this.filterLastName.value = '';
        if (this.filterDocument) this.filterDocument.value = '';
        if (this.filterEmail) this.filterEmail.value = '';
        if (this.filterPhone) this.filterPhone.value = '';
        if (this.filterDocumentTypeId) this.filterDocumentTypeId.value = '';
        if (this.filterStatusId) this.filterStatusId.value = '';
        if (this.filterActive) this.filterActive.value = '';
        if (this.filterValidationStartDate) this.filterValidationStartDate.value = '';
        if (this.filterValidationEndDate) this.filterValidationEndDate.value = '';
        if (this.userSortBy) this.userSortBy.value = 'name';
        if (this.userSortDirection) this.userSortDirection.value = 'ASC';
        if (this.pageSizeSelect) this.pageSizeSelect.value = '20';

        this.filters = {
            sortBy: 'name',
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
            // Limpia la tabla antes de cargar
            if (this.usersTableBody) this.usersTableBody.innerHTML = '<tr><td colspan="10">Cargando...</td></tr>';
            const params = {
                page: this.currentPage,
                perPage: this.perPage,
                ...this.filters
            };
            const response = await this.service.getUserReports(params);
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
            if (this.usersTableBody)
                this.usersTableBody.innerHTML = `<tr><td colspan="10">Error cargando datos: ${error.message}</td></tr>`;
            if (this.paginationInfo) this.paginationInfo.innerText = '';
        }
    }

    renderTable(data) {
        if (!this.usersTableBody) return;
        this.usersTableBody.innerHTML = '';
        if (!data || data.length === 0) {
            this.usersTableBody.innerHTML = '<tr><td colspan="10" class="text-center text-muted">No se encontraron usuarios.</td></tr>';
            return;
        }
        data.forEach(user => {
            // Badge de estado
            let badgeHtml = '';
            if (user.active === true) {
                badgeHtml = `<span class="badge bg-success-lt text-success">Activo</span>`;
            } else if (user.active === false) {
                badgeHtml = `<span class="badge bg-danger-lt text-danger">Inactivo</span>`;
            } else {
                badgeHtml = `<span class="badge bg-secondary-lt">-</span>`;
            }

            // Formatear fecha
            let fechaValid = user.validationDate
                ? new Date(user.validationDate).toLocaleDateString('es-PE')
                : 'N/A';

            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${user.id || ''}</td>
                <td>${user.name || ''}</td>
                <td>${user.lastName || ''}</td>
                <td>${user.document || ''}</td>
                <td>${user.documentType || ''}</td>
                <td>${badgeHtml}</td>
                <td>${user.email || ''}</td>
                <td>${user.phone || ''}</td>
                <td>${user.status || ''}</td>
                <td>${fechaValid}</td>
            `;
            this.usersTableBody.appendChild(row);
        });
    }

    updatePaginationUI() {
        // Info textual de paginación
        if (this.paginationInfo) {
            let start = (this.currentPage - 1) * this.perPage + 1;
            let end = Math.min(this.currentPage * this.perPage, this.totalResults);
            this.paginationInfo.innerText = (this.totalResults > 0)
                ? `Mostrando ${start} a ${end} de ${this.totalResults} usuarios`
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
}

// Hacer global para debug y para inicialización
window.UserReportsController = UserReportsController;

// Instanciar solo cuando exista la tabla en el DOM
document.addEventListener('DOMContentLoaded', () => {
    setTimeout(() => {
        if (document.getElementById('users-table-body')) {
            window.userReportsController = new UserReportsController();
        }
    }, 400);
});
