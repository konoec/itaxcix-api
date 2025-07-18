/**
 * Servicio para la gestión de tipos de contacto
 * Endpoint: /admin/contact-types
 * Maneja las operaciones de listado con filtros avanzados según la API
 */

class ContactTypeService {
    static get API_BASE_URL() {
        return 'https://149.130.161.148/api/v1';
    }

    static getAuthToken() {
        return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
    }

    /**
     * Obtiene la lista de tipos de contacto con paginación y filtros avanzados
     * @param {number} page - Número de página (default: 1, min: 1)
     * @param {number} limit - Elementos por página (default: 15, min: 1, max: 100)
     * @param {string} search - Búsqueda global en nombre (opcional)
     * @param {string} name - Filtro por nombre del tipo (opcional)
     * @param {boolean} active - Filtro por estado activo (opcional)
     * @param {string} sortBy - Campo de ordenamiento: 'id', 'name', 'active' (default: 'name')
     * @param {string} sortDirection - Dirección de ordenamiento: 'ASC', 'DESC' (default: 'ASC')
     * @param {boolean} onlyActive - Incluir solo activos (default: false)
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async getContactTypes(page = 1, limit = 15, search = null, name = null, active = null, sortBy = 'name', sortDirection = 'ASC', onlyActive = false) {
        try {
            console.log(`� ContactTypeService: Obteniendo tipos de contacto - página ${page}, por página ${limit}`);
            if (search) console.log(`🔍 Filtro de búsqueda: "${search}"`);
            if (name) console.log(`🏷️ Filtro por nombre: "${name}"`);
            if (active !== null) console.log(`✅ Filtro por activo: ${active}`);
            console.log(`🔧 Ordenamiento: ${sortBy} ${sortDirection}`);
            console.log(`🎯 Solo activos: ${onlyActive}`);
            
            const token = ContactTypeService.getAuthToken();
            
            // Construir URL con parámetros
            const url = new URL(`${ContactTypeService.API_BASE_URL}/admin/contact-types`);
            
            // Parámetros de paginación
            url.searchParams.append('page', Math.max(1, page).toString());
            url.searchParams.append('limit', Math.min(100, Math.max(1, limit)).toString());
            
            // Parámetros opcionales de filtro
            if (search && search.trim()) {
                url.searchParams.append('name', search.trim()); // API usa 'name' para búsqueda
                console.log(`🔍 Parámetro de búsqueda agregado a URL: "${search.trim()}"`);
            }
            
            if (name && name.trim() && !search) {
                url.searchParams.append('name', name.trim());
                console.log(`🏷️ Parámetro de nombre agregado a URL: "${name.trim()}"`);
            }
            
            if (active !== null && typeof active === 'boolean') {
                url.searchParams.append('active', active.toString());
                console.log(`✅ Parámetro activo agregado a URL: ${active}`);
            }
            
            // Parámetros de ordenamiento
            const validSortFields = ['id', 'name', 'active'];
            const validSortDirections = ['ASC', 'DESC'];
            
            if (validSortFields.includes(sortBy)) {
                url.searchParams.append('sortBy', sortBy);
            }
            
            if (validSortDirections.includes(sortDirection.toUpperCase())) {
                url.searchParams.append('sortDirection', sortDirection.toUpperCase());
            }
            
            // Solo activos
            if (onlyActive) {
                url.searchParams.append('onlyActive', 'true');
                console.log(`🎯 Parámetro solo activos agregado a URL`);
            }

            console.log(`📡 ContactTypeService: Realizando petición a ${url.toString()}`);

            const response = await fetch(url.toString(), {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            console.log(`📡 ContactTypeService: Respuesta recibida con status ${response.status}`);

            if (!response.ok) {
                const errorText = await response.text();
                console.error(`❌ ContactTypeService: Error ${response.status}: ${errorText}`);
                throw new Error(`Error ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            console.log('✅ ContactTypeService: Tipos de contacto obtenidos exitosamente:', data);

            return data;

        } catch (error) {
            console.error('❌ ContactTypeService: Error al obtener tipos de contacto:', error);
            throw error;
        }
    }
}

// Hacer disponible globalmente
window.ContactTypeService = ContactTypeService;

console.log('✅ ContactTypeService cargado y disponible globalmente');
