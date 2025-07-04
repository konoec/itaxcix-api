/**
 * Servicio para reportes de viajes
 * Maneja las operaciones de consulta y filtrado de reportes de viajes administrativos
 */
class TravelReportsService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.endpoint = '/reports/travels';
        console.log('🚗 TravelReportsService inicializado');
    }

    /**
     * Obtiene reportes de viajes con filtros
     * @param {Object} filters - Filtros de búsqueda
     * @returns {Promise<Object>} Respuesta del servidor
     */
    async getTravelReports(filters = {}) {
        try {
            console.log('🔄 Solicitando reportes de viajes con filtros:', filters);
            
            // Construir parámetros de consulta
            const params = new URLSearchParams();
            
            // Paginación
            if (filters.page) params.append('page', filters.page);
            if (filters.perPage) params.append('perPage', filters.perPage);
            
            // Filtros de fechas
            if (filters.startDate) params.append('startDate', filters.startDate);
            if (filters.endDate) params.append('endDate', filters.endDate);
            
            // Filtros de IDs
            if (filters.citizenId) params.append('citizenId', filters.citizenId);
            if (filters.driverId) params.append('driverId', filters.driverId);
            if (filters.statusId) params.append('statusId', filters.statusId);
            
            // Filtros de ubicación
            if (filters.origin) params.append('origin', filters.origin);
            if (filters.destination) params.append('destination', filters.destination);
            
            // Ordenamiento
            if (filters.sortBy) params.append('sortBy', filters.sortBy);
            if (filters.sortDirection) params.append('sortDirection', filters.sortDirection);
            
            const url = `${this.baseUrl}${this.endpoint}?${params.toString()}`;
            console.log('📡 URL de consulta:', url);
            
            // Obtener token de autenticación
            const token = sessionStorage.getItem('authToken') || localStorage.getItem('authToken');
            if (!token) {
                throw new Error('Token de autenticación no encontrado');
            }
            
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });
            
            if (!response.ok) {
                const errorText = await response.text();
                console.error('❌ Error en respuesta del servidor:', response.status, errorText);
                throw new Error(`Error del servidor: ${response.status} - ${errorText}`);
            }
            
            const data = await response.json();
            console.log('✅ Reportes de viajes obtenidos exitosamente:', data);
            
            return data;
            
        } catch (error) {
            console.error('❌ Error al obtener reporte de viajes:', error);
            throw error;
        }
    }

    /**
     * Obtiene estadísticas básicas de viajes
     * @returns {Promise<Object>} Estadísticas de viajes
     */
    async getTravelStats() {
        try {
            // Por ahora usando el endpoint principal sin filtros para obtener totales
            const data = await this.getTravelReports({ perPage: 1 });
            
            if (data && data.data && data.data.pagination) {
                return {
                    total: data.data.pagination.total_items || 0,
                    pages: data.data.pagination.total_pages || 0
                };
            }
            
            return { total: 0, pages: 0 };
            
        } catch (error) {
            console.error('❌ Error al obtener estadísticas de viajes:', error);
            return { total: 0, pages: 0 };
        }
    }
}

// Crear instancia global del servicio
window.travelReportsService = new TravelReportsService();

console.log('✅ TravelReportsService registrado globalmente');
