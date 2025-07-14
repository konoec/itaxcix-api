/**
 * Controlador para listar departamentos con filtros avanzados
 * Endpoint: /api/v1/admin/departments
 * Funcionalidades:
 * - Lista paginada de departamentos
 * - Búsqueda por nombre y ubigeo
 * - Ordenamiento por id, name, ubigeo
 * - Transformación de datos de API a formato UI
 */
class DepartmentsListController {
    constructor() {
        console.log('📋 DepartmentsListController constructor ejecutado');
        
        // Estado de la lista
        this.departments = [];
        this.currentPage = 1;
        this.itemsPerPage = 15; // Valor por defecto según API
        this.totalDepartments = 0;
        this.totalPages = 0;
        this.searchTerm = '';
        this.searchTimeout = null;
        
        // Control de origen de carga para notificaciones
        this.lastAction = 'initial_load'; // 'initial_load', 'filter_change', 'manual_refresh', 'auto_refresh'
        
        // Filtros específicos de departamentos según la API
        this.activeFilters = {
            orderBy: 'name',        // 'id', 'name', 'ubigeo'
            orderDirection: 'ASC'   // 'ASC', 'DESC'
        };
        
        // Estados de UI
        this.isLoading = false;
        this.isInitialized = false;
    }

    /**
     * Inicializa el controlador
     */
    async init() {
        console.log('📋 Inicializando DepartmentsListController...');
        try {
            // Verificar que DepartmentsService esté disponible
            await this.waitForServices();
            
            // Configurar event listeners
            this.setupEventListeners();
            
            // Cargar datos iniciales
            await this.loadDepartments();
            
            // Configurar auto-refresh cada 5 minutos
            this.setupAutoRefresh();
            
            this.isInitialized = true;
            console.log('✅ DepartmentsListController inicializado correctamente');
            
        } catch (error) {
            console.error('❌ Error al inicializar DepartmentsListController:', error);
            this.showErrorToast('Error al inicializar la lista de departamentos');
        }
    }

    /**
     * Espera a que los servicios estén disponibles
     */
    async waitForServices() {
        let attempts = 0;
        const maxAttempts = 50; // 5 segundos máximo
        
        while (attempts < maxAttempts) {
            if (window.DepartmentsService && window.GlobalToast) {
                console.log('✅ Servicios disponibles para DepartmentsListController');
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
        console.log('🔧 Configurando event listeners para departamentos');
        
        // Event listener para búsqueda
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                clearTimeout(this.searchTimeout);
                this.searchTimeout = setTimeout(() => {
                    this.handleSearch(e.target.value);
                }, 500);
            });
        }

        // Event listener para limpiar búsqueda
        const clearSearchBtn = document.getElementById('clearSearchBtn');
        if (clearSearchBtn) {
            clearSearchBtn.addEventListener('click', () => {
                const searchInput = document.getElementById('searchInput');
                if (searchInput) {
                    searchInput.value = '';
                    this.handleSearch('');
                }
            });
        }

