// Controlador para la lista de colores con paginación moderna (puntos suspensivos)
class ColorListController {
  constructor() {
    console.log('🏗️ Inicializando ColorListController');
    this.tableBody = document.getElementById('colorTableBody');
    this.stats = {
      total: document.getElementById('totalColors'),
      filtered: document.getElementById('filteredColors'),
      page: document.getElementById('currentColorPage'),
      range: document.getElementById('colorsRange'),
      totalFooter: document.getElementById('totalColorsFooter'),
      rangeFooter: document.getElementById('colorsRangeFooter')
    };
    this.filters = {
      search: document.getElementById('colorSearchInput'),
      name: document.getElementById('colorNameFilter'),
      active: document.getElementById('colorActiveFilter'),
      perPage: document.getElementById('colorPerPageSelect'),
      sortBy: document.getElementById('colorSortBySelect'),
      sortDirection: document.getElementById('colorSortDirectionSelect')
    };
    this.pagination = document.getElementById('colorPagination');
    this.refreshBtn = document.getElementById('refreshColorBtn');
    this.clearBtn = document.getElementById('clearColorFiltersBtn');

    this.currentPage = 1;
    this.colorList = [];            // ← inicializamos el array donde guardaremos los colores

    this.initEvents();
    setTimeout(() => this.load(1), 100); // Siempre inicia en página 1
  }

  debounce(fn, wait = 300) {
    let t;
    return (...args) => {
      clearTimeout(t);
      t = setTimeout(() => fn.apply(this, args), wait);
    };
  }

  initEvents() {
    const debounced = this.debounce(() => this.load(1));

    this.filters.search?.addEventListener('input', debounced);
    this.filters.name?.addEventListener('input', debounced);
    this.filters.active?.addEventListener('change', () => this.load(1));
    this.filters.perPage?.addEventListener('change', () => this.load(1));
    this.filters.sortBy?.addEventListener('change', () => this.load(1));
    this.filters.sortDirection?.addEventListener('change', () => this.load(1));

    this.refreshBtn?.addEventListener('click', () => this.load(this.currentPage));
    this.clearBtn?.addEventListener('click', () => this.clearFilters());

    const clearBtn = document.getElementById('clearColorSearchBtn');
    clearBtn?.addEventListener('click', () => {
      this.filters.search.value = '';
      this.load(1);
    });
  }

  clearFilters() {
    this.filters.search.value = '';
    this.filters.name.value = '';
    this.filters.active.value = '';
    this.filters.perPage.value = '15';
    this.filters.sortBy.value = 'name';
    this.filters.sortDirection.value = 'ASC';
    this.load(1);
  }

  async load(page = 1) {
    try {
      this.showLoading();
      this.currentPage = page;
      const params = {
        page: this.currentPage,
        perPage: this.filters.perPage.value,
        search: this.filters.search.value,
        name: this.filters.name.value,
        sortBy: this.filters.sortBy.value,
        sortDirection: this.filters.sortDirection.value
      };
      if (this.filters.active.value !== '') {
        params.active = this.filters.active.value === 'true';
      }

      const res = await ColorService.getColors(params);
      const items = res.data.data || [];
      const pageInfo = res.data.meta || {};

      // ← Guarda la lista completa para que el controlador de edición la reutilice
      this.colorList = items;

      this.render(items);
      this.updateStats({ data: items, pagination: pageInfo });
      this.renderPagination(pageInfo);
    } catch (err) {
      console.error(err);
      this.showError(err.message);
      GlobalToast.show('Error al cargar colores: ' + err.message, 'error');
    }
  }

  showLoading() {
    this.tableBody.innerHTML = `
      <tr><td colspan="4" class="text-center py-4">
        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
        Cargando colores...
      </td></tr>`;
  }

  showError(msg) {
    this.tableBody.innerHTML = `
      <tr><td colspan="4" class="text-center py-4 text-danger">
        <i class="fas fa-exclamation-triangle me-1"></i>${msg}
      </td></tr>`;
  }

