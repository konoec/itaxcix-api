/**
 * Servicio para crear clases de vehículo
 * Maneja las operaciones de creación de clases de vehículo en el sistema
 */
class VehicleClassCreateService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.apiUrl = `${this.baseUrl}/admin/vehicle-classes`;
    }

    /**
     * Crea una nueva clase de vehículo
     * @param {Object} vehicleClassData - Datos de la clase de vehículo
     * @param {string} vehicleClassData.name - Nombre de la clase
     * @param {boolean} vehicleClassData.active - Estado activo/inactivo
     * @returns {Promise<Object>} Respuesta de la API
     */
    async createVehicleClass(vehicleClassData) {
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
                body: JSON.stringify(vehicleClassData)
            });
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                let errorMsg = '';
                if (errorData && typeof errorData === 'object') {
                    if (errorData.error && typeof errorData.error === 'object' && errorData.error.message) {
                        errorMsg = errorData.error.message;
                    } else if (errorData.message) {
                        errorMsg = errorData.message;
                    } else {
                        errorMsg = `Error HTTP: ${response.status}`;
                    }
                } else {
                    errorMsg = `Error HTTP: ${response.status}`;
                }
                throw new Error(errorMsg);
            }
            const result = await response.json();
            if (!result.message || result.message.trim().toUpperCase() === 'OK') {
                result.message = 'Clase de vehículo creada correctamente';
            }
            return result;
        } catch (error) {
            console.error('Error al crear clase de vehículo:', error);
            // Si el error tiene un mensaje, mostrarlo siempre
            if (error && error.message) {
                throw new Error(error.message);
            }
            // Error de conexión genérico solo si no hay mensaje
            throw new Error('Error de conexión. Intente nuevamente más tarde.');
        }
    }

    /**
     * Valida los datos de la clase de vehículo antes de enviar
     * @param {Object} data - Datos a validar
     * @returns {Object} Resultado de la validación
     */
    validateVehicleClassData(data) {
        const errors = [];
        if (!data.name || data.name.trim().length === 0) {
            errors.push('El nombre de la clase es requerido');
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
window.vehicleClassCreateService = new VehicleClassCreateService();
