// Servicio para crear un nuevo distrito
class CreateDistrictService {
    static get API_BASE_URL() {
        return 'https://149.130.161.148/api/v1';
    }

    static getAuthToken() {
        return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
    }

    /**
     * Crea un nuevo distrito
     * @param {Object} data - { name, provinceId, ubigeo }
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async createDistrict(data) {
        try {
            console.log('🚀 CreateDistrictService: Iniciando creación de distrito');
            console.log('📊 Datos recibidos:', data);
            
            const token = this.getAuthToken();
            if (!token) {
                throw new Error('Token de autenticación no encontrado');
            }
            
            console.log('🔑 Token obtenido:', token ? 'Sí' : 'No');
            
            // Validar datos antes de enviar
            if (!data.name || data.name.trim().length < 2) {
                throw new Error('El nombre del distrito debe tener al menos 2 caracteres');
            }
            if (!data.provinceId || isNaN(parseInt(data.provinceId))) {
                throw new Error('ID de provincia inválido');
            }
            if (!data.ubigeo || !/^[0-9]{6}$/.test(data.ubigeo)) {
                throw new Error('El código UBIGEO debe tener exactamente 6 dígitos');
            }
            
            // Preparar datos para envío
            const requestData = {
                name: data.name.trim(),
                provinceId: parseInt(data.provinceId),
                ubigeo: data.ubigeo.trim()
            };
            
            console.log('📤 Datos preparados para envío:', requestData);
            console.log('🌐 URL:', `${this.API_BASE_URL}/admin/districts`);
            
            const response = await fetch(`${this.API_BASE_URL}/admin/districts`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(requestData)
            });

            console.log('📥 Response status:', response.status);
            console.log('📥 Response headers:', Object.fromEntries(response.headers.entries()));
            
            // Verificar si la respuesta es JSON
            const contentType = response.headers.get('content-type');
            let responseData;
            
            if (contentType && contentType.includes('application/json')) {
                responseData = await response.json();
            } else {
                const textResponse = await response.text();
                console.error('❌ Respuesta no-JSON:', textResponse);
                throw new Error(`Error del servidor (${response.status}): Respuesta inválida`);
            }
            
            console.log('📥 Response data:', responseData);

            if (!response.ok) {
                // Manejar errores específicos según el código de estado
                let errorMessage = responseData.message || 'Error desconocido';
                
                switch (response.status) {
                    case 400:
                        errorMessage = responseData.message || 'Datos inválidos. Verifique la información ingresada.';
                        break;
                    case 401:
                        errorMessage = 'Sesión expirada. Inicie sesión nuevamente.';
                        break;
                    case 409:
                        errorMessage = 'El distrito ya existe con el mismo nombre o UBIGEO.';
                        break;
                    case 422:
                        errorMessage = 'Datos de validación incorrectos. Verifique los campos.';
                        break;
                    case 500:
                        errorMessage = 'Error interno del servidor. Intente nuevamente.';
                        break;
                }
                
                console.error('❌ Error HTTP:', response.status, errorMessage);
                throw new Error(errorMessage);
            }

            console.log('✅ Distrito creado exitosamente');
            return responseData;
            
        } catch (error) {
            console.error('❌ Error en CreateDistrictService:', error);
            console.error('❌ Stack trace:', error.stack);
            throw error;
        }
    }
}
window.CreateDistrictService = CreateDistrictService;
