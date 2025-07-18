// Función debounce para optimizar las búsquedas
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

// Controlador para la lista de marcas (Brand)
class BrandListController {
  constructor() {
    console.log('🏗️ Inicializando BrandListController');

    // Referencias a DOM
    this.tableBody            = document.getElementById('brandTableBody');
    this.stats = {
      total:       document.getElementById('totalBrands'),
      filtered:    document.getElementById('filteredResults'),
      page:        document.getElementById('currentPage'),
      range:       document.getElementById('itemsRange'),
      totalFooter: document.getElementById('totalBrandsFooter'),
      rangeFooter: document.getElementById('itemsRangeFooter')
    };
    this.filters = {
      search:        document.getElementById('searchInput'),
      name:          document.getElementById('nameFilter'),
      active:        document.getElementById('activeFilter'),
      onlyActive:    document.getElementById('onlyActiveFilter'),
      perPage:       document.getElementById('perPageSelect'),
      sortBy:        document.getElementById('sortBySelect'),
      sortDirection: document.getElementById('sortOrderSelect')
    };
    this.paginationContainer = document.getElementById('paginationContainer');
    this.refreshBtn          = document.getElementById('refreshBtn');
    this.clearBtn            = document.getElementById('clearFiltersBtn');

    // Estado interno
    this.currentPage = 1;
    this.totalPages  = 1;
    this.brands      = []; // ← Inicializamos la lista de marcas

    this.initEvents();
    // Primera carga con un pequeño delay
    setTimeout(() => this.load(1), 100);
  }

  initEvents() {
    // Al cambiar cualquier filtro recargamos en página 1
    Object.values(this.filters).forEach(input => {
      if (!input) return;
      input.addEventListener('change', () => this.load(1));
      if (input.type === 'text') {
        input.addEventListener('input', debounce(() => this.load(1), 300));
      }
    });

    if (this.refreshBtn) this.refreshBtn.addEventListener('click', () => this.load(this.currentPage));
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
    Object.values(this.filters).forEach(input => {
      if (input) input.value = '';
    });
    // Valores por defecto
    this.filters.perPage.value       = '15';
    this.filters.sortBy.value        = 'name';
    this.filters.sortDirection.value = 'asc';
    this.load(1);
  }

  async load(page = 1) {
    try {
      this.currentPage = page;
      this.showLoading();

      // Construir parámetros
      const params = {
        page,
        perPage:       this.filters.perPage?.value || 15,
        search:        this.filters.search?.value || '',
        name:          this.filters.name?.value || '',
        sortBy:        this.filters.sortBy?.value || 'name',
        sortDirection: this.filters.sortDirection?.value || 'asc'
      };
      if (this.filters.active?.value !== '')    params.active     = this.filters.active.value === 'true';
      if (this.filters.onlyActive?.value !== '')params.onlyActive = this.filters.onlyActive.value === 'true';

      const response = await BrandService.getBrands(params);
      if (response.success && response.data) {
        // ← Guardamos los items en this.brands para que otros controladores los reutilicen
        this.brands = response.data.items || [];
        this.render(response.data);
      } else {
        throw new Error(response.message || 'Respuesta inválida del servidor');
      }
    } catch (err) {
      console.error('❌ Error al cargar marcas:', err);
      this.showError(err.message);
      GlobalToast.show('Error al cargar marcas: ' + err.message, 'error');
    }
  }

  showLoading() {
    if (this.tableBody) {
      this.tableBody.innerHTML = `
        <tr>
          <td colspan="4" class="text-center py-4">
            <div class="d-flex justify-content-center align-items-center">
              <div class="spinner-border spinner-border-sm me-2" role="status"></div>
              <span>Cargando marcas...</span>
            </div>
          </td>
        </tr>`;
    }
  }

  showError(message) {
    if (this.tableBody) {
      this.tableBody.innerHTML = `
        <tr>
          <td colspan="4" class="text-center py-4">
            <div class="text-danger">
              <i class="fas fa-exclamation-triangle me-2"></i>
              Error: ${message}
            </div>
          </td>
        </tr>`;
    }
  }

