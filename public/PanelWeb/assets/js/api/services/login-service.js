// Clase para manejar la autenticación
class LoginService {
  constructor() {
    // URL base para la API real
    this.baseUrl = "https://149.130.161.148/api/v1";
  }
  // Método para verificar credenciales de login
  async verifyCredentials(username, password) {
    try {
      console.log(`Verificando credenciales para: ${username}`);
      
      // Validar parámetros de entrada
      if (!username || !password) {
        throw new Error('Usuario y contraseña son requeridos');
      }
      
      // Crear el objeto de datos en el formato requerido
      const loginData = {
        documentValue: username.toString().trim(),
        password: password.toString().trim(),
        web: true
      };
      
      console.log('Enviando datos:', { ...loginData, password: '[OCULTA]' });
      console.log('⚠️ NOTA: Si ves errores de certificado SSL, es normal en desarrollo');
      
      // Intentar múltiples estrategias para manejar problemas SSL
      let response;
      let lastError;
      
      // Estrategia 1: HTTPS normal (puede fallar por SSL)
      try {
        console.log('🔒 Intentando HTTPS con certificado SSL...');
        response = await fetch(`${this.baseUrl}/auth/login`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify(loginData)
        });
        console.log('✅ HTTPS exitoso');
      } catch (sslError) {
        console.warn('❌ HTTPS falló (problema SSL):', sslError.message);
        lastError = sslError;


        
      }

      console.log('Status:', response.status, response.statusText);

      // Leer la respuesta como texto primero para manejar errores
      const responseText = await response.text();
      console.log('Response text:', responseText);
      
      let baseResponse;
      
      try {
        baseResponse = JSON.parse(responseText);
      } catch (parseError) {
        console.error('Error al parsear respuesta JSON:', parseError);
        console.error('Texto de respuesta:', responseText);
        throw new Error('Error en la comunicación con el servidor');
      }
      
      console.log("Respuesta del servidor:", baseResponse);

      // Manejar errores según la documentación de la API
      if (!response.ok) {
        let errorMessage = 'Error del servidor';
        
        // Mapear errores según la documentación
        switch (response.status) {
          case 400:
            // Error de validación - usar el mensaje específico del servidor
            errorMessage = baseResponse?.error?.message || baseResponse?.message || 'Documento o contraseña incorrectos';
            break;
          case 401:
            // Credenciales inválidas
            errorMessage = baseResponse?.error?.message || baseResponse?.message || 'Credenciales inválidas';
            break;
          case 500:
            // Error interno del servidor
            errorMessage = 'Error interno del servidor. Inténtalo más tarde.';
            break;
          default:
            errorMessage = baseResponse?.error?.message || baseResponse?.message || `Error HTTP: ${response.status}`;
        }
        
        throw new Error(errorMessage);
      }
      
      // Verificar si la respuesta fue exitosa
      if (!baseResponse.success) {
        const errorMessage = baseResponse?.error?.message || baseResponse?.message || 'Error de autenticación';
        throw new Error(errorMessage);
      }
      
      // Verificar que tenemos los datos necesarios
      if (!baseResponse.data || !baseResponse.data.token) {
        throw new Error('Respuesta del servidor incompleta');
      }
      
      // Devolver los datos de autenticación (AuthLoginResponseDTO)
      return baseResponse.data;
      
    } catch (error) {
      console.error("Error al verificar credenciales:", error);
      
      // Si es un error de red (Failed to fetch), dar un mensaje más específico
      if (error.message === 'Failed to fetch') {
        throw new Error('El servidor esté disponible.');
      }
      
      throw error;
    }
  }

  // Método para obtener datos con el token
  async fetchWithToken(url, options = {}) {
    const token = sessionStorage.getItem("authToken");
    
    if (!token) {
      throw new Error("No hay token de autenticación");
    }
    
    const headers = {
      ...options.headers,
      'Authorization': `Bearer ${token}`
    };
    
    return fetch(url, {
      ...options,
      headers
    });
  }
}

// Exportar la clase para que esté disponible en otros archivos
if (typeof module !== "undefined" && module.exports) {
  module.exports = LoginService;
} else {
  // Para navegadores sin soporte de módulos
  window.LoginService = new LoginService();
}