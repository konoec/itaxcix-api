/**
 * Controlador para reportes de incidentes
 * Extiende BaseReportsController para manejar reportes de incidentes específicamente
 */
class IncidentReportsController extends BaseReportsController {
    constructor() {
        super();
        console.log('📊 Inicializando IncidentReportsController...');
        
        // Filtros específicos para incidentes
        this.filters = {
            userId: '',
            travelId: '',
            typeId: '',
            active: '',
            comment: '',
            sortBy: 'id',
            sortDirection: 'DESC'
        };
        
        // Servicio de datos
        this.service = window.incidentReportsService;
        
        // Elementos específicos del DOM
        this.initializeIncidentElements();
        
        // Event listeners específicos
        this.initializeIncidentEventListeners();
    }
    
    /**
     * Inicializa los elementos específicos para incidentes
     */
    initializeIncidentElements() {
        // Sección de filtros específica
        this.filtersSection = document.getElementById('incidents-filters');
        this.headers = document.getElementById('incidents-headers');
        
        // Filtros específicos
        this.filterUserId = document.getElementById('filter-user-id');
        this.filterTravelId = document.getElementById('filter-travel-id');
        this.filterTypeId = document.getElementById('filter-type-id');
        this.filterActive = document.getElementById('filter-active');
        this.filterComment = document.getElementById('filter-comment');
        this.sortBy = document.getElementById('sort-by');
        this.sortDirection = document.getElementById('sort-direction');
        this.perPageSelect = document.getElementById('per-page');
        
        // Botones de acción específicos
        this.applyFiltersBtn = document.getElementById('apply-filters-btn');
        this.clearFiltersBtn = document.getElementById('clear-filters-btn');
        this.refreshDataBtn = document.getElementById('refresh-data-btn');
        this.retryBtn = document.getElementById('retry-btn');
        
        console.log('✅ Elementos específicos de incidentes inicializados');
    }
    
