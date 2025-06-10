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

      // Manejar errores basándose en el formato específico de tu API
      // Primero verificar si success es false (independientemente del status HTTP)
      if (baseResponse.success === false) {
        let errorMessage = 'Error de autenticación';
        
        // Priorizar el mensaje del error.message si existe
        if (baseResponse.error && baseResponse.error.message) {
          errorMessage = baseResponse.error.message;
        } 
        // Si no hay error.message, usar el message principal
        else if (baseResponse.message) {
          errorMessage = baseResponse.message;
        }
        
        // Mapear mensajes específicos para mejorar la experiencia del usuario
        switch (errorMessage) {
          case "Documento o contraseña incorrectos":
            errorMessage = "El documento o contraseña ingresados son incorrectos. Verifica tus datos.";
            break;
          case "Credenciales inválidas":
            errorMessage = "Las credenciales proporcionadas no son válidas. Intenta nuevamente.";
            break;
          case "Ocurrió un error inesperado":
            errorMessage = "Error interno del servidor. Intenta más tarde o contacta al administrador.";
            break;
          case "Petición inválida":
            errorMessage = "Los datos enviados no son válidos. Verifica el formato del documento.";
            break;
          default:
            // Usar el mensaje original si no coincide con los casos específicos
            break;
        }
        
        console.error("Error de autenticación:", errorMessage);
        throw new Error(errorMessage);
      }

      // Verificar errores HTTP adicionales (por si el servidor devuelve errores sin success:false)
      if (!response.ok) {
        let errorMessage = 'Error del servidor';
        
        switch (response.status) {
          case 400:
            errorMessage = baseResponse?.error?.message || baseResponse?.message || 'Datos de entrada inválidos';
            break;
          case 401:
            errorMessage = baseResponse?.error?.message || baseResponse?.message || 'Credenciales inválidas';
            break;
          case 403:
            errorMessage = 'Acceso denegado. No tienes permisos para realizar esta acción.';
            break;
          case 404:
            errorMessage = 'Servicio no encontrado. Verifica la configuración.';
            break;
          case 500:
            errorMessage = baseResponse?.error?.message || 'Error interno del servidor. Intenta más tarde.';
            break;
          case 502:
          case 503:
          case 504:
            errorMessage = 'Servidor no disponible temporalmente. Intenta más tarde.';
            break;
          default:
            errorMessage = baseResponse?.error?.message || baseResponse?.message || `Error HTTP: ${response.status}`;
        }
          console.error("Error HTTP:", response.status, errorMessage);
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