/**
 * Controlador para el módulo de Gestión de Empresas
 * Recibe sus 4 servicios desde el inicializador: listar, crear, actualizar y eliminar
 */
class CompanyController {
  /**
   * @param {CompanyService}       listService
   * @param {CompanyCreateService} createService
   * @param {CompanyUpdateService} updateService
   * @param {CompanyDeleteService} deleteService
   */
  constructor(listService, createService, updateService, deleteService) {
    console.log('🏢 Inicializando CompanyController...');

    // Estado interno
    this.currentPage    = 1;
    this.perPage        = 15;
    this.totalPages     = 1;
    this.totalItems     = 0;
    this.currentData    = [];
    this.sortBy         = 'id';
    this.sortDirection  = 'asc';
    this.isLoading      = false;
    this.filters        = { search: '', ruc: '', active: 'all' };

    // Servicios inyectados
    this.listService    = listService;
    this.createService  = createService;
    this.updateService  = updateService;
    this.deleteService  = deleteService;

    // Controlador de edición
    this.editController = new EditCompanyController(
      this.updateService,
      'edit-company-modal',
      'edit-company-form'
    );

    // Elementos DOM
    this._initElements();

    // Listeners
    this._initEventListeners();
    this._initCreateModalListeners();

    // Carga inicial
    this.loadCompanies();
  }

  _initElements() {
  this.loadingContainer    = document.getElementById('companies-loading');
  this.contentContainer    = document.getElementById('companies-content');
  this.searchInput         = document.getElementById('company-search-input');
  this.statusFilter        = document.getElementById('company-status-filter');
  this.rucFilter           = document.getElementById('company-ruc-filter');
  this.sortBySelect        = document.getElementById('sort-by-select');
  this.sortDirectionSelect = document.getElementById('sort-direction-select');
  this.searchBtn           = document.getElementById('search-btn');
  this.clearFiltersBtn     = document.getElementById('clear-filters-btn');
  this.refreshBtn          = document.getElementById('refresh-companies-btn');
  this.addCompanyBtn       = document.getElementById('add-company-btn');
  this.tableBody           = document.getElementById('companies-table-body');
  this.showingStart        = document.getElementById('showing-start');
  this.showingEnd          = document.getElementById('showing-end');
  this.totalRecords        = document.getElementById('total-records');
  this.paginationContainer = document.querySelector('#pagination-container ul.pagination');
  this.perPageSelect       = document.getElementById('companies-per-page');
  this.totalCompaniesEl    = document.getElementById('total-companies');
  this.activeCompaniesEl   = document.getElementById('active-companies');
  this.inactiveCompaniesEl = document.getElementById('inactive-companies');
  console.log('✅ Elementos del DOM inicializados');
}


  _initEventListeners() {
  this.searchBtn?.addEventListener('click',   () => this.handleSearch());
  this.searchInput?.addEventListener('keypress', e => e.key === 'Enter' && this.handleSearch());
  this.statusFilter?.addEventListener('change', () => this.applyFilters());
  this.rucFilter?.addEventListener('input', () => {
    clearTimeout(this.filterTimeout);
    this.filterTimeout = setTimeout(() => this.applyFilters(), 500);
  });
  this.sortBySelect?.addEventListener('change',        () => this.handleSortChange());
  this.sortDirectionSelect?.addEventListener('change', () => this.handleSortChange());
  this.clearFiltersBtn?.addEventListener('click',      () => this.clearFilters());
  this.refreshBtn?.addEventListener('click',           () => this.refreshData());
  this.addCompanyBtn?.addEventListener('click',        () => this.handleAddCompany());

  this.perPageSelect?.addEventListener('change', () => {
    this.perPage = parseInt(this.perPageSelect.value, 10) || 15;
    this.currentPage = 1;
    this.loadCompanies();
});


  console.log('✅ Event listeners inicializados');
}


  _initCreateModalListeners() {
    const form = document.getElementById('create-company-form');
    form?.addEventListener('submit', e => this.handleCreateSubmit(e));
    console.log('✅ Event listeners del modal de creación inicializados');
  }

