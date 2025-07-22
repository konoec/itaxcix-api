/**
 * Controlador para listar estados de conductores con filtros avanzados
 * Endpoint: /api/v1/admin/driver-statuses
 * Funcionalidades:
 * - Lista paginada de estados de conductores
 * - Búsqueda global en nombre
 * - Filtros específicos por nombre y estado activo
 * - Ordenamiento por id, name, active
 * - Solo activos
 * - Interfaz moderna con Tabler
 */
class DriverStatusListController {
    constructor() {
        console.log('📋 DriverStatusListController constructor ejecutado');
        
        // Estado de la lista
        this.driverStatuses = [];
        this.currentPage = 1;
        this.itemsPerPage = 15; // Valor por defecto según API
        this.totalItems = 0;
        this.totalPages = 0;
        this.searchTerm = '';
        this.searchTimeout = null;
        
        // Control de origen de carga para notificaciones
        this.lastAction = 'initial_load'; // 'initial_load', 'filter_change', 'manual_refresh', 'auto_refresh'
        
        // Filtros específicos de estados de conductores según la API
        this.activeFilters = {
            name: null,                 // Filtro por nombre del estado
            active: null,               // Filtro por estado activo (true/false)
            sortBy: 'name',            // 'id', 'name', 'active'
            sortDirection: 'asc',      // 'asc', 'desc'
            onlyActive: false          // Incluir solo activos
        };
        
        // Estados de UI
        this.isLoading = false;
        this.isInitialized = false;
        this.predefinedStatuses = {};
    }

    /**
     * Inicializa el controlador
     */
    async init() {
        console.log('📋 Inicializando DriverStatusListController...');
        try {
            // Verificar que DriverStatusService esté disponible
            await this.waitForServices();
            
            // Renderizar la interfaz
            this.renderInterface();
            
            // Configurar event listeners
            this.setupEventListeners();
            
            // Cargar datos iniciales
            await this.loadDriverStatuses();
            
            // Configurar auto-refresh cada 5 minutos
            this.setupAutoRefresh();
            
            this.isInitialized = true;
            console.log('✅ DriverStatusListController inicializado correctamente');
            
        } catch (error) {
            console.error('❌ Error al inicializar DriverStatusListController:', error);
            this.showErrorToast('Error al inicializar la lista de estados de conductores');
        }
    }

    /**
     * Espera a que los servicios estén disponibles
     */
    async waitForServices() {
        let attempts = 0;
        const maxAttempts = 50; // 5 segundos máximo
        
        while (attempts < maxAttempts) {
            if (window.DriverStatusService && window.GlobalToast) {
                console.log('✅ Servicios disponibles para DriverStatusListController');
                return;
            }
            
            attempts++;
            await new Promise(resolve => setTimeout(resolve, 100));
        }
        
        throw new Error('Servicios requeridos no están disponibles');
    }

