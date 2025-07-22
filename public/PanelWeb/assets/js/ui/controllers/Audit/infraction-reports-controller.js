/**
 * Controlador para reportes de infracciones con filtros y paginación.
 */
console.log('📢 infraction-reports-controller.js cargado y ejecutándose');

class InfractionReportsController {
  constructor() {
    console.log('⚠️ Inicializando InfractionReportsController...');

    // Filtros y paginación por defecto
    this.filters = {
      sortBy: 'id',
      sortDirection: 'DESC',
    };
    this.currentPage = 1;
    this.perPage = 20;
    this.totalPages = 1;
    this.totalResults = 0;

    // Servicio de datos (global)
    this.service = window.InfractionReportsService;

    // Elementos del DOM
    this.infractionsTableBody = document.getElementById('infractions-table-body');
    this.pageSizeSelect = document.getElementById('page-size-select');
    this.paginationInfo = document.getElementById('pagination-info-infractions');
    this.paginationContainer = document.getElementById('pagination-container');

    this.filterUserId = document.getElementById('filter-infraction-user-id');
    this.filterSeverityId = document.getElementById('filter-severity-id');
    this.filterStatusId = document.getElementById('filter-infraction-status-id');
    this.filterDateFrom = document.getElementById('filter-date-from');
    this.filterDateTo = document.getElementById('filter-date-to');
    this.filterDescription = document.getElementById('filter-infraction-description');
    this.sortBySelect = document.getElementById('infraction-sort-by');
    this.sortDirectionSelect = document.getElementById('infraction-sort-direction');

    this.applyFiltersBtn = document.getElementById('apply-filters-btn-infractions');
    this.clearFiltersBtn = document.getElementById('clear-filters-btn-infractions');
    this.refreshDataBtn = document.getElementById('refresh-data-btn-infractions');

    // Estados visuales opcionales para carga y error (agrega si tienes en el HTML)
    this.loadingRow = `<tr><td colspan="9" class="text-center">Cargando...</td></tr>`;
    this.errorRow = (msg) => `<tr><td colspan="9" class="text-center text-danger">Error: ${msg}</td></tr>`;
    this.emptyRow = `<tr><td colspan="9" class="text-center text-muted">No se encontraron infracciones.</td></tr>`;

    // Ligar eventos
    this._bindEvents();

    // Cargar datos inicial
    this.loadReports();
  }

