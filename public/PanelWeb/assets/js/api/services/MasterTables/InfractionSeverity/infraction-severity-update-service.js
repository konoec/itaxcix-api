/**
 * Servicio para actualizar severidades de infracción
 * Endpoint: PUT /api/v1/admin/infraction-severities/{id}
 */
class InfractionSeverityUpdateService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1/admin/infraction-severities';
    }

    /**
     * Actualiza una severidad de infracción existente
     * @param {number} id - ID de la severidad
     * @param {Object} data - Datos de la severidad
     * @param {string} data.name - Nombre
     * @param {boolean} data.active - Estado activo/inactivo
     * @returns {Promise<Object>} Respuesta de la API
     */
    async updateInfractionSeverity(id, data) {
        try {
            // Validar datos
            const validation = this.validateInfractionSeverityData(data);
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
                throw new Error(`Error del servidor (${response.status}): Respuesta inválida`);
            }

            if (!response.ok) {
                if (response.status === 400) {
                    throw new Error(result.message || 'Datos inválidos. Verifique la información ingresada.');
                } else if (response.status === 401) {
                    throw new Error('Sesión expirada. Inicie sesión nuevamente.');
                } else if (response.status === 404) {
                    throw new Error('Severidad de infracción no encontrada.');
                } else if (response.status === 409) {
                    throw new Error('Ya existe una severidad con ese nombre.');
                } else if (response.status === 422) {
                    throw new Error('Datos de validación incorrectos. Verifique los campos.');
                } else {
                    throw new Error(result.message || `Error del servidor (${response.status})`);
                }
            }

            if (result.success || result === 'ok') {
                return {
                    success: true,
                    message: (result.message || result === 'ok' ? 'Actualizado correctamente' : 'Severidad de infracción actualizada correctamente'),
                    data: result.data || null
                };
            } else {
                throw new Error(result.message || 'Error desconocido al actualizar la severidad');
            }
        } catch (error) {
            throw error;
        }
    }

    /**
     * Valida los datos de la severidad
     * @param {Object} data - Datos a validar
     * @returns {Object} Resultado de la validación
     */
    validateInfractionSeverityData(data) {
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
window.InfractionSeverityUpdateService = new InfractionSeverityUpdateService();
window.infractionSeverityUpdateService = window.InfractionSeverityUpdateService;
console.log('✅ InfractionSeverityUpdateService cargado y disponible globalmente');
