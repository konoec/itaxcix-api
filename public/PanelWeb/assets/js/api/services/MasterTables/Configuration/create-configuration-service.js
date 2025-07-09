/**
 * Create Configuration Service
 * Servicio especializado para la creación de configuraciones del sistema
 * 
 * @author Sistema
 * @version 1.0.0
 */

class CreateConfigurationService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.endpoint = '/admin/configurations';
        
        console.log('➕ CreateConfigurationService inicializado');
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
            // Validar datos antes de enviar
            const validation = this.validateConfigurationData(configData);
            if (!validation.isValid) {
                throw new Error(`Datos inválidos: ${Object.values(validation.errors).join(', ')}`);
            }

            // Preparar datos sin ID (se genera en el servidor)
            const requestData = {
                key: configData.key.trim(),
                value: configData.value.toString().trim(),
                active: Boolean(configData.active)
            };

            console.log('🚀 Enviando datos para crear configuración:', requestData);

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
                const errorData = await response.json().catch(() => ({}));
                throw new Error(`Error HTTP ${response.status}: ${errorData.message || 'Error desconocido'}`);
            }

            const result = await response.json();
            console.log('✅ Configuración creada exitosamente:', result);

            return result;
        } catch (error) {
            console.error('❌ Error creando configuración:', error);
            throw error;
        }
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

        // Validar que configData exista
        if (!configData || typeof configData !== 'object') {
            errors.general = 'Los datos de configuración son requeridos';
            return { isValid: false, errors };
        }

        // Validar clave requerida
        if (!configData.key || typeof configData.key !== 'string' || configData.key.trim() === '') {
            errors.key = 'La clave es requerida';
        } else if (configData.key.trim().length < 3) {
            errors.key = 'La clave debe tener al menos 3 caracteres';
        } else if (configData.key.trim().length > 100) {
            errors.key = 'La clave no puede exceder 100 caracteres';
        } else if (!/^[a-zA-Z0-9._-]+$/.test(configData.key.trim())) {
            errors.key = 'La clave solo puede contener letras, números, puntos, guiones y guiones bajos';
        }

        // Validar valor requerido
        if (configData.value === undefined || configData.value === null) {
            errors.value = 'El valor es requerido';
        } else if (typeof configData.value === 'string' && configData.value.trim() === '') {
            errors.value = 'El valor no puede estar vacío';
        } else if (typeof configData.value === 'string' && configData.value.trim().length > 1000) {
            errors.value = 'El valor no puede exceder 1000 caracteres';
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
     * Prepara datos para envío limpiando y normalizando
     * @param {Object} configData - Datos originales
     * @returns {Object} Datos preparados
     */
    prepareConfigurationData(configData) {
        return {
            key: configData.key.trim(),
            value: configData.value.toString().trim(),
            active: Boolean(configData.active)
        };
    }

    /**
     * Verifica disponibilidad de clave
     * @param {string} key - Clave a verificar
     * @returns {Promise<boolean>} true si está disponible
     */
    async checkKeyAvailability(key) {
        try {
            const response = await fetch(`${this.baseUrl}${this.endpoint}/check-key`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'Authorization': this.getAuthHeader()
                },
                body: JSON.stringify({ key: key.trim() })
            });

            if (!response.ok) {
                console.warn('No se pudo verificar disponibilidad de clave');
                return true; // Asumir disponible si no se puede verificar
            }

            const result = await response.json();
            return result.available;
        } catch (error) {
            console.warn('Error verificando disponibilidad de clave:', error);
            return true; // Asumir disponible si hay error
        }
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
window.CreateConfigurationService = CreateConfigurationService;
