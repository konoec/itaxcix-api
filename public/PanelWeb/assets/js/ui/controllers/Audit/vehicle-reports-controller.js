/**
 * Controlador para reportes de vehículos
 * Extiende BaseReportsController para manejar reportes de vehículos específicamente
 */
class VehicleReportsController extends BaseReportsController {
    constructor() {
        super();
        console.log('🚗 Inicializando VehicleReportsController...');
        
        // Filtros específicos para vehículos - solo valores por defecto
        this.filters = {
            sortBy: 'licensePlate',
            sortDirection: 'ASC'
        };
        
        // Servicio de datos
        this.service = window.vehicleReportsService;
        
        // Elementos específicos del DOM
        this.initializeVehicleElements();
        
        // Event listeners específicos
        this.initializeVehicleEventListeners();
    }
    
    /**
     * Inicializa los elementos específicos para vehículos
     */
    initializeVehicleElements() {
        // Sección de filtros específica
        this.filtersSection = document.getElementById('vehicles-filters');
        this.headers = document.getElementById('vehicles-headers');
        
        // Filtros específicos
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
        
        // Botones de acción específicos
        this.applyFiltersBtn = document.getElementById('apply-filters-btn-vehicles');
        this.clearFiltersBtn = document.getElementById('clear-filters-btn-vehicles');
        this.refreshDataBtn = document.getElementById('refresh-data-btn-vehicles');
        
        console.log('✅ Elementos específicos de vehículos inicializados');
    }
    
