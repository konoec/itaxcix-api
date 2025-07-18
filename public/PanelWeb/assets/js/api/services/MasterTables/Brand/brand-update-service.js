/**
 * Servicio para actualizar marcas de vehículo
 * Endpoint: PUT /api/v1/admin/brands/{id}
 */
class BrandUpdateService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1/admin/brands';
    }

    /**
     * Actualiza una marca de vehículo existente
     * @param {number} id - ID de la marca
     * @param {Object} data - Datos de la marca
     * @param {string} data.name - Nombre de la marca
     * @param {boolean} data.active - Estado activo/inactivo
     * @returns {Promise<Object>} Respuesta de la API
     */
    async updateBrand(id, data) {
        try {
            console.log('🔄 Actualizando marca:', { id, data });

            // Validar datos de entrada
            const validation = this.validateBrandData(data);
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

            // Parsear JSON o texto de error
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

            // Manejo de errores HTTP específicos
            if (!response.ok) {
                switch (response.status) {
                    case 400:
                        throw new Error(result.message || 'Datos inválidos. Verifique la información ingresada.');
                    case 401:
                        throw new Error('Sesión expirada. Inicie sesión nuevamente.');
                    case 404:
                        throw new Error('Marca no encontrada.');
                    case 409:
                        throw new Error('Ya existe una marca con ese nombre.');
                    case 422:
                        throw new Error('Datos de validación incorrectos. Verifique los campos.');
                    default:
                        throw new Error(result.message || `Error del servidor (${response.status})`);
                }
            }

            // Si la API indica éxito
            if (result.success) {
                // Sobrescribir mensaje 'OK' de la API
                let msg = result.message;
                if (!msg || msg.trim().toUpperCase() === 'OK') {
                    msg = 'Marca actualizada exitosamente';
                }
                console.log('✅ Marca actualizada:', msg);
                return {
                    success: true,
                    message: msg,
                    data: result.data
                };
            } else {
                throw new Error(result.message || 'Error desconocido al actualizar la marca');
            }

        } catch (error) {
            console.error('❌ Error en updateBrand:', error);
            // Re-lanzar errores conocidos
            const known = ['Token', 'Sesión', 'encontrado', 'existe', 'validación'];
            if (known.some(k => error.message.includes(k))) {
                throw error;
            }
            // Error genérico de conexión
            throw new Error('Error de conexión. Verifique su conexión a internet e intente nuevamente.');
        }
    }

    /**
     * Valida los datos de la marca
     * @param {Object} data - Datos a validar
     * @returns {Object} Resultado de la validación
     */
    validateBrandData(data) {
        const errors = [];
        if (!data.name || typeof data.name !== 'string') {
            errors.push('El nombre es requerido');
        } else {
            const trimmed = data.name.trim();
            if (trimmed.length === 0) {
                errors.push('El nombre no puede estar vacío');
            } else if (trimmed.length > 100) {
                errors.push('El nombre no puede exceder 100 caracteres');
            } else if (trimmed.length < 2) {
                errors.push('El nombre debe tener al menos 2 caracteres');
            }
        }
        if (typeof data.active !== 'boolean') {
            errors.push('El estado activo debe ser verdadero o falso');
        }
        return { isValid: errors.length === 0, errors };
    }

    /**
     * Transforma la respuesta de la API al formato esperado por el frontend
     * @param {Object} apiResponse - Respuesta de la API
     * @returns {Object|null} Datos transformados
     */
    transformApiResponse(apiResponse) {
        if (!apiResponse || !apiResponse.data) return null;
        const d = apiResponse.data;
        return {
            id: d.id,
            name: d.name,
            active: Boolean(d.active),
            createdAt: d.created_at || d.createdAt,
            updatedAt: d.updated_at || d.updatedAt
        };
    }

    /**
     * Valida parámetros de entrada
     */
    validateParams(params) {
        return {
            id: parseInt(params.id) || null,
            name: typeof params.name === 'string' ? params.name.trim() : null,
            active: typeof params.active === 'boolean' ? params.active : null
        };
    }
}

// Instancia global
window.BrandUpdateService = new BrandUpdateService();
window.brandUpdateService = window.BrandUpdateService;
console.log('✅ BrandUpdateService cargado y disponible globalmente');
