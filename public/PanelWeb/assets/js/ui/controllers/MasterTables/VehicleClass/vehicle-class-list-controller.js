// Controlador para la lista de clases de vehículos
class VehicleClassListController {
  constructor() {
    console.log('🏗️ Inicializando VehicleClassListController');
    this.tableBody = document.getElementById('vehicleClassTableBody');
    this.stats = {
      total: document.getElementById('totalVehicleClasses'),
      filtered: document.getElementById('filteredVehicleClasses'),
      page: document.getElementById('vehicleClassCurrentPage'),
      range: document.getElementById('vehicleClassItemsRange'),
      totalFooter: document.getElementById('totalVehicleClassesFooter'),
      rangeFooter: document.getElementById('vehicleClassItemsRangeFooter')
    };
    this.filters = {
      search: document.getElementById('vehicleClassSearchInput'),
      name: document.getElementById('vehicleClassNameFilter'),
      active: document.getElementById('vehicleClassActiveFilter'),
      perPage: document.getElementById('vehicleClassPerPageSelect'),
      sortBy: document.getElementById('vehicleClassSortBySelect'),
      sortDirection: document.getElementById('vehicleClassSortDirectionSelect')
    };
    this.pagination = document.getElementById('vehicleClassPagination');
    this.refreshBtn = document.getElementById('refreshVehicleClassBtn');
    this.clearBtn = document.getElementById('clearVehicleClassFiltersBtn');

    // Exponer el controlador globalmente para el flujo de eliminación
    window.vehicleClassListController = this;

    this.initEvents();
    setTimeout(() => this.load(), 100);
  }

  debounce(func, wait = 300) {
    let timeout;
    return (...args) => {
      clearTimeout(timeout);
      timeout = setTimeout(() => func.apply(this, args), wait);
    };
  }

  initEvents() {
    const debouncedLoad = this.debounce(() => this.load());

    this.filters.search?.addEventListener('input', debouncedLoad);
    this.filters.name?.addEventListener('input', debouncedLoad);
    this.filters.active?.addEventListener('change', () => this.load());
    this.filters.perPage?.addEventListener('change', () => this.load());
    this.filters.sortBy?.addEventListener('change', () => this.load());
    this.filters.sortDirection?.addEventListener('change', () => this.load());

    this.refreshBtn?.addEventListener('click', () => this.load());
    this.clearBtn?.addEventListener('click', () => this.clearFilters());

    const clearSearch = document.getElementById('clearVehicleClassSearchBtn');
    if (clearSearch && this.filters.search) {
      clearSearch.addEventListener('click', () => {
        this.filters.search.value = '';
        this.load();
      });
    }
  }

