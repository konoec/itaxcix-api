/**
 * Servicio para crear modalidades TUC
 * Maneja las operaciones de creación de modalidades TUC en el sistema
 */
class TucModalityCreateService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.apiUrl = `${this.baseUrl}/admin/tuc-modalities`;
    }

    /**
     * Crea una nueva modalidad TUC
     * @param {Object} tucModalityData - Datos de la modalidad TUC
     * @param {string} tucModalityData.name - Nombre de la modalidad
     * @param {boolean} tucModalityData.active - Estado activo/inactivo
     * @returns {Promise<Object>} Respuesta de la API
     */
    async createTucModality(tucModalityData) {
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
                body: JSON.stringify(tucModalityData)
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || `Error HTTP: ${response.status}`);
            }

            const result = await response.json();
            if (!result.message || result.message.trim().toUpperCase() === 'OK') {
                result.message = 'Modalidad TUC creada exitosamente';
            }
            return result;
        } catch (error) {
            console.error('Error al crear modalidad TUC:', error);
            throw error;
        }
    }

    /**
     * Valida los datos de la modalidad TUC antes de enviar
     * @param {Object} data - Datos a validar
     * @returns {Object} Resultado de la validación
     */
    validateTucModalityData(data) {
        const errors = [];

        if (!data.name || data.name.trim().length === 0) {
            errors.push('El nombre de la modalidad es requerido');
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
window.tucModalityCreateService = new TucModalityCreateService();
