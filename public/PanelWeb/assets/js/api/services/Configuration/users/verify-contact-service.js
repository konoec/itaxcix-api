/**
 * VerifyContactService - Servicio para verificar contactos de usuarios
 * Maneja la verificación manual de contactos de email y teléfono
 */

class VerifyContactService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.updateAuthToken(); // Actualizar token al inicializar
    }

    /**
     * Actualizar token de autenticación desde sessionStorage o localStorage
     */
    updateAuthToken(newToken = null) {
        if (newToken) {
            this.authToken = newToken;
        } else {
            // Buscar token en sessionStorage o localStorage
            this.authToken = sessionStorage.getItem('token') || 
                            sessionStorage.getItem('authToken') || 
                            localStorage.getItem('token') || 
                            localStorage.getItem('authToken');
        }
        console.log('🔑 Token de autenticación actualizado en VerifyContactService:', this.authToken ? 'Presente' : 'Ausente');
    }

    /**
     * Verificar un contacto específico de un usuario
     * @param {number} userId - ID del usuario
     * @param {number} contactId - ID del contacto a verificar
     * @returns {Promise<Object>} Respuesta de la API
     */
    async verifyContact(userId, contactId) {
        try {
            // Asegurar que tenemos el token más actualizado
            this.updateAuthToken();
            
            if (!this.authToken) {
                throw new Error('No hay token de autenticación disponible');
            }

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
     * Obtener los contactos de un usuario
     * @param {number} userId - ID del usuario
     * @returns {Promise<Object>} Contactos del usuario
     */
    async getUserContacts(userId) {
        try {
            console.log(`🔍 Obteniendo contactos del usuario ${userId}...`);
            
            // Asegurar que tenemos el token más actualizado
            this.updateAuthToken();
            
            if (!this.authToken) {
                throw new Error('No hay token de autenticación disponible');
            }
            
            const response = await fetch(`${this.baseUrl}/users/${userId}`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${this.authToken}`,
                    'Content-Type': 'application/json'
                }
            });

            console.log(`📡 Respuesta de obtener usuario - Status: ${response.status}`);

            if (!response.ok) {
                const errorText = await response.text();
                console.error(`❌ Error HTTP al obtener usuario: ${response.status} - ${errorText}`);
                throw new Error(`Error al obtener usuario: ${response.status}`);
            }

            const result = await response.json();
            console.log('📋 Datos del usuario obtenidos:', result);
            
            const contacts = result.data?.contacts || [];
            console.log('📞 Contactos del usuario:', contacts);
            console.log(`📞 Total de contactos encontrados: ${contacts.length}`);
            
            // Mostrar información detallada de cada contacto
            contacts.forEach((contact, index) => {
                console.log(`📞 Contacto ${index + 1}:`, {
                    id: contact.id,
                    type: contact.type?.name || contact.type,
                    value: contact.value,
                    confirmed: contact.confirmed
                });
            });
            
            return {
                success: true,
                data: contacts
            };

        } catch (error) {
            console.error('❌ Error al obtener contactos del usuario:', error);
            return {
                success: false,
                error: error.message,
                message: 'Error al obtener contactos del usuario: ' + error.message
            };
        }
    }

    /**
     * Verificar contacto de email del usuario
     * @param {number} userId - ID del usuario
     * @returns {Promise<Object>} Respuesta de la API
     */
    async verifyEmailContact(userId) {
        console.log(`📧 Iniciando verificación de email para usuario ${userId}`);
        
        try {
            // Primero obtener los contactos del usuario
            const contactsResult = await this.getUserContacts(userId);
            if (!contactsResult.success) {
                return contactsResult;
            }

            // Buscar el contacto de email con búsqueda más flexible
            console.log('🔍 Buscando contacto de email entre los tipos disponibles...');
            
            const emailContact = contactsResult.data.find(contact => {
                // Manejar tanto la estructura anidada (type.name) como la simple (type)
                const typeName = contact.type?.name || contact.type;
                const type = typeName?.toString().toLowerCase().trim();
                console.log(`🔍 Evaluando contacto tipo: "${typeName}" (normalizado: "${type}")`);
                
                return type === 'correo electrónico' || 
                       type === 'correo electronico' ||
                       type === 'email' ||
                       type === 'e-mail' ||
                       type.includes('correo') ||
                       type.includes('email') ||
                       type.includes('mail');
            });

            if (!emailContact) {
                console.log('❌ No se encontró contacto de email');
                const availableTypes = contactsResult.data.map(c => c.type?.name || c.type);
                console.log('📋 Tipos de contacto disponibles:', availableTypes);
                return {
                    success: false,
                    message: `No se encontró un contacto de email para este usuario. Tipos disponibles: ${availableTypes.join(', ')}`
                };
            }

            console.log(`📧 Contacto de email encontrado:`, emailContact);
            console.log(`📧 Verificando contacto de email ID ${emailContact.id} del usuario ${userId}`);
            
            return await this.verifyContact(userId, emailContact.id);

        } catch (error) {
            console.error('❌ Error en verificación de email:', error);
            return {
                success: false,
                error: error.message,
                message: 'Error al verificar email'
            };
        }
    }

    /**
     * Verificar contacto de teléfono del usuario
     * @param {number} userId - ID del usuario
     * @returns {Promise<Object>} Respuesta de la API
     */
    async verifyPhoneContact(userId) {
        console.log(`📱 Iniciando verificación de teléfono para usuario ${userId}`);
        
        try {
            // Primero obtener los contactos del usuario
            const contactsResult = await this.getUserContacts(userId);
            if (!contactsResult.success) {
                return contactsResult;
            }

            // Buscar el contacto de teléfono con búsqueda más flexible
            console.log('🔍 Buscando contacto de teléfono entre los tipos disponibles...');
            
            const phoneContact = contactsResult.data.find(contact => {
                // Manejar tanto la estructura anidada (type.name) como la simple (type)
                const typeName = contact.type?.name || contact.type;
                const type = typeName?.toString().toLowerCase().trim();
                console.log(`🔍 Evaluando contacto tipo: "${typeName}" (normalizado: "${type}")`);
                
                return type === 'teléfono móvil' || 
                       type === 'telefono movil' ||
                       type === 'teléfono' ||
                       type === 'telefono' ||
                       type === 'phone' ||
                       type === 'móvil' ||
                       type === 'movil' ||
                       type.includes('teléfono') ||
                       type.includes('telefono') ||
                       type.includes('phone') ||
                       type.includes('móvil') ||
                       type.includes('movil');
            });

            if (!phoneContact) {
                console.log('❌ No se encontró contacto de teléfono');
                const availableTypes = contactsResult.data.map(c => c.type?.name || c.type);
                console.log('📋 Tipos de contacto disponibles:', availableTypes);
                return {
                    success: false,
                    message: `No se encontró un contacto de teléfono para este usuario. Tipos disponibles: ${availableTypes.join(', ')}`
                };
            }

            console.log(`📱 Contacto de teléfono encontrado:`, phoneContact);
            console.log(`📱 Verificando contacto de teléfono ID ${phoneContact.id} del usuario ${userId}`);
            
            return await this.verifyContact(userId, phoneContact.id);

        } catch (error) {
            console.error('❌ Error en verificación de teléfono:', error);
            return {
                success: false,
                error: error.message,
                message: 'Error al verificar teléfono'
            };
        }
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
