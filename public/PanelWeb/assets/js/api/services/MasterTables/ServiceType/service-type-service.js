/**
 * Servicio para la gestión de tipos de servicio
 */
class ServiceTypeService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.endpoint = '/admin/service-types';
        
        // Cache para optimizar rendimiento
        this.serviceTypesCache = new Map();
        this.cacheExpiry = 5 * 60 * 1000; // 5 minutos
        
        console.log('🚕 ServiceTypeService inicializado con base URL:', this.baseUrl);
    }

    /**
     * Obtiene la lista de tipos de servicio con filtros y paginación
     * @param {Object} params - Parámetros de búsqueda y filtros
     * @param {number} params.page - Número de página (default: 1)
     * @param {number} params.perPage - Elementos por página (default: 15, max: 100)
     * @param {string} params.search - Búsqueda global en nombre
     * @param {string} params.name - Filtro por nombre del tipo de servicio
     * @param {boolean} params.active - Filtro por estado activo
     * @param {string} params.sortBy - Campo de ordenamiento (id, name, active)
     * @param {string} params.sortDirection - Dirección de ordenamiento (ASC, DESC)
     * @returns {Promise<Object>} - Respuesta con lista paginada de tipos de servicio
     */
    async getServiceTypes(params = {}) {
        console.log('🚕 Obteniendo lista de tipos de servicio...', params);

        try {
            const token = sessionStorage.getItem("authToken");
            if (!token) {
                throw new Error('No hay token de autenticación');
            }

            // Construir parámetros de consulta
            const queryParams = this.buildQueryParams(params);
            const cacheKey = `service_types_${queryParams}`;

            // Verificar cache
            if (this.serviceTypesCache.has(cacheKey)) {
                const cached = this.serviceTypesCache.get(cacheKey);
                if (Date.now() - cached.timestamp < this.cacheExpiry) {
                    console.log('🚕 Usando datos de tipos de servicio desde cache');
                    return cached.data;
                }
            }

            // Realizar petición HTTP
            const url = `${this.baseUrl}${this.endpoint}?${queryParams}`;
            console.log('🌐 Realizando petición a:', url);

            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 segundos

            try {
                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    signal: controller.signal
                });

                clearTimeout(timeoutId);

                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(`HTTP ${response.status}: ${errorData.message || 'Error al obtener tipos de servicio'}`);
                }

                const result = await response.json();
                console.log('✅ Tipos de servicio obtenidos exitosamente:', result);

                // Validar estructura de respuesta
                if (!result.success) {
                    throw new Error(result.message || 'La API devolvió success: false');
                }

                if (!result.data || !Array.isArray(result.data.data)) {
                    console.warn('⚠️ Estructura de datos inesperada:', result);
                    // Crear estructura por defecto
                    result.data = {
                        data: [],
                        pagination: {
                            current_page: 1,
                            per_page: 15,
                            total_items: 0,
                            total_pages: 1,
                            has_next_page: false,
                            has_previous_page: false
                        }
                    };
                }

                // Guardar en cache
                this.serviceTypesCache.set(cacheKey, {
                    data: result,
                    timestamp: Date.now()
                });

                return result;

            } catch (fetchError) {
                clearTimeout(timeoutId);
                
                if (fetchError.name === 'AbortError') {
                    throw new Error('Timeout: La petición tardó demasiado');
                }
                throw fetchError;
            }

        } catch (error) {
            console.error('❌ Error al obtener tipos de servicio:', error);
            throw error;
        }
    }

    /**
     * Construye los parámetros de consulta para la URL
     * @param {Object} params - Parámetros de entrada
     * @returns {string} - Query string para la URL
     */
    buildQueryParams(params) {
        const query = new URLSearchParams();

        // Paginación
        query.append('page', params.page || 1);
        query.append('perPage', Math.min(params.perPage || 15, 100));

        // Filtros
        if (params.search) {
            query.append('search', params.search.trim());
        }
        
        if (params.name) {
            query.append('name', params.name.trim());
        }

        if (params.active !== undefined && params.active !== 'all') {
            query.append('active', params.active === true || params.active === 'true');
        }

        // Ordenamiento
        if (params.sortBy) {
            query.append('sortBy', params.sortBy);
        } else {
            query.append('sortBy', 'name'); // Default sortBy
        }

        if (params.sortDirection) {
            query.append('sortDirection', params.sortDirection);
        } else {
            query.append('sortDirection', 'ASC'); // Default sortDirection
        }

        return query.toString();
    }

    /**
     * Limpia la cache de tipos de servicio
     */
    clearCache() {
        this.serviceTypesCache.clear();
        console.log('🧹 Cache de tipos de servicio limpiada');
    }

    /**
     * Limpia cache específica por parámetros
     * @param {Object} params - Parámetros específicos a limpiar
     */
    clearSpecificCache(params = {}) {
        const queryParams = this.buildQueryParams(params);
        const cacheKey = `service_types_${queryParams}`;
        
        if (this.serviceTypesCache.has(cacheKey)) {
            this.serviceTypesCache.delete(cacheKey);
            console.log('🧹 Cache específica limpiada:', cacheKey);
        }
    }

    /**
     * Obtiene estadísticas de tipos de servicio
     * @returns {Promise<Object>} - Estadísticas básicas
     */
    async getServiceTypesStats() {
        try {
            console.log('📊 Obteniendo estadísticas de tipos de servicio...');
            
            // Obtener datos sin filtros para estadísticas
            const response = await this.getServiceTypes({ 
                page: 1, 
                perPage: 100, // Obtener más datos para estadísticas
                sortBy: 'id',
                sortDirection: 'ASC'
            });

            if (response.success && response.data) {
                const data = response.data.data || [];
                const pagination = response.data.pagination || {};

                const stats = {
                    total: pagination.total_items || data.length,
                    active: data.filter(item => item.active === true).length,
                    inactive: data.filter(item => item.active === false).length,
                    growth: 0 // Placeholder - requeriría datos históricos
                };

                console.log('📊 Estadísticas calculadas:', stats);
                return stats;
            }

            throw new Error('Respuesta inválida del servidor');

        } catch (error) {
            console.error('❌ Error al obtener estadísticas:', error);
            
            // Retornar estadísticas por defecto en caso de error
            return {
                total: 0,
                active: 0,
                inactive: 0,
                growth: 0
            };
        }
    }

    /**
     * Simula exportación a Excel (placeholder)
     * @param {Object} filters - Filtros aplicados
     * @returns {Promise<boolean>} - Éxito de la operación
     */
    async exportToExcel(filters = {}) {
        try {
            console.log('📊 Iniciando exportación a Excel...', filters);
            
            // Simular delay de exportación
            await new Promise(resolve => setTimeout(resolve, 2000));
            
            // En una implementación real, aquí se generaría el archivo Excel
            console.log('✅ Exportación a Excel completada');
            return true;
            
        } catch (error) {
            console.error('❌ Error en exportación a Excel:', error);
            throw error;
        }
    }

    /**
     * Simula exportación a PDF (placeholder)
     * @param {Object} filters - Filtros aplicados
     * @returns {Promise<boolean>} - Éxito de la operación
     */
    async exportToPDF(filters = {}) {
        try {
            console.log('📑 Iniciando exportación a PDF...', filters);
            
            // Simular delay de exportación
            await new Promise(resolve => setTimeout(resolve, 2000));
            
            // En una implementación real, aquí se generaría el archivo PDF
            console.log('✅ Exportación a PDF completada');
            return true;
            
        } catch (error) {
            console.error('❌ Error en exportación a PDF:', error);
            throw error;
        }
    }
}