    /**
     * Renderiza la interfaz principal
     */
    renderInterface() {
        const container = document.querySelector('.page-body .container-xl');
        if (!container) {
            console.error('❌ Contenedor principal no encontrado');
            return;
        }

        container.innerHTML = `
            <!-- Header de la página -->
            <div class="page-header d-print-none">
                <div class="container-xl">
                    <div class="row g-2 align-items-center">
                        <div class="col">
                            <div class="page-pretitle">
                                Gestión de Datos
                            </div>
                            <h2 class="page-title">
                                <i class="fas fa-user-check me-2"></i>
                                Estados de Conductores
                            </h2>
                        </div>
                        <!-- Page title actions -->
                        <div class="col-auto ms-auto d-print-none">
                            <div class="btn-list">
                                <button class="btn btn-outline-cyan" id="refreshBtn" title="Actualizar datos">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                                <button class="btn btn-outline-secondary" id="clearFiltersBtn" title="Limpiar filtros">
                                    <i class="fas fa-broom"></i>
                                </button>
                                <button class="btn btn-indigo" id="addStatusBtn" title="Nuevo estado">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="page-body">
                <div class="container-xl">
                    <!-- Statistics Cards -->
                    <div class="row mb-4" id="driver-status-stats">
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-blue text-white avatar">
                                                <i class="fas fa-user-check"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="fw-bold" id="totalStatsCount">0</div>
                                            <div class="text-muted">Total Estados</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-green text-white avatar">
                                                <i class="fas fa-check-circle"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="fw-bold" id="activeStatsCount">0</div>
                                            <div class="text-muted">Estados Activos</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-red text-white avatar">
                                                <i class="fas fa-times-circle"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="fw-bold" id="inactiveStatsCount">0</div>
                                            <div class="text-muted">Estados Inactivos</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-sm">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-auto">
                                            <span class="bg-purple text-white avatar">
                                                <i class="fas fa-chart-line"></i>
                                            </span>
                                        </div>
                                        <div class="col">
                                            <div class="fw-bold" id="currentPageStats">0%</div>
                                            <div class="text-muted">Tasa Activos</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-filter me-2"></i>
                                Filtros Avanzados
                            </h3>
                            <div class="card-actions">
                                <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#advancedFilters">
                                    <i class="fas fa-sliders-h me-1"></i>
                                    Mostrar/Ocultar
                                </button>
                            </div>
                        </div>
                        <div class="collapse show" id="advancedFilters">
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Búsqueda general</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="searchInput" 
                                                   placeholder="Buscar por nombre...">
                                            <button class="btn btn-teal" type="button" id="searchBtn">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Estado</label>
                                        <select class="form-select" id="activeFilter">
                                            <option value="">Todos</option>
                                            <option value="true">Activos</option>
                                            <option value="false">Inactivos</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Filtro por nombre</label>
                                        <input type="text" class="form-control" id="nameFilter" 
                                               placeholder="Nombre específico...">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Ordenar por</label>
                                        <div class="row g-2">
                                            <div class="col-8">
                                                <select class="form-select" id="sortBySelect">
                                                    <option value="name">Nombre</option>
                                                    <option value="id">ID</option>
                                                    <option value="active">Estado</option>
                                                </select>
                                            </div>
                                            <div class="col-4">
                                                <select class="form-select" id="sortDirectionSelect">
                                                    <option value="asc">ASC</option>
                                                    <option value="desc">DESC</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row g-3 mt-2">
                                    <div class="col-12">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="onlyActiveSwitch">
                                            <label class="form-check-label" for="onlyActiveSwitch">
                                                <strong>Mostrar solo estados activos</strong>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Driver Status Table -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-list me-2"></i>
                                Lista de Estados de Conductores
                            </h3>
                            <div class="card-actions">
                                <div class="dropdown">
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="#" id="exportExcelBtn">
                                            <i class="fas fa-file-excel me-2"></i>
                                            Excel
                                        </a>
                                        <a class="dropdown-item" href="#" id="exportPdfBtn">
                                            <i class="fas fa-file-pdf me-2"></i>
                                            PDF
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Indicador de carga -->
                        <div id="loadingIndicator" class="text-center py-4 d-none">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Cargando...</span>
                            </div>
                            <div class="mt-2 text-muted">Cargando estados de conductores...</div>
                        </div>

                        <div class="table-responsive" id="tableContainer">
                            <table class="table table-vcenter">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>Estado</th>
                                        <th class="w-1">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody id="driverStatusTableBody">
                                    <!-- Dynamic content will be inserted here -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Mensaje cuando no hay datos -->
                        <div id="noDataMessage" class="text-center py-5 d-none">
                            <div class="text-muted">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <h3>No se encontraron estados de conductores</h3>
                                <p>No hay estados que coincidan con los filtros aplicados.</p>
                            </div>
                        </div>

                        <div class="card-footer d-flex align-items-center" id="paginationContainer">
                            <p class="m-0 text-muted" id="paginationInfo">
                                Mostrando <span id="showingStart">0</span> a <span id="showingEnd">0</span> 
                                de <span id="totalRecords">0</span> registros
                            </p>
                            <ul class="pagination m-0 ms-auto" id="paginationList">
                                <li class="page-item">
                                    <button class="page-link" id="prevPageBtn" disabled>
                                        <i class="fas fa-chevron-left"></i>
                                        Anterior
                                    </button>
                                </li>
                                <li class="page-item active">
                                    <span class="page-link" id="currentPageInfo">1</span>
                                </li>
                                <li class="page-item">
                                    <button class="page-link" id="nextPageBtn">
                                        Siguiente
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        `;

        console.log('✅ Interfaz de estados de conductores renderizada');
    }