  render(data) {
    if (!Array.isArray(data) || data.length === 0) {
      this.tableBody.innerHTML = `
        <tr><td colspan="4" class="text-center py-4 text-muted">
          <i class="fas fa-search me-1"></i>No se encontraron colores
        </td></tr>`;
      return;
    }
    this.tableBody.innerHTML = data
      .map(c => `
        <tr>
          <td class="text-center">${c.id}</td>
          <td>${c.name}</td>
          <td class="text-center">
            <span class="badge ${c.active ? 'bg-success-lt' : 'bg-danger-lt'}">
              <i class="fas ${c.active ? 'fa-check-circle ' : 'fa-times-circle'} me-1"></i>
              ${c.active ? 'Activo' : 'Inactivo'}
            </span>
          </td>
          <td class="text-center">
            <div class="btn-group">
              <button class="btn btn-sm btn-outline-warning" data-action="edit-color" data-id="${c.id}" title="Editar">
                <i class="fas fa-edit"></i>
              </button>
              <button class="btn btn-sm btn-outline-danger" data-action="delete-color" data-color-id="${c.id}" data-color-name="${c.name}" title="Eliminar">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>
      `).join('');
  }

  updateStats({ data, pagination }) {
    const totalItems = typeof pagination.total === 'number' ? pagination.total : data.length;
    const currentPage = typeof pagination.currentPage === 'number' ? pagination.currentPage : 1;
    const perPage = typeof pagination.perPage === 'number' ? pagination.perPage : 15;
    let start = 0, end = 0;
    if (totalItems > 0 && data.length > 0) {
      start = (currentPage - 1) * perPage + 1;
      end = start + data.length - 1;
      if (end > totalItems) end = totalItems;
    } else if (totalItems > 0) {
      start = 1;
      end = totalItems;
    }
    this.stats.total.textContent = totalItems;
    this.stats.filtered.textContent = data.length;
    this.stats.page.textContent = currentPage;
    this.stats.range.textContent = totalItems === 0 ? '0-0' : `${start}-${end}`;
    this.stats.totalFooter.textContent = totalItems;
    this.stats.rangeFooter.textContent = totalItems === 0 ? '0-0' : `${start}-${end}`;
  }

  renderPagination(p) {
  const totalPages = p.lastPage || p.totalPages || 1;
  const currentPage = p.currentPage || p.page || 1;
  const pagination = this.pagination;
  if (totalPages <= 1) {
    pagination.innerHTML = '';
    return;
  }
  let html = '';

  // Primera página <<
  html += `
    <li class="page-item${currentPage === 1 ? ' disabled' : ''}">
      <a class="page-link" href="#" data-page="1" aria-label="Primera">
        <i class="fas fa-angle-double-left"></i>
      </a>
    </li>`;

  // Anterior <
  html += `
    <li class="page-item${currentPage === 1 ? ' disabled' : ''}">
      <a class="page-link" href="#" data-page="${currentPage - 1}" aria-label="Anterior">
        <i class="fas fa-angle-left"></i>
      </a>
    </li>`;

  // Números con puntos suspensivos
  const maxVisible = 2;
  let start = Math.max(1, currentPage - maxVisible);
  let end = Math.min(totalPages, currentPage + maxVisible);

  if (start > 1) {
    html += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
    if (start > 2) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
  }

  for (let i = start; i <= end; i++) {
    html += `
      <li class="page-item${i === currentPage ? ' active' : ''}">
        <a class="page-link" href="#" data-page="${i}">${i}</a>
      </li>`;
  }

  if (end < totalPages) {
    if (end < totalPages - 1) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
    html += `<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`;
  }

  // Siguiente >
  html += `
    <li class="page-item${currentPage === totalPages ? ' disabled' : ''}">
      <a class="page-link" href="#" data-page="${currentPage + 1}" aria-label="Siguiente">
        <i class="fas fa-angle-right"></i>
      </a>
    </li>`;

  // Última página >>
  html += `
    <li class="page-item${currentPage === totalPages ? ' disabled' : ''}">
      <a class="page-link" href="#" data-page="${totalPages}" aria-label="Última">
        <i class="fas fa-angle-double-right"></i>
      </a>
    </li>`;

  pagination.innerHTML = html;

  pagination.querySelectorAll('a[data-page]').forEach(a => {
    a.addEventListener('click', e => {
      e.preventDefault();
      const pg = parseInt(a.dataset.page, 10);
      if (!isNaN(pg) && pg >= 1 && pg <= totalPages && pg !== currentPage) {
        this.load(pg);
      }
    });
  });
}

}

// Instancia global
window.ColorListController = ColorListController;

