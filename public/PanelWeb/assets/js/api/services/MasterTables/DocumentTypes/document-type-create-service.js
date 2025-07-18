/**
 * Servicio para crear tipos de documento
 * Maneja las operaciones de creación de tipos de documento en el sistema
 */
class DocumentTypeCreateService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.apiUrl = `${this.baseUrl}/admin/document-types`;
    }

    /**
     * Crea un nuevo tipo de documento
     * @param {Object} documentTypeData - Datos del tipo de documento
     * @param {string} documentTypeData.name - Nombre del tipo de documento
     * @param {boolean} documentTypeData.active - Estado activo/inactivo
     * @returns {Promise<Object>} Respuesta de la API
     */
    async createDocumentType(documentTypeData) {
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
                body: JSON.stringify(documentTypeData)
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || `Error HTTP: ${response.status}`);
            }

            const result = await response.json();
            if (!result.message || result.message.trim().toUpperCase() === 'OK') {
                result.message = 'Tipo de documento creado exitosamente';
            }
            return result;
        } catch (error) {
            console.error('Error al crear tipo de documento:', error);
            throw error;
        }
    }

    /**
     * Valida los datos del tipo de documento antes de enviar
     * @param {Object} data - Datos a validar
     * @returns {Object} Resultado de la validación
     */
    validateDocumentTypeData(data) {
        const errors = [];

        if (!data.name || data.name.trim().length === 0) {
            errors.push('El nombre del tipo de documento es requerido');
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
window.documentTypeCreateService = new DocumentTypeCreateService();