  async loadCompanies() {
    if (this.isLoading) return;
    this.isLoading = true;
    this._showLoading();

    try {
      const params = {
        page: this.currentPage,
        perPage: this.perPage,
        sortBy: this.sortBy,
        sortDirection: this.sortDirection,
        ...this.filters
      };
      Object.keys(params).forEach(k =>
        (params[k] === '' || params[k] === 'all') && delete params[k]
      );

      const res = await this.listService.getCompanies(params);
      if (!res.success || !res.data) throw new Error(res.message || 'Error');

      this.currentData = res.data.items;
      // 👇 SINCRONIZA EL SELECT
      if (this.perPageSelect && res.data.pagination && res.data.pagination.perPage) {
      this.perPageSelect.value = res.data.pagination.perPage;}
      this._updatePagination(res.data.pagination);
      this._renderTable();
      this._updateStatistics();
      
    } catch (err) {
      console.error(err);
      this._showError(err.message || 'Error al cargar compañías');
      this.currentData = [];
      this._updatePagination({ page:1, perPage:this.perPage, total:0, totalPages:1 });
      this._renderTable();
      this._updateStatistics();
    } finally {
      this._hideLoading();
      this.isLoading = false;
    }
  }

  async handleCreateSubmit(event) {
    event.preventDefault();
    const form    = event.target;
    const data    = new FormData(form);
    const payload = {
      name:   data.get('name'),
      ruc:    data.get('ruc'),
      active: data.get('active') === 'on'
    };

    const btn     = form.querySelector('button[type=submit]');
    const origTxt = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Creando';
    btn.disabled  = true;

    try {
      const res = await this.createService.create(payload);
      if (res.success) {
        this._toast('Empresa creada', 'success');
        bootstrap.Modal.getInstance(form.closest('.modal')).hide();
        this.loadCompanies();
      } else {
        this._toast(res.message || 'Error al crear', 'error');
      }
    } catch (e) {
      console.error(e);
      this._toast('Error al crear', 'error');
    } finally {
      btn.innerHTML = origTxt;
      btn.disabled  = false;
    }
  }

  handleSearch() {
    this.filters.search = this.searchInput.value.trim();
    this.currentPage    = 1;
    this.loadCompanies();
  }

  applyFilters() {
    this.filters.active = this.statusFilter.value;
    this.filters.ruc    = this.rucFilter.value.trim();
    this.currentPage    = 1;
    this.loadCompanies();
  }

  clearFilters() {
    this.filters = { search:'', ruc:'', active:'all' };
    this.searchInput.value         = '';
    this.statusFilter.value        = 'all';
    this.rucFilter.value           = '';
    this.sortBySelect.value        = 'id';
    this.sortDirectionSelect.value = 'asc';
    this.sortBy        = 'id';
    this.sortDirection = 'asc';
    this.currentPage   = 1;
    this.loadCompanies();
  }

  handleSortChange() {
    this.sortBy        = this.sortBySelect.value;
    this.sortDirection = this.sortDirectionSelect.value;
    this.currentPage   = 1;
    this.loadCompanies();
  }

  refreshData() {
    this.listService.clearCache?.();
    this.loadCompanies();
    this._toast('Datos actualizados correctamente', 'success');
  }

  handleAddCompany() {
    this._clearCreateForm();
    new bootstrap.Modal(document.getElementById('create-company-modal')).show();
  }

  handleEditCompany(id) {
    const company = this.currentData.find(c => c.id === id);
    if (!company) return this._toast('Empresa no encontrada', 'error');
    this.editController.open(company);
  }

  handleDeleteCompany(id, name) {
    window.globalConfirmationModal?.showConfirmation({
      title:    'Eliminar empresa',
      name,
      subtitle: 'Esta acción no se puede deshacer',
      onConfirm: async () => {
        try {
          const res = await this.deleteService.delete(id);
          if (res.success) {
            this._toast('Empresa eliminada', 'success');
            this.loadCompanies();
          } else {
            throw new Error(res.message);
          }
        } catch (err) {
          console.error(err);
          this._toast('Error al eliminar', 'error');
        }
      }
    });
  }

  // ---- Métodos privados de renderizado y utilitarios ----

  _showLoading() {
    this.loadingContainer && (this.loadingContainer.style.display = '');
    this.contentContainer && (this.contentContainer.style.display = 'none');
  }
  _hideLoading() {
    this.loadingContainer && (this.loadingContainer.style.display = 'none');
    this.contentContainer && (this.contentContainer.style.display = '');
  }

