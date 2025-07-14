/**
 * Servicio para la gestión de provincias
 * Maneja las operaciones de listado con filtros avanzados
 * Endpoint: /admin/provinces
 */

class ProvincesService {
    static get API_BASE_URL() {
        return 'https://149.130.161.148/api/v1';
    }

    static getAuthToken() {
        return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
    }

    /**
     * Obtiene la lista de provincias con paginación y filtros avanzados
     * @param {number} page - Número de página (default: 1)
     * @param {number} perPage - Elementos por página (default: 15, max: 100)
     * @param {string} search - Búsqueda global en nombre, ubigeo y departamento (opcional)
     * @param {string} name - Filtro por nombre de provincia (opcional)
     * @param {number} departmentId - Filtro por ID de departamento (opcional)
     * @param {string} ubigeo - Filtro por código UBIGEO (opcional)
     * @param {string} sortBy - Campo de ordenamiento: 'id', 'name', 'ubigeo' (default: 'name')
     * @param {string} sortOrder - Dirección de ordenamiento: 'ASC', 'DESC' (default: 'ASC')
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async getProvinces(page = 1, perPage = 15, search = null, name = null, departmentId = null, ubigeo = null, sortBy = 'name', sortOrder = 'ASC') {
        try {
            console.log(`🔄 ProvincesService: Obteniendo provincias - página ${page}, límite ${perPage}`);
            if (search) console.log(`🔍 Filtro de búsqueda global: "${search}"`);
            if (name) console.log(`🏷️ Filtro por nombre: "${name}"`);
            if (departmentId) console.log(`🗺️ Filtro por departamento ID: ${departmentId}`);
            if (ubigeo) console.log(`📍 Filtro por UBIGEO: "${ubigeo}"`);
            console.log(`🔧 Ordenamiento: ${sortBy} ${sortOrder}`);
            
            const token = ProvincesService.getAuthToken();
            
            // Construir URL con parámetros
            const url = new URL(`${ProvincesService.API_BASE_URL}/admin/provinces`);
            
            // Parámetros de paginación
            url.searchParams.append('page', page.toString());
            url.searchParams.append('perPage', Math.min(perPage, 100).toString()); // Máximo 100 según API
            
            // Parámetros opcionales de filtrado
            if (search && search.trim()) {
                url.searchParams.append('search', search.trim());
                console.log(`🔍 Parámetro de búsqueda global agregado: "${search.trim()}"`);
            }
            
            if (name && name.trim()) {
                url.searchParams.append('name', name.trim());
                console.log(`🏷️ Parámetro de filtro por nombre agregado: "${name.trim()}"`);
            }
            
            if (departmentId && departmentId > 0) {
                url.searchParams.append('departmentId', departmentId.toString());
                console.log(`🗺️ Parámetro de filtro por departamento agregado: ${departmentId}`);
            }
            
            if (ubigeo && ubigeo.trim()) {
                url.searchParams.append('ubigeo', ubigeo.trim());
                console.log(`📍 Parámetro de filtro por UBIGEO agregado: "${ubigeo.trim()}"`);
            }
            
            // Parámetros de ordenamiento
            if (sortBy && ['id', 'name', 'ubigeo'].includes(sortBy)) {
                url.searchParams.append('sortBy', sortBy);
            }
            
            if (sortOrder && ['ASC', 'DESC'].includes(sortOrder)) {
                url.searchParams.append('sortOrder', sortOrder);
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
                    console.error('🚫 Sin permisos para acceder a provincias');
                    throw new Error('No tienes permisos para ver las provincias.');
                } else {
                    throw new Error(errorData.message || `Error del servidor: ${response.status}`);
                }
            }

            const data = await response.json();
            console.log('✅ Provincias obtenidas exitosamente:', data);
            
            // Validar estructura de respuesta
            if (!data.success || !data.data) {
                console.error('❌ Estructura de respuesta inválida:', data);
                throw new Error('Formato de respuesta inválido del servidor');
            }

            return data;

        } catch (error) {
            console.error('❌ Error en ProvincesService.getProvinces:', error);
            
            if (error.name === 'TypeError' && error.message.includes('fetch')) {
                throw new Error('Error de conexión. Verifica tu conexión a internet.');
            }
            
            throw error;
        }
    }

    /**
     * Validar los parámetros de la consulta
     * @param {Object} params - Parámetros a validar
     * @returns {Object} Parámetros validados
     */
    static validateParams(params) {
        const validated = {};
        
        // Validar página
        if (params.page) {
            validated.page = Math.max(1, parseInt(params.page) || 1);
        }
        
        // Validar perPage
        if (params.perPage) {
            validated.perPage = Math.min(100, Math.max(1, parseInt(params.perPage) || 15));
        }
        
        // Validar sortBy
        if (params.sortBy && ['id', 'name', 'ubigeo'].includes(params.sortBy)) {
            validated.sortBy = params.sortBy;
        }
        
        // Validar sortOrder
        if (params.sortOrder && ['ASC', 'DESC'].includes(params.sortOrder)) {
            validated.sortOrder = params.sortOrder;
        }
        
        // Validar departmentId
        if (params.departmentId) {
            const deptId = parseInt(params.departmentId);
            if (deptId > 0) {
                validated.departmentId = deptId;
            }
        }
        
        return validated;
    }

    /**
     * Obtener estadísticas de provincias
     * @returns {Promise<Object>} Estadísticas básicas
     */
    static async getProvincesStats() {
        try {
            console.log('📊 Obteniendo estadísticas de provincias...');
            
            // Obtener la primera página para obtener el total
            const response = await ProvincesService.getProvinces(1, 1);
            
            if (response && response.data && response.data.pagination) {
                const stats = {
                    total: response.data.pagination.total,
                    totalPages: response.data.pagination.totalPages
                };
                
                console.log('📊 Estadísticas obtenidas:', stats);
                return stats;
            }
            
            return { total: 0, totalPages: 0 };
        } catch (error) {
            console.error('❌ Error obteniendo estadísticas:', error);
            return { total: 0, totalPages: 0 };
        }
    }
}

// Hacer disponible globalmente
window.ProvincesService = ProvincesService;

console.log('🌎 ProvincesService cargado y disponible globalmente');
