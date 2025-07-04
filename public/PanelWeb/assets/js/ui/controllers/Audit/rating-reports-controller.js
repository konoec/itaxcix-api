/**
 * Controlador para reportes de calificaciones
 * Extiende BaseReportsController para manejar reportes de calificaciones específicamente
 */
class RatingReportsController extends BaseReportsController {
    constructor() {
        super();
        console.log('⭐ Inicializando RatingReportsController...');
        
        // Filtros específicos para calificaciones - solo valores por defecto
        this.filters = {
            sortBy: 'id',
            sortDirection: 'DESC'
        };
        
        // Servicio de datos
        this.service = window.ratingReportsService;
        
        // Elementos específicos del DOM
        this.initializeRatingElements();
        
        // Event listeners específicos
        this.initializeRatingEventListeners();
    }
    
    /**
     * Inicializa los elementos específicos para calificaciones
     */
    initializeRatingElements() {
        // Sección de filtros específica
        this.filtersSection = document.getElementById('ratings-filters');
        this.headers = document.getElementById('ratings-headers');
        
        // Filtros específicos
        this.filterRaterId = document.getElementById('filter-rater-id');
        this.filterRatedId = document.getElementById('filter-rated-id');
        this.filterTravelId = document.getElementById('filter-travel-id');
        this.filterMinScore = document.getElementById('filter-min-score');
        this.filterMaxScore = document.getElementById('filter-max-score');
        this.filterComment = document.getElementById('filter-comment');
        this.ratingSortBy = document.getElementById('rating-sort-by');
        this.ratingSortDirection = document.getElementById('rating-sort-direction');
        
        // Botones de acción específicos
        this.applyFiltersBtn = document.getElementById('apply-filters-btn-ratings');
        this.clearFiltersBtn = document.getElementById('clear-filters-btn-ratings');
        this.refreshDataBtn = document.getElementById('refresh-data-btn-ratings');
        this.exportBtn = document.getElementById('export-btn-ratings');
        
        console.log('✅ Elementos específicos de calificaciones inicializados');
    }
    
