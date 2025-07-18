/**
 * Servicio para crear severidades de infracción
 * Maneja las operaciones de creación de severidades de infracción en el sistema
 */
class InfractionSeverityCreateService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.apiUrl = `${this.baseUrl}/admin/infraction-severities`;
    }

    /**
     * Crea una nueva severidad de infracción
     * @param {Object} infractionSeverityData - Datos de la severidad
     * @param {string} infractionSeverityData.name - Nombre de la severidad
     * @param {boolean} infractionSeverityData.active - Estado activo/inactivo
     * @returns {Promise<Object>} Respuesta de la API
     */
    async createInfractionSeverity(infractionSeverityData) {
        try {
            const token = sessionStorage.getItem('authToken');
            if (!token) {
                throw new Error('Token de autenticación no encontrado');
            }
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(infractionSeverityData)
            });
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || `Error HTTP: ${response.status}`);
            }
            const result = await response.json();
            if (!result.message || result.message.trim().toUpperCase() === 'OK') {
                result.message = 'Gravedad de infracción creada exitosamente';
            }
            return result;
        } catch (error) {
            throw error;
        }
    }

    /**
     * Valida los datos antes de enviar
     * @param {Object} data - Datos a validar
     * @returns {Object} Resultado de la validación
     */
    validateInfractionSeverityData(data) {
        const errors = [];
        if (!data.name || data.name.trim().length === 0) {
            errors.push('El nombre de la severidad es requerido');
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
window.infractionSeverityCreateService = new InfractionSeverityCreateService();
