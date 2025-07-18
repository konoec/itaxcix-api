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

/**
 * Controlador para listar tipos de contacto con filtros avanzados
 * Endpoint: /admin/contact-types
 */
class ContactTypeListController {
  constructor() {
    console.log('🏗️ Inicializando ContactTypeListController');

    // Referencias al DOM (según tu HTML)
    this.tableBody     = document.getElementById('documentTypesTableBody');
    this.stats         = {
      total:       document.getElementById('totalContactTypes'),
      filtered:    document.getElementById('filteredResults'),
      page:        document.getElementById('currentPage'),
      range:       document.getElementById('itemsRange'),
      totalFooter: document.getElementById('totalDocumentTypesFooter'),
      rangeFooter: document.getElementById('itemsRangeFooter')
    };
    this.filters       = {
      search:     document.getElementById('searchInput'),
      clearSearch:document.getElementById('clearSearchBtn'),
      name:       document.getElementById('nameFilter'),
      active:     document.getElementById('activeFilter'),
      onlyActive: document.getElementById('onlyActiveFilter'),
      perPage:    document.getElementById('perPageSelect'),
      sortBy:     document.getElementById('sortBySelect'),
      sortDirection: document.getElementById('sortOrderSelect'),
      refresh:    document.getElementById('refreshBtn'),
      clearAll:   document.getElementById('clearFiltersBtn'),
      create:     document.getElementById('createContactTypeBtn')
    };
    this.pagination    = document.getElementById('paginationContainer');

    // Estado interno
    this.contactTypes   = [];
    this.currentPage    = 1;
    this.itemsPerPage   = 15;
    this.totalItems     = 0;
    this.totalPages     = 1;
    this.searchTerm     = '';
    this.searchDebounce = debounce(() => this.load(1), 500);
    this.isLoading      = false;

    this.init();
  }

  async init() {
    console.log('📋 Esperando ContactTypeService...');
    await this.waitForService();
    this.initEvents();
    setTimeout(() => this.load(), 100); // Pequeño delay para el DOM
  }

  async waitForService() {
    let tries = 0;
    while (!window.ContactTypeService && tries++ < 50) {
      await new Promise(r => setTimeout(r, 100));
    }
    if (!window.ContactTypeService) {
      throw new Error('ContactTypeService no disponible');
    }
    console.log('✅ ContactTypeService disponible');
  }

  initEvents() {
    console.log('🎯 Configurando eventos ContactTypeListController');

    // Búsqueda global
    if (this.filters.search) {
      this.filters.search.addEventListener('input', e => {
        this.searchTerm = e.target.value.trim();
        this.searchDebounce();
      });
    }
    if (this.filters.clearSearch) {
      this.filters.clearSearch.addEventListener('click', () => {
        this.filters.search.value = '';
        this.searchTerm = '';
        this.load(1);
      });
    }

    // Filtro por nombre
    if (this.filters.name) {
      this.filters.name.addEventListener('input', this.searchDebounce);
    }

    // Filtro activo/inactivo
    if (this.filters.active) {
      this.filters.active.addEventListener('change', () => {
        this.currentPage = 1;
        this.load();
      });
    }

    // Solo activos
    if (this.filters.onlyActive) {
      this.filters.onlyActive.addEventListener('change', () => {
        this.currentPage = 1;
        this.load();
      });
    }

    // Elementos por página
    if (this.filters.perPage) {
      this.filters.perPage.addEventListener('change', () => {
        this.itemsPerPage = Number(this.filters.perPage.value);
        this.currentPage  = 1;
        this.load();
      });
    }

    // Ordenar
    if (this.filters.sortBy) {
      this.filters.sortBy.addEventListener('change', () => this.load());
    }
    if (this.filters.sortDirection) {
      this.filters.sortDirection.addEventListener('change', () => this.load());
    }

    // Refrescar y limpiar todo
    if (this.filters.refresh) {
      this.filters.refresh.addEventListener('click', () => this.load());
    }
    if (this.filters.clearAll) {
      this.filters.clearAll.addEventListener('click', () => {
        // Resetear todos los filtros
        Object.values(this.filters).forEach(f => {
          if (f.tagName === 'INPUT' || f.tagName === 'SELECT') f.value = '';
        });
        this.searchTerm   = '';
        this.currentPage  = 1;
        this.itemsPerPage = 15;
        this.load();
      });
    }

    // Crear nuevo
    if (this.filters.create) {
      this.filters.create.addEventListener('click', () => {
        if (window.createContactTypeController) {
          window.createContactTypeController.open();
        }
      });
    }

    // Paginación (delegación)
    if (this.pagination) {
      this.pagination.addEventListener('click', e => {
        const btn = e.target.closest('button[data-page]');
        if (!btn) return;
        const p = Number(btn.dataset.page);
        if (p >= 1 && p <= this.totalPages) {
          this.currentPage = p;
          this.load(p);
        }
      });
    }

    console.log('✅ Eventos configurados');
  }

