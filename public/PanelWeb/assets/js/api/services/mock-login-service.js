// assets/js/api/services/mock-login-service.js

class MockLoginService {
  constructor() {
    console.log("Inicializando servicio de login simulado");
  }

  async verifyCredentials(documentValue, password) {
    console.log('Enviando request a la API:', {
        documentValue,
        password,
        web: true
    });

    // Simular delay de red
    await new Promise(resolve => setTimeout(resolve, 1000));

    // Simular respuesta de la API
    if (documentValue === "73605624" && password === "1234asdA@") {
        return {
            success: true,
            message: "Login exitoso",
            data: {
                token: "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
                userId: 12345,
                documentValue: "73605624",
                roles: ["admin"],
                permissions: ["read", "write", "approve_drivers"],
                availability: true
            },
            error: null,
            timestamp: new Date().toISOString()
        };
    }

    return {
        success: false,
        message: "Credenciales inválidas",
        data: null,
        error: ["Credenciales incorrectas"],
        timestamp: new Date().toISOString()
    };
  }

  // Método para simular peticiones autenticadas
  async fetchWithToken(url, options = {}) {
    const token = sessionStorage.getItem("authToken");
    
    if (!token) {
      throw new Error("No hay token de autenticación");
    }
    
    console.log(`Simulando petición autenticada a: ${url}`);
    console.log(`Token utilizado: ${token}`);
    
    // Simular un retraso de red
    await new Promise(resolve => setTimeout(resolve, 500));
    
    // Simular una respuesta exitosa
    return {
      ok: true,
      json: async () => ({ success: true, data: { message: "Datos obtenidos correctamente" } })
    };
  }
}

// Exportar la clase para uso global
window.LoginService = new MockLoginService();