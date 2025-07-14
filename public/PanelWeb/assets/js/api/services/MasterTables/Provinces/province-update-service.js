/**
 * Province Update Service
 * Servicio para actualizar provincias existentes
 * Endpoint: PUT /api/v1/admin/provinces/{id}
 */

class ProvinceUpdateService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1/admin/provinces';
        console.log('🔧 ProvinceUpdateService inicializado');
    }

    /**
     * Actualiza una provincia existente
     * @param {number} provinceId - ID de la provincia a actualizar
     * @param {Object} provinceData - Datos de la provincia
     * @param {string} provinceData.name - Nombre de la provincia
     * @param {number} provinceData.departmentId - ID del departamento
     * @param {string} provinceData.ubigeo - Código ubigeo
     * @returns {Promise<Object>} Respuesta de la API
     */
    async updateProvince(provinceId, provinceData) {
        try {
            console.log('📤 Actualizando provincia:', provinceId, provinceData);

            // Validar parámetros requeridos
            if (!provinceId) {
                throw new Error('ID de provincia es requerido');
            }

            if (!provinceData || typeof provinceData !== 'object') {
                throw new Error('Datos de provincia son requeridos');
            }

            // Validar campos requeridos
            const { name, departmentId, ubigeo } = provinceData;
            
            if (!name || name.trim().length === 0) {
                throw new Error('El nombre de la provincia es requerido');
            }

            if (!departmentId || !Number.isInteger(Number(departmentId))) {
                throw new Error('ID del departamento es requerido y debe ser un número');
            }

            if (!ubigeo || !/^[0-9]{4}$/.test(ubigeo.toString())) {
                throw new Error('El código ubigeo debe tener exactamente 4 dígitos');
            }

            // Preparar datos para envío
            const requestBody = {
                name: name.trim(),
                departmentId: Number(departmentId),
                ubigeo: ubigeo.toString()
            };

            console.log('📋 Datos a enviar:', requestBody);

            // Obtener token de autenticación
            const token = sessionStorage.getItem('token') || 
                         sessionStorage.getItem('authToken') || 
                         sessionStorage.getItem('userToken');

            if (!token) {
                throw new Error('Token de autenticación no encontrado');
            }

            // Realizar petición a la API
            const response = await fetch(`${this.baseUrl}/${provinceId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(requestBody)
            });

            console.log('📡 Respuesta HTTP:', response.status);

            // Verificar si la respuesta es exitosa
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                const errorMessage = errorData.message || 
                                   errorData.error || 
                                   `Error HTTP ${response.status}: ${response.statusText}`;
                throw new Error(errorMessage);
            }

            // Parsear respuesta JSON
            const result = await response.json();
            console.log('📥 Respuesta de la API:', result);

            // Verificar estructura de respuesta esperada
            if (result.status === 'success' || result.success === true) {
                console.log('✅ Provincia actualizada exitosamente');
                
                return {
                    success: true,
                    message: result.message || 'Provincia actualizada exitosamente',
                    data: result.data || result.province || result
                };
            } else {
                // La API devolvió un error
                const errorMessage = result.message || result.error || 'Error desconocido al actualizar la provincia';
                throw new Error(errorMessage);
            }

        } catch (error) {
            console.error('❌ Error en ProvinceUpdateService:', error);
            
            // Re-lanzar errores de red o servidor
            if (error.name === 'TypeError' || error.message.includes('fetch')) {
                throw new Error('Error de conexión. Verifique su conexión a internet.');
            }
            
            // Re-lanzar otros errores tal como están
            throw error;
        }
    }

    /**
     * Valida los datos de una provincia antes de enviarlos
     * @param {Object} provinceData - Datos a validar
     * @returns {Object} Objeto con validación y errores
     */
    validateProvinceData(provinceData) {
        const errors = [];
        const warnings = [];

        if (!provinceData) {
            errors.push('Los datos de la provincia son requeridos');
            return { isValid: false, errors, warnings };
        }

        // Validar nombre
        if (!provinceData.name || typeof provinceData.name !== 'string') {
            errors.push('El nombre de la provincia es requerido');
        } else {
            const name = provinceData.name.trim();
            if (name.length === 0) {
                errors.push('El nombre de la provincia no puede estar vacío');
            } else if (name.length < 2) {
                errors.push('El nombre de la provincia debe tener al menos 2 caracteres');
            } else if (name.length > 100) {
                errors.push('El nombre de la provincia no debe exceder 100 caracteres');
            }
        }

        // Validar departmentId
        if (!provinceData.departmentId) {
            errors.push('El ID del departamento es requerido');
        } else {
            const depId = Number(provinceData.departmentId);
            if (!Number.isInteger(depId) || depId <= 0) {
                errors.push('El ID del departamento debe ser un número entero positivo');
            }
        }

        // Validar ubigeo
        if (!provinceData.ubigeo) {
            errors.push('El código ubigeo es requerido');
        } else {
            const ubigeo = provinceData.ubigeo.toString();
            if (!/^[0-9]{4}$/.test(ubigeo)) {
                errors.push('El código ubigeo debe tener exactamente 4 dígitos numéricos');
            }
        }

        return {
            isValid: errors.length === 0,
            errors,
            warnings
        };
    }

    /**
     * Obtiene los datos actuales de una provincia para cargar en el formulario
     * @param {number} provinceId - ID de la provincia
     * @returns {Promise<Object>} Datos de la provincia
     */
    async getProvinceById(provinceId) {
        try {
            console.log('📡 Obteniendo datos de provincia:', provinceId);

            if (!provinceId) {
                throw new Error('ID de provincia es requerido');
            }

            const token = sessionStorage.getItem('token') || 
                         sessionStorage.getItem('authToken') || 
                         sessionStorage.getItem('userToken');

            if (!token) {
                throw new Error('Token de autenticación no encontrado');
            }

            const response = await fetch(`${this.baseUrl}/${provinceId}`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || `Error HTTP ${response.status}`);
            }

            const result = await response.json();
            console.log('📥 Datos de provincia obtenidos:', result);

            return {
                success: true,
                data: result.data || result.province || result
            };

        } catch (error) {
            console.error('❌ Error obteniendo provincia:', error);
            throw error;
        }
    }
}

// Hacer disponible globalmente
window.ProvinceUpdateService = ProvinceUpdateService;

console.log('✅ ProvinceUpdateService cargado correctamente');
