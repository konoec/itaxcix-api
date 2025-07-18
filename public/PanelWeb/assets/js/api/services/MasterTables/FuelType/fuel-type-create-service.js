/**
 * Servicio para crear tipos de combustible
 * Maneja las operaciones de creación de tipos de combustible en el sistema
 */
class FuelTypeCreateService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.apiUrl = `${this.baseUrl}/admin/fuel-types`;
    }

    /**
     * Crea un nuevo tipo de combustible
     * @param {Object} fuelTypeData - Datos del tipo de combustible
     * @param {string} fuelTypeData.name - Nombre del tipo
     * @param {boolean} fuelTypeData.active - Estado activo/inactivo
     * @returns {Promise<Object>} Respuesta de la API
     */
    async createFuelType(fuelTypeData) {
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
                body: JSON.stringify(fuelTypeData)
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || `Error HTTP: ${response.status}`);
            }

            const result = await response.json();
            if (!result.message || result.message.trim().toUpperCase() === 'OK') {
                result.message = 'Tipo de combustible creado exitosamente';
            }
            return result;
        } catch (error) {
            console.error('Error al crear tipo de combustible:', error);
            throw error;
        }
    }

    /**
     * Valida los datos del tipo de combustible antes de enviar
     * @param {Object} data - Datos a validar
     * @returns {Object} Resultado de la validación
     */
    validateFuelTypeData(data) {
        const errors = [];

        if (!data.name || data.name.trim().length === 0) {
            errors.push('El nombre del tipo de combustible es requerido');
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
window.fuelTypeCreateService = new FuelTypeCreateService();
