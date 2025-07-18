/**
 * Servicio para eliminar tipos de código de usuario
 * Maneja la comunicación con la API para eliminación de tipos
 */
class DeleteUserCodeTypeService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.endpoint = '/admin/user-code-types';
    }

    /**
     * Obtiene el token de autenticación del sessionStorage
     * @returns {string|null} Token de autenticación
     */
    getAuthToken() {
        return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
    }

    /**
     * Valida los datos para eliminación
     * @param {Object} data - Datos a validar
     * @returns {Object} Resultado de validación
     */
    validateDeletion(data) {
        const errors = [];

        if (!data.id || typeof data.id !== 'number' || data.id <= 0) {
            errors.push('ID del tipo de código de usuario es requerido');
        }

        if (!data.name || typeof data.name !== 'string' || data.name.trim() === '') {
            errors.push('Nombre del tipo de código de usuario es requerido para confirmación');
        }

        return {
            isValid: errors.length === 0,
            errors
        };
    }

    /**
     * Obtiene la configuración del modal de confirmación
     * @param {Object} data - Datos del tipo de código de usuario
     * @returns {Object} Configuración del modal
     */
    getConfirmationConfig(data) {
        return {
            title: 'Eliminar Tipo de Código de Usuario',
            details: `¿Está seguro de que desea eliminar el tipo "${data.name}"?`,
            subtitle: 'Esta acción no se puede deshacer y podría afectar a los usuarios que tengan asignado este tipo de código.',
            confirmText: 'Sí, eliminar',
            cancelText: 'Cancelar',
            confirmClass: 'btn-danger',
            icon: 'fas fa-exclamation-triangle',
            iconClass: 'text-danger'
        };
    }

    /**
     * Elimina un tipo de código de usuario
     * @param {number} id - ID del tipo de código de usuario
     * @returns {Promise<Object>} Respuesta de la API
     */
    async deleteUserCodeType(id) {
        try {
            console.log('🗑️ Eliminando tipo de código de usuario:', id);

            // Validar ID
            if (!id || typeof id !== 'number' || id <= 0) {
                throw new Error('ID del tipo de código de usuario es requerido y debe ser un número válido');
            }

            // Obtener token de autenticación
            const token = this.getAuthToken();
            if (!token) {
                throw new Error('Token de autenticación requerido');
            }

            // Realizar petición HTTP
            const response = await fetch(`${this.baseUrl}${this.endpoint}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            // Verificar respuesta HTTP
            if (!response.ok) {
                let errorMessage = `Error HTTP ${response.status}: ${response.statusText}`;
                
                try {
                    const errorData = await response.json();
                    if (errorData.message) {
                        errorMessage = errorData.message;
                    }
                } catch (e) {
                    // Si no se puede parsear la respuesta de error, usar mensaje por defecto
                }
                
                throw new Error(errorMessage);
            }

            // Parsear respuesta
            const responseData = await response.json();

            // Verificar estructura de respuesta
            if (!responseData.success) {
                throw new Error(responseData.message || 'Error al eliminar tipo de código de usuario');
            }

            console.log('✅ Tipo de código de usuario eliminado exitosamente:', responseData);
            return responseData;

        } catch (error) {
            console.error('❌ Error en deleteUserCodeType:', error);
            throw error;
        }
    }
}

// Exportar como instancia singleton
window.DeleteUserCodeTypeService = new DeleteUserCodeTypeService();
