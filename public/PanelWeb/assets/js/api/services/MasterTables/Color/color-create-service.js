/**
 * Servicio para crear colores
 * Maneja las operaciones de creación de colores en el sistema
 */
class ColorCreateService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.apiUrl = `${this.baseUrl}/admin/colors`;
    }

    /**
     * Crea un nuevo color
     * @param {Object} colorData - Datos del color
     * @param {string} colorData.name - Nombre del color
     * @param {boolean} colorData.active - Estado activo/inactivo
     * @returns {Promise<Object>} Respuesta de la API
     */
    async createColor(colorData) {
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
                body: JSON.stringify(colorData)
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || `Error HTTP: ${response.status}`);
            }

            const result = await response.json();
            // Forzar mensaje formal si el backend responde con 'OK' o vacío
            if (!result.message || result.message.trim().toUpperCase() === 'OK') {
                result.message = 'Color creado exitosamente';
            }
            return result;
        } catch (error) {
            console.error('Error al crear color:', error);
            throw error;
        }
    }

    /**
     * Valida los datos del color antes de enviar
     * @param {Object} data - Datos a validar
     * @returns {Object} Resultado de la validación
     */
    validateColorData(data) {
        const errors = [];

        if (!data.name || data.name.trim().length === 0) {
            errors.push('El nombre del color es requerido');
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
window.colorCreateService = new ColorCreateService();