  _bindEvents() {
    if (this.pageSizeSelect) {
      this.pageSizeSelect.addEventListener('change', (e) => {
        this.perPage = parseInt(e.target.value);
        console.log(`🔢 Cambió elementos por página: ${this.perPage}`);
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

    [
      this.filterUserId,
      this.filterSeverityId,
      this.filterStatusId,
      this.filterDateFrom,
      this.filterDateTo,
      this.filterDescription,
    ].forEach((input) => {
      if (input) {
        input.addEventListener('change', () => this.applyFilters());
        input.addEventListener('keypress', (e) => {
          if (e.key === 'Enter') this.applyFilters();
        });
      }
    });

    if (this.sortBySelect) {
      this.sortBySelect.addEventListener('change', () => this.applyFilters());
    }
    if (this.sortDirectionSelect) {
      this.sortDirectionSelect.addEventListener('change', () => this.applyFilters());
    }
  }

  applyFilters() {
    this.filters = {
      sortBy: this.sortBySelect?.value || 'id',
      sortDirection: this.sortDirectionSelect?.value || 'DESC',
    };

    const userId = this.filterUserId?.value;
    if (userId && userId.trim() !== '') {
      this.filters.userId = parseInt(userId);
    } else {
      delete this.filters.userId;
    }

    const severityId = this.filterSeverityId?.value;
    if (severityId && severityId.trim() !== '') {
      this.filters.severityId = parseInt(severityId);
    } else {
      delete this.filters.severityId;
    }

    const statusId = this.filterStatusId?.value;
    if (statusId && statusId.trim() !== '') {
      this.filters.statusId = parseInt(statusId);
    } else {
      delete this.filters.statusId;
    }

    const dateFrom = this.filterDateFrom?.value;
    if (dateFrom && dateFrom.trim() !== '') {
      this.filters.dateFrom = dateFrom;
    } else {
      delete this.filters.dateFrom;
    }

    const dateTo = this.filterDateTo?.value;
    if (dateTo && dateTo.trim() !== '') {
      this.filters.dateTo = dateTo;
    } else {
      delete this.filters.dateTo;
    }

    const description = this.filterDescription?.value;
    if (description && description.trim() !== '') {
      this.filters.description = description.trim();
    } else {
      delete this.filters.description;
    }

    this.currentPage = 1;

    console.log('📋 Aplicando filtros:', this.filters);
    this.loadReports();
  }

  clearFilters() {
    if (this.filterUserId) this.filterUserId.value = '';
    if (this.filterSeverityId) this.filterSeverityId.value = '';
    if (this.filterStatusId) this.filterStatusId.value = '';
    if (this.filterDateFrom) this.filterDateFrom.value = '';
    if (this.filterDateTo) this.filterDateTo.value = '';
    if (this.filterDescription) this.filterDescription.value = '';
    if (this.sortBySelect) this.sortBySelect.value = 'id';
    if (this.sortDirectionSelect) this.sortDirectionSelect.value = 'DESC';
    if (this.pageSizeSelect) this.pageSizeSelect.value = '20';

    this.filters = {
      sortBy: 'id',
      sortDirection: 'DESC',
    };
    this.perPage = 20;
    this.currentPage = 1;

    console.log('🧹 Filtros limpiados, recargando datos...');
    this.loadReports();
  }

  async loadReports() {
    if (!this.service) {
      console.warn('⚠️ Servicio no disponible, no se cargarán datos');
      this.renderTable([]);
      return;
    }
    try {
      if (this.infractionsTableBody)
        this.infractionsTableBody.innerHTML = this.loadingRow;

      const params = {
        page: this.currentPage,
        perPage: this.perPage,
        ...this.filters,
      };

      console.log('🚀 Cargando reportes con parámetros:', params);
      const response = await this.service.getInfractionReports(params);
      console.log('🟢 Respuesta API:', response);

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
      console.error('❌ Error en loadReports:', error);
      if (this.infractionsTableBody)
        this.infractionsTableBody.innerHTML = this.errorRow(error.message);
      if (this.paginationInfo) this.paginationInfo.innerText = '';
    }
  }

  renderTable(data) {
    if (!this.infractionsTableBody) return;
    this.infractionsTableBody.innerHTML = '';

    if (!data || data.length === 0) {
      this.infractionsTableBody.innerHTML = this.emptyRow;
      return;
    }

    data.forEach((infraction) => {
      const severityBadge = `<span class="badge bg-${this.getSeverityColor(infraction.severityName)}-lt">${infraction.severityName || 'Sin severidad'}</span>`;
      const statusBadge = `<span class="badge bg-${this.getStatusColor(infraction.statusName)}-lt">${infraction.statusName || 'Sin estado'}</span>`;

      const formattedDate = infraction.date ? new Date(infraction.date).toLocaleString('es-PE') : 'N/A';

      const row = document.createElement('tr');
      row.innerHTML = `
                <td>${infraction.id || ''}</td>
                <td>${infraction.userId || ''}</td>
                <td>${infraction.userName || 'Sin nombre'}</td>
                <td>${infraction.severityId || ''}</td>
                <td>${severityBadge}</td>
                <td>${infraction.statusId || ''}</td>
                <td>${statusBadge}</td>
                <td>${formattedDate}</td>
                <td title="${infraction.description || ''}">
                    ${(infraction.description && infraction.description.length > 60)
          ? infraction.description.slice(0, 60) + '...'
          : infraction.description || 'Sin descripción'}
                </td>
            `;
      this.infractionsTableBody.appendChild(row);
    });
  }

  getSeverityColor(severity) {
    if (!severity) return 'secondary';
    const s = severity.toLowerCase();
    if (s.includes('leve')) return 'success';
    if (s.includes('moderada')) return 'warning';
    if (s.includes('grave')) return 'danger';
    return 'secondary';
  }

  getStatusColor(status) {
    if (!status) return 'secondary';
    const s = status.toLowerCase();
    if (s.includes('pendiente')) return 'warning';
    if (s.includes('resuelta') || s.includes('finalizada')) return 'success';
    if (s.includes('anulada')) return 'danger';
    return 'secondary';
  }

  updatePaginationUI() {
    if (this.paginationInfo) {
      let start = (this.currentPage - 1) * this.perPage + 1;
      let end = Math.min(this.currentPage * this.perPage, this.totalResults);
      this.paginationInfo.innerText = this.totalResults > 0
        ? `Mostrando ${start} a ${end} de ${this.totalResults} infracciones`
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
      a.addEventListener('click', (e) => {
        e.preventDefault();
        this.currentPage = p;
        this.loadReports();
      });
    }
    li.appendChild(a);
    return li;
  };

  // << Primera página
  ul.appendChild(makeItem('<i class="fas fa-angle-double-left"></i>', 1, currentPage === 1));
  // < Anterior
  ul.appendChild(makeItem('<i class="fas fa-angle-left"></i>', currentPage - 1, currentPage === 1));

  // Números
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

  // > Siguiente
  ul.appendChild(makeItem('<i class="fas fa-angle-right"></i>', currentPage + 1, currentPage === totalPages));
  // >> Última página
  ul.appendChild(makeItem('<i class="fas fa-angle-double-right"></i>', totalPages, currentPage === totalPages));
}

}

// Hacer global para debug e inicialización
window.InfractionReportsController = new InfractionReportsController();
