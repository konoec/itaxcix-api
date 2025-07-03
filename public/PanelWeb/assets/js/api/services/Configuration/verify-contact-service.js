/**
 * VerifyContactService - Servicio para verificar contactos de usuarios
 * Maneja la verificación manual de contactos de email y teléfono
 */

class VerifyContactService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.authToken = sessionStorage.getItem('authToken') || localStorage.getItem('authToken');
    }

    /**
     * Verificar un contacto específico de un usuario
     * @param {number} userId - ID del usuario
     * @param {number} contactId - ID del contacto a verificar
     * @returns {Promise<Object>} Respuesta de la API
     */
    async verifyContact(userId, contactId) {
        try {
            console.log(`🔍 Verificando contacto ${contactId} del usuario ${userId}...`);
            console.log(`📡 Enviando POST a: ${this.baseUrl}/users/${userId}/verify-contact`);
            console.log(`📦 Body de la petición:`, { contactId: contactId });
            
            const response = await fetch(`${this.baseUrl}/users/${userId}/verify-contact`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${this.authToken}`
                },
                body: JSON.stringify({
                    contactId: contactId
                })
            });

            console.log('📡 Respuesta del servidor:', response.status, response.statusText);

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                console.error('❌ Error en la verificación:', errorData);
                throw new Error(errorData.message || `Error al verificar contacto: ${response.status}`);
            }

            const result = await response.json();
            console.log('✅ Contacto verificado exitosamente:', result);
            
            return {
                success: true,
                data: result,
                message: result.message || 'Contacto verificado exitosamente'
            };

        } catch (error) {
            console.error('❌ Error en VerifyContactService.verifyContact:', error);
            return {
                success: false,
                error: error.message,
                message: error.message || 'Error al verificar el contacto'
            };
        }
    }

    /**
     * Verificar contacto de email específico
     * @param {number} userId - ID del usuario
     * @param {number} emailContactId - ID del contacto de email
     * @returns {Promise<Object>} Respuesta de la API
     */
    async verifyEmailContact(userId, emailContactId) {
        console.log(`📧 Verificando contacto de email ${emailContactId} del usuario ${userId}`);
        return await this.verifyContact(userId, emailContactId);
    }

    /**
     * Verificar contacto de teléfono específico
     * @param {number} userId - ID del usuario
     * @param {number} phoneContactId - ID del contacto de teléfono
     * @returns {Promise<Object>} Respuesta de la API
     */
    async verifyPhoneContact(userId, phoneContactId) {
        console.log(`📱 Verificando contacto de teléfono ${phoneContactId} del usuario ${userId}`);
        return await this.verifyContact(userId, phoneContactId);
    }

    /**
     * Actualizar token de autenticación
     * @param {string} newToken - Nuevo token de autenticación
     */
    updateAuthToken(newToken) {
        this.authToken = newToken;
        console.log('🔑 Token de autenticación actualizado en VerifyContactService');
    }
}

// Crear instancia global del servicio
if (typeof window !== 'undefined') {
    window.VerifyContactService = new VerifyContactService();
    console.log('✅ VerifyContactService inicializado y disponible globalmente');
}

// Exportar para uso en módulos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = VerifyContactService;
}
