// Archivo: /services/DeleteDocumentTypeService.js
// Servicio para eliminar tipos de documento
class DeleteDocumentTypeService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.endpoint = '/admin/document-types';
    }

    // Obtiene el token de autenticación desde sessionStorage
    getAuthToken() {
        return sessionStorage.getItem('token');
    }

    /**
     * Elimina un tipo de documento por ID
     * @param {number} id - ID del tipo de documento
     * @returns {Promise<Object>} Respuesta de la API
     */
    async deleteDocumentType(id) {
        if (!id || typeof id !== 'number' || id <= 0) {
            throw new Error('ID del tipo de documento es requerido y debe ser un número válido');
        }
        const token = this.getAuthToken();
        if (!token) {
            throw new Error('Token de autenticación requerido');
        }
        try {
            const response = await fetch(`${this.baseUrl}${this.endpoint}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });
            const data = await response.json();
            if (!response.ok || !data.success) {
                throw new Error(data.message || `Error HTTP ${response.status}`);
            }
            return data;
        } catch (error) {
            throw new Error(error.message || 'Error al eliminar tipo de documento');
        }
    }
}
// Exportar como singleton global
window.DeleteDocumentTypeService = new DeleteDocumentTypeService();
