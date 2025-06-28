// Servicio para manejar el perfil del usuario
class ProfileService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        
        // Cache para optimizar rendimiento
        this.imageCache = new Map();
        this.defaultAvatarCache = null;
        
        console.log('📋 ProfileService inicializado con base URL:', this.baseUrl);
    }

    /**
     * Obtiene la foto de perfil del usuario
     * @param {string} userId - ID del usuario
     * @returns {Promise<string|null>} - Base64 de la imagen o null si no existe
     */
    async getProfilePhoto(userId) {
        if (!userId) {
            console.warn('⚠️ getProfilePhoto: userId requerido');
            return null;
        }

        // Verificar cache primero
        const cacheKey = `profile_photo_${userId}`;
        if (this.imageCache.has(cacheKey)) {
            console.log('📸 Usando foto de perfil desde cache');
            return this.imageCache.get(cacheKey);
        }

        try {
            console.log(`📸 Obteniendo foto de perfil para usuario ${userId}...`);

            const token = sessionStorage.getItem("authToken");
            if (!token) {
                console.warn('⚠️ No hay token de autenticación');
                return null;
            }

            // Timeout optimizado para mejor UX
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 3000);

            try {
                const response = await fetch(`${this.baseUrl}/users/${userId}/profile-photo`, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    },
                    signal: controller.signal
                });

                clearTimeout(timeoutId);

                if (!response.ok) {
                    if (response.status === 404) {
                        console.log('📸 No se encontró foto de perfil');
                        this.imageCache.set(cacheKey, null);
                        return null;
                    }
                    
                    if (response.status === 401) {
                        console.warn('⚠️ Token inválido para obtener foto');
                        return null;
                    }

                    throw new Error(`Error del servidor: ${response.status}`);
                }

                const data = await response.json();

                if (data.success && data.data && data.data.base64Image) {
                    console.log('✅ Foto de perfil obtenida exitosamente');
                    
                    // Guardar en cache
                    this.imageCache.set(cacheKey, data.data.base64Image);
                    
                    return data.data.base64Image;
                } else {
                    console.log('📸 Respuesta sin imagen válida');
                    this.imageCache.set(cacheKey, null);
                    return null;
                }

            } catch (error) {
                clearTimeout(timeoutId);
                
                if (error.name === 'AbortError') {
                    console.log('📸 Timeout al cargar foto de perfil');
                    return null;
                }
                
                throw error;
            }

        } catch (error) {
            console.log('📸 Error al obtener foto de perfil:', error.message);
            return null;
        }
    }

    /**
     * Sube una nueva foto de perfil
     * @param {string} userId - ID del usuario
     * @param {string} base64Image - Imagen en formato base64
     * @returns {Promise<Object>} - Resultado de la operación
     */
    async uploadProfilePhoto(userId, base64Image) {
        if (!userId) {
            return { success: false, message: 'ID de usuario requerido' };
        }

        if (!base64Image) {
            return { success: false, message: 'Imagen requerida' };
        }

        try {
            console.log(`📤 Subiendo foto de perfil para usuario ${userId}...`);

            const token = sessionStorage.getItem("authToken");
            if (!token) {
                return { success: false, message: 'No hay token de autenticación' };
            }

            // Preparar imagen en formato requerido por el servidor
            let processedImage = base64Image;
            
            // Si no tiene prefijo, agregarlo (formato JPEG por defecto)
            if (!base64Image.startsWith('data:image/')) {
                processedImage = `data:image/jpeg;base64,${base64Image}`;
            }

            const requestBody = {
                base64Image: processedImage
            };

            const response = await fetch(`${this.baseUrl}/users/${userId}/profile-photo`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(requestBody)
            });

            if (!response.ok) {
                let errorMessage = `Error del servidor: ${response.status}`;
                
                try {
                    const errorData = await response.json();
                    if (errorData.message) {
                        errorMessage = errorData.message;
                    }
                } catch (e) {
                    // Si no se puede parsear el error, usar el mensaje por defecto
                }

                if (response.status === 401) {
                    errorMessage = 'Token de autenticación inválido';
                } else if (response.status === 404) {
                    errorMessage = 'Usuario no encontrado';
                } else if (response.status === 400) {
                    errorMessage = 'Datos de imagen inválidos';
                }

                return { success: false, message: errorMessage };
            }

            const data = await response.json();

            if (data.success) {
                console.log('✅ Foto de perfil subida exitosamente');
                
                // Limpiar cache para forzar recarga
                const cacheKey = `profile_photo_${userId}`;
                this.imageCache.delete(cacheKey);
                
                return { success: true, message: data.message || 'Foto actualizada correctamente' };
            } else {
                return { success: false, message: data.message || 'Error al procesar la imagen' };
            }

        } catch (error) {
            console.error('❌ Error al subir foto de perfil:', error);
            return { success: false, message: 'Error de conexión al servidor' };
        }
    }    /**
     * Obtiene información del perfil del usuario administrador
     * @param {string} userId - ID del usuario
     * @returns {Promise<Object|null>} - Datos del perfil o null
     */
    async getUserProfile(userId) {
        if (!userId) {
            console.warn('⚠️ getUserProfile: userId requerido');
            return null;
        }

        try {
            console.log(`👤 Obteniendo datos de perfil de administrador para usuario ${userId}...`);

            const token = sessionStorage.getItem("authToken");
            if (!token) {
                console.warn('⚠️ No hay token de autenticación');
                return null;
            }

            const response = await fetch(`${this.baseUrl}/profile/admin/${userId}`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                if (response.status === 404) {
                    console.log('👤 Perfil de administrador no encontrado');
                    return null;
                }
                
                if (response.status === 401) {
                    console.warn('⚠️ Token inválido para obtener perfil');
                    return null;
                }

                throw new Error(`Error del servidor: ${response.status}`);
            }

            const response_data = await response.json();

            if (response_data && response_data.success && response_data.data) {
                console.log('✅ Datos de perfil de administrador obtenidos exitosamente');
                
                // Estructura de respuesta esperada:
                // {
                //   "success": true,
                //   "message": "OK",
                //   "data": {
                //     "firstName": "Administrador",
                //     "lastName": "Principal",
                //     "documentType": "DNI",
                //     "document": "00000000",
                //     "area": "Sistemas",
                //     "position": "Administrador General",
                //     "email": "itaxcix@gmail.com",
                //     "phone": "Teléfono no registrado"
                //   },
                //   "error": null,
                //   "timestamp": {...}
                // }
                
                const profileData = response_data.data;
                
                return {
                    firstName: profileData.firstName || '',
                    lastName: profileData.lastName || '',
                    documentType: profileData.documentType || '',
                    document: profileData.document || '',
                    area: profileData.area || '',
                    position: profileData.position || '',
                    email: profileData.email || '',
                    phone: profileData.phone || ''
                };
            } else {
                console.log('👤 Respuesta sin datos válidos o con error:', {
                    success: response_data?.success,
                    message: response_data?.message,
                    error: response_data?.error
                });
                return null;
            }

        } catch (error) {
            console.log('👤 Error al obtener datos de perfil:', error.message);
            return null;
        }
    }

    /**
     * Convierte una imagen base64 a URL de datos válida
     * @param {string} base64String - String base64 de la imagen
     * @returns {string|null} - URL de datos o null si es inválida
     */
    base64ToImageUrl(base64String) {
        if (!base64String) {
            return null;
        }

        // Si ya tiene el prefijo data:image, devolverlo como está
        if (base64String.startsWith('data:image/')) {
            return base64String;
        }

        // Extraer contenido base64 puro
        let cleanBase64 = base64String;
        if (base64String.includes('base64,')) {
            cleanBase64 = base64String.split('base64,')[1];
        }

        // Detectar tipo de imagen por firma
        let mimeType = 'image/jpeg'; // Por defecto

        if (cleanBase64.startsWith('iVBORw0KGgo')) {
            mimeType = 'image/png';
        } else if (cleanBase64.startsWith('R0lGODlh')) {
            mimeType = 'image/gif';
        } else if (cleanBase64.startsWith('UklGR')) {
            mimeType = 'image/webp';
        } else if (cleanBase64.startsWith('/9j/')) {
            mimeType = 'image/jpeg';
        }

        return `data:${mimeType};base64,${cleanBase64}`;
    }

    /**
     * Obtiene la URL del avatar por defecto
     * @returns {string} - URL del avatar por defecto
     */
    getDefaultAvatarUrl() {
        // Cache del avatar por defecto para mejor rendimiento
        if (!this.defaultAvatarCache) {
            this.defaultAvatarCache = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMjAiIGZpbGw9IiM2MzczZDAiLz4KPGNpcmNsZSBjeD0iMjAiIGN5PSIxNiIgcj0iNiIgZmlsbD0id2hpdGUiLz4KPHBhdGggZD0iTTEwIDMyYzAtNS41MjMgNC40NzctMTAgMTAtMTBzMTAgNC40NzcgMTAgMTAiIGZpbGw9IndoaXRlIi8+Cjwvc3ZnPgo=';
        }
        
        return this.defaultAvatarCache;
    }

    /**
     * Valida si una imagen base64 es válida
     * @param {string} base64String - String base64 a validar
     * @returns {Object} - Resultado de la validación
     */
    validateBase64Image(base64String) {
        const result = {
            isValid: false,
            error: null,
            sizeKB: 0,
            format: 'unknown'
        };

        if (!base64String) {
            result.error = 'No se proporcionó imagen';
            return result;
        }

        try {
            // Extraer contenido base64 puro
            let cleanBase64 = base64String;
            if (base64String.includes('base64,')) {
                cleanBase64 = base64String.split('base64,')[1];
            }

            if (!cleanBase64) {
                result.error = 'Base64 vacío';
                return result;
            }

            // Validar formato base64
            try {
                atob(cleanBase64);
            } catch (e) {
                result.error = 'Formato base64 inválido';
                return result;
            }

            // Calcular tamaño aproximado
            result.sizeKB = Math.round((cleanBase64.length * 3) / 4 / 1024);

            // Detectar formato
            if (cleanBase64.startsWith('/9j/')) {
                result.format = 'JPEG';
            } else if (cleanBase64.startsWith('iVBORw0KGgo')) {
                result.format = 'PNG';
            } else if (cleanBase64.startsWith('R0lGODlh')) {
                result.format = 'GIF';
            } else if (cleanBase64.startsWith('UklGR')) {
                result.format = 'WebP';
            }

            // Validar tamaño (máximo 5MB)
            if (result.sizeKB > 5120) {
                result.error = `Imagen muy grande (${result.sizeKB}KB). Máximo: 5MB`;
                return result;
            }

            // Validar tamaño mínimo (1KB)
            if (result.sizeKB < 1) {
                result.error = `Imagen muy pequeña (${result.sizeKB}KB). Mínimo: 1KB`;
                return result;
            }

            result.isValid = true;
            return result;

        } catch (error) {
            result.error = 'Error al validar la imagen';
            return result;
        }
    }

    /**
     * Limpia el cache de imágenes
     */
    clearCache() {
        this.imageCache.clear();
        console.log('🧹 Cache de imágenes limpiado');
    }

    /**
     * Obtiene estadísticas del cache
     * @returns {Object} - Estadísticas del cache
     */
    getCacheStats() {
        return {
            imageCount: this.imageCache.size,
            hasDefaultAvatar: !!this.defaultAvatarCache
        };
    }

    /**
     * Debug: Verifica el estado del token de autenticación
     */
    debugAuthToken() {
        const token = sessionStorage.getItem("authToken");
        console.log('🔍 Debug Token:', {
            exists: !!token,
            length: token?.length || 0,
            starts: token?.substring(0, 20) + '...' || 'No token',
            type: typeof token,
            sessionStorageKeys: Object.keys(sessionStorage)
        });
        return token;
    }

    /**
     * Solicita cambio de correo electrónico
     * @param {number} userId - ID del usuario
     * @param {string} newEmail - Nuevo correo electrónico
     * @returns {Promise<Object>} - Resultado de la operación
     */
    async requestEmailChange(userId, newEmail) {
        if (!userId) {
            return { success: false, message: 'ID de usuario requerido' };
        }

        if (!newEmail) {
            return { success: false, message: 'Correo electrónico requerido' };
        }

        // Validar formato de email básico
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(newEmail)) {
            return { success: false, message: 'El formato del correo electrónico es inválido' };
        }

        try {
            console.log(`📧 Solicitando cambio de correo para usuario ${userId} a ${newEmail}...`);

            // Debug del token
            this.debugAuthToken();
            
            const token = sessionStorage.getItem("authToken");
            if (!token) {
                console.error('❌ No se encontró token de autenticación en sessionStorage');
                // Intentar otros nombres posibles para el token
                const alternativeTokens = [
                    sessionStorage.getItem("token"),
                    sessionStorage.getItem("accessToken"),
                    sessionStorage.getItem("jwt"),
                    sessionStorage.getItem("bearerToken")
                ];
                console.log('🔍 Tokens alternativos encontrados:', alternativeTokens.filter(t => t));
                return { success: false, message: 'No hay token de autenticación' };
            }

            console.log('✅ Token encontrado, preparando solicitud...');

            const requestBody = {
                userId: parseInt(userId),
                email: newEmail
            };

            console.log('📤 Enviando solicitud con:', {
                url: `${this.baseUrl}/profile/change-email`,
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token.substring(0, 20)}...`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: requestBody
            });

            const response = await fetch(`${this.baseUrl}/profile/change-email`, {
                method: 'POST',
                headers: {
                    'Authorization': token ? 'Bearer ' + token : '',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(requestBody)
            });

            console.log('📥 Respuesta recibida:', {
                status: response.status,
                statusText: response.statusText,
                ok: response.ok
            });

            if (!response.ok) {
                let errorMessage = `Error del servidor: ${response.status}`;
                
                try {
                    const errorData = await response.json();
                    console.log('📥 Error data received:', errorData);
                    
                    // Priorizar el mensaje específico del error de la API
                    if (errorData.error && errorData.error.message) {
                        errorMessage = errorData.error.message;
                        console.log('✅ Usando mensaje específico del error:', errorMessage);
                    } else if (errorData.message) {
                        errorMessage = errorData.message;
                        console.log('✅ Usando mensaje general:', errorMessage);
                    } else {
                        // Solo usar mensajes genéricos si no hay mensaje específico de la API
                        console.log('⚠️ No hay mensaje específico, usando mensaje genérico');
                        if (response.status === 401) {
                            errorMessage = 'Token de autenticación inválido';
                        } else if (response.status === 404) {
                            errorMessage = 'Usuario no encontrado';
                        } else if (response.status === 400) {
                            errorMessage = 'Datos inválidos';
                        }
                    }
                } catch (e) {
                    console.warn('⚠️ No se pudo parsear respuesta de error:', e);
                    // Si no se puede parsear el error, usar mensajes por defecto
                    if (response.status === 401) {
                        errorMessage = 'Token de autenticación inválido';
                    } else if (response.status === 404) {
                        errorMessage = 'Usuario no encontrado';
                    } else if (response.status === 400) {
                        errorMessage = 'Datos inválidos';
                    }
                }

                console.error(`❌ Error ${response.status}:`, errorMessage);
                return { success: false, message: errorMessage };
            }

            const data = await response.json();

            if (data.success) {
                console.log('✅ Solicitud de cambio de correo enviada exitosamente');
                
                return { 
                    success: true, 
                    message: data.data?.message || 'Se ha enviado un código de verificación a tu nuevo correo',
                    data: data.data
                };
            } else {
                return { 
                    success: false, 
                    message: data.error?.message || data.message || 'Error al procesar la solicitud' 
                };
            }

        } catch (error) {
            console.error('❌ Error al solicitar cambio de correo:', error);
            return { success: false, message: 'Error de conexión al servidor' };
        }
    }

    /**
     * Verifica el código de cambio de correo electrónico
     * @param {number} userId - ID del usuario
     * @param {string} code - Código de verificación
     * @returns {Promise<Object>} - Resultado de la operación
     */
    async verifyEmailChange(userId, code) {
        if (!userId) {
            return { success: false, message: 'ID de usuario requerido' };
        }

        if (!code) {
            return { success: false, message: 'Código de verificación requerido' };
        }

        try {
            console.log(`🔐 Verificando código de cambio de correo para usuario ${userId}...`);

            // Debug del token
            this.debugAuthToken();

            const token = sessionStorage.getItem("authToken");
            if (!token) {
                return { success: false, message: 'No hay token de autenticación' };
            }

            const requestBody = {
                userId: parseInt(userId),
                code: code.toString()
            };

            const response = await fetch(`${this.baseUrl}/profile/verify-email`, {
                method: 'POST',
                headers: {
                    'Authorization': token ? 'Bearer ' + token : '',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(requestBody)
            });

            if (!response.ok) {
                let errorMessage = `Error del servidor: ${response.status}`;
                
                try {
                    const errorData = await response.json();
                    if (errorData.error && errorData.error.message) {
                        errorMessage = errorData.error.message;
                    } else if (errorData.message) {
                        errorMessage = errorData.message;
                    }
                } catch (e) {
                    // Si no se puede parsear el error, usar el mensaje por defecto
                }

                if (response.status === 401) {
                    errorMessage = 'Token de autenticación inválido';
                } else if (response.status === 404) {
                    errorMessage = 'Usuario no encontrado o código inválido';
                } else if (response.status === 400) {
                    errorMessage = 'Código de verificación inválido';
                }

                return { success: false, message: errorMessage };
            }

            const data = await response.json();

            if (data.success) {
                console.log('✅ Código de verificación válido, correo actualizado');
                
                return { 
                    success: true, 
                    message: data.data?.message || 'Correo electrónico actualizado correctamente',
                    data: data.data
                };
            } else {
                return { 
                    success: false, 
                    message: data.error?.message || data.message || 'Código de verificación inválido' 
                };
            }

        } catch (error) {
            console.error('❌ Error al verificar código de cambio de correo:', error);
            return { success: false, message: 'Error de conexión al servidor' };
        }
    }

    /**
     * Solicita cambio de número de teléfono
     * @param {number} userId - ID del usuario
     * @param {string} newPhone - Nuevo número de teléfono
     * @returns {Promise<Object>} - Resultado de la operación
     */
    async requestPhoneChange(userId, newPhone) {
        if (!userId) {
            return { success: false, message: 'ID de usuario requerido' };
        }

        if (!newPhone) {
            return { success: false, message: 'Número de teléfono requerido' };
        }

        // Validar formato de teléfono básico
        const phoneRegex = /^[0-9+\-\s()]+$/;
        if (!phoneRegex.test(newPhone) || newPhone.length < 10) {
            return { success: false, message: 'El formato del número de teléfono es inválido' };
        }

        try {
            console.log(`📱 Solicitando cambio de teléfono para usuario ${userId} a ${newPhone}...`);

            // Debug del token
            this.debugAuthToken();
            
            const token = sessionStorage.getItem("authToken");
            if (!token) {
                console.error('❌ No se encontró token de autenticación en sessionStorage');
                // Intentar otros nombres posibles para el token
                const alternativeTokens = [
                    sessionStorage.getItem("token"),
                    sessionStorage.getItem("accessToken"),
                    sessionStorage.getItem("jwt"),
                    sessionStorage.getItem("bearerToken")
                ];
                console.log('🔍 Tokens alternativos encontrados:', alternativeTokens.filter(t => t));
                return { success: false, message: 'No hay token de autenticación' };
            }

            console.log('✅ Token encontrado, preparando solicitud...');

            const requestBody = {
                userId: parseInt(userId),
                phone: newPhone
            };

            console.log('📤 Enviando solicitud con:', {
                url: `${this.baseUrl}/profile/change-phone`,
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token.substring(0, 20)}...`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: requestBody
            });

            const response = await fetch(`${this.baseUrl}/profile/change-phone`, {
                method: 'POST',
                headers: {
                    'Authorization': token ? 'Bearer ' + token : '',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(requestBody)
            });

            console.log('📥 Respuesta recibida:', {
                status: response.status,
                statusText: response.statusText,
                ok: response.ok
            });

            if (!response.ok) {
                let errorMessage = `Error del servidor: ${response.status}`;
                
                try {
                    const errorData = await response.json();
                    console.log('📥 Error data received:', errorData);
                    
                    // Priorizar el mensaje específico del error de la API
                    if (errorData.error && errorData.error.message) {
                        errorMessage = errorData.error.message;
                        console.log('✅ Usando mensaje específico del error:', errorMessage);
                    } else if (errorData.message) {
                        errorMessage = errorData.message;
                        console.log('✅ Usando mensaje general:', errorMessage);
                    } else {
                        // Solo usar mensajes genéricos si no hay mensaje específico de la API
                        console.log('⚠️ No hay mensaje específico, usando mensaje genérico');
                        if (response.status === 401) {
                            errorMessage = 'Token de autenticación inválido';
                        } else if (response.status === 404) {
                            errorMessage = 'Usuario no encontrado';
                        } else if (response.status === 400) {
                            errorMessage = 'Datos inválidos';
                        }
                    }
                } catch (e) {
                    console.warn('⚠️ No se pudo parsear respuesta de error:', e);
                    // Si no se puede parsear el error, usar mensajes por defecto
                    if (response.status === 401) {
                        errorMessage = 'Token de autenticación inválido';
                    } else if (response.status === 404) {
                        errorMessage = 'Usuario no encontrado';
                    } else if (response.status === 400) {
                        errorMessage = 'Datos inválidos';
                    }
                }

                console.error(`❌ Error ${response.status}:`, errorMessage);
                return { success: false, message: errorMessage };
            }

            const data = await response.json();

            if (data.success) {
                console.log('✅ Solicitud de cambio de teléfono enviada exitosamente');
                
                return { 
                    success: true, 
                    message: data.data?.message || 'Se ha enviado un código de verificación a tu nuevo número',
                    data: data.data
                };
            } else {
                return { 
                    success: false, 
                    message: data.error?.message || data.message || 'Error al procesar la solicitud' 
                };
            }

        } catch (error) {
            console.error('❌ Error al solicitar cambio de teléfono:', error);
            return { success: false, message: 'Error de conexión al servidor' };
        }
    }

    /**
     * Verifica el código de cambio de número de teléfono
     * @param {number} userId - ID del usuario
     * @param {string} code - Código de verificación
     * @returns {Promise<Object>} - Resultado de la operación
     */
    async verifyPhoneChange(userId, code) {
        if (!userId) {
            return { success: false, message: 'ID de usuario requerido' };
        }

        if (!code) {
            return { success: false, message: 'Código de verificación requerido' };
        }

        try {
            console.log(`🔐 Verificando código de cambio de teléfono para usuario ${userId}...`);

            // Debug del token
            this.debugAuthToken();

            const token = sessionStorage.getItem("authToken");
            if (!token) {
                return { success: false, message: 'No hay token de autenticación' };
            }

            const requestBody = {
                userId: parseInt(userId),
                code: code.toString()
            };

            const response = await fetch(`${this.baseUrl}/profile/verify-phone`, {
                method: 'POST',
                headers: {
                    'Authorization': token ? 'Bearer ' + token : '',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(requestBody)
            });

            if (!response.ok) {
                let errorMessage = `Error del servidor: ${response.status}`;
                
                try {
                    const errorData = await response.json();
                    if (errorData.error && errorData.error.message) {
                        errorMessage = errorData.error.message;
                    } else if (errorData.message) {
                        errorMessage = errorData.message;
                    }
                } catch (e) {
                    // Si no se puede parsear el error, usar el mensaje por defecto
                }

                if (response.status === 401) {
                    errorMessage = 'Token de autenticación inválido';
                } else if (response.status === 404) {
                    errorMessage = 'Usuario no encontrado o código inválido';
                } else if (response.status === 400) {
                    errorMessage = 'Código de verificación inválido';
                }

                return { success: false, message: errorMessage };
            }

            const data = await response.json();

            if (data.success) {
                console.log('✅ Código de verificación válido, teléfono actualizado');
                
                return { 
                    success: true, 
                    message: data.data?.message || 'Número telefónico actualizado correctamente',
                    data: data.data
                };
            } else {
                return { 
                    success: false, 
                    message: data.error?.message || data.message || 'Código de verificación inválido' 
                };
            }

        } catch (error) {
            console.error('❌ Error al verificar código de cambio de teléfono:', error);
            return { success: false, message: 'Error de conexión al servidor' };
        }
    }
}

// Exportar para uso en navegador
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ProfileService;
} else {
    // Exportar tanto clase como instancia para máxima compatibilidad
    window.ProfileService = new ProfileService();
    window.ProfileServiceClass = ProfileService;
    
    console.log('✅ ProfileService exportado globalmente');
}