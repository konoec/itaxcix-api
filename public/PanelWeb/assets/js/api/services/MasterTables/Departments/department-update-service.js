/**
 * Servicio para actualizar departamentos
 * Maneja las operaciones de actualización de departamentos via API REST
 */
class DepartmentUpdateService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.endpoint = '/admin/departments';
        
        console.log('🏗️ DepartmentUpdateService inicializado con base URL:', this.baseUrl);
    }

    /**
     * Actualiza un departamento existente
     * @param {number} id - ID del departamento a actualizar
     * @param {Object} departmentData - Datos del departamento
     * @param {string} departmentData.name - Nombre del departamento
     * @param {string} departmentData.ubigeo - Código ubigeo del departamento (2 dígitos)
     * @returns {Promise<Object>} - Respuesta del servidor
     */
    async updateDepartment(id, departmentData) {
        console.log('📝 Actualizando departamento:', id, departmentData);

        try {
            // Validar token de autenticación
            const token = sessionStorage.getItem("authToken");
            if (!token) {
                throw new Error('No hay token de autenticación disponible');
            }

            // Validar datos requeridos
            if (!id || id <= 0) {
                throw new Error('ID del departamento es requerido y debe ser válido');
            }

            if (!departmentData || typeof departmentData !== 'object') {
                throw new Error('Los datos del departamento son requeridos');
            }

            if (!departmentData.name || departmentData.name.trim() === '') {
                throw new Error('El nombre del departamento es requerido');
            }

            if (!departmentData.ubigeo || departmentData.ubigeo.trim() === '') {
                throw new Error('El código ubigeo es requerido');
            }

            // Validar formato del ubigeo (exactamente 2 dígitos según API)
            if (!/^[0-9]{2}$/.test(departmentData.ubigeo)) {
                throw new Error('El código ubigeo debe ser exactamente 2 dígitos');
            }

            // Preparar datos para envío
            const dataToSend = {
                name: departmentData.name.trim(),
                ubigeo: departmentData.ubigeo.trim()
            };

            console.log('🌐 Enviando datos al servidor:', dataToSend);

            // Configurar timeout para la petición
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 15000); // 15 segundos

            try {
                const response = await fetch(`${this.baseUrl}${this.endpoint}/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(dataToSend),
                    signal: controller.signal
                });

                clearTimeout(timeoutId);

                // Manejar respuesta HTTP
                if (!response.ok) {
                    let errorMessage = `HTTP ${response.status}: Error al actualizar departamento`;
                    
                    try {
                        const errorData = await response.json();
                        if (errorData.message) {
                            errorMessage = errorData.message;
                        } else if (errorData.errors) {
                            // Manejar errores de validación
                            const validationErrors = Object.values(errorData.errors).flat();
                            errorMessage = validationErrors.join(', ');
                        }
                    } catch (parseError) {
                        console.warn('⚠️ No se pudo parsear la respuesta de error');
                    }
                    
                    throw new Error(errorMessage);
                }

                const result = await response.json();
                console.log('✅ Departamento actualizado exitosamente:', result);

                // Validar estructura de respuesta
                if (!result.success) {
                    throw new Error(result.message || 'La actualización no fue exitosa');
                }

                return result;

            } catch (fetchError) {
                clearTimeout(timeoutId);
                
                if (fetchError.name === 'AbortError') {
                    throw new Error('Timeout: La petición tardó demasiado tiempo en responder');
                }
                
                throw fetchError;
            }

        } catch (error) {
            console.error('❌ Error al actualizar departamento:', error);
            
            // Re-lanzar el error con contexto adicional
            if (error.message.includes('Failed to fetch')) {
                throw new Error('Error de conexión: Verifique su conexión a internet');
            }
            
            throw error;
        }
    }

    /**
     * Valida los datos del departamento antes del envío
     * @param {Object} departmentData - Datos a validar
     * @returns {Object} - Resultado de la validación
     */
    validateDepartmentData(departmentData) {
        const errors = {};
        
        // Validar nombre
        if (!departmentData.name || departmentData.name.trim() === '') {
            errors.name = 'El nombre del departamento es requerido';
        } else if (departmentData.name.trim().length < 2) {
            errors.name = 'El nombre debe tener al menos 2 caracteres';
        } else if (departmentData.name.trim().length > 100) {
            errors.name = 'El nombre no puede exceder 100 caracteres';
        }

        // Validar ubigeo (2 dígitos según API)
        if (!departmentData.ubigeo || departmentData.ubigeo.trim() === '') {
            errors.ubigeo = 'El código ubigeo es requerido';
        } else if (!/^[0-9]{2}$/.test(departmentData.ubigeo.trim())) {
            errors.ubigeo = 'El código ubigeo debe ser exactamente 2 dígitos';
        }

        return {
            isValid: Object.keys(errors).length === 0,
            errors
        };
    }
}

// Hacer disponible globalmente
window.DepartmentUpdateService = DepartmentUpdateService;
