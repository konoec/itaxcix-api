// Servicio para gestionar conductores (conectándose a endpoints)
class ConductorService {
    constructor() {
        this.apiUrl = 'https://149.130.161.148/api/v1'; // ← Sin barra final
        console.log('API URL configurada:', this.apiUrl);
    }

    /**
     * Obtiene los detalles de un conductor pendiente por su ID desde la API real.
     * @param {number} id - ID del conductor pendiente
     * @returns {Promise<Object>} - Detalles del conductor
     */
    async obtenerConductorPendientePorId(id) {
        try {
            const url = `${this.apiUrl}/drivers/pending/${id}`;
            const token = sessionStorage.getItem('authToken');
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': token ? 'Bearer ' + token : ''
                }
            });

            if (!response.ok) {
                const errorText = await response.text();
                console.error('Respuesta de error:', errorText);
                throw new Error(`Error HTTP: ${response.status}`);
            }

            const responseData = await response.json();
            console.log('Respuesta detalle conductor:', responseData); // Para depuración
            
            if (responseData.success === false) {
                throw new Error(responseData.message || 'Error al obtener conductor');
            }
            
            // Extraer los datos del conductor del objeto data
            if (responseData.data) {
                return responseData.data;
            }
            
            return null;
        } catch (error) {
            console.error('Error al obtener detalles del conductor:', error);
            throw error;
        }
    }

    /**
     * Obtiene la lista de todos los conductores pendientes desde la API real con paginación.
     * @param {number} page - Número de página (empezando en 0)
     * @param {number} perPage - Cantidad de elementos por página
     * @returns {Promise<Array>} - Array de conductores pendientes
     */    async obtenerConductoresPendientes(page = 0, perPage = 8) {
        try {
            console.log(`🔍 Solicitando conductores: page=${page}, perPage=${perPage}`);
            const url = `${this.apiUrl}/drivers/pending?page=${page}&perPage=${perPage}`;
            console.log(`📡 URL completa: ${url}`);
            const token = sessionStorage.getItem('authToken');
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': token ? 'Bearer ' + token : ''
                }
            });

            if (!response.ok) {
                const errorText = await response.text();
                console.error('Respuesta de error:', errorText);
                throw new Error(`Error HTTP: ${response.status}`);
            }

            const responseData = await response.json();
            console.log('Respuesta de la API conductores:', responseData); // Para depuración
            
            // Verificar si la respuesta es exitosa
            if (responseData.success === false) {
                throw new Error(responseData.message || 'No se encontraron conductores pendientes');
            }
              // Extraer el array de conductores del objeto data.items
            if (responseData.data && responseData.data.items && Array.isArray(responseData.data.items)) {
                console.log(`✅ Conductores recibidos: ${responseData.data.items.length} de ${perPage} solicitados`);
                return responseData.data.items;
            }
            
            return [];
        } catch (error) {
            console.error('Error al obtener conductores pendientes:', error);
            throw error;
        }
    }

    /**
     * Aprueba un conductor pendiente por su ID.
     * @param {number} driverId - ID del conductor a aprobar
     * @returns {Promise<Object>} - Respuesta de la API
     */
    async aprobarConductor(driverId) {
    try {
        const url = `${this.apiUrl}/drivers/approve`;
        const token = sessionStorage.getItem('authToken');

        const requestBody = { driverId: parseInt(driverId) };

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': token ? 'Bearer ' + token : ''
            },
            body: JSON.stringify(requestBody)
        });

        const responseData = await response.json();

        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status} - ${responseData.message || 'Error desconocido'}`);
        }

        return responseData;
    } catch (error) {
        console.error('❌ Error al aprobar conductor:', error);
        throw error;
    }
}



    /**
     * Rechaza un conductor pendiente por su ID.
     * @param {number} driverId - ID del conductor a rechazar
     * @returns {Promise<Object>} - Respuesta de la API
     */
   async rechazarConductor(driverId) {
    try {
        const url = `${this.apiUrl}/drivers/reject`;
        const token = sessionStorage.getItem('authToken');

        const requestBody = { driverId: parseInt(driverId) };

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'Authorization': token ? 'Bearer ' + token : ''
            },
            body: JSON.stringify(requestBody)
        });

        const responseData = await response.json();

        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status} - ${responseData.message || 'Error desconocido'}`);
        }

        return responseData;
    } catch (error) {
        console.error('❌ Error al rechazar conductor:', error);
        throw error;
    }
  }

}

// Exportar la clase para que esté disponible en otros archivos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ConductorService;
} else {
    // Para navegadores sin soporte de módulos
    window.ConductorService = ConductorService;
}