  async load(page = this.currentPage) {
    if (this.isLoading) return;
    this.isLoading = true;
    this.currentPage = page;

    // Mostrar spinner mientras carga
    this.tableBody.innerHTML = `
      <tr><td colspan="4" class="text-center py-4">
        <div class="spinner-border text-primary"></div>
      </td></tr>`;

    // Parámetros
    const params = {
      page,
      limit:       this.itemsPerPage,
      search:      this.searchTerm || null,
      name:        this.filters.name?.value || null,
      active:      this.filters.active?.value === '' ? null : this.filters.active.value === 'true',
      onlyActive:  this.filters.onlyActive?.value === 'true',
      sortBy:      this.filters.sortBy?.value,
      sortDirection: this.filters.sortDirection?.value
    };

    try {
      const res = await window.ContactTypeService.getContactTypes(
        params.page, params.limit,
        params.search, params.name, params.active,
        params.sortBy, params.sortDirection, params.onlyActive
      );
      if (!res.success) throw new Error(res.message);

      // Ajuste de datos
      this.contactTypes  = res.data.data  || [];
      this.totalItems    = res.data.total || 0;
      this.currentPage   = res.data.page  || page;
      this.itemsPerPage  = res.data.limit || this.itemsPerPage;
      this.totalPages    = Math.ceil(this.totalItems / this.itemsPerPage);

      this.render();
    } catch (err) {
      console.error('❌ Error al cargar:', err);
      this.tableBody.innerHTML = `
        <tr><td colspan="4" class="text-center text-danger py-4">
          ${err.message}
        </td></tr>`;
    } finally {
      this.isLoading = false;
    }
  }

  render() {
    // Renderizar filas
    if (!this.contactTypes.length) {
      this.tableBody.innerHTML = `
        <tr><td colspan="4" class="text-center text-muted py-4">
          No hay tipos de contacto
        </td></tr>`;
    } else {
      this.tableBody.innerHTML = this.contactTypes.map(t => `
        <tr>
          <td class="text-center">${t.id}</td>
          <td>${t.name}</td>
          <td class="text-center">
            <span class="badge bg-${t.active ? 'success' : 'danger'}-lt">
              <i class="fas fa-${t.active ? 'check' : 'times'}"></i> ${t.active ? 'Activo' : 'Inactivo'}
            </span>
          </td>
          <td class="text-center">
            <button class="btn btn-sm btn-outline-primary me-2" data-action="edit-contact-type" data-contact-type-id="${t.id}">
              <i class="fas fa-edit"></i>
            </button>
            <button class="btn btn-sm btn-outline-danger" data-action="delete-contact-type" data-contact-type-id="${t.id}">
              <i class="fas fa-trash"></i>
            </button>
          </td>
        </tr>
      `).join('');
    }

    // Estadísticas
    if (this.stats.total) this.stats.total.textContent = this.totalItems;
    if (this.stats.filtered) this.stats.filtered.textContent = this.contactTypes.length;
    if (this.stats.page) this.stats.page.textContent = this.currentPage;
    if (this.stats.range) {
      const start = (this.currentPage - 1) * this.itemsPerPage + 1;
      const end   = Math.min(this.totalItems, this.currentPage * this.itemsPerPage);
      this.stats.range.textContent = this.contactTypes.length ? `${start}–${end}` : '0';
    }

    // Footer
    if (this.stats.totalFooter) this.stats.totalFooter.textContent = this.totalItems;
    if (this.stats.rangeFooter) {
      const start = (this.currentPage - 1) * this.itemsPerPage + 1;
      const end   = Math.min(this.totalItems, this.currentPage * this.itemsPerPage);
      this.stats.rangeFooter.textContent = this.contactTypes.length
        ? `${start}–${end}`
        : '0';
    }

    // Paginación
    if (this.pagination) {
      let html = '';
      for (let i = 1; i <= this.totalPages; i++) {
        html += `<li class="page-item${i === this.currentPage ? ' active' : ''}">
          <button class="page-link" data-page="${i}">${i}</button>
        </li>`;
      }
      this.pagination.innerHTML = html;
    }
  }
}

window.ContactTypeListController = ContactTypeListController;
console.log('✅ ContactTypeListController listo');
('✅ ContactTypeListController listo');
