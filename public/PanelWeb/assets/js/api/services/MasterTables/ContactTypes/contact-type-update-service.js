/**
 * Servicio para actualizar tipos de contacto
 * Endpoint: PUT /api/v1/admin/contact-types/{id}
 */
class ContactTypeUpdateService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1/admin/contact-types';
    }

    /**
     * Actualiza un tipo de contacto existente
     * @param {number} id - ID del tipo de contacto
     * @param {Object} data - Datos del tipo de contacto
     * @param {string} data.name - Nombre del tipo
     * @param {boolean} data.active - Estado activo/inactivo
     * @returns {Promise<Object>} Respuesta de la API
     */
    async updateContactType(id, data) {
        try {
            // Validar datos de entrada
            const validation = this.validateContactTypeData(data);
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

            let result;
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                result = await response.json();
            } else {
                const textResponse = await response.text();
                throw new Error(`Error del servidor (${response.status}): Respuesta inválida`);
            }

            if (!response.ok) {
                if (response.status === 400) {
                    throw new Error(result.message || 'Datos inválidos. Verifique la información ingresada.');
                } else if (response.status === 401) {
                    throw new Error('Sesión expirada. Inicie sesión nuevamente.');
                } else if (response.status === 404) {
                    throw new Error('Tipo de contacto no encontrado.');
                } else if (response.status === 409) {
                    throw new Error('Ya existe un tipo de contacto con ese nombre.');
                } else if (response.status === 422) {
                    throw new Error('Datos de validación incorrectos. Verifique los campos.');
                } else {
                    throw new Error(result.message || `Error del servidor (${response.status})`);
                }
            }

            if (result.success) {
                return {
                    success: true,
                    message: result.message || 'Tipo de contacto actualizado correctamente',
                    data: result.data
                };
            } else {
                throw new Error(result.message || 'Error desconocido al actualizar el tipo de contacto');
            }
        } catch (error) {
            throw error;
        }
    }

    /**
     * Valida los datos del tipo de contacto
     * @param {Object} data - Datos a validar
     * @returns {Object} Resultado de la validación
     */
    validateContactTypeData(data) {
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
}

window.ContactTypeUpdateService = new ContactTypeUpdateService();
window.contactTypeUpdateService = window.ContactTypeUpdateService;

console.log('✅ ContactTypeUpdateService cargado y disponible globalmente');
