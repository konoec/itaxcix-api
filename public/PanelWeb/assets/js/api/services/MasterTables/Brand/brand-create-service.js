/**
 * Servicio para crear marcas de vehículo
 * Maneja las operaciones de creación de marcas en el sistema
 */
class BrandCreateService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.apiUrl = `${this.baseUrl}/admin/brands`;
    }

    /**
     * Crea una nueva marca de vehículo
     * @param {Object} brandData - Datos de la marca
     * @param {string} brandData.name - Nombre de la marca
     * @param {boolean} brandData.active - Estado activo/inactivo
     * @returns {Promise<Object>} Respuesta de la API
     */
    async createBrand(brandData) {
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
                body: JSON.stringify(brandData)
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || `Error HTTP: ${response.status}`);
            }

            const result = await response.json();
            if (!result.message || result.message.trim().toUpperCase() === 'OK') {
                result.message = 'Marca creada exitosamente';
            }
            return result;
        } catch (error) {
            console.error('Error al crear marca:', error);
            throw error;
        }
    }

    /**
     * Valida los datos de la marca antes de enviar
     * @param {Object} data - Datos a validar
     * @returns {Object} Resultado de la validación
     */
    validateBrandData(data) {
        const errors = [];

        if (!data.name || data.name.trim().length === 0) {
            errors.push('El nombre de la marca es requerido');
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
window.brandCreateService = new BrandCreateService();
