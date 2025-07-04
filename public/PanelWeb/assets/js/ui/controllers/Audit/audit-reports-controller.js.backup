/**
 * Controlador para el módulo de Reportes de Incidentes
 * Maneja la generación, visualización y exportación de reportes de incidentes
 */
class AuditReportsController {
    constructor() {
        console.log('📊 Inicializando AuditReportsController...');
        
        // Configuración inicial
        this.currentData = [];
        this.isLoading = false;
        this.currentPage = 1;
        this.perPage = 20;
        this.totalPages = 1;
        this.totalResults = 0;
        this.currentReportType = ''; // 'incidents' o 'vehicles'
        
        // Filtros actuales para incidentes
        this.incidentFilters = {
            userId: '',
            travelId: '',
            typeId: '',
            active: '',
            comment: '',
            sortBy: 'id',
            sortDirection: 'DESC'
        };

        // Filtros actuales para vehículos
        this.vehicleFilters = {
            licensePlate: '',
            brandId: '',
            modelId: '',
            colorId: '',
            manufactureYearFrom: '',
            manufactureYearTo: '',
            seatCount: '',
            passengerCount: '',
            fuelTypeId: '',
            vehicleClassId: '',
            categoryId: '',
            active: '',
            companyId: '',
            districtId: '',
            statusId: '',
            procedureTypeId: '',
            modalityId: '',
            sortBy: 'licensePlate',
            sortDirection: 'ASC'
        };

        // Filtros actuales para infracciones
        this.infractionFilters = {
            userId: '',
            severityId: '',
            statusId: '',
            dateFrom: '',
            dateTo: '',
            description: '',
            sortBy: 'id',
            sortDirection: 'DESC'
        };

        // Filtros actuales para viajes
        this.travelFilters = {
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
        
        // Servicios de datos
        this.incidentService = window.incidentReportsService;
        this.vehicleService = window.vehicleReportsService;
        this.infractionService = window.infractionReportsService;
        this.travelService = window.travelReportsService;
        
        // Elementos del DOM
        this.initializeElements();
        
        // Event listeners
        this.initializeEventListeners();
        
        // Configurar estado inicial basado en el tipo de reporte seleccionado
        this.initializeReportTypeState();
    }
    
    /**
     * Inicializa las referencias a elementos del DOM
     */
    initializeElements() {
        // Selector de tipo de reporte
        this.reportTypeSelect = document.getElementById('report-type-select');
        
        // Secciones de filtros
        this.incidentsFiltersSection = document.getElementById('incidents-filters');
        this.vehiclesFiltersSection = document.getElementById('vehicles-filters');
        this.infractionsFiltersSection = document.getElementById('infractions-filters');
        this.travelsFiltersSection = document.getElementById('travels-filters');
        
        // Headers de la tabla
        this.incidentsHeaders = document.getElementById('incidents-headers');
        this.vehiclesHeaders = document.getElementById('vehicles-headers');
        this.infractionsHeaders = document.getElementById('infractions-headers');
        this.travelsHeaders = document.getElementById('travels-headers');

        // Filtros de incidentes
        this.filterUserId = document.getElementById('filter-user-id');
        this.filterTravelId = document.getElementById('filter-travel-id');
        this.filterTypeId = document.getElementById('filter-type-id');
        this.filterActive = document.getElementById('filter-active');
        this.filterComment = document.getElementById('filter-comment');
        this.sortBy = document.getElementById('sort-by');
        this.sortDirection = document.getElementById('sort-direction');
        this.perPageSelect = document.getElementById('per-page');

        // Filtros de vehículos
        this.filterLicensePlate = document.getElementById('filter-license-plate');
        this.filterBrandId = document.getElementById('filter-brand-id');
        this.filterModelId = document.getElementById('filter-model-id');
        this.filterColorId = document.getElementById('filter-color-id');
        this.filterManufactureYearFrom = document.getElementById('filter-manufacture-year-from');
        this.filterManufactureYearTo = document.getElementById('filter-manufacture-year-to');
        this.filterSeatCount = document.getElementById('filter-seat-count');
        this.filterPassengerCount = document.getElementById('filter-passenger-count');
        this.filterFuelTypeId = document.getElementById('filter-fuel-type-id');
        this.filterVehicleClassId = document.getElementById('filter-vehicle-class-id');
        this.filterCategoryId = document.getElementById('filter-category-id');
        this.filterVehicleActive = document.getElementById('filter-vehicle-active');
        this.filterCompanyId = document.getElementById('filter-company-id');
        this.filterDistrictId = document.getElementById('filter-district-id');
        this.filterStatusId = document.getElementById('filter-status-id');
        this.filterProcedureTypeId = document.getElementById('filter-procedure-type-id');
        this.filterModalityId = document.getElementById('filter-modality-id');
        this.vehicleSortBy = document.getElementById('vehicle-sort-by');
        this.vehicleSortDirection = document.getElementById('vehicle-sort-direction');
        // this.vehiclePerPageSelect = document.getElementById('vehicle-per-page'); // Removido - ahora se usa el de la paginación

        // Filtros de infracciones
        this.filterInfractionUserId = document.getElementById('filter-infraction-user-id');
        this.filterSeverityId = document.getElementById('filter-severity-id');
        this.filterInfractionStatusId = document.getElementById('filter-infraction-status-id');
        this.filterDateFrom = document.getElementById('filter-date-from');
        this.filterDateTo = document.getElementById('filter-date-to');
        this.filterInfractionDescription = document.getElementById('filter-infraction-description');
        this.infractionSortBy = document.getElementById('infraction-sort-by');
        this.infractionSortDirection = document.getElementById('infraction-sort-direction');

        // Filtros de viajes
        this.filterCitizenId = document.getElementById('filter-citizen-id');
        this.filterDriverId = document.getElementById('filter-driver-id');
        this.filterTravelStatusId = document.getElementById('filter-travel-status-id');
        this.filterStartDate = document.getElementById('filter-start-date');
        this.filterEndDate = document.getElementById('filter-end-date');
        this.filterOrigin = document.getElementById('filter-origin');
        this.filterDestination = document.getElementById('filter-destination');
        this.travelSortBy = document.getElementById('travel-sort-by');
        this.travelSortDirection = document.getElementById('travel-sort-direction');

        // Botones de acción - Incidentes
        this.applyFiltersBtn = document.getElementById('apply-filters-btn');
        this.clearFiltersBtn = document.getElementById('clear-filters-btn');
        this.refreshDataBtn = document.getElementById('refresh-data-btn');
        
        // Botones de acción - Vehículos
        this.applyFiltersBtnVehicles = document.getElementById('apply-filters-btn-vehicles');
        this.clearFiltersBtnVehicles = document.getElementById('clear-filters-btn-vehicles');
        this.refreshDataBtnVehicles = document.getElementById('refresh-data-btn-vehicles');
        
        // Botones de acción - Infracciones
        this.applyFiltersBtnInfractions = document.getElementById('apply-filters-btn-infractions');
        this.clearFiltersBtnInfractions = document.getElementById('clear-filters-btn-infractions');
        this.refreshDataBtnInfractions = document.getElementById('refresh-data-btn-infractions');
        
        // Botones de acción - Viajes
        this.applyFiltersBtnTravels = document.getElementById('apply-filters-btn-travels');
        this.clearFiltersBtnTravels = document.getElementById('clear-filters-btn-travels');
        this.refreshDataBtnTravels = document.getElementById('refresh-data-btn-travels');
        
        this.retryBtn = document.getElementById('retry-btn');
        
        // Estados de la tabla
        this.incidentsLoading = document.getElementById('incidents-loading');
        this.incidentsEmpty = document.getElementById('incidents-empty');
        this.incidentsError = document.getElementById('incidents-error');
        this.incidentsTableWrapper = document.getElementById('incidents-table-wrapper');
        this.errorMessage = document.getElementById('error-message');
        
        // Tabla y paginación
        this.incidentsTableBody = document.getElementById('incidents-table-body');
        this.incidentsPagination = document.getElementById('incidents-pagination');
        this.paginationInfoText = document.getElementById('pagination-info-text');
        this.pageSizeSelect = document.getElementById('page-size-select');
        
        // Botones de paginación (simplificados)
        this.prevPageBtn = document.getElementById('prev-page-btn');
        this.nextPageBtn = document.getElementById('next-page-btn');
        
        // Toggle filters
        this.toggleFiltersBtn = document.getElementById('toggle-filters');
        this.filtersContent = document.getElementById('filters-content');
        
        // Botones del estado vacío
        this.clearFiltersEmptyBtn = document.getElementById('clear-filters-empty-btn');
        this.refreshEmptyBtn = document.getElementById('refresh-empty-btn');
        
        // Mensaje de "Sin filtro"
        this.noReportSelected = document.getElementById('no-report-selected');
        
        console.log('✅ Elementos del DOM inicializados');
    }
    
    /**
     * Inicializa los event listeners
     */
    initializeEventListeners() {
        // Selector de tipo de reporte
        if (this.reportTypeSelect) {
            this.reportTypeSelect.addEventListener('change', () => this.handleReportTypeChange());
        }
        
        // Filtros - Incidentes
        if (this.applyFiltersBtn) {
            this.applyFiltersBtn.addEventListener('click', () => this.applyFilters());
        }
        
        if (this.clearFiltersBtn) {
            this.clearFiltersBtn.addEventListener('click', () => this.clearFilters());
        }
        
        if (this.refreshDataBtn) {
            this.refreshDataBtn.addEventListener('click', () => this.refreshData());
        }
        
        // Filtros - Vehículos
        if (this.applyFiltersBtnVehicles) {
            this.applyFiltersBtnVehicles.addEventListener('click', () => this.applyFilters());
        }
        
        if (this.clearFiltersBtnVehicles) {
            this.clearFiltersBtnVehicles.addEventListener('click', () => this.clearFilters());
        }
        
        if (this.refreshDataBtnVehicles) {
            this.refreshDataBtnVehicles.addEventListener('click', () => this.refreshData());
        }
        
        // Filtros - Infracciones
        if (this.applyFiltersBtnInfractions) {
            this.applyFiltersBtnInfractions.addEventListener('click', () => this.applyFilters());
        }
        
        if (this.clearFiltersBtnInfractions) {
            this.clearFiltersBtnInfractions.addEventListener('click', () => this.clearFilters());
        }
        
        if (this.refreshDataBtnInfractions) {
            this.refreshDataBtnInfractions.addEventListener('click', () => this.refreshData());
        }
        
        // Filtros - Viajes
        if (this.applyFiltersBtnTravels) {
            this.applyFiltersBtnTravels.addEventListener('click', () => this.applyFilters());
        }
        
        if (this.clearFiltersBtnTravels) {
            this.clearFiltersBtnTravels.addEventListener('click', () => this.clearFilters());
        }
        
        if (this.refreshDataBtnTravels) {
            this.refreshDataBtnTravels.addEventListener('click', () => this.refreshData());
        }
        
        if (this.retryBtn) {
            this.retryBtn.addEventListener('click', () => this.loadIncidentReports());
        }
        
        // Cambio de elementos por página
        // Selector de tamaño de página
        if (this.pageSizeSelect) {
            this.pageSizeSelect.addEventListener('change', () => this.changePageSize());
        }
        
        // Paginación (simplificada)
        if (this.prevPageBtn) {
            this.prevPageBtn.addEventListener('click', () => this.goToPage(this.currentPage - 1));
        }
        
        if (this.nextPageBtn) {
            this.nextPageBtn.addEventListener('click', () => this.goToPage(this.currentPage + 1));
        }
        
        // Toggle de filtros
        if (this.toggleFiltersBtn) {
            this.toggleFiltersBtn.addEventListener('click', () => this.toggleFilters());
        }
        
        // Aplicar filtros al presionar Enter en campos de texto
        [this.filterUserId, this.filterTravelId, this.filterTypeId, this.filterComment].forEach(input => {
            if (input) {
                input.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        this.applyFilters();
                    }
                });
            }
        });

        // Aplicar filtros al presionar Enter en campos de infracciones
        [this.filterInfractionUserId, this.filterSeverityId, this.filterInfractionStatusId, this.filterInfractionDescription].forEach(input => {
            if (input) {
                input.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        this.applyFilters();
                    }
                });
            }
        });

        // Aplicar filtros al presionar Enter en campos de viajes
        [this.filterCitizenId, this.filterDriverId, this.filterTravelStatusId, this.filterOrigin, this.filterDestination].forEach(input => {
            if (input) {
                input.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        this.applyFilters();
                    }
                });
            }
        });
        
        // Botones del estado vacío
        if (this.clearFiltersEmptyBtn) {
            this.clearFiltersEmptyBtn.addEventListener('click', () => this.clearFilters());
        }
        
        if (this.refreshEmptyBtn) {
            this.refreshEmptyBtn.addEventListener('click', () => this.refreshData());
        }
        
        console.log('✅ Event listeners inicializados');
    }
    
    /**
     * Carga los reportes de incidentes
     */
    async loadIncidentReports() {
        if (!this.incidentService) {
            console.error('❌ Servicio de incidentes no disponible');
            this.showErrorState('Servicio de incidentes no disponible');
            return;
        }
        
        try {
            this.showLoadingState();
            
            const requestFilters = {
                page: this.currentPage,
                perPage: this.perPage,
                ...this.incidentFilters
            };
            
            console.log('🔄 Cargando reportes de incidentes con filtros:', requestFilters);
            
            const response = await this.incidentService.getIncidentReports(requestFilters);
            
            if (response && response.success && response.data) {
                console.log('✅ Respuesta completa de la API:', JSON.stringify(response, null, 2));
                
                this.currentData = response.data.data || [];
                console.log('📊 Datos de incidentes recibidos:', this.currentData);
                
                // Verificar qué campos están disponibles en cada incidente
                if (this.currentData.length > 0) {
                    console.log('📋 Campos disponibles en el primer incidente:', Object.keys(this.currentData[0]));
                    console.log('📋 Primer incidente completo:', this.currentData[0]);
                }
                
                // Extraer información de paginación
                const pagination = response.data.pagination;
                if (pagination) {
                    this.totalResults = pagination.total_items || 0;
                    this.totalPages = pagination.total_pages || 1;
                    this.currentPage = pagination.current_page || 1;
                    this.perPage = pagination.per_page || 20;
                } else {
                    // Si no hay información de paginación, usar los datos como página única
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
     * Carga los reportes de vehículos
     */
    async loadVehicleReports() {
        if (!this.vehicleService) {
            console.error('❌ Servicio de vehículos no disponible');
            this.showErrorState('Servicio de vehículos no disponible');
            return;
        }

        try {
            this.showLoadingState();
            
            const requestFilters = {
                page: this.currentPage,
                perPage: this.perPage,
                ...this.vehicleFilters
            };
            
            console.log('🔄 Cargando reportes de vehículos con filtros:', requestFilters);
            
            const response = await this.vehicleService.getVehicleReports(requestFilters);
            
            if (response && response.success && response.data) {
                this.currentData = response.data.vehicles || [];
                
                // Extraer información de paginación
                const pagination = response.data.pagination;
                if (pagination) {
                    this.totalResults = pagination.total || 0;
                    this.totalPages = pagination.totalPages || 1;
                    this.currentPage = pagination.currentPage || 1;
                    this.perPage = pagination.perPage || 20;
                } else {
                    // Si no hay información de paginación, usar los datos como página única
                    this.totalResults = this.currentData.length;
                    this.totalPages = 1;
                    this.currentPage = 1;
                    this.perPage = 20;
                }
                
                this.renderVehicleTable();
                this.updatePagination();
                this.showTableState();
                
                console.log('✅ Reportes de vehículos cargados exitosamente');
            } else {
                throw new Error('Respuesta inválida del servidor');
            }
            
        } catch (error) {
            console.error('❌ Error al cargar reportes de vehículos:', error);
            this.showErrorState(error.message || 'Error al cargar los reportes de vehículos');
        }
    }

    /**
     * Carga reportes de infracciones
     */
    async loadInfractionReports() {
        if (!this.infractionService) {
            console.error('❌ Servicio de infracciones no disponible');
            this.showErrorState('Servicio de infracciones no disponible');
            return;
        }

        try {
            this.showLoadingState();
            
            const requestFilters = {
                page: this.currentPage,
                perPage: this.perPage,
                ...this.infractionFilters
            };
            
            console.log('🔄 Cargando reportes de infracciones con filtros:', requestFilters);
            
            const response = await this.infractionService.getInfractionReports(requestFilters);
            
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
                    // Si no hay información de paginación, usar los datos como página única
                    this.totalResults = this.currentData.length;
                    this.totalPages = 1;
                    this.currentPage = 1;
                    this.perPage = 20;
                }
                
                this.renderInfractionTable();
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
     * Carga reportes de viajes con filtros
     */
    async loadTravelReports() {
        if (!this.travelService) {
            console.error('❌ Servicio de viajes no disponible');
            this.showErrorState('Servicio de viajes no disponible');
            return;
        }

        try {
            this.showLoadingState();
            
            const requestFilters = {
                page: this.currentPage,
                perPage: this.perPage,
                ...this.travelFilters
            };
            
            console.log('🔄 Cargando reportes de viajes con filtros:', requestFilters);
            
            const response = await this.travelService.getTravelReports(requestFilters);
            
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
                    // Si no hay información de paginación, usar los datos como página única
                    this.totalResults = this.currentData.length;
                    this.totalPages = 1;
                    this.currentPage = 1;
                    this.perPage = 20;
                }
                
                this.renderTravelTable();
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
        if (this.currentReportType === 'incidents') {
            this.updateIncidentFiltersFromForm();
            this.currentPage = 1; // Reset a la primera página
            this.loadIncidentReports();
        } else if (this.currentReportType === 'vehicles') {
            this.updateVehicleFiltersFromForm();
            this.currentPage = 1; // Reset a la primera página
            this.loadVehicleReports();
        } else if (this.currentReportType === 'infractions') {
            this.updateInfractionFiltersFromForm();
            this.currentPage = 1; // Reset a la primera página
            this.loadInfractionReports();
        } else if (this.currentReportType === 'travels') {
            this.updateTravelFiltersFromForm();
            this.currentPage = 1; // Reset a la primera página
            this.loadTravelReports();
        }
    }
    
    /**
     * Limpia todos los filtros
     */
    clearFilters() {
        if (this.currentReportType === 'incidents') {
            // Limpiar formulario de incidentes
            if (this.filterUserId) this.filterUserId.value = '';
            if (this.filterTravelId) this.filterTravelId.value = '';
            if (this.filterTypeId) this.filterTypeId.value = '';
            if (this.filterActive) this.filterActive.value = '';
            if (this.filterComment) this.filterComment.value = '';
            if (this.sortBy) this.sortBy.value = 'id';
            if (this.sortDirection) this.sortDirection.value = 'DESC';
            if (this.perPageSelect) this.perPageSelect.value = '20';
            
            // Resetear filtros de incidentes
            this.incidentFilters = {
                userId: '',
                travelId: '',
                typeId: '',
                active: '',
                comment: '',
                sortBy: 'id',
                sortDirection: 'DESC'
            };
        } else if (this.currentReportType === 'vehicles') {
            // Limpiar formulario de vehículos
            if (this.filterLicensePlate) this.filterLicensePlate.value = '';
            if (this.filterBrandId) this.filterBrandId.value = '';
            if (this.filterModelId) this.filterModelId.value = '';
            if (this.filterColorId) this.filterColorId.value = '';
            if (this.filterManufactureYearFrom) this.filterManufactureYearFrom.value = '';
            if (this.filterManufactureYearTo) this.filterManufactureYearTo.value = '';
            if (this.filterSeatCount) this.filterSeatCount.value = '';
            if (this.filterPassengerCount) this.filterPassengerCount.value = '';
            if (this.filterFuelTypeId) this.filterFuelTypeId.value = '';
            if (this.filterVehicleClassId) this.filterVehicleClassId.value = '';
            if (this.filterCategoryId) this.filterCategoryId.value = '';
            if (this.filterVehicleActive) this.filterVehicleActive.value = '';
            if (this.filterCompanyId) this.filterCompanyId.value = '';
            if (this.filterDistrictId) this.filterDistrictId.value = '';
            if (this.filterStatusId) this.filterStatusId.value = '';
            if (this.filterProcedureTypeId) this.filterProcedureTypeId.value = '';
            if (this.filterModalityId) this.filterModalityId.value = '';
            if (this.vehicleSortBy) this.vehicleSortBy.value = 'licensePlate';
            if (this.vehicleSortDirection) this.vehicleSortDirection.value = 'ASC';
            // Usar el selector de la paginación en lugar del de filtros
            if (this.pageSizeSelect) this.pageSizeSelect.value = '20';
            
            // Resetear filtros de vehículos
            this.vehicleFilters = {
                licensePlate: '',
                brandId: '',
                modelId: '',
                colorId: '',
                manufactureYearFrom: '',
                manufactureYearTo: '',
                seatCount: '',
                passengerCount: '',
                fuelTypeId: '',
                vehicleClassId: '',
                categoryId: '',
                active: '',
                companyId: '',
                districtId: '',
                statusId: '',
                procedureTypeId: '',
                modalityId: '',
                sortBy: 'licensePlate',
                sortDirection: 'ASC'
            };
        } else if (this.currentReportType === 'infractions') {
            // Limpiar formulario de infracciones
            if (this.filterInfractionUserId) this.filterInfractionUserId.value = '';
            if (this.filterSeverityId) this.filterSeverityId.value = '';
            if (this.filterInfractionStatusId) this.filterInfractionStatusId.value = '';
            if (this.filterDateFrom) this.filterDateFrom.value = '';
            if (this.filterDateTo) this.filterDateTo.value = '';
            if (this.filterInfractionDescription) this.filterInfractionDescription.value = '';
            if (this.infractionSortBy) this.infractionSortBy.value = 'id';
            if (this.infractionSortDirection) this.infractionSortDirection.value = 'DESC';
            // Usar el selector de la paginación en lugar del de filtros
            if (this.pageSizeSelect) this.pageSizeSelect.value = '20';
            
            // Resetear filtros de infracciones
            this.infractionFilters = {
                userId: '',
                severityId: '',
                statusId: '',
                dateFrom: '',
                dateTo: '',
                description: '',
                sortBy: 'id',
                sortDirection: 'DESC'
            };
        } else if (this.currentReportType === 'travels') {
            // Limpiar formulario de viajes
            if (this.filterCitizenId) this.filterCitizenId.value = '';
            if (this.filterDriverId) this.filterDriverId.value = '';
            if (this.filterTravelStatusId) this.filterTravelStatusId.value = '';
            if (this.filterStartDate) this.filterStartDate.value = '';
            if (this.filterEndDate) this.filterEndDate.value = '';
            if (this.filterOrigin) this.filterOrigin.value = '';
            if (this.filterDestination) this.filterDestination.value = '';
            if (this.travelSortBy) this.travelSortBy.value = 'creationDate';
            if (this.travelSortDirection) this.travelSortDirection.value = 'DESC';
            // Usar el selector de la paginación en lugar del de filtros
            if (this.pageSizeSelect) this.pageSizeSelect.value = '20';
            
            // Resetear filtros de viajes
            this.travelFilters = {
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
        }
        
        this.perPage = 20;
        this.currentPage = 1;
        
        // Recargar datos según el tipo de reporte actual
        if (this.currentReportType === 'incidents') {
            this.loadIncidentReports();
        } else if (this.currentReportType === 'vehicles') {
            this.loadVehicleReports();
        } else if (this.currentReportType === 'infractions') {
            this.loadInfractionReports();
        } else if (this.currentReportType === 'travels') {
            this.loadTravelReports();
        }
    }
    
    /**
     * Actualiza los filtros desde el formulario
     */
    updateFiltersFromForm() {
        if (this.currentReportType === 'incidents') {
            this.incidentFilters = {
                userId: this.filterUserId?.value || '',
                travelId: this.filterTravelId?.value || '',
                typeId: this.filterTypeId?.value || '',
                active: this.filterActive?.value || '',
                comment: this.filterComment?.value || '',
                sortBy: this.sortBy?.value || 'id',
                sortDirection: this.sortDirection?.value || 'DESC'
            };
        } else if (this.currentReportType === 'vehicles') {
            this.vehicleFilters = {
                licensePlate: this.filterLicensePlate?.value || '',
                brandId: this.filterBrandId?.value || '',
                modelId: this.filterModelId?.value || '',
                colorId: this.filterColorId?.value || '',
                manufactureYearFrom: this.filterManufactureYearFrom?.value || '',
                manufactureYearTo: this.filterManufactureYearTo?.value || '',
                seatCount: this.filterSeatCount?.value || '',
                passengerCount: this.filterPassengerCount?.value || '',
                fuelTypeId: this.filterFuelTypeId?.value || '',
                vehicleClassId: this.filterVehicleClassId?.value || '',
                categoryId: this.filterCategoryId?.value || '',
                active: this.filterVehicleActive?.value || '',
                companyId: this.filterCompanyId?.value || '',
                districtId: this.filterDistrictId?.value || '',
                statusId: this.filterStatusId?.value || '',
                procedureTypeId: this.filterProcedureTypeId?.value || '',
                modalityId: this.filterModalityId?.value || '',
                sortBy: this.vehicleSortBy?.value || 'licensePlate',
                sortDirection: this.vehicleSortDirection?.value || 'ASC'
            };
        } else if (this.currentReportType === 'infractions') {
            this.infractionFilters = {
                userId: this.filterInfractionUserId?.value || '',
                severityId: this.filterSeverityId?.value || '',
                statusId: this.filterInfractionStatusId?.value || '',
                dateFrom: this.filterDateFrom?.value || '',
                dateTo: this.filterDateTo?.value || '',
                description: this.filterInfractionDescription?.value || '',
                sortBy: this.infractionSortBy?.value || 'id',
                sortDirection: this.infractionSortDirection?.value || 'DESC'
            };
        } else if (this.currentReportType === 'travels') {
            this.travelFilters = {
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
        }
        
        // Actualizar perPage desde el selector correspondiente
        if (this.currentReportType === 'incidents' && this.perPageSelect?.value) {
            this.perPage = parseInt(this.perPageSelect.value);
        } else if ((this.currentReportType === 'vehicles' || this.currentReportType === 'infractions') && this.pageSizeSelect?.value) {
            this.perPage = parseInt(this.pageSizeSelect.value);
        }
    }
    
    /**
     * Actualiza los filtros de incidentes desde el formulario
     */
    updateIncidentFiltersFromForm() {
        this.incidentFilters = {
            userId: this.filterUserId?.value || '',
            travelId: this.filterTravelId?.value || '',
            typeId: this.filterTypeId?.value || '',
            active: this.filterActive?.value || '',
            comment: this.filterComment?.value || '',
            sortBy: this.sortBy?.value || 'id',
            sortDirection: this.sortDirection?.value || 'DESC'
        };
        
        this.perPage = parseInt(this.perPageSelect?.value || '20');
        console.log('📝 Filtros de incidentes actualizados:', this.incidentFilters);
    }

    /**
     * Actualiza los filtros de vehículos desde el formulario
     */
    updateVehicleFiltersFromForm() {
        this.vehicleFilters = {
            licensePlate: this.filterLicensePlate?.value || '',
            brandId: this.filterBrandId?.value || '',
            modelId: this.filterModelId?.value || '',
            colorId: this.filterColorId?.value || '',
            manufactureYearFrom: this.filterManufactureYearFrom?.value || '',
            manufactureYearTo: this.filterManufactureYearTo?.value || '',
            seatCount: this.filterSeatCount?.value || '',
            passengerCount: this.filterPassengerCount?.value || '',
            fuelTypeId: this.filterFuelTypeId?.value || '',
            vehicleClassId: this.filterVehicleClassId?.value || '',
            categoryId: this.filterCategoryId?.value || '',
            active: this.filterVehicleActive?.value || '',
            companyId: this.filterCompanyId?.value || '',
            districtId: this.filterDistrictId?.value || '',
            statusId: this.filterStatusId?.value || '',
            procedureTypeId: this.filterProcedureTypeId?.value || '',
            modalityId: this.filterModalityId?.value || '',
            sortBy: this.vehicleSortBy?.value || 'licensePlate',
            sortDirection: this.vehicleSortDirection?.value || 'ASC'
        };
        
        this.perPage = parseInt(this.pageSizeSelect?.value || '20');
        console.log('📝 Filtros de vehículos actualizados:', this.vehicleFilters);
    }

    /**
     * Actualiza los filtros de infracciones desde el formulario
     */
    updateInfractionFiltersFromForm() {
        this.infractionFilters = {
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
        console.log('📝 Filtros de infracciones actualizados:', this.infractionFilters);
    }

    /**
     * Actualiza los filtros de viajes desde el formulario
     */
    updateTravelFiltersFromForm() {
        this.travelFilters = {
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
        console.log('📝 Filtros de viajes actualizados:', this.travelFilters);
    }

    /**
     * Refresca los datos
     */
    refreshData() {
        if (this.currentReportType === 'incidents') {
            this.loadIncidentReports();
        } else if (this.currentReportType === 'vehicles') {
            this.loadVehicleReports();
        } else if (this.currentReportType === 'infractions') {
            this.loadInfractionReports();
        } else if (this.currentReportType === 'travels') {
            this.loadTravelReports();
        }
    }
    
    /**
     * Navega a una página específica
     */
    goToPage(page) {
        if (page < 1 || page > this.totalPages || page === this.currentPage) {
            return;
        }
        
        this.currentPage = page;
        
        // Cargar datos según el tipo de reporte actual
        if (this.currentReportType === 'incidents') {
            this.loadIncidentReports();
        } else if (this.currentReportType === 'vehicles') {
            this.loadVehicleReports();
        } else if (this.currentReportType === 'infractions') {
            this.loadInfractionReports();
        }
    }
    
    /**
     * Cambia el tamaño de página
     */
    changePageSize() {
        // Obtener valor del selector
        let newPerPage = 20;
        if (this.pageSizeSelect?.value) {
            newPerPage = parseInt(this.pageSizeSelect.value);
        }
        
        this.perPage = newPerPage;
        this.currentPage = 1;
        
        // Cargar datos según el tipo de reporte actual
        if (this.currentReportType === 'incidents') {
            this.loadIncidentReports();
        } else if (this.currentReportType === 'vehicles') {
            this.loadVehicleReports();
        } else if (this.currentReportType === 'infractions') {
            this.loadInfractionReports();
        }
    }
    
    /**
     * Toggle de visibilidad de filtros
     */
    toggleFilters() {
        if (this.filtersContent) {
            const isHidden = this.filtersContent.style.display === 'none';
            this.filtersContent.style.display = isHidden ? 'block' : 'none';
            
            if (this.toggleFiltersBtn) {
                const icon = this.toggleFiltersBtn.querySelector('i');
                if (icon) {
                    icon.className = isHidden ? 'fas fa-chevron-up' : 'fas fa-chevron-down';
                }
            }
        }
    }
    
    /**
     * Renderiza la tabla con los datos actuales
     */
    renderTable() {
        if (!this.incidentsTableBody) return;
        
        console.log('🎨 Renderizando tabla con datos:', this.currentData);
        
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
        
        console.log('✅ Tabla renderizada exitosamente');
    }
    
    /**
     * Crea una fila de la tabla
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
     * Renderiza la tabla de vehículos
     */
    renderVehicleTable() {
        if (!this.incidentsTableBody) {
            console.error('❌ Cuerpo de tabla no encontrado');
            return;
        }

        console.log('🎨 Renderizando tabla de vehículos con', this.currentData.length, 'registros');

        // Limpiar tabla
        this.incidentsTableBody.innerHTML = '';

        if (this.currentData.length === 0) {
            this.showEmptyState();
            return;
        }

        // Generar filas
        this.currentData.forEach(vehicle => {
            const row = this.createVehicleTableRow(vehicle);
            this.incidentsTableBody.appendChild(row);
        });

        console.log('✅ Tabla de vehículos renderizada');
    }

    /**
     * Crea una fila de tabla para un vehículo
     * @param {Object} vehicle - Datos del vehículo
     * @returns {HTMLTableRowElement} Fila de tabla
     */
    createVehicleTableRow(vehicle) {
        const row = document.createElement('tr');
        row.className = 'data-row';

        const statusBadge = vehicle.active 
            ? '<span class="status-badge active">Activo</span>'
            : '<span class="status-badge inactive">Inactivo</span>';

        row.innerHTML = `
            <td>${vehicle.id || ''}</td>
            <td><strong>${vehicle.licensePlate || ''}</strong></td>
            <td>${vehicle.brandName || ''}</td>
            <td>${vehicle.modelName || ''}</td>
            <td>${vehicle.colorName || ''}</td>
            <td>${vehicle.manufactureYear || ''}</td>
            <td>${vehicle.seatCount || ''}</td>
            <td>${vehicle.passengerCount || ''}</td>
            <td>${vehicle.fuelTypeName || ''}</td>
            <td>${vehicle.vehicleClassName || ''}</td>
            <td>${vehicle.categoryName || ''}</td>
            <td>${statusBadge}</td>
            <td>${vehicle.companyName || 'N/A'}</td>
            <td>${vehicle.districtName || 'N/A'}</td>
            <td>${vehicle.statusName || 'N/A'}</td>
            <td>${vehicle.procedureTypeName || 'N/A'}</td>
            <td>${vehicle.modalityName || 'N/A'}</td>
        `;

        return row;
    }

    /**
     * Renderiza la tabla de infracciones
     */
    renderInfractionTable() {
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
            const row = this.createInfractionTableRow(infraction);
            this.incidentsTableBody.appendChild(row);
        });

        console.log('✅ Tabla de infracciones renderizada');
    }

    /**
     * Crea una fila de tabla para una infracción
     * @param {Object} infraction - Datos de la infracción
     * @returns {HTMLTableRowElement} Fila de tabla
     */
    createInfractionTableRow(infraction) {
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
     * Renderiza la tabla de viajes
     */
    renderTravelTable() {
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
            const row = this.createTravelTableRow(travel);
            this.incidentsTableBody.appendChild(row);
        });

        console.log('✅ Tabla de viajes renderizada');
    }

    /**
     * Crea una fila de tabla para un viaje
     * @param {Object} travel - Datos del viaje
     * @returns {HTMLTableRowElement} Fila de tabla
     */
    createTravelTableRow(travel) {
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
     * @param {string} status - Estado del viaje
     * @returns {string} Clase CSS
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
     * Formatea una fecha para mostrar
     * @param {string} dateString - Fecha en formato string
     * @returns {string} Fecha formateada
     */
    formatDate(dateString) {
        if (!dateString) return 'N/A';
        
        try {
            const date = new Date(dateString);
            return date.toLocaleString('es-ES', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (error) {
            return dateString;
        }
    }

    /**
     * Trunca un texto a una longitud específica
     * @param {string} text - Texto a truncar
     * @param {number} maxLength - Longitud máxima
     * @returns {string} Texto truncado
     */
    truncateText(text, maxLength) {
        if (!text || text.length <= maxLength) return text;
        return text.substring(0, maxLength) + '...';
    }

    /**
     * Actualiza la paginación
     */
    updatePagination() {
        if (!this.incidentsPagination) {
            return;
        }
        
        // Actualizar información
        if (this.paginationInfoText) {
            const start = (this.currentPage - 1) * this.perPage + 1;
            const end = Math.min(start + this.perPage - 1, this.totalResults);
            this.paginationInfoText.textContent = `Mostrando ${start}-${end} de ${this.totalResults} resultados`;
        }
        
        // Actualizar botones de navegación (simplificados)
        if (this.prevPageBtn) {
            this.prevPageBtn.disabled = this.currentPage === 1;
        }
        if (this.nextPageBtn) {
            this.nextPageBtn.disabled = this.currentPage === this.totalPages;
        }
        
        // Mostrar paginación siempre que haya datos
        if (this.incidentsPagination) {
            // Siempre mostrar la paginación
            this.incidentsPagination.style.display = 'flex';
        }
    }
    
    /**
     * Muestra el estado de carga
     */
    showLoadingState() {
        this.hideAllStates();
        if (this.incidentsLoading) {
            this.incidentsLoading.style.display = 'block';
        }
        this.isLoading = true;
    }
    
    /**
     * Muestra el estado vacío
     */
    /**
     * Muestra el estado vacío con mensaje informativo
     */
    showEmptyState(customMessage = null) {
        this.hideAllStates();
        
        if (this.incidentsEmpty) {
            this.incidentsEmpty.style.display = 'block';
            
            // Personalizar mensaje según el contexto
            const emptyTitle = this.incidentsEmpty.querySelector('h3');
            const emptyDescription = this.incidentsEmpty.querySelector('p');
            
            if (customMessage) {
                if (emptyTitle) emptyTitle.textContent = customMessage.title || 'No hay incidentes registrados';
                if (emptyDescription) emptyDescription.textContent = customMessage.description || 'No se encontraron reportes con los criterios especificados.';
            } else {
                // Verificar si hay filtros activos
                const hasActiveFilters = this.hasActiveFilters();
                
                if (hasActiveFilters) {
                    if (emptyTitle) emptyTitle.textContent = 'No se encontraron incidentes con los filtros aplicados';
                    if (emptyDescription) emptyDescription.textContent = 'Intenta ajustar o limpiar los filtros para ver más resultados.';
                } else {
                    if (emptyTitle) emptyTitle.textContent = 'No hay incidentes registrados';
                    if (emptyDescription) emptyDescription.textContent = 'Actualmente no hay reportes de incidentes disponibles en el sistema.';
                }
            }
        }
        
        console.log('📋 Estado vacío mostrado');
    }
    
    /**
     * Muestra el estado de error
     */
    showErrorState(message) {
        this.hideAllStates();
        if (this.incidentsError) {
            this.incidentsError.style.display = 'block';
        }
        if (this.errorMessage) {
            this.errorMessage.textContent = message;
        }
        this.isLoading = false;
    }
    
    /**
     * Muestra el estado de la tabla
     */
    showTableState() {
        this.hideAllStates();
        if (this.incidentsTableWrapper) {
            this.incidentsTableWrapper.style.display = 'block';
        }
        // La paginación se mostrará a través de updatePagination()
        this.isLoading = false;
    }
    
    /**
     * Oculta todos los estados
     */
    hideAllStates() {
        [this.incidentsLoading, this.incidentsEmpty, this.incidentsError, this.incidentsTableWrapper, this.incidentsPagination].forEach(element => {
            if (element) {
                element.style.display = 'none';
            }
        });
    }
    
    /**
     * Establece el estado de loading de un botón
     */
    setButtonLoading(button, loading) {
        if (!button) return;
        
        if (loading) {
            button.classList.add('loading');
            button.disabled = true;
        } else {
            button.classList.remove('loading');
            button.disabled = false;
        }
    }
    
    /**
     * Muestra un toast notification
     */
    showToast(message, type = 'info') {
        // Utilizar el sistema global de toasts si está disponible
        if (window.GlobalToast) {
            window.GlobalToast.show(message, type);
        } else {
            console.log(`Toast ${type}: ${message}`);
        }
    }
    
    /**
     * Maneja el cambio del selector de tipo de reporte
     */
    handleReportTypeChange() {
        const selectedType = this.reportTypeSelect.value;
        console.log('📊 Tipo de reporte seleccionado:', selectedType);
        
        this.currentReportType = selectedType;
        
        if (selectedType === '' || selectedType === 'none') {
            // Sin filtro - ocultar filtros y tabla
            this.hideFiltersAndTable();
        } else if (selectedType === 'incidents') {
            // Incidentes - mostrar filtros y tabla, cargar datos
            this.showFiltersAndTable('incidents');
            this.loadIncidentReports();
        } else if (selectedType === 'vehicles') {
            // Vehículos - mostrar filtros y tabla, cargar datos
            this.showFiltersAndTable('vehicles');
            this.loadVehicleReports();
        } else if (selectedType === 'infractions') {
            // Infracciones - mostrar filtros y tabla, cargar datos
            this.showFiltersAndTable('infractions');
            this.loadInfractionReports();
        } else if (selectedType === 'travels') {
            // Viajes - mostrar filtros y tabla, cargar datos
            this.showFiltersAndTable('travels');
            this.loadTravelReports();
        }
    }
    
    /**
     * Oculta los filtros y la tabla
     */
    hideFiltersAndTable() {
        console.log('🙈 Ocultando filtros y tabla');
        
        // Mostrar mensaje de "Sin filtro"
        if (this.noReportSelected) {
            this.noReportSelected.style.display = 'block';
        }
        
        // Ocultar panel de filtros
        const filtersCard = document.querySelector('.incidents-filters-card');
        if (filtersCard) {
            filtersCard.style.display = 'none';
        }
        
        // Ocultar toda la sección de la tabla
        const tableSection = document.querySelector('.incidents-table-section');
        if (tableSection) {
            tableSection.style.display = 'none';
        }
        
        // Ocultar estados de carga, error, etc.
        if (this.incidentsLoading) this.incidentsLoading.style.display = 'none';
        if (this.incidentsEmpty) this.incidentsEmpty.style.display = 'none';
        if (this.incidentsError) this.incidentsError.style.display = 'none';
        if (this.incidentsTableWrapper) this.incidentsTableWrapper.style.display = 'none';
        // La paginación siempre permanece visible
    }
    
    /**
     * Muestra los filtros y la tabla
     */
    showFiltersAndTable(reportType = 'incidents') {
        console.log('👁️ Mostrando filtros y tabla para:', reportType);
        
        // Ocultar mensaje de "Sin filtro"
        if (this.noReportSelected) {
            this.noReportSelected.style.display = 'none';
        }
        
        // Mostrar panel de filtros
        const filtersCard = document.querySelector('.incidents-filters-card');
        if (filtersCard) {
            filtersCard.style.display = 'block';
        }
        
        // Mostrar toda la sección de la tabla
        const tableSection = document.querySelector('.incidents-table-section');
        if (tableSection) {
            tableSection.style.display = 'block';
        }

        // Mostrar el wrapper de la tabla
        if (this.incidentsTableWrapper) {
            this.incidentsTableWrapper.style.display = 'block';
        }

        // Mostrar/ocultar secciones de filtros según el tipo
        if (this.incidentsFiltersSection && this.vehiclesFiltersSection && this.infractionsFiltersSection && this.travelsFiltersSection) {
            if (reportType === 'incidents') {
                this.incidentsFiltersSection.style.display = 'block';
                this.vehiclesFiltersSection.style.display = 'none';
                this.infractionsFiltersSection.style.display = 'none';
                this.travelsFiltersSection.style.display = 'none';
            } else if (reportType === 'vehicles') {
                this.incidentsFiltersSection.style.display = 'none';
                this.vehiclesFiltersSection.style.display = 'block';
                this.infractionsFiltersSection.style.display = 'none';
                this.travelsFiltersSection.style.display = 'none';
            } else if (reportType === 'infractions') {
                this.incidentsFiltersSection.style.display = 'none';
                this.vehiclesFiltersSection.style.display = 'none';
                this.infractionsFiltersSection.style.display = 'block';
                this.travelsFiltersSection.style.display = 'none';
            } else if (reportType === 'travels') {
                this.incidentsFiltersSection.style.display = 'none';
                this.vehiclesFiltersSection.style.display = 'none';
                this.infractionsFiltersSection.style.display = 'none';
                this.travelsFiltersSection.style.display = 'block';
            }
        }

        // Mostrar/ocultar headers de la tabla según el tipo
        if (this.incidentsHeaders && this.vehiclesHeaders && this.infractionsHeaders && this.travelsHeaders) {
            if (reportType === 'incidents') {
                this.incidentsHeaders.style.display = 'table-row';
                this.vehiclesHeaders.style.display = 'none';
                this.infractionsHeaders.style.display = 'none';
                this.travelsHeaders.style.display = 'none';
            } else if (reportType === 'vehicles') {
                this.incidentsHeaders.style.display = 'none';
                this.vehiclesHeaders.style.display = 'table-row';
                this.infractionsHeaders.style.display = 'none';
                this.travelsHeaders.style.display = 'none';
            } else if (reportType === 'infractions') {
                this.incidentsHeaders.style.display = 'none';
                this.vehiclesHeaders.style.display = 'none';
                this.infractionsHeaders.style.display = 'table-row';
                this.travelsHeaders.style.display = 'none';
            } else if (reportType === 'travels') {
                this.incidentsHeaders.style.display = 'none';
                this.vehiclesHeaders.style.display = 'none';
                this.infractionsHeaders.style.display = 'none';
                this.travelsHeaders.style.display = 'table-row';
            }
        }

        // Preparar la paginación (se mostrará cuando se carguen los datos)
        if (this.incidentsPagination) {
            this.incidentsPagination.style.display = 'none'; // Ocultar hasta que se carguen datos
        }
    }
    
    /**
     * Inicializa el estado basado en el tipo de reporte seleccionado por defecto
     */
    initializeReportTypeState() {
        if (this.reportTypeSelect) {
            const initialType = this.reportTypeSelect.value;
            console.log('🔧 Estado inicial del tipo de reporte:', initialType);
            
            if (initialType === '' || initialType === 'none') {
                // Sin filtro por defecto - ocultar todo
                this.hideFiltersAndTable();
            } else if (initialType === 'incidents') {
                // Incidentes seleccionado - mostrar y cargar datos
                this.showFiltersAndTable();
                this.loadIncidentReports();
            }
        } else {
            console.warn('⚠️ Selector de tipo de reporte no encontrado');
        }
    }
    
    /**
     * Verifica si hay filtros activos
     */
    hasActiveFilters() {
        if (this.currentReportType === 'incidents') {
            return (
                (this.incidentFilters.userId && this.incidentFilters.userId.toString().trim() !== '') ||
                (this.incidentFilters.travelId && this.incidentFilters.travelId.toString().trim() !== '') ||
                (this.incidentFilters.typeId && this.incidentFilters.typeId.toString().trim() !== '') ||
                (this.incidentFilters.active && this.incidentFilters.active.trim() !== '') ||
                (this.incidentFilters.comment && this.incidentFilters.comment.trim() !== '') ||
                this.incidentFilters.sortBy !== 'id' ||
                this.incidentFilters.sortDirection !== 'DESC'
            );
        } else if (this.currentReportType === 'vehicles') {
            return (
                (this.vehicleFilters.licensePlate && this.vehicleFilters.licensePlate.trim() !== '') ||
                (this.vehicleFilters.brandId && this.vehicleFilters.brandId.toString().trim() !== '') ||
                (this.vehicleFilters.modelId && this.vehicleFilters.modelId.toString().trim() !== '') ||
                (this.vehicleFilters.colorId && this.vehicleFilters.colorId.toString().trim() !== '') ||
                (this.vehicleFilters.manufactureYearFrom && this.vehicleFilters.manufactureYearFrom.toString().trim() !== '') ||
                (this.vehicleFilters.manufactureYearTo && this.vehicleFilters.manufactureYearTo.toString().trim() !== '') ||
                (this.vehicleFilters.seatCount && this.vehicleFilters.seatCount.toString().trim() !== '') ||
                (this.vehicleFilters.passengerCount && this.vehicleFilters.passengerCount.toString().trim() !== '') ||
                (this.vehicleFilters.fuelTypeId && this.vehicleFilters.fuelTypeId.toString().trim() !== '') ||
                (this.vehicleFilters.vehicleClassId && this.vehicleFilters.vehicleClassId.toString().trim() !== '') ||
                (this.vehicleFilters.categoryId && this.vehicleFilters.categoryId.toString().trim() !== '') ||
                (this.vehicleFilters.active && this.vehicleFilters.active.trim() !== '') ||
                (this.vehicleFilters.companyId && this.vehicleFilters.companyId.toString().trim() !== '') ||
                (this.vehicleFilters.districtId && this.vehicleFilters.districtId.toString().trim() !== '') ||
                (this.vehicleFilters.statusId && this.vehicleFilters.statusId.toString().trim() !== '') ||
                (this.vehicleFilters.procedureTypeId && this.vehicleFilters.procedureTypeId.toString().trim() !== '') ||
                (this.vehicleFilters.modalityId && this.vehicleFilters.modalityId.toString().trim() !== '') ||
                this.vehicleFilters.sortBy !== 'licensePlate' ||
                this.vehicleFilters.sortDirection !== 'ASC'
            );
        }
        
        return false;
    }
}

// Hacer el controlador disponible globalmente
window.AuditReportsController = AuditReportsController;