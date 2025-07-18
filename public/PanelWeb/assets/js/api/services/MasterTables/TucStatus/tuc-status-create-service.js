/**
 * Servicio para crear estados TUC
 * Maneja las operaciones de creación de estados TUC en el sistema
 */
class TucStatusCreateService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.apiUrl = `${this.baseUrl}/admin/tuc-statuses`;
    }

    /**
     * Crea un nuevo estado TUC
     * @param {Object} tucStatusData - Datos del estado TUC
     * @param {string} tucStatusData.name - Nombre del estado
     * @param {boolean} tucStatusData.active - Estado activo/inactivo
     * @returns {Promise<Object>} Respuesta de la API
     */
    async createTucStatus(tucStatusData) {
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
                body: JSON.stringify(tucStatusData)
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || `Error HTTP: ${response.status}`);
            }

            const result = await response.json();
            if (!result.message || result.message.trim().toUpperCase() === 'OK') {
                result.message = 'Estado TUC creado exitosamente';
            }
            return result;
        } catch (error) {
            console.error('Error al crear estado TUC:', error);
            throw error;
        }
    }

    /**
     * Valida los datos del estado TUC antes de enviar
     * @param {Object} data - Datos a validar
     * @returns {Object} Resultado de la validación
     */
    validateTucStatusData(data) {
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
window.tucStatusCreateService = new TucStatusCreateService();
