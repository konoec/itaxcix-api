// Servicio para crear un nuevo distrito
class CreateDistrictService {
    static get API_BASE_URL() {
        return 'https://149.130.161.148/api/v1';
    }

    static getAuthToken() {
        return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
    }

    /**
     * Crea un nuevo distrito
     * @param {Object} data - { name, provinceId, ubigeo }
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async createDistrict(data) {
        try {
            const token = this.getAuthToken();
            const response = await fetch(`${this.API_BASE_URL}/admin/districts`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || 'Error al crear distrito');
            }

            return await response.json();
        } catch (error) {
            throw error;
        }
    }
}
window.CreateDistrictService = CreateDistrictService;
