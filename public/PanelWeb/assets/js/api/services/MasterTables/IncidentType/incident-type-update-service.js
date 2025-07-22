/**
 * Servicio para actualizar tipos de incidencia
 * Endpoint: PUT /api/v1/admin/incident-types/{id}
 */
class IncidentTypeUpdateService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1/admin/incident-types';
    }

    /**
     * Actualiza un tipo de incidencia existente
     * @param {number} id - ID del tipo de incidencia
     * @param {Object} data - Datos del tipo de incidencia
     * @param {string} data.name - Nombre del tipo
     * @param {boolean} data.active - Estado activo/inactivo
     * @returns {Promise<Object>} Respuesta de la API
     */
    async updateIncidentType(id, data) {
        try {
            // Validar datos de entrada
            const validation = this.validateIncidentTypeData(data);
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
                    throw new Error('Tipo de incidencia no encontrado.');
                } else if (response.status === 409) {
                    throw new Error('Ya existe un tipo de incidencia con ese nombre.');
                } else if (response.status === 422) {
                    throw new Error('Datos de validación incorrectos. Verifique los campos.');
                } else {
                    throw new Error(result.message || `Error del servidor (${response.status})`);
                }
            }

            if (result.success) {
                let msg = result.message;
                // Si la API responde solo con 'ok', mostrar mensaje formal
                if (!msg || msg.trim().toLowerCase() === 'ok') {
                    msg = 'Tipo de incidencia actualizado correctamente';
                }
                return {
                    success: true,
                    message: msg,
                    data: result.data
                };
            } else {
                throw new Error(result.message || 'Error desconocido al actualizar el tipo de incidencia');
            }

        } catch (error) {
            throw error;
        }
    }

    /**
     * Valida los datos del tipo de incidencia
     * @param {Object} data - Datos a validar
     * @returns {Object} Resultado de la validación
     */
    validateIncidentTypeData(data) {
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
window.IncidentTypeUpdateService = new IncidentTypeUpdateService();
window.incidentTypeUpdateService = window.IncidentTypeUpdateService;
