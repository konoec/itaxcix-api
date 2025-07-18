/**
 * Servicio para crear nuevos elementos del centro de ayuda
 * Maneja la creación de elementos mediante la API
 */
class CreateHelpCenterService {
    static get API_BASE_URL() {
        return 'https://149.130.161.148/api/v1';
    }

    /**
     * Obtiene el token de autenticación del sessionStorage
     */
    static getAuthToken() {
        return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
    }

    /**
     * Valida los datos del elemento antes de enviarlos
     * @param {Object} data - Datos del elemento
     * @returns {Object} - { isValid: boolean, errors: array }
     */
    static validateData(data) {
        const errors = [];

        // Validar título
        if (!data.title || typeof data.title !== 'string' || data.title.trim().length === 0) {
            errors.push('El título es requerido');
        } else if (data.title.trim().length > 255) {
            errors.push('El título no puede exceder 255 caracteres');
        }

        // Validar subtítulo
        if (!data.subtitle || typeof data.subtitle !== 'string' || data.subtitle.trim().length === 0) {
            errors.push('El subtítulo es requerido');
        } else if (data.subtitle.trim().length > 255) {
            errors.push('El subtítulo no puede exceder 255 caracteres');
        }

        // Validar respuesta
        if (!data.answer || typeof data.answer !== 'string' || data.answer.trim().length === 0) {
            errors.push('La respuesta es requerida');
        } else if (data.answer.trim().length > 1000) {
            errors.push('La respuesta no puede exceder 1000 caracteres');
        }

        // Validar estado activo
        if (typeof data.active !== 'boolean') {
            errors.push('El estado activo debe ser verdadero o falso');
        }

        return {
            isValid: errors.length === 0,
            errors: errors
        };
    }

    /**
     * Crea un nuevo elemento del centro de ayuda
     * @param {Object} data - Datos del elemento a crear
     * @param {string} data.title - Título del elemento
     * @param {string} data.subtitle - Subtítulo del elemento
     * @param {string} data.answer - Respuesta del elemento
     * @param {boolean} data.active - Estado activo del elemento
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async createHelpCenterItem(data) {
        try {
            console.log('🔄 Creando elemento del centro de ayuda:', data);

            // Validar datos antes de enviar
            const validation = this.validateData(data);
            if (!validation.isValid) {
                throw new Error(validation.errors.join(', '));
            }

            const token = this.getAuthToken();
            if (!token) {
                throw new Error('Token de autenticación requerido');
            }

            // Preparar datos para envío
            const requestBody = {
                title: data.title.trim(),
                subtitle: data.subtitle.trim(),
                answer: data.answer.trim(),
                active: Boolean(data.active)
            };

            const response = await fetch(`${this.API_BASE_URL}/help-center`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(requestBody)
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || `Error HTTP ${response.status}: ${response.statusText}`);
            }

            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.message || 'Error al crear elemento del centro de ayuda');
            }

            console.log('✅ Elemento del centro de ayuda creado correctamente');
            return result;

        } catch (error) {
            console.error('❌ Error en createHelpCenterItem:', error);
            throw error;
        }
    }
}

// Exportar servicio globalmente
window.CreateHelpCenterService = CreateHelpCenterService;
