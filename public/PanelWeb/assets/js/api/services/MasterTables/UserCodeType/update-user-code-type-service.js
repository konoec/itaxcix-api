/**
 * Servicio para actualizar tipos de código de usuario
 * Maneja la comunicación con la API para actualización de tipos
 */
class UpdateUserCodeTypeService {
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
     * Valida los datos antes del envío
     * @param {Object} data - Datos a validar
     * @returns {Object} Resultado de validación
     */
    validateData(data) {
        const errors = [];

        if (!data.name || typeof data.name !== 'string' || data.name.trim() === '') {
            errors.push('El nombre es requerido');
        }

        if (data.name && data.name.trim().length > 50) {
            errors.push('El nombre no puede exceder 50 caracteres');
        }

        if (typeof data.active !== 'boolean') {
            errors.push('El estado activo debe ser verdadero o falso');
        }

        return {
            isValid: errors.length === 0,
            errors
        };
    }

    /**
     * Actualiza un tipo de código de usuario
     * @param {number} id - ID del tipo de código de usuario
     * @param {Object} data - Datos del tipo de código de usuario
     * @param {string} data.name - Nombre del tipo
     * @param {boolean} data.active - Estado activo
     * @returns {Promise<Object>} Respuesta de la API
     */
    async updateUserCodeType(id, data) {
        try {
            console.log('🔄 Actualizando tipo de código de usuario:', { id, data });

            // Validar ID
            if (!id || typeof id !== 'number' || id <= 0) {
                throw new Error('ID del tipo de código de usuario es requerido y debe ser un número válido');
            }

            // Validar datos
            const validation = this.validateData(data);
            if (!validation.isValid) {
                throw new Error(validation.errors.join(', '));
            }

            // Obtener token de autenticación
            const token = this.getAuthToken();
            if (!token) {
                throw new Error('Token de autenticación requerido');
            }

            // Preparar datos para envío
            const requestBody = {
                name: data.name.trim(),
                active: data.active
            };

            // Realizar petición HTTP
            const response = await fetch(`${this.baseUrl}${this.endpoint}/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(requestBody)
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
                throw new Error(responseData.message || 'Error al actualizar tipo de código de usuario');
            }

            console.log('✅ Tipo de código de usuario actualizado exitosamente:', responseData);
            return responseData;

        } catch (error) {
            console.error('❌ Error en updateUserCodeType:', error);
            throw error;
        }
    }

    /**
     * Obtiene un tipo de código de usuario específico (fallback si no se tienen datos)
     * @param {number} id - ID del tipo de código de usuario
     * @returns {Promise<Object>} Datos del tipo de código de usuario
     */
    async getUserCodeType(id) {
        try {
            console.log('📡 Obteniendo tipo de código de usuario:', id);

            if (!id || typeof id !== 'number' || id <= 0) {
                throw new Error('ID del tipo de código de usuario es requerido');
            }

            const token = this.getAuthToken();
            if (!token) {
                throw new Error('Token de autenticación requerido');
            }

            const response = await fetch(`${this.baseUrl}${this.endpoint}/${id}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            if (!response.ok) {
                throw new Error(`Error HTTP ${response.status}: ${response.statusText}`);
            }

            const responseData = await response.json();

            if (!responseData.success) {
                throw new Error(responseData.message || 'Error al obtener tipo de código de usuario');
            }

            console.log('✅ Tipo de código de usuario obtenido:', responseData);
            return responseData;

        } catch (error) {
            console.error('❌ Error en getUserCodeType:', error);
            throw error;
        }
    }
}

// Exportar como instancia singleton
window.UpdateUserCodeTypeService = new UpdateUserCodeTypeService();
