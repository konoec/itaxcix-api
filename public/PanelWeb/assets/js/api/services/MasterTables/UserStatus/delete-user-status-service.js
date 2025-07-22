/**
 * Delete User Status Service
 * Servicio para eliminar estados de usuario del sistema
 */

class DeleteUserStatusService {
    constructor() {
        this.baseURL = 'https://149.130.161.148/api/v1/admin/user-statuses';
        console.log('🗑️ DeleteUserStatusService inicializado');
    }

    /**
     * Elimina un estado de usuario por ID
     * @param {number} id - ID del estado de usuario a eliminar
     * @returns {Promise<Object>} Resultado de la operación
     */
    async deleteUserStatus(id) {
        try {
            console.log(`🗑️ Eliminando estado de usuario con ID: ${id}`);

            if (!id || isNaN(id)) {
                throw new Error('ID de estado de usuario inválido');
            }

            // Obtener token de autenticación
            const token = sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
            if (!token) {
                throw new Error('Token de autenticación no encontrado');
            }

            // Realizar petición DELETE
            const response = await fetch(`${this.baseURL}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            const result = await response.json();
            console.log('📄 Datos de respuesta:', result);

            if (response.ok && result.success) {
                return {
                    success: true,
                    message: result.message || 'Estado de usuario eliminado exitosamente',
                    data: result.data || null
                };
            } else {
                const errorMessage = result.message || 'Error al eliminar el estado de usuario';
                return {
                    success: false,
                    message: errorMessage,
                    errors: result.errors || []
                };
            }

        } catch (error) {
            console.error('❌ Error en deleteUserStatus:', error);
            if (error.name === 'TypeError' && error.message.includes('fetch')) {
                return {
                    success: false,
                    message: 'Error de conexión. Verifique su internet e intente nuevamente.'
                };
            }
            return {
                success: false,
                message: error.message || 'Error interno del sistema'
            };
        }
    }

    /**
     * Valida si un estado de usuario puede ser eliminado
     * @param {Object} userStatusData - Datos del estado de usuario
     * @returns {Object} Resultado de la validación
     */
    validateDeletion(userStatusData) {
        const errors = [];

        if (!userStatusData) {
            errors.push('No se proporcionaron datos del estado de usuario');
            return { isValid: false, errors };
        }

        if (!userStatusData.id) {
            errors.push('ID del estado de usuario es requerido');
        }

        // Ejemplo de protección adicional: no permitir eliminar "Activo"
        if (userStatusData.name && userStatusData.name.toLowerCase() === 'activo') {
            errors.push('No se puede eliminar el estado "Activo" del sistema');
        }

        return {
            isValid: errors.length === 0,
            errors
        };
    }

    /**
     * Configuración para el modal de confirmación de eliminación
     * @param {Object} userStatusData - Datos del estado de usuario
     * @returns {Object} Configuración del modal
     */
    getConfirmationConfig(userStatusData) {
        if (!userStatusData) {
            return {
                title: 'Eliminar Estado de Usuario',
                details: '¿Está seguro de que desea eliminar este estado de usuario?',
                confirmText: 'Sí, Eliminar',
                cancelText: 'Cancelar',
                icon: 'fas fa-trash-alt'
            };
        }

        return {
            title: 'Confirmar Eliminación',
            details: `¿Está seguro de que desea eliminar el estado "${userStatusData.name}"? Esta acción no se puede deshacer y podría afectar a los usuarios que tengan asignado este estado.`,
            confirmText: 'Sí, Eliminar',
            cancelText: 'Cancelar',
            icon: 'fas fa-trash-alt'
        };
    }
}

// Instancia global
window.DeleteUserStatusService = new DeleteUserStatusService();

console.log('✅ DeleteUserStatusService cargado correctamente');
