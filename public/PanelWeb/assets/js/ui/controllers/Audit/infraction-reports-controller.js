/**
 * Controlador para reportes de infracciones
 * Extiende BaseReportsController para manejar reportes de infracciones específicamente
 */
class InfractionReportsController extends BaseReportsController {
    constructor() {
        super();
        console.log('⚠️ Inicializando InfractionReportsController...');
        
        // Filtros específicos para infracciones
        this.filters = {
            userId: '',
            severityId: '',
            statusId: '',
            dateFrom: '',
            dateTo: '',
            description: '',
            sortBy: 'id',
            sortDirection: 'DESC'
        };
        
        // Servicio de datos
        this.service = window.infractionReportsService;
        
        // Elementos específicos del DOM
        this.initializeInfractionElements();
        
        // Event listeners específicos
        this.initializeInfractionEventListeners();
    }
    
    /**
     * Inicializa los elementos específicos para infracciones
     */
    initializeInfractionElements() {
        // Sección de filtros específica
        this.filtersSection = document.getElementById('infractions-filters');
        this.headers = document.getElementById('infractions-headers');
        
        // Filtros específicos
        this.filterInfractionUserId = document.getElementById('filter-infraction-user-id');
        this.filterSeverityId = document.getElementById('filter-severity-id');
        this.filterInfractionStatusId = document.getElementById('filter-infraction-status-id');
        this.filterDateFrom = document.getElementById('filter-date-from');
        this.filterDateTo = document.getElementById('filter-date-to');
        this.filterInfractionDescription = document.getElementById('filter-infraction-description');
        this.infractionSortBy = document.getElementById('infraction-sort-by');
        this.infractionSortDirection = document.getElementById('infraction-sort-direction');
        
        // Botones de acción específicos
        this.applyFiltersBtn = document.getElementById('apply-filters-btn-infractions');
        this.clearFiltersBtn = document.getElementById('clear-filters-btn-infractions');
        this.refreshDataBtn = document.getElementById('refresh-data-btn-infractions');
        
        console.log('✅ Elementos específicos de infracciones inicializados');
    }
    
    /**
     * Inicializa los event listeners específicos para infracciones
     */
    initializeInfractionEventListeners() {
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
        
        // Enter en campos de texto
        [this.filterInfractionUserId, this.filterSeverityId, this.filterInfractionStatusId, this.filterInfractionDescription].forEach(input => {
            if (input) {
                input.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        this.applyFilters();
                    }
                });
            }
        });
        
        console.log('✅ Event listeners específicos de infracciones inicializados');
    }
    
    /**
     * Carga reportes de infracciones
     */
    async loadReports() {
        if (!this.service) {
            console.error('❌ Servicio de infracciones no disponible');
            this.showErrorState('Servicio de infracciones no disponible');
            return;
        }

        try {
            this.showLoadingState();
            
            const requestFilters = {
                page: this.currentPage,
                perPage: this.perPage,
                ...this.filters
            };
            
            console.log('🔄 Cargando reportes de infracciones con filtros:', requestFilters);
            
            const response = await this.service.getInfractionReports(requestFilters);
            
            if (response && response.success && response.data) {
                this.currentData = response.data.data || [];
                
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
                
                console.log('✅ Reportes de infracciones cargados exitosamente');
            } else {
                throw new Error('Respuesta inválida del servidor');
            }
            
        } catch (error) {
            console.error('❌ Error al cargar reportes de infracciones:', error);
            this.showErrorState(error.message || 'Error al cargar los reportes de infracciones');
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
        if (this.filterInfractionUserId) this.filterInfractionUserId.value = '';
        if (this.filterSeverityId) this.filterSeverityId.value = '';
        if (this.filterInfractionStatusId) this.filterInfractionStatusId.value = '';
        if (this.filterDateFrom) this.filterDateFrom.value = '';
        if (this.filterDateTo) this.filterDateTo.value = '';
        if (this.filterInfractionDescription) this.filterInfractionDescription.value = '';
        if (this.infractionSortBy) this.infractionSortBy.value = 'id';
        if (this.infractionSortDirection) this.infractionSortDirection.value = 'DESC';
        if (this.pageSizeSelect) this.pageSizeSelect.value = '20';
        
        // Resetear filtros
        this.filters = {
            userId: '',
            severityId: '',
            statusId: '',
            dateFrom: '',
            dateTo: '',
            description: '',
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
            userId: this.filterInfractionUserId?.value || '',
            severityId: this.filterSeverityId?.value || '',
            statusId: this.filterInfractionStatusId?.value || '',
            dateFrom: this.filterDateFrom?.value || '',
            dateTo: this.filterDateTo?.value || '',
            description: this.filterInfractionDescription?.value || '',
            sortBy: this.infractionSortBy?.value || 'id',
            sortDirection: this.infractionSortDirection?.value || 'DESC'
        };
        
        this.perPage = parseInt(this.pageSizeSelect?.value || '20');
        console.log('📝 Filtros de infracciones actualizados:', this.filters);
    }
    
    /**
     * Renderiza la tabla con los datos actuales
     */
    renderTable() {
        if (!this.incidentsTableBody) {
            console.error('❌ Cuerpo de tabla no encontrado');
            return;
        }

        console.log('🎨 Renderizando tabla de infracciones con', this.currentData.length, 'registros');

        // Limpiar tabla
        this.incidentsTableBody.innerHTML = '';

        if (this.currentData.length === 0) {
            this.showEmptyState();
            return;
        }

        // Generar filas
        this.currentData.forEach(infraction => {
            const row = this.createTableRow(infraction);
            this.incidentsTableBody.appendChild(row);
        });

        console.log('✅ Tabla de infracciones renderizada');
    }

    /**
     * Crea una fila de tabla para una infracción
     */
    createTableRow(infraction) {
        const row = document.createElement('tr');
        row.className = 'data-row';

        // Formatear fecha
        const formattedDate = this.formatDate(infraction.date);

        row.innerHTML = `
            <td>${infraction.id || ''}</td>
            <td>${infraction.userId || ''}</td>
            <td><strong>${infraction.userName || 'Sin nombre'}</strong></td>
            <td>${infraction.severityId || ''}</td>
            <td><span class="severity-badge severity-${infraction.severityId || 'unknown'}">${infraction.severityName || 'Sin severidad'}</span></td>
            <td>${infraction.statusId || ''}</td>
            <td><span class="status-badge status-${infraction.statusId || 'unknown'}">${infraction.statusName || 'Sin estado'}</span></td>
            <td>${formattedDate}</td>
            <td title="${infraction.description || 'Sin descripción'}">${this.truncateText(infraction.description || 'Sin descripción', 50)}</td>
        `;

        return row;
    }
    
    /**
     * Verifica si hay filtros activos
     */
    hasActiveFilters() {
        return (
            (this.filters.userId && this.filters.userId.toString().trim() !== '') ||
            (this.filters.severityId && this.filters.severityId.toString().trim() !== '') ||
            (this.filters.statusId && this.filters.statusId.toString().trim() !== '') ||
            (this.filters.dateFrom && this.filters.dateFrom.trim() !== '') ||
            (this.filters.dateTo && this.filters.dateTo.trim() !== '') ||
            (this.filters.description && this.filters.description.trim() !== '') ||
            this.filters.sortBy !== 'id' ||
            this.filters.sortDirection !== 'DESC'
        );
    }
    
    /**
     * Muestra los filtros y headers específicos para infracciones
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
     * Oculta los filtros y headers específicos para infracciones
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
window.InfractionReportsController = InfractionReportsController;