    /**
     * Inicializa los event listeners específicos para incidentes
     */
    initializeIncidentEventListeners() {
        // Botones de acción
        if (this.applyFiltersBtn) {
            this.applyFiltersBtn.addEventListener('click', () => this.applyFilters());
        }
        
        if (this.clearFiltersBtn) {
            this.clearFiltersBtn.addEventListener('click', () => this.clearFilters());
        }
        
        if (this.refreshDataBtn) {
            this.refreshDataBtn.addEventListener('click', () => this.refreshData());
        }
        
        if (this.retryBtn) {
            this.retryBtn.addEventListener('click', () => this.loadReports());
        }
        
        // Enter en campos de texto
        [this.filterUserId, this.filterTravelId, this.filterTypeId, this.filterComment].forEach(input => {
            if (input) {
                input.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        this.applyFilters();
                    }
                });
            }
        });
        
        console.log('✅ Event listeners específicos de incidentes inicializados');
    }
    
    /**
     * Carga los reportes de incidentes
     */
    async loadReports() {
        if (!this.service) {
            console.error('❌ Servicio de incidentes no disponible');
            this.showErrorState('Servicio de incidentes no disponible');
            return;
        }
        
        try {
            this.showLoadingState();
            
            const requestFilters = {
                page: this.currentPage,
                perPage: this.perPage,
                ...this.filters
            };
            
            console.log('🔄 Cargando reportes de incidentes con filtros:', requestFilters);
            
            const response = await this.service.getIncidentReports(requestFilters);
            
            if (response && response.success && response.data) {
                console.log('✅ Respuesta completa de la API:', JSON.stringify(response, null, 2));
                
                this.currentData = response.data.data || [];
                console.log('📊 Datos de incidentes recibidos:', this.currentData);
                
                // Extraer información de paginación
                const pagination = response.data.pagination;
                if (pagination) {
                    this.totalResults = pagination.total_items || 0;
                    this.totalPages = pagination.total_pages || 1;
                    this.currentPage = pagination.current_page || 1;
                    this.perPage = pagination.per_page || 20;
                } else {
                    this.totalResults = this.currentData.length;
                    this.totalPages = 1;
                    this.currentPage = 1;
                    this.perPage = 20;
                }
                
                this.renderTable();
                this.updatePagination();
                this.showTableState();
                
                console.log('✅ Reportes de incidentes cargados exitosamente');
            } else {
                throw new Error('Respuesta inválida del servidor');
            }
            
        } catch (error) {
            console.error('❌ Error al cargar reportes de incidentes:', error);
            this.showErrorState(error.message || 'Error al cargar los reportes de incidentes');
        }
    }
    
    /**
     * Aplica los filtros actuales
     */
    applyFilters() {
        this.updateFiltersFromForm();
        this.currentPage = 1;
        this.loadReports();
    }
    
    /**
     * Limpia todos los filtros
     */
    clearFilters() {
        // Limpiar formulario
        if (this.filterUserId) this.filterUserId.value = '';
        if (this.filterTravelId) this.filterTravelId.value = '';
        if (this.filterTypeId) this.filterTypeId.value = '';
        if (this.filterActive) this.filterActive.value = '';
        if (this.filterComment) this.filterComment.value = '';
        if (this.sortBy) this.sortBy.value = 'id';
        if (this.sortDirection) this.sortDirection.value = 'DESC';
        if (this.perPageSelect) this.perPageSelect.value = '20';
        
        // Resetear filtros
        this.filters = {
            userId: '',
            travelId: '',
            typeId: '',
            active: '',
            comment: '',
            sortBy: 'id',
            sortDirection: 'DESC'
        };
        
        this.perPage = 20;
        this.currentPage = 1;
        
        this.loadReports();
    }
    
    /**
     * Refresca los datos
     */
    refreshData() {
        this.loadReports();
    }
    
    /**
     * Actualiza los filtros desde el formulario
     */
    updateFiltersFromForm() {
        this.filters = {
            userId: this.filterUserId?.value || '',
            travelId: this.filterTravelId?.value || '',
            typeId: this.filterTypeId?.value || '',
            active: this.filterActive?.value || '',
            comment: this.filterComment?.value || '',
            sortBy: this.sortBy?.value || 'id',
            sortDirection: this.sortDirection?.value || 'DESC'
        };
        
        this.perPage = parseInt(this.perPageSelect?.value || '20');
        console.log('📝 Filtros de incidentes actualizados:', this.filters);
    }
    
    /**
     * Renderiza la tabla con los datos actuales
     */
    renderTable() {
        if (!this.incidentsTableBody) return;
        
        console.log('🎨 Renderizando tabla de incidentes con datos:', this.currentData);
        
        this.incidentsTableBody.innerHTML = '';
        
        if (!this.currentData || this.currentData.length === 0) {
            console.log('📭 No hay datos para mostrar');
            this.showEmptyState();
            return;
        }
        
        console.log(`📊 Renderizando ${this.currentData.length} incidentes`);
        
        this.currentData.forEach((incident, index) => {
            console.log(`📋 Procesando incidente ${index + 1}:`, incident);
            const row = this.createTableRow(incident);
            this.incidentsTableBody.appendChild(row);
        });
        
        console.log('✅ Tabla de incidentes renderizada exitosamente');
    }
    
    /**
     * Crea una fila de la tabla para un incidente
     */
    createTableRow(incident) {
        const row = document.createElement('tr');
        
        // Formatear datos según la API
        const status = incident.active ? 'active' : 'inactive';
        const statusLabel = incident.active ? 'Activo' : 'Inactivo';
        const comment = incident.comment || '-';
        
        // Mostrar nombres cuando estén disponibles, sino mostrar IDs
        const userDisplay = incident.userName ? `${incident.userName} (ID: ${incident.userId})` : (incident.userId || '-');
        const typeDisplay = incident.typeName ? `${incident.typeName} (ID: ${incident.typeId})` : (incident.typeId || '-');
        
        row.innerHTML = `
            <td>${incident.id || '-'}</td>
            <td title="${userDisplay}">${userDisplay}</td>
            <td>${incident.travelId || '-'}</td>
            <td title="${typeDisplay}">${typeDisplay}</td>
            <td><span class="status-badge ${status}">${statusLabel}</span></td>
            <td title="${comment}">${comment.length > 50 ? comment.substring(0, 50) + '...' : comment}</td>
        `;
        
        return row;
    }
    
    /**
     * Verifica si hay filtros activos
     */
    hasActiveFilters() {
        return (
            (this.filters.userId && this.filters.userId.toString().trim() !== '') ||
            (this.filters.travelId && this.filters.travelId.toString().trim() !== '') ||
            (this.filters.typeId && this.filters.typeId.toString().trim() !== '') ||
            (this.filters.active && this.filters.active.trim() !== '') ||
            (this.filters.comment && this.filters.comment.trim() !== '') ||
            this.filters.sortBy !== 'id' ||
            this.filters.sortDirection !== 'DESC'
        );
    }
    
    /**
     * Muestra los filtros y headers específicos para incidentes
     */
    showFiltersAndHeaders() {
        if (this.filtersSection) {
            this.filtersSection.style.display = 'block';
        }
        
        if (this.headers) {
            this.headers.style.display = 'table-row';
        }
    }
    
    /**
     * Oculta los filtros y headers específicos para incidentes
     */
    hideFiltersAndHeaders() {
        if (this.filtersSection) {
            this.filtersSection.style.display = 'none';
        }
        
        if (this.headers) {
            this.headers.style.display = 'none';
        }
    }
}

// Hacer el controlador disponible globalmente
window.IncidentReportsController = IncidentReportsController;
