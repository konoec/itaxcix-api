// Servicio para la configuración general del sistema
const API_BASE_URL = 'https://149.130.161.148/api/v1';

function getAuthToken() {
    // Compatibilidad: busca 'token' o 'authToken'
    return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
}

class ConfigurationService {
    static async getEmergencyNumber() {
        try {
            const token = getAuthToken();
            const response = await fetch(`${API_BASE_URL}/emergency/number`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    ...(token ? { 'Authorization': `Bearer ${token}` } : {})
                },
            });
            return await response.json();
        } catch (error) {
            return { success: false, message: 'Error de red', error };
        }
    }

    static async updateEmergencyNumber(number) {
        try {
            const token = getAuthToken();
            const response = await fetch(`${API_BASE_URL}/emergency/number`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    ...(token ? { 'Authorization': `Bearer ${token}` } : {})
                },
                body: JSON.stringify({ number })
            });
            return await response.json();
        } catch (error) {
            return { success: false, message: 'Error de red', error };
        }
    }
}

// Para compatibilidad con proyectos sin módulos ES6
typeof window !== 'undefined' && (window.ConfigurationService = ConfigurationService);
// Mantener compatibilidad con el nombre anterior temporalmente
typeof window !== 'undefined' && (window.ConfiguracionService = ConfigurationService);
typeof window !== 'undefined' && (window.EmergencyConfigService = ConfigurationService);
