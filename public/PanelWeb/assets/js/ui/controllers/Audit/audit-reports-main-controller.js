/**
 * Controlador principal para reportes de auditoría
 * Coordina todos los controladores específicos de cada tipo de reporte
 */
class AuditReportsMainController {
    constructor() {
        console.log('🎯 Inicializando AuditReportsMainController...');
        
        // Estado actual del tipo de reporte
        this.currentReportType = '';
        
        // Instancias de controladores específicos
        this.controllers = {};
        
        // Elementos del DOM
        this.initializeMainElements();
        
        // Crear instancias de controladores específicos
        this.initializeControllers();
        
        // Event listeners principales
        this.initializeMainEventListeners();
        
        // Configurar estado inicial basado en el tipo de reporte seleccionado
        this.initializeReportTypeState();
    }
    
    /**
     * Inicializa los elementos principales del DOM
     */
    initializeMainElements() {
        // Selector de tipo de reporte
        this.reportTypeSelect = document.getElementById('report-type-select');
        
        // Secciones de filtros
        this.incidentsFiltersSection = document.getElementById('incidents-filters');
        this.vehiclesFiltersSection = document.getElementById('vehicles-filters');
        this.infractionsFiltersSection = document.getElementById('infractions-filters');
        this.travelsFiltersSection = document.getElementById('travels-filters');
        this.ratingsFiltersSection = document.getElementById('ratings-filters');
        this.usersFiltersSection = document.getElementById('users-filters');
        
        // Headers de la tabla
        this.incidentsHeaders = document.getElementById('incidents-headers');
        this.vehiclesHeaders = document.getElementById('vehicles-headers');
        this.infractionsHeaders = document.getElementById('infractions-headers');
        this.travelsHeaders = document.getElementById('travels-headers');
        this.ratingsHeaders = document.getElementById('ratings-headers');
        this.usersHeaders = document.getElementById('users-headers');
        
        // Mensaje de "Sin filtro"
        this.noReportSelected = document.getElementById('no-report-selected');
        
        console.log('✅ Elementos principales del DOM inicializados');
    }
    
    /**
     * Crea las instancias de los controladores específicos
     */
    initializeControllers() {
        try {
            // Crear controladores específicos
            this.controllers.incidents = new IncidentReportsController();
            this.controllers.vehicles = new VehicleReportsController();
            this.controllers.infractions = new InfractionReportsController();
            this.controllers.travels = new TravelReportsController();
            this.controllers.ratings = new RatingReportsController();
            this.controllers.usuarios = new UserReportsController();
            
            console.log('✅ Controladores específicos inicializados');
        } catch (error) {
            console.error('❌ Error al inicializar controladores:', error);
        }
    }
    
