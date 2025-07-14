/**
 * Controlador para listar provincias con filtros avanzados
 * Endpoint: /api/v1/admin/provinces
 * Funcionalidades:
 * - Lista paginada de provincias
 * - Búsqueda global por nombre, ubigeo y departamento
 * - Filtros específicos por nombre, departmentId, ubigeo
 * - Ordenamiento por id, name, ubigeo
 * - Transformación de datos de API a formato UI
 */
class ProvincesListController {
    constructor() {
        console.log('📋 ProvincesListController constructor ejecutado');
        console.log('🔍 Verificando servicios disponibles:', {
            ProvincesService: !!window.ProvincesService,
            GlobalToast: !!window.GlobalToast
        });
        
        // Estado de la lista
        this.provinces = [];
        this.currentPage = 1;
        this.itemsPerPage = 15; // Valor por defecto según API
        this.totalProvinces = 0;
        this.totalPages = 0;
        this.searchTerm = '';
        this.searchTimeout = null;
        
        // Control de origen de carga para notificaciones
        this.lastAction = 'initial_load'; // 'initial_load', 'filter_change', 'manual_refresh', 'auto_refresh'
        
        // Filtros específicos de provincias según la API
        this.activeFilters = {
            name: '',               // Filtro por nombre específico
            departmentId: null,     // Filtro por ID de departamento
            ubigeo: '',             // Filtro por código UBIGEO
            sortBy: 'name',         // 'id', 'name', 'ubigeo'
            sortOrder: 'ASC'        // 'ASC', 'DESC'
        };
        
        // Estados de UI
        this.isLoading = false;
        this.isInitialized = false;
    }

    /**
     * Inicializa el controlador
     */
    async init() {
        console.log('📋 Inicializando ProvincesListController...');
        try {
            // Verificar que ProvincesService esté disponible
            await this.waitForServices();
            
            // Verificar elementos del DOM
            this.verifyDOMElements();
            
            // Configurar event listeners
            this.setupEventListeners();
            
            // Cargar datos iniciales
            await this.loadProvinces();
            
            // Configurar auto-refresh cada 5 minutos
            this.setupAutoRefresh();
            
            this.isInitialized = true;
            console.log('✅ ProvincesListController inicializado correctamente');
            
        } catch (error) {
            console.error('❌ Error al inicializar ProvincesListController:', error);
            this.showErrorToast('Error al inicializar la lista de provincias');
        }
    }

    /**
     * Verifica que los elementos del DOM existan
     */
    verifyDOMElements() {
        console.log('🔍 Verificando elementos del DOM...');
        const requiredElements = [
            'provincesTableBody',
            'totalProvinces',
            'filteredResults',
            'currentPage',
            'itemsRange',
            'paginationContainer',
            'searchInput'
        ];
        
        const missingElements = [];
        requiredElements.forEach(id => {
            if (!document.getElementById(id)) {
                missingElements.push(id);
            }
        });
        
        if (missingElements.length > 0) {
            console.warn('⚠️ Elementos del DOM faltantes:', missingElements);
        } else {
            console.log('✅ Todos los elementos del DOM encontrados');
        }
    }

    /**
     * Espera a que los servicios estén disponibles
     */
    async waitForServices() {
        let attempts = 0;
        const maxAttempts = 50; // 5 segundos máximo
        
        while (attempts < maxAttempts) {
            if (window.ProvincesService && window.GlobalToast) {
                console.log('✅ Servicios disponibles para ProvincesListController');
                return;
            }
            
            attempts++;
            await new Promise(resolve => setTimeout(resolve, 100));
        }
        
        throw new Error('Servicios requeridos no están disponibles');
    }

