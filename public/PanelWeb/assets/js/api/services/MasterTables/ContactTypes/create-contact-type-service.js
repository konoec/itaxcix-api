/**
 * Servicio específico para crear nuevos tipos de contacto
 * Endpoint: /admin/contact-types
 */
class CreateContactTypeService {
    static get API_BASE_URL() {
        return 'https://149.130.161.148/api/v1';
    }

    static getAuthToken() {
        return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
    }

    /**
     * Crea un nuevo tipo de contacto con validaciones avanzadas
     * @param {Object} contactTypeData - Datos del tipo de contacto a crear
     * @param {string} contactTypeData.name - Nombre del tipo de contacto
     * @param {boolean} contactTypeData.active - Estado activo del tipo de contacto
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async createContactType(contactTypeData) {
        try {
            console.log('📝 CreateContactTypeService: Creando nuevo tipo de contacto:', contactTypeData);
            
            const token = this.getAuthToken();
            
            const response = await fetch(`${this.API_BASE_URL}/admin/contact-types`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(contactTypeData)
            });

            console.log(`📡 CreateContactTypeService: Respuesta recibida con status ${response.status}`);

            const data = await response.json();
            
            if (!response.ok) {
                console.error(`❌ CreateContactTypeService: Error ${response.status}:`, data);
                throw new Error(data.error?.message || `Error ${response.status}: ${response.statusText}`);
            }

            console.log('✅ CreateContactTypeService: Tipo de contacto creado exitosamente:', data);
            return data;

        } catch (error) {
            console.error('❌ CreateContactTypeService: Error al crear tipo de contacto:', error);
            throw error;
        }
    }
}

// Hacer disponible globalmente
window.CreateContactTypeService = CreateContactTypeService;

console.log('✅ CreateContactTypeService cargado y disponible globalmente');
