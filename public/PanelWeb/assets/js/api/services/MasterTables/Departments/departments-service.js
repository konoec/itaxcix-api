/**
 * Servicio para la gestión de departamentos
 * Maneja las operaciones de listado con filtros avanzados
 */

class DepartmentsService {
    static get API_BASE_URL() {
        return 'https://149.130.161.148/api/v1';
    }

    static getAuthToken() {
        return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
    }

    /**
     * Obtiene la lista de departamentos con paginación y filtros avanzados
     * @param {number} page - Número de página (default: 1)
     * @param {number} limit - Elementos por página (default: 15, max: 100)
     * @param {string} search - Búsqueda global en nombre y ubigeo (opcional)
     * @param {string} orderBy - Campo de ordenamiento: 'id', 'name', 'ubigeo' (default: 'name')
     * @param {string} orderDirection - Dirección de ordenamiento: 'ASC', 'DESC' (default: 'ASC')
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async getDepartments(page = 1, limit = 15, search = null, orderBy = 'name', orderDirection = 'ASC') {
        try {
            console.log(`🔄 DepartmentsService: Obteniendo departamentos - página ${page}, límite ${limit}`);
            if (search) console.log(`🔍 Filtro de búsqueda: "${search}"`);
            console.log(`🔧 Ordenamiento: ${orderBy} ${orderDirection}`);
            
            const token = DepartmentsService.getAuthToken();
            
            // Construir URL con parámetros
            const url = new URL(`${DepartmentsService.API_BASE_URL}/admin/departments`);
            
            // Parámetros de paginación
            url.searchParams.append('page', page.toString());
            url.searchParams.append('limit', Math.min(limit, 100).toString()); // Máximo 100 según API
            
            // Parámetros opcionales
            if (search && search.trim()) {
                url.searchParams.append('search', search.trim());
                console.log(`🔍 Parámetro de búsqueda agregado a URL: "${search.trim()}"`);
            }
            
            // Parámetros de ordenamiento
            if (orderBy && ['id', 'name', 'ubigeo'].includes(orderBy)) {
                url.searchParams.append('orderBy', orderBy);
            }
            
            if (orderDirection && ['ASC', 'DESC'].includes(orderDirection)) {
                url.searchParams.append('orderDirection', orderDirection);
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
                    console.error('🚫 Sin permisos para acceder a departamentos');
                    throw new Error('No tienes permisos para ver los departamentos.');
                } else {
                    throw new Error(errorData.message || `Error del servidor: ${response.status}`);
                }
            }

            const data = await response.json();
            console.log('✅ Departamentos obtenidos exitosamente:', data);
            
            // Validar estructura de respuesta
            if (!data.success || !data.data) {
                console.error('❌ Estructura de respuesta inválida:', data);
                throw new Error('Formato de respuesta inválido del servidor');
            }

            return data;

        } catch (error) {
            console.error('❌ Error en DepartmentsService.getDepartments:', error);
            
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
            limit: Math.min(100, Math.max(1, parseInt(params.limit) || 15)),
            search: params.search?.trim() || null,
            orderBy: ['id', 'name', 'ubigeo'].includes(params.orderBy) ? params.orderBy : 'name',
            orderDirection: ['ASC', 'DESC'].includes(params.orderDirection) ? params.orderDirection : 'ASC'
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
            departments: apiResponse.data.data || [],
            pagination: {
                page: apiResponse.data.pagination?.page || 1,
                limit: apiResponse.data.pagination?.limit || 15,
                total: apiResponse.data.pagination?.total || 0,
                totalPages: apiResponse.data.pagination?.totalPages || 0,
                hasNext: apiResponse.data.pagination?.hasNext || false,
                hasPrev: apiResponse.data.pagination?.hasPrev || false
            },
            success: true,
            message: apiResponse.message || 'Operación realizada correctamente.'
        };
    }
}

// Exportar para uso global
window.DepartmentsService = DepartmentsService;