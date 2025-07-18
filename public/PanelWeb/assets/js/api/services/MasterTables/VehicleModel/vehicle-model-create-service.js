// Servicio para crear modelos de vehículo
class VehicleModelCreateService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.apiUrl = `${this.baseUrl}/admin/models`;
    }

    async createVehicleModel(modelData) {
        try {
            const token = sessionStorage.getItem('authToken');
            if (!token) {
                console.error('Token de autenticación no encontrado en sessionStorage.');
                throw new Error('Token de autenticación no encontrado');
            }
            const response = await fetch(this.apiUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(modelData)
            });
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || `Error HTTP: ${response.status}`);
            }
            const result = await response.json();
            if (!result.message || result.message.trim().toUpperCase() === 'OK') {
                result.message = 'Modelo de vehículo creado correctamente';
            }
            return result;
        } catch (error) {
            console.error('Error al crear modelo de vehículo:', error);
            throw error;
        }
    }

    validateVehicleModelData(data) {
        const errors = [];
        if (!data.name || data.name.trim().length === 0) {
            errors.push('El nombre del modelo es requerido');
        }
        if (data.name && data.name.trim().length > 100) {
            errors.push('El nombre no puede exceder 100 caracteres');
        }
        if (!data.brandId || isNaN(data.brandId)) {
            errors.push('Debe seleccionar una marca válida');
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
window.vehicleModelCreateService = new VehicleModelCreateService();
