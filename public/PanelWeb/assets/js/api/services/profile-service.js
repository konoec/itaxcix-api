// Clase para manejar el perfil del usuario
class ProfileService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1'; // URL base de la API
    }

    /**
     * Obtiene la foto de perfil del usuario
     * @param {string} userId - ID del usuario
     * @returns {Promise<string>} - Base64 de la imagen o null si no existe
     */
    async getProfilePhoto(userId) {
        if (!userId) {
            throw new Error('ID de usuario requerido');
        }

        try {
            console.log(`📸 Obteniendo foto de perfil para usuario ${userId}...`);

            const token = sessionStorage.getItem("authToken");
            if (!token) {
                throw new Error('No hay token de autenticación');
            }

            // Intentar múltiples estrategias para manejar problemas SSL
            let response;
            let lastError;

            // Estrategia 1: HTTPS normal
            try {
                console.log('🔒 Intentando HTTPS...');
                response = await fetch(`${this.baseUrl}/users/${userId}/profile-photo`, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json'
                    }
                });
                console.log('✅ HTTPS exitoso');
            } catch (sslError) {
                console.warn('❌ HTTPS falló (problema SSL):', sslError.message);
                lastError = sslError;

                // Estrategia 2: HTTP
                const httpUrl = this.baseUrl.replace('https://', 'http://');
                try {
                    console.log('🔓 Intentando HTTP...');
                    response = await fetch(`${httpUrl}/users/${userId}/profile-photo`, {
                        method: 'GET',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Accept': 'application/json'
                        }
                    });
                    console.log('✅ HTTP exitoso');
                } catch (httpError) {
                    console.warn('❌ HTTP también falló:', httpError.message);
                    throw new Error('Error de conexión al servidor');
                }
            }

            if (!response.ok) {
                if (response.status === 404) {
                    console.log('📸 No se encontró foto de perfil para el usuario');
                    return null; // No hay foto, esto es normal
                }
                
                if (response.status === 401) {
                    throw new Error('Token de autenticación inválido');
                }

                throw new Error(`Error del servidor: ${response.status}`);
            }

            const data = await response.json();

            if (!data.success) {
                console.log('📸 Respuesta del servidor:', data.message || 'Foto no encontrada');
                return null; // No hay foto disponible
            }

            if (!data.data || !data.data.base64Image) {
                console.log('📸 No se recibió imagen en la respuesta');
                return null;
            }

            console.log('✅ Foto de perfil obtenida exitosamente');
            return data.data.base64Image;

        } catch (error) {
            console.error('❌ Error al obtener foto de perfil:', error);
            
            // Si es un error de red, es mejor no mostrar error al usuario
            if (error.message.includes('Failed to fetch') || 
                error.message.includes('conexión') ||
                error.message.includes('certificado') ||
                error.message.includes('SSL')) {
                console.log('📸 Error de red, usando avatar por defecto');
                return null;
            }

            throw error;
        }
    }    /**
     * Convierte una imagen base64 a URL de datos
     * @param {string} base64String - String base64 de la imagen
     * @returns {string} - URL de datos para usar en src de img
     */
    base64ToImageUrl(base64String) {
        if (!base64String) {
            return null;
        }

        // Si ya tiene el prefijo data:image, devolverlo tal como está
        if (base64String.startsWith('data:image/')) {
            console.log('📸 Base64 ya tiene formato data URL completo');
            return base64String;
        }

        // Extraer solo la parte base64 si viene con prefijo parcial
        let cleanBase64 = base64String;
        if (base64String.includes('base64,')) {
            cleanBase64 = base64String.split('base64,')[1];
            console.log('📸 Extraído contenido base64 puro');
        }

        // Detectar el tipo de imagen (por defecto JPEG)
        let mimeType = 'image/jpeg';
        
        // Detectar PNG
        if (cleanBase64.startsWith('iVBORw0KGgo')) {
            mimeType = 'image/png';
        }
        // Detectar GIF
        else if (cleanBase64.startsWith('R0lGODlh')) {
            mimeType = 'image/gif';
        }
        // Detectar WebP
        else if (cleanBase64.startsWith('UklGR')) {
            mimeType = 'image/webp';
        }
        // Detectar JPEG (múltiples firmas posibles)
        else if (cleanBase64.startsWith('/9j/') || cleanBase64.startsWith('iVBORw0KGgoAAAANSUhEUgAA')) {
            mimeType = 'image/jpeg';
        }

        console.log(`📸 Tipo de imagen detectado: ${mimeType}`);
        return `data:${mimeType};base64,${cleanBase64}`;
    }

    /**
     * Obtiene la URL de avatar por defecto
     * @returns {string} - URL del avatar por defecto
     */
    getDefaultAvatarUrl() {
        return 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMjAiIGN5PSIyMCIgcj0iMjAiIGZpbGw9IiM2MzczZDAiLz4KPGNpcmNsZSBjeD0iMjAiIGN5PSIxNiIgcj0iNiIgZmlsbD0id2hpdGUiLz4KPHBhdGggZD0iTTEwIDMyYzAtNS41MjMgNC40NzctMTAgMTAtMTBzMTAgNC40NzcgMTAgMTAiIGZpbGw9IndoaXRlIi8+Cjwvc3ZnPgo=';
    }

    /**
     * Método de prueba para validar el formato base64 recibido
     * @param {string} base64Data - Datos base64 recibidos de la API
     * @returns {Object} - Información sobre el formato detectado
     */
    validateBase64Format(base64Data) {
        const result = {
            original: base64Data,
            hasDataPrefix: false,
            detectedType: 'unknown',
            isValid: false,
            processedUrl: null
        };

        if (!base64Data) {
            return result;
        }

        // Verificar si ya tiene prefijo data:
        result.hasDataPrefix = base64Data.startsWith('data:image/');
        
        // Si ya es un data URL completo, usarlo directamente
        if (result.hasDataPrefix) {
            result.detectedType = 'complete-data-url';
            result.isValid = true;
            result.processedUrl = base64Data;
            return result;
        }

        // Extraer contenido base64 puro
        let cleanBase64 = base64Data;
        if (base64Data.includes('base64,')) {
            cleanBase64 = base64Data.split('base64,')[1];
        }

        // Detectar tipo por firma
        if (cleanBase64.startsWith('/9j/')) {
            result.detectedType = 'jpeg';
            result.isValid = true;
        } else if (cleanBase64.startsWith('iVBORw0KGgo')) {
            result.detectedType = 'png';
            result.isValid = true;
        } else if (cleanBase64.startsWith('R0lGODlh')) {
            result.detectedType = 'gif';
            result.isValid = true;
        } else if (cleanBase64.startsWith('UklGR')) {
            result.detectedType = 'webp';
            result.isValid = true;
        }

        // Generar URL procesada
        if (result.isValid) {
            const mimeType = result.detectedType === 'jpeg' ? 'image/jpeg' :
                           result.detectedType === 'png' ? 'image/png' :
                           result.detectedType === 'gif' ? 'image/gif' :
                           result.detectedType === 'webp' ? 'image/webp' : 'image/jpeg';
            
            result.processedUrl = `data:${mimeType};base64,${cleanBase64}`;
        }

        return result;
    }
}

// Exportar la clase para que esté disponible en otros archivos
if (typeof module !== "undefined" && module.exports) {
    module.exports = ProfileService;
} else {
    // Para navegadores sin soporte de módulos
    window.ProfileService = new ProfileService();
}
