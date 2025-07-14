/**
 * Servicio para actualizar estados de usuario
 * Endpoint: PUT /admin/user-statuses/{id}
 * Permite actualizar un estado de usuario existente en el sistema
 * 
 * @author Sistema
 * @version 1.0.0
 */

class UpdateUserStatusService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1/admin/user-statuses';
        console.log('🔧 UpdateUserStatusService inicializado con baseUrl:', this.baseUrl);
    }

    /**
     * Obtiene el token de autenticación
     * @returns {string|null} Token de autenticación
     */
    getAuthToken() {
        return sessionStorage.getItem('token') || 
               sessionStorage.getItem('authToken') || 
               sessionStorage.getItem('userToken');
    }

    /**
     * Actualiza un estado de usuario existente
     * @param {number} id - ID del estado de usuario a actualizar
     * @param {Object} userStatusData - Datos del estado de usuario
     * @param {string} userStatusData.name - Nombre del estado de usuario
     * @param {boolean} userStatusData.active - Si el estado está activo
     * @returns {Promise<Object>} Respuesta de la API
     */
    async updateUserStatus(id, userStatusData) {
        console.log(`🔄 Actualizando estado de usuario ID ${id}:`, userStatusData);
        
        try {
            // Validar ID
            if (!id || !Number.isInteger(Number(id)) || Number(id) <= 0) {
                throw new Error('ID del estado de usuario inválido');
            }

            // Validar datos requeridos
            this.validateUserStatusData(userStatusData);

            // Preparar datos para envío
            const payload = {
                name: userStatusData.name.trim(),
                active: Boolean(userStatusData.active)
            };

            console.log('📤 Enviando payload:', payload);

            // Obtener token de autenticación
            const token = this.getAuthToken();
            if (!token) {
                throw new Error('Token de autenticación no encontrado');
            }

            // Realizar petición PUT
            const response = await fetch(`${this.baseUrl}/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            console.log(`📡 Respuesta del servidor (${response.status}):`, response.statusText);

            // Verificar respuesta
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                const errorMessage = this.getErrorMessage(response.status, errorData);
                console.error('❌ Error en actualización:', errorMessage);
                throw new Error(errorMessage);
            }

            // Procesar respuesta exitosa
            const result = await response.json();
            console.log('✅ Estado de usuario actualizado exitosamente:', result);

            return {
                success: true,
                data: result.data,
                message: 'Estado de usuario actualizado exitosamente'
            };

        } catch (error) {
            console.error('❌ Error al actualizar estado de usuario:', error);
            
            if (error.name === 'TypeError' && error.message.includes('fetch')) {
                throw new Error('Error de conexión. Verifique su conexión a internet.');
            }
            
            throw error;
        }
    }

    /**
     * Valida los datos del estado de usuario
     * @param {Object} userStatusData - Datos a validar
     */
    validateUserStatusData(userStatusData) {
        if (!userStatusData || typeof userStatusData !== 'object') {
            throw new Error('Datos del estado de usuario inválidos');
        }

        // Validar nombre
        if (!userStatusData.name || typeof userStatusData.name !== 'string') {
            throw new Error('El nombre del estado de usuario es requerido');
        }

        if (userStatusData.name.trim().length === 0) {
            throw new Error('El nombre del estado de usuario no puede estar vacío');
        }

        if (userStatusData.name.trim().length > 50) {
            throw new Error('El nombre del estado de usuario no puede exceder 50 caracteres');
        }

        // Validar active (puede ser boolean o string)
        if (userStatusData.active !== undefined && 
            typeof userStatusData.active !== 'boolean' && 
            typeof userStatusData.active !== 'string') {
            throw new Error('El estado activo debe ser un valor boolean');
        }
    }

    /**
     * Obtiene mensaje de error basado en el código de respuesta
     * @param {number} status - Código de estado HTTP
     * @param {Object} errorData - Datos del error del servidor
     * @returns {string} Mensaje de error
     */
    getErrorMessage(status, errorData) {
        // Usar mensaje del servidor si está disponible
        if (errorData.message) {
            return errorData.message;
        }

        // Mensajes por código de estado
        switch (status) {
            case 400:
                return 'Datos inválidos. Verifique la información ingresada.';
            case 401:
                return 'No autorizado. Inicie sesión nuevamente.';
            case 403:
                return 'No tiene permisos para actualizar estados de usuario.';
            case 404:
                return 'Estado de usuario no encontrado.';
            case 409:
                return 'Ya existe un estado de usuario con ese nombre.';
            case 422:
                return 'Datos de entrada inválidos. Verifique los campos.';
            case 500:
                return 'Error interno del servidor. Intente más tarde.';
            default:
                return `Error del servidor (${status}). Intente más tarde.`;
        }
    }

    /**
     * Obtiene un estado de usuario por ID para edición
     * NOTA: Este método no se usa actualmente ya que obtenemos los datos desde la tabla
     * @param {number} id - ID del estado de usuario
     * @returns {Promise<Object>} Datos del estado de usuario
     */
    /*
    async getUserStatusById(id) {
        console.log(`🔍 Obteniendo estado de usuario ID ${id} para edición`);
        
        try {
            if (!id || !Number.isInteger(Number(id)) || Number(id) <= 0) {
                throw new Error('ID del estado de usuario inválido');
            }

            const token = this.getAuthToken();
            if (!token) {
                throw new Error('Token de autenticación no encontrado');
            }

            const response = await fetch(`${this.baseUrl}/${id}`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                const errorMessage = this.getErrorMessage(response.status, errorData);
                throw new Error(errorMessage);
            }

            const result = await response.json();
            console.log('✅ Estado de usuario obtenido:', result);

            return {
                success: true,
                data: result.data
            };

        } catch (error) {
            console.error('❌ Error al obtener estado de usuario:', error);
            throw error;
        }
    }
    */
}

// Hacer disponible globalmente
window.UpdateUserStatusService = UpdateUserStatusService;

// Instancia global
window.updateUserStatusService = new UpdateUserStatusService();

console.log('✅ UpdateUserStatusService cargado y disponible globalmente');
