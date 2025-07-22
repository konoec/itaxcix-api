/**
 * Servicio para actualizar estados de viaje
 * Endpoint: PUT /api/v1/admin/travel-statuses/{id}
 */
class TravelStatusUpdateService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1/admin/travel-statuses';
    }

    /**
     * Actualiza un estado de viaje existente
     * @param {number} id - ID del estado de viaje
     * @param {Object} data - Datos del estado de viaje
     * @param {string} data.name - Nombre del estado
     * @param {boolean} data.active - Estado activo/inactivo
     * @returns {Promise<Object>} Respuesta de la API
     */
    async updateTravelStatus(id, data) {
        try {
            // Validar datos de entrada
            const validation = this.validateTravelStatusData(data);
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
                    throw new Error('Estado de viaje no encontrado.');
                } else if (response.status === 409) {
                    throw new Error('Ya existe un estado de viaje con ese nombre.');
                } else if (response.status === 422) {
                    throw new Error('Datos de validación incorrectos. Verifique los campos.');
                } else {
                    throw new Error(result.message || `Error del servidor (${response.status})`);
                }
            }

            if (result.success) {
                return {
                    success: true,
                    message: 'Estado de viaje actualizado correctamente',
                    data: result.data
                };
            } else {
                throw new Error(result.message || 'Error desconocido al actualizar el estado de viaje');
            }

        } catch (error) {
            throw error;
        }
    }

    /**
     * Valida los datos del estado de viaje
     * @param {Object} data - Datos a validar
     * @returns {Object} Resultado de la validación
     */
    validateTravelStatusData(data) {
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
window.TravelStatusUpdateService = new TravelStatusUpdateService();
window.travelStatusUpdateService = window.TravelStatusUpdateService;
console.log('✅ TravelStatusUpdateService cargado y disponible globalmente');
