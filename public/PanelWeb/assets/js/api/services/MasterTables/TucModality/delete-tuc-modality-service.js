// Archivo: DeleteTucModalityService.js
// Ubicación: assets/js/api/services/MasterTables/TucModality/delete-tuc-modality-service.js

/**
 * Servicio para eliminar modalidades TUC
 * Maneja la comunicación con la API para eliminación de modalidades TUC
 */
class DeleteTucModalityService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.endpoint = '/admin/tuc-modalities';
    }

    /**
     * Obtiene el token de autenticación del sessionStorage
     * @returns {string|null} Token de autenticación
     */
    getAuthToken() {
        return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
    }

    /**
     * Elimina una modalidad TUC
     * @param {number} id - ID de la modalidad TUC
     * @returns {Promise<Object>} Respuesta de la API
     */
    async deleteTucModality(id) {
        try {
            if (!id || typeof id !== 'number' || id <= 0) {
                throw new Error('ID de la modalidad TUC es requerido y debe ser un número válido');
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

            let responseData = null;
            try {
                responseData = await response.json();
            } catch (e) {
                responseData = null;
            }

            if (!response.ok) {
                let errorMessage = `Error HTTP ${response.status}: ${response.statusText}`;
                if (responseData) {
                    if (responseData.error && responseData.error.message) {
                        errorMessage = responseData.error.message;
                    } else if (responseData.message) {
                        errorMessage = responseData.message;
                    }
                }
                throw new Error(errorMessage);
            }

            if (!responseData || !responseData.success) {
                let errorMessage = 'Error al eliminar modalidad TUC';
                if (responseData) {
                    if (responseData.error && responseData.error.message) {
                        errorMessage = responseData.error.message;
                    } else if (responseData.message) {
                        errorMessage = responseData.message;
                    }
                }
                throw new Error(errorMessage);
            }

            return responseData;
        } catch (error) {
            console.error('❌ Error en deleteTucModality:', error);
            throw error;
        }
    }
}

// Exportar como instancia singleton
window.DeleteTucModalityService = new DeleteTucModalityService();
