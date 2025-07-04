/**
 * Controlador para reportes de viajes
 * Extiende BaseReportsController para manejar reportes de viajes específicamente
 */
class TravelReportsController extends BaseReportsController {
    constructor() {
        super();
        console.log('🚕 Inicializando TravelReportsController...');
        
        // Filtros específicos para viajes
        this.filters = {
            citizenId: '',
            driverId: '',
            statusId: '',
            startDate: '',
            endDate: '',
            origin: '',
            destination: '',
            sortBy: 'creationDate',
            sortDirection: 'DESC'
        };
        
        // Servicio de datos
        this.service = window.travelReportsService;
        
        // Elementos específicos del DOM
        this.initializeTravelElements();
        
        // Event listeners específicos
        this.initializeTravelEventListeners();
    }
    
    /**
     * Inicializa los elementos específicos para viajes
     */
    initializeTravelElements() {
        // Sección de filtros específica
        this.filtersSection = document.getElementById('travels-filters');
        this.headers = document.getElementById('travels-headers');
        
        // Filtros específicos
        this.filterCitizenId = document.getElementById('filter-citizen-id');
        this.filterDriverId = document.getElementById('filter-driver-id');
        this.filterTravelStatusId = document.getElementById('filter-travel-status-id');
        this.filterStartDate = document.getElementById('filter-start-date');
        this.filterEndDate = document.getElementById('filter-end-date');
        this.filterOrigin = document.getElementById('filter-origin');
        this.filterDestination = document.getElementById('filter-destination');
        this.travelSortBy = document.getElementById('travel-sort-by');
        this.travelSortDirection = document.getElementById('travel-sort-direction');
        
        // Botones de acción específicos
        this.applyFiltersBtn = document.getElementById('apply-filters-btn-travels');
        this.clearFiltersBtn = document.getElementById('clear-filters-btn-travels');
        this.refreshDataBtn = document.getElementById('refresh-data-btn-travels');
        
        console.log('✅ Elementos específicos de viajes inicializados');
    }
    
