/**
 * Servicio para crear tipos de procedimiento
 * Maneja las operaciones de creación de tipos de trámite en el sistema
 */
class ProcedureTypeCreateService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.apiUrl = `${this.baseUrl}/admin/procedure-types`;
    }

    /**
     * Crea un nuevo tipo de trámite
     * @param {Object} procedureTypeData - Datos del tipo de trámite
     * @param {string} procedureTypeData.name - Nombre del tipo de trámite
     * @param {boolean} procedureTypeData.active - Estado activo/inactivo
     * @returns {Promise<Object>} Respuesta de la API
     */
    async createProcedureType(procedureTypeData) {
        try {
            const token = sessionStorage.getItem('authToken');
            if (!token) {
                console.error('Token de autenticación no encontrado en sessionStorage. Asegúrate de iniciar sesión antes de realizar esta acción.');
                throw new Error('Token de autenticación no encontrado');
            }

            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(procedureTypeData)
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                let errorMsg = errorData?.error?.message || errorData.message || `Error HTTP: ${response.status}`;
                throw new Error(errorMsg);
            }

            const result = await response.json();
            if (!result.message || result.message.trim().toUpperCase() === 'OK') {
                result.message = 'Tipo de trámite creado exitosamente';
            }
            return result;
        } catch (error) {
            console.error('Error al crear tipo de trámite:', error);
            throw error;
        }
    }

    /**
     * Valida los datos del tipo de trámite antes de enviar
     * @param {Object} data - Datos a validar
     * @returns {Object} Resultado de la validación
     */
    validateProcedureTypeData(data) {
        const errors = [];

        if (!data.name || data.name.trim().length === 0) {
            errors.push('El nombre del tipo de trámite es requerido');
        }

        if (data.name && data.name.trim().length > 100) {
            errors.push('El nombre no puede exceder 100 caracteres');
        }

        if (typeof data.active !== 'boolean') {
            errors.push('El estado activo debe ser verdadero o falso');
        }

        return {
            isValid: errors.length === 0,
            errors
        };
    }
}

// Inicializar servicio
window.procedureTypeCreateService = new ProcedureTypeCreateService();