        // Event listener para limpiar filtros
        const clearFiltersBtn = document.getElementById('clearFiltersBtn');
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', () => {
                this.clearAllFilters();
            });
        }

        // Event listener para límite de elementos por página
        const limitSelect = document.getElementById('limitSelect');
        if (limitSelect) {
            limitSelect.addEventListener('change', (e) => {
                this.handleLimitChange(parseInt(e.target.value));
            });
        }

        // Event listener para ordenamiento
        const orderBySelect = document.getElementById('orderBySelect');
        if (orderBySelect) {
            orderBySelect.addEventListener('change', (e) => {
                this.handleOrderByChange(e.target.value);
            });
        }

        // Event listener para dirección de ordenamiento
        const orderDirectionSelect = document.getElementById('orderDirectionSelect');
        if (orderDirectionSelect) {
            orderDirectionSelect.addEventListener('change', (e) => {
                this.handleOrderDirectionChange(e.target.value);
            });
        }

        // Event listener para botón de actualizar
        const refreshBtn = document.getElementById('refreshBtn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => {
                this.handleManualRefresh();
            });
        }
    }

    /**
     * Maneja la búsqueda
     */
    async handleSearch(searchTerm) {
        console.log(`🔍 Búsqueda solicitada: "${searchTerm}"`);
        this.searchTerm = searchTerm;
        this.currentPage = 1; // Resetear a la primera página
        this.lastAction = 'filter_change';
        await this.loadDepartments();
    }

    /**
     * Maneja el cambio de límite de elementos por página
     */
    async handleLimitChange(newLimit) {
        console.log(`📄 Cambio de límite: ${newLimit}`);
        this.itemsPerPage = newLimit;
        this.currentPage = 1; // Resetear a la primera página
        this.lastAction = 'filter_change';
        await this.loadDepartments();
    }

    /**
     * Maneja el cambio de campo de ordenamiento
     */
    async handleOrderByChange(orderBy) {
        console.log(`🔄 Cambio de ordenamiento: ${orderBy}`);
        this.activeFilters.orderBy = orderBy;
        this.currentPage = 1; // Resetear a la primera página
        this.lastAction = 'filter_change';
        await this.loadDepartments();
    }

    /**
     * Maneja el cambio de dirección de ordenamiento
     */
    async handleOrderDirectionChange(orderDirection) {
        console.log(`🔄 Cambio de dirección: ${orderDirection}`);
        this.activeFilters.orderDirection = orderDirection;
        this.currentPage = 1; // Resetear a la primera página
        this.lastAction = 'filter_change';
        await this.loadDepartments();
    }

    /**
     * Maneja el refresh manual
     */
    async handleManualRefresh() {
        console.log('🔄 Refresh manual solicitado');
        this.lastAction = 'manual_refresh';
        await this.loadDepartments();
    }

    /**
     * Limpia todos los filtros
     */
    clearAllFilters() {
        console.log('🧹 Limpiando todos los filtros');
        
        // Limpiar campo de búsqueda
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.value = '';
        }
        
        // Resetear selects a valores por defecto
        const limitSelect = document.getElementById('limitSelect');
        if (limitSelect) {
            limitSelect.value = '15';
        }
        
        const orderBySelect = document.getElementById('orderBySelect');
        if (orderBySelect) {
            orderBySelect.value = 'name';
        }
        
        const orderDirectionSelect = document.getElementById('orderDirectionSelect');
        if (orderDirectionSelect) {
            orderDirectionSelect.value = 'ASC';
        }
        
        // Resetear valores del controlador
        this.searchTerm = '';
        this.itemsPerPage = 15;
        this.activeFilters.orderBy = 'name';
        this.activeFilters.orderDirection = 'ASC';
        this.currentPage = 1;
        
        // Recargar datos
        this.lastAction = 'filter_change';
        this.loadDepartments();
    }

    /**
     * Carga los departamentos desde la API
     */
    async loadDepartments() {
        if (this.isLoading) {
            console.log('⏳ Carga ya en progreso, omitiendo...');
            return;
        }

        this.isLoading = true;
        this.showLoadingState();

        try {
            console.log(`📋 Cargando departamentos - página ${this.currentPage}, límite ${this.itemsPerPage}`);
            
            const response = await DepartmentsService.getDepartments(
                this.currentPage,
                this.itemsPerPage,
                this.searchTerm,
                this.activeFilters.orderBy,
                this.activeFilters.orderDirection
            );

            const transformedData = DepartmentsService.transformApiResponse(response);
            
            this.departments = transformedData.departments;
            this.totalDepartments = transformedData.pagination.total;
            this.totalPages = transformedData.pagination.totalPages;

            console.log(`✅ ${this.departments.length} departamentos cargados`);
            
            // Actualizar UI
            this.renderDepartmentsList();
            this.updatePaginationInfo();
            this.updateStatsCards();
            this.setupPagination();
            
            // Mostrar notificación según el tipo de acción
            this.showSuccessNotification();

        } catch (error) {
            console.error('❌ Error al cargar departamentos:', error);
            this.showErrorToast(error.message || 'Error al cargar departamentos');
            this.showErrorState();
        } finally {
            this.isLoading = false;
            this.hideLoadingState();
        }
    }

    /**
     * Renderiza la lista de departamentos
     */
    renderDepartmentsList() {
        const departmentsContainer = document.getElementById('departmentsTableBody');
        if (!departmentsContainer) {
            console.error('❌ Contenedor de departamentos no encontrado');
            return;
        }

        if (this.departments.length === 0) {
            departmentsContainer.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center py-5">
                        <div class="d-flex flex-column align-items-center">
                            <div class="empty-icon mb-3">
                                <i class="fas fa-search fa-3x text-muted"></i>
                            </div>
                            <h4 class="text-muted mb-2">
                                <i class="fas fa-info-circle me-2"></i>
                                No se encontraron departamentos
                            </h4>
                            <p class="text-muted mb-3">
                                ${this.searchTerm ? 
                                    `No hay resultados para "<strong>${this.escapeHtml(this.searchTerm)}</strong>"` : 
                                    'No hay departamentos registrados en el sistema'}
                            </p>
                            ${this.searchTerm ? `
                                <button class="btn btn-outline-primary" onclick="departmentsController.clearAllFilters()">
                                    <i class="fas fa-times me-1"></i>
                                    Limpiar búsqueda
                                </button>
                            ` : ''}
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        const departmentsHTML = this.departments.map((department, index) => `
            <tr class="align-middle">
                <td class="text-center text-muted fw-bold">
                    ${this.getRowNumber(index)}
                </td>
                <td>
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-3 bg-primary-lt">
                            <i class="fas fa-map-marker-alt text-primary"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">
                                ${this.escapeHtml(department.name)}
                            </div>
                            <div class="text-muted small">
                                <i class="fas fa-hashtag me-1"></i>
                                ID: ${department.id}
                            </div>
                        </div>
                    </div>
                </td>
                <td class="text-center">
                    <span class="badge bg-info-lt fs-6 px-3 py-2">
                        <i class="fas fa-barcode me-1"></i>
                        ${this.escapeHtml(department.ubigeo)}0000
                    </span>
                </td>
                <td class="text-center">
                    <div class="btn-list flex-nowrap">
                        <button class="btn btn-sm btn-outline-orange" 
                                onclick="departmentsController.handleEditDepartment(${department.id})" 
                                title="Editar">
                            <i class="fas fa-edit text-orange"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-red" 
                                onclick="departmentsController.handleDeleteDepartment(${department.id}, '${this.escapeHtml(department.name)}', '${this.escapeHtml(department.ubigeo)}')" 
                                title="Eliminar">
                            <i class="fas fa-trash text-red"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');

        departmentsContainer.innerHTML = departmentsHTML;
        console.log('✅ Lista de departamentos renderizada con nuevo diseño');
    }

    /**
     * Calcula el número de fila global
     */
    getRowNumber(index) {
        return (this.currentPage - 1) * this.itemsPerPage + index + 1;
    }

    /**
     * Escapa caracteres HTML para prevenir XSS
     */
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
     * Actualiza la información de paginación
     */
    updatePaginationInfo() {
        const paginationInfo = document.getElementById('paginationInfo');
        if (!paginationInfo) return;

        const startItem = this.departments.length === 0 ? 0 : (this.currentPage - 1) * this.itemsPerPage + 1;
        const endItem = Math.min(this.currentPage * this.itemsPerPage, this.totalDepartments);

        paginationInfo.innerHTML = `
            <i class="fas fa-info-circle me-1"></i>
            Mostrando <strong>${startItem}</strong> a <strong>${endItem}</strong> de <strong>${this.totalDepartments}</strong> departamentos
        `;
    }

    /**
     * Actualiza las tarjetas de estadísticas
     */
    updateStatsCards() {
        // Total departamentos
        const totalDepartments = document.getElementById('totalDepartments');
        if (totalDepartments) {
            totalDepartments.innerHTML = `<strong class="fs-3">${this.totalDepartments}</strong>`;
        }

        // Resultados filtrados (departamentos actuales en la página)
        const filteredResults = document.getElementById('filteredResults');
        if (filteredResults) {
            filteredResults.innerHTML = `<strong class="fs-3">${this.departments.length}</strong>`;
        }

        // Página actual
        const currentPageElement = document.getElementById('currentPage');
        if (currentPageElement) {
            currentPageElement.innerHTML = `<strong class="fs-3">${this.currentPage}/${this.totalPages}</strong>`;
        }

        // Información de ordenamiento
        const sortInfo = document.getElementById('sortInfo');
        if (sortInfo) {
            const orderByText = {
                'name': 'Nombre',
                'id': 'ID',
                'ubigeo': 'Ubigeo'
            };
            const directionText = this.activeFilters.orderDirection === 'ASC' ? 'A-Z' : 'Z-A';
            sortInfo.innerHTML = `<strong>${orderByText[this.activeFilters.orderBy]} ${directionText}</strong>`;
        }
    }

    /**
     * Configura la paginación
     */
    setupPagination() {
        const paginationContainer = document.getElementById('paginationContainer');
        if (!paginationContainer || this.totalPages <= 1) {
            if (paginationContainer) paginationContainer.innerHTML = '';
            return;
        }

        let paginationHTML = '<ul class="pagination pagination-sm m-0">';

        // Botón Anterior
        if (this.currentPage > 1) {
            paginationHTML += `
                <li class="page-item">
                    <button class="page-link" onclick="departmentsController.goToPage(${this.currentPage - 1})">
                        <i class="fas fa-chevron-left me-1"></i>
                        Anterior
                    </button>
                </li>
            `;
        } else {
            paginationHTML += `
                <li class="page-item disabled">
                    <span class="page-link">
                        <i class="fas fa-chevron-left me-1"></i>
                        Anterior
                    </span>
                </li>
            `;
        }

        // Páginas numeradas
        const startPage = Math.max(1, this.currentPage - 2);
        const endPage = Math.min(this.totalPages, this.currentPage + 2);

        if (startPage > 1) {
            paginationHTML += `
                <li class="page-item">
                    <button class="page-link" onclick="departmentsController.goToPage(1)">1</button>
                </li>
            `;
            if (startPage > 2) {
                paginationHTML += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            paginationHTML += `
                <li class="page-item ${i === this.currentPage ? 'active' : ''}">
                    <button class="page-link" onclick="departmentsController.goToPage(${i})">${i}</button>
                </li>
            `;
        }

        if (endPage < this.totalPages) {
            if (endPage < this.totalPages - 1) {
                paginationHTML += '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            paginationHTML += `
                <li class="page-item">
                    <button class="page-link" onclick="departmentsController.goToPage(${this.totalPages})">${this.totalPages}</button>
                </li>
            `;
        }

        // Botón Siguiente
        if (this.currentPage < this.totalPages) {
            paginationHTML += `
                <li class="page-item">
                    <button class="page-link" onclick="departmentsController.goToPage(${this.currentPage + 1})">
                        Siguiente
                        <i class="fas fa-chevron-right ms-1"></i>
                    </button>
                </li>
            `;
        } else {
            paginationHTML += `
                <li class="page-item disabled">
                    <span class="page-link">
                        Siguiente
                        <i class="fas fa-chevron-right ms-1"></i>
                    </span>
                </li>
            `;
        }

        paginationHTML += '</ul>';
        paginationContainer.innerHTML = paginationHTML;
    }

    /**
     * Navega a una página específica
     */
    async goToPage(page) {
        if (page < 1 || page > this.totalPages || page === this.currentPage) return;
        
        console.log(`📄 Navegando a página ${page}`);
        this.currentPage = page;
        this.lastAction = 'filter_change';
        await this.loadDepartments();
    }

    /**
     * Muestra el estado de carga
     */
    showLoadingState() {
        const refreshBtn = document.getElementById('refreshBtn');
        if (refreshBtn) {
            refreshBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                Cargando...
            `;
            refreshBtn.disabled = true;
        }

        // Actualizar tarjetas con estado de carga
        ['totalDepartments', 'filteredResults', 'currentPage'].forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.innerHTML = '<div class="spinner-border spinner-border-sm" role="status"></div>';
            }
        });
    }

    /**
     * Oculta el estado de carga
     */
    hideLoadingState() {
        const refreshBtn = document.getElementById('refreshBtn');
        if (refreshBtn) {
            refreshBtn.innerHTML = `
                <i class="fas fa-sync-alt me-1"></i>
                
            `;
            refreshBtn.disabled = false;
        }
    }

    /**
     * Muestra estado de error
     */
    showErrorState() {
        const departmentsContainer = document.getElementById('departmentsTableBody');
        if (!departmentsContainer) return;

        departmentsContainer.innerHTML = `
            <tr>
                <td colspan="4" class="text-center py-5">
                    <div class="d-flex flex-column align-items-center">
                        <div class="empty-icon mb-3">
                            <i class="fas fa-exclamation-triangle fa-3x text-danger"></i>
                        </div>
                        <h4 class="text-danger mb-2">
                            <i class="fas fa-times-circle me-2"></i>
                            Error al cargar departamentos
                        </h4>
                        <p class="text-muted mb-3">
                            Ocurrió un problema al obtener los datos del servidor
                        </p>
                        <div class="btn-list">
                            <button class="btn btn-primary" onclick="departmentsController.handleManualRefresh()">
                                <i class="fas fa-redo me-1"></i>
                                Intentar nuevamente
                            </button>
                            <button class="btn btn-outline-secondary" onclick="departmentsController.clearAllFilters()">
                                <i class="fas fa-times me-1"></i>
                                Limpiar filtros
                            </button>
                        </div>
                    </div>
                </td>
            </tr>
        `;

        // Resetear tarjetas de estadísticas
        ['totalDepartments', 'filteredResults', 'currentPage'].forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.innerHTML = '<span class="text-danger"><i class="fas fa-times"></i></span>';
            }
        });
    }

    /**
     * Muestra notificación de éxito según el contexto
     */
    showSuccessNotification() {
        if (!this.isInitialized) return; // No mostrar en carga inicial

        const messages = {
            manual_refresh: 'Lista de departamentos actualizada correctamente',
            filter_change: null, // No mostrar notificación para cambios de filtro
            auto_refresh: 'Datos actualizados automáticamente'
        };

        const message = messages[this.lastAction];
        if (message) {
            this.showSuccessToast(message);
        }
    }

    /**
     * Muestra toast de éxito
     */
    showSuccessToast(message) {
        if (window.GlobalToast) {
            GlobalToast.show(message, 'success');
        }
    }

    /**
     * Muestra toast de error
     */
    showErrorToast(message) {
        if (window.GlobalToast) {
            GlobalToast.show(message, 'error');
        }
    }

    /**
     * Configura el auto-refresh
     */
    setupAutoRefresh() {
        // Auto-refresh cada 5 minutos
        setInterval(async () => {
            if (!document.hidden && this.isInitialized) {
                console.log('🔄 Auto-refresh ejecutado');
                this.lastAction = 'auto_refresh';
                await this.loadDepartments();
            }
        }, 5 * 60 * 1000);
    }

    /**
     * Maneja la edición de un departamento
     */
    handleEditDepartment(departmentId) {
        console.log(`✏️ Editar departamento: ${departmentId}`);
        
        // Buscar el departamento en los datos actuales
        const department = this.departments.find(d => d.id === departmentId);
        
        if (!department) {
            this.showErrorToast('No se pudo encontrar el departamento seleccionado');
            return;
        }

        // Verificar que el controlador de actualización esté disponible
        if (!window.departmentUpdateController) {
            this.showErrorToast('El sistema de edición no está disponible');
            console.error('❌ DepartmentUpdateController no encontrado');
            return;
        }

        // Abrir modal de edición
        window.departmentUpdateController.openEditModal(departmentId, department);
    }

    /**
     * Ver detalles de un departamento
     */
    viewDepartment(departmentId) {
        console.log(`👁️ Ver departamento: ${departmentId}`);
        // Implementar vista de detalles según sea necesario
        this.showSuccessToast(`Ver detalles del departamento ID: ${departmentId}`);
    }

    /**
     * Mostrar información adicional de un departamento
     */
    showDepartmentInfo(departmentId) {
        console.log(`ℹ️ Información del departamento: ${departmentId}`);
        const department = this.departments.find(d => d.id === departmentId);
        if (department) {
            this.showSuccessToast(`Información: ${department.name} (Ubigeo: ${department.ubigeo})`);
        }
    }

    /**
     * Maneja la eliminación de un departamento
     */
    handleDeleteDepartment(departmentId, departmentName, departmentUbigeo) {
        console.log(`🗑️ Eliminar departamento: ${departmentId}`);
        
        // Verificar que el modal global esté disponible
        if (!window.globalConfirmationModal) {
            this.showErrorToast('El sistema de eliminación no está disponible');
            console.error('❌ GlobalConfirmationModal no encontrado');
            return;
        }

        // SVG de Tabler para departamentos (map-pin - mismo ícono que el listado)
        const departmentIconSvg = `
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <circle cx="12" cy="11" r="3"/>
                <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z"/>
            </svg>
        `;

        // Abrir modal de confirmación global
        window.globalConfirmationModal.showConfirmation({
            title: '¿Está seguro de eliminar?',
            name: departmentName,
            subtitle: `Ubigeo: ${departmentUbigeo}`,
            iconSvg: departmentIconSvg,
            avatarColor: 'bg-primary',
            confirmText: 'Eliminar',
            loadingText: 'Eliminando departamento...',
            onConfirm: async (data) => {
                // Ejecutar eliminación
                await this.deleteDepartment(departmentId);
            },
            data: { id: departmentId, name: departmentName, ubigeo: departmentUbigeo }
        });
    }

    /**
     * Elimina un departamento usando el servicio de eliminación
     */
    async deleteDepartment(departmentId) {
        try {
            const departmentDeleteService = new DepartmentDeleteService();
            const result = await departmentDeleteService.deleteDepartment(departmentId);

            if (result.success) {
                console.log('✅ Departamento eliminado exitosamente');
                
                // Mostrar notificación de éxito
                this.showSuccessToast('Departamento eliminado correctamente');
                
                // Recargar la lista para reflejar los cambios
                this.lastAction = 'delete_refresh';
                await this.loadDepartments();
                
            } else {
                throw new Error(result.message || 'Error al eliminar el departamento');
            }

        } catch (error) {
            console.error('❌ Error eliminando departamento:', error);
            throw error; // Re-lanzar el error para que el modal lo maneje
        }
    }

    /**
     * Callback ejecutado cuando se elimina un departamento
     */
    onDepartmentDeleted(departmentId) {
        console.log(`✅ Departamento eliminado: ${departmentId}`);
        
        // Recargar la lista para reflejar los cambios
        this.lastAction = 'delete_refresh';
        this.loadDepartments();
    }

    /**
     * Limpia los recursos del controlador
     */
    destroy() {
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
        }
        console.log('🧹 DepartmentsListController destruido');
    }
}

// Crear instancia global
window.departmentsController = new DepartmentsListController();