    /**
     * Inicializa los event listeners específicos para vehículos
     */
    initializeVehicleEventListeners() {
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
        
        // Enter en campos de texto principales
        [this.filterLicensePlate, this.filterSeatCount, this.filterPassengerCount].forEach(input => {
            if (input) {
                input.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        this.applyFilters();
                    }
                });
            }
        });
        
        // Validación en tiempo real para placa
        if (this.filterLicensePlate) {
            this.filterLicensePlate.addEventListener('input', (e) => {
                const value = e.target.value.trim();
                const feedback = this.getLicensePlateFeedback(value);
                this.showLicensePlateFeedback(feedback);
            });
        }
        
        console.log('✅ Event listeners específicos de vehículos inicializados');
    }
    
    /**
     * Carga los reportes de vehículos
     */
    async loadReports() {
        if (!this.service) {
            console.error('❌ Servicio de vehículos no disponible');
            this.showErrorState('Servicio de vehículos no disponible');
            return;
        }

        try {
            this.showLoadingState();
            
            const requestFilters = {
                page: this.currentPage,
                perPage: this.perPage,
                ...this.filters
            };
            
            console.log('🔄 Cargando reportes de vehículos con filtros:', requestFilters);
            
            const response = await this.service.getVehicleReports(requestFilters);
            console.log('🔍 Respuesta completa de la API:', response);
            
            if (response && response.success && response.data && response.data.data) {
                // La API devuelve data.data como array de vehículos
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
                
                console.log('📊 Datos procesados:', {
                    vehiculos: this.currentData.length,
                    totalResultados: this.totalResults,
                    paginaActual: this.currentPage,
                    totalPaginas: this.totalPages
                });
                
                this.renderTable();
                this.updatePagination();
                this.showTableState();
                
                console.log('✅ Reportes de vehículos cargados exitosamente');
            } else {
                console.error('❌ Estructura de respuesta inválida:', response);
                throw new Error('Respuesta inválida del servidor');
            }
            
        } catch (error) {
            console.error('❌ Error al cargar reportes de vehículos:', error);
            this.showErrorState(error.message || 'Error al cargar los reportes de vehículos');
        }
    }
    
    /**
     * Aplica los filtros actuales
     */
    applyFilters() {
        // Validar filtros antes de aplicarlos
        const validation = this.validateFilters();
        if (!validation.isValid) {
            this.showValidationErrors(validation.errors);
            return;
        }
        
        this.updateFiltersFromForm();
        this.currentPage = 1;
        this.loadReports();
    }
    
    /**
     * Limpia todos los filtros
     */
    clearFilters() {
        // Limpiar formulario
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
        if (this.pageSizeSelect) this.pageSizeSelect.value = '20';
        
        // Resetear filtros - solo mantener valores por defecto
        this.filters = {
            sortBy: 'licensePlate',
            sortDirection: 'ASC'
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
        // Crear objeto de filtros solo con valores válidos
        const formFilters = {};
        
        // Filtros de texto - validar que no esté vacío
        const licensePlate = this.filterLicensePlate?.value?.trim();
        if (licensePlate && licensePlate.length >= 4 && licensePlate.length <= 10) {
            // Validar que sea alfanumérico
            if (/^[A-Za-z0-9]+$/.test(licensePlate)) {
                formFilters.licensePlate = licensePlate.toUpperCase();
            } else {
                console.warn('⚠️ Placa debe contener solo caracteres alfanuméricos:', licensePlate);
            }
        } else if (licensePlate && licensePlate.length > 0) {
            console.warn('⚠️ Placa debe tener entre 4 y 10 caracteres:', licensePlate);
        }
        
        // Filtros numéricos (IDs) - validar que sean números válidos
        const brandId = this.filterBrandId?.value;
        if (brandId && brandId !== '' && !isNaN(brandId)) {
            formFilters.brandId = parseInt(brandId);
        }
        
        const modelId = this.filterModelId?.value;
        if (modelId && modelId !== '' && !isNaN(modelId)) {
            formFilters.modelId = parseInt(modelId);
        }
        
        const colorId = this.filterColorId?.value;
        if (colorId && colorId !== '' && !isNaN(colorId)) {
            formFilters.colorId = parseInt(colorId);
        }
        
        const fuelTypeId = this.filterFuelTypeId?.value;
        if (fuelTypeId && fuelTypeId !== '' && !isNaN(fuelTypeId)) {
            formFilters.fuelTypeId = parseInt(fuelTypeId);
        }
        
        const vehicleClassId = this.filterVehicleClassId?.value;
        if (vehicleClassId && vehicleClassId !== '' && !isNaN(vehicleClassId)) {
            formFilters.vehicleClassId = parseInt(vehicleClassId);
        }
        
        const categoryId = this.filterCategoryId?.value;
        if (categoryId && categoryId !== '' && !isNaN(categoryId)) {
            formFilters.categoryId = parseInt(categoryId);
        }
        
        const companyId = this.filterCompanyId?.value;
        if (companyId && companyId !== '' && !isNaN(companyId)) {
            formFilters.companyId = parseInt(companyId);
        }
        
        const districtId = this.filterDistrictId?.value;
        if (districtId && districtId !== '' && !isNaN(districtId)) {
            formFilters.districtId = parseInt(districtId);
        }
        
        const statusId = this.filterStatusId?.value;
        if (statusId && statusId !== '' && !isNaN(statusId)) {
            formFilters.statusId = parseInt(statusId);
        }
        
        const procedureTypeId = this.filterProcedureTypeId?.value;
        if (procedureTypeId && procedureTypeId !== '' && !isNaN(procedureTypeId)) {
            formFilters.procedureTypeId = parseInt(procedureTypeId);
        }
        
        const modalityId = this.filterModalityId?.value;
        if (modalityId && modalityId !== '' && !isNaN(modalityId)) {
            formFilters.modalityId = parseInt(modalityId);
        }
        
        // Filtros de año de fabricación - validar rango válido
        const manufactureYearFrom = this.filterManufactureYearFrom?.value;
        if (manufactureYearFrom && manufactureYearFrom !== '' && !isNaN(manufactureYearFrom)) {
            const yearFrom = parseInt(manufactureYearFrom);
            if (yearFrom >= 1900 && yearFrom <= new Date().getFullYear() + 1) {
                formFilters.manufactureYearFrom = yearFrom;
            }
        }
        
        const manufactureYearTo = this.filterManufactureYearTo?.value;
        if (manufactureYearTo && manufactureYearTo !== '' && !isNaN(manufactureYearTo)) {
            const yearTo = parseInt(manufactureYearTo);
            if (yearTo >= 1900 && yearTo <= new Date().getFullYear() + 1) {
                formFilters.manufactureYearTo = yearTo;
            }
        }
        
        // Filtros de conteo - validar que sean números positivos
        const seatCount = this.filterSeatCount?.value;
        if (seatCount && seatCount !== '' && !isNaN(seatCount)) {
            const seats = parseInt(seatCount);
            if (seats > 0) {
                formFilters.seatCount = seats;
            }
        }
        
        const passengerCount = this.filterPassengerCount?.value;
        if (passengerCount && passengerCount !== '' && !isNaN(passengerCount)) {
            const passengers = parseInt(passengerCount);
            if (passengers >= 0) {
                formFilters.passengerCount = passengers;
            }
        }
        
        // Filtro boolean para active - asegurar formato correcto
        const active = this.filterVehicleActive?.value;
        if (active && active !== '') {
            if (active === 'true' || active === '1' || active === 'active') {
                formFilters.active = true;
            } else if (active === 'false' || active === '0' || active === 'inactive') {
                formFilters.active = false;
            }
        }
        
        // Ordenamiento - validar valores permitidos
        const sortBy = this.vehicleSortBy?.value;
        if (sortBy && sortBy !== '') {
            formFilters.sortBy = sortBy;
        } else {
            formFilters.sortBy = 'licensePlate'; // Valor por defecto
        }
        
        const sortDirection = this.vehicleSortDirection?.value;
        if (sortDirection && (sortDirection === 'ASC' || sortDirection === 'DESC')) {
            formFilters.sortDirection = sortDirection;
        } else {
            formFilters.sortDirection = 'ASC'; // Valor por defecto
        }
        
        // Paginación - validar rangos
        const perPageValue = parseInt(this.pageSizeSelect?.value || '20');
        if (perPageValue >= 1 && perPageValue <= 100) {
            this.perPage = perPageValue;
        } else {
            this.perPage = 20; // Valor por defecto
        }
        
        this.filters = formFilters;
        
        console.log('📝 Filtros de vehículos actualizados:', this.filters);
        console.log('📄 Paginación configurada:', { page: this.currentPage, perPage: this.perPage });
    }
    
    /**
     * Renderiza la tabla con los datos actuales
     */
    renderTable() {
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
            const row = this.createTableRow(vehicle);
            this.incidentsTableBody.appendChild(row);
        });

        console.log('✅ Tabla de vehículos renderizada');
    }

    /**
     * Crea una fila de tabla para un vehículo
     */
    createTableRow(vehicle) {
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
     * Verifica si hay filtros activos
     */
    hasActiveFilters() {
        return Object.keys(this.filters).some(key => {
            // Excluir los parámetros de ordenamiento por defecto
            if (key === 'sortBy' && this.filters[key] === 'licensePlate') return false;
            if (key === 'sortDirection' && this.filters[key] === 'ASC') return false;
            
            // Verificar si hay algún filtro con valor
            const value = this.filters[key];
            return value !== undefined && value !== null && value !== '';
        });
    }
    
    /**
     * Muestra los filtros y headers específicos para vehículos
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
     * Oculta los filtros y headers específicos para vehículos
     */
    hideFiltersAndHeaders() {
        if (this.filtersSection) {
            this.filtersSection.style.display = 'none';
        }
        
        if (this.headers) {
            this.headers.style.display = 'none';
        }
    }
    
    /**
     * Valida los filtros antes de aplicarlos
     * @returns {Object} { isValid: boolean, errors: string[] }
     */
    validateFilters() {
        const errors = [];
        
        // Validar placa
        const licensePlate = this.filterLicensePlate?.value?.trim();
        if (licensePlate) {
            if (licensePlate.length < 4 || licensePlate.length > 10) {
                errors.push('La placa debe tener entre 4 y 10 caracteres');
            } else if (!/^[A-Za-z0-9]+$/.test(licensePlate)) {
                errors.push('La placa debe contener solo caracteres alfanuméricos');
            }
        }
        
        // Validar años de fabricación
        const yearFrom = this.filterManufactureYearFrom?.value;
        const yearTo = this.filterManufactureYearTo?.value;
        const currentYear = new Date().getFullYear();
        
        if (yearFrom && (yearFrom < 1900 || yearFrom > currentYear + 1)) {
            errors.push(`El año inicial debe estar entre 1900 y ${currentYear + 1}`);
        }
        
        if (yearTo && (yearTo < 1900 || yearTo > currentYear + 1)) {
            errors.push(`El año final debe estar entre 1900 y ${currentYear + 1}`);
        }
        
        if (yearFrom && yearTo && parseInt(yearFrom) > parseInt(yearTo)) {
            errors.push('El año inicial no puede ser mayor al año final');
        }
        
        // Validar conteos
        const seatCount = this.filterSeatCount?.value;
        if (seatCount && (isNaN(seatCount) || parseInt(seatCount) <= 0)) {
            errors.push('El número de asientos debe ser un número positivo');
        }
        
        const passengerCount = this.filterPassengerCount?.value;
        if (passengerCount && (isNaN(passengerCount) || parseInt(passengerCount) < 0)) {
            errors.push('El número de pasajeros debe ser un número mayor o igual a 0');
        }
        
        return {
            isValid: errors.length === 0,
            errors: errors
        };
    }

    /**
     * Muestra mensajes de error de validación
     * @param {string[]} errors 
     */
    showValidationErrors(errors) {
        // Crear o encontrar el contenedor de errores
        let errorContainer = document.getElementById('validation-errors');
        if (!errorContainer) {
            errorContainer = document.createElement('div');
            errorContainer.id = 'validation-errors';
            errorContainer.className = 'alert alert-danger mt-2';
            errorContainer.style.display = 'none';
            
            // Insertar después del contenedor de filtros
            if (this.filtersSection) {
                this.filtersSection.parentNode.insertBefore(errorContainer, this.filtersSection.nextSibling);
            }
        }
        
        if (errors.length > 0) {
            errorContainer.innerHTML = `
                <strong>Errores de validación:</strong>
                <ul class="mb-0 mt-1">
                    ${errors.map(error => `<li>${error}</li>`).join('')}
                </ul>
            `;
            errorContainer.style.display = 'block';
            
            // Auto-ocultar después de 5 segundos
            setTimeout(() => {
                errorContainer.style.display = 'none';
            }, 5000);
        } else {
            errorContainer.style.display = 'none';
        }
    }
    
    /**
     * Obtiene feedback sobre la validez de una placa
     * @param {string} value 
     * @returns {Object}
     */
    getLicensePlateFeedback(value) {
        if (!value) {
            return { isValid: true, message: '', type: 'info' };
        }
        
        if (value.length < 4) {
            return { 
                isValid: false, 
                message: `Placa muy corta (${value.length}/4 caracteres mínimo)`, 
                type: 'warning' 
            };
        }
        
        if (value.length > 10) {
            return { 
                isValid: false, 
                message: `Placa muy larga (${value.length}/10 caracteres máximo)`, 
                type: 'error' 
            };
        }
        
        if (!/^[A-Za-z0-9]+$/.test(value)) {
            return { 
                isValid: false, 
                message: 'Solo se permiten caracteres alfanuméricos', 
                type: 'error' 
            };
        }
        
        return { 
            isValid: true, 
            message: `Placa válida (${value.length} caracteres)`, 
            type: 'success' 
        };
    }

    /**
     * Muestra feedback visual para la placa
     * @param {Object} feedback 
     */
    showLicensePlateFeedback(feedback) {
        if (!this.filterLicensePlate) return;
        
        // Crear o encontrar el elemento de feedback
        let feedbackElement = document.getElementById('license-plate-feedback');
        if (!feedbackElement) {
            feedbackElement = document.createElement('small');
            feedbackElement.id = 'license-plate-feedback';
            feedbackElement.className = 'form-text';
            this.filterLicensePlate.parentNode.appendChild(feedbackElement);
        }
        
        // Limpiar clases previas
        this.filterLicensePlate.classList.remove('is-valid', 'is-invalid');
        feedbackElement.classList.remove('text-success', 'text-warning', 'text-danger');
        
        if (feedback.message) {
            feedbackElement.textContent = feedback.message;
            feedbackElement.style.display = 'block';
            
            switch (feedback.type) {
                case 'success':
                    this.filterLicensePlate.classList.add('is-valid');
                    feedbackElement.classList.add('text-success');
                    break;
                case 'warning':
                    feedbackElement.classList.add('text-warning');
                    break;
                case 'error':
                    this.filterLicensePlate.classList.add('is-invalid');
                    feedbackElement.classList.add('text-danger');
                    break;
            }
        } else {
            feedbackElement.style.display = 'none';
        }
    }
}

// Hacer el controlador disponible globalmente
window.VehicleReportsController = VehicleReportsController;
