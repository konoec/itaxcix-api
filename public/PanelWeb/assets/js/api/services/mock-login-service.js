// assets/js/api/services/mock-login-service.js

class MockLoginService {
  constructor() {
    console.log("🔧 Inicializando MockLoginService");
    
    // Usuarios de prueba predefinidos
    this.mockUsers = [
      {
        document: "00000000",
        password: "Password@123",
        userData: {
          userId: 11111,
          firstName: "Test",
          lastName: "Admin",
          roles: [
            { id: 1, name: "ADMINISTRADOR", description: "Administrador del sistema" }
          ],
          permissions: [
            { id: 1, name: "CONFIGURACIÓN", description: "Acceso a configuración" },
            { id: 2, name: "USUARIOS", description: "Gestión de usuarios" },
            { id: 3, name: "ROLES", description: "Gestión de roles" }
          ],
          rating: 5.0
        }
      },
      {
        document: "73605624",
        password: "Password@123",
        userData: {
          userId: 12345,
          firstName: "Admin",
          lastName: "Usuario",
          roles: [
            { id: 1, name: "ADMINISTRADOR", description: "Administrador del sistema" }
          ],
          permissions: [
            { id: 1, name: "CONFIGURACIÓN", description: "Acceso a configuración" },
            { id: 2, name: "USUARIOS", description: "Gestión de usuarios" },
            { id: 3, name: "ROLES", description: "Gestión de roles" }
          ],
          rating: 5.0
        }
      },
      {
        document: "12345678",
        password: "123456",
        userData: {
          userId: 22222,
          firstName: "Usuario",
          lastName: "Conductor",
          roles: [
            { id: 2, name: "CONDUCTOR", description: "Conductor del sistema" }
          ],
          permissions: [
            { id: 4, name: "CONDUCTORES", description: "Gestión de conductores" }
          ],
          rating: 4.0
        }
      }
    ];
  }

  async verifyCredentials(documentValue, password) {
    console.log('🔄 MockLoginService: Verificando credenciales:', {
        documentValue,
        password: password ? '***' : 'sin contraseña',
        web: true
    });

    // Simular delay de red realista
    await new Promise(resolve => setTimeout(resolve, 800));

    // Buscar usuario válido
    const validUser = this.mockUsers.find(user => 
        user.document === documentValue && user.password === password
    );

    if (validUser) {
        console.log('✅ MockLoginService: Login exitoso para:', validUser.userData.firstName);
        
        // Retornar en el formato esperado por el controlador
        return {
            success: true,
            message: "Login exitoso",
            token: `mock_token_${validUser.userData.userId}_${Date.now()}`,
            userId: validUser.userData.userId,
            documentValue: documentValue,
            firstName: validUser.userData.firstName,
            lastName: validUser.userData.lastName,
            roles: validUser.userData.roles,
            permissions: validUser.userData.permissions,
            availability: true,
            rating: validUser.userData.rating,
            timestamp: new Date().toISOString()
        };
    }

    console.log('❌ MockLoginService: Credenciales inválidas');
    return {
        success: false,
        message: "Documento o contraseña incorrectos",
        error: "Credenciales inválidas",
        timestamp: new Date().toISOString()
    };
  }

  // Método para simular peticiones autenticadas
  async fetchWithToken(url, options = {}) {
    const token = sessionStorage.getItem("authToken");
    
    if (!token) {
      throw new Error("No hay token de autenticación");
    }
    
    console.log('🔄 MockLoginService: Simulando petición autenticada a:', url);
    
    // Simular un retraso de red
    await new Promise(resolve => setTimeout(resolve, 300));
    
    // Simular una respuesta exitosa
    return {
      ok: true,
      status: 200,
      json: async () => ({ 
        success: true, 
        message: "Datos obtenidos correctamente",
        data: { message: "Respuesta simulada desde MockLoginService" } 
      })
    };
  }
  
  // Método para obtener usuarios de prueba (útil para mostrar en UI)
  getMockUsers() {
    return this.mockUsers.map(user => ({
      document: user.document,
      // No retornar password por seguridad
      name: `${user.userData.firstName} ${user.userData.lastName}`,
      roles: user.userData.roles.map(r => r.name)
    }));
  }
}

// Hacer disponible globalmente
if (typeof window !== 'undefined') {
    window.MockLoginService = MockLoginService;
    console.log('✅ MockLoginService disponible globalmente');
}