// Archivo: DeleteVehicleClassService.js
// Ubicación: assets/js/api/services/MasterTables/VehicleClass/delete-vehicle-class-service.js

/**
 * Servicio para eliminar clases de vehículo
 * Maneja la comunicación con la API para eliminación de clases
 */
class DeleteVehicleClassService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.endpoint = '/admin/vehicle-classes';
    }

    /**
     * Obtiene el token de autenticación del sessionStorage
     * @returns {string|null} Token de autenticación
     */
    getAuthToken() {
        return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
    }

    /**
     * Elimina una clase de vehículo
     * @param {number} id - ID de la clase
     * @returns {Promise<Object>} Respuesta de la API
     */
    async deleteVehicleClass(id) {
        try {
            if (!id || typeof id !== 'number' || id <= 0) {
                throw new Error('ID de la clase de vehículo es requerido y debe ser un número válido');
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
                    if (errorData.message) {
                        errorMessage = errorData.message;
                    }
                } catch (e) {}
                throw new Error(errorMessage);
            }

            const responseData = await response.json();
            if (!responseData.success) {
                throw new Error(responseData.message || 'Error al eliminar clase de vehículo');
            }

            return responseData;
        } catch (error) {
            console.error('❌ Error en deleteVehicleClass:', error);
            throw error;
        }
    }
}

// Exportar como instancia singleton
window.DeleteVehicleClassService = new DeleteVehicleClassService();
