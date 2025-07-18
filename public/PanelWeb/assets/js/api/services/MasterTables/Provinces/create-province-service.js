// Servicio para crear una nueva provincia
class CreateProvinceService {
    static get API_BASE_URL() {
        return 'https://149.130.161.148/api/v1';
    }

    static getAuthToken() {
        return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
    }

    /**
     * Crea una nueva provincia
     * @param {Object} data - { name, departmentId, ubigeo }
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async createProvince(data) {
        try {
            const token = this.getAuthToken();
            const response = await fetch(`${this.API_BASE_URL}/admin/provinces`, {
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
                throw new Error(errorData.message || 'Error al crear provincia');
            }

            const result = await response.json();
            if (!result.message || result.message.trim().toUpperCase() === 'OK') {
                result.message = 'Provincia creada correctamente';
            }
            return result;
        } catch (error) {
            throw error;
        }
    }
}
window.CreateProvinceService = CreateProvinceService;
