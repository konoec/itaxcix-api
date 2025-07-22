// Archivo: DeleteIncidentTypeService.js
// Ubicación: assets/js/api/services/MasterTables/IncidentType/delete-incident-type-service.js

/**
 * Servicio para eliminar tipos de incidencia
 * Maneja la comunicación con la API para eliminación de tipos
 */
class DeleteIncidentTypeService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.endpoint = '/admin/incident-types';
    }

    /**
     * Obtiene el token de autenticación del sessionStorage
     * @returns {string|null} Token de autenticación
     */
    getAuthToken() {
        return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
    }

    /**
     * Elimina un tipo de incidencia
     * @param {number} id - ID del tipo de incidencia
     * @returns {Promise<Object>} Respuesta de la API
     */
    async deleteIncidentType(id) {
        try {
            if (!id || typeof id !== 'number' || id <= 0) {
                throw new Error('ID del tipo de incidencia es requerido y debe ser un número válido');
            }

            const token = this.getAuthToken();
            if (!token) {
                throw new Error('Token de autenticación requerido');
            }

            const response = await fetch(`${this.baseUrl}${this.endpoint}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            if (!response.ok) {
                let errorMessage = `Error HTTP ${response.status}: ${response.statusText}`;
                try {
                    const errorData = await response.json();
                    if (errorData.error && errorData.error.message) {
                        errorMessage = errorData.error.message;
                    } else if (errorData.message) {
                        errorMessage = errorData.message;
                    }
                } catch (e) {}
                throw new Error(errorMessage);
            }

            const responseData = await response.json();
            if (!responseData.success) {
                throw new Error(responseData.message || 'Error al eliminar tipo de incidencia');
            }

            return responseData;
        } catch (error) {
            console.error('❌ Error en deleteIncidentType:', error);
            throw error;
        }
    }
}

// Exportar como instancia singleton
window.DeleteIncidentTypeService = new DeleteIncidentTypeService();