  render(data) {
    const items = data.items || [];
    const meta  = data.meta  || {};

    // Actualizar estadísticas en cabecera y footer
    this.stats.total.textContent        = meta.total        || 0;
    this.stats.filtered.textContent     = items.length     || 0;
    this.stats.page.textContent         = meta.currentPage || 1;
    this.stats.range.textContent        = items.length
      ? `${(meta.currentPage - 1) * meta.perPage + 1}-${(meta.currentPage - 1) * meta.perPage + items.length}`
      : '0';
    this.stats.totalFooter.textContent   = meta.total        || 0;
    this.stats.rangeFooter.textContent   = this.stats.range.textContent;

    // Guardar para paginación
    this.currentPage = meta.currentPage || 1;
    this.totalPages  = meta.lastPage    || 1;

    // Render filas
    if (!items.length) {
      this.tableBody.innerHTML = `
        <tr>
          <td colspan="4" class="text-center py-4">
            <div class="text-muted">
              <i class="fas fa-search me-2"></i>
              No se encontraron marcas
            </div>
          </td>
        </tr>`;
    } else {
      this.tableBody.innerHTML = items.map(b => this.renderBrandRow(b)).join('');
    }

    // Render paginación
    this.renderPaginationButtons();
  }

  renderBrandRow(brand) {
    return `
      <tr>
        <td class="text-center text-muted">${brand.id}</td>
        <td>
          <div class="d-flex align-items-center">
            <span class="avatar avatar-sm me-2 bg-primary-lt">
              <i class="fas fa-tag text-primary"></i>
            </span>
            <div class="font-weight-medium">${brand.name}</div>
          </div>
        </td>
        <td class="text-center">
          <span class="badge ${brand.active ? 'bg-success-lt' : 'bg-danger-lt'}">
            <i class="fas ${brand.active ? 'fa-check-circle' : 'fa-times-circle'} me-1"></i>
            ${brand.active ? 'Activo' : 'Inactivo'}
          </span>
        </td>
        <td class="text-center">
          <div class="btn-group">
            <button class="btn btn-sm btn-outline-warning" title="Editar" data-action="edit-brand" data-id="${brand.id}"><i class="fas fa-edit"></i></button>
            <button class="btn btn-sm btn-outline-danger" title="Eliminar"><i class="fas fa-trash"></i></button>
          </div>
        </td>
      </tr>`;
  }

  renderPaginationButtons() {
    if (!this.paginationContainer) return;
    const ul = this.paginationContainer;
    ul.innerHTML = '';

    const makeLi = (inner, page, disabled = false, active = false) => {
      const li = document.createElement('li');
      li.className = `page-item${disabled ? ' disabled' : ''}${active ? ' active' : ''}`;
      const btn = document.createElement('button');
      btn.className = 'page-link';
      btn.disabled = disabled;
      btn.innerHTML = inner;
      btn.addEventListener('click', e => {
        e.preventDefault();
        if (!disabled && page && page !== this.currentPage) {
          this.load(page);
        }
      });
      li.appendChild(btn);
      return li;
    };

    // ««  «  números  »  »»

    ul.appendChild(makeLi('<i class="fas fa-angle-left"></i>', this.currentPage - 1, this.currentPage === 1));

    const delta = 2;
    let start = Math.max(1, this.currentPage - delta);
    let end   = Math.min(this.totalPages, this.currentPage + delta);

    if (start > 1) {
      ul.appendChild(makeLi('1', 1));
      if (start > 2) ul.appendChild(makeLi('...', null, true));
    }

    for (let p = start; p <= end; p++) {
      ul.appendChild(makeLi(p, p, false, p === this.currentPage));
    }

    if (end < this.totalPages) {
      if (end < this.totalPages - 1) ul.appendChild(makeLi('...', null, true));
      ul.appendChild(makeLi(this.totalPages, this.totalPages));
    }

    ul.appendChild(makeLi('<i class="fas fa-angle-right"></i>', this.currentPage + 1, this.currentPage === this.totalPages));
  }
}

// Disponibilizar globalmente
window.BrandListController = BrandListController;
