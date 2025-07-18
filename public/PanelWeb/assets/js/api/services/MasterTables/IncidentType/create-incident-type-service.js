// Servicio para crear un nuevo tipo de incidencia siguiendo el patrón de los otros servicios
class CreateIncidentTypeService {
    static get API_BASE_URL() {
        return 'https://149.130.161.148/api/v1';
    }

    static getAuthToken() {
        return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
    }

    /**
     * Crea un nuevo tipo de incidencia
     * @param {Object} data - { name, active }
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async createIncidentType(data) {
        try {
            const token = this.getAuthToken();
            if (!token) {
                throw new Error('Token de autenticación no encontrado');
            }
            const response = await fetch(`${this.API_BASE_URL}/admin/incident-types`, {
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
                throw new Error(errorData.message || 'Error al crear tipo de incidencia');
            }
            const result = await response.json();
            if (!result.message || result.message.trim().toUpperCase() === 'OK') {
                result.message = 'Tipo de incidente creado correctamente';
            }
            return result;
        } catch (error) {
            throw error;
        }
    }
}

window.CreateIncidentTypeService = CreateIncidentTypeService;
export { CreateIncidentTypeService };
