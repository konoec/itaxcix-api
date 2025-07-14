// Servicio para crear un nuevo estado de usuario
class CreateUserStatusService {
    static get API_BASE_URL() {
        return 'https://149.130.161.148/api/v1';
    }

    static getAuthToken() {
        return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
    }

    /**
     * Crea un nuevo estado de usuario
     * @param {Object} data - { name, active }
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async createUserStatus(data) {
        try {
            const token = this.getAuthToken();
            const response = await fetch(`${this.API_BASE_URL}/admin/user-statuses`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || 'Error al crear estado de usuario');
            }
            return await response.json();
        } catch (error) {
            throw error;
        }
    }
}
window.CreateUserStatusService = CreateUserStatusService;
