// Servicio para manejar la recuperación de contraseñas
class PasswordRecoveryService {
    constructor() {
        this.baseUrl = "https://149.130.161.148/api/v1";
        console.log('PasswordRecoveryService inicializado');
    }    /**
     * Solicita el envío de un enlace de recuperación de contraseña
     * @param {string} contactValue - Correo electrónico o número de teléfono del usuario
     * @param {string} contactType - Tipo de contacto ('email' o 'phone')
     * @returns {Promise<Object>} - Respuesta de la API
     */
    async requestPasswordReset(contactValue, contactType = 'phone') {
        try {
            console.log(`Solicitando recuperación de contraseña para ${contactType}: ${contactValue}`);
            
            const url = `${this.baseUrl}/auth/recovery/start`;
            
            // Determinar contactTypeId según el tipo
            const contactTypeId = contactType === 'email' ? 1 : 2;
            
            let formattedContactValue;
            if (contactType === 'phone') {
                formattedContactValue = this.formatPhoneNumber(contactValue.trim());
            } else {
                formattedContactValue = contactValue.trim().toLowerCase();
            }
            
            const requestBody = {
                contactTypeId,
                contactValue: formattedContactValue
            };

            console.log('Enviando datos:', requestBody);

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(requestBody)
            });

            const responseData = await response.json();
            console.log('Respuesta del servidor:', responseData);

            if (!response.ok) {
                throw new Error(responseData.message || `Error HTTP: ${response.status}`);
            }

