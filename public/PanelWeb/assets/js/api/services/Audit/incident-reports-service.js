/**
 * Servicio para manejo de reportes de incidentes
 * Proporciona métodos para obtener reportes de incidentes con filtros avanzados
 */
class IncidentReportsService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.endpoints = {
            incidents: '/reports/incidents'
        };
    }

    /**
     * Obtiene un reporte paginado de incidentes con filtros avanzados
     * @param {Object} filters - Filtros para el reporte
     * @returns {Promise<Object>} Respuesta con datos de incidentes y paginación
     */
    async getIncidentReports(filters = {}) {
        try {
            const token = sessionStorage.getItem('authToken') || localStorage.getItem('authToken');
            if (!token) {
                throw new Error('Token de autenticación no encontrado');
            }

            let url = `${this.baseUrl}${this.endpoints.incidents}`;
            
            // Agregar filtros como parámetros de consulta según la documentación de la API
            const queryParams = new URLSearchParams();
            
            // Parámetros de paginación
            if (filters.page) queryParams.append('page', filters.page);
            if (filters.perPage) queryParams.append('perPage', filters.perPage);
            
            // Filtros específicos
            if (filters.userId) queryParams.append('userId', filters.userId);
            if (filters.travelId) queryParams.append('travelId', filters.travelId);
            if (filters.typeId) queryParams.append('typeId', filters.typeId);
            if (filters.active !== undefined && filters.active !== '') {
                queryParams.append('active', filters.active);
            }
            if (filters.comment) queryParams.append('comment', filters.comment);
            
            // Ordenamiento
            if (filters.sortBy) queryParams.append('sortBy', filters.sortBy);
            if (filters.sortDirection) queryParams.append('sortDirection', filters.sortDirection);
            
            if (queryParams.toString()) {
                url += `?${queryParams.toString()}`;
            }

            console.log('🔍 Obteniendo reporte de incidentes desde:', url);

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                if (response.status === 401) {
                    throw new Error('No autorizado. Token de autenticación inválido o expirado');
                }
                if (response.status === 403) {
                    throw new Error('No tiene permisos para acceder a los reportes de incidentes');
                }
                if (response.status === 404) {
                    throw new Error('Endpoint de reportes de incidentes no encontrado');
                }
                throw new Error(`Error HTTP: ${response.status} ${response.statusText}`);
            }

            const result = await response.json();
            
            console.log('✅ Reporte de incidentes obtenido:', result);
            return result;

        } catch (error) {
            console.error('❌ Error al obtener reporte de incidentes:', error);
            throw error;
        }
    }

    /**
     * Formatea la fecha para mostrar
     * @param {string} dateString - Fecha en formato ISO
     * @returns {string} Fecha formateada
     */
    formatDate(dateString) {
        if (!dateString) return '-';
        
        try {
            const date = new Date(dateString);
            return date.toLocaleDateString('es-ES', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (error) {
            console.warn('Error al formatear fecha:', error);
            return dateString;
        }
    }

    /**
     * Obtiene las opciones de ordenamiento disponibles
     * @returns {Array} Array de opciones de ordenamiento
     */
    getSortOptions() {
        return [
            { value: 'id', label: 'ID' },
            { value: 'userId', label: 'Usuario' },
            { value: 'travelId', label: 'Viaje' },
            { value: 'typeId', label: 'Tipo' },
            { value: 'active', label: 'Estado' }
        ];
    }

    /**
     * Obtiene las opciones de estado (activo/inactivo)
     * @returns {Array} Array de opciones de estado
     */
    getActiveOptions() {
        return [
            { value: '', label: 'Todos los estados' },
            { value: 'true', label: 'Activo' },
            { value: 'false', label: 'Inactivo' }
        ];
    }

    /**
     * Valida los filtros antes de enviar la petición
     * @param {Object} filters - Filtros a validar
     * @returns {Object} Filtros validados
     */
    validateFilters(filters) {
        const validated = { ...filters };
        
        // Validar paginación
        if (validated.page && (validated.page < 1 || !Number.isInteger(Number(validated.page)))) {
            validated.page = 1;
        }
        
        if (validated.perPage) {
            const perPage = Number(validated.perPage);
            if (perPage < 1 || perPage > 100 || !Number.isInteger(perPage)) {
                validated.perPage = 20;
            }
        }

        // Validar IDs (deben ser números enteros positivos)
        ['userId', 'travelId', 'typeId'].forEach(field => {
            if (validated[field] && (!Number.isInteger(Number(validated[field])) || Number(validated[field]) < 1)) {
                delete validated[field];
            }
        });

        // Validar sortBy
        const validSortFields = ['id', 'userId', 'travelId', 'typeId', 'active'];
        if (validated.sortBy && !validSortFields.includes(validated.sortBy)) {
            validated.sortBy = 'id';
        }

        // Validar sortDirection
        if (validated.sortDirection && !['ASC', 'DESC'].includes(validated.sortDirection)) {
            validated.sortDirection = 'DESC';
        }

        return validated;
    }
}

// Hacer el servicio disponible globalmente
window.IncidentReportsService = IncidentReportsService;

// Crear instancia global
if (typeof window !== 'undefined') {
    window.incidentReportsService = new IncidentReportsService();
}
