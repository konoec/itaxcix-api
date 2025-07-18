/**
 * Servicio para la gestión de estados de conductores
 * Endpoint: /admin/driver-statuses
 * Maneja las operaciones de listado con filtros avanzados según la API
 */

class DriverStatusService {
    static get API_BASE_URL() {
        return 'https://149.130.161.148/api/v1';
    }

    static getAuthToken() {
        return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
    }

    /**
     * Obtiene la lista de estados de conductores con paginación y filtros avanzados
     * @param {number} page - Número de página (default: 1, min: 1)
     * @param {number} perPage - Elementos por página (default: 15, min: 1, max: 100)
     * @param {string} search - Búsqueda global en nombre (opcional)
     * @param {string} name - Filtro por nombre del estado (opcional)
     * @param {boolean} active - Filtro por estado activo (opcional)
     * @param {string} sortBy - Campo de ordenamiento: 'id', 'name', 'active' (default: 'name')
     * @param {string} sortDirection - Dirección de ordenamiento: 'asc', 'desc' (default: 'asc')
     * @param {boolean} onlyActive - Incluir solo activos (default: false)
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async getDriverStatuses(page = 1, perPage = 15, search = null, name = null, active = null, sortBy = 'name', sortDirection = 'asc', onlyActive = false) {
        try {
            console.log(`🔄 DriverStatusService: Obteniendo estados de conductores - página ${page}, por página ${perPage}`);
            if (search) console.log(`🔍 Filtro de búsqueda: "${search}"`);
            if (name) console.log(`🏷️ Filtro por nombre: "${name}"`);
            if (active !== null) console.log(`✅ Filtro por activo: ${active}`);
            console.log(`🔧 Ordenamiento: ${sortBy} ${sortDirection}`);
            console.log(`🎯 Solo activos: ${onlyActive}`);
            
            const token = DriverStatusService.getAuthToken();
            
            // Construir URL con parámetros
            const url = new URL(`${DriverStatusService.API_BASE_URL}/admin/driver-statuses`);
            
            // Parámetros de paginación
            url.searchParams.append('page', Math.max(1, page).toString());
            url.searchParams.append('perPage', Math.min(100, Math.max(1, perPage)).toString());
            
            // Parámetros opcionales de filtro
            if (search && search.trim()) {
                url.searchParams.append('search', search.trim());
                console.log(`🔍 Parámetro de búsqueda agregado a URL: "${search.trim()}"`);
            }
            
            if (name && name.trim()) {
                url.searchParams.append('name', name.trim());
                console.log(`🏷️ Parámetro de nombre agregado a URL: "${name.trim()}"`);
            }
            
            if (active !== null) {
                url.searchParams.append('active', active.toString());
                console.log(`✅ Parámetro activo agregado a URL: "${active}"`);
            }
            
            // Parámetros de ordenamiento
            if (sortBy && ['id', 'name', 'active'].includes(sortBy)) {
                url.searchParams.append('sortBy', sortBy);
            }
            
            if (sortDirection && ['asc', 'desc'].includes(sortDirection)) {
                url.searchParams.append('sortDirection', sortDirection);
            }
            
            // Parámetro onlyActive
            if (onlyActive) {
                url.searchParams.append('onlyActive', 'true');
            }
            
            console.log(`🌐 URL completa: ${url.toString()}`);
            
            const response = await fetch(url.toString(), {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });

            console.log(`📡 Status de respuesta: ${response.status}`);

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                console.error('❌ Error en la respuesta del servidor:', errorData);
                
                if (response.status === 401) {
                    console.error('🚫 Token de autenticación inválido o expirado');
                    throw new Error('Sesión expirada. Por favor, inicia sesión nuevamente.');
                } else if (response.status === 403) {
                    console.error('🚫 Sin permisos para acceder a estados de conductores');
                    throw new Error('No tienes permisos para ver los estados de conductores.');
                } else {
                    throw new Error(errorData.message || `Error del servidor: ${response.status}`);
                }
            }

            const data = await response.json();
            console.log('✅ Estados de conductores obtenidos exitosamente:', data);
            
            // Validar estructura de respuesta
            if (!data.success || !data.data) {
                console.error('❌ Estructura de respuesta inválida:', data);
                throw new Error('Formato de respuesta inválido del servidor');
            }

            return data;

        } catch (error) {
            console.error('❌ Error en DriverStatusService.getDriverStatuses:', error);
            
            if (error.name === 'TypeError' && error.message.includes('fetch')) {
                throw new Error('Error de conexión. Verifica tu conexión a internet.');
            }
            
            throw error;
        }
    }

    /**
     * Valida los parámetros de entrada para el listado
     * @param {Object} params - Parámetros a validar
     * @returns {Object} Parámetros validados
     */
    static validateParams(params = {}) {
        const validated = {
            page: Math.max(1, parseInt(params.page) || 1),
            perPage: Math.min(100, Math.max(1, parseInt(params.perPage) || 15)),
            search: params.search?.trim() || null,
            name: params.name?.trim() || null,
            active: params.active !== null && params.active !== undefined ? Boolean(params.active) : null,
            sortBy: ['id', 'name', 'active'].includes(params.sortBy) ? params.sortBy : 'name',
            sortDirection: ['asc', 'desc'].includes(params.sortDirection) ? params.sortDirection : 'asc',
            onlyActive: Boolean(params.onlyActive || false)
        };

        console.log('✅ Parámetros validados:', validated);
        return validated;
    }

    /**
     * Transforma los datos de la API al formato requerido por la UI
     * @param {Object} apiResponse - Respuesta de la API
     * @returns {Object} Datos transformados para la UI
     */
    static transformApiResponse(apiResponse) {
        if (!apiResponse.success || !apiResponse.data) {
            throw new Error('Respuesta de API inválida');
        }

        return {
            driverStatuses: apiResponse.data.items || [],
            meta: {
                total: apiResponse.data.meta?.total || 0,
                perPage: apiResponse.data.meta?.perPage || 15,
                currentPage: apiResponse.data.meta?.currentPage || 1,
                lastPage: apiResponse.data.meta?.lastPage || 1,
                search: apiResponse.data.meta?.search || null,
                filters: apiResponse.data.meta?.filters || {},
                sortBy: apiResponse.data.meta?.sortBy || 'name',
                sortDirection: apiResponse.data.meta?.sortDirection || 'asc'
            },
            predefinedStatuses: apiResponse.data.predefinedStatuses || {},
            success: true,
            message: apiResponse.message || 'Operación realizada correctamente.'
        };
    }

    /**
     * Obtiene estadísticas rápidas de estados de conductores
     * @param {Object} meta - Metadatos de la respuesta
     * @returns {Object} Estadísticas calculadas
     */
    static getQuickStats(meta) {
        return {
            total: meta.total || 0,
            currentPage: meta.currentPage || 1,
            lastPage: meta.lastPage || 1,
            perPage: meta.perPage || 15,
            hasSearch: Boolean(meta.search),
            hasFilters: Object.keys(meta.filters || {}).length > 0
        };
    }
}

// Exportar para uso global
window.DriverStatusService = DriverStatusService;
