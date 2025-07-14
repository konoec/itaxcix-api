// Servicio para la configuración de emergencia
const API_BASE_URL = 'https://149.130.161.148/api/v1';

function getAuthToken() {
    // Compatibilidad: busca 'token' o 'authToken'
    return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
}

class EmergencyNumberService {
    static async getEmergencyNumber() {
        try {
            console.log('🔄 EmergencyNumberService: Obteniendo número de emergencia...');
            const token = getAuthToken();
            console.log('🔑 Token disponible:', token ? 'Sí' : 'No');
            
            const response = await fetch(`${API_BASE_URL}/emergency/number`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    ...(token ? { 'Authorization': `Bearer ${token}` } : {})
                },
            });
            
            console.log('📡 Respuesta HTTP status:', response.status);
            const result = await response.json();
            console.log('📋 Respuesta parseada:', result);
            
            return result;
        } catch (error) {
            console.error('❌ Error en EmergencyNumberService.getEmergencyNumber:', error);
            return { success: false, message: 'Error de red', error };
        }
    }

    static async updateEmergencyNumber(number) {
        try {
            console.log('💾 EmergencyNumberService: Actualizando número de emergencia a:', number);
            const token = getAuthToken();
            console.log('🔑 Token disponible:', token ? 'Sí' : 'No');
            
            const response = await fetch(`${API_BASE_URL}/emergency/number`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    ...(token ? { 'Authorization': `Bearer ${token}` } : {})
                },
                body: JSON.stringify({ number })
            });
            
            console.log('📡 Respuesta HTTP status:', response.status);
            const result = await response.json();
            console.log('📋 Respuesta parseada:', result);
            
            return result;
        } catch (error) {
            console.error('❌ Error en EmergencyNumberService.updateEmergencyNumber:', error);
            return { success: false, message: 'Error de red', error };
        }
    }
}

// Para compatibilidad global
if (typeof window !== 'undefined') {
    window.EmergencyNumberService = EmergencyNumberService;
    // Mantener compatibilidad con el nombre anterior
    window.EmergencyService = EmergencyNumberService;
}

console.log('✅ EmergencyNumberService cargado y disponible globalmente');
