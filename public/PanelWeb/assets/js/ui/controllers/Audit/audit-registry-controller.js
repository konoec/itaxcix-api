class AuditRegistryController {
  constructor() {
    // Configuración
    this.currentPage = 1;
    this.perPage = 20;
    this.totalPages = 1;
    this.totalItems = 0;
    this.currentData = [];
    this.sortBy = 'date';
    this.sortDirection = 'DESC';
    this.isLoading = false;
    this.filters = {
      affectedTable: '',
      operation: '',
      systemUser: '',
      dateFrom: '',
      dateTo: ''
    };

    // Elementos del DOM
    this.initializeElements();
    this.initializeEventListeners();
    this.loadAuditData();
  }

  initializeElements() {
    // Filtros
    this.filterTable = document.getElementById('filter-table');
    this.filterOperation = document.getElementById('filter-operation');
    this.filterUser = document.getElementById('filter-user');
    this.filterDateFrom = document.getElementById('filter-date-from');
    this.filterDateTo = document.getElementById('filter-date-to');
    this.clearFiltersBtn = document.getElementById('clear-filters-btn');
    this.applyFiltersBtn = document.getElementById('apply-filters-btn');
    this.clearFiltersEmptyBtn = document.getElementById('clear-filters-empty');
    // Tabla y paginación
    this.tableBody = document.getElementById('audit-table-body');
    this.tableLoading = document.getElementById('table-loading');
    this.tableEmpty = document.getElementById('table-empty');
    this.resultsCount = document.getElementById('results-count');
    this.sortBySelect = document.getElementById('sort-by');
    this.sortDirectionBtn = document.getElementById('sort-direction');
    this.perPageSelect = document.getElementById('per-page');
    this.paginationContainer = document.getElementById('pagination-container');
    this.paginationInfo = document.getElementById('pagination-info-text');
    this.paginationPages = document.getElementById('pagination-pages');
    this.refreshBtn = document.getElementById('refresh-audit-btn');
    this.downloadBtn = document.getElementById('download-audit-btn');
    // Modal detalles
    this.detailsModal = document.getElementById('audit-details-modal');
    this.closeDetailsBtn = document.getElementById('close-audit-details');
    this.closeDetailBtn = document.getElementById('close-detail-btn');
  }

  initializeEventListeners() {
    // Filtros
    this.clearFiltersBtn?.addEventListener('click', () => this.clearFilters());
    this.applyFiltersBtn?.addEventListener('click', () => this.applyFilters());
    this.clearFiltersEmptyBtn?.addEventListener('click', () => this.clearFilters());
    [this.filterUser, this.filterDateFrom, this.filterDateTo].forEach(input => {
      input?.addEventListener('keypress', e => { if (e.key === 'Enter') this.applyFilters(); });
    });
    this.sortBySelect?.addEventListener('change', () => this.updateSort());
    this.sortDirectionBtn?.addEventListener('click', () => this.toggleSortDirection());
    this.perPageSelect?.addEventListener('change', () => this.updatePerPage());
    // Botones paginación con Tabler moderno
    this.paginationPages?.addEventListener('click', e => {
      if (e.target.classList.contains('page-link')) {
        e.preventDefault();
        const page = parseInt(e.target.dataset.page);
        if (page && page !== this.currentPage) this.goToPage(page);
      }
    });
    // Botones acción
    this.refreshBtn?.addEventListener('click', () => this.refreshData());
    this.downloadBtn?.addEventListener('click', () => this.downloadData());
    this.closeDetailsBtn?.addEventListener('click', () => this.closeDetailsModal());
    this.closeDetailBtn?.addEventListener('click', () => this.closeDetailsModal());
    this.detailsModal?.addEventListener('click', e => {
      if (e.target === this.detailsModal) this.closeDetailsModal();
    });
  }

  async loadAuditData() {
    if (this.isLoading) return;
    this.showLoading();
    this.isLoading = true;
    try {
      // Mapea los filtros
      const params = {
        page: this.currentPage,
        perPage: this.perPage,
        sortBy: this.sortBy,
        sortDirection: this.sortDirection,
        ...this.filters
      };
      const res = await window.auditService.getAuditLogs(params);

      // Estructura de respuesta compatible con tu API
      this.currentData = res?.data?.data || [];
      this.totalItems = res?.data?.pagination?.total_items || 0;
      this.totalPages = res?.data?.pagination?.total_pages || 1;
      this.currentPage = res?.data?.pagination?.current_page || 1;

      this.renderTable();
      this.updatePagination();
      this.updateResultsCount();
    } catch (err) {
      this.showApiError(`Error al cargar registros de auditoría: ${err.message}`);
    } finally {
      this.hideLoading();
      this.isLoading = false;
    }
  }

  renderTable() {
    if (!this.tableBody) return;
    if (this.currentData.length === 0) return this.showEmptyState();
    this.hideEmptyState();
    this.tableBody.innerHTML = this.currentData.map(record => this.createTableRow(record)).join('');
  }

  createTableRow(record) {
    const operationClass = {
      'INSERT': 'bg-success-lt text-success',
      'UPDATE': 'bg-warning-lt text-warning',
      'DELETE': 'bg-danger-lt text-danger'
    }[record.operation?.toUpperCase()] || 'bg-secondary-lt text-secondary';
    return `
      <tr>
        <td>${record.id}</td>
        <td>${this.formatDate(record.date)}</td>
        <td><span class="badge bg-blue-lt">${record.affectedTable || '-'}</span></td>
        <td>
          <span class="badge ${operationClass}">${record.operation || '-'}</span>
        </td>
        <td>${record.systemUser || '-'}</td>
        <td>
          <button class="btn btn-sm btn-outline-info" title="Ver detalles" onclick="window.auditController?.showDetails(${record.id})">
            <i class="fas fa-eye"></i>
          </button>
        </td>
      </tr>
    `;
  }

  // Formatea fecha usando locale español
  formatDate(dateString) {
    if (!dateString) return '-';
    try {
      return new Date(dateString).toLocaleString('es-PE', {
        year: 'numeric', month: '2-digit', day: '2-digit',
        hour: '2-digit', minute: '2-digit'
      });
    } catch { return dateString; }
  }

  // ---- PAGINACIÓN MODERNA tipo Tabler ----
  updatePagination() {
    if (this.paginationInfo) {
      this.paginationInfo.textContent = `Página ${this.currentPage} de ${this.totalPages}`;
    }
    this.generatePaginationPages();
  }

  generatePaginationPages() {
    if (!this.paginationPages) return;

    const totalPages = this.totalPages;
    const currentPage = this.currentPage;
    const maxPagesToShow = 5;
    let pages = [];

    // Botón Primero
    pages.push(`
        <li class="page-item${currentPage === 1 ? ' disabled' : ''}">
            <a class="page-link" href="#" data-page="1" aria-label="Primero"><i class="fas fa-angle-double-left"></i></a>
        </li>
    `);

    // Botón Anterior
    pages.push(`
        <li class="page-item${currentPage === 1 ? ' disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage - 1}" aria-label="Anterior"><i class="fas fa-angle-left"></i></a>
        </li>
    `);

    // Lógica para mostrar páginas y puntos suspensivos
    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, currentPage + 2);

    if (startPage > 1) {
        pages.push(`<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`);
        if (startPage > 2) {
            pages.push(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
        }
    }

    for (let i = startPage; i <= endPage; i++) {
        pages.push(`
            <li class="page-item${i === currentPage ? ' active' : ''}">
                <a class="page-link" href="#" data-page="${i}">${i}</a>
            </li>
        `);
    }

    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            pages.push(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
        }
        pages.push(`<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`);
    }

    // Botón Siguiente
    pages.push(`
        <li class="page-item${currentPage === totalPages ? ' disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage + 1}" aria-label="Siguiente"><i class="fas fa-angle-right"></i></a>
        </li>
    `);

    // Botón Último
    pages.push(`
        <li class="page-item${currentPage === totalPages ? ' disabled' : ''}">
            <a class="page-link" href="#" data-page="${totalPages}" aria-label="Último"><i class="fas fa-angle-double-right"></i></a>
        </li>
    `);

    this.paginationPages.innerHTML = pages.join('');

    // Listeners para cada link
    this.paginationPages.querySelectorAll('a.page-link[data-page]').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const page = parseInt(e.currentTarget.getAttribute('data-page'));
            if (!isNaN(page) && page >= 1 && page <= totalPages && page !== currentPage) {
                this.goToPage(page);
            }
        });
    });
}


  updateResultsCount() {
    if (this.resultsCount) {
      const start = (this.currentPage - 1) * this.perPage + 1;
      const end = Math.min(this.currentPage * this.perPage, this.totalItems);
      this.resultsCount.textContent = this.totalItems === 0
        ? 'No hay registros para mostrar'
        : `Mostrando ${start}-${end} de ${this.totalItems} registros`;
    }
  }

  goToPage(page) {
    if (page < 1 || page > this.totalPages || page === this.currentPage) return;
    this.currentPage = page;
    this.loadAuditData();
  }

  // ------------------- ACCIONES -------------------
  applyFilters() {
    this.filters.affectedTable = this.filterTable?.value || '';
    this.filters.operation = this.filterOperation?.value || '';
    this.filters.systemUser = this.filterUser?.value || '';
    this.filters.dateFrom = this.filterDateFrom?.value || '';
    this.filters.dateTo = this.filterDateTo?.value || '';
    this.currentPage = 1;
    this.loadAuditData();
  }

  clearFilters() {
    if (this.filterTable) this.filterTable.value = '';
    if (this.filterOperation) this.filterOperation.value = '';
    if (this.filterUser) this.filterUser.value = '';
    if (this.filterDateFrom) this.filterDateFrom.value = '';
    if (this.filterDateTo) this.filterDateTo.value = '';
    this.filters = {
      affectedTable: '',
      operation: '',
      systemUser: '',
      dateFrom: '',
      dateTo: ''
    };
    this.currentPage = 1;
    this.loadAuditData();
  }

  updateSort() {
    this.sortBy = this.sortBySelect?.value || 'date';
    this.currentPage = 1;
    this.loadAuditData();
  }
  toggleSortDirection() {
    this.sortDirection = this.sortDirection === 'ASC' ? 'DESC' : 'ASC';
    this.currentPage = 1;
    this.loadAuditData();
  }
  updatePerPage() {
    this.perPage = parseInt(this.perPageSelect?.value) || 20;
    this.currentPage = 1;
    this.loadAuditData();
  }
  refreshData() {
    this.loadAuditData();
  }
  showLoading() {
    this.tableLoading && (this.tableLoading.style.display = 'flex');
    this.tableBody && (this.tableBody.style.display = 'none');
    this.hideEmptyState();
  }
  hideLoading() {
    this.tableLoading && (this.tableLoading.style.display = 'none');
    this.tableBody && (this.tableBody.style.display = '');
  }
  showEmptyState() {
    this.tableEmpty && (this.tableEmpty.style.display = 'flex');
    this.tableBody && (this.tableBody.style.display = 'none');
    this.paginationContainer && (this.paginationContainer.style.display = 'none');
  }
  hideEmptyState() {
    this.tableEmpty && (this.tableEmpty.style.display = 'none');
    this.paginationContainer && (this.paginationContainer.style.display = 'flex');
  }
  showApiError(message) {
    this.currentData = [];
    this.totalItems = 0;
    this.totalPages = 0;
    this.currentPage = 1;
    this.showErrorState(message);
    this.showError(message);
    this.updatePagination();
    this.updateResultsCount();
  }
  showErrorState(message) {
    if (this.tableEmpty) {
      const emptyIcon = this.tableEmpty.querySelector('.empty-icon i');
      const emptyTitle = this.tableEmpty.querySelector('h3');
      const emptyDescription = this.tableEmpty.querySelector('p');
      const emptyButton = this.tableEmpty.querySelector('button');
      if (emptyIcon) emptyIcon.className = 'fas fa-exclamation-triangle';
      if (emptyTitle) emptyTitle.textContent = 'Error de conexión con la API';
      if (emptyDescription) emptyDescription.textContent = message;
      if (emptyButton) {
        emptyButton.textContent = 'Reintentar';
        emptyButton.onclick = () => this.refreshData();
      }
      this.tableEmpty.style.display = 'flex';
    }
    this.tableBody && (this.tableBody.style.display = 'none');
    this.paginationContainer && (this.paginationContainer.style.display = 'none');
  }
  showError(message) {
    window.GlobalToast?.show(message, 'error');
    // alert fallback solo para desarrollo
    // alert(message);
  }
  // ------------------- DETALLE AUDITORÍA -------------------
  async showDetails(recordId) {
    try {
      let record = this.currentData.find(r => r.id === recordId);
      if (!record) {
        const response = await window.auditService.getAuditLogById(recordId);
        record = response?.data || response;
      }
      if (!record) return this.showError('Registro de auditoría no encontrado');
      this.populateDetailsModal(record);
      if (window.bootstrap && typeof window.bootstrap.Modal === 'function') {
        if (!this._bsDetailsModal) this._bsDetailsModal = new window.bootstrap.Modal(this.detailsModal);
        this._bsDetailsModal.show();
      } else if (typeof $ !== 'undefined' && $.fn.modal) {
        $(this.detailsModal).modal('show');
      } else {
        this.detailsModal.classList.add('show');
        this.detailsModal.style.display = 'block';
      }
    } catch (err) {
      this.showError('Error al cargar detalles del registro: ' + err.message);
    }
  }
  populateDetailsModal(record) {
  // — Detalles básicos —
  document.getElementById('detail-id').textContent        = record.id || '-';
  document.getElementById('detail-date').textContent      = this.formatDate(record.date) || '-';
  document.getElementById('detail-table').textContent     = record.affectedTable || '-';
  document.getElementById('detail-operation').textContent = record.operation || '-';
  document.getElementById('detail-user').textContent      = record.systemUser || '-';

  // — Badge de operación —
  const opBadge = document.getElementById('detail-operation');
  if (opBadge && record.operation) {
    const cls = {
      'INSERT': 'badge bg-success-lt text-success',
      'UPDATE': 'badge bg-warning-lt text-warning',
      'DELETE': 'badge bg-danger-lt text-danger'
    }[record.operation.toUpperCase()] || 'badge bg-secondary-lt text-secondary';
    opBadge.className = cls;
  }

  // — Secciones de datos Anteriores y Nuevos —
  const prevSection = document.getElementById('previous-data-section');
  const newSection  = document.getElementById('new-data-section');
  const prevElem    = document.getElementById('previous-data-content');
  const newElem     = document.getElementById('new-data-content');

  const prevData = record.previousData || {};
  const newData  = record.newData      || {};

  // Rellena o deja vacío
  prevElem.textContent = Object.keys(prevData).length
    ? JSON.stringify(prevData, null, 2)
    : '';
  newElem.textContent  = Object.keys(newData).length
    ? JSON.stringify(newData, null, 2)
    : '';

  // Saber cuántas columnas mostrar
  const hasPrev = Object.keys(prevData).length > 0;
  const hasNew  = Object.keys(newData).length  > 0;

  // Ajustar ancho de columnas y visibilidad
  const setCol = (el, size) => {
    if (!el) return;
    el.classList.remove('col-6', 'col-12', 'd-none');
    if (size === 'none') return el.classList.add('d-none');
    el.classList.add(size);
  };

  if (hasPrev && hasNew) {
    setCol(prevSection, 'col-6');
    setCol(newSection,  'col-6');
  } else if (hasPrev) {
    setCol(prevSection, 'col-12');
    setCol(newSection,  'none');
  } else if (hasNew) {
    setCol(prevSection, 'none');
    setCol(newSection,  'col-12');
  } else {
    setCol(prevSection, 'none');
    setCol(newSection,  'none');
  }

  // — Ajustar tamaño del modal según columnas —
  const dialog = this.detailsModal.querySelector('.modal-dialog');
  if (dialog) {
    dialog.classList.remove('modal-sm','modal-md','modal-lg');
    if (hasPrev && hasNew) {
      dialog.classList.add('modal-lg');
    } else if (hasPrev || hasNew) {
      dialog.classList.add('modal-md');
    } else {
      dialog.classList.add('modal-sm');
    }
  }

  // — Cambios Detectados —
  const changesSection   = document.getElementById('changes-section');
  const changesContainer = document.getElementById('changes-container');
  const hasChanges = changesContainer && changesContainer.children.length > 0;
  if (changesSection) {
    changesSection.style.display = hasChanges ? '' : 'none';
  }
}


  closeDetailsModal() {
    if (this.detailsModal) this.detailsModal.classList.remove('show');
  }
  // --------- DESCARGA CSV -----------
  async downloadData() {
    try {
      this.downloadBtn.disabled = true;
      const originalIcon = this.downloadBtn.querySelector('i');
      if (originalIcon) originalIcon.className = 'fas fa-spinner fa-spin';
      const exportFilters = {...this.filters};
      const csvBlob = await window.auditService.exportAuditLogsToCSV(exportFilters);
      const now = new Date();
      const timestamp = now.toISOString().slice(0, 19).replace(/[:.]/g, '-');
      const filename = `auditoria_${timestamp}.csv`;
      const downloadUrl = window.URL.createObjectURL(csvBlob);
      const downloadLink = document.createElement('a');
      downloadLink.href = downloadUrl;
      downloadLink.download = filename;
      downloadLink.style.display = 'none';
      document.body.appendChild(downloadLink);
      downloadLink.click();
      document.body.removeChild(downloadLink);
      window.URL.revokeObjectURL(downloadUrl);
      window.GlobalToast?.show(`Archivo ${filename} descargado exitosamente`, 'success');
    } catch (error) {
      let errorMessage = 'Error al descargar el archivo';
      if (error.message.includes('no encontrado')) errorMessage = 'El servicio de exportación no está disponible';
      else if (error.message.includes('permisos')) errorMessage = 'No tiene permisos para exportar datos';
      else if (error.message.includes('autenticación')) errorMessage = 'Sesión expirada. Por favor, inicie sesión nuevamente';
      window.GlobalToast?.show(errorMessage, 'error');
    } finally {
      if (this.downloadBtn) {
        this.downloadBtn.disabled = false;
        const icon = this.downloadBtn.querySelector('i');
        if (icon) icon.className = 'fas fa-download';
      }
    }
  }
}

// Instancia global (puedes modificar el nombre si quieres)
window.AuditRegistryController = AuditRegistryController;

