// Servicio para gestión de distritos
class DistrictsService {
    static get API_BASE_URL() {
        return 'https://149.130.161.148/api/v1';
    }
    static getAuthToken() {
        return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
    }
    /**
     * Obtiene la lista de distritos con filtros y paginación
     * @param {Object} params
     * @returns {Promise<Object>}
     */
    static async getDistricts(params = {}) {
        const {
            page = 1,
            perPage = 15,
            search = '',
            name = '',
            provinceId = '',
            ubigeo = '',
            sortBy = 'name',
            sortDirection = 'asc'
        } = params;
        const url = new URL(`${this.API_BASE_URL}/admin/districts`);
        url.searchParams.append('page', page);
        url.searchParams.append('perPage', perPage);
        if (search) url.searchParams.append('search', search);
        if (name) url.searchParams.append('name', name);
        if (provinceId) url.searchParams.append('provinceId', provinceId);
        if (ubigeo) url.searchParams.append('ubigeo', ubigeo);
        if (sortBy) url.searchParams.append('sortBy', sortBy);
        if (sortDirection) url.searchParams.append('sortDirection', sortDirection);
        const token = this.getAuthToken();
        const response = await fetch(url.toString(), {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        if (!response.ok) {
            const errorData = await response.json().catch(() => ({}));
            throw new Error(errorData.message || 'Error al obtener distritos');
        }
        const result = await response.json();
        if (!result.message || result.message.trim().toUpperCase() === 'OK') {
            result.message = 'Distrito creado correctamente';
        }
        return result;
    }
}
window.DistrictsService = DistrictsService;
