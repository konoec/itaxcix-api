/**
 * Controlador para el módulo de Gestión de Empresas
 * Maneja la carga, filtrado, paginación y CRUD de empresas
 */
class CompanyController {
    constructor() {
        console.log('🏢 Inicializando CompanyController...');
        
        // Configuración inicial
        this.currentPage = 1;
        this.perPage = 15;
        this.totalPages = 1;
        this.totalItems = 0;
        this.currentData = [];
        this.sortBy = 'id';
        this.sortDirection = 'asc';
        this.isLoading = false;
        
        // Filtros actuales
        this.filters = {
            search: '',
            ruc: '',
            name: '',
            active: 'all'
        };
        
        // Servicios
        this.companyService = new CompanyService();
        
        // Elementos del DOM
        this.initializeElements();
        
        // Event listeners
        this.initializeEventListeners();
        
        // Event listeners del modal
        this.initializeCreateModalListeners();
        this.initializeEditModalListeners();
        
        // Cargar datos iniciales
        this.loadCompanies();
    }
    
    /**
     * Inicializa las referencias a elementos del DOM
     */
    initializeElements() {
        // Contenedores principales
        this.loadingContainer = document.getElementById('companies-loading');
        this.contentContainer = document.getElementById('companies-content');
        
        // Filtros y búsqueda
        this.searchInput = document.getElementById('company-search-input');
        this.statusFilter = document.getElementById('company-status-filter');
        this.rucFilter = document.getElementById('company-ruc-filter');
        this.sortBySelect = document.getElementById('sort-by-select');
        this.sortDirectionSelect = document.getElementById('sort-direction-select');
        
        // Botones de filtro
        this.searchBtn = document.getElementById('search-btn');
        this.clearFiltersBtn = document.getElementById('clear-filters-btn');
        
        // Botones de acción
        this.refreshBtn = document.getElementById('refresh-companies-btn');
        this.addCompanyBtn = document.getElementById('add-company-btn');
        
        // Tabla
        this.tableBody = document.getElementById('companies-table-body');
        
        // Paginación
        this.showingStart = document.getElementById('showing-start');
        this.showingEnd = document.getElementById('showing-end');
        this.totalRecords = document.getElementById('total-records');
        this.currentPageInfo = document.getElementById('current-page-info');
        this.prevPageBtn = document.getElementById('prev-page-btn');
        this.nextPageBtn = document.getElementById('next-page-btn');
        
        // Estadísticas
        this.totalCompaniesEl = document.getElementById('total-companies');
        this.activeCompaniesEl = document.getElementById('active-companies');
        this.inactiveCompaniesEl = document.getElementById('inactive-companies');
        this.growthPercentageEl = document.getElementById('growth-percentage');
        
        // Botones de exportación
        this.exportExcelBtn = document.getElementById('export-excel-btn');
        this.exportPdfBtn = document.getElementById('export-pdf-btn');
        
        console.log('✅ Elementos del DOM inicializados');
    }
    
