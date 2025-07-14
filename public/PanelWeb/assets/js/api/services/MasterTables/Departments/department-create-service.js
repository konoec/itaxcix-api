/**
 * Servicio para crear departamentos
 * Maneja la creación de nuevos departamentos via API
 */

class DepartmentCreateService {
    static get API_BASE_URL() {
        return 'https://149.130.161.148/api/v1';
    }

    static getAuthToken() {
        return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
    }

    /**
     * Crea un nuevo departamento
     * @param {Object} departmentData - Datos del departamento
     * @param {string} departmentData.name - Nombre del departamento
     * @param {string} departmentData.ubigeo - Código ubigeo del departamento
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async createDepartment(departmentData) {
        try {
            console.log('🆕 DepartmentCreateService: Creando departamento:', departmentData);
            
            const token = DepartmentCreateService.getAuthToken();
            
            // Validar datos de entrada
            const validatedData = DepartmentCreateService.validateDepartmentData(departmentData);
            
            console.log('✅ Datos validados:', validatedData);
            
            const response = await fetch(`${DepartmentCreateService.API_BASE_URL}/admin/departments`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(validatedData)
            });

            console.log(`📡 Status de respuesta: ${response.status}`);

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                console.error('❌ Error en la respuesta del servidor:', errorData);
                
                if (response.status === 401) {
                    console.error('🚫 Token de autenticación inválido o expirado');
                    throw new Error('Sesión expirada. Por favor, inicia sesión nuevamente.');
                } else if (response.status === 403) {
                    console.error('🚫 Sin permisos para crear departamentos');
                    throw new Error('No tienes permisos para crear departamentos.');
                } else if (response.status === 400) {
                    console.error('🚫 Datos de entrada inválidos');
                    throw new Error(errorData.message || 'Los datos proporcionados son inválidos.');
                } else if (response.status === 409) {
                    console.error('🚫 Departamento ya existe');
                    throw new Error('Ya existe un departamento con ese nombre o ubigeo.');
                } else {
                    throw new Error(errorData.message || `Error del servidor: ${response.status}`);
                }
            }

            const data = await response.json();
            console.log('✅ Departamento creado exitosamente:', data);
            
            // Validar estructura de respuesta
            if (!data.success || !data.data) {
                console.error('❌ Estructura de respuesta inválida:', data);
                throw new Error('Formato de respuesta inválido del servidor');
            }

            return data;

        } catch (error) {
            console.error('❌ Error en DepartmentCreateService.createDepartment:', error);
            
            if (error.name === 'TypeError' && error.message.includes('fetch')) {
                throw new Error('Error de conexión. Verifica tu conexión a internet.');
            }
            
            throw error;
        }
    }

    /**
     * Valida los datos del departamento antes de enviarlos
     * @param {Object} departmentData - Datos a validar
     * @returns {Object} Datos validados
     */
    static validateDepartmentData(departmentData) {
        const errors = [];

        // Validar nombre
        if (!departmentData.name || typeof departmentData.name !== 'string') {
            errors.push('El nombre del departamento es requerido');
        } else {
            const name = departmentData.name.trim();
            if (name.length < 2) {
                errors.push('El nombre debe tener al menos 2 caracteres');
            }
            if (name.length > 100) {
                errors.push('El nombre no puede exceder 100 caracteres');
            }
            if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(name)) {
                errors.push('El nombre solo puede contener letras y espacios');
            }
        }

        // Validar ubigeo
        if (!departmentData.ubigeo || typeof departmentData.ubigeo !== 'string') {
            errors.push('El código ubigeo es requerido');
        } else {
            const ubigeo = departmentData.ubigeo.trim();
            if (!/^[0-9]{1,2}$/.test(ubigeo)) {
                errors.push('El ubigeo debe ser un número de 1-2 dígitos');
            }
        }

        if (errors.length > 0) {
            throw new Error(`Errores de validación: ${errors.join(', ')}`);
        }

        return {
            name: departmentData.name.trim(),
            ubigeo: departmentData.ubigeo.trim()
        };
    }

    /**
     * Verifica si un departamento ya existe por nombre o ubigeo
     * @param {string} name - Nombre del departamento
     * @param {string} ubigeo - Código ubigeo
     * @returns {Promise<boolean>} True si existe, false si no
     */
    static async checkDepartmentExists(name, ubigeo) {
        try {
            // Buscar por nombre
            const nameSearchResponse = await DepartmentsService.getDepartments(1, 1, name.trim());
            if (nameSearchResponse.success && nameSearchResponse.data.data.length > 0) {
                const existingDept = nameSearchResponse.data.data[0];
                if (existingDept.name.toLowerCase() === name.trim().toLowerCase()) {
                    return true;
                }
            }

            // Buscar por ubigeo
            const ubigeoSearchResponse = await DepartmentsService.getDepartments(1, 1, ubigeo.trim());
            if (ubigeoSearchResponse.success && ubigeoSearchResponse.data.data.length > 0) {
                const existingDept = ubigeoSearchResponse.data.data[0];
                if (existingDept.ubigeo === ubigeo.trim()) {
                    return true;
                }
            }

            return false;
        } catch (error) {
            console.warn('⚠️ No se pudo verificar duplicados:', error);
            return false; // En caso de error, permitir continuar
        }
    }

    /**
     * Transforma la respuesta de la API para uso en la UI
     * @param {Object} apiResponse - Respuesta de la API
     * @returns {Object} Datos transformados
     */
    static transformCreateResponse(apiResponse) {
        if (!apiResponse.success || !apiResponse.data) {
            throw new Error('Respuesta de API inválida');
        }

        return {
            department: {
                id: apiResponse.data.id,
                name: apiResponse.data.name,
                ubigeo: apiResponse.data.ubigeo
            },
            success: true,
            message: apiResponse.message || 'Departamento creado correctamente'
        };
    }

    /**
     * Genera códigos ubigeo sugeridos basados en los existentes
     * @returns {Promise<Array>} Lista de códigos sugeridos
     */
    static async getSuggestedUbigeoCodes() {
        try {
            const response = await DepartmentsService.getDepartments(1, 100); // Obtener todos
            if (response.success && response.data.data) {
                const existingCodes = response.data.data.map(dept => parseInt(dept.ubigeo));
                const suggestions = [];
                
                // Sugerir códigos del 1-25 que no estén en uso
                for (let i = 1; i <= 25; i++) {
                    if (!existingCodes.includes(i)) {
                        suggestions.push(i.toString().padStart(2, '0'));
                    }
                }
                
                return suggestions.slice(0, 5); // Solo primeros 5
            }
            return [];
        } catch (error) {
            console.warn('⚠️ No se pudieron obtener sugerencias:', error);
            return [];
        }
    }
}

// Exportar para uso global
window.DepartmentCreateService = DepartmentCreateService;
