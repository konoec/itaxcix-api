/**
 * Servicio para gestionar elementos del centro de ayuda
 * Maneja operaciones CRUD para elementos del centro de ayuda
 */
class HelpCenterService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
    }

    /**
     * Obtiene el token de autenticación del sessionStorage
     */
    getAuthToken() {
        return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
    }

    /**
     * Obtiene elementos del centro de ayuda con paginación
     * @param {number} page - Número de página (mínimo 1)
     * @param {number} perPage - Elementos por página (máximo 100, mínimo 1)
     * @returns {Promise<Object>} Respuesta con elementos paginados
     */
    async getHelpCenterItems(page = 1, perPage = 10) {
        try {
            // Validaciones de parámetros
            if (page < 1) page = 1;
            if (perPage < 1) perPage = 10;
            if (perPage > 100) perPage = 100;

            const params = new URLSearchParams({
                page: page.toString(),
                perPage: perPage.toString()
            });

            const token = this.getAuthToken();

            const response = await fetch(`${this.baseUrl}/help-center?${params}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    ...(token ? { 'Authorization': `Bearer ${token}` } : {})
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Error al obtener elementos del centro de ayuda');
            }

            // Debug temporal: ver estructura de datos
            console.log('🔍 Datos recibidos del API:', data);
            console.log('🔍 Items:', data.data?.items);

            // Retornar la estructura completa para que el controlador pueda acceder a data.items y data.meta
            return data;
        } catch (error) {
            console.error('Error en getHelpCenterItems:', error);
            throw error;
        }
    }

    /**
     * Busca elementos del centro de ayuda por término de búsqueda
     * @param {string} searchTerm - Término de búsqueda
     * @param {number} page - Número de página
     * @param {number} perPage - Elementos por página
     * @returns {Promise<Object>} Respuesta con elementos filtrados
     */
    async searchHelpCenterItems(searchTerm, page = 1, perPage = 10) {
        try {
            if (page < 1) page = 1;
            if (perPage < 1) perPage = 10;
            if (perPage > 100) perPage = 100;

            const params = new URLSearchParams({
                page: page.toString(),
                perPage: perPage.toString()
            });

            if (searchTerm && searchTerm.trim()) {
                params.append('search', searchTerm.trim());
            }

            const token = this.getAuthToken();

            const response = await fetch(`${this.baseUrl}/help-center?${params}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    ...(token ? { 'Authorization': `Bearer ${token}` } : {})
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Error al buscar elementos del centro de ayuda');
            }

            return data;
        } catch (error) {
            console.error('Error en searchHelpCenterItems:', error);
            throw error;
        }
    }

    /**
     * Obtiene un elemento específico del centro de ayuda
     * @param {number} id - ID del elemento
     * @returns {Promise<Object>} Respuesta con el elemento
     */
    async getHelpCenterItem(id) {
        try {
            if (!id || id <= 0) {
                throw new Error('ID del elemento es requerido');
            }

            const token = this.getAuthToken();

            const response = await fetch(`${this.baseUrl}/help-center/${id}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    ...(token ? { 'Authorization': `Bearer ${token}` } : {})
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Error al obtener elemento del centro de ayuda');
            }

            return data;
        } catch (error) {
            console.error('Error en getHelpCenterItem:', error);
            throw error;
        }
    }

    /**
     * Crea un nuevo elemento del centro de ayuda
     * @param {Object} itemData - Datos del elemento
     * @returns {Promise<Object>} Respuesta con el elemento creado
     */
    async createHelpCenterItem(itemData) {
        try {
            if (!itemData || !itemData.title || !itemData.subtitle || !itemData.answer) {
                throw new Error('Datos del elemento son requeridos (title, subtitle, answer)');
            }

            const token = this.getAuthToken();

            const response = await fetch(`${this.baseUrl}/help-center`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    ...(token ? { 'Authorization': `Bearer ${token}` } : {})
                },
                body: JSON.stringify(itemData)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Error al crear elemento del centro de ayuda');
            }

            return data;
        } catch (error) {
            console.error('Error en createHelpCenterItem:', error);
            throw error;
        }
    }

    /**
     * Actualiza un elemento del centro de ayuda
     * @param {number} id - ID del elemento a actualizar
     * @param {Object} itemData - Datos del elemento
     * @param {string} itemData.title - Título del elemento
     * @param {string} itemData.subtitle - Subtítulo del elemento
     * @param {string} itemData.answer - Respuesta del elemento
     * @param {boolean} itemData.active - Estado activo del elemento
     * @returns {Promise<Object>} Respuesta del servidor
     */
    async updateHelpCenterItem(id, itemData) {
        try {
            // Validar parámetros requeridos
            if (!id || typeof id !== 'number') {
                throw new Error('ID del elemento es requerido y debe ser un número');
            }

            if (!itemData || typeof itemData !== 'object') {
                throw new Error('Datos del elemento son requeridos');
            }

            const { title, subtitle, answer, active } = itemData;

            // Validar campos requeridos
            if (!title || typeof title !== 'string' || title.trim() === '') {
                throw new Error('El título es requerido');
            }

            if (!subtitle || typeof subtitle !== 'string' || subtitle.trim() === '') {
                throw new Error('El subtítulo es requerido');
            }

            if (!answer || typeof answer !== 'string' || answer.trim() === '') {
                throw new Error('La respuesta es requerida');
            }

            if (typeof active !== 'boolean') {
                throw new Error('El estado activo debe ser verdadero o falso');
            }

            const token = this.getAuthToken();
            if (!token) {
                throw new Error('Token de autenticación requerido');
            }

            const requestBody = {
                id: id,
                title: title.trim(),
                subtitle: subtitle.trim(),
                answer: answer.trim(),
                active: active
            };

            console.log('🔄 Actualizando elemento del centro de ayuda:', requestBody);

            const response = await fetch(`${this.baseUrl}/help-center/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(requestBody)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Error al actualizar elemento del centro de ayuda');
            }

            console.log('✅ Elemento del centro de ayuda actualizado correctamente');
            return data;

        } catch (error) {
            console.error('❌ Error en updateHelpCenterItem:', error);
            throw error;
        }
    }

    /**
     * Elimina un elemento del centro de ayuda
     * @param {number} id - ID del elemento
     * @returns {Promise<Object>} Respuesta de confirmación
     */
    async deleteHelpCenterItem(id) {
        try {
            if (!id || id <= 0) {
                throw new Error('ID del elemento es requerido');
            }

            const token = this.getAuthToken();

            const response = await fetch(`${this.baseUrl}/help-center/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    ...(token ? { 'Authorization': `Bearer ${token}` } : {})
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Error al eliminar elemento del centro de ayuda');
            }

            return data;
        } catch (error) {
            console.error('Error en deleteHelpCenterItem:', error);
            throw error;
        }
    }

    /**
     * Cambia el estado activo de un elemento del centro de ayuda
     * @param {number} id - ID del elemento
     * @param {boolean} active - Nuevo estado activo
     * @returns {Promise<Object>} Respuesta con el elemento actualizado
     */
    async toggleHelpCenterItemStatus(id, active) {
        try {
            if (!id || id <= 0) {
                throw new Error('ID del elemento es requerido');
            }

            const token = this.getAuthToken();

            const response = await fetch(`${this.baseUrl}/help-center/${id}/toggle-status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    ...(token ? { 'Authorization': `Bearer ${token}` } : {})
                },
                body: JSON.stringify({ active })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.message || 'Error al cambiar estado del elemento');
            }

            return data;
        } catch (error) {
            console.error('Error en toggleHelpCenterItemStatus:', error);
            throw error;
        }
    }
}

// Exportar como instancia singleton
window.HelpCenterService = new HelpCenterService();
