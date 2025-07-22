// Controlador para la lista de tipos de trámite
class ProcedureTypeListController {
  constructor() {
    console.log('🏗️ Inicializando ProcedureTypeListController');

    this.tableBody = document.getElementById('procedureTypeTableBody');
    this.stats = {
      total: document.getElementById('totalProcedureTypes'),
      filtered: document.getElementById('filteredProcedureTypes'),
      page: document.getElementById('currentProcedureTypePage'),
      range: document.getElementById('procedureTypesRange'),
      totalFooter: document.getElementById('totalProcedureTypesFooter'),
      rangeFooter: document.getElementById('procedureTypesRangeFooter')
    };
    this.filters = {
      search: document.getElementById('procedureTypeSearchInput'),
      name: document.getElementById('procedureTypeNameFilter'),
      active: document.getElementById('procedureTypeActiveFilter'),
      perPage: document.getElementById('procedureTypePerPageSelect'),
      sortBy: document.getElementById('procedureTypeSortBySelect'),
      sortDirection: document.getElementById('procedureTypeSortDirectionSelect')
    };
    this.pagination = document.getElementById('procedureTypePagination');
    this.refreshBtn = document.getElementById('refreshProcedureTypeBtn');
    this.clearBtn = document.getElementById('clearProcedureTypeFiltersBtn');
    this.currentPage = 1;
    this.initEvents();
    setTimeout(() => this.load(), 100);
  }

  debounce(fn, wait = 300) {
    let timeout;
    return (...args) => {
      clearTimeout(timeout);
      timeout = setTimeout(() => fn.apply(this, args), wait);
    };
  }

  initEvents() {
    const debouncedLoad = this.debounce(() => this.load());
    this.filters.search?.addEventListener('input', debouncedLoad);
    this.filters.name?.addEventListener('input', debouncedLoad);

    ['active','perPage','sortBy','sortDirection'].forEach(key => {
      this.filters[key]?.addEventListener('change', () => this.load());
    });

    this.refreshBtn?.addEventListener('click', () => this.load());
    this.clearBtn?.addEventListener('click', () => this.clearFilters());

    const clearSearchBtn = document.getElementById('clearProcedureTypeSearchBtn');
    clearSearchBtn?.addEventListener('click', () => {
      this.filters.search.value = '';
      this.load();
    });
  }

  clearFilters() {
    this.filters.search.value = '';
    this.filters.name.value = '';
    this.filters.active.value = '';
    this.filters.perPage.value = '15';
    this.filters.sortBy.value = 'name';
    this.filters.sortDirection.value = 'ASC';
    this.currentPage = 1;
    this.load();
  }

  async load() {
    try {
      this.showLoading();
      const params = {
        page: this.currentPage || 1,
        perPage: this.filters.perPage.value,
        search: this.filters.search.value,
        name: this.filters.name.value,
        sortBy: this.filters.sortBy.value,
        sortDirection: this.filters.sortDirection.value
      };
      if (this.filters.active.value !== '') {
        params.active = this.filters.active.value === 'true';
      }

      const res = await ProcedureTypeService.getProcedureTypes(params);
      const items = res.data.data || [];
      const pg = res.data.pagination || {};

      this.render(items);
      this.updateStats({ data: items, pagination: pg });
      this.renderPagination(pg);
    } catch (err) {
      console.error(err);
      this.showError(err.message);
      GlobalToast.show('Error al cargar tipos de trámite: ' + err.message, 'error');
    }
  }

  showLoading() {
    this.tableBody.innerHTML = `
      <tr><td colspan="4" class="text-center py-4">
        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
        Cargando tipos de trámite...
      </td></tr>`;
  }

  showError(msg) {
    this.tableBody.innerHTML = `
      <tr><td colspan="4" class="text-center py-4 text-danger">
        <i class="fas fa-exclamation-triangle me-1"></i>${msg}
      </td></tr>`;
  }

  /**
   * Abre el modal de edición para un tipo de trámite
   * @param {number} id - ID del tipo de trámite
   */
  editProcedureType(id) {
    const procedureType = (this.tableBody && this.lastData)
      ? this.lastData.find(item => item.id === id)
      : null;
    if (!procedureType) {
      console.error('❌ Tipo de trámite no encontrado para edición:', id);
      if (window.GlobalToast) {
        window.GlobalToast.showToast('Tipo de trámite no encontrado', 'error');
      }
      return;
    }
    // Disparar evento global para abrir el modal de edición
    const event = new CustomEvent('openProcedureTypeEditModal', {
      detail: { id, procedureTypeData: procedureType }
    });
    document.dispatchEvent(event);
  }

