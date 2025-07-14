/**
 * District Update Service
 * Servicio para actualizar distritos existentes
 * 
 * Funciones principales:
 * - Actualización de distritos por ID
 * - Validación de datos antes de actualizar
 * - Manejo de errores específicos
 */

class DistrictUpdateService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.endpoint = '/admin/districts';
        
        console.log('🔄 Inicializando DistrictUpdateService...');
    }

    /**
     * Actualiza un distrito existente
     * @param {number} id - ID del distrito
     * @param {Object} districtData - Datos del distrito a actualizar
     * @param {string} districtData.name - Nombre del distrito
     * @param {number} districtData.provinceId - ID de la provincia
     * @param {string} districtData.ubigeo - Código UBIGEO (6 dígitos)
     * @returns {Promise<Object>} Respuesta de la API
     */
    async updateDistrict(id, districtData) {
        console.log(`🔄 Actualizando distrito ${id}:`, districtData);

        try {
            // Validar datos antes de enviar
            const validationResult = this.validateDistrictData(districtData);
            if (!validationResult.isValid) {
                throw new Error(validationResult.message);
            }

            const token = sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
            if (!token) {
                throw new Error('No hay token de autenticación');
            }

            // Preparar datos para envío
            const payload = {
                name: districtData.name.trim(),
                provinceId: parseInt(districtData.provinceId),
                ubigeo: districtData.ubigeo.trim()
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
                    throw new Error(`HTTP ${response.status}: ${errorData.message || 'Error al actualizar distrito'}`);
                }

                const result = await response.json();
                console.log('✅ Distrito actualizado exitosamente:', result);

                return result;

            } catch (fetchError) {
                clearTimeout(timeoutId);
                
                if (fetchError.name === 'AbortError') {
                    throw new Error('La solicitud tardó demasiado en responder');
                }
                throw fetchError;
            }

        } catch (error) {
            console.error('❌ Error actualizando distrito:', error);
            
            // Manejo específico de errores comunes
            if (error.message.includes('404')) {
                throw new Error('El distrito no existe');
            } else if (error.message.includes('403')) {
                throw new Error('No tienes permisos para actualizar este distrito');
            } else if (error.message.includes('409')) {
                throw new Error('Ya existe un distrito con estos datos');
            } else if (error.message.includes('422')) {
                throw new Error('Los datos proporcionados no son válidos');
            } else if (error.message.includes('500')) {
                throw new Error('Error interno del servidor. Inténtelo más tarde');
            }
            
            throw error;
        }
    }

    /**
     * Valida los datos del distrito antes de actualizar
     * @param {Object} districtData - Datos a validar
     * @returns {Object} Resultado de la validación
     */
    validateDistrictData(districtData) {
        console.log('🔍 Validando datos del distrito:', districtData);

        // Validar que existan los campos requeridos
        if (!districtData) {
            return {
                isValid: false,
                message: 'No se proporcionaron datos del distrito'
            };
        }

        // Validar nombre
        if (!districtData.name || typeof districtData.name !== 'string') {
            return {
                isValid: false,
                message: 'El nombre del distrito es requerido'
            };
        }

        const trimmedName = districtData.name.trim();
        if (trimmedName.length === 0) {
            return {
                isValid: false,
                message: 'El nombre del distrito no puede estar vacío'
            };
        }

        if (trimmedName.length > 100) {
            return {
                isValid: false,
                message: 'El nombre del distrito no puede exceder 100 caracteres'
            };
        }

        // Validar provinceId
        if (!districtData.provinceId) {
            return {
                isValid: false,
                message: 'La provincia es requerida'
            };
        }

        const provinceId = parseInt(districtData.provinceId);
        if (isNaN(provinceId) || provinceId <= 0) {
            return {
                isValid: false,
                message: 'ID de provincia inválido'
            };
        }

        // Validar UBIGEO
        if (!districtData.ubigeo || typeof districtData.ubigeo !== 'string') {
            return {
                isValid: false,
                message: 'El código UBIGEO es requerido'
            };
        }

        const trimmedUbigeo = districtData.ubigeo.trim();
        if (!/^[0-9]{6}$/.test(trimmedUbigeo)) {
            return {
                isValid: false,
                message: 'El código UBIGEO debe tener exactamente 6 dígitos numéricos'
            };
        }

        console.log('✅ Validación exitosa');
        return {
            isValid: true,
            message: 'Datos válidos'
        };
    }

    /**
     * Obtiene el historial de cambios de un distrito (si está disponible)
     * @param {number} id - ID del distrito
     * @returns {Promise<Object>} Historial de cambios
     */
    async getDistrictHistory(id) {
        console.log(`📜 Obteniendo historial del distrito: ${id}`);

        try {
            // Nota: Este endpoint podría no estar disponible en todas las APIs
            // Es una funcionalidad opcional
            const token = sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
            if (!token) {
                throw new Error('No hay token de autenticación');
            }

            const response = await fetch(`${this.baseUrl}${this.endpoint}/${id}/history`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                // Si no existe el endpoint de historial, devolver array vacío
                if (response.status === 404) {
                    return { data: [] };
                }
                throw new Error('Error al obtener historial');
            }

            const result = await response.json();
            return result;

        } catch (error) {
            console.warn('⚠️ No se pudo obtener historial:', error.message);
            return { data: [] };
        }
    }
}

// Exportar para uso global
window.DistrictUpdateService = DistrictUpdateService;

console.log('✅ DistrictUpdateService cargado correctamente');
