/**
 * Servicio para crear tipos de servicio
 * Maneja las operaciones de creación de tipos de servicio en el sistema
 */
class ServiceTypeCreateService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.apiUrl = `${this.baseUrl}/admin/service-types`;
    }

    /**
     * Crea un nuevo tipo de servicio
     * @param {Object} serviceTypeData - Datos del tipo de servicio
     * @param {string} serviceTypeData.name - Nombre del tipo de servicio
     * @param {boolean} serviceTypeData.active - Estado activo/inactivo
     * @returns {Promise<Object>} Respuesta de la API
     */
    async createServiceType(serviceTypeData) {
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
                body: JSON.stringify(serviceTypeData)
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || `Error HTTP: ${response.status}`);
            }

            const result = await response.json();
            if (!result.message || result.message.trim().toUpperCase() === 'OK') {
                result.message = 'Tipo de servicio creado exitosamente';
            }
            return result;
        } catch (error) {
            console.error('Error al crear tipo de servicio:', error);
            throw error;
        }
    }

    /**
     * Valida los datos del tipo de servicio antes de enviar
     * @param {Object} data - Datos a validar
     * @returns {Object} Resultado de la validación
     */
    validateServiceTypeData(data) {
        const errors = [];

        if (!data.name || data.name.trim().length === 0) {
            errors.push('El nombre del tipo de servicio es requerido');
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
window.serviceTypeCreateService = new ServiceTypeCreateService();