    /**
     * Inicializa los event listeners principales
     */
    initializeMainEventListeners() {
        // Selector de tipo de reporte
        if (this.reportTypeSelect) {
            this.reportTypeSelect.addEventListener('change', () => this.handleReportTypeChange());
        }
        
        // Toggle de filtros (manejado desde el controlador principal)
        const toggleFiltersBtn = document.getElementById('toggle-filters');
        if (toggleFiltersBtn) {
            toggleFiltersBtn.addEventListener('click', () => this.toggleFilters());
            console.log('✅ Event listener para toggle de filtros agregado');
        }
        
        console.log('✅ Event listeners principales inicializados');
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
            this.controllers.incidents.loadReports();
        } else if (selectedType === 'vehicles') {
            // Vehículos - mostrar filtros y tabla, cargar datos
            this.showFiltersAndTable('vehicles');
            this.controllers.vehicles.loadReports();
        } else if (selectedType === 'infractions') {
            // Infracciones - mostrar filtros y tabla, cargar datos
            this.showFiltersAndTable('infractions');
            this.controllers.infractions.loadReports();
        } else if (selectedType === 'travels') {
            // Viajes - mostrar filtros y tabla, cargar datos
            this.showFiltersAndTable('travels');
            this.controllers.travels.loadReports();
        } else if (selectedType === 'ratings') {
            // Calificaciones - mostrar filtros y tabla, cargar datos
            this.showFiltersAndTable('ratings');
            this.controllers.ratings.loadReports();
        } else if (selectedType === 'usuarios') {
            // Usuarios - mostrar filtros y tabla, cargar datos
            this.showFiltersAndTable('usuarios');
            this.controllers.usuarios.loadReports();
        }
    }
    
    /**
     * Oculta los filtros y la tabla
     */
    hideFiltersAndTable() {
        console.log('🙈 Ocultando filtros y tabla');
        
        // Mostrar mensaje de "Sin filtro"
        if (this.noReportSelected) {
            console.log('📋 Mostrando mensaje "Sin reporte seleccionado"');
            this.noReportSelected.style.display = 'block';
            this.noReportSelected.style.visibility = 'visible';
        } else {
            console.warn('⚠️ Elemento "no-report-selected" no encontrado');
            // Buscar el elemento directamente por ID como fallback
            const fallbackElement = document.getElementById('no-report-selected');
            if (fallbackElement) {
                console.log('📋 Encontrado elemento fallback, mostrándolo');
                fallbackElement.style.display = 'block';
                fallbackElement.style.visibility = 'visible';
            }
        }
        
        // Ocultar panel de filtros
        const filtersCard = document.querySelector('.incidents-filters-card');
        if (filtersCard) {
            console.log('📋 Ocultando panel de filtros');
            filtersCard.style.display = 'none';
        } else {
            console.warn('⚠️ Panel de filtros no encontrado');
        }
        
        // Ocultar toda la sección de la tabla
        const tableSection = document.querySelector('.incidents-table-section');
        if (tableSection) {
            console.log('📋 Ocultando sección de tabla');
            tableSection.style.display = 'none';
        } else {
            console.warn('⚠️ Sección de tabla no encontrada');
        }
        
        // Ocultar todos los controladores específicos
        console.log('📋 Ocultando filtros específicos de controladores');
        Object.keys(this.controllers).forEach(type => {
            const controller = this.controllers[type];
            if (controller && controller.hideFiltersAndHeaders) {
                console.log(`📋 Ocultando filtros de ${type}`);
                controller.hideFiltersAndHeaders();
            }
        });
        
        console.log('✅ Filtros y tabla ocultados completamente');
    }
    
    /**
     * Muestra los filtros y la tabla para un tipo específico de reporte
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

        // Ocultar todos los filtros y headers primero
        this.hideAllFiltersAndHeaders();
        
        // Mostrar el controlador específico
        if (this.controllers[reportType] && this.controllers[reportType].showFiltersAndHeaders) {
            this.controllers[reportType].showFiltersAndHeaders();
        }
    }
    
    /**
     * Oculta todos los filtros y headers específicos
     */
    hideAllFiltersAndHeaders() {
        // Ocultar secciones de filtros
        if (this.incidentsFiltersSection) this.incidentsFiltersSection.style.display = 'none';
        if (this.vehiclesFiltersSection) this.vehiclesFiltersSection.style.display = 'none';
        if (this.infractionsFiltersSection) this.infractionsFiltersSection.style.display = 'none';
        if (this.travelsFiltersSection) this.travelsFiltersSection.style.display = 'none';
        if (this.ratingsFiltersSection) this.ratingsFiltersSection.style.display = 'none';
        if (this.usersFiltersSection) this.usersFiltersSection.style.display = 'none';
        
        // Ocultar headers de la tabla
        if (this.incidentsHeaders) this.incidentsHeaders.style.display = 'none';
        if (this.vehiclesHeaders) this.vehiclesHeaders.style.display = 'none';
        if (this.infractionsHeaders) this.infractionsHeaders.style.display = 'none';
        if (this.travelsHeaders) this.travelsHeaders.style.display = 'none';
        if (this.ratingsHeaders) this.ratingsHeaders.style.display = 'none';
        if (this.usersHeaders) this.usersHeaders.style.display = 'none';
    }
    
    /**
     * Inicializa el estado basado en el tipo de reporte seleccionado por defecto
     */
    initializeReportTypeState() {
        console.log('🔧 Inicializando estado del tipo de reporte...');
        
        if (this.reportTypeSelect) {
            const initialType = this.reportTypeSelect.value;
            console.log('🔧 Estado inicial del tipo de reporte:', initialType);
            
            if (initialType === '' || initialType === 'none') {
                // Sin filtro por defecto - ocultar todo y mostrar mensaje
                console.log('🔧 Configurando estado inicial: Sin filtro');
                this.hideFiltersAndTable();
            } else if (initialType === 'incidents') {
                // Incidentes seleccionado - mostrar y cargar datos
                console.log('🔧 Configurando estado inicial: Incidentes');
                this.showFiltersAndTable('incidents');
                this.controllers.incidents.loadReports();
            } else if (initialType === 'vehicles') {
                // Vehículos seleccionado - mostrar y cargar datos
                console.log('🔧 Configurando estado inicial: Vehículos');
                this.showFiltersAndTable('vehicles');
                this.controllers.vehicles.loadReports();
            } else if (initialType === 'infractions') {
                // Infracciones seleccionado - mostrar y cargar datos
                console.log('🔧 Configurando estado inicial: Infracciones');
                this.showFiltersAndTable('infractions');
                this.controllers.infractions.loadReports();
            } else if (initialType === 'travels') {
                // Viajes seleccionado - mostrar y cargar datos
                console.log('🔧 Configurando estado inicial: Viajes');
                this.showFiltersAndTable('travels');
                this.controllers.travels.loadReports();
            } else if (initialType === 'ratings') {
                // Calificaciones seleccionado - mostrar y cargar datos
                console.log('🔧 Configurando estado inicial: Calificaciones');
                this.showFiltersAndTable('ratings');
                this.controllers.ratings.loadReports();
            } else if (initialType === 'usuarios') {
                // Usuarios seleccionado - mostrar y cargar datos
                console.log('🔧 Configurando estado inicial: Usuarios');
                this.showFiltersAndTable('usuarios');
                this.controllers.usuarios.loadReports();
            }
        } else {
            console.warn('⚠️ Selector de tipo de reporte no encontrado - aplicando estado por defecto');
            this.hideFiltersAndTable();
        }
    }
    
    /**
     * Obtiene el controlador activo según el tipo de reporte actual
     */
    getActiveController() {
        return this.controllers[this.currentReportType] || null;
    }
    
    /**
     * Toggle de visibilidad de filtros (manejado desde el controlador principal)
     */
    toggleFilters() {
        console.log('🔄 Toggle de filtros activado desde controlador principal');
        
        const filtersContent = document.getElementById('filters-content');
        const toggleBtn = document.getElementById('toggle-filters');
        
        if (filtersContent) {
            const isHidden = filtersContent.style.display === 'none' || 
                            getComputedStyle(filtersContent).display === 'none';
            
            console.log('🔄 Estado actual de filtros:', isHidden ? 'oculto' : 'visible');
            
            filtersContent.style.display = isHidden ? 'block' : 'none';
            
            console.log('🔄 Nuevo estado de filtros:', isHidden ? 'visible' : 'oculto');
            
            if (toggleBtn) {
                const icon = toggleBtn.querySelector('i');
                if (icon) {
                    icon.className = isHidden ? 'fas fa-chevron-up' : 'fas fa-chevron-down';
                    console.log('🔄 Icono actualizado a:', icon.className);
                }
            }
        } else {
            console.warn('⚠️ Elemento filters-content no encontrado');
        }
    }

    /**
     * Destruye las instancias de controladores (limpieza)
     */
    destroy() {
        console.log('🗑️ Destruyendo AuditReportsMainController...');
        
        // Limpiar event listeners
        if (this.reportTypeSelect) {
            this.reportTypeSelect.removeEventListener('change', this.handleReportTypeChange);
        }
        
        // Limpiar controladores específicos si tienen método destroy
        Object.values(this.controllers).forEach(controller => {
            if (controller.destroy && typeof controller.destroy === 'function') {
                controller.destroy();
            }
        });
        
        this.controllers = {};
        console.log('✅ AuditReportsMainController destruido');
    }
}

// Hacer el controlador principal disponible globalmente
window.AuditReportsMainController = AuditReportsMainController;
