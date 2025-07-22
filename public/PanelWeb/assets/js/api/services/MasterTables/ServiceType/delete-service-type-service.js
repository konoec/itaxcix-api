// Archivo: DeleteServiceTypeService.js
// Ubicación: assets/js/api/services/MasterTables/ServiceType/delete-service-type-service.js

/**
 * Servicio para eliminar tipos de servicio
 * Maneja la comunicación con la API para eliminación de tipos de servicio
 */
class DeleteServiceTypeService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.endpoint = '/admin/service-types';
    }

    /**
     * Obtiene el token de autenticación del sessionStorage
     * @returns {string|null} Token de autenticación
     */
    getAuthToken() {
        return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
    }

    /**
     * Elimina un tipo de servicio
     * @param {number} id - ID del tipo de servicio
     * @returns {Promise<Object>} Respuesta de la API
     */
    async deleteServiceType(id) {
        try {
            if (!id || typeof id !== 'number' || id <= 0) {
                throw new Error('ID del tipo de servicio es requerido y debe ser un número válido');
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

            const responseData = await response.json();

            if (!response.ok || !responseData.success) {
                throw new Error(responseData.message || `Error HTTP ${response.status}: ${response.statusText}`);
            }

            return responseData;
        } catch (error) {
            console.error('❌ Error en deleteServiceType:', error);
            throw error;
        }
    }
}

// Exportar como instancia singleton
window.DeleteServiceTypeService = new DeleteServiceTypeService();