  render(data) {
    this.lastData = data;
    if (!Array.isArray(data) || data.length === 0) {
      this.tableBody.innerHTML = `
        <tr><td colspan="4" class="text-center py-4 text-muted">
          <i class="fas fa-search me-1"></i>No se encontraron tipos de trámite
        </td></tr>`;
      return;
    }
    this.tableBody.innerHTML = data.map(item => `
      <tr>
        <td class="text-center">${item.id}</td>
        <td>${item.name}</td>
        <td class="text-center">
          <span class="badge ${item.active ? 'bg-success-lt' : 'bg-danger-lt'}">
            <i class="fas ${item.active ? 'fa-check-circle' : 'fa-times-circle'} me-1"></i>
            ${item.active ? 'Activo' : 'Inactivo'}
          </span>
        </td>
        <td class="text-center">
          <div class="btn-group">
            <button class="btn btn-sm btn-outline-warning" onclick="window.procedureTypeListControllerInstance.editProcedureType(${item.id})"><i class="fas fa-edit"></i></button>
            <button class="btn btn-sm btn-outline-danger" data-action="delete-procedure-type" data-procedure-type-id="${item.id}" data-procedure-type-name="${item.name}"><i class="fas fa-trash"></i></button>
          </div>
        </td>
      </tr>`).join('');
  }

  updateStats({ data, pagination }) {
    this.stats.total.textContent = pagination.total_items || 0;
    this.stats.filtered.textContent = data.length;
    this.stats.page.textContent = pagination.current_page || 1;
    const start = ((pagination.current_page || 1) - 1) * (pagination.per_page || 15) + 1;
    const end = start + data.length - 1;
    const range = `${start}-${end}`;
    this.stats.range.textContent = range;
    this.stats.totalFooter.textContent = pagination.total_items || 0;
    this.stats.rangeFooter.textContent = range;
  }

  renderPagination(p) {
  const totalPages = p.total_pages || 1;
  const currentPage = p.current_page || 1;
  let html = '';

  // Doble flecha izquierda (Primera página)
  html += `
    <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
      <a class="page-link" href="#" data-page="1" title="Primera">
        <i class="fas fa-angle-double-left"></i>
      </a>
    </li>`;
  // Flecha izquierda (Anterior)
  html += `
    <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
      <a class="page-link" href="#" data-page="${currentPage - 1}" title="Anterior">
        <i class="fas fa-angle-left"></i>
      </a>
    </li>`;

  const delta = 2;
  let startPage = Math.max(1, currentPage - delta);
  let endPage = Math.min(totalPages, currentPage + delta);

  if (startPage > 1) {
    html += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
    if (startPage > 2) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
  }

  for (let i = startPage; i <= endPage; i++) {
    html += `
      <li class="page-item ${i === currentPage ? 'active' : ''}">
        <a class="page-link" href="#" data-page="${i}">${i}</a>
      </li>`;
  }

  if (endPage < totalPages) {
    if (endPage < totalPages - 1) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
    html += `<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`;
  }

  // Flecha derecha (Siguiente)
  html += `
    <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
      <a class="page-link" href="#" data-page="${currentPage + 1}" title="Siguiente">
        <i class="fas fa-angle-right"></i>
      </a>
    </li>`;
  // Doble flecha derecha (Última página)
  html += `
    <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
      <a class="page-link" href="#" data-page="${totalPages}" title="Última">
        <i class="fas fa-angle-double-right"></i>
      </a>
    </li>`;

  this.pagination.innerHTML = html;
  this.pagination.querySelectorAll('a[data-page]').forEach(a => {
    a.addEventListener('click', e => {
      e.preventDefault();
      const pg = parseInt(a.dataset.page, 10);
      if (!isNaN(pg) && pg >= 1 && pg <= totalPages && pg !== currentPage) {
        this.currentPage = pg;
        this.load();
      }
    });
  });
}
}

window.ProcedureTypeListController = ProcedureTypeListController;
