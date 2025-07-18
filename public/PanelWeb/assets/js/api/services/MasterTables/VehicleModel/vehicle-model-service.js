// Servicio para listar modelos de vehículos
class VehicleModelService {
    static get API_BASE_URL() {
        return 'https://149.130.161.148/api/v1';
    }

    static getAuthToken() {
        return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
    }

    /**
     * Obtiene la lista de modelos de vehículos con filtros, búsqueda y paginación
     * @param {Object} params - { page, perPage, search, name, brandId, active, sortBy, sortOrder }
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async getVehicleModels(params = {}) {
        const query = new URLSearchParams();
        if (params.page) query.append('page', params.page);
        if (params.perPage) query.append('perPage', params.perPage);
        if (params.search) query.append('search', params.search);
        if (params.name) query.append('name', params.name);
        if (params.brandId) query.append('brandId', params.brandId);
        if (typeof params.active === 'boolean') query.append('active', params.active);
        if (params.sortBy) query.append('sortBy', params.sortBy);
        if (params.sortOrder) query.append('sortOrder', params.sortOrder);
        
        try {
            const token = this.getAuthToken();
            const response = await fetch(`${this.API_BASE_URL}/admin/models?${query.toString()}`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || 'Error al obtener modelos de vehículos');
            }
            
            const result = await response.json();
            if (!result.message || result.message.trim().toUpperCase() === 'OK') {
                result.message = 'Modelo de vehículo creado correctamente';
            }
            return result;
        } catch (error) {
            throw error;
        }
    }
}

window.VehicleModelService = VehicleModelService;
