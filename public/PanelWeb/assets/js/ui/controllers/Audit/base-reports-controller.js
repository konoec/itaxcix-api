/**
 * Controlador base para reportes de auditoría
 * Contiene funcionalidades comunes a todos los tipos de reportes
 */
class BaseReportsController {
    constructor() {
        // Configuración inicial común
        this.currentData = [];
        this.isLoading = false;
        this.currentPage = 1;
        this.perPage = 20;
        this.totalPages = 1;
        this.totalResults = 0;
        
        // Elementos del DOM comunes
        this.initializeCommonElements();
        
        // Event listeners comunes
        this.initializeCommonEventListeners();
    }
    
    /**
     * Inicializa los elementos del DOM comunes a todos los reportes
     */
    initializeCommonElements() {
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
        
        // Botones de paginación
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
        
        console.log('✅ Elementos comunes del DOM inicializados');
    }
    
    /**
     * Inicializa los event listeners comunes
     */
    initializeCommonEventListeners() {
        // Cambio de tamaño de página
        if (this.pageSizeSelect) {
            this.pageSizeSelect.addEventListener('change', () => this.changePageSize());
        }
        
        // Paginación
        if (this.prevPageBtn) {
            this.prevPageBtn.addEventListener('click', () => this.goToPage(this.currentPage - 1));
        }
        
        if (this.nextPageBtn) {
            this.nextPageBtn.addEventListener('click', () => this.goToPage(this.currentPage + 1));
        }
        
        // NOTA: Toggle de filtros ahora se maneja desde el controlador principal
        // para evitar conflictos entre múltiples controladores
        
        // Botones del estado vacío
        if (this.clearFiltersEmptyBtn) {
            this.clearFiltersEmptyBtn.addEventListener('click', () => this.clearFilters());
        }
        
        if (this.refreshEmptyBtn) {
            this.refreshEmptyBtn.addEventListener('click', () => this.refreshData());
        }
        
        console.log('✅ Event listeners comunes inicializados');
    }
    
    /**
     * Navega a una página específica
     */
    goToPage(page) {
        if (page < 1 || page > this.totalPages || page === this.currentPage) {
            return;
        }
        
        this.currentPage = page;
        this.loadReports();
    }
    
    /**
     * Cambia el tamaño de página
     */
    changePageSize() {
        let newPerPage = 20;
        if (this.pageSizeSelect?.value) {
            newPerPage = parseInt(this.pageSizeSelect.value);
        }
        
        this.perPage = newPerPage;
        this.currentPage = 1;
        this.loadReports();
    }
    
    /**
     * Toggle de visibilidad de filtros
     */
    toggleFilters() {
        console.log('🔄 Toggle de filtros activado');
        
        // Buscar elemento directamente por ID como fallback
        const filtersContent = this.filtersContent || document.getElementById('filters-content');
        const toggleBtn = this.toggleFiltersBtn || document.getElementById('toggle-filters');
        
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
        
        // Actualizar botones de navegación
        if (this.prevPageBtn) {
            this.prevPageBtn.disabled = this.currentPage === 1;
        }
        if (this.nextPageBtn) {
            this.nextPageBtn.disabled = this.currentPage === this.totalPages;
        }
        
        // Mostrar paginación siempre que haya datos
        if (this.incidentsPagination) {
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
    showEmptyState(customMessage = null) {
        this.hideAllStates();
        
        if (this.incidentsEmpty) {
            this.incidentsEmpty.style.display = 'block';
            
            // Personalizar mensaje según el contexto
            const emptyTitle = this.incidentsEmpty.querySelector('h3');
            const emptyDescription = this.incidentsEmpty.querySelector('p');
            
            if (customMessage) {
                if (emptyTitle) emptyTitle.textContent = customMessage.title || 'No hay reportes registrados';
                if (emptyDescription) emptyDescription.textContent = customMessage.description || 'No se encontraron reportes con los criterios especificados.';
            } else {
                // Verificar si hay filtros activos
                const hasActiveFilters = this.hasActiveFilters();
                
                if (hasActiveFilters) {
                    if (emptyTitle) emptyTitle.textContent = 'No se encontraron reportes con los filtros aplicados';
                    if (emptyDescription) emptyDescription.textContent = 'Intenta ajustar o limpiar los filtros para ver más resultados.';
                } else {
                    if (emptyTitle) emptyTitle.textContent = 'No hay reportes registrados';
                    if (emptyDescription) emptyDescription.textContent = 'Actualmente no hay reportes disponibles en el sistema.';
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
        if (window.GlobalToast) {
            window.GlobalToast.show(message, type);
        } else {
            console.log(`Toast ${type}: ${message}`);
        }
    }
    
    /**
     * Formatea una fecha para mostrar
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
     */
    truncateText(text, maxLength) {
        if (!text || text.length <= maxLength) return text;
        return text.substring(0, maxLength) + '...';
    }
    
    /**
     * Métodos abstractos que deben ser implementados por las clases hijas
     */
    loadReports() {
        throw new Error('El método loadReports() debe ser implementado por la clase hija');
    }
    
    applyFilters() {
        throw new Error('El método applyFilters() debe ser implementado por la clase hija');
    }
    
    clearFilters() {
        throw new Error('El método clearFilters() debe ser implementado por la clase hija');
    }
    
    refreshData() {
        throw new Error('El método refreshData() debe ser implementado por la clase hija');
    }
    
    hasActiveFilters() {
        throw new Error('El método hasActiveFilters() debe ser implementado por la clase hija');
    }
    
    renderTable() {
        throw new Error('El método renderTable() debe ser implementado por la clase hija');
    }
}

// Hacer el controlador base disponible globalmente
window.BaseReportsController = BaseReportsController;
