/**
 * Servicio para eliminar elementos del centro de ayuda
 * Maneja la eliminación de elementos mediante la API
 */
class DeleteHelpCenterService {
    static get API_BASE_URL() {
        return 'https://149.130.161.148/api/v1';
    }

    /**
     * Obtiene el token de autenticación del sessionStorage
     */
    static getAuthToken() {
        return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
    }

    /**
     * Elimina un elemento del centro de ayuda
     * @param {number} itemId - ID del elemento a eliminar
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async deleteHelpCenterItem(itemId) {
        try {
            console.log(`🗑️ Eliminando elemento del centro de ayuda ID: ${itemId}`);

            // Validar ID
            if (!itemId || typeof itemId !== 'number' || itemId <= 0) {
                throw new Error('ID del elemento es requerido y debe ser un número válido');
            }

            const token = this.getAuthToken();
            if (!token) {
                throw new Error('Token de autenticación requerido');
            }

            const response = await fetch(`${this.API_BASE_URL}/help-center/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || `Error HTTP ${response.status}: ${response.statusText}`);
            }

            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.message || 'Error al eliminar elemento del centro de ayuda');
            }

            console.log('✅ Elemento del centro de ayuda eliminado correctamente');
            return result;

        } catch (error) {
            console.error('❌ Error en deleteHelpCenterItem:', error);
            throw error;
        }
    }
}

// Exportar servicio globalmente
window.DeleteHelpCenterService = DeleteHelpCenterService;
