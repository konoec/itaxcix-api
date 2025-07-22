/**
 * Fuel Type Update Service
 * Servicio para actualizar tipos de combustible existentes
 *
 * Funciones principales:
 * - Actualización de tipo de combustible por ID
 * - Validación de datos antes de actualizar
 * - Manejo de errores específicos
 */

class FuelTypeUpdateService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.endpoint = '/admin/fuel-types';
        console.log('🔄 Inicializando FuelTypeUpdateService...');
    }

    /**
     * Actualiza un tipo de combustible existente
     * @param {number} id - ID del tipo de combustible
     * @param {Object} fuelTypeData - Datos del tipo de combustible a actualizar
     * @param {string} fuelTypeData.name - Nombre del tipo de combustible
     * @param {boolean} fuelTypeData.active - Estado activo/inactivo
     * @returns {Promise<Object>} Respuesta de la API
     */
    async updateFuelType(id, fuelTypeData) {
        console.log(`🔄 Actualizando tipo de combustible ${id}:`, fuelTypeData);
        try {
            // Validar datos antes de enviar
            const validationResult = this.validateFuelTypeData(fuelTypeData);
            if (!validationResult.isValid) {
                throw new Error(validationResult.message);
            }

            const token = sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
            if (!token) {
                throw new Error('No hay token de autenticación');
            }

            // Preparar datos para envío
            const payload = {
                name: fuelTypeData.name.trim(),
                active: !!fuelTypeData.active
            };

            console.log('📤 Payload a enviar:', payload);

            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 segundos para updates

            try {
                const response = await fetch(`${this.baseUrl}${this.endpoint}/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload),
                    signal: controller.signal
                });

                clearTimeout(timeoutId);

                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(`HTTP ${response.status}: ${errorData.message || 'Error al actualizar tipo de combustible'}`);
                }

                const result = await response.json();
                console.log('✅ Tipo de combustible actualizado exitosamente:', result);
                return result;
            } catch (fetchError) {
                clearTimeout(timeoutId);
                if (fetchError.name === 'AbortError') {
                    throw new Error('La solicitud tardó demasiado en responder');
                }
                throw fetchError;
            }
        } catch (error) {
            console.error('❌ Error actualizando tipo de combustible:', error);
            // Manejo específico de errores comunes
            if (error.message.includes('404')) {
                throw new Error('El tipo de combustible no existe');
            } else if (error.message.includes('403')) {
                throw new Error('No tienes permisos para actualizar este tipo de combustible');
            } else if (error.message.includes('409')) {
                throw new Error('Ya existe un tipo de combustible con estos datos');
            } else if (error.message.includes('422')) {
                throw new Error('Los datos proporcionados no son válidos');
            } else if (error.message.includes('500')) {
                throw new Error('Error interno del servidor. Inténtelo más tarde');
            }
            throw error;
        }
    }

    /**
     * Valida los datos del tipo de combustible antes de actualizar
     * @param {Object} fuelTypeData - Datos a validar
     * @returns {Object} Resultado de la validación
     */
    validateFuelTypeData(fuelTypeData) {
        console.log('🔍 Validando datos del tipo de combustible:', fuelTypeData);
        if (!fuelTypeData) {
            return {
                isValid: false,
                message: 'No se proporcionaron datos del tipo de combustible'
            };
        }
        if (!fuelTypeData.name || typeof fuelTypeData.name !== 'string') {
            return {
                isValid: false,
                message: 'El nombre del tipo de combustible es requerido'
            };
        }
        const trimmedName = fuelTypeData.name.trim();
        if (trimmedName.length === 0) {
            return {
                isValid: false,
                message: 'El nombre del tipo de combustible no puede estar vacío'
            };
        }
        if (trimmedName.length > 100) {
            return {
                isValid: false,
                message: 'El nombre no puede exceder 100 caracteres'
            };
        }
        if (typeof fuelTypeData.active !== 'boolean') {
            return {
                isValid: false,
                message: 'El estado activo/inactivo es requerido'
            };
        }
        console.log('✅ Validación exitosa');
        return {
            isValid: true,
            message: 'Datos válidos'
        };
    }
}

// Exportar para uso global
window.FuelTypeUpdateService = FuelTypeUpdateService;
window.FuelTypeUpdateService = new FuelTypeUpdateService();
console.log('✅ FuelTypeUpdateService cargado correctamente');
