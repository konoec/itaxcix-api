/**
 * Servicio para crear estados de infracción
 * Maneja las operaciones de creación de estados de infracción en el sistema
 */
class InfractionStatusCreateService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.apiUrl = `${this.baseUrl}/admin/infraction-statuses`;
    }

    /**
     * Crea un nuevo estado de infracción
     * @param {Object} infractionStatusData - Datos del estado de infracción
     * @param {string} infractionStatusData.name - Nombre del estado
     * @param {boolean} infractionStatusData.active - Estado activo/inactivo
     * @returns {Promise<Object>} Respuesta de la API
     */
    async createInfractionStatus(infractionStatusData) {
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
                body: JSON.stringify(infractionStatusData)
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || `Error HTTP: ${response.status}`);
            }

            const result = await response.json();
            if (!result.message || result.message.trim().toUpperCase() === 'OK') {
                result.message = 'Estado de infracción creado exitosamente';
            }
            return result;
        } catch (error) {
            console.error('Error al crear estado de infracción:', error);
            throw error;
        }
    }

    /**
     * Valida los datos del estado de infracción antes de enviar
     * @param {Object} data - Datos a validar
     * @returns {Object} Resultado de la validación
     */
    validateInfractionStatusData(data) {
        const errors = [];

        if (!data.name || data.name.trim().length === 0) {
            errors.push('El nombre del estado es requerido');
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
window.infractionStatusCreateService = new InfractionStatusCreateService();