    /**
     * Inicializa los event listeners
     */
    initializeEventListeners() {
        // Búsqueda y filtros
        if (this.searchBtn) {
            this.searchBtn.addEventListener('click', () => this.handleSearch());
        }
        
        if (this.searchInput) {
            this.searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    this.handleSearch();
                }
            });
        }
        
        if (this.statusFilter) {
            this.statusFilter.addEventListener('change', () => this.applyFilters());
        }
        
        if (this.rucFilter) {
            this.rucFilter.addEventListener('input', () => {
                // Aplicar filtros después de un pequeño delay
                clearTimeout(this.filterTimeout);
                this.filterTimeout = setTimeout(() => this.applyFilters(), 500);
            });
        }
        
        if (this.sortBySelect) {
            this.sortBySelect.addEventListener('change', () => this.handleSortChange());
        }
        
        if (this.sortDirectionSelect) {
            this.sortDirectionSelect.addEventListener('change', () => this.handleSortChange());
        }
        
        if (this.clearFiltersBtn) {
            this.clearFiltersBtn.addEventListener('click', () => this.clearFilters());
        }
        
        // Botones de acción
        if (this.refreshBtn) {
            this.refreshBtn.addEventListener('click', () => this.refreshData());
        }
        
        if (this.addCompanyBtn) {
            this.addCompanyBtn.addEventListener('click', () => this.handleAddCompany());
        }
        
        // Paginación
        if (this.prevPageBtn) {
            this.prevPageBtn.addEventListener('click', () => this.goToPreviousPage());
        }
        
        if (this.nextPageBtn) {
            this.nextPageBtn.addEventListener('click', () => this.goToNextPage());
        }
        
        // Seleccionar todos
        if (this.selectAllCheckbox) {
            this.selectAllCheckbox.addEventListener('change', () => this.handleSelectAll());
        }
        
        // Exportación
        if (this.exportExcelBtn) {
            this.exportExcelBtn.addEventListener('click', () => this.exportToExcel());
        }
        
        if (this.exportPdfBtn) {
            this.exportPdfBtn.addEventListener('click', () => this.exportToPdf());
        }
        
        console.log('✅ Event listeners inicializados');
    }
    
    /**
     * Carga los datos de compañías
     */
    async loadCompanies() {
        if (this.isLoading) return;
        
        console.log('🏢 Cargando compañías...');
        console.log('🌐 API ACTIVADA - Conectando a servidor...');
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
            
            // Limpiar parámetros vacíos
            Object.keys(params).forEach(key => {
                if (params[key] === '' || params[key] === 'all') {
                    delete params[key];
                }
            });
            
            console.log('📡 Parámetros enviados a API:', params);
            const response = await this.companyService.getCompanies(params);
            console.log('📥 Respuesta recibida:', response);
            
            if (response.success && response.data) {
                this.currentData = response.data.items || [];
                this.updatePagination(response.data.pagination);
                this.renderTable();
                this.updateStatistics();
                this.hideLoading();
                console.log('✅ Datos cargados - Total items:', this.currentData.length);
            } else {
                console.warn('⚠️ Respuesta sin datos válidos:', response);
                throw new Error(response.message || 'Error al cargar compañías');
            }
            
        } catch (error) {
            console.error('❌ Error al cargar compañías:', error);
            
            // Mostrar mensaje de error específico
            let errorMessage = 'Error al cargar compañías';
            if (error.message.includes('token')) {
                errorMessage = 'Error de autenticación. Por favor, inicie sesión nuevamente.';
            } else if (error.message.includes('Timeout')) {
                errorMessage = 'La conexión tardó demasiado. Verifique su conexión a internet.';
            } else if (error.message.includes('HTTP')) {
                errorMessage = 'Error del servidor. Intente nuevamente en unos momentos.';
            } else {
                errorMessage = `Error: ${error.message}`;
            }
            
            this.showError(errorMessage);
            
            // Mostrar tabla vacía con mensaje de error
            this.currentData = [];
            this.updatePagination({ page: 1, perPage: 15, total: 0, totalPages: 0 });
            this.renderTable();
            this.updateStatistics();
            this.hideLoading();
        } finally {
            this.isLoading = false;
        }
    }
    
    /**
     * Renderiza la tabla de compañías
     */
    renderTable() {
        if (!this.tableBody) return;
        
        console.log('🏢 Renderizando tabla de compañías...');
        
        if (!this.currentData || this.currentData.length === 0) {
            this.tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <div class="empty">
                            <div class="empty-icon">
                                <i class="fas fa-building fa-2x text-secondary"></i>
                            </div>
                            <p class="empty-title">No se encontraron compañías</p>
                            <p class="empty-subtitle text-muted">
                                No hay compañías que coincidan con los filtros aplicados
                            </p>
                            <div class="empty-action">
                                <button class="btn btn-azure" onclick="window.companyController.clearFilters()">
                                    <i class="fas fa-filter me-2 text-white"></i>
                                    Limpiar filtros
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }
        
        this.tableBody.innerHTML = this.currentData.map((company, index) => `
            <tr>
                <td>
                    <div class="fw-bold text-primary">${company.id}</div>
                    <div class="text-muted small">ID</div>
                </td>
                <td>
                    <div class="d-flex py-1 align-items-center">
                        <span class="avatar me-2 ${company.active ? 'bg-lime' : 'bg-red'}" 
                              style="background-image: none;">
                            <i class="fas fa-building text-white"></i>
                        </span>
                        <div class="flex-fill">
                            <div class="fw-bold">${this.escapeHtml(company.name || 'Sin nombre')}</div>
                            <div class="text-muted">Empresa</div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="fw-bold">${company.ruc || 'Sin RUC'}</div>
                    <div class="text-muted">RUC</div>
                </td>
                <td>
                    <span class="badge ${company.active ? 'bg-lime text-white' : 'bg-red text-white'}">
                        <i class="fas ${company.active ? 'fa-check-circle text-white' : 'fa-times-circle text-white'} me-1"></i>
                        ${company.active ? 'Activo' : 'Inactivo'}
                    </span>
                </td>
                <td>
                    <div>${company.created_at ? this.formatDate(company.created_at) : 'No disponible'}</div>
                    <div class="text-muted">${company.created_at ? this.formatTime(company.created_at) : ''}</div>
                </td>
                <td>
                    <div class="btn-list flex-nowrap">
                        <button class="btn btn-sm btn-outline-orange" 
                                onclick="window.companyController.handleEditCompany(${company.id})"
                                title="Editar">
                            <i class="fas fa-edit text-orange"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-red" 
                                onclick="window.companyController.handleDeleteCompany(${company.id}, '${this.escapeHtml(company.name || 'Sin nombre')}')"
                                title="Eliminar">
                            <i class="fas fa-trash text-red"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
        
        console.log('✅ Tabla renderizada con', this.currentData.length, 'compañías');
    }
    
    /**
     * Actualiza la información de paginación
     */
    updatePagination(pagination) {
        if (!pagination) return;
        
        this.currentPage = pagination.page || 1;
        this.totalPages = pagination.totalPages || 1;
        this.totalItems = pagination.total || 0;
        
        // Actualizar elementos de paginación
        if (this.showingStart) {
            const start = ((this.currentPage - 1) * this.perPage) + 1;
            this.showingStart.textContent = this.totalItems > 0 ? start : 0;
        }
        
        if (this.showingEnd) {
            const end = Math.min(this.currentPage * this.perPage, this.totalItems);
            this.showingEnd.textContent = end;
        }
        
        if (this.totalRecords) {
            this.totalRecords.textContent = this.totalItems;
        }
        
        if (this.currentPageInfo) {
            this.currentPageInfo.textContent = `${this.currentPage} de ${this.totalPages}`;
        }
        
        // Habilitar/deshabilitar botones de paginación
        if (this.prevPageBtn) {
            this.prevPageBtn.disabled = this.currentPage <= 1;
        }
        
        if (this.nextPageBtn) {
            this.nextPageBtn.disabled = this.currentPage >= this.totalPages;
        }
        
        console.log('✅ Paginación actualizada:', {
            currentPage: this.currentPage,
            totalPages: this.totalPages,
            totalItems: this.totalItems
        });
    }
    
    /**
     * Actualiza las estadísticas de compañías
     */
    updateStatistics() {
        if (!this.currentData) return;
        
        const totalCompanies = this.totalItems;
        const activeCompanies = this.currentData.filter(c => c.active).length;
        const inactiveCompanies = this.currentData.filter(c => !c.active).length;
        
        if (this.totalCompaniesEl) {
            this.totalCompaniesEl.textContent = totalCompanies;
        }
        
        if (this.activeCompaniesEl) {
            this.activeCompaniesEl.textContent = activeCompanies;
        }
        
        if (this.inactiveCompaniesEl) {
            this.inactiveCompaniesEl.textContent = inactiveCompanies;
        }
        
        // Calcular crecimiento (simulado por ahora)
        const growthPercentage = Math.floor(Math.random() * 20) + 5; // Simulado
        if (this.growthPercentageEl) {
            this.growthPercentageEl.textContent = `+${growthPercentage}%`;
        }
        
        // Generar sparklines para cada métrica
        this.generateSparklines({
            totalCompanies,
            activeCompanies,
            inactiveCompanies,
            growthPercentage
        });
        
        console.log('✅ Estadísticas actualizadas:', {
            total: totalCompanies,
            active: activeCompanies,
            inactive: inactiveCompanies,
            growth: growthPercentage
        });
    }
    
    /**
     * Maneja la búsqueda
     */
    handleSearch() {
        if (this.searchInput) {
            this.filters.search = this.searchInput.value.trim();
        }
        this.currentPage = 1;
        this.loadCompanies();
    }
    
    /**
     * Aplica los filtros
     */
    applyFilters() {
        // Actualizar filtros
        if (this.statusFilter) {
            this.filters.active = this.statusFilter.value;
        }
        
        if (this.rucFilter) {
            this.filters.ruc = this.rucFilter.value.trim();
        }
        
        this.currentPage = 1;
        this.loadCompanies();
    }
    
    /**
     * Limpia todos los filtros
     */
    clearFilters() {
        console.log('🏢 Limpiando filtros...');
        
        // Limpiar filtros
        this.filters = {
            search: '',
            ruc: '',
            name: '',
            active: 'all'
        };
        
        // Limpiar inputs
        if (this.searchInput) this.searchInput.value = '';
        if (this.statusFilter) this.statusFilter.value = 'all';
        if (this.rucFilter) this.rucFilter.value = '';
        if (this.sortBySelect) this.sortBySelect.value = 'id';
        if (this.sortDirectionSelect) this.sortDirectionSelect.value = 'asc';
        
        // Resetear ordenamiento
        this.sortBy = 'id';
        this.sortDirection = 'asc';
        this.currentPage = 1;
        
        this.loadCompanies();
    }
    
    /**
     * Maneja el cambio de ordenamiento
     */
    handleSortChange() {
        if (this.sortBySelect) {
            this.sortBy = this.sortBySelect.value;
        }
        
        if (this.sortDirectionSelect) {
            this.sortDirection = this.sortDirectionSelect.value;
        }
        
        this.currentPage = 1;
        this.loadCompanies();
    }
    
    /**
     * Refresca los datos
     */
    refreshData() {
        console.log('🏢 Refrescando datos...');
        this.companyService.clearCache();
        this.loadCompanies();
        this.showToast('Datos actualizados correctamente', 'success');
    }
    
    /**
     * Va a la página anterior
     */
    goToPreviousPage() {
        if (this.currentPage > 1) {
            this.currentPage--;
            this.loadCompanies();
        }
    }
    
    /**
     * Va a la página siguiente
     */
    goToNextPage() {
        if (this.currentPage < this.totalPages) {
            this.currentPage++;
            this.loadCompanies();
        }
    }
    
    /**
     * Maneja la selección de todas las compañías
     */
    handleSelectAll() {
        const checkboxes = document.querySelectorAll('.company-checkbox');
        const isChecked = this.selectAllCheckbox.checked;
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
        });
        
        console.log('🏢 Selección masiva:', isChecked ? 'activada' : 'desactivada');
    }
    
    /**
     * Maneja la adición de una nueva compañía
     */
    handleAddCompany() {
        console.log('🏢 Abriendo modal para nueva compañía...');
        
        // Limpiar el formulario
        this.clearCreateForm();
        
        // Mostrar el modal
        const modal = document.getElementById('create-company-modal');
        if (modal) {
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        } else {
            console.error('❌ Modal create-company-modal no encontrado');
            this.showToast('Error al abrir el modal', 'error');
        }
    }
    
    /**
     * Maneja la visualización de una compañía
     */
    
    /**
     * Maneja la edición de una compañía
     */
    async handleEditCompany(id) {
        console.log('🏢 Editando compañía:', id);
        
        try {
            // Obtener datos de la empresa
            const companies = this.currentData || [];
            const company = companies.find(c => c.id === parseInt(id));
            
            if (!company) {
                this.showToast('Empresa no encontrada', 'error');
                return;
            }
            
            // Llenar el formulario con los datos actuales
            this.fillEditForm(company);
            
            // Mostrar el modal
            const modal = document.getElementById('edit-company-modal');
            if (modal) {
                const bsModal = new bootstrap.Modal(modal);
                bsModal.show();
            } else {
                console.error('❌ Modal edit-company-modal no encontrado');
                this.showToast('Error al abrir el modal', 'error');
            }
            
        } catch (error) {
            console.error('❌ Error al cargar datos de empresa:', error);
            this.showToast('Error al cargar datos de la empresa', 'error');
        }
    }
    
    /**
     * Maneja la eliminación de una compañía
     */
    async handleDeleteCompany(id, name) {
        console.log('🏢 Eliminando empresa:', id, name);
        
        // Confirmación nativa del navegador
        const confirmed = confirm(
            `¿Está seguro de que desea eliminar la empresa "${name}"?\n\n` +
            `Esta acción no se puede deshacer.`
        );
        if (!confirmed) return;
        
        try {
            // Mostrar toast de progreso
            this.showToast('Eliminando empresa...', 'info');
            
            // Llamar al servicio para eliminar
            const response = await this.companyService.deleteCompany(id);
            
            if (response.success) {
                this.showToast('Empresa eliminada exitosamente', 'success');
                
                // Recargar la lista de empresas
                this.loadCompanies();
            } else {
                throw new Error(response.message || 'Error al eliminar empresa');
            }
            
        } catch (error) {
            console.error('❌ Error al eliminar empresa:', error);
            this.showToast('Error al eliminar empresa: ' + error.message, 'error');
        }
    }
    
    /**
     * Exporta a Excel
     */
    exportToExcel() {
        console.log('🏢 Exportando a Excel...');
        this.showToast('Funcionalidad en desarrollo', 'info');
    }
    
    /**
     * Exporta a PDF
     */
    exportToPdf() {
        console.log('🏢 Exportando a PDF...');
        this.showToast('Funcionalidad en desarrollo', 'info');
    }
    
    /**
     * Muestra el estado de carga
     */
    showLoading() {
        if (this.loadingContainer) {
            this.loadingContainer.style.display = 'block';
        }
        if (this.contentContainer) {
            this.contentContainer.style.display = 'none';
        }
    }
    
    /**
     * Oculta el estado de carga
     */
    hideLoading() {
        if (this.loadingContainer) {
            this.loadingContainer.style.display = 'none';
        }
        if (this.contentContainer) {
            this.contentContainer.style.display = 'block';
        }
    }
    
    /**
     * Muestra un error
     */
    showError(message) {
        console.error('❌ Error:', message);
        this.showToast(message, 'error');
        this.hideLoading();
    }
    
    /**
     * Muestra una notificación toast
     */
    showToast(message, type = 'info') {
        if (window.showToast) {
            window.showToast(message, type);
        } else {
            console.log(`Toast (${type}):`, message);
        }
    }
    
    /**
     * Escapa HTML para prevenir XSS
     */
    escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, (m) => map[m]);
    }
    
    /**
     * Limpia el formulario de creación de empresa
     */
    clearCreateForm() {
        const form = document.getElementById('create-company-form');
        if (form) {
            form.reset();
            
            // Limpiar validaciones visuales
            const inputs = form.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.classList.remove('is-valid', 'is-invalid');
            });
            
            // Limpiar mensajes de error
            const errorMessages = form.querySelectorAll('.invalid-feedback');
            errorMessages.forEach(error => {
                error.textContent = '';
            });
            
            // Resetear el estado activo por defecto
            const activeCheckbox = document.getElementById('company-active');
            if (activeCheckbox) {
                activeCheckbox.checked = true;
            }
            
            console.log('✅ Formulario de creación limpiado');
        }
    }
    
    /**
     * Limpia el formulario de edición de empresa
     */
    clearEditForm() {
        const form = document.getElementById('edit-company-form');
        if (form) {
            form.reset();
            
            // Limpiar validaciones visuales
            const inputs = form.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.classList.remove('is-valid', 'is-invalid');
            });
            
            // Limpiar mensajes de error
            const errorMessages = form.querySelectorAll('.invalid-feedback');
            errorMessages.forEach(error => {
                error.textContent = '';
            });
            
            console.log('✅ Formulario de edición limpiado');
        }
    }
    
    /**
     * Inicializa los event listeners del modal de creación
     */
    initializeCreateModalListeners() {
        const form = document.getElementById('create-company-form');
        if (form) {
            form.addEventListener('submit', (e) => this.handleCreateSubmit(e));
        }
        
        console.log('✅ Event listeners del modal de creación inicializados');
    }
    
    /**
     * Inicializa los event listeners del modal de edición
     */
    initializeEditModalListeners() {
        const form = document.getElementById('edit-company-form');
        if (form) {
            form.addEventListener('submit', (e) => this.handleEditSubmit(e));
        }
        
        console.log('✅ Event listeners del modal de edición inicializados');
    }
    
    /**
     * Llena el formulario de edición con los datos de la empresa
     */
    fillEditForm(company) {
        document.getElementById('edit-company-id').value = company.id;
        document.getElementById('edit-company-ruc').value = company.ruc || '';
        document.getElementById('edit-company-name').value = company.name || '';
        document.getElementById('edit-company-active').checked = company.active || false;
        
        // Limpiar validaciones visuales
        const form = document.getElementById('edit-company-form');
        if (form) {
            const inputs = form.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.classList.remove('is-valid', 'is-invalid');
            });
            
            // Limpiar mensajes de error
            const errorMessages = form.querySelectorAll('.invalid-feedback');
            errorMessages.forEach(error => {
                error.textContent = '';
            });
        }
        
        console.log('✅ Formulario de edición llenado con datos:', company);
    }
    
    /**
     * Maneja el envío del formulario de edición
     */
    async handleEditSubmit(event) {
        event.preventDefault();
        
        const form = event.target;
        const formData = new FormData(form);
        const companyId = formData.get('id');
        
        // Preparar datos para enviar
        const companyData = {
            name: formData.get('name'),
            ruc: formData.get('ruc'),
            active: formData.get('active') === 'on' || document.getElementById('edit-company-active').checked
        };
        
        console.log('📤 Actualizando empresa:', companyId, companyData);
        
        try {
            // Mostrar loading en el botón
            const submitBtn = document.getElementById('edit-company-submit');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Actualizando...';
            submitBtn.disabled = true;
            
            // Llamar al servicio para actualizar la empresa
            const response = await this.companyService.updateCompany(companyId, companyData);
            
            if (response.success) {
                this.showToast('Empresa actualizada exitosamente', 'success');
                
                // Cerrar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('edit-company-modal'));
                if (modal) {
                    modal.hide();
                }
                
                // Recargar datos
                this.loadCompanies();
            } else {
                this.showToast('Error al actualizar la empresa', 'error');
            }
            
        } catch (error) {
            console.error('❌ Error al actualizar empresa:', error);
            this.showToast('Error al actualizar la empresa', 'error');
        } finally {
            // Restaurar botón
            const submitBtn = document.getElementById('edit-company-submit');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    }
    
    /**
     * Maneja el envío del formulario de creación
     */
    async handleCreateSubmit(event) {
        event.preventDefault();
        
        const form = event.target;
        const formData = new FormData(form);
        
        // Preparar datos para enviar
        const companyData = {
            name: formData.get('name'),
            ruc: formData.get('ruc'),
            active: formData.get('active') === 'on' || document.getElementById('company-active').checked
        };
        
        console.log('📤 Enviando datos de empresa:', companyData);
        
        try {
            // Mostrar loading en el botón
            const submitBtn = document.getElementById('create-company-submit');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creando...';
            submitBtn.disabled = true;
            
            // Llamar al servicio para crear la empresa
            const response = await this.companyService.createCompany(companyData);
            
            if (response.success) {
                this.showToast('Empresa creada exitosamente', 'success');
                
                // Cerrar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('create-company-modal'));
                if (modal) {
                    modal.hide();
                }
                
                // Recargar datos
                this.loadCompanies();
            } else {
                this.showToast('Error al crear la empresa', 'error');
            }
            
        } catch (error) {
            console.error('❌ Error al crear empresa:', error);
            this.showToast('Error al crear la empresa', 'error');
        } finally {
            // Restaurar botón
            const submitBtn = document.getElementById('create-company-submit');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    }

    /**
     * Genera sparklines para las métricas de empresas
     */
    generateSparklines(metrics) {
        // Generar datos simulados para sparklines
        const baseData = {
            totalCompanies: this.generateSparklineData(metrics.totalCompanies, 'growth'),
            activeCompanies: this.generateSparklineData(metrics.activeCompanies, 'stable'),
            inactiveCompanies: this.generateSparklineData(metrics.inactiveCompanies, 'decline'),
            growth: this.generateSparklineData(metrics.growthPercentage, 'volatile')
        };

        // Crear sparklines
        this.createSparkline('sparkline-total-companies', baseData.totalCompanies, '#206bc4', 'line');
        this.createSparkline('sparkline-active-companies', baseData.activeCompanies, '#2fb344', 'area');
        this.createSparkline('sparkline-inactive-companies', baseData.inactiveCompanies, '#d63384', 'line');
        this.createSparkline('sparkline-growth', baseData.growth, '#9c27b0', 'area');
    }

    /**
     * Genera datos simulados para sparklines
     */
    generateSparklineData(currentValue, trend = 'stable', days = 7) {
        const data = [];
        let baseValue = currentValue * 0.8; // Empezar desde 80% del valor actual
        
        for (let i = 0; i < days; i++) {
            let variation = 0;
            
            switch (trend) {
                case 'growth':
                    variation = (Math.random() * 0.1 + 0.05) * baseValue; // 5-15% growth
                    break;
                case 'decline':
                    variation = -(Math.random() * 0.1 + 0.02) * baseValue; // 2-12% decline
                    break;
                case 'volatile':
                    variation = (Math.random() - 0.5) * 0.3 * baseValue; // ±15% volatility
                    break;
                case 'stable':
                default:
                    variation = (Math.random() - 0.5) * 0.1 * baseValue; // ±5% stability
                    break;
            }
            
            baseValue = Math.max(0, baseValue + variation);
            data.push(Math.round(baseValue));
        }
        
        // Asegurar que el último valor sea el actual
        data[data.length - 1] = currentValue;
        
        return data;
    }

    /**
     * Crea un sparkline SVG
     */
    createSparkline(containerId, data, color = '#206bc4', type = 'line') {
        const container = document.getElementById(containerId);
        if (!container || !data || data.length === 0) return;

        const width = 100;
        const height = 40;
        const padding = 2;
        
        const maxValue = Math.max(...data);
        const minValue = Math.min(...data);
        const range = maxValue - minValue || 1;
        
        // Crear SVG
        const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        svg.setAttribute('width', '100%');
        svg.setAttribute('height', height);
        svg.setAttribute('viewBox', `0 0 ${width} ${height}`);
        svg.style.overflow = 'visible';

        if (type === 'area') {
            // Crear área rellena
            const areaPath = this.createAreaPath(data, width, height, padding, minValue, range);
            const areaElement = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            areaElement.setAttribute('d', areaPath);
            areaElement.setAttribute('fill', color);
            areaElement.setAttribute('opacity', '0.1');
            areaElement.classList.add('sparkline-area');
            svg.appendChild(areaElement);
        }

        if (type === 'line' || type === 'area') {
            // Crear línea
            const linePath = this.createLinePath(data, width, height, padding, minValue, range);
            const lineElement = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            lineElement.setAttribute('d', linePath);
            lineElement.setAttribute('fill', 'none');
            lineElement.setAttribute('stroke', color);
            lineElement.setAttribute('stroke-width', '2');
            lineElement.setAttribute('stroke-linecap', 'round');
            lineElement.setAttribute('stroke-linejoin', 'round');
            lineElement.classList.add('sparkline-line');
            svg.appendChild(lineElement);

            // Crear puntos
            data.forEach((value, index) => {
                const x = (index / (data.length - 1)) * (width - 2 * padding) + padding;
                const y = height - padding - ((value - minValue) / range) * (height - 2 * padding);
                
                const point = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                point.setAttribute('cx', x);
                point.setAttribute('cy', y);
                point.setAttribute('r', '2');
                point.setAttribute('fill', color);
                point.classList.add('sparkline-point');
                point.style.animationDelay = `${index * 0.1}s`;
                svg.appendChild(point);
            });
        }

        // Limpiar container y añadir SVG
        container.innerHTML = '';
        container.appendChild(svg);
    }

    /**
     * Crea el path para una línea sparkline
     */
    createLinePath(data, width, height, padding, minValue, range) {
        const points = data.map((value, index) => {
            const x = (index / (data.length - 1)) * (width - 2 * padding) + padding;
            const y = height - padding - ((value - minValue) / range) * (height - 2 * padding);
            return `${x},${y}`;
        });

        return `M ${points.join(' L ')}`;
    }

    /**
     * Crea el path para un área sparkline
     */
    createAreaPath(data, width, height, padding, minValue, range) {
        const linePath = this.createLinePath(data, width, height, padding, minValue, range);
        const lastX = ((data.length - 1) / (data.length - 1)) * (width - 2 * padding) + padding;
        const firstX = padding;
        const bottomY = height - padding;
        
        return `${linePath} L ${lastX},${bottomY} L ${firstX},${bottomY} Z`;
    }

    /**
     * Formatea una fecha
     */
    formatDate(dateString) {
        if (!dateString) return '';
        try {
            const date = new Date(dateString);
            return date.toLocaleDateString('es-PE');
        } catch (error) {
            return dateString;
        }
    }
    
    /**
     * Formatea una hora
     */
    formatTime(dateString) {
        if (!dateString) return '';
        try {
            const date = new Date(dateString);
            return date.toLocaleTimeString('es-PE', { 
                hour: '2-digit', 
                minute: '2-digit' 
            });
        } catch (error) {
            return '';
        }
    }
}

// Instancia global del controlador
window.CompanyController = CompanyController;
