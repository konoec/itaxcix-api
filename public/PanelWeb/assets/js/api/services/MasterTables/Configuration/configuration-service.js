/**
 * Configuration Service
 * Servicio para gestionar configuraciones del sistema
 * 
 * @author Sistema
 * @version 1.0.0
 */

class ConfigurationService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.endpoint = '/admin/configurations';
        this.cache = new Map();
        this.cacheTimeout = 5 * 60 * 1000; // 5 minutos
        
        console.log('⚙️ ConfigurationService inicializado con base URL:', this.baseUrl);
    }

    /**
     * Obtiene lista paginada de configuraciones
     * @param {Object} options - Opciones de búsqueda y filtros
     * @param {number} options.page - Número de página
     * @param {number} options.perPage - Elementos por página
     * @param {string} options.search - Búsqueda global
     * @param {string} options.key - Filtro por clave
     * @param {string} options.value - Filtro por valor
     * @param {boolean} options.active - Filtro por estado activo
     * @param {string} options.sortBy - Campo de ordenamiento
     * @param {string} options.sortDirection - Dirección de ordenamiento
     * @param {boolean} options.onlyActive - Solo activos
     * @returns {Promise<Object>} Lista paginada de configuraciones
     */
    async getConfigurations(options = {}) {
        try {
            const params = new URLSearchParams();
            
            // Parámetros de paginación
            if (options.page) params.append('page', options.page);
            if (options.perPage) params.append('perPage', options.perPage);
            
            // Parámetros de búsqueda y filtros
            if (options.search) params.append('search', options.search);
            if (options.key) params.append('key', options.key);
            if (options.value) params.append('value', options.value);
            if (options.active !== undefined) params.append('active', options.active);
            
            // Parámetros de ordenamiento
            if (options.sortBy) params.append('sortBy', options.sortBy);
            if (options.sortDirection) params.append('sortDirection', options.sortDirection);
            if (options.onlyActive !== undefined) params.append('onlyActive', options.onlyActive);

            const cacheKey = `configurations_${params.toString()}`;
            
            // Verificar cache
            if (this.cache.has(cacheKey)) {
                const cached = this.cache.get(cacheKey);
                if (Date.now() - cached.timestamp < this.cacheTimeout) {
                    return cached.data;
                }
            }

            const url = `${this.baseUrl}${this.endpoint}?${params.toString()}`;
            
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': this.getAuthHeader()
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            
            // Guardar en cache
            this.cache.set(cacheKey, {
                data: data,
                timestamp: Date.now()
            });

            return data;
        } catch (error) {
            console.error('Error fetching configurations:', error);
            throw error;
        }
    }

    /**
     * Obtiene configuración por ID
     * @param {number} id - ID de la configuración
     * @returns {Promise<Object>} Configuración encontrada
     */
    async getConfigurationById(id) {
        try {
            const response = await fetch(`${this.baseUrl}${this.endpoint}/${id}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': this.getAuthHeader()
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('Error fetching configuration by ID:', error);
            throw error;
        }
    }

    /**
     * Crea nueva configuración
     * @param {Object} configData - Datos de la configuración
     * @param {string} configData.key - Clave de la configuración
     * @param {string} configData.value - Valor de la configuración
     * @param {boolean} configData.active - Estado activo/inactivo
     * @returns {Promise<Object>} Configuración creada
     */
    async createConfiguration(configData) {
        try {
            // Preparar datos sin ID (se genera en el servidor)
            const requestData = {
                key: configData.key,
                value: configData.value,
                active: configData.active
            };

            const response = await fetch(`${this.baseUrl}${this.endpoint}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': this.getAuthHeader()
                },
                body: JSON.stringify(requestData)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            // Limpiar cache
            this.clearCache();

            return await response.json();
        } catch (error) {
            console.error('Error creating configuration:', error);
            throw error;
        }
    }

    /**
     * Actualiza configuración existente
     * @param {number} id - ID de la configuración
     * @param {Object} configData - Datos actualizados
     * @returns {Promise<Object>} Configuración actualizada
     */
    async updateConfiguration(id, configData) {
        try {
            const response = await fetch(`${this.baseUrl}${this.endpoint}/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': this.getAuthHeader()
                },
                body: JSON.stringify(configData)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            // Limpiar cache
            this.clearCache();

            return await response.json();
        } catch (error) {
            console.error('Error updating configuration:', error);
            throw error;
        }
    }

    /**
     * Elimina configuración
     * @param {number} id - ID de la configuración
     * @returns {Promise<Object>} Resultado de la eliminación
     */
    async deleteConfiguration(id) {
        try {
            const response = await fetch(`${this.baseUrl}${this.endpoint}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': this.getAuthHeader()
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            // Limpiar cache
            this.clearCache();

            return await response.json();
        } catch (error) {
            console.error('Error deleting configuration:', error);
            throw error;
        }
    }

    /**
     * Actualiza estado activo de una configuración
     * @param {number} id - ID de la configuración
     * @param {boolean} active - Nuevo estado
     * @returns {Promise<Object>} Configuración actualizada
     */
    async toggleConfigurationStatus(id, active) {
        try {
            const response = await fetch(`${this.baseUrl}${this.endpoint}/${id}/toggle`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': this.getAuthHeader()
                },
                body: JSON.stringify({ active })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            // Limpiar cache
            this.clearCache();

            return await response.json();
        } catch (error) {
            console.error('Error toggling configuration status:', error);
            throw error;
        }
    }

    /**
     * Obtiene estadísticas de configuraciones
     * @returns {Promise<Object>} Estadísticas del sistema
     */
    async getConfigurationStats() {
        try {
            const response = await fetch(`${this.baseUrl}${this.endpoint}/stats`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': this.getAuthHeader()
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('Error fetching configuration stats:', error);
            throw error;
        }
    }

    /**
     * Limpia el cache del servicio
     */
    clearCache() {
        this.cache.clear();
    }

    /**
     * Valida datos de configuración para creación
     * @param {Object} configData - Datos a validar
     * @param {string} configData.key - Clave de la configuración
     * @param {string} configData.value - Valor de la configuración
     * @param {boolean} configData.active - Estado activo/inactivo
     * @returns {Object} Resultado de la validación
     */
    validateConfigurationData(configData) {
        const errors = {};

        // Validar clave requerida
        if (!configData.key || configData.key.trim() === '') {
            errors.key = 'La clave es requerida';
        } else if (configData.key.length < 3) {
            errors.key = 'La clave debe tener al menos 3 caracteres';
        }

        // Validar valor requerido
        if (configData.value === undefined || configData.value === null || configData.value.toString().trim() === '') {
            errors.value = 'El valor es requerido';
        }

        // Validar estado activo (debe ser boolean)
        if (typeof configData.active !== 'boolean') {
            errors.active = 'El estado debe ser verdadero o falso';
        }

        return {
            isValid: Object.keys(errors).length === 0,
            errors
        };
    }

    /**
     * Formatea datos de configuración para mostrar
     * @param {Object} config - Configuración a formatear
     * @returns {Object} Configuración formateada
     */
    formatConfigurationData(config) {
        return {
            ...config,
            statusText: config.active ? 'Activo' : 'Inactivo',
            statusBadge: config.active ? 'success' : 'danger',
            formattedValue: this.formatConfigValue(config.value),
            displayKey: config.key.replace(/[._]/g, ' ').toUpperCase()
        };
    }

    /**
     * Formatea valor de configuración según su tipo
     * @param {string} value - Valor a formatear
     * @returns {string} Valor formateado
     */
    formatConfigValue(value) {
        if (value === 'true' || value === 'false') {
            return value === 'true' ? 'Sí' : 'No';
        }
        
        if (!isNaN(value) && value !== '') {
            return parseInt(value).toLocaleString();
        }
        
        return value;
    }

    /**
     * Obtiene el header de autorización
     * @returns {string} Header de autorización
     */
    getAuthHeader() {
        const token = localStorage.getItem('authToken') || sessionStorage.getItem('authToken');
        return token ? `Bearer ${token}` : '';
    }
}

// Exportar instancia del servicio
window.ConfigurationService = ConfigurationService;
