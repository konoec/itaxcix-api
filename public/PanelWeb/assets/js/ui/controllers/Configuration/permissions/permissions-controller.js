/**
 * Controlador para la gestión de permisos
 * Implementación completa con funcionalidad de tabla y modales
 */
class PermissionsController {
    constructor() {
        this.isInitialized = false;
        this.permissions = []; // Todos los permisos cargados desde la API
        this.filteredPermissions = []; // Permisos filtrados por búsqueda
        this.currentPage = 1;
        this.itemsPerPage = 9;
        this.totalPermissions = 0;
        this.totalPages = 0;
        this.editingPermission = null;
        this.searchTerm = '';
        this.searchTimeout = null;
        
        // Filtros adicionales
        this.activeFilters = {
            status: [], // ['active', 'inactive']
            type: []    // ['web', 'app']
        };
        
        this.init();
    }

    /**
     * Inicializa el controlador
     */
    async init() {
        console.log('🔐 Inicializando PermissionsController...');
        try {
            // Esperar a que los servicios estén disponibles
            await this.waitForServices();
            
            this.setupEventListeners();
            this.loadPermissions();
            
            this.isInitialized = true;
            console.log('✅ PermissionsController inicializado correctamente');
            
        } catch (error) {
            console.error('❌ Error al inicializar PermissionsController:', error);
        }
    }

