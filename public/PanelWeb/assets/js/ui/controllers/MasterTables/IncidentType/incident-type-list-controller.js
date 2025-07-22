// assets/js/ui/controllers/MasterTables/IncidentType/incident-type-list-controller.js

// Controlador para la lista de tipos de incidencia
class IncidentTypeListController {
  constructor() {
    console.log('🏗️ Inicializando IncidentTypeListController');

    this.tableBody = document.getElementById('incidentTypeTableBody');
    this.stats = {
      total:   document.getElementById('totalIncidentTypes'),
      filtered:document.getElementById('filteredIncidentTypes'),
      page:    document.getElementById('currentIncidentTypePage'),
      range:   document.getElementById('incidentTypesRange'),
      totalFooter: document.getElementById('totalIncidentTypesFooter'),
      rangeFooter: document.getElementById('incidentTypesRangeFooter')
    };
    this.filters = {
      search:    document.getElementById('incidentTypeSearchInput'),
      name:      document.getElementById('incidentTypeNameFilter'),
      active:    document.getElementById('incidentTypeActiveFilter'),
      perPage:   document.getElementById('incidentTypePerPageSelect'),
      sortBy:    document.getElementById('incidentTypeSortBySelect'),
      sortDirection: document.getElementById('incidentTypeSortDirectionSelect')
    };
    this.pagination = document.getElementById('incidentTypePagination');
    this.refreshBtn = document.getElementById('refreshIncidentTypeBtn');
    this.clearBtn   = document.getElementById('clearIncidentTypeFiltersBtn');
    this.createBtn  = document.getElementById('createIncidentTypeBtn');

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
    const dLoad = this.debounce(() => this.load());
    this.filters.search?.addEventListener('input', dLoad);
    this.filters.name?.addEventListener('input', dLoad);
    ['active','perPage','sortBy','sortDirection'].forEach(key => {
      this.filters[key]?.addEventListener('change', () => this.load());
    });
    this.refreshBtn?.addEventListener('click', () => this.load());
    this.clearBtn?.addEventListener('click', () => this.clearFilters());
    document.getElementById('clearIncidentTypeSearchBtn')
      ?.addEventListener('click', () => {
        this.filters.search.value = '';
        this.load();
      });

    // Abrir modal de creación al hacer click en el botón
    this.createBtn?.addEventListener('click', () => {
      const modal = document.getElementById('incidentTypeCreateModal');
      if (!window.bootstrap) {
        console.error('window.bootstrap no está disponible. ¿Tabler JS se está cargando correctamente?');
        alert('Error: Bootstrap JS no está disponible. Verifica la carga de Tabler.');
        return;
      }
      if (modal) {
        const modalInstance = window.bootstrap.Modal.getOrCreateInstance(modal);
        // Limpiar el formulario antes de mostrar
        const form = modal.querySelector('form');
        if (form) form.reset();
        modalInstance.show();
      } else {
        console.error('No se encontró el modal de creación.');
      }
    });
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

      const res = await IncidentTypeService.getIncidentTypes(params);
      const items = res.data.data || [];
      const pg    = res.data.pagination || {};

      this.render(items);
      this.updateStats({ data: items, pagination: pg });
      this.renderPagination(pg);

    } catch (err) {
      console.error(err);
      this.showError(err.message);
      GlobalToast.show('Error al cargar tipos de incidencia: ' + err.message, 'error');
    }
  }

  showLoading() {
    this.tableBody.innerHTML = `
      <tr>
        <td colspan="4" class="text-center py-4">
          <div class="spinner-border spinner-border-sm me-2" role="status"></div>
          Cargando tipos de incidencia...
        </td>
      </tr>`;
  }

  showError(msg) {
    this.tableBody.innerHTML = `
      <tr>
        <td colspan="4" class="text-center py-4 text-danger">
          <i class="fas fa-exclamation-triangle me-1"></i>${msg}
        </td>
      </tr>`;
  }

  render(data) {
    if (!Array.isArray(data) || data.length === 0) {
      this.tableBody.innerHTML = `
        <tr>
          <td colspan="4" class="text-center py-4 text-muted">
            <i class="fas fa-search me-1"></i>No se encontraron tipos de incidencia
          </td>
        </tr>`;
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
            <button class="btn btn-sm btn-outline-warning"><i class="fas fa-edit"></i></button>
            <button class="btn btn-sm btn-outline-danger" data-action="delete-incident-type" data-incident-type-id="${item.id}" data-incident-type-name="${item.name}"><i class="fas fa-trash"></i></button>
          </div>
        </td>
      </tr>
    `).join('');
  }
  // Delegar el evento de eliminación en el tbody
  initEvents() {
    const dLoad = this.debounce(() => this.load());
    this.filters.search?.addEventListener('input', dLoad);
    this.filters.name?.addEventListener('input', dLoad);
    ['active','perPage','sortBy','sortDirection'].forEach(key => {
      this.filters[key]?.addEventListener('change', () => this.load());
    });
    this.refreshBtn?.addEventListener('click', () => this.load());
    this.clearBtn?.addEventListener('click', () => this.clearFilters());
    document.getElementById('clearIncidentTypeSearchBtn')
      ?.addEventListener('click', () => {
        this.filters.search.value = '';
        this.load();
      });

    // Abrir modal de creación al hacer click en el botón
    this.createBtn?.addEventListener('click', () => {
      const modal = document.getElementById('incidentTypeCreateModal');
      if (!window.bootstrap) {
        console.error('window.bootstrap no está disponible. ¿Tabler JS se está cargando correctamente?');
        alert('Error: Bootstrap JS no está disponible. Verifica la carga de Tabler.');
        return;
      }
      if (modal) {
        const modalInstance = window.bootstrap.Modal.getOrCreateInstance(modal);
        // Limpiar el formulario antes de mostrar
        const form = modal.querySelector('form');
        if (form) form.reset();
        modalInstance.show();
      } else {
        console.error('No se encontró el modal de creación.');
      }
    });

    // ...existing code...
  }

  updateStats({ data, pagination }) {
    this.stats.total.textContent       = pagination.total_items || 0;
    this.stats.filtered.textContent    = data.length;
    this.stats.page.textContent        = pagination.current_page || 1;
    const start = ((pagination.current_page || 1) - 1) * (pagination.per_page || 15) + 1;
    const end   = start + data.length - 1;
    const range = `${start}-${end}`;
    this.stats.range.textContent       = range;
    this.stats.totalFooter.textContent  = pagination.total_items || 0;
    this.stats.rangeFooter.textContent  = range;
  }

  renderPagination(p) {
    if ((p.total_pages || 1) <= 1) {
      this.pagination.innerHTML = '';
      return;
    }

    let html = `
      <li class="page-item ${p.current_page === 1 ? 'disabled' : ''}">
        <a class="page-link" href="#" data-page="${p.current_page - 1}">
          <i class="fas fa-chevron-left"></i> Anterior
        </a>
      </li>`;

    const startPage = Math.max(1, p.current_page - 2);
    const endPage   = Math.min(p.total_pages, p.current_page + 2);

    if (startPage > 1) {
      html += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
      if (startPage > 2) {
        html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
      }
    }

    for (let i = startPage; i <= endPage; i++) {
      html += `
        <li class="page-item ${i === p.current_page ? 'active' : ''}">
          <a class="page-link" href="#" data-page="${i}">${i}</a>
        </li>`;
    }

    if (endPage < p.total_pages) {
      if (endPage < p.total_pages - 1) {
        html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
      }
      html += `<li class="page-item"><a class="page-link" href="#" data-page="${p.total_pages}">${p.total_pages}</a></li>`;
    }

    html += `
      <li class="page-item ${p.current_page === p.total_pages ? 'disabled' : ''}">
        <a class="page-link" href="#" data-page="${p.current_page + 1}">
          Siguiente <i class="fas fa-chevron-right ms-1"></i>
        </a>
      </li>`;

    this.pagination.innerHTML = html;
    this.pagination.querySelectorAll('a[data-page]').forEach(a => {
      a.addEventListener('click', e => {
        e.preventDefault();
        const pg = parseInt(a.dataset.page);
        if (!isNaN(pg)) {
          this.currentPage = pg;
          this.load();
        }
      });
    });
  }
}

window.IncidentTypeListController = IncidentTypeListController;
