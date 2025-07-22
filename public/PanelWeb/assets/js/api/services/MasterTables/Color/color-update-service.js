/**
 * Servicio para actualizar colores
 * Endpoint: PUT /api/v1/admin/colors/{id}
 */
class ColorUpdateService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1/admin/colors';
    }

    /**
     * Actualiza un color existente
     * @param {number} id - ID del color
     * @param {Object} data - Datos del color
     * @param {string} data.name - Nombre del color
     * @param {boolean} data.active - Estado activo/inactivo
     * @returns {Promise<Object>} Respuesta de la API
     */
    async updateColor(id, data) {
        try {
            console.log('🔄 Actualizando color:', { id, data });
            const validation = this.validateColorData(data);
            if (!validation.isValid) {
                throw new Error(validation.errors.join(', '));
            }
            const token = sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
            if (!token) {
                throw new Error('Token de autenticación no encontrado');
            }
            const requestData = {
                id: parseInt(id),
                name: data.name.trim(),
                active: Boolean(data.active)
            };
            const response = await fetch(`${this.baseUrl}/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(requestData)
            });
            let result;
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                result = await response.json();
            } else {
                throw new Error(`Error del servidor (${response.status}): Respuesta inválida`);
            }
            if (!response.ok) {
                throw new Error(result.message || `Error del servidor (${response.status})`);
            }
            if (result.success) {
                return {
                    success: true,
                    message: 'Color actualizado correctamente',
                    data: result.data
                };
            } else {
                throw new Error(result.message || 'Error desconocido al actualizar el color');
            }
        } catch (error) {
            throw error;
        }
    }

    /**
     * Valida los datos del color
     * @param {Object} data - Datos a validar
     * @returns {Object} Resultado de la validación
     */
    validateColorData(data) {
        const errors = [];
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
}
window.ColorUpdateService = new ColorUpdateService();
window.colorUpdateService = window.ColorUpdateService;
console.log('✅ ColorUpdateService cargado y disponible globalmente');
