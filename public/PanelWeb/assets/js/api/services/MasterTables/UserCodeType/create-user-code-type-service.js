// Servicio para crear un nuevo tipo de código de usuario
class CreateUserCodeTypeService {
    static get API_BASE_URL() {
        return 'https://149.130.161.148/api/v1';
    }

    static getAuthToken() {
        return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
    }

    /**
     * Crea un nuevo tipo de código de usuario
     * @param {Object} data - { name, active }
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async createUserCodeType(data) {
        try {
            const token = this.getAuthToken();
            const response = await fetch(`${this.API_BASE_URL}/admin/user-code-types`, {
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
                throw new Error(errorData.message || 'Error al crear tipo de código de usuario');
            }
            return await response.json();
        } catch (error) {
            throw error;
        }
    }
}
window.CreateUserCodeTypeService = CreateUserCodeTypeService;
