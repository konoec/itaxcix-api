/**
 * Controlador para el módulo de Gestión de Tipos de Servicio
 * Maneja la carga, filtrado, paginación y visualización de tipos de servicio
 */
class ServiceTypeController {
    constructor() {
        console.log('🚕 Inicializando ServiceTypeController...');
        
        // Configuración inicial
        this.currentPage = 1;
        this.perPage = 15;
        this.totalPages = 1;
        this.totalItems = 0;
        this.currentData = [];
        this.sortBy = 'name';
        this.sortDirection = 'ASC';
        this.isLoading = false;
        
        // Filtros actuales
        this.filters = {
            search: '',
            name: '',
            active: 'all'
        };
        
        // Servicios
        this.serviceTypeService = new ServiceTypeService();
        
        // Elementos del DOM
        this.initializeElements();
        
        // Event listeners
        this.initializeEventListeners();
        
        // Cargar datos iniciales
        this.loadServiceTypes();
    }
    
    /**
     * Inicializa las referencias a elementos del DOM
     */
    initializeElements() {
        // Contenedores principales
        this.loadingContainer = document.getElementById('service-types-loading');
        this.contentContainer = document.getElementById('service-types-content');
        
        // Filtros y búsqueda
        this.searchInput = document.getElementById('service-type-search-input');
        this.statusFilter = document.getElementById('service-type-status-filter');
        this.nameFilter = document.getElementById('service-type-name-filter');
        this.sortBySelect = document.getElementById('sort-by-select');
        this.sortDirectionSelect = document.getElementById('sort-direction-select');
        
        // Botones de filtro
        this.searchBtn = document.getElementById('search-btn');
        this.clearFiltersBtn = document.getElementById('clear-filters-btn');
        
        // Botones de acción
        this.refreshBtn = document.getElementById('refresh-service-types-btn');
        
        // Tabla
        this.tableBody = document.getElementById('service-types-table-body');
        
        // Paginación
        this.showingStart = document.getElementById('showing-start');
        this.showingEnd = document.getElementById('showing-end');
        this.totalRecords = document.getElementById('total-records');
        this.currentPageInfo = document.getElementById('current-page-info');
        this.paginationContainer = document.getElementById('service-types-pagination');

        
        // Estadísticas
        this.totalServiceTypesEl = document.getElementById('total-service-types');
        this.activeServiceTypesEl = document.getElementById('active-service-types');
        this.inactiveServiceTypesEl = document.getElementById('inactive-service-types');
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
        
        if (this.clearFiltersBtn) {
            this.clearFiltersBtn.addEventListener('click', () => this.clearFilters());
        }
        
        // Filtros en tiempo real
        if (this.searchInput) {
            this.searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    this.handleSearch();
                }
            });
        }
        
        if (this.statusFilter) {
            this.statusFilter.addEventListener('change', () => this.handleSearch());
        }
        
        if (this.nameFilter) {
            this.nameFilter.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    this.handleSearch();
                }
            });
        }
        
        // Ordenamiento
        if (this.sortBySelect) {
            this.sortBySelect.addEventListener('change', (e) => {
                this.sortBy = e.target.value;
                this.handleSearch();
            });
        }
        
        if (this.sortDirectionSelect) {
            this.sortDirectionSelect.addEventListener('change', (e) => {
                this.sortDirection = e.target.value;
                this.handleSearch();
            });
        }
        
        // Paginación
        if (this.prevPageBtn) {
            this.prevPageBtn.addEventListener('click', () => this.goToPreviousPage());
        }
        
        if (this.nextPageBtn) {
            this.nextPageBtn.addEventListener('click', () => this.goToNextPage());
        }
        
        // Actualizar
        if (this.refreshBtn) {
            this.refreshBtn.addEventListener('click', () => this.refresh());
        }
        
        // Exportación
        if (this.exportExcelBtn) {
            this.exportExcelBtn.addEventListener('click', () => this.handleExportExcel());
        }
        
        if (this.exportPdfBtn) {
            this.exportPdfBtn.addEventListener('click', () => this.handleExportPDF());
        }
        
        console.log('✅ Event listeners inicializados');
    }
    
    /**
     * Carga los tipos de servicio con los filtros actuales
     */
    async loadServiceTypes() {
        if (this.isLoading) {
            console.log('⚠️ Carga ya en progreso, omitiendo...');
            return;
        }
        
        this.isLoading = true;
        this.showLoading();
        
        try {
            console.log('🚕 Cargando tipos de servicio...', {
                page: this.currentPage,
                perPage: this.perPage,
                filters: this.filters,
                sortBy: this.sortBy,
                sortDirection: this.sortDirection
            });
            
            const params = {
                page: this.currentPage,
                perPage: this.perPage,
                search: this.filters.search,
                name: this.filters.name,
                active: this.filters.active === 'all' ? undefined : this.filters.active === 'true',
                sortBy: this.sortBy,
                sortDirection: this.sortDirection
            };
            
            const response = await this.serviceTypeService.getServiceTypes(params);
            
            if (response.success && response.data) {
                // Asegurar que currentData sea un array
                this.currentData = Array.isArray(response.data.data) ? response.data.data : [];
                this.totalItems = response.data.pagination?.total_items || 0;
                this.totalPages = response.data.pagination?.total_pages || 1;
                
                console.log('📊 Datos procesados:', {
                    currentData: this.currentData.length,
                    totalItems: this.totalItems,
                    totalPages: this.totalPages
                });
                
                this.renderTable();
                this.updatePagination();
                await this.updateStatistics();
                
                console.log('✅ Tipos de servicio cargados exitosamente');
            } else {
                throw new Error(response.message || 'Error al cargar tipos de servicio');
            }
            
        } catch (error) {
            console.error('❌ Error al cargar tipos de servicio:', error);
            this.showError('Error al cargar los tipos de servicio. Intente nuevamente.');
            this.currentData = [];
        } finally {
            this.isLoading = false;
            this.hideLoading();
        }
    }
    
    /**
     * Renderiza la tabla con los datos actuales
     */
    renderTable() {
        if (!this.tableBody) {
            console.warn('⚠️ Elemento tableBody no encontrado');
            return;
        }
        
        // Asegurar que currentData sea un array
        if (!Array.isArray(this.currentData)) {
            console.warn('⚠️ currentData no es un array:', this.currentData);
            this.currentData = [];
        }
        
        if (this.currentData.length === 0) {
            this.tableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center py-4">
                        <div class="text-muted">
                            <i class="fas fa-concierge-bell fs-1 mb-3"></i>
                            <p>No se encontraron tipos de servicio</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }
        
        const rows = this.currentData.map((serviceType, index) => {
            const rowNumber = (this.currentPage - 1) * this.perPage + index + 1;
            const statusBadge = serviceType.active 
                ? `<span class="badge bg-success-lt"><i class="fas fa-check-circle me-1"></i>Activo</span>`
                : `<span class="badge bg-danger-lt"><i class="fas fa-times-circle me-1"></i>Inactivo</span>`;
            
            return `
                <tr>
                    <td class="text-muted">${rowNumber}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-xs bg-cyan text-white me-2">
                                <i class="fas fa-concierge-bell"></i>
                            </div>
                            <div>
                                <strong>${this.escapeHtml(serviceType.name)}</strong>
                            </div>
                        </div>
                    </td>
                    <td>${statusBadge}</td>
                    <td>
                        <div class="btn-list flex-nowrap">
                            <button class="btn btn-sm btn-outline-orange" 
                                    onclick="window.serviceTypeController.editServiceType(${serviceType.id})" 
                                    title="Editar">
                                <i class="fas fa-edit text-orange"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-red" 
                                    onclick="window.serviceTypeDeleteController.handleDeleteServiceType(${serviceType.id}, '${this.escapeHtml(serviceType.name)}')" 
                                    title="Eliminar">
                                <i class="fas fa-trash text-red"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
        
        this.tableBody.innerHTML = rows;
    }
    
    /**
     * Actualiza la información de paginación
     */
    updatePagination() {
    const start = this.currentData.length > 0 ? (this.currentPage - 1) * this.perPage + 1 : 0;
    const end = Math.min(start + this.currentData.length - 1, this.totalItems);

    if (this.showingStart) this.showingStart.textContent = start;
    if (this.showingEnd) this.showingEnd.textContent = end;
    if (this.totalRecords) this.totalRecords.textContent = this.totalItems;
    if (this.currentPageInfo) this.currentPageInfo.textContent = `Página ${this.currentPage} de ${this.totalPages}`;

    this._renderPaginationButtons();
}

_renderPaginationButtons() {
    if (!this.paginationContainer) return;
    const ul = this.paginationContainer;
    ul.innerHTML = '';
    const totalPages = this.totalPages || 1;
    const currentPage = this.currentPage || 1;
    const delta = 2;

    const makeItem = (html, p, disabled = false, active = false, isDots = false) => {
        const li = document.createElement('li');
        li.className = `page-item${disabled ? ' disabled' : ''}${active ? ' active' : ''}${isDots ? ' disabled' : ''}`;
        if (isDots) {
            li.innerHTML = `<span class="page-link">…</span>`;
            return li;
        }
        const a = document.createElement('a');
        a.className = 'page-link';
        a.href = '#';
        a.innerHTML = html;
        if (!disabled && !active) {
            a.addEventListener('click', (e) => {
                e.preventDefault();
                this.currentPage = p;
                this.loadServiceTypes();
            });
        }
        li.appendChild(a);
        return li;
    };

    // << Primera página
    ul.appendChild(makeItem('<i class="fas fa-angle-double-left"></i>', 1, currentPage === 1));
    // < Anterior
    ul.appendChild(makeItem('<i class="fas fa-angle-left"></i>', currentPage - 1, currentPage === 1));

    // Números con puntos suspensivos
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

    // > Siguiente
    ul.appendChild(makeItem('<i class="fas fa-angle-right"></i>', currentPage + 1, currentPage === totalPages));
    // >> Última página
    ul.appendChild(makeItem('<i class="fas fa-angle-double-right"></i>', totalPages, currentPage === totalPages));
}

    
    /**
     * Actualiza las estadísticas
     */
    async updateStatistics() {
        try {
            const stats = await this.serviceTypeService.getServiceTypesStats();
            
            if (this.totalServiceTypesEl) this.totalServiceTypesEl.textContent = stats.total;
            if (this.activeServiceTypesEl) this.activeServiceTypesEl.textContent = stats.active;
            if (this.inactiveServiceTypesEl) this.inactiveServiceTypesEl.textContent = stats.inactive;
            if (this.growthPercentageEl) this.growthPercentageEl.textContent = `${stats.growth}%`;
            
        } catch (error) {
            console.error('❌ Error al actualizar estadísticas:', error);
        }
    }
    
    /**
     * Maneja la búsqueda y aplicación de filtros
     */
    handleSearch() {
        this.filters.search = this.searchInput?.value?.trim() || '';
        this.filters.name = this.nameFilter?.value?.trim() || '';
        this.filters.active = this.statusFilter?.value || 'all';
        
        this.currentPage = 1; // Resetear a primera página
        this.loadServiceTypes();
    }
    
    /**
     * Limpia todos los filtros
     */
    clearFilters() {
        this.filters = {
            search: '',
            name: '',
            active: 'all'
        };
        
        if (this.searchInput) this.searchInput.value = '';
        if (this.nameFilter) this.nameFilter.value = '';
        if (this.statusFilter) this.statusFilter.value = 'all';
        if (this.sortBySelect) this.sortBySelect.value = 'name';
        if (this.sortDirectionSelect) this.sortDirectionSelect.value = 'ASC';
        
        this.sortBy = 'name';
        this.sortDirection = 'ASC';
        this.currentPage = 1;
        
        this.loadServiceTypes();
        
        if (window.showRecoveryToast) {
            window.showRecoveryToast('Filtros limpiados correctamente', 'info');
        }
    }
    
    /**
     * Navega a la página anterior
     */
    goToPreviousPage() {
        if (this.currentPage > 1) {
            this.currentPage--;
            this.loadServiceTypes();
        }
    }
    
    /**
     * Navega a la página siguiente
     */
    goToNextPage() {
        if (this.currentPage < this.totalPages) {
            this.currentPage++;
            this.loadServiceTypes();
        }
    }
    
    /**
     * Refresca los datos
     */
    refresh() {
        this.serviceTypeService.clearCache();
        this.loadServiceTypes();
        
        if (window.showRecoveryToast) {
            window.showRecoveryToast('Datos actualizados correctamente', 'success');
        }
    }
    
    /**
     * Muestra detalles de un tipo de servicio
     * @param {number} id - ID del tipo de servicio
     */
    viewDetails(id) {
        const serviceType = this.currentData.find(item => item.id === id);
        if (!serviceType) {
            console.error('❌ Tipo de servicio no encontrado:', id);
            return;
        }
        
        console.log('👁️ Viendo detalles del tipo de servicio:', serviceType);
        
        if (window.showRecoveryToast) {
            window.showRecoveryToast(`Viendo detalles de: ${serviceType.name}`, 'info');
        }
    }
    
    /**
     * Maneja la exportación a Excel
     */
    async handleExportExcel() {
        try {
            this.setExportButtonLoading(this.exportExcelBtn, true);
            
            await this.serviceTypeService.exportToExcel(this.filters);
            
            if (window.showRecoveryToast) {
                window.showRecoveryToast('Exportación a Excel completada', 'success');
            }
            
        } catch (error) {
            console.error('❌ Error en exportación Excel:', error);
            if (window.showRecoveryToast) {
                window.showRecoveryToast('Error al exportar a Excel', 'error');
            }
        } finally {
            this.setExportButtonLoading(this.exportExcelBtn, false);
        }
    }
    
    /**
     * Maneja la exportación a PDF
     */
    async handleExportPDF() {
        try {
            this.setExportButtonLoading(this.exportPdfBtn, true);
            
            await this.serviceTypeService.exportToPDF(this.filters);
            
            if (window.showRecoveryToast) {
                window.showRecoveryToast('Exportación a PDF completada', 'success');
            }
            
        } catch (error) {
            console.error('❌ Error en exportación PDF:', error);
            if (window.showRecoveryToast) {
                window.showRecoveryToast('Error al exportar a PDF', 'error');
            }
        } finally {
            this.setExportButtonLoading(this.exportPdfBtn, false);
        }
    }
    
    /**
     * Establece el estado de carga de un botón de exportación
     */
    setExportButtonLoading(button, isLoading) {
        if (!button) return;
        
        if (isLoading) {
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Exportando...';
        } else {
            button.disabled = false;
            if (button === this.exportExcelBtn) {
                button.innerHTML = '<i class="fas fa-file-excel me-1"></i> Excel';
            } else if (button === this.exportPdfBtn) {
                button.innerHTML = '<i class="fas fa-file-pdf me-1"></i> PDF';
            }
        }
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
     * Muestra un mensaje de error
     */
    showError(message) {
        console.error('❌ Error:', message);
        
        if (window.showRecoveryToast) {
            window.showRecoveryToast(message, 'error');
        }
        
        // Mostrar mensaje en la tabla también
        if (this.tableBody) {
            this.tableBody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center py-4">
                        <div class="text-danger">
                            <i class="fas fa-exclamation-triangle fs-1 mb-3"></i>
                            <p>${this.escapeHtml(message)}</p>
                            <button class="btn btn-outline-primary btn-sm" onclick="serviceTypeController.refresh()">
                                <i class="fas fa-sync-alt me-1"></i> Reintentar
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }
    }
    
    /**
     * Escapa caracteres HTML para prevenir XSS
     */
    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    /**
     * Abre el modal de edición para un tipo de servicio
     * @param {number} id - ID del tipo de servicio
     */
    editServiceType(id) {
        const serviceType = this.currentData.find(item => item.id === id);
        if (!serviceType) {
            console.error('❌ Tipo de servicio no encontrado para edición:', id);
            if (window.showRecoveryToast) {
                window.showRecoveryToast('Tipo de servicio no encontrado', 'error');
            }
            return;
        }
        // Disparar evento global para abrir el modal de edición
        const event = new CustomEvent('openServiceTypeEditModal', {
            detail: { id, serviceTypeData: serviceType }
        });
        document.dispatchEvent(event);
    }
}

// Variable global para acceso desde HTML
window.serviceTypeController = new ServiceTypeController();