    /**
     * Configura los event listeners
     */
    setupEventListeners() {
        console.log('🔧 Configurando event listeners para provincias');
        
        // Event listener para búsqueda global
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    this.handleSearch(e.target.value);
                }, 500);
            });
        }

        // Event listener para filtro por nombre específico
        const nameFilterInput = document.getElementById('nameFilter');
        if (nameFilterInput) {
            nameFilterInput.addEventListener('input', (e) => {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    this.handleNameFilter(e.target.value);
                }, 500);
            });
        }

        // Event listener para filtro por departamento
        const departmentFilter = document.getElementById('departmentFilter');
        if (departmentFilter) {
            departmentFilter.addEventListener('change', (e) => {
                this.handleDepartmentFilter(e.target.value);
            });
        }

        // Event listener para filtro por UBIGEO
        const ubigeoFilterInput = document.getElementById('ubigeoFilter');
        if (ubigeoFilterInput) {
            ubigeoFilterInput.addEventListener('input', (e) => {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    this.handleUbigeoFilter(e.target.value);
                }, 500);
            });
        }

        // Event listener para ordenamiento
        const sortBySelect = document.getElementById('sortBySelect');
        if (sortBySelect) {
            sortBySelect.addEventListener('change', (e) => {
                this.handleSortChange(e.target.value);
            });
        }

        // Event listener para dirección de ordenamiento
        const sortOrderSelect = document.getElementById('sortOrderSelect');
        if (sortOrderSelect) {
            sortOrderSelect.addEventListener('change', (e) => {
                this.handleSortOrderChange(e.target.value);
            });
        }

        // Event listener para elementos por página
        const perPageSelect = document.getElementById('perPageSelect');
        if (perPageSelect) {
            perPageSelect.addEventListener('change', (e) => {
                this.handlePerPageChange(parseInt(e.target.value));
            });
        }

        // Event listener para botón de refresh
        const refreshBtn = document.getElementById('refreshBtn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => {
                this.handleManualRefresh();
            });
        }

        // Event listener para limpiar filtros
        const clearFiltersBtn = document.getElementById('clearFiltersBtn');
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', () => {
                this.clearAllFilters();
            });
        }
    }

    /**
     * Maneja la búsqueda global
     */
    async handleSearch(term) {
        console.log(`🔍 Búsqueda global iniciada: "${term}"`);
        this.searchTerm = term;
        this.currentPage = 1;
        this.lastAction = 'filter_change';
        await this.loadProvinces();
    }

    /**
     * Maneja el filtro por nombre específico
     */
    async handleNameFilter(name) {
        console.log(`🏷️ Filtro por nombre: "${name}"`);
        this.activeFilters.name = name;
        this.currentPage = 1;
        this.lastAction = 'filter_change';
        await this.loadProvinces();
    }

    /**
     * Maneja el filtro por departamento
     */
    async handleDepartmentFilter(departmentId) {
        console.log(`🗺️ Filtro por departamento: ${departmentId}`);
        this.activeFilters.departmentId = departmentId ? parseInt(departmentId) : null;
        this.currentPage = 1;
        this.lastAction = 'filter_change';
        await this.loadProvinces();
    }

    /**
     * Maneja el filtro por UBIGEO
     */
    async handleUbigeoFilter(ubigeo) {
        console.log(`📍 Filtro por UBIGEO: "${ubigeo}"`);
        this.activeFilters.ubigeo = ubigeo;
        this.currentPage = 1;
        this.lastAction = 'filter_change';
        await this.loadProvinces();
    }

    /**
     * Maneja el cambio de ordenamiento
     */
    async handleSortChange(sortBy) {
        console.log(`🔧 Cambio de ordenamiento: ${sortBy}`);
        this.activeFilters.sortBy = sortBy;
        this.currentPage = 1;
        this.lastAction = 'filter_change';
        await this.loadProvinces();
    }

    /**
     * Maneja el cambio de dirección de ordenamiento
     */
    async handleSortOrderChange(sortOrder) {
        console.log(`🔧 Cambio de dirección de ordenamiento: ${sortOrder}`);
        this.activeFilters.sortOrder = sortOrder;
        this.currentPage = 1;
        this.lastAction = 'filter_change';
        await this.loadProvinces();
    }

    /**
     * Maneja el cambio de elementos por página
     */
    async handlePerPageChange(perPage) {
        console.log(`📄 Cambio de elementos por página: ${perPage}`);
        this.itemsPerPage = perPage;
        this.currentPage = 1;
        this.lastAction = 'filter_change';
        await this.loadProvinces();
    }

    /**
     * Maneja el refresh manual
     */
    async handleManualRefresh() {
        console.log('🔄 Refresh manual iniciado');
        this.lastAction = 'manual_refresh';
        await this.loadProvinces();
    }

    /**
     * Limpia todos los filtros
     */
    async clearAllFilters() {
        console.log('🧹 Limpiando todos los filtros');
        
        // Limpiar filtros
        this.searchTerm = '';
        this.activeFilters = {
            name: '',
            departmentId: null,
            ubigeo: '',
            sortBy: 'name',
            sortOrder: 'ASC'
        };
        this.currentPage = 1;
        
        // Limpiar campos de entrada
        const searchInput = document.getElementById('searchInput');
        if (searchInput) searchInput.value = '';
        
        const nameFilterInput = document.getElementById('nameFilter');
        if (nameFilterInput) nameFilterInput.value = '';
        
        const departmentFilter = document.getElementById('departmentFilter');
        if (departmentFilter) departmentFilter.value = '';
        
        const ubigeoFilterInput = document.getElementById('ubigeoFilter');
        if (ubigeoFilterInput) ubigeoFilterInput.value = '';
        
        const sortBySelect = document.getElementById('sortBySelect');
        if (sortBySelect) sortBySelect.value = 'name';
        
        const sortOrderSelect = document.getElementById('sortOrderSelect');
        if (sortOrderSelect) sortOrderSelect.value = 'ASC';
        
        this.lastAction = 'filter_change';
        await this.loadProvinces();
    }

    /**
     * Carga la lista de provincias desde la API
     */
    async loadProvinces() {
        if (this.isLoading) {
            console.log('⏳ Carga ya en progreso, ignorando solicitud');
            return;
        }

        this.isLoading = true;
        this.showLoadingState();

        try {
            console.log('🔄 Cargando provincias...');
            
            const response = await window.ProvincesService.getProvinces(
                this.currentPage,
                this.itemsPerPage,
                this.searchTerm,
                this.activeFilters.name,
                this.activeFilters.departmentId,
                this.activeFilters.ubigeo,
                this.activeFilters.sortBy,
                this.activeFilters.sortOrder
            );

            console.log('📊 Respuesta de la API de Provincias:', response);

            if (response.success && response.data) {
                this.provinces = response.data.data || [];
                this.totalProvinces = response.data.pagination?.total || 0;
                this.totalPages = response.data.pagination?.totalPages || 0;
                this.currentPage = response.data.pagination?.page || 1;
                
                console.log(`✅ ${this.provinces.length} provincias cargadas de ${this.totalProvinces} total`);
                
                this.renderProvinces();
                this.updatePagination();
                this.updateStats();
                
                // Mostrar notificación según la acción
                if (this.lastAction === 'manual_refresh') {
                    this.showSuccessToast('Lista de provincias actualizada');
                } else if (this.lastAction === 'filter_change') {
                    this.showInfoToast(`${this.totalProvinces} provincia(s) encontrada(s)`);
                }
            } else {
                throw new Error('Respuesta de API inválida');
            }

        } catch (error) {
            console.error('❌ Error cargando provincias:', error);
            this.showErrorToast(error.message || 'Error al cargar las provincias');
            this.showErrorState();
        } finally {
            this.isLoading = false;
            this.hideLoadingState();
        }
    }

    /**
     * Renderiza la lista de provincias en la tabla
     */
    renderProvinces() {
        console.log('🎨 Renderizando lista de provincias');
        
        const tableBody = document.getElementById('provincesTableBody');
        if (!tableBody) {
            console.error('❌ No se encontró el elemento provincesTableBody');
            return;
        }

        if (this.provinces.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <div class="empty">
                            <div class="empty-img">
                                <i class="fas fa-map-marker-alt fa-3x text-muted"></i>
                            </div>
                            <p class="empty-title">No se encontraron provincias</p>
                            <p class="empty-subtitle text-muted">
                                ${this.searchTerm || this.hasActiveFilters() ? 
                                    'Intenta ajustar los filtros de búsqueda' : 
                                    'No hay provincias disponibles en este momento'}
                            </p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        const rows = this.provinces.map((province, index) => {
            const rowNumber = ((this.currentPage - 1) * this.itemsPerPage) + index + 1;
            
            return `
                <tr class="province-row" data-province-id="${province.id}">
                    <td class="text-muted">${rowNumber}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="avatar avatar-sm me-2" style="background-color: var(--tblr-primary);">
                                <i class="fas fa-map-marker-alt text-white"></i>
                            </span>
                            <div>
                                <div class="fw-bold">${this.escapeHtml(province.name)}</div>
                                <div class="text-muted small">ID: ${province.id}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="avatar avatar-sm me-2" style="background-color: var(--tblr-success);">
                                <i class="fas fa-map text-white"></i>
                            </span>
                            <div>
                                <div class="fw-bold">${this.escapeHtml(province.departmentName)}</div>
                                <div class="text-muted small">ID: ${province.departmentId}</div>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-outline text-blue">
                            <i class="fas fa-barcode me-1"></i>
                            ${this.escapeHtml(province.ubigeo).padEnd(6, '0')}
                        </span>
                    </td>
                    <td>
                        <div class="btn-list flex-nowrap">
                            <button class="btn btn-sm btn-outline-orange" 
                                    onclick="window.provincesController.editProvince(${province.id})" 
                                    title="Editar">
                                <i class="fas fa-edit text-orange"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-red" 
                                    onclick="window.provinceDeleteController.handleDeleteProvince(${province.id}, '${this.escapeHtml(province.name)}', '${this.escapeHtml(province.ubigeo)}', '${this.escapeHtml(province.departmentName)}')" 
                                    title="Eliminar">
                                <i class="fas fa-trash text-red"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');

        tableBody.innerHTML = rows;
    }

    /**
     * Actualiza la paginación
     */
    updatePagination() {
        console.log('📄 Actualizando paginación');
        
        const paginationContainer = document.getElementById('paginationContainer');
        if (!paginationContainer) return;

        if (this.totalPages <= 1) {
            paginationContainer.innerHTML = '';
            return;
        }

        const maxVisiblePages = 5;
        const startPage = Math.max(1, this.currentPage - Math.floor(maxVisiblePages / 2));
        const endPage = Math.min(this.totalPages, startPage + maxVisiblePages - 1);

        let paginationHTML = '';
        
        // Botón anterior
        paginationHTML += `
            <li class="page-item ${this.currentPage === 1 ? 'disabled' : ''}">
                <button class="page-link" data-page="${this.currentPage - 1}" ${this.currentPage === 1 ? 'disabled' : ''}>
                    <i class="fas fa-chevron-left"></i>
                    Anterior
                </button>
            </li>
        `;
        
        // Números de página
        for (let i = startPage; i <= endPage; i++) {
            paginationHTML += `
                <li class="page-item ${i === this.currentPage ? 'active' : ''}">
                    <button class="page-link" data-page="${i}">${i}</button>
                </li>
            `;
        }
        
        // Botón siguiente
        paginationHTML += `
            <li class="page-item ${this.currentPage === this.totalPages ? 'disabled' : ''}">
                <button class="page-link" data-page="${this.currentPage + 1}" ${this.currentPage === this.totalPages ? 'disabled' : ''}>
                    Siguiente
                    <i class="fas fa-chevron-right"></i>
                </button>
            </li>
        `;
        
        paginationContainer.innerHTML = paginationHTML;
        
        // Agregar event listeners a los botones de paginación
        paginationContainer.querySelectorAll('.page-link').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const page = parseInt(e.target.getAttribute('data-page'));
                if (!isNaN(page)) {
                    this.goToPage(page);
                }
            });
        });
    }

    /**
     * Ir a una página específica
     */
    async goToPage(page) {
        if (page < 1 || page > this.totalPages || page === this.currentPage) {
            return;
        }
        
        console.log(`📄 Navegando a página ${page}`);
        this.currentPage = page;
        await this.loadProvinces();
    }

    /**
     * Actualiza las estadísticas
     */
    updateStats() {
        console.log('📊 Actualizando estadísticas');
        
        // Total de provincias
        const totalElement = document.getElementById('totalProvinces');
        if (totalElement) {
            totalElement.textContent = this.totalProvinces.toLocaleString();
        }
        
        // Total de provincias en footer
        const totalFooterElement = document.getElementById('totalProvincesFooter');
        if (totalFooterElement) {
            totalFooterElement.textContent = this.totalProvinces.toLocaleString();
        }
        
        // Resultados filtrados
        const filteredElement = document.getElementById('filteredResults');
        if (filteredElement) {
            filteredElement.textContent = this.provinces.length.toLocaleString();
        }
        
        // Página actual
        const currentPageElement = document.getElementById('currentPage');
        if (currentPageElement) {
            currentPageElement.textContent = `${this.currentPage} de ${this.totalPages}`;
        }
        
        // Rango de elementos
        const rangeElement = document.getElementById('itemsRange');
        if (rangeElement) {
            const start = ((this.currentPage - 1) * this.itemsPerPage) + 1;
            const end = Math.min(this.currentPage * this.itemsPerPage, this.totalProvinces);
            rangeElement.textContent = `${start}-${end}`;
        }
        
        // Rango de elementos en footer
        const rangeFooterElement = document.getElementById('itemsRangeFooter');
        if (rangeFooterElement) {
            const start = ((this.currentPage - 1) * this.itemsPerPage) + 1;
            const end = Math.min(this.currentPage * this.itemsPerPage, this.totalProvinces);
            rangeFooterElement.textContent = `${start}-${end}`;
        }
    }

    /**
     * Verifica si hay filtros activos
     */
    hasActiveFilters() {
        return this.searchTerm || 
               this.activeFilters.name || 
               this.activeFilters.departmentId || 
               this.activeFilters.ubigeo ||
               this.activeFilters.sortBy !== 'name' ||
               this.activeFilters.sortOrder !== 'ASC';
    }

    /**
     * Configurar auto-refresh
     */
    setupAutoRefresh() {
        console.log('🔄 Configurando auto-refresh cada 5 minutos');
        
        setInterval(() => {
            if (this.isInitialized && !this.isLoading) {
                console.log('🔄 Auto-refresh ejecutándose');
                this.lastAction = 'auto_refresh';
                this.loadProvinces();
            }
        }, 5 * 60 * 1000); // 5 minutos
    }

    /**
     * Mostrar estado de carga
     */
    showLoadingState() {
        const tableBody = document.getElementById('provincesTableBody');
        if (tableBody) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-4">
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                            <span>Cargando provincias...</span>
                        </div>
                    </td>
                </tr>
            `;
        }
    }

    /**
     * Ocultar estado de carga
     */
    hideLoadingState() {
        // El contenido real se muestra en renderProvinces()
    }

    /**
     * Mostrar estado de error
     */
    showErrorState() {
        const tableBody = document.getElementById('provincesTableBody');
        if (tableBody) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center py-4">
                        <div class="empty">
                            <div class="empty-img">
                                <i class="fas fa-exclamation-triangle fa-3x text-danger"></i>
                            </div>
                            <p class="empty-title">Error al cargar provincias</p>
                            <p class="empty-subtitle text-muted">
                                Hubo un problema al obtener la información. 
                                <a href="#" onclick="window.provincesController.loadProvinces()" class="link-primary">Intentar nuevamente</a>
                            </p>
                        </div>
                    </td>
                </tr>
            `;
        }
    }

    /**
     * Acciones CRUD placeholder
     */
    async viewProvince(id) {
        console.log(`👁️ Ver provincia ID: ${id}`);
        this.showInfoToast(`Función de vista para provincia ID: ${id} (No implementada)`);
    }

    async editProvince(id) {
        console.log(`✏️ Editando provincia ID: ${id}`);
        
        try {
            // Verificar que el controlador de actualización esté disponible
            if (!window.ProvinceUpdateController) {
                throw new Error('Controlador de actualización no disponible');
            }

            // Buscar los datos de la provincia en la lista actual
            const provinceData = this.provinces.find(province => province.id === id);
            
            if (!provinceData) {
                // Si no está en la lista actual, obtener de la API
                console.log('📡 Obteniendo datos de provincia desde API...');
                const service = new ProvincesService();
                const response = await service.getProvinceById(id);
                
                if (response.success && response.data) {
                    await window.provinceUpdateController.openModal(response.data);
                } else {
                    throw new Error('No se pudieron obtener los datos de la provincia');
                }
            } else {
                // Usar los datos de la lista actual
                await window.provinceUpdateController.openModal(provinceData);
            }
            
        } catch (error) {
            console.error('❌ Error al abrir modal de edición:', error);
            this.showErrorToast(error.message || 'Error al abrir el editor de provincia');
        }
    }

    /**
     * Utilidades para notificaciones
     */
    showSuccessToast(message) {
        if (window.GlobalToast) {
            window.GlobalToast.show(message, 'success');
        }
    }

    showErrorToast(message) {
        if (window.GlobalToast) {
            window.GlobalToast.show(message, 'error');
        }
    }

    showInfoToast(message) {
        if (window.GlobalToast) {
            window.GlobalToast.show(message, 'info');
        }
    }

    /**
     * Escape HTML para prevenir XSS
     */
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Hacer disponible globalmente
window.ProvincesListController = ProvincesListController;

// Crear instancia global
window.provincesController = new ProvincesListController();

console.log('🌎 ProvincesListController cargado y disponible globalmente');
