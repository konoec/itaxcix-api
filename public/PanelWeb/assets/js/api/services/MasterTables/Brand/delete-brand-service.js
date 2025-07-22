// Archivo: DeleteBrandService.js
// Ubicación: assets/js/api/services/MasterTables/Brand/delete-brand-service.js

/**
 * Servicio para eliminar marcas de vehículo
 * Maneja la comunicación con la API para eliminación de marcas
 */
class DeleteBrandService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.endpoint = '/admin/brands';
    }

    /**
     * Obtiene el token de autenticación del sessionStorage
     * @returns {string|null} Token de autenticación
     */
    getAuthToken() {
        return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
    }

    /**
     * Elimina una marca de vehículo
     * @param {number} id - ID de la marca
     * @returns {Promise<Object>} Respuesta de la API
     */
    async deleteBrand(id) {
        try {
            if (!id || typeof id !== 'number' || id <= 0) {
                throw new Error('ID de la marca es requerido y debe ser un número válido');
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
                throw new Error(responseData.message || 'Error al eliminar marca');
            }

            return responseData;
        } catch (error) {
            console.error('❌ Error en deleteBrand:', error);
            throw error;
        }
    }
}

// Exportar como instancia singleton
window.DeleteBrandService = new DeleteBrandService();
