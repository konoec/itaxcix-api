/**
 * Controlador para el módulo de Gestión de Empresas
 * Maneja la carga, filtrado, paginación y CRUD de empresas
 */
class CompanyController {
  constructor() {
    console.log('🏢 Inicializando CompanyController...');

    // Configuración inicial
    this.currentPage     = 1;
    this.perPage         = 15;
    this.totalPages      = 1;
    this.totalItems      = 0;
    this.currentData     = [];
    this.sortBy          = 'id';
    this.sortDirection   = 'asc';
    this.isLoading       = false;
    this.filters         = { search: '', ruc: '', name: '', active: 'all' };

    // Único servicio para todas las operaciones
    this.companyService = new CompanyService();

    // Controlador de edición
    this.editController = new EditCompanyController(
      this.companyService,
      'edit-company-modal',
      'edit-company-form'
    );

    // Elementos del DOM
    this.initializeElements();

    // Listeners
    this.initializeEventListeners();
    this.initializeCreateModalListeners();
  }

  initializeElements() {
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
    this.totalCompaniesEl    = document.getElementById('total-companies');
    this.activeCompaniesEl   = document.getElementById('active-companies');
    this.inactiveCompaniesEl = document.getElementById('inactive-companies');
    this.growthPercentageEl  = document.getElementById('growth-percentage');
    this.exportExcelBtn      = document.getElementById('export-excel-btn');
    this.exportPdfBtn        = document.getElementById('export-pdf-btn');

    console.log('✅ Elementos del DOM inicializados');
  }

  initializeEventListeners() {
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
    this.exportExcelBtn?.addEventListener('click',       () => this.exportToExcel());
    this.exportPdfBtn?.addEventListener('click',         () => this.exportToPdf());

    console.log('✅ Event listeners inicializados');
  }

  initializeCreateModalListeners() {
    const form = document.getElementById('create-company-form');
    form?.addEventListener('submit', e => this.handleCreateSubmit(e));
    console.log('✅ Event listeners del modal de creación inicializados');
  }

  async loadCompanies() {
    if (this.isLoading) return;
    this.isLoading = true;
    this.showLoading();

    try {
      const params = {
        page: this.currentPage,
        perPage: this.perPage,
        sortBy: this.sortBy,
        sortDirection: this.sortDirection,
        ...this.filters
      };
      Object.keys(params).forEach(k => (params[k] === '' || params[k] === 'all') && delete params[k]);

      const res = await this.companyService.getCompanies(params);
      if (!res.success || !res.data) throw new Error(res.message || 'Error');

      this.currentData = res.data.items || [];
      this.updatePagination(res.data.pagination);
      this.renderTable();
      this.updateStatistics();
    } catch (err) {
      console.error(err);
      this.showError(err.message || 'Error al cargar compañías');
      this.currentData = [];
      this.updatePagination({ page:1, perPage:this.perPage, total:0, totalPages:1 });
      this.renderTable();
      this.updateStatistics();
    } finally {
      this.hideLoading();
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
      const res = await this.companyService.createCompany(payload);
      if (res.success) {
        this.showToast('Empresa creada', 'success');
        bootstrap.Modal.getInstance(form.closest('.modal')).hide();
        this.loadCompanies();
      } else {
        this.showToast(res.message || 'Error al crear', 'error');
      }
    } catch (e) {
      console.error(e);
      this.showToast('Error al crear', 'error');
    } finally {
      btn.innerHTML = origTxt;
      btn.disabled  = false;
    }
  }

  handleEditCompany(id) {
    const company = this.currentData.find(c => c.id === id);
    if (!company) return this.showToast('Empresa no encontrada','error');
    this.editController.open(company);
  }

  async handleDeleteCompany(id, name) {
    // ... igual que antes, llamando this.companyService.deleteCompany(id)
  }

  // Resto de métodos inalterados...
}

// Hacer disponible globalmente
window.CompanyController = CompanyController;
