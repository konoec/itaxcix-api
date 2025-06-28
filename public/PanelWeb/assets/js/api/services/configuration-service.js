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

    // ===== MÉTODOS PARA GESTIÓN DE PERMISOS =====

    /**
     * Crea un nuevo permiso
     * @param {Object} permissionData - Datos del permiso
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async createPermission(permissionData) {
        try {
            const token = getAuthToken();
            const response = await fetch(`${API_BASE_URL}/admin/permission/create`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    ...(token ? { 'Authorization': `Bearer ${token}` } : {})
                },
                body: JSON.stringify(permissionData)
            });
            return await response.json();
        } catch (error) {
            return { success: false, message: 'Error de red', error };
        }
    }

    /**
     * Obtiene la lista de permisos con paginación
     * @param {number} page - Número de página (default: 1)
     * @param {number} perPage - Cantidad de elementos por página (default: 10)
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async getPermissions(page = 1, perPage = 10) {
        try {
            const token = getAuthToken();
            const url = new URL(`${API_BASE_URL}/admin/permission/list`);
            url.searchParams.append('page', page.toString());
            url.searchParams.append('perPage', perPage.toString());
            
            console.log('🌐 Haciendo petición a:', url.toString());
            console.log('🔑 Token de autorización:', token ? 'Presente' : 'No disponible');
            
            const response = await fetch(url.toString(), {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    ...(token ? { 'Authorization': `Bearer ${token}` } : {})
                },
            });
            
            console.log('📡 Status de respuesta HTTP:', response.status);
            console.log('📡 Headers de respuesta:', Object.fromEntries(response.headers.entries()));
            
            const responseData = await response.json();
            console.log('📋 Datos de respuesta parseados:', responseData);
            
            // Verificar si la respuesta HTTP fue exitosa
            if (!response.ok) {
                console.error('❌ Error HTTP:', response.status, response.statusText);
                return { 
                    success: false, 
                    message: `Error HTTP ${response.status}: ${response.statusText}`,
                    error: responseData 
                };
            }
            
            return responseData;
        } catch (error) {
            console.error('❌ Error de red en getPermissions:', error);
            return { success: false, message: 'Error de red', error };
        }
    }

    /**
     * Actualiza un permiso existente
     * @param {number} permissionId - ID del permiso
     * @param {Object} permissionData - Datos del permiso
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async updatePermission(permissionId, permissionData) {
        try {
            const token = getAuthToken();
            const response = await fetch(`${API_BASE_URL}/admin/permission/update/${permissionId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    ...(token ? { 'Authorization': `Bearer ${token}` } : {})
                },
                body: JSON.stringify(permissionData)
            });
            return await response.json();
        } catch (error) {
            return { success: false, message: 'Error de red', error };
        }
    }

    /**
     * Elimina un permiso
     * @param {number} permissionId - ID del permiso
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async deletePermission(permissionId) {
        try {
            const token = getAuthToken();
            const response = await fetch(`${API_BASE_URL}/admin/permission/delete/${permissionId}`, {
                method: 'DELETE',
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

    // ===== MÉTODOS PARA GESTIÓN DE ROLES =====

    /**
     * Crea un nuevo rol
     * @param {Object} roleData - Datos del rol
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async createRole(roleData) {
        try {
            const token = getAuthToken();
            const response = await fetch(`${API_BASE_URL}/admin/role/create`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    ...(token ? { 'Authorization': `Bearer ${token}` } : {})
                },
                body: JSON.stringify(roleData)
            });
            return await response.json();
        } catch (error) {
            return { success: false, message: 'Error de red', error };
        }
    }

    /**
     * Obtiene la lista de roles
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async getRoles() {
        try {
            const token = getAuthToken();
            const response = await fetch(`${API_BASE_URL}/admin/role/list`, {
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

    /**
     * Actualiza un rol existente
     * @param {number} roleId - ID del rol
     * @param {Object} roleData - Datos del rol
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async updateRole(roleId, roleData) {
        try {
            const token = getAuthToken();
            const response = await fetch(`${API_BASE_URL}/admin/role/update/${roleId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    ...(token ? { 'Authorization': `Bearer ${token}` } : {})
                },
                body: JSON.stringify(roleData)
            });
            return await response.json();
        } catch (error) {
            return { success: false, message: 'Error de red', error };
        }
    }

    /**
     * Elimina un rol
     * @param {number} roleId - ID del rol
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async deleteRole(roleId) {
        try {
            const token = getAuthToken();
            const response = await fetch(`${API_BASE_URL}/admin/role/delete/${roleId}`, {
                method: 'DELETE',
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
}

// Para compatibilidad con proyectos sin módulos ES6
typeof window !== 'undefined' && (window.ConfigurationService = ConfigurationService);
// Mantener compatibilidad con el nombre anterior temporalmente
typeof window !== 'undefined' && (window.ConfiguracionService = ConfigurationService);
typeof window !== 'undefined' && (window.EmergencyConfigService = ConfigurationService);

// Log para verificar que el servicio se cargue correctamente
console.log('✅ ConfigurationService cargado y disponible globalmente');
