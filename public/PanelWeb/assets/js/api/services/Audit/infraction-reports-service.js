/**
 * Servicio para obtener reportes de infracciones
 * Maneja la comunicación con la API para reportes de infracciones
 */

class InfractionReportsService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.endpoints = {
            infractions: '/reports/infractions'
        };
        console.log('🚔 InfractionReportsService inicializado');
    }

    /**
     * Obtiene un reporte paginado de infracciones con filtros
     * @param {Object} filters - Filtros para aplicar al reporte
     * @param {number} filters.page - Página actual (default: 1)
     * @param {number} filters.perPage - Elementos por página (default: 20)
     * @param {number} filters.userId - ID del usuario
     * @param {number} filters.severityId - ID de severidad
     * @param {number} filters.statusId - ID del estado
     * @param {string} filters.dateFrom - Fecha desde (YYYY-MM-DD)
     * @param {string} filters.dateTo - Fecha hasta (YYYY-MM-DD)
     * @param {string} filters.description - Descripción a buscar
     * @param {string} filters.sortBy - Campo por el cual ordenar (id, userId, severityId, statusId, date)
     * @param {string} filters.sortDirection - Dirección del ordenamiento (ASC, DESC)
     * @returns {Promise} Promesa con los datos del reporte
     */
    async getInfractionReports(filters = {}) {
        try {
            const token = sessionStorage.getItem('authToken') || localStorage.getItem('authToken');
            if (!token) {
                throw new Error('Token de autenticación no encontrado');
            }

            // Construir parámetros de consulta
            const params = new URLSearchParams();
            
            // Parámetros de paginación
            params.append('page', filters.page || 1);
            params.append('perPage', filters.perPage || 20);
            
            // Filtros opcionales
            if (filters.userId) {
                params.append('userId', filters.userId);
            }
            
            if (filters.severityId) {
                params.append('severityId', filters.severityId);
            }
            
            if (filters.statusId) {
                params.append('statusId', filters.statusId);
            }
            
            if (filters.dateFrom) {
                params.append('dateFrom', filters.dateFrom);
            }
            
            if (filters.dateTo) {
                params.append('dateTo', filters.dateTo);
            }
            
            if (filters.description && filters.description.trim()) {
                params.append('description', filters.description.trim());
            }
            
            // Parámetros de ordenamiento
            params.append('sortBy', filters.sortBy || 'id');
            params.append('sortDirection', filters.sortDirection || 'DESC');

            const url = `${this.baseUrl}${this.endpoints.infractions}?${params.toString()}`;
            
            console.log('🔍 Obteniendo reporte de infracciones con filtros:', {
                url,
                filters
            });
            
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });
            
            console.log('📡 Respuesta del servidor:', response.status, response.statusText);
            
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || `Error ${response.status}: ${response.statusText}`);
            }
            
            const result = await response.json();
            
            console.log('✅ Reporte de infracciones obtenido exitosamente:', {
                totalItems: result.data?.pagination?.total_items || 0,
                currentPage: result.data?.pagination?.current_page || 1,
                itemsInPage: result.data?.data?.length || 0
            });
            
            return result;
            
        } catch (error) {
            console.error('❌ Error al obtener reporte de infracciones:', error);
            throw error;
        }
    }

    /**
     * Obtiene información resumida de infracciones para estadísticas
     * @returns {Promise} Promesa con estadísticas básicas
     */
    async getInfractionStats() {
        try {
            console.log('📊 Obteniendo estadísticas de infracciones...');
            
            // Obtener una muestra pequeña para estadísticas básicas
            const result = await this.getInfractionReports({
                page: 1,
                perPage: 1
            });
            
            return {
                totalInfractions: result.data?.pagination?.total_items || 0,
                success: true
            };
            
        } catch (error) {
            console.error('❌ Error al obtener estadísticas de infracciones:', error);
            return {
                totalInfractions: 0,
                success: false,
                error: error.message
            };
        }
    }

    /**
     * Valida los filtros antes de enviar la solicitud
     * @param {Object} filters - Filtros a validar
     * @returns {Object} Filtros validados
     */
    validateFilters(filters) {
        const validated = { ...filters };
        
        // Validar página
        if (validated.page && (validated.page < 1 || !Number.isInteger(Number(validated.page)))) {
            validated.page = 1;
        }
        
        // Validar elementos por página
        if (validated.perPage) {
            const perPage = Number(validated.perPage);
            if (perPage < 1 || perPage > 100 || !Number.isInteger(perPage)) {
                validated.perPage = 20;
            }
        }
        
        // Validar IDs (deben ser números positivos)
        ['userId', 'severityId', 'statusId'].forEach(field => {
            if (validated[field]) {
                const value = Number(validated[field]);
                if (value < 1 || !Number.isInteger(value)) {
                    delete validated[field];
                }
            }
        });
        
        // Validar fechas
        if (validated.dateFrom && !this.isValidDate(validated.dateFrom)) {
            delete validated.dateFrom;
        }
        
        if (validated.dateTo && !this.isValidDate(validated.dateTo)) {
            delete validated.dateTo;
        }
        
        // Validar que dateFrom sea menor que dateTo
        if (validated.dateFrom && validated.dateTo && validated.dateFrom > validated.dateTo) {
            delete validated.dateTo;
        }
        
        // Validar sortBy
        const validSortFields = ['id', 'userId', 'severityId', 'statusId', 'date'];
        if (validated.sortBy && !validSortFields.includes(validated.sortBy)) {
            validated.sortBy = 'id';
        }
        
        // Validar sortDirection
        if (validated.sortDirection && !['ASC', 'DESC'].includes(validated.sortDirection)) {
            validated.sortDirection = 'DESC';
        }
        
        return validated;
    }

    /**
     * Valida si una fecha está en formato válido
     * @param {string} dateString - Fecha en formato string
     * @returns {boolean} True si es válida
     */
    isValidDate(dateString) {
        const date = new Date(dateString);
        return date instanceof Date && !isNaN(date);
    }

    /**
     * Formatea los datos de infracciones para mostrar en la tabla
     * @param {Array} infractions - Array de infracciones
     * @returns {Array} Array formateado para la tabla
     */
    formatInfractionsForTable(infractions) {
        if (!Array.isArray(infractions)) {
            return [];
        }

        return infractions.map(infraction => ({
            id: infraction.id || 'N/A',
            userId: infraction.userId || 'N/A',
            userName: infraction.userName || 'Sin nombre',
            severityId: infraction.severityId || 'N/A',
            severityName: infraction.severityName || 'Sin severidad',
            statusId: infraction.statusId || 'N/A',
            statusName: infraction.statusName || 'Sin estado',
            date: this.formatDate(infraction.date) || 'N/A',
            description: infraction.description || 'Sin descripción'
        }));
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
}

// Hacer disponible globalmente
window.InfractionReportsService = InfractionReportsService;

console.log('✅ InfractionReportsService cargado correctamente');