    /**
     * Configura los event listeners
     */
    setupEventListeners() {
        // Botón crear permiso
        const createBtn = document.getElementById('create-permission-btn');
        if (createBtn) {
            createBtn.addEventListener('click', () => this.openCreateModal());
        }

        // Modal events para Tabler
        const modal = document.getElementById('permission-modal');
        const saveBtn = document.getElementById('save-permission-btn');
        const form = document.getElementById('permission-form');

        if (saveBtn) {
            saveBtn.addEventListener('click', () => this.handleFormSubmit());
        }

        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleFormSubmit();
            });
        }

        // Paginación
        const prevBtn = document.getElementById('prev-page-btn');
        const nextBtn = document.getElementById('next-page-btn');

        if (prevBtn) {
            prevBtn.addEventListener('click', () => this.previousPage());
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => this.nextPage());
        }

        // Búsqueda
        const searchInput = document.getElementById('search-permissions');

        if (searchInput) {
            // Búsqueda dinámica en tiempo real
            searchInput.addEventListener('input', (e) => this.handleDynamicSearch(e.target.value));
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    this.handleDynamicSearch(e.target.value);
                }
            });
        }

        // Filtros de estado y tipo
        this.setupFilterListeners();
        
        // Botón de refrescar
        this.setupRefreshButton();
    }

    /**
     * Configura el botón de refrescar
     */
    setupRefreshButton() {
        const refreshBtn = document.getElementById('refresh-permissions-btn');
        
        if (refreshBtn) {
            refreshBtn.addEventListener('click', async () => {
                console.log('🔄 Refrescando tabla de permisos...');
                
                // Mostrar indicador de carga en el botón
                const icon = refreshBtn.querySelector('i');
                const originalText = refreshBtn.innerHTML;
                
                if (icon) {
                    icon.classList.add('fa-spin');
                }
                
                try {
                    // Resetear filtros y búsqueda
                    this.searchTerm = '';
                    this.activeFilters = {
                        status: [],
                        type: []
                    };
                    
                    // Limpiar el input de búsqueda
                    const searchInput = document.getElementById('search-permissions');
                    const clearBtn = document.getElementById('clear-search');
                    
                    if (searchInput) {
                        searchInput.value = '';
                    }
                    
                    if (clearBtn) {
                        clearBtn.style.display = 'none';
                    }
                    
                    // Resetear checkboxes de filtros
                    const filterCheckboxes = [
                        'filter-active',
                        'filter-inactive', 
                        'filter-web',
                        'filter-app'
                    ];
                    
                    filterCheckboxes.forEach(filterId => {
                        const checkbox = document.getElementById(filterId);
                        if (checkbox) {
                            checkbox.checked = false;
                        }
                    });
                    
                    // Resetear página a la primera
                    this.currentPage = 1;
                    
                    // Recargar datos desde la API
                    await this.loadPermissions();
                    
                    // Mostrar mensaje de éxito
                    if (typeof window.showToast === 'function') {
                        window.showToast('Lista de permisos actualizada correctamente', 'success');
                    }
                    
                } catch (error) {
                    console.error('❌ Error al refrescar permisos:', error);
                    
                    if (typeof window.showToast === 'function') {
                        window.showToast('Error al actualizar la lista de permisos', 'error');
                    } else {
                        alert('Error al actualizar la lista de permisos');
                    }
                } finally {
                    // Quitar indicador de carga del botón
                    if (icon) {
                        icon.classList.remove('fa-spin');
                    }
                }
            });
        } else {
            console.warn('⚠️ Botón de refrescar no encontrado (refresh-permissions-btn)');
        }
    }

    /**
     * Configura los event listeners para los filtros
     */
    setupFilterListeners() {
        const filterCheckboxes = [
            'filter-active',
            'filter-inactive', 
            'filter-web',
            'filter-app'
        ];

        filterCheckboxes.forEach(filterId => {
            const checkbox = document.getElementById(filterId);
            if (checkbox) {
                checkbox.addEventListener('change', () => this.handleFilterChange());
            }
        });
    }

    /**
     * Carga los permisos desde la API con los filtros actuales
     */
    async loadPermissions() {
        console.log('� Carga inicial de permisos desde API...');
        // Usar el método unificado para cargar desde API
        await this.reloadPermissionsFromAPI();
    }

    /**
     * Muestra/oculta el indicador de carga
     */
    showLoading(show) {
        const loadingRow = document.getElementById('permissions-loading-row');
        const permissionsList = document.getElementById('permissions-list');
        
        if (loadingRow && permissionsList) {
            loadingRow.style.display = show ? 'table-row-group' : 'none';
            permissionsList.style.display = show ? 'none' : 'table-row-group';
        }
    }

    /**
     * Renderiza la tabla de permisos usando los datos filtrados y paginados
     */
    renderTable() {
        const tbody = document.getElementById('permissions-list');
        if (!tbody) return;

        tbody.innerHTML = '';

        // Si no hay permisos filtrados y hay filtros activos, mostrar mensaje de no resultados
        if (this.filteredPermissions.length === 0 && (this.searchTerm || this.activeFilters.status.length > 0 || this.activeFilters.type.length > 0)) {
            const row = document.createElement('tr');
            const filtersText = this.getActiveFiltersText().replace(' (filtrado por: ', '').replace(')', '');
            row.innerHTML = `
                <td colspan="4" class="text-center py-4 text-muted">
                    <div class="empty">
                        <div class="empty-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <p class="empty-title">No se encontraron resultados</p>
                        <p class="empty-subtitle text-muted">
                            No hay permisos que coincidan con los filtros: <strong>${filtersText}</strong>
                        </p>
                        <div class="empty-action">
                            <button onclick="permissionsController.clearDynamicSearch()" class="btn btn-outline-primary">
                                <i class="fas fa-times me-2"></i>
                                Limpiar filtros
                            </button>
                        </div>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
            return;
        }

        // Si no hay permisos y no hay búsqueda, mostrar mensaje general
        if (this.filteredPermissions.length === 0) {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td colspan="4" class="text-center py-4 text-muted">
                    <div class="empty">
                        <div class="empty-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <p class="empty-title">No hay permisos disponibles</p>
                        <p class="empty-subtitle text-muted">
                            Crea tu primer permiso haciendo clic en "Crear permiso"
                        </p>
                        <div class="empty-action">
                            <button onclick="permissionsController.openCreateModal()" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                Crear permiso
                            </button>
                        </div>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
            return;
        }

        // Calcular la paginación local
        const start = (this.currentPage - 1) * this.itemsPerPage;
        const end = start + this.itemsPerPage;
        const pageItems = this.filteredPermissions.slice(start, end);

        // Renderizar permisos de la página actual
        pageItems.forEach(permission => {
            const row = document.createElement('tr');
            
            // Obtener clases y estilos para badges
            const statusBadgeClass = this.getStatusBadgeClass(permission.active);
            const platformBadgeClass = this.getPlatformBadgeClass(permission.web);
            const platformStyle = permission.web ? `style="${this.getGoldenBadgeStyle()}"` : '';
            
            row.innerHTML = `
                <td>
                    <div class="d-flex py-1 align-items-center">
                        <div class="flex-fill">
                            <div class="font-weight-medium">${permission.name || 'Sin nombre'}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="${statusBadgeClass}">${this.getStatusText(permission.active)}</span>
                </td>
                <td>
                    <span class="${platformBadgeClass}" ${platformStyle}>${this.getPlatformText(permission.web)}</span>
                </td>
                <td>
                    <div class="btn-list">
                        <button class="btn btn-outline-primary btn-sm" onclick="permissionsController.editPermission(${permission.id})" title="Editar permiso">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-outline-danger btn-sm" onclick="permissionsController.deletePermission(${permission.id})" title="Eliminar permiso">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    /**
     * Maneja la búsqueda dinámica en tiempo real
     */
    handleDynamicSearch(searchValue) {
        // Limpiar el timeout anterior
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
        }

        // Configurar nuevo timeout para la búsqueda (debounce de 300ms para mayor dinamismo)
        this.searchTimeout = setTimeout(() => {
            this.performDynamicSearch(searchValue);
        }, 300);
    }

    /**
     * Maneja los cambios en los filtros de estado y tipo
     * Cada cambio de filtro hace una nueva petición a la API
     */
    handleFilterChange() {
        console.log('🔄 Filtros cambiados, recargando desde API...');
        this.updateActiveFilters();
        this.reloadPermissionsFromAPI();
    }

    /**
     * Actualiza los filtros activos basado en los checkboxes
     */
    updateActiveFilters() {
        // Filtros de estado
        this.activeFilters.status = [];
        if (document.getElementById('filter-active')?.checked) {
            this.activeFilters.status.push('active');
        }
        if (document.getElementById('filter-inactive')?.checked) {
            this.activeFilters.status.push('inactive');
        }

        // Filtros de tipo
        this.activeFilters.type = [];
        if (document.getElementById('filter-web')?.checked) {
            this.activeFilters.type.push('web');
        }
        if (document.getElementById('filter-app')?.checked) {
            this.activeFilters.type.push('app');
        }

        console.log('🔍 Filtros actualizados:', this.activeFilters);
    }

    /**
     * Recarga los permisos desde la API aplicando los filtros activos
     */
    async reloadPermissionsFromAPI() {
        try {
            this.showLoading(true);
            
            // Convertir filtros a parámetros de API
            const searchTerm = this.searchTerm || null;
            const webOnly = this.getWebOnlyFilter();
            const activeOnly = this.getActiveOnlyFilter();
            
            console.log('📡 === DEBUGGING PETICIÓN API ===');
            console.log('📡 Parámetros enviados a la API:', {
                search: searchTerm,
                webOnly,
                activeOnly,
                page: 1,
                limit: 100
            });
            console.log('📡 URL de la API:', `${PermissionService.API_BASE_URL}/permissions`);
            
            // Hacer petición a la API con filtros
            const response = await PermissionService.getPermissions(1, 100, searchTerm, webOnly, activeOnly);
            
            console.log('📋 === DEBUGGING RESPUESTA API ===');
            console.log('📋 Respuesta completa:', response);
            console.log('📋 Estado de éxito:', response.success);
            console.log('📋 Mensaje:', response.message);
            console.log('📋 Estructura de data:', response.data);
            
            if (response.data && response.data.permissions) {
                console.log('📋 Número de permisos recibidos:', response.data.permissions.length);
                console.log('📋 Primeros 3 permisos:', response.data.permissions.slice(0, 3));
            }
            
            if (response.success === false) {
                console.error('❌ API devolvió error:', response.message);
                throw new Error(response.message || 'Error al cargar permisos filtrados');
            }
            
            // Extraer datos según la estructura de la API
            if (response.data && response.data.permissions) {
                this.permissions = response.data.permissions;
                this.filteredPermissions = [...this.permissions]; // Ya están filtrados por la API
                console.log(`✅ ${this.permissions.length} permisos cargados con filtros desde API`);
                
                // Debugging detallado de los permisos recibidos
                if (this.searchTerm) {
                    console.log(`🔍 === ANÁLISIS DE RESULTADOS DE BÚSQUEDA ===`);
                    console.log(`🔍 Término buscado: "${this.searchTerm}"`);
                    console.log(`🔍 Permisos encontrados: ${this.permissions.length}`);
                    this.permissions.forEach((permission, index) => {
                        const nameMatch = permission.name.toLowerCase().includes(this.searchTerm.toLowerCase());
                        console.log(`🔍 Permiso ${index + 1}: "${permission.name}" - Coincide: ${nameMatch}`);
                    });
                }
                
                // Si hay más páginas, cargar todas iterativamente
                const totalPages = response.data.totalPages || 1;
                if (totalPages > 1) {
                    console.log(`📄 Cargando ${totalPages - 1} páginas adicionales con filtros...`);
                    
                    for (let page = 2; page <= totalPages; page++) {
                        const additionalResponse = await PermissionService.getPermissions(page, 100, searchTerm, webOnly, activeOnly);
                        if (additionalResponse.success !== false && additionalResponse.data && additionalResponse.data.permissions) {
                            this.permissions = this.permissions.concat(additionalResponse.data.permissions);
                            this.filteredPermissions = [...this.permissions];
                            console.log(`✅ Página ${page} cargada con filtros, total: ${this.permissions.length} permisos`);
                        }
                    }
                }
                
            } else {
                console.warn('⚠️ Respuesta sin data.permissions:', response);
                this.permissions = [];
                this.filteredPermissions = [];
            }
            
            // Actualizar paginación
            this.totalPermissions = this.filteredPermissions.length;
            this.totalPages = Math.ceil(this.totalPermissions / this.itemsPerPage);
            this.currentPage = 1;
            
            console.log('📊 === RESULTADO FINAL ===');
            console.log('📊 Total permisos filtrados:', this.totalPermissions);
            console.log('📊 Páginas totales:', this.totalPages);
            console.log('📊 Página actual:', this.currentPage);
            
            this.showLoading(false);
            this.renderTable();
            this.updatePagination();
            
        } catch (error) {
            console.error('❌ Error recargando permisos con filtros desde API:', error);
            this.showLoading(false);
            
            // Manejo específico de errores de búsqueda
            let errorMessage = 'Error al aplicar filtros';
            
            if (error.message && error.message.includes('400')) {
                if (this.searchTerm && this.searchTerm.length < 2) {
                    errorMessage = 'La búsqueda requiere al menos 2 caracteres';
                } else {
                    errorMessage = 'Parámetros de búsqueda inválidos';
                }
                console.error('❌ Error 400 - Bad Request:', {
                    searchTerm: this.searchTerm,
                    searchLength: this.searchTerm?.length,
                    filters: this.activeFilters
                });
            } else {
                errorMessage = error.message || errorMessage;
            }
            
            // Mostrar mensaje de error temporal en lugar de toast persistente
            const paginationInfo = document.getElementById('pagination-info');
            if (paginationInfo) {
                paginationInfo.textContent = errorMessage;
                paginationInfo.style.color = '#e74c3c';
                
                // Restaurar mensaje normal después de 3 segundos
                setTimeout(() => {
                    paginationInfo.style.color = '';
                    this.updateSearchUI();
                }, 3000);
            }
            
            // Solo mostrar toast para errores no relacionados con validación de búsqueda
            if (!error.message?.includes('400') && window.showToast) {
                window.showToast(errorMessage, 'error');
            }
        }
    }

    /**
     * Convierte los filtros de tipo a parámetro webOnly para la API
     * @returns {boolean|null} true = solo web, false = solo app, null = ambos
     */
    getWebOnlyFilter() {
        const hasWeb = this.activeFilters.type.includes('web');
        const hasApp = this.activeFilters.type.includes('app');
        
        if (hasWeb && hasApp) {
            return null; // Ambos tipos
        } else if (hasWeb) {
            return true; // Solo web
        } else if (hasApp) {
            return false; // Solo app
        } else {
            return null; // Sin filtros de tipo
        }
    }

    /**
     * Convierte los filtros de estado a parámetro activeOnly para la API
     * @returns {boolean|null} true = solo activos, false = solo inactivos, null = ambos
     */
    getActiveOnlyFilter() {
        const hasActive = this.activeFilters.status.includes('active');
        const hasInactive = this.activeFilters.status.includes('inactive');
        
        if (hasActive && hasInactive) {
            return null; // Ambos estados
        } else if (hasActive) {
            return true; // Solo activos
        } else if (hasInactive) {
            return false; // Solo inactivos
        } else {
            return null; // Sin filtros de estado
        }
    }

    /**
     * Obtiene un resumen legible de los filtros activos
     */
    getActiveFiltersText() {
        const filterInfo = [];
        
        if (this.searchTerm) {
            filterInfo.push(`búsqueda: "${this.searchTerm}"`);
        }
        
        if (this.activeFilters.status.length > 0) {
            const statusLabels = this.activeFilters.status.map(s => s === 'active' ? 'Activo' : 'Inactivo');
            filterInfo.push(`estado: [${statusLabels.join(', ')}]`);
        }
        
        if (this.activeFilters.type.length > 0) {
            const typeLabels = this.activeFilters.type.map(t => t === 'web' ? 'Web' : 'App');
            filterInfo.push(`tipo: [${typeLabels.join(', ')}]`);
        }
        
        return filterInfo.length > 0 ? ` (filtrado por: ${filterInfo.join(', ')})` : '';
    }

    /**
     * Ejecuta la búsqueda dinámica haciendo petición a la API
     */
    performDynamicSearch(searchValue) {
        const trimmedSearch = searchValue.trim();
        
        console.log(`🔍 === DEBUGGING BÚSQUEDA DE PERMISOS ===`);
        console.log(`🔍 Término de búsqueda original: "${searchValue}"`);
        console.log(`🔍 Término de búsqueda procesado: "${trimmedSearch}"`);
        console.log(`🔍 Longitud del término: ${trimmedSearch.length}`);
        console.log(`🔍 Filtros activos:`, this.activeFilters);
        
        // Validar término de búsqueda - mínimo 2 caracteres para evitar Error 400
        if (trimmedSearch.length > 0 && trimmedSearch.length < 2) {
            console.log(`⚠️ Término de búsqueda muy corto (${trimmedSearch.length} caracteres). Mínimo requerido: 2 caracteres`);
            
            // Mostrar mensaje temporal en la UI
            const paginationInfo = document.getElementById('pagination-info');
            if (paginationInfo) {
                paginationInfo.textContent = 'Ingrese al menos 2 caracteres para buscar';
                paginationInfo.style.color = '#f39c12';
            }
            
            return; // No hacer la búsqueda
        }
        
        // Resetear el color del texto de paginación
        const paginationInfo = document.getElementById('pagination-info');
        if (paginationInfo) {
            paginationInfo.style.color = '';
        }
        
        this.searchTerm = trimmedSearch;
        
        if (this.searchTerm === '') {
            console.log(`🔍 Búsqueda vacía, cargando todos los permisos`);
        } else {
            console.log(`🔍 Realizando búsqueda con término válido: "${this.searchTerm}"`);
        }
        
        // Recargar desde API con el nuevo término de búsqueda y filtros actuales
        this.reloadPermissionsFromAPI();
    }

    /**
     * Limpia la búsqueda dinámica y los filtros, recargando desde API
     */
    clearDynamicSearch() {
        const searchInput = document.getElementById('search-permissions');
        const clearBtn = document.getElementById('clear-search');
        const searchIcon = document.querySelector('.search-icon');
        const searchContainer = document.querySelector('.search-container');
        
        if (searchInput) {
            searchInput.value = '';
            searchInput.classList.remove('searching');
        }
        
        if (clearBtn) clearBtn.style.display = 'none';
        if (searchIcon) searchIcon.style.display = 'block';
        if (searchContainer) {
            searchContainer.classList.remove('searching');
            searchContainer.classList.remove('loading');
        }

        // Limpiar también todos los filtros
        this.clearAllFilters();
        
        // Limpiar término de búsqueda
        this.searchTerm = '';
        
        // Recargar todos los permisos desde API sin filtros
        this.reloadPermissionsFromAPI();
        
        console.log('🔍 Búsqueda y filtros limpiados, recargando todos los permisos desde API');
    }

    /**
     * Limpia todos los filtros de estado y tipo
     */
    clearAllFilters() {
        // Desmarcar todos los checkboxes
        const filterCheckboxes = [
            'filter-active',
            'filter-inactive', 
            'filter-web',
            'filter-app'
        ];

        filterCheckboxes.forEach(filterId => {
            const checkbox = document.getElementById(filterId);
            if (checkbox) {
                checkbox.checked = false;
            }
        });

        // Resetear filtros activos
        this.activeFilters = {
            status: [],
            type: []
        };
    }

    /**
     * Actualiza la UI después de una búsqueda o filtrado
     */
    updateSearchUI() {
        const paginationInfo = document.getElementById('pagination-info');
        if (paginationInfo) {
            const startItem = this.totalPermissions === 0 ? 0 : (this.currentPage - 1) * this.itemsPerPage + 1;
            const endItem = Math.min(this.currentPage * this.itemsPerPage, this.totalPermissions);
            
            let infoText = `Mostrando ${startItem}-${endItem} de ${this.totalPermissions} permisos`;
            infoText += this.getActiveFiltersText();
            
            paginationInfo.textContent = infoText;
        }
    }

    /**
     * Actualiza la información de paginación
     */
    updatePagination() {
        this.updateSearchUI();

        const prevBtn = document.getElementById('prev-page-btn');
        const nextBtn = document.getElementById('next-page-btn');
        
        if (prevBtn) {
            prevBtn.disabled = this.currentPage === 1;
        }
        
        if (nextBtn) {
            nextBtn.disabled = this.currentPage >= this.totalPages;
        }
    }

    /**
     * Página anterior
     */
    previousPage() {
        if (this.currentPage > 1) {
            this.currentPage--;
            this.renderTable();
            this.updatePagination();
        }
    }

    /**
     * Página siguiente
     */
    nextPage() {
        if (this.currentPage < this.totalPages) {
            this.currentPage++;
            this.renderTable();
            this.updatePagination();
        }
    }

    /**
     * Abre el modal para crear permiso
     */
    openCreateModal() {
        this.editingPermission = null;
        this.resetForm();
        document.getElementById('modal-title').textContent = 'Crear Permiso';
        
        // Usar Bootstrap modal de Tabler
        const modal = new bootstrap.Modal(document.getElementById('permission-modal'));
        modal.show();
    }

    /**
     * Abre el modal para editar permiso
     */
    editPermission(id) {
        const permission = this.permissions.find(p => p.id === id);
        if (!permission) return;

        this.editingPermission = permission;
        this.populateForm(permission);
        document.getElementById('modal-title').textContent = 'Editar Permiso';
        
        // Usar Bootstrap modal de Tabler
        const modal = new bootstrap.Modal(document.getElementById('permission-modal'));
        modal.show();
    }

    /**
     * Elimina un permiso
     */
    async deletePermission(id) {
        const permission = this.permissions.find(p => p.id == id);
        const permissionName = permission ? permission.name : `ID ${id}`;
        
        if (confirm(`¿Está seguro de que desea eliminar el permiso "${permissionName}"?`)) {
            try {
                console.log('🗑️ Eliminando permiso con ID:', id);
                const response = await PermissionService.deletePermission(id);
                console.log('📋 Respuesta del servicio deletePermission:', response);
                
                if (response.success === true) {
                    // Éxito: Mostrar mensaje de éxito
                    const successMessage = response.message || 'Permiso eliminado exitosamente';
                    console.log('✅ Permiso eliminado exitosamente:', successMessage);
                    
                    if (window.showToast) {
                        window.showToast(successMessage, 'success');
                    }
                    
                    // Recargar la lista después de eliminar
                    await this.loadPermissions();
                    
                } else {
                    // Error: Mostrar mensaje específico del servidor
                    const errorMessage = response.message || 'Error desconocido al eliminar el permiso';
                    console.error('❌ Error al eliminar permiso:', errorMessage);
                    
                    if (window.showToast) {
                        window.showToast(errorMessage, 'error');
                    }
                }
                
            } catch (error) {
                console.error('❌ Excepción eliminando permiso:', error);
                const errorMessage = error.message || 'Error de conexión al eliminar el permiso';
                
                if (window.showToast) {
                    window.showToast(errorMessage, 'error');
                }
            }
        }
    }

    /**
     * Cierra el modal
     */
    closeModal() {
        document.getElementById('permission-modal').style.display = 'none';
        this.resetForm();
        this.editingPermission = null;
    }

    /**
     * Resetea el formulario
     */
    resetForm() {
        const form = document.getElementById('permission-form');
        if (form) {
            form.reset();
        }
        
        // Habilitar el campo de nombre para creación
        const nameField = document.getElementById('permission-name');
        if (nameField) {
            nameField.readOnly = false;
            nameField.style.backgroundColor = '';
            nameField.style.cursor = '';
            nameField.title = '';
        }
    }

    /**
     * Llena el formulario con datos del permiso
     */
    populateForm(permission) {
        const nameField = document.getElementById('permission-name');
        if (nameField) {
            nameField.value = permission.name || '';
            // Bloquear el campo de nombre para edición
            nameField.readOnly = true;
            nameField.style.backgroundColor = '#f5f5f5';
            nameField.style.cursor = 'not-allowed';
            nameField.title = 'El nombre del permiso no se puede modificar';
        }
        
        document.getElementById('permission-active').value = permission.active ? 'true' : 'false';
        document.getElementById('permission-web').value = permission.web ? 'true' : 'false';
    }

    /**
     * Maneja el envío del formulario
     */
    async handleFormSubmit() {
        const form = document.getElementById('permission-form');
        const formData = new FormData(form);
        const permissionData = {
            name: formData.get('name'),
            active: formData.get('active') === 'true',
            web: formData.get('web') === 'true'
        };

        // Validación básica
        if (!permissionData.name || permissionData.name.trim().length === 0) {
            if (window.showToast) {
                window.showToast('El nombre del permiso es requerido', 'error');
            } else {
                alert('El nombre del permiso es requerido');
            }
            return;
        }

        // Validación de duplicados (solo para creación, ya que en edición el nombre está bloqueado)
        if (!this.editingPermission) {
            // Obtener los permisos actuales de la tabla para verificar duplicados
            const tbody = document.getElementById('permissions-list');
            const existingRows = tbody ? tbody.querySelectorAll('tr') : [];
            const existingNames = [];
            
            existingRows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length > 0) {
                    // Buscar el nombre del permiso en la primera celda
                    const nameElement = cells[0].querySelector('.font-weight-medium');
                    if (nameElement && nameElement.textContent !== 'Sin nombre') {
                        existingNames.push(nameElement.textContent.toLowerCase());
                    }
                }
            });
            
            if (existingNames.includes(permissionData.name.toLowerCase())) {
                const errorMessage = `El nombre "${permissionData.name}" ya existe. Por favor, elija un nombre diferente.`;
                
                if (window.showToast) {
                    window.showToast(errorMessage, 'error');
                } else {
                    alert(errorMessage);
                }
                
                // Resaltar el campo nombre
                const nameField = document.getElementById('permission-name');
                if (nameField) {
                    nameField.style.borderColor = '#dc3545';
                    nameField.focus();
                    nameField.select();
                    setTimeout(() => nameField.style.borderColor = '', 3000);
                }
                return;
            }
        }

        try {
            if (this.editingPermission) {
                // Actualizar permiso existente
                const permissionToUpdate = {
                    id: this.editingPermission.id,
                    name: this.editingPermission.name,
                    active: permissionData.active,
                    web: permissionData.web
                };
                const response = await PermissionService.updatePermission(permissionToUpdate);
                
                if (!response.success || response.success === false) {
                    throw new Error(response.message || 'Error al actualizar el permiso');
                }
                
                if (window.showToast) {
                    window.showToast('Permiso actualizado exitosamente', 'success');
                }
            } else {
                // Crear nuevo permiso
                const response = await PermissionService.createPermission(permissionData);
                
                if (!response.success || response.success === false) {
                    throw new Error(response.message || 'Error al crear el permiso');
                }
                
                if (window.showToast) {
                    window.showToast('Permiso creado exitosamente', 'success');
                }
            }

            // Recargar la lista después de guardar
            await this.loadPermissions();
            
            // Cerrar modal usando Bootstrap
            const modal = bootstrap.Modal.getInstance(document.getElementById('permission-modal'));
            if (modal) modal.hide();
            
        } catch (error) {
            console.error('Error guardando permiso:', error);
            if (window.showToast) {
                window.showToast(error.message || 'Error al guardar el permiso', 'error');
            } else {
                alert(error.message || 'Error al guardar el permiso');
            }
        }
    }

    /**
     * Obtiene la clase CSS para el badge de estado
     */
    getStatusBadgeClass(isActive) {
        return isActive ? 'badge bg-success text-white' : 'badge bg-danger text-white';
    }

    /**
     * Obtiene la clase CSS para el badge de plataforma
     */
    getPlatformBadgeClass(isWeb) {
        return isWeb ? 'badge text-white' : 'badge bg-info text-white';
    }

    /**
     * Obtiene el estilo inline para el badge dorado (Web)
     */
    getGoldenBadgeStyle() {
        return 'background-color: #d4af37 !important; color: white !important;';
    }

    /**
     * Obtiene el texto del estado
     */
    getStatusText(isActive) {
        return isActive ? 'Activo' : 'Inactivo';
    }

    /**
     * Obtiene el texto de la plataforma
     */
    getPlatformText(isWeb) {
        return isWeb ? 'Web' : 'App';
    }

    /**
     * Espera a que los servicios necesarios estén disponibles
     */
    async waitForServices() {
        return new Promise((resolve) => {
            const checkServices = () => {
                if (typeof PermissionService !== 'undefined') {
                    resolve();
                } else {
                    setTimeout(checkServices, 100);
                }
            };
            checkServices();
        });
    }
}

// El controlador se inicializa desde permissions-initializer.js
// No se inicializa automáticamente aquí para evitar duplicación
