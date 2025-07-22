/**
 * Servicio para actualizar estado de infracción
 * Endpoint: PUT /api/v1/admin/infraction-statuses/{id}
 */
class InfractionStatusUpdateService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1/admin/infraction-statuses';
    }

    /**
     * Actualiza un estado de infracción existente
     * @param {number} id - ID del estado
     * @param {Object} data - Datos del estado
     * @param {string} data.name - Nombre
     * @param {boolean} data.active - Estado activo/inactivo
     * @returns {Promise<Object>} Respuesta de la API
     */
    async updateInfractionStatus(id, data) {
        try {
            const validation = this.validateInfractionStatusData(data);
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
                if (response.status === 400) {
                    throw new Error(result.message || 'Datos inválidos. Verifique la información ingresada.');
                } else if (response.status === 401) {
                    throw new Error('Sesión expirada. Inicie sesión nuevamente.');
                } else if (response.status === 404) {
                    throw new Error('Estado de infracción no encontrado.');
                } else if (response.status === 409) {
                    throw new Error('Ya existe un estado con ese nombre.');
                } else if (response.status === 422) {
                    throw new Error('Datos de validación incorrectos. Verifique los campos.');
                } else {
                    throw new Error(result.message || `Error del servidor (${response.status})`);
                }
            }
            if (result.success || result === 'ok') {
                return {
                    success: true,
                    message: (result.message || result === 'ok' ? 'Actualizado correctamente' : 'Estado de infracción actualizado correctamente'),
                    data: result.data || null
                };
            } else {
                throw new Error(result.message || 'Error desconocido al actualizar el estado');
            }
        } catch (error) {
            throw error;
        }
    }

    /**
     * Valida los datos del estado
     * @param {Object} data - Datos a validar
     * @returns {Object} Resultado de la validación
     */
    validateInfractionStatusData(data) {
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
window.InfractionStatusUpdateService = new InfractionStatusUpdateService();
window.infractionStatusUpdateService = window.InfractionStatusUpdateService;
console.log('✅ InfractionStatusUpdateService cargado y disponible globalmente');
