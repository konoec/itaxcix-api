/**
 * Servicio para la creación de usuarios administradores
 * Endpoint: POST /api/v1/users
 */
class UserCreateService {
    
    /**
     * Crea un nuevo usuario administrador
     * @param {Object} userData - Datos del usuario a crear
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async createUser(userData) {
        console.log('📡 UserCreateService.createUser - Datos a enviar:', userData);
        
        try {
            // Validar datos antes de enviar
            const validationResult = this.validateUserData(userData);
            if (!validationResult.isValid) {
                return {
                    success: false,
                    message: validationResult.message,
                    errors: validationResult.errors
                };
            }

            // Preparar los datos según la estructura esperada por la API
            const payload = this.preparePayload(userData);
            console.log('📡 Payload preparado:', payload);

            // Realizar la petición
            const response = await fetch('/api/v1/users', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${localStorage.getItem('authToken') || ''}`
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();
            console.log('📡 Respuesta de /api/v1/users:', data);

            if (response.ok && data.success) {
                return {
                    success: true,
                    message: data.message || 'Usuario creado exitosamente',
                    data: data.data
                };
            } else {
                return {
                    success: false,
                    message: data.message || 'Error al crear el usuario',
                    errors: data.errors || {}
                };
            }

        } catch (error) {
            console.error('❌ Error en UserCreateService.createUser:', error);
            return {
                success: false,
                message: 'Error de conexión al crear el usuario',
                error: error.message
            };
        }
    }

    /**
     * Valida los datos del usuario antes de enviarlos
     * @param {Object} userData - Datos del usuario
     * @returns {Object} Resultado de la validación
     */
    static validateUserData(userData) {
        const errors = {};
        let isValid = true;

        // Validar documento (requerido)
        if (!userData.document || userData.document.trim().length === 0) {
            errors.document = 'El documento es requerido';
            isValid = false;
        } else if (!/^\d{8}$/.test(userData.document.trim())) {
            errors.document = 'El documento debe tener exactamente 8 dígitos';
            isValid = false;
        }

        // Validar email (requerido)
        if (!userData.email || userData.email.trim().length === 0) {
            errors.email = 'El email es requerido';
            isValid = false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(userData.email.trim())) {
            errors.email = 'El formato del email no es válido';
            isValid = false;
        }

        // Validar contraseña (requerida)
        if (!userData.password || userData.password.length < 8) {
            errors.password = 'La contraseña debe tener al menos 8 caracteres';
            isValid = false;
        }

        // Validar área (requerida)
        if (!userData.area || userData.area.trim().length === 0) {
            errors.area = 'El área es requerida';
            isValid = false;
        }

        // Validar posición (requerida)
        if (!userData.position || userData.position.trim().length === 0) {
            errors.position = 'La posición es requerida';
            isValid = false;
        }

        return {
            isValid,
            message: isValid ? 'Datos válidos' : 'Hay errores en los datos proporcionados',
            errors
        };
    }

    /**
     * Prepara el payload según la estructura esperada por la API
     * @param {Object} userData - Datos del usuario
     * @returns {Object} Payload formateado
     */
    static preparePayload(userData) {
        // Estructura simple según la documentación de la API
        return {
            document: userData.document.trim(),
            email: userData.email.trim(),
            password: userData.password,
            area: userData.area.trim(),
            position: userData.position.trim()
        };
    }

    /**
     * Verifica si un documento ya existe
     * @param {string} document - Documento a verificar
     * @returns {Promise<Object>} Resultado de la verificación
     */
    static async checkDocumentExists(document) {
        try {
            const response = await fetch(`/api/v1/users/check-document?document=${encodeURIComponent(document)}`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('authToken') || ''}`
                }
            });

            const data = await response.json();
            return {
                success: response.ok,
                exists: data.exists || false,
                message: data.message || ''
            };
        } catch (error) {
            console.error('❌ Error verificando documento:', error);
            return {
                success: false,
                exists: false,
                message: 'Error de conexión'
            };
        }
    }

    /**
     * Verifica si un email ya existe
     * @param {string} email - Email a verificar
     * @returns {Promise<Object>} Resultado de la verificación
     */
    static async checkEmailExists(email) {
        try {
            const response = await fetch(`/api/v1/users/check-email?email=${encodeURIComponent(email)}`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('authToken') || ''}`
                }
            });

            const data = await response.json();
            return {
                success: response.ok,
                exists: data.exists || false,
                message: data.message || ''
            };
        } catch (error) {
            console.error('❌ Error verificando email:', error);
            return {
                success: false,
                exists: false,
                message: 'Error de conexión'
            };
        }
    }
}

// Hacer disponible globalmente
window.UserCreateService = UserCreateService;