  _renderTable() {
    if (!this.tableBody) return;
    if (!this.currentData.length) {
      this.tableBody.innerHTML = `
        <tr><td colspan="6" class="text-center py-4">
          <div class="empty">
            <i class="fas fa-building fa-2x text-secondary mb-2"></i>
            <p>No se encontraron compañías</p>
            <button class="btn btn-azure mt-2" onclick="window.companyController.clearFilters()">
              <i class="fas fa-filter me-1"></i> Limpiar filtros
            </button>
          </div>
        </td></tr>`;
      return;
    }
    this.tableBody.innerHTML = this.currentData.map(c => `
      <tr>
        <td>${c.id}</td>
        <td>${this.escapeHtml(c.name)}</td>
        <td>${c.ruc || 'Sin RUC'}</td>
        <td>
          <span class="badge ${c.active?'bg-success-lt':'bg-danger-lt'}">
          <i class="fas ${c.active ? 'fa-check-circle ' : 'fa-times-circle'} me-1"></i>
            ${c.active?'Activo':'Inactivo'}
          </span>
        </td>
        <td>
          <button class="btn btn-sm btn-outline-warning" onclick="window.companyController.handleEditCompany(${c.id})">
            <i class="fas fa-edit"></i>
          </button>
          <button class="btn btn-sm btn-outline-danger" onclick="window.companyController.handleDeleteCompany(${c.id},'${this.escapeHtml(c.name)}')">
            <i class="fas fa-trash"></i>
          </button>
        </td>
      </tr>
    `).join('');
  }

  _updatePagination({ page, perPage, total, totalPages }) {
    this.currentPage = page;
    this.perPage     = perPage;
    this.totalItems  = total;
    this.totalPages  = totalPages;

    // Asegura que el select refleja el valor correcto
    if (this.perPageSelect && parseInt(this.perPageSelect.value, 10) !== perPage) {
        this.perPageSelect.value = perPage;
    }

    const start = total ? (page - 1) * perPage + 1 : 0;
    const end   = total ? Math.min(page * perPage, total) : 0;
    this.showingStart.textContent = start;
    this.showingEnd.textContent   = end;
    this.totalRecords.textContent = total;

    this._renderPaginationButtons();
}


  _renderPaginationButtons() {
  if (!this.paginationContainer) return;
  const ul = this.paginationContainer;
  ul.innerHTML = '';
  const totalPages = this.totalPages || 1;
  const currentPage = this.currentPage || 1;
  const delta = 2; // Cuántos botones a la izquierda y derecha

  // Helper para crear cada <li>
  const makeItem = (html, p, disabled=false, active=false, isDots=false) => {
    const li = document.createElement('li');
    li.className = `page-item${disabled?' disabled':''}${active?' active':''}${isDots?' disabled':''}`;
    if (isDots) {
      li.innerHTML = `<span class="page-link">…</span>`;
      return li;
    }
    const a = document.createElement('a');
    a.className = 'page-link';
    a.href      = '#';
    a.innerHTML = html;
    if (!disabled && !active) {
      a.addEventListener('click', e => {
        e.preventDefault();
        this.currentPage = p;
        this.loadCompanies();
      });
    }
    li.appendChild(a);
    return li;
  };

  // Flecha anterior
  ul.appendChild(makeItem('<i class="fas fa-chevron-left"></i>', currentPage - 1, currentPage === 1));

  // Lógica para ... y extremos
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

  // Flecha siguiente
  ul.appendChild(makeItem('<i class="fas fa-chevron-right"></i>', currentPage + 1, currentPage === totalPages));
}


  _updateStatistics() {
    const total    = this.totalItems;
    const activos  = this.currentData.filter(c => c.active).length;
    const inactivos= total - activos;
    this.totalCompaniesEl.textContent   = total;
    this.activeCompaniesEl.textContent  = activos;
    this.inactiveCompaniesEl.textContent= inactivos;
  }

  escapeHtml(str) {
    return String(str)
      .replace(/&/g,'&amp;')
      .replace(/</g,'&lt;')
      .replace(/>/g,'&gt;')
      .replace(/"/g,'&quot;')
      .replace(/'/g,'&#039;');
  }

  _clearCreateForm() {
    const form = document.getElementById('create-company-form');
    if (!form) return;
    form.reset();
    form.querySelectorAll('.is-valid, .is-invalid').forEach(i => i.classList.remove('is-valid','is-invalid'));
    form.querySelectorAll('.invalid-feedback').forEach(f => f.textContent = '');
    const chk = document.getElementById('company-active');
    if (chk) chk.checked = true;
  }

  _toast(msg, type='info') {
    if (window.showRecoveryToast) window.showRecoveryToast(msg, type);
    else alert(`${type.toUpperCase()}: ${msg}`);
  }

  _showError(msg) { this._toast(msg, 'error'); }
}

// Hacer disponible globalmente
window.CompanyController = CompanyController;
