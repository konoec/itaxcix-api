/**
 * Servicio para actualizar tipos de servicio
 * Endpoint: PUT /api/v1/admin/service-types/{id}
 */
class ServiceTypeUpdateService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1/admin/service-types';
    }

    /**
     * Actualiza un tipo de servicio existente
     * @param {number} id - ID del tipo de servicio
     * @param {Object} data - Datos del tipo de servicio
     * @param {string} data.name - Nombre del tipo de servicio
     * @param {boolean} data.active - Estado activo/inactivo
     * @returns {Promise<Object>} Respuesta de la API
     */
    async updateServiceType(id, data) {
        try {
            console.log('🔄 Actualizando tipo de servicio:', { id, data });

            // Validar datos de entrada
            const validation = this.validateServiceTypeData(data);
            if (!validation.isValid) {
                throw new Error(validation.errors.join(', '));
            }

            // Obtener token de autenticación
            const token = sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
            if (!token) {
                throw new Error('Token de autenticación no encontrado');
            }

            // Preparar datos para el API
            const requestData = {
                id: parseInt(id),
                name: data.name.trim(),
                active: Boolean(data.active)
            };

            console.log('📤 Datos a enviar:', requestData);

            // Realizar petición
            const response = await fetch(`${this.baseUrl}/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(requestData)
            });

            console.log('📥 Respuesta HTTP:', response.status);

            // Verificar si la respuesta es JSON válida
            let result;
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                result = await response.json();
            } else {
                const textResponse = await response.text();
                console.error('❌ Respuesta no-JSON:', textResponse);
                throw new Error(`Error del servidor (${response.status}): Respuesta inválida`);
            }

            console.log('📋 Datos de respuesta:', result);

            if (!response.ok) {
                // Manejar diferentes tipos de errores
                if (response.status === 400) {
                    throw new Error(result.message || 'Datos inválidos. Verifique la información ingresada.');
                } else if (response.status === 401) {
                    throw new Error('Sesión expirada. Inicie sesión nuevamente.');
                } else if (response.status === 404) {
                    throw new Error('Tipo de servicio no encontrado.');
                } else if (response.status === 409) {
                    throw new Error('Ya existe un tipo de servicio con ese nombre.');
                } else if (response.status === 422) {
                    throw new Error('Datos de validación incorrectos. Verifique los campos.');
                } else {
                    throw new Error(result.message || `Error del servidor (${response.status})`);
                }
            }

            if (result.success) {
                console.log('✅ Tipo de servicio actualizado exitosamente');
                return {
                    success: true,
                    message: result.message || 'Tipo de servicio actualizado correctamente',
                    data: result.data
                };
            } else {
                throw new Error(result.message || 'Error desconocido al actualizar el tipo de servicio');
            }

        } catch (error) {
            console.error('❌ Error en updateServiceType:', error);
            // Re-lanzar errores conocidos
            if (error.message.includes('Token') || 
                error.message.includes('Sesión') ||
                error.message.includes('encontrado') ||
                error.message.includes('existe') ||
                error.message.includes('validación')) {
                throw error;
            }
            // Error genérico para otros casos
            throw new Error('Error de conexión. Verifique su conexión a internet e intente nuevamente.');
        }
    }

    /**
     * Valida los datos del tipo de servicio
     * @param {Object} data - Datos a validar
     * @returns {Object} Resultado de la validación
     */
    validateServiceTypeData(data) {
        const errors = [];
        // Validar nombre
        if (!data.name || typeof data.name !== 'string') {
            errors.push('El nombre es requerido');
        } else {
            const trimmedName = data.name.trim();
            if (trimmedName.length === 0) {
                errors.push('El nombre no puede estar vacío');
            } else if (trimmedName.length > 100) {
                errors.push('El nombre no puede exceder 100 caracteres');
            } else if (trimmedName.length < 2) {
                errors.push('El nombre debe tener al menos 2 caracteres');
            }
        }
        // Validar estado activo
        if (typeof data.active !== 'boolean') {
            errors.push('El estado activo debe ser verdadero o falso');
        }
        return {
            isValid: errors.length === 0,
            errors: errors
        };
    }

    /**
     * Transforma la respuesta de la API al formato esperado por el frontend
     * @param {Object} apiResponse - Respuesta de la API
     * @returns {Object} Datos transformados
     */
    transformApiResponse(apiResponse) {
        if (!apiResponse || !apiResponse.data) {
            return null;
        }
        const data = apiResponse.data;
        return {
            id: data.id,
            name: data.name,
            active: Boolean(data.active),
            createdAt: data.created_at || data.createdAt,
            updatedAt: data.updated_at || data.updatedAt
        };
    }

    /**
     * Valida parámetros de entrada
     * @param {Object} params - Parámetros a validar
     * @returns {Object} Parámetros validados
     */
    validateParams(params) {
        return {
            id: parseInt(params.id) || null,
            name: typeof params.name === 'string' ? params.name.trim() : null,
            active: typeof params.active === 'boolean' ? params.active : null
        };
    }
}
// Crear instancia global
window.ServiceTypeUpdateService = new ServiceTypeUpdateService();
window.serviceTypeUpdateService = window.ServiceTypeUpdateService;
console.log('✅ ServiceTypeUpdateService cargado y disponible globalmente');
