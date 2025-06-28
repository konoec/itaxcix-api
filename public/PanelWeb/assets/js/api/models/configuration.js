/**
 * Modelo Configuration
 * Maneja la estructura y validación de datos de configuración del sistema
 */
class Configuration {
    constructor(data = {}) {
        this.id = data.id || null;
        this.key = data.key || '';
        this.value = data.value || '';
        this.type = data.type || 'string'; // string, number, boolean, json
        this.category = data.category || 'general';
        this.description = data.description || '';
        this.isActive = data.isActive !== undefined ? data.isActive : true;
        this.isEditable = data.isEditable !== undefined ? data.isEditable : true;
        this.createdAt = data.createdAt || new Date();
        this.updatedAt = data.updatedAt || new Date();
        this.createdBy = data.createdBy || null;
        this.updatedBy = data.updatedBy || null;
    }

    /**
     * Valida los datos de configuración
     * @returns {Object} {isValid: boolean, errors: Array}
     */
    validate() {
        const errors = [];

        if (!this.key || this.key.trim() === '') {
            errors.push('La clave de configuración es requerida');
        }

        if (this.key && this.key.length < 3) {
            errors.push('La clave debe tener al menos 3 caracteres');
        }

        if (this.key && !/^[a-zA-Z][a-zA-Z0-9_.-]*$/.test(this.key)) {
            errors.push('La clave debe comenzar con una letra y solo contener letras, números, guiones y puntos');
        }

        if (!this.value && this.value !== 0 && this.value !== false) {
            errors.push('El valor de configuración es requerido');
        }

        const validTypes = ['string', 'number', 'boolean', 'json'];
        if (!validTypes.includes(this.type)) {
            errors.push('El tipo debe ser: string, number, boolean o json');
        }

        if (!this.category || this.category.trim() === '') {
            errors.push('La categoría es requerida');
        }

        // Validar valor según el tipo
        if (this.type === 'number' && isNaN(this.value)) {
            errors.push('El valor debe ser un número válido');
        }

        if (this.type === 'boolean' && typeof this.value !== 'boolean' && this.value !== 'true' && this.value !== 'false') {
            errors.push('El valor debe ser true o false');
        }

        if (this.type === 'json') {
            try {
                JSON.parse(this.value);
            } catch (e) {
                errors.push('El valor debe ser un JSON válido');
            }
        }

        return {
            isValid: errors.length === 0,
            errors
        };
    }

    /**
     * Convierte el valor según su tipo
     * @returns {any} Valor convertido
     */
    getTypedValue() {
        switch (this.type) {
            case 'number':
                return Number(this.value);
            case 'boolean':
                return this.value === true || this.value === 'true';
            case 'json':
                try {
                    return JSON.parse(this.value);
                } catch (e) {
                    return null;
                }
            default:
                return this.value;
        }
    }

    /**
     * Prepara los datos para enviar al servidor
     * @returns {Object} Datos formateados
     */
    toApiFormat() {
        return {
            id: this.id,
            key: this.key.trim(),
            value: this.value,
            type: this.type,
            category: this.category.trim(),
            description: this.description.trim(),
            isActive: this.isActive,
            isEditable: this.isEditable,
            updatedBy: this.updatedBy
        };
    }

    /**
     * Actualiza los datos del modelo
     * @param {Object} data - Nuevos datos
     */
    update(data) {
        Object.keys(data).forEach(key => {
            if (this.hasOwnProperty(key)) {
                this[key] = data[key];
            }
        });
        this.updatedAt = new Date();
    }

    /**
     * Clona el modelo
     * @returns {Configuration} Nueva instancia
     */
    clone() {
        return new Configuration({
            id: this.id,
            key: this.key,
            value: this.value,
            type: this.type,
            category: this.category,
            description: this.description,
            isActive: this.isActive,
            isEditable: this.isEditable,
            createdAt: this.createdAt,
            updatedAt: this.updatedAt,
            createdBy: this.createdBy,
            updatedBy: this.updatedBy
        });
    }

    /**
     * Crea una instancia desde datos de API
     * @param {Object} apiData - Datos de la API
     * @returns {Configuration} Nueva instancia
     */
    static fromApiData(apiData) {
        return new Configuration({
            id: apiData.id,
            key: apiData.key,
            value: apiData.value,
            type: apiData.type || 'string',
            category: apiData.category || 'general',
            description: apiData.description || '',
            isActive: apiData.is_active !== undefined ? apiData.is_active : true,
            isEditable: apiData.is_editable !== undefined ? apiData.is_editable : true,
            createdAt: apiData.created_at ? new Date(apiData.created_at) : new Date(),
            updatedAt: apiData.updated_at ? new Date(apiData.updated_at) : new Date(),
            createdBy: apiData.created_by,
            updatedBy: apiData.updated_by
        });
    }

    /**
     * Obtiene categorías disponibles
     * @returns {Array} Lista de categorías
     */
    static getAvailableCategories() {
        return [
            'general',
            'security',
            'ui',
            'notifications',
            'system',
            'api',
            'database',
            'logging'
        ];
    }

    /**
     * Obtiene tipos disponibles
     * @returns {Array} Lista de tipos
     */
    static getAvailableTypes() {
        return [
            { value: 'string', label: 'Texto' },
            { value: 'number', label: 'Número' },
            { value: 'boolean', label: 'Verdadero/Falso' },
            { value: 'json', label: 'JSON' }
        ];
    }
}