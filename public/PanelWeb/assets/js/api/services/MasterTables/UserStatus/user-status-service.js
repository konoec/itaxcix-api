// Servicio para listar estados de usuario
class UserStatusService {
    static get API_BASE_URL() {
        return 'https://149.130.161.148/api/v1';
    }

    static getAuthToken() {
        return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
    }

    /**
     * Obtiene la lista de estados de usuario con filtros, búsqueda y paginación
     * @param {Object} params - { page, perPage, search, name, active, sortBy, sortDirection }
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async getUserStatuses(params = {}) {
        const query = new URLSearchParams();
        if (params.page) query.append('page', params.page);
        if (params.perPage) query.append('perPage', params.perPage);
        if (params.search) query.append('search', params.search);
        if (params.name) query.append('name', params.name);
        if (typeof params.active === 'boolean') query.append('active', params.active);
        if (params.sortBy) query.append('sortBy', params.sortBy);
        if (params.sortDirection) query.append('sortDirection', params.sortDirection);
        try {
            const token = this.getAuthToken();
            const response = await fetch(`${this.API_BASE_URL}/admin/user-statuses?${query.toString()}`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                let errorMsg = errorData?.error?.message || errorData.message || 'Error al obtener estados de usuario';
                throw new Error(errorMsg);
            }
            const result = await response.json();
            if (!result.message || result.message.trim().toUpperCase() === 'OK') {
                result.message = 'Estado de usuario creado correctamente';
            }
            return result;
        } catch (error) {
            throw error;
        }
    }
}
window.UserStatusService = UserStatusService;
