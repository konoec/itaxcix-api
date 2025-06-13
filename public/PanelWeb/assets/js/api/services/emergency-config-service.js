// Servicio para la configuración del número de emergencia
const API_BASE_URL = 'https://149.130.161.148/api/v1';

function getAuthToken() {
    // Compatibilidad: busca 'token' o 'authToken'
    return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
}

class EmergencyConfigService {
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
typeof window !== 'undefined' && (window.EmergencyConfigService = EmergencyConfigService);