    /**
     * Configura los event listeners
     */
    setupEventListeners() {
        console.log('🔧 Configurando event listeners para estados de conductores');
        
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

        // Event listener para filtro por nombre
        const nameFilter = document.getElementById('nameFilter');
        if (nameFilter) {
            nameFilter.addEventListener('input', (e) => {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    this.handleNameFilter(e.target.value);
                }, 500);
            });
        }

        // Event listener para filtro de estado activo
        const activeFilter = document.getElementById('activeFilter');
        if (activeFilter) {
            activeFilter.addEventListener('change', (e) => {
                this.handleActiveFilter(e.target.value);
            });
        }

        // Event listener para ordenamiento
        const sortBySelect = document.getElementById('sortBySelect');
        if (sortBySelect) {
            sortBySelect.addEventListener('change', (e) => {
                this.handleSortByChange(e.target.value);
            });
        }

        const sortDirectionSelect = document.getElementById('sortDirectionSelect');
        if (sortDirectionSelect) {
            sortDirectionSelect.addEventListener('change', (e) => {
                this.handleSortDirectionChange(e.target.value);
            });
        }

        // Event listener para solo activos
        const onlyActiveSwitch = document.getElementById('onlyActiveSwitch');
        if (onlyActiveSwitch) {
            onlyActiveSwitch.addEventListener('change', (e) => {
                this.handleOnlyActiveChange(e.target.checked);
            });
        }

        // Event listener para elementos por página
        const itemsPerPageSelect = document.getElementById('itemsPerPageSelect');
        if (itemsPerPageSelect) {
            itemsPerPageSelect.addEventListener('change', (e) => {
                this.handleItemsPerPageChange(parseInt(e.target.value));
            });
        }

        // Event listener para limpiar filtros
        const clearFiltersBtn = document.getElementById('clearFiltersBtn');
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', () => {
                this.clearAllFilters();
            });
        }

        // Event listener para actualizar
        const refreshBtn = document.getElementById('refreshBtn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => {
                this.refreshData();
            });
        }

        // Event listener para agregar nuevo estado
        const addStatusBtn = document.getElementById('addStatusBtn');
        if (addStatusBtn) {
            addStatusBtn.addEventListener('click', () => {
                this.handleAddStatus();
            });
        }

        // Event listeners para botones de búsqueda
        const searchBtn = document.getElementById('searchBtn');
        if (searchBtn) {
            searchBtn.addEventListener('click', () => {
                const searchValue = document.getElementById('searchInput').value;
                this.handleSearch(searchValue);
            });
        }

        // Event listeners para exportación
        const exportExcelBtn = document.getElementById('exportExcelBtn');
        if (exportExcelBtn) {
            exportExcelBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleExportExcel();
            });
        }

        const exportPdfBtn = document.getElementById('exportPdfBtn');
        if (exportPdfBtn) {
            exportPdfBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleExportPdf();
            });
        }

        // Event listeners para paginación
        const prevPageBtn = document.getElementById('prevPageBtn');
        if (prevPageBtn) {
            prevPageBtn.addEventListener('click', () => {
                this.handlePreviousPage();
            });
        }

        const nextPageBtn = document.getElementById('nextPageBtn');
        if (nextPageBtn) {
            nextPageBtn.addEventListener('click', () => {
                this.handleNextPage();
            });
        }
    }

    /**
     * Carga los estados de conductores desde la API
     */
    async loadDriverStatuses() {
        if (this.isLoading) {
            console.log('⏳ Ya hay una carga en progreso, omitiendo...');
            return;
        }

        this.isLoading = true;
        this.showLoadingState();

        try {
            console.log('🔄 Cargando estados de conductores...');
            
            // Preparar parámetros para la API
            const params = DriverStatusService.validateParams({
                page: this.currentPage,
                perPage: this.itemsPerPage,
                search: this.searchTerm || null,
                name: this.activeFilters.name,
                active: this.activeFilters.active,
                sortBy: this.activeFilters.sortBy,
                sortDirection: this.activeFilters.sortDirection,
                onlyActive: this.activeFilters.onlyActive
            });

            // Llamar a la API
            const response = await DriverStatusService.getDriverStatuses(
                params.page,
                params.perPage,
                params.search,
                params.name,
                params.active,
                params.sortBy,
                params.sortDirection,
                params.onlyActive
            );

            // Transformar respuesta
            const transformedData = DriverStatusService.transformApiResponse(response);
            
            // Actualizar estado local
            this.driverStatuses = transformedData.driverStatuses;
            this.totalItems = transformedData.meta.total;
            this.totalPages = transformedData.meta.lastPage;
            this.currentPage = transformedData.meta.currentPage;
            this.predefinedStatuses = transformedData.predefinedStatuses;

            // Renderizar datos
            this.renderDriverStatuses();
            this.renderPagination();
            this.updateStats();

            // Mostrar notificación según la acción
            this.showSuccessNotification();

            console.log('✅ Estados de conductores cargados exitosamente:', this.driverStatuses);

        } catch (error) {
            console.error('❌ Error al cargar estados de conductores:', error);
            this.showErrorState(error.message);
            this.showErrorToast(error.message || 'Error al cargar los estados de conductores');
        } finally {
            this.isLoading = false;
            this.hideLoadingState();
        }
    }

    /**
     * Renderiza los estados de conductores en la tabla
     */
    renderDriverStatuses() {
        const tbody = document.getElementById('driverStatusTableBody');
        const noDataMessage = document.getElementById('noDataMessage');
        const tableContainer = document.getElementById('tableContainer');

        if (!tbody) {
            console.error('❌ Tabla de estados de conductores no encontrada');
            return;
        }

        if (this.driverStatuses.length === 0) {
            tbody.innerHTML = '';
            tableContainer.classList.add('d-none');
            noDataMessage.classList.remove('d-none');
            return;
        }

        tableContainer.classList.remove('d-none');
        noDataMessage.classList.add('d-none');

        tbody.innerHTML = this.driverStatuses.map(status => `
            <tr>
                <td>
                    <div class="text-muted">#${status.id}</div>
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-user-check text-primary"></i>
                        </div>
                        <div>
                            <div class="fw-bold">${this.escapeHtml(status.name)}</div>
                        </div>
                    </div>
                </td>
                <td>
                    ${status.active ? 
                        '<span class="badge bg-success-lt"><i class="fas fa-check-circle me-1"></i>Activo</span>' : 
                        '<span class="badge bg-danger-lt"><i class="fas fa-times-circle me-1"></i>Inactivo</span>'
                    }
                </td>
                <td>
                    <div class="btn-list flex-nowrap">
                        <button class="btn btn-sm btn-outline-primary" 
                                data-action="edit-driver-status"
                                data-driver-status-id="${status.id}"
                                title="Editar estado">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" 
                                data-action="delete-driver-status"
                                data-driver-status-id="${status.id}"
                                title="Eliminar estado">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');

        console.log(`✅ ${this.driverStatuses.length} estados de conductores renderizados en la tabla`);
    }

    /**
     * Renderiza la paginación
     */
    renderPagination() {
        const showingStart = document.getElementById('showingStart');
        const showingEnd = document.getElementById('showingEnd');
        const totalRecords = document.getElementById('totalRecords');
        const prevPageBtn = document.getElementById('prevPageBtn');
        const nextPageBtn = document.getElementById('nextPageBtn');
        const currentPageInfo = document.getElementById('currentPageInfo');

        // Información de paginación
        const start = this.totalItems === 0 ? 0 : (this.currentPage - 1) * this.itemsPerPage + 1;
        const end = Math.min(this.currentPage * this.itemsPerPage, this.totalItems);
        
        if (showingStart) showingStart.textContent = start;
        if (showingEnd) showingEnd.textContent = end;
        if (totalRecords) totalRecords.textContent = this.totalItems;
        if (currentPageInfo) currentPageInfo.textContent = this.currentPage;

        // Botones de paginación
        if (prevPageBtn) {
            prevPageBtn.disabled = this.currentPage <= 1;
        }

        if (nextPageBtn) {
            nextPageBtn.disabled = this.currentPage >= this.totalPages;
        }
    }

    /**
     * Actualiza las estadísticas
     */
    updateStats() {
        const totalStatsCount = document.getElementById('totalStatsCount');
        const activeStatsCount = document.getElementById('activeStatsCount');
        const inactiveStatsCount = document.getElementById('inactiveStatsCount');
        const currentPageStats = document.getElementById('currentPageStats');

        if (totalStatsCount) {
            totalStatsCount.textContent = this.totalItems.toLocaleString();
        }

        if (this.driverStatuses.length > 0) {
            const activeCount = this.driverStatuses.filter(status => status.active).length;
            const inactiveCount = this.driverStatuses.length - activeCount;
            const activePercentage = this.totalItems > 0 ? ((activeCount / this.totalItems) * 100).toFixed(1) : 0;

            if (activeStatsCount) {
                activeStatsCount.textContent = activeCount.toLocaleString();
            }

            if (inactiveStatsCount) {
                inactiveStatsCount.textContent = inactiveCount.toLocaleString();
            }

            if (currentPageStats) {
                currentPageStats.textContent = `${activePercentage}%`;
            }
        } else {
            if (activeStatsCount) {
                activeStatsCount.textContent = '0';
            }
            if (inactiveStatsCount) {
                inactiveStatsCount.textContent = '0';
            }
            if (currentPageStats) {
                currentPageStats.textContent = '0%';
            }
        }
    }

    // Métodos de manejo de eventos
    handleSearch(searchTerm) {
        console.log(`🔍 Búsqueda: "${searchTerm}"`);
        this.searchTerm = searchTerm.trim();
        this.currentPage = 1;
        this.lastAction = 'filter_change';
        this.loadDriverStatuses();
    }

    handleNameFilter(name) {
        console.log(`🏷️ Filtro por nombre: "${name}"`);
        this.activeFilters.name = name.trim() || null;
        this.currentPage = 1;
        this.lastAction = 'filter_change';
        this.loadDriverStatuses();
    }

    handleActiveFilter(active) {
        console.log(`✅ Filtro por activo: "${active}"`);
        this.activeFilters.active = active === '' ? null : active === 'true';
        this.currentPage = 1;
        this.lastAction = 'filter_change';
        this.loadDriverStatuses();
    }

    handleSortByChange(sortBy) {
        console.log(`🔧 Cambio de ordenamiento: ${sortBy}`);
        this.activeFilters.sortBy = sortBy;
        this.currentPage = 1;
        this.lastAction = 'filter_change';
        this.loadDriverStatuses();
    }

    handleSortDirectionChange(sortDirection) {
        console.log(`🔧 Cambio de dirección: ${sortDirection}`);
        this.activeFilters.sortDirection = sortDirection;
        this.currentPage = 1;
        this.lastAction = 'filter_change';
        this.loadDriverStatuses();
    }

    handleOnlyActiveChange(onlyActive) {
        console.log(`🎯 Solo activos: ${onlyActive}`);
        this.activeFilters.onlyActive = onlyActive;
        this.currentPage = 1;
        this.lastAction = 'filter_change';
        this.loadDriverStatuses();
    }

    handleItemsPerPageChange(itemsPerPage) {
        console.log(`📄 Elementos por página: ${itemsPerPage}`);
        this.itemsPerPage = itemsPerPage;
        this.currentPage = 1;
        this.lastAction = 'filter_change';
        this.loadDriverStatuses();
    }

    goToPage(page) {
        console.log(`📄 Ir a página: ${page}`);
        this.currentPage = page;
        this.lastAction = 'pagination';
        this.loadDriverStatuses();
    }

    clearAllFilters() {
        console.log('🧹 Limpiando todos los filtros');
        
        // Limpiar campos de UI
        const searchInput = document.getElementById('searchInput');
        const nameFilter = document.getElementById('nameFilter');
        const activeFilter = document.getElementById('activeFilter');
        const sortBySelect = document.getElementById('sortBySelect');
        const sortDirectionSelect = document.getElementById('sortDirectionSelect');
        const onlyActiveSwitch = document.getElementById('onlyActiveSwitch');

        if (searchInput) searchInput.value = '';
        if (nameFilter) nameFilter.value = '';
        if (activeFilter) activeFilter.value = '';
        if (sortBySelect) sortBySelect.value = 'name';
        if (sortDirectionSelect) sortDirectionSelect.value = 'asc';
        if (onlyActiveSwitch) onlyActiveSwitch.checked = false;

        // Resetear filtros
        this.searchTerm = '';
        this.activeFilters = {
            name: null,
            active: null,
            sortBy: 'name',
            sortDirection: 'asc',
            onlyActive: false
        };
        this.currentPage = 1;
        this.lastAction = 'filter_change';
        
        this.loadDriverStatuses();
    }

    refreshData() {
        console.log('🔄 Refrescando datos manualmente');
        this.lastAction = 'manual_refresh';
        this.loadDriverStatuses();
    }

    // Métodos de UI
    showLoadingState() {
        const loadingIndicator = document.getElementById('loadingIndicator');
        const tableContainer = document.getElementById('tableContainer');
        
        if (loadingIndicator) loadingIndicator.classList.remove('d-none');
        if (tableContainer) tableContainer.style.opacity = '0.5';
    }

    hideLoadingState() {
        const loadingIndicator = document.getElementById('loadingIndicator');
        const tableContainer = document.getElementById('tableContainer');
        
        if (loadingIndicator) loadingIndicator.classList.add('d-none');
        if (tableContainer) tableContainer.style.opacity = '1';
    }

    showErrorState(message) {
        const tbody = document.getElementById('driverStatusTableBody');
        if (tbody) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center py-4">
                        <div class="text-danger">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                            <div>Error al cargar datos</div>
                            <small>${this.escapeHtml(message)}</small>
                        </div>
                    </td>
                </tr>
            `;
        }
    }

    showSuccessNotification() {
        const messages = {
            'initial_load': 'Estados de conductores cargados correctamente',
            'filter_change': 'Filtros aplicados correctamente',
            'manual_refresh': 'Datos actualizados correctamente',
            'auto_refresh': 'Datos sincronizados automáticamente'
        };

        this.showSuccessToast(messages[this.lastAction] || 'Operación completada');
    }

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

    setupAutoRefresh() {
        setInterval(() => {
            if (this.isInitialized && !this.isLoading) {
                console.log('🔄 Auto-refresh de estados de conductores');
                this.lastAction = 'auto_refresh';
                this.loadDriverStatuses();
            }
        }, 5 * 60 * 1000); // 5 minutos
    }

    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text ? text.toString().replace(/[&<>"']/g, m => map[m]) : '';
    }

    /**
     * Maneja la acción de agregar nuevo estado
     */
    handleAddStatus() {
        console.log('➕ Abriendo modal para agregar nuevo estado de conductor');
        if (window.driverStatusCreateController && typeof window.driverStatusCreateController.openCreateModal === 'function') {
            window.driverStatusCreateController.openCreateModal();
        } else {
            this.showErrorToast('No se encontró el controlador de creación de estado');
        }
    }

    /**
     * Maneja la exportación a Excel
     */
    handleExportExcel() {
        console.log('📊 Exportando datos a Excel');
        // TODO: Implementar exportación a Excel
        this.showErrorToast('Exportación a Excel próximamente disponible');
    }

    /**
     * Maneja la exportación a PDF
     */
    handleExportPdf() {
        console.log('📄 Exportando datos a PDF');
        // TODO: Implementar exportación a PDF
        this.showErrorToast('Exportación a PDF próximamente disponible');
    }

    /**
     * Maneja ir a la página anterior
     */
    handlePreviousPage() {
        if (this.currentPage > 1) {
            this.currentPage--;
            this.lastAction = 'filter_change';
            this.loadDriverStatuses();
        }
    }

    /**
     * Maneja ir a la página siguiente
     */
    handleNextPage() {
        if (this.currentPage < this.totalPages) {
            this.currentPage++;
            this.lastAction = 'filter_change';
            this.loadDriverStatuses();
        }
    }
}

// Exportar para uso global
window.DriverStatusListController = DriverStatusListController;