    /**
     * Inicializa los event listeners específicos para calificaciones
     */
    initializeRatingEventListeners() {
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
        
        if (this.exportBtn) {
            this.exportBtn.addEventListener('click', () => this.exportReports());
        }
        
        // Enter en campos de texto principales
        [this.filterRaterId, this.filterRatedId, this.filterTravelId, this.filterMinScore, this.filterMaxScore, this.filterComment].forEach(input => {
            if (input) {
                input.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        this.applyFilters();
                    }
                });
            }
        });
        
        // Validación en tiempo real para puntajes
        if (this.filterMinScore) {
            this.filterMinScore.addEventListener('input', (e) => {
                this.validateScoreRange();
            });
        }
        
        if (this.filterMaxScore) {
            this.filterMaxScore.addEventListener('input', (e) => {
                this.validateScoreRange();
            });
        }
        
        console.log('✅ Event listeners específicos de calificaciones inicializados');
    }
    
    /**
     * Carga los reportes de calificaciones
     */
    async loadReports() {
        if (!this.service) {
            console.error('❌ Servicio de calificaciones no disponible');
            this.showErrorState('Servicio de calificaciones no disponible');
            return;
        }

        try {
            this.showLoadingState();
            
            const requestFilters = {
                page: this.currentPage,
                perPage: this.perPage,
                ...this.filters
            };
            
            console.log('🔄 Cargando reportes de calificaciones con filtros:', requestFilters);
            
            const response = await this.service.getRatingReports(requestFilters);
            console.log('🔍 Respuesta completa de la API:', response);
            
            if (response && response.success && response.data && response.data.data) {
                // La API devuelve data.data como array de calificaciones
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
                    calificaciones: this.currentData.length,
                    totalResultados: this.totalResults,
                    paginaActual: this.currentPage,
                    totalPaginas: this.totalPages
                });
                
                this.renderTable();
                this.updatePagination();
                this.showTableState();
                
                console.log('✅ Reportes de calificaciones cargados exitosamente');
            } else {
                console.error('❌ Estructura de respuesta inválida:', response);
                throw new Error('Respuesta inválida del servidor');
            }
            
        } catch (error) {
            console.error('❌ Error al cargar reportes de calificaciones:', error);
            this.showErrorState(error.message || 'Error al cargar los reportes de calificaciones');
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
        if (this.filterRaterId) this.filterRaterId.value = '';
        if (this.filterRatedId) this.filterRatedId.value = '';
        if (this.filterTravelId) this.filterTravelId.value = '';
        if (this.filterMinScore) this.filterMinScore.value = '';
        if (this.filterMaxScore) this.filterMaxScore.value = '';
        if (this.filterComment) this.filterComment.value = '';
        if (this.ratingSortBy) this.ratingSortBy.value = 'id';
        if (this.ratingSortDirection) this.ratingSortDirection.value = 'DESC';
        if (this.pageSizeSelect) this.pageSizeSelect.value = '20';
        
        // Resetear filtros - solo mantener valores por defecto
        this.filters = {
            sortBy: 'id',
            sortDirection: 'DESC'
        };
        
        this.perPage = 20;
        this.currentPage = 1;
        
        // Limpiar validaciones visuales
        this.clearValidationFeedback();
        
        this.loadReports();
    }
    
    /**
     * Refresca los datos
     */
    refreshData() {
        this.loadReports();
    }
    
    /**
     * Exporta los reportes
     */
    async exportReports() {
        try {
            console.log('📤 Iniciando exportación de reportes de calificaciones...');
            
            // Usar los filtros actuales para la exportación
            const exportFilters = { ...this.filters };
            delete exportFilters.sortBy;
            delete exportFilters.sortDirection;
            
            const blob = await this.service.exportRatingReports(exportFilters);
            
            // Crear enlace de descarga
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            a.download = `reporte_calificaciones_${new Date().toISOString().split('T')[0]}.xlsx`;
            
            document.body.appendChild(a);
            a.click();
            
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
            
            console.log('✅ Exportación completada exitosamente');
            
        } catch (error) {
            console.error('❌ Error al exportar reportes:', error);
            this.showErrorState('Error al exportar los reportes: ' + error.message);
        }
    }
    
    /**
     * Actualiza los filtros desde el formulario
     */
    updateFiltersFromForm() {
        // Crear objeto de filtros solo con valores válidos
        const formFilters = {};
        
        // Filtros numéricos (IDs) - validar que sean números válidos
        const raterId = this.filterRaterId?.value;
        if (raterId && raterId !== '' && !isNaN(raterId) && parseInt(raterId) > 0) {
            formFilters.raterId = parseInt(raterId);
        }
        
        const ratedId = this.filterRatedId?.value;
        if (ratedId && ratedId !== '' && !isNaN(ratedId) && parseInt(ratedId) > 0) {
            formFilters.ratedId = parseInt(ratedId);
        }
        
        const travelId = this.filterTravelId?.value;
        if (travelId && travelId !== '' && !isNaN(travelId) && parseInt(travelId) > 0) {
            formFilters.travelId = parseInt(travelId);
        }
        
        // Filtros de puntaje - validar rango 1-5
        const minScore = this.filterMinScore?.value;
        if (minScore && minScore !== '' && !isNaN(minScore)) {
            const score = parseInt(minScore);
            if (score >= 1 && score <= 5) {
                formFilters.minScore = score;
            }
        }
        
        const maxScore = this.filterMaxScore?.value;
        if (maxScore && maxScore !== '' && !isNaN(maxScore)) {
            const score = parseInt(maxScore);
            if (score >= 1 && score <= 5) {
                formFilters.maxScore = score;
            }
        }
        
        // Filtro de comentario - validar longitud mínima
        const comment = this.filterComment?.value?.trim();
        if (comment && comment.length >= 3) {
            formFilters.comment = comment;
        }
        
        // Ordenamiento - validar valores permitidos
        const validSortFields = ['id', 'raterId', 'ratedId', 'travelId', 'score'];
        const sortBy = this.ratingSortBy?.value;
        if (sortBy && validSortFields.includes(sortBy)) {
            formFilters.sortBy = sortBy;
        } else {
            formFilters.sortBy = 'id'; // Valor por defecto
        }
        
        const sortDirection = this.ratingSortDirection?.value;
        if (sortDirection && (sortDirection === 'ASC' || sortDirection === 'DESC')) {
            formFilters.sortDirection = sortDirection;
        } else {
            formFilters.sortDirection = 'DESC'; // Valor por defecto
        }
        
        // Paginación - validar rangos
        const perPageValue = parseInt(this.pageSizeSelect?.value || '20');
        if (perPageValue >= 1 && perPageValue <= 100) {
            this.perPage = perPageValue;
        } else {
            this.perPage = 20; // Valor por defecto
        }
        
        this.filters = formFilters;
        
        console.log('📝 Filtros de calificaciones actualizados:', this.filters);
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

        console.log('🎨 Renderizando tabla de calificaciones con', this.currentData.length, 'registros');

        // Limpiar tabla
        this.incidentsTableBody.innerHTML = '';

        if (this.currentData.length === 0) {
            this.showEmptyState();
            return;
        }

        // Generar filas
        this.currentData.forEach(rating => {
            const row = this.createTableRow(rating);
            this.incidentsTableBody.appendChild(row);
        });

        console.log('✅ Tabla de calificaciones renderizada');
    }

    /**
     * Crea una fila de tabla para una calificación
     */
    createTableRow(rating) {
        const row = document.createElement('tr');
        row.className = 'data-row';

        // Crear badge de puntaje con color según valor
        const scoreBadge = this.createScoreBadge(rating.score);
        
        // Truncar comentario si es muy largo
        const comment = rating.comment || 'Sin comentario';
        const truncatedComment = comment.length > 50 ? comment.substring(0, 50) + '...' : comment;

        row.innerHTML = `
            <td>${rating.id || ''}</td>
            <td>${rating.raterId || ''}</td>
            <td><strong>${rating.raterName || 'N/A'}</strong></td>
            <td>${rating.ratedId || ''}</td>
            <td><strong>${rating.ratedName || 'N/A'}</strong></td>
            <td>${rating.travelId || ''}</td>
            <td>${scoreBadge}</td>
            <td title="${comment}">${truncatedComment}</td>
        `;

        return row;
    }
    
    /**
     * Crea un badge de puntaje con color apropiado
     */
    createScoreBadge(score) {
        let badgeClass = 'badge';
        let badgeColor = '';
        
        if (score >= 4.5) {
            badgeColor = 'badge-success';
        } else if (score >= 3.5) {
            badgeColor = 'badge-warning';
        } else if (score >= 2.5) {
            badgeColor = 'badge-secondary';
        } else {
            badgeColor = 'badge-danger';
        }
        
        return `<span class="${badgeClass} ${badgeColor}">${score || 0} ⭐</span>`;
    }
    
    /**
     * Verifica si hay filtros activos
     */
    hasActiveFilters() {
        return Object.keys(this.filters).some(key => {
            // Excluir los parámetros de ordenamiento por defecto
            if (key === 'sortBy' && this.filters[key] === 'id') return false;
            if (key === 'sortDirection' && this.filters[key] === 'DESC') return false;
            
            // Verificar si hay algún filtro con valor
            const value = this.filters[key];
            return value !== undefined && value !== null && value !== '';
        });
    }
    
    /**
     * Muestra los filtros y headers específicos para calificaciones
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
     * Oculta los filtros y headers específicos para calificaciones
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
        
        // Validar IDs (deben ser números positivos)
        const raterId = this.filterRaterId?.value;
        if (raterId && (isNaN(raterId) || parseInt(raterId) <= 0)) {
            errors.push('El ID del calificador debe ser un número positivo');
        }
        
        const ratedId = this.filterRatedId?.value;
        if (ratedId && (isNaN(ratedId) || parseInt(ratedId) <= 0)) {
            errors.push('El ID del calificado debe ser un número positivo');
        }
        
        const travelId = this.filterTravelId?.value;
        if (travelId && (isNaN(travelId) || parseInt(travelId) <= 0)) {
            errors.push('El ID del viaje debe ser un número positivo');
        }
        
        // Validar puntajes (deben estar entre 1 y 5)
        const minScore = this.filterMinScore?.value;
        const maxScore = this.filterMaxScore?.value;
        
        if (minScore && (isNaN(minScore) || parseInt(minScore) < 1 || parseInt(minScore) > 5)) {
            errors.push('El puntaje mínimo debe estar entre 1 y 5');
        }
        
        if (maxScore && (isNaN(maxScore) || parseInt(maxScore) < 1 || parseInt(maxScore) > 5)) {
            errors.push('El puntaje máximo debe estar entre 1 y 5');
        }
        
        if (minScore && maxScore && parseInt(minScore) > parseInt(maxScore)) {
            errors.push('El puntaje mínimo no puede ser mayor al puntaje máximo');
        }
        
        // Validar comentario (longitud mínima)
        const comment = this.filterComment?.value?.trim();
        if (comment && comment.length < 3) {
            errors.push('El comentario debe tener al menos 3 caracteres');
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
        let errorContainer = document.getElementById('validation-errors-ratings');
        if (!errorContainer) {
            errorContainer = document.createElement('div');
            errorContainer.id = 'validation-errors-ratings';
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
     * Valida el rango de puntajes en tiempo real
     */
    validateScoreRange() {
        const minScore = parseInt(this.filterMinScore?.value || 0);
        const maxScore = parseInt(this.filterMaxScore?.value || 0);
        
        // Limpiar clases de validación previas
        if (this.filterMinScore) {
            this.filterMinScore.classList.remove('is-valid', 'is-invalid');
        }
        if (this.filterMaxScore) {
            this.filterMaxScore.classList.remove('is-valid', 'is-invalid');
        }
        
        // Validar rango
        if (minScore && maxScore) {
            if (minScore > maxScore) {
                this.filterMinScore?.classList.add('is-invalid');
                this.filterMaxScore?.classList.add('is-invalid');
            } else {
                this.filterMinScore?.classList.add('is-valid');
                this.filterMaxScore?.classList.add('is-valid');
            }
        }
    }
    
    /**
     * Limpia el feedback de validación visual
     */
    clearValidationFeedback() {
        // Limpiar clases de validación
        [this.filterMinScore, this.filterMaxScore].forEach(input => {
            if (input) {
                input.classList.remove('is-valid', 'is-invalid');
            }
        });
        
        // Ocultar contenedor de errores si existe
        const errorContainer = document.getElementById('validation-errors-ratings');
        if (errorContainer) {
            errorContainer.style.display = 'none';
        }
    }
}

// Hacer el controlador disponible globalmente
window.RatingReportsController = RatingReportsController;