    /**
     * Inicializa los event listeners específicos para viajes
     */
    initializeTravelEventListeners() {
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
        [this.filterCitizenId, this.filterDriverId, this.filterTravelStatusId, this.filterOrigin, this.filterDestination].forEach(input => {
            if (input) {
                input.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        this.applyFilters();
                    }
                });
            }
        });
        
        console.log('✅ Event listeners específicos de viajes inicializados');
    }
    
    /**
     * Carga reportes de viajes con filtros
     */
    async loadReports() {
        if (!this.service) {
            console.error('❌ Servicio de viajes no disponible');
            this.showErrorState('Servicio de viajes no disponible');
            return;
        }

        try {
            this.showLoadingState();
            
            const requestFilters = {
                page: this.currentPage,
                perPage: this.perPage,
                ...this.filters
            };
            
            console.log('🔄 Cargando reportes de viajes con filtros:', requestFilters);
            
            const response = await this.service.getTravelReports(requestFilters);
            
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
                
                console.log('✅ Reportes de viajes cargados exitosamente');
            } else {
                throw new Error('Respuesta inválida del servidor');
            }
            
        } catch (error) {
            console.error('❌ Error al cargar reportes de viajes:', error);
            this.showErrorState(error.message || 'Error al cargar los reportes de viajes');
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
        if (this.filterCitizenId) this.filterCitizenId.value = '';
        if (this.filterDriverId) this.filterDriverId.value = '';
        if (this.filterTravelStatusId) this.filterTravelStatusId.value = '';
        if (this.filterStartDate) this.filterStartDate.value = '';
        if (this.filterEndDate) this.filterEndDate.value = '';
        if (this.filterOrigin) this.filterOrigin.value = '';
        if (this.filterDestination) this.filterDestination.value = '';
        if (this.travelSortBy) this.travelSortBy.value = 'creationDate';
        if (this.travelSortDirection) this.travelSortDirection.value = 'DESC';
        if (this.pageSizeSelect) this.pageSizeSelect.value = '20';
        
        // Resetear filtros
        this.filters = {
            citizenId: '',
            driverId: '',
            statusId: '',
            startDate: '',
            endDate: '',
            origin: '',
            destination: '',
            sortBy: 'creationDate',
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
            citizenId: this.filterCitizenId?.value || '',
            driverId: this.filterDriverId?.value || '',
            statusId: this.filterTravelStatusId?.value || '',
            startDate: this.filterStartDate?.value || '',
            endDate: this.filterEndDate?.value || '',
            origin: this.filterOrigin?.value || '',
            destination: this.filterDestination?.value || '',
            sortBy: this.travelSortBy?.value || 'creationDate',
            sortDirection: this.travelSortDirection?.value || 'DESC'
        };
        
        this.perPage = parseInt(this.pageSizeSelect?.value || '20');
        console.log('📝 Filtros de viajes actualizados:', this.filters);
    }
    
    /**
     * Renderiza la tabla con los datos actuales
     */
    renderTable() {
        if (!this.incidentsTableBody) {
            console.error('❌ Cuerpo de tabla no encontrado');
            return;
        }

        console.log('🎨 Renderizando tabla de viajes con', this.currentData.length, 'registros');

        // Limpiar tabla
        this.incidentsTableBody.innerHTML = '';

        if (this.currentData.length === 0) {
            this.showEmptyState();
            return;
        }

        // Generar filas
        this.currentData.forEach(travel => {
            const row = this.createTableRow(travel);
            this.incidentsTableBody.appendChild(row);
        });

        console.log('✅ Tabla de viajes renderizada');
    }

    /**
     * Crea una fila de tabla para un viaje
     */
    createTableRow(travel) {
        const row = document.createElement('tr');
        row.className = 'data-row';

        // Formatear fechas
        const formattedStartDate = this.formatDate(travel.startDate);
        const formattedEndDate = this.formatDate(travel.endDate);
        const formattedCreationDate = this.formatDate(travel.creationDate);

        // Formatear estado con badge
        const statusClass = this.getStatusClass(travel.status);

        row.innerHTML = `
            <td>${travel.id || ''}</td>
            <td><strong>${travel.citizenName || 'Sin nombre'}</strong></td>
            <td><strong>${travel.driverName || 'Sin nombre'}</strong></td>
            <td title="${travel.origin || 'Sin origen'}">${this.truncateText(travel.origin || 'Sin origen', 30)}</td>
            <td title="${travel.destination || 'Sin destino'}">${this.truncateText(travel.destination || 'Sin destino', 30)}</td>
            <td>${formattedStartDate}</td>
            <td>${formattedEndDate}</td>
            <td>${formattedCreationDate}</td>
            <td><span class="status-badge ${statusClass}">${travel.status || 'Sin estado'}</span></td>
        `;

        return row;
    }

    /**
     * Obtiene la clase CSS para el estado del viaje
     */
    getStatusClass(status) {
        if (!status) return 'status-unknown';
        
        switch (status.toUpperCase()) {
            case 'FINALIZADO':
                return 'status-completed';
            case 'CANCELADO':
                return 'status-cancelled';
            case 'ACEPTADO':
                return 'status-accepted';
            case 'RECHAZADO':
                return 'status-rejected';
            case 'EN_PROGRESO':
            case 'EN PROGRESO':
                return 'status-in-progress';
            default:
                return 'status-unknown';
        }
    }
    
    /**
     * Verifica si hay filtros activos
     */
    hasActiveFilters() {
        return (
            (this.filters.citizenId && this.filters.citizenId.toString().trim() !== '') ||
            (this.filters.driverId && this.filters.driverId.toString().trim() !== '') ||
            (this.filters.statusId && this.filters.statusId.toString().trim() !== '') ||
            (this.filters.startDate && this.filters.startDate.trim() !== '') ||
            (this.filters.endDate && this.filters.endDate.trim() !== '') ||
            (this.filters.origin && this.filters.origin.trim() !== '') ||
            (this.filters.destination && this.filters.destination.trim() !== '') ||
            this.filters.sortBy !== 'creationDate' ||
            this.filters.sortDirection !== 'DESC'
        );
    }
    
    /**
     * Muestra los filtros y headers específicos para viajes
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
     * Oculta los filtros y headers específicos para viajes
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
window.TravelReportsController = TravelReportsController;
