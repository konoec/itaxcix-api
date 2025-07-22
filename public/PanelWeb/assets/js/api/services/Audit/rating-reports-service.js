/**
 * Servicio para el manejo de reportes de calificaciones
 * Proporciona métodos para obtener reportes de calificaciones con filtros y paginación
 */
class RatingReportsService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.endpoints = {
            ratings: '/reports/ratings'
        };
        console.log('⭐ RatingReportsService inicializado');
    }

    /**
     * Obtiene un reporte paginado de calificaciones con filtros avanzados
     * @param {Object} params - Parámetros de filtrado y paginación
     * @param {number} params.page - Número de página (default: 1)
     * @param {number} params.perPage - Elementos por página (default: 20)
     * @param {number} params.raterId - ID del calificador
     * @param {number} params.ratedId - ID del calificado
     * @param {number} params.travelId - ID del viaje
     * @param {number} params.minScore - Puntaje mínimo
     * @param {number} params.maxScore - Puntaje máximo
     * @param {string} params.comment - Comentario a buscar
     * @param {string} params.sortBy - Campo para ordenar (default: 'id')
     * @param {string} params.sortDirection - Dirección de ordenamiento (ASC|DESC, default: 'DESC')
     * @returns {Promise<Object>} Respuesta con datos paginados de calificaciones
     */
    async getRatingReports(params = {}) {
        try {
            // Verificar autenticación
            const token = sessionStorage.getItem('authToken') || localStorage.getItem('authToken');
            if (!token) {
                throw new Error('Token de autenticación no encontrado');
            }

            console.log('🔄 Obteniendo reportes de calificaciones con parámetros:', params);

            // Construir parámetros de consulta de manera más robusta
            const queryParams = new URLSearchParams();

            // Parámetros de paginación - siempre incluir con valores por defecto
            queryParams.append('page', params.page || 1);
            queryParams.append('perPage', params.perPage || 20);

            // Filtros de calificación - solo agregar si tienen valores válidos
            if (params.raterId && Number.isInteger(params.raterId)) {
                queryParams.append('raterId', params.raterId.toString());
            }
            
            if (params.ratedId && Number.isInteger(params.ratedId)) {
                queryParams.append('ratedId', params.ratedId.toString());
            }
            
            if (params.travelId && Number.isInteger(params.travelId)) {
                queryParams.append('travelId', params.travelId.toString());
            }
            
            if (params.minScore && Number.isInteger(params.minScore)) {
                queryParams.append('minScore', params.minScore.toString());
            }
            
            if (params.maxScore && Number.isInteger(params.maxScore)) {
                queryParams.append('maxScore', params.maxScore.toString());
            }
            
            if (params.comment && typeof params.comment === 'string' && params.comment.trim().length > 0) {
                queryParams.append('comment', params.comment.trim());
            }

            // Parámetros de ordenamiento - siempre incluir con valores por defecto
            const validSortFields = ['id', 'raterId', 'ratedId', 'travelId', 'score'];
            const sortBy = validSortFields.includes(params.sortBy) ? params.sortBy : 'id';
            queryParams.append('sortBy', sortBy);
            
            queryParams.append('sortDirection', (params.sortDirection === 'ASC') ? 'ASC' : 'DESC');

            // Construir URL completa
            const url = `${this.baseUrl}${this.endpoints.ratings}?${queryParams.toString()}`;
            console.log('📡 URL de solicitud:', url);

            // Realizar petición
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                let errorMessage = `Error HTTP: ${response.status}`;
                let errorDetails = null;
                
                try {
                    const errorData = await response.json();
                    console.error('❌ Error del servidor completo:', errorData);
                    
                    errorDetails = errorData;
                    errorMessage += ` - ${errorData.message || errorData.error || response.statusText}`;
                    
                    // Si hay detalles específicos de validación, incluirlos
                    if (errorData.data && errorData.data.errors) {
                        console.error('❌ Errores de validación:', errorData.data.errors);
                        errorMessage += `. Errores de validación: ${JSON.stringify(errorData.data.errors)}`;
                    }
                    
                } catch (parseError) {
                    console.error('❌ Error al parsear respuesta de error:', parseError);
                    errorMessage += ` - ${response.statusText}`;
                }
                
                // Mensajes específicos por código de error
                if (response.status === 400) {
                    console.error('❌ Error 400 - Parámetros enviados:', params);
                    console.error('❌ Error 400 - URL generada:', url);
                    errorMessage = `Petición inválida (400). Verifique los parámetros de filtrado. ${errorMessage}`;
                } else if (response.status === 401) {
                    throw new Error('No autorizado. Token de autenticación inválido o expirado');
                } else if (response.status === 403) {
                    throw new Error('No tiene permisos para acceder a los reportes de calificaciones');
                } else if (response.status === 404) {
                    throw new Error('Endpoint de reportes de calificaciones no encontrado');
                }
                
                const error = new Error(errorMessage);
                error.status = response.status;
                error.details = errorDetails;
                throw error;
            }

            const data = await response.json();
            console.log('✅ Respuesta del servidor:', data);

            // Devolver la respuesta directamente de la API
            return data;

        } catch (error) {
            console.error('❌ Error al obtener reportes de calificaciones:', error);
            throw error;
        }
    }

    /**
     * Exporta los reportes de calificaciones a CSV usando el endpoint paginado y descarga en frontend
     * @param {Object} params - Parámetros de filtrado
     * @returns {Promise<void>} Descarga el archivo CSV
     */
    async exportRatingReports(params = {}) {
        try {
            // Obtener todos los datos (puedes ajustar para paginación si hay muchos)
            const allParams = { ...params, page: 1, perPage: 100 };
            const response = await this.getRatingReports(allParams);
            const rows = response?.data?.data || [];
            if (!rows.length) throw new Error('No hay datos para exportar');

            // Definir columnas
            const columns = ['id', 'raterId', 'raterName', 'ratedId', 'ratedName', 'travelId', 'score', 'comment'];
            const csv = [columns.join(',')].concat(
                rows.map(row => columns.map(col => `"${(row[col] ?? '').toString().replace(/"/g, '""')}"`).join(','))
            ).join('\r\n');

            // Descargar CSV
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'calificaciones.csv';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
            console.log('✅ Exportación CSV completada');
        } catch (error) {
            console.error('❌ Error al exportar reportes de calificaciones:', error);
            throw error;
        }
    }
}

// Hacer el servicio disponible globalmente
window.ratingReportsService = new RatingReportsService();
window.RatingReportsService = RatingReportsService;

console.log('✅ RatingReportsService cargado y disponible globalmente');
