// Archivo: /services/DeleteContactTypeService.js
// Servicio para eliminar tipos de contacto
class DeleteContactTypeService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.endpoint = '/admin/contact-types';
    }

    /**
     * Elimina un tipo de contacto
     * @param {number} id - ID del tipo de contacto
     * @returns {Promise<Object>} Respuesta de la API
     */
    async deleteContactType(id) {
        try {
            if (!id || typeof id !== 'number' || id <= 0) {
                throw new Error('ID del tipo de contacto es requerido y debe ser un número válido');
            }
            // Compatibilidad con ambos nombres de token
            const token = sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
            if (!token) {
                throw new Error('Token de autenticación requerido');
            }
            const response = await fetch(`${this.baseUrl}${this.endpoint}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });
            const data = await response.json().catch(() => ({}));
            if (!response.ok || !data.success) {
                throw new Error(data.message || `Error HTTP ${response.status}: ${response.statusText}`);
            }
            return data;
        } catch (error) {
            console.error('❌ Error en deleteContactType:', error);
            throw error;
        }
    }
}
// Exportar como singleton global
window.DeleteContactTypeService = new DeleteContactTypeService();
