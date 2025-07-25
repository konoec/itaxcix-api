// assets/js/ui/controllers/MasterTables/TucStatus/tuc-status-list-controller.js

// Controlador para la lista de estados TUC
class TucStatusListController {
  constructor() {
    console.log('🏗️ Inicializando TucStatusListController');

    this.tableBody = document.getElementById('tucStatusTableBody');
    this.stats = {
      total:   document.getElementById('totalTucStatuses'),
      filtered:document.getElementById('filteredTucStatuses'),
      page:    document.getElementById('currentTucStatusPage'),
      range:   document.getElementById('tucStatusesRange'),
      totalFooter: document.getElementById('totalTucStatusesFooter'),
      rangeFooter: document.getElementById('tucStatusesRangeFooter')
    };
    this.filters = {
      search:    document.getElementById('tucStatusSearchInput'),
      name:      document.getElementById('tucStatusNameFilter'),
      active:    document.getElementById('tucStatusActiveFilter'),
      perPage:   document.getElementById('tucStatusPerPageSelect'),
      sortBy:    document.getElementById('tucStatusSortBySelect'),
      sortDirection: document.getElementById('tucStatusSortDirectionSelect')
    };
    this.pagination = document.getElementById('tucStatusPagination');
    this.refreshBtn = document.getElementById('refreshTucStatusBtn');
    this.clearBtn   = document.getElementById('clearTucStatusFiltersBtn');

    this.initEvents();
    this.initEditEvent();
    this.initDeleteEvent();
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
    document.getElementById('clearTucStatusSearchBtn')
      ?.addEventListener('click', () => {
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

      const res = await TucStatusService.getTucStatuses(params);
      const items = res.data.data || [];
      const pg    = res.data.pagination || {};

      this.render(items);
      this.updateStats({ data: items, pagination: pg });
      this.renderPagination(pg);

    } catch (err) {
      console.error(err);
      this.showError(err.message);
      GlobalToast.show('Error al cargar estados TUC: ' + err.message, 'error');
    }
  }

  showLoading() {
    this.tableBody.innerHTML = `
      <tr>
        <td colspan="4" class="text-center py-4">
          <div class="spinner-border spinner-border-sm me-2" role="status"></div>
          Cargando estados TUC...
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
            <i class="fas fa-search me-1"></i>No se encontraron estados TUC
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
            <button class="btn btn-sm btn-outline-warning edit-tuc-status-btn" data-id="${item.id}" data-name="${item.name}" data-active="${item.active}"><i class="fas fa-edit"></i></button>
            <button class="btn btn-sm btn-outline-danger delete-tuc-status-btn" data-id="${item.id}" data-name="${item.name}"><i class="fas fa-trash"></i></button>
          </div>
        </td>
      </tr>
    `).join('');
  }

  initEditEvent() {
    document.addEventListener('click', (e) => {
      const btn = e.target.closest('.edit-tuc-status-btn');
      if (btn) {
        const id = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name');
        const active = btn.getAttribute('data-active') === 'true';
        if (window.TucStatusEditController) {
          window.TucStatusEditController.openEditModal(id, { id, name, active });
        }
      }
    });
  }

  initDeleteEvent() {
    this.tableBody.addEventListener('click', (e) => {
      const btn = e.target.closest('.delete-tuc-status-btn');
      if (btn) {
        const id = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name');
        const tucStatusData = { id: parseInt(id, 10), name };
        if (window.TucStatusDeleteController) {
          window.TucStatusDeleteController.handleDeleteButtonClick(btn, tucStatusData);
        } else if (window.DeleteTucStatusController) {
          window.TucStatusDeleteController = new window.DeleteTucStatusController();
          window.TucStatusDeleteController.handleDeleteButtonClick(btn, tucStatusData);
        } else {
          console.error('❌ Controlador de eliminación de Estado TUC no disponible');
          window.GlobalToast?.show('Error: Funcionalidad de eliminación no disponible', 'error');
        }
      }
    });
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

window.TucStatusListController = TucStatusListController;