            // Verificar si la respuesta fue exitosa
            if (responseData.success === false) {
                throw new Error(responseData.message || 'Error al solicitar recuperación de contraseña');
            }            const contactMethod = contactType === 'email' ? 'correo electrónico' : 'número de teléfono';
            return {
                success: true,
                message: `Se ha enviado un código de recuperación a tu ${contactMethod}.`,
                userId: responseData.data?.userId || null, // Guardar userId para el siguiente paso
                data: responseData.data || null
            };        } catch (error) {
            console.error('Error al solicitar recuperación de contraseña:', error);
            
            // Mejorar manejo de errores específicos
            let errorMessage = error.message || 'Error interno del servidor. Inténtalo más tarde.';
              // Detectar errores SMTP específicos
            if (errorMessage.toLowerCase().includes('smtp') || 
                errorMessage.toLowerCase().includes('authentication') ||
                errorMessage.toLowerCase().includes('mail') ||
                errorMessage.toLowerCase().includes('invalid login')) {
                errorMessage = 'Error temporal con el servicio de correo electrónico. Inténtalo de nuevo en unos minutos.';
            }
            
            return {
                success: false,
                message: errorMessage,
                data: null
            };
        }
    }    /**
     * Verifica el código de recuperación de contraseña
     * @param {number} userId - ID del usuario
     * @param {string} code - Código de recuperación recibido
     * @returns {Promise<Object>} - Respuesta de la API
     */
    async verifyRecoveryCode(userId, code) {
        try {
            console.log(`Verificando código de recuperación para usuario ${userId}: ${code}`);
            
            const url = `${this.baseUrl}/auth/recovery/verify-code`;
            
            // Validaciones básicas
            if (!userId) {
                throw new Error('userId es requerido');
            }
            
            if (!code || typeof code !== 'string' || code.trim().length === 0) {
                throw new Error('El código de recuperación es requerido');
            }
            
            const requestBody = {
                userId: parseInt(userId),
                code: code.trim().toUpperCase() // Normalizar código a mayúsculas
            };

            console.log('Enviando datos para verificación:', requestBody);

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(requestBody)
            });

            const responseData = await response.json();
            console.log('Respuesta del servidor:', responseData);

            // Manejar errores HTTP
            if (!response.ok) {
                if (responseData.error && responseData.error.message) {
                    throw new Error(responseData.error.message);
                }
                throw new Error(responseData.message || `Error HTTP: ${response.status}`);
            }

            // Verificar si la respuesta fue exitosa
            if (responseData.success === false) {
                const errorMessage = responseData.error?.message || responseData.message || 'Error al verificar el código de recuperación';
                throw new Error(errorMessage);
            }

            // Respuesta exitosa
            return {
                success: true,
                message: responseData.data?.message || 'Código verificado correctamente',
                token: responseData.data?.token || null,
                data: responseData.data || null
            };

        } catch (error) {
            console.error('Error al verificar código de recuperación:', error);
            
            // Determinar el tipo de error y mensaje apropiado
            let errorMessage = error.message;
            
            if (error.message.includes('código de recuperación no es válido') || 
                error.message.includes('expirado') || 
                error.message.includes('inválido')) {
                errorMessage = 'El código de recuperación no es válido o ha expirado. Solicita un nuevo código.';
            } else if (error.message.includes('requerido')) {
                errorMessage = 'Datos incompletos. Verifica e inténtalo nuevamente.';
            } else if (error.message.includes('Error interno del servidor')) {
                errorMessage = 'Error interno del servidor. Inténtalo más tarde.';
            } else if (!error.message || error.message.includes('fetch')) {
                errorMessage = 'Error de conexión. Verifica tu conexión a internet e inténtalo nuevamente.';
            }
            
            return {
                success: false,
                message: errorMessage,
                token: null,
                data: null
            };
        }
    }

    /**
     * Reenvía el código de verificación al usuario
     * @param {number} userId - ID del usuario
     * @param {string} contactValue - Correo electrónico o número de teléfono del usuario
     * @param {string} contactType - Tipo de contacto ('email' o 'phone')
     * @returns {Promise<Object>} - Respuesta de la API
     */
    async resendVerificationCode(userId, contactValue, contactType = 'phone') {
        try {
            console.log(`Reenviando código de verificación para ${contactType}: ${contactValue}`);
            
            const url = `${this.baseUrl}/auth/recovery/start`;
            
            // Determinar contactTypeId según el tipo
            const contactTypeId = contactType === 'email' ? 1 : 2;
            
            let formattedContactValue;
            if (contactType === 'phone') {
                formattedContactValue = this.formatPhoneNumber(contactValue.trim());
            } else {
                formattedContactValue = contactValue.trim().toLowerCase();
            }
            
            const requestBody = {
                contactTypeId,
                contactValue: formattedContactValue,
                userId: userId // Incluir userId para indicar que es un reenvío
            };

            console.log('Enviando datos para reenvío:', requestBody);

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(requestBody)
            });

            const responseData = await response.json();
            console.log('Respuesta del servidor:', responseData);

            if (!response.ok) {
                throw new Error(responseData.message || `Error HTTP: ${response.status}`);
            }

            // Verificar si la respuesta fue exitosa
            if (responseData.success === false) {
                throw new Error(responseData.message || 'Error al reenviar código de verificación');
            }

            const contactMethod = contactType === 'email' ? 'correo electrónico' : 'número de teléfono';
            return {
                success: true,
                message: `Se ha reenviado el código de verificación a tu ${contactMethod}.`,
                data: responseData.data || null
            };

        } catch (error) {
            console.error('Error al reenviar código de verificación:', error);
            
            // Mejorar manejo de errores específicos
            let errorMessage = error.message || 'Error interno del servidor. Inténtalo más tarde.';
            
            // Detectar errores SMTP específicos
            if (errorMessage.toLowerCase().includes('smtp') || 
                errorMessage.toLowerCase().includes('authentication') ||
                errorMessage.toLowerCase().includes('mail') ||
                errorMessage.toLowerCase().includes('invalid login')) {
                errorMessage = 'Error temporal con el servicio de correo electrónico. Inténtalo de nuevo en unos minutos.';
            }
            
            return {
                success: false,
                message: errorMessage,
                data: null
            };
        }
    }

    /**
     * Valida el formato del correo electrónico
     * @param {string} email - Correo electrónico a validar
     * @returns {boolean} - true si es válido, false si no
     */
    validateEmailFormat(email) {
        if (!email || typeof email !== 'string') {
            return false;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email.trim());
    }

    /**
     * Valida el formato del número de teléfono
     * @param {string} phoneNumber - Número de teléfono a validar
     * @returns {boolean} - true si es válido, false si no
     */
    validatePhoneFormat(phoneNumber) {
        if (!phoneNumber || typeof phoneNumber !== 'string') {
            return false;
        }

        const cleaned = phoneNumber.trim();
        
        // Remover espacios y caracteres especiales para validar solo números
        const numbersOnly = cleaned.replace(/[\s\-\(\)\+]/g, '');
        
        // Verificar que tenga entre 9 y 15 dígitos (estándar internacional)
        const isValidLength = numbersOnly.length >= 9 && numbersOnly.length <= 15;
        
        // Verificar que solo contenga números después de limpiar
        const isNumeric = /^\d+$/.test(numbersOnly);
        
        return isValidLength && isNumeric;
    }

    /**
     * Valida el formato del código de recuperación
     * @param {string} code - Código a validar
     * @returns {boolean} - true si es válido, false si no
     */
    validateCodeFormat(code) {
        if (!code || typeof code !== 'string') {
            return false;
        }

        const cleaned = code.trim();
        
        // El código debe tener entre 4 y 8 caracteres alfanuméricos
        const isValidLength = cleaned.length >= 4 && cleaned.length <= 8;
        
        // Verificar que solo contenga letras y números
        const isAlphaNumeric = /^[A-Za-z0-9]+$/.test(cleaned);
        
        return isValidLength && isAlphaNumeric;
    }

    /**
     * Formatea el número de teléfono para enviarlo a la API
     * @param {string} phoneNumber - Número de teléfono a formatear
     * @returns {string} - Número formateado
     */
    formatPhoneNumber(phoneNumber) {
        if (!phoneNumber) return '';
        
        let cleaned = phoneNumber.trim();
        
        // Si no empieza con +, asumimos que es un número peruano y agregamos +51
        if (!cleaned.startsWith('+')) {
            // Si empieza con 51, solo agregamos el +
            if (cleaned.startsWith('51')) {
                cleaned = '+' + cleaned;
            } else {
                // Si no empieza con 51, asumimos que es un número local peruano
                cleaned = '+51' + cleaned;
            }
        }
          return cleaned;
    }    /**
     * Cambia la contraseña del usuario después de la verificación
     * @param {number} userId - ID del usuario
     * @param {string} newPassword - Nueva contraseña
     * @param {string} repeatPassword - Repetir nueva contraseña
     * @param {string} token - Token de autorización obtenido en la verificación
     * @returns {Promise<Object>} - Respuesta de la API
     */
    async changePassword(userId, newPassword, repeatPassword, token) {
        try {
            console.log(`Cambiando contraseña para usuario: ${userId}`);
            
            const url = `${this.baseUrl}/auth/recovery/change-password`;
            
            const requestBody = {
                userId: userId,
                newPassword: newPassword,
                repeatPassword: repeatPassword
            };

            console.log('Enviando solicitud de cambio de contraseña...');

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(requestBody)
            });

            const data = await response.json();
            
            console.log('Respuesta del servidor:', data);

            if (data.success) {
                return {
                    success: true,
                    message: data.data?.message || 'Contraseña cambiada exitosamente'
                };
            } else {
                return {
                    success: false,
                    message: data.error?.message || data.message || 'Error al cambiar la contraseña'
                };
            }

        } catch (error) {
            console.error('Error al cambiar contraseña:', error);
            return {
                success: false,
                message: 'Error de conexión. Inténtalo más tarde.'
            };
        }
    }

    /**
     * Valida que la contraseña cumpla con los requisitos de seguridad
     * @param {string} password - Contraseña a validar
     * @returns {Object} - Resultado de la validación con detalles
     */
    validatePasswordStrength(password) {
        if (!password) {
            return { isValid: false, message: 'La contraseña es requerida' };
        }

        const requirements = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /[0-9]/.test(password),
            special: /[@$!%*?&]/.test(password)
        };

        const isValid = Object.values(requirements).every(req => req);

        return {
            isValid,
            requirements,
            message: isValid ? 'Contraseña válida' : 'La contraseña no cumple con todos los requisitos'
        };
    }
}

// Exportar la clase para que esté disponible en otros archivos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PasswordRecoveryService;
} else {
    // Para navegadores sin soporte de módulos
    window.PasswordRecoveryService = PasswordRecoveryService;
}