  clearFilters() {
    this.filters.search.value = '';
    this.filters.name.value = '';
    this.filters.active.value = '';
    this.filters.perPage.value = '15';
    this.filters.sortBy.value = 'name';
    this.filters.sortDirection.value = 'ASC';
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

      const res = await VehicleClassService.getVehicleClasses(params);
      console.log('📥 Respuesta recibida:', res);
      console.log('📥 Estructura res.data:', res.data);
      console.log('📥 Estructura res.data.data:', res.data?.data);
      
      if (res.success && res.data) {
        // Verificar si la estructura es { data: { data: [], pagination: {} } } o { data: { items: [], meta: {} } }
        const itemsArray = res.data.data || res.data.items || [];
        const paginationData = res.data.pagination || res.data.meta || {};
        
        console.log('📥 Items array:', itemsArray);
        console.log('📥 Pagination data:', paginationData);
        
        this.render(itemsArray);
        this.updateStats({ data: itemsArray, pagination: paginationData });
        this.renderPagination(paginationData);
      } else {
        throw new Error(res.message || 'Respuesta inválida');
      }
    } catch (err) {
      console.error(err);
      this.showError(err.message);
      GlobalToast.show('Error al cargar clases de vehículos: ' + err.message, 'error');
    }
  }

  showLoading() {
    this.tableBody.innerHTML = `
      <tr><td colspan="4" class="text-center py-4">
        <div class="spinner-border spinner-border-sm me-2" role="status"></div>
        Cargando clases de vehículos...
      </td></tr>`;
  }

  showError(msg) {
    this.tableBody.innerHTML = `
      <tr><td colspan="4" class="text-center py-4 text-danger">
        <i class="fas fa-exclamation-triangle me-1"></i>${msg}
      </td></tr>`;
  }

  render(data) {
    if (!data || !Array.isArray(data)) {
      console.warn('⚠️ No se puede renderizar: data no es un array válido', data);
      this.tableBody.innerHTML = `
        <tr><td colspan="4" class="text-center py-4 text-muted">
          <i class="fas fa-exclamation-triangle me-1"></i>Error en los datos
        </td></tr>`;
      return;
    }
    
    if (!data.length) {
      this.tableBody.innerHTML = `
        <tr><td colspan="4" class="text-center py-4 text-muted">
          <i class="fas fa-search me-1"></i>No se encontraron clases
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
              <i class="fas ${c.active ? 'fa-check-circle' : 'fa-times-circle'} me-1"></i>
              ${c.active ? 'Activo' : 'Inactivo'}
            </span>
          </td>
          <td class="text-center">
            <div class="btn-group">
              <button class="btn btn-sm btn-outline-warning"><i class="fas fa-edit"></i></button>
              <button class="btn btn-sm btn-outline-danger"
                data-action="delete-vehicle-class"
                data-vehicle-class-id="${c.id}"
                data-vehicle-class-name="${c.name}">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </td>
        </tr>
      `).join('');
  }

  updateStats(responseData) {
    const data = responseData.data || [];
    const pagination = responseData.pagination || {};
    
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
  // Asegura que los nombres de los campos sean robustos según posibles variantes
  const totalPages = p.total_pages || p.totalPages || p.lastPage || 1;
  const currentPage = p.current_page || p.currentPage || p.page || 1;
  if (totalPages <= 1) {
    this.pagination.innerHTML = '';
    return;
  }

  const pageRange = 2; // ¿Cuántas páginas a la izquierda/derecha de la actual?
  let html = '';

  // Flecha "Anterior"
  html += `
    <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
      <a class="page-link" href="#" data-page="${currentPage - 1}">
        <i class="fas fa-chevron-left"></i>
      </a>
    </li>`;

  // Si no estás en la página 1, muestra la primera página y puntos suspensivos si corresponde
  const startPage = Math.max(1, currentPage - pageRange);
  const endPage = Math.min(totalPages, currentPage + pageRange);

  if (startPage > 1) {
    html += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
    if (startPage > 2) {
      html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
    }
  }

  // Botones de páginas "centrales"
  for (let i = startPage; i <= endPage; i++) {
    html += `
      <li class="page-item ${i === currentPage ? 'active' : ''}">
        <a class="page-link" href="#" data-page="${i}">${i}</a>
      </li>`;
  }

  // Si no estás en la última página, muestra puntos y el último botón
  if (endPage < totalPages) {
    if (endPage < totalPages - 1) {
      html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
    }
    html += `<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`;
  }

  // Flecha "Siguiente"
  html += `
    <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
      <a class="page-link" href="#" data-page="${currentPage + 1}">
        <i class="fas fa-chevron-right"></i>
      </a>
    </li>`;

  this.pagination.innerHTML = html;

  // Delegación de eventos
  this.pagination.querySelectorAll('a[data-page]').forEach(a => {
    a.addEventListener('click', e => {
      e.preventDefault();
      const pg = parseInt(a.dataset.page);
      if (!isNaN(pg) && pg !== currentPage && pg >= 1 && pg <= totalPages) {
        this.currentPage = pg;
        this.load();
      }
    });
  });
}}


window.VehicleClassListController = VehicleClassListController;
