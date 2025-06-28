/**
 * Modelo Permission
 * Maneja la estructura y validación de permisos del sistema
 * Actualizado para coincidir con la API del sistema
 */
class Permission {
    constructor(data = {}) {
        this.id = data.id || null;
        this.name = data.name || '';
        this.active = data.active !== undefined ? data.active : true;
        this.web = data.web !== undefined ? data.web : true;
        this.createdAt = data.createdAt || new Date();
        this.updatedAt = data.updatedAt || new Date();
    }

    /**
     * Valida los datos del permiso según la API
     * @returns {Object} {isValid: boolean, errors: Array}
     */
    validate() {
        const errors = [];

        if (!this.name || this.name.trim() === '') {
            errors.push('El nombre del permiso es requerido');
        }

        if (this.name && this.name.length < 3) {
            errors.push('El nombre debe tener al menos 3 caracteres');
        }

        if (this.name && this.name.length > 100) {
            errors.push('El nombre no puede exceder 100 caracteres');
        }

        return {
            isValid: errors.length === 0,
            errors
        };
    }

    /**
     * Prepara los datos para enviar a la API
     * @returns {Object} Datos formateados para la API
     */
    toApiFormat() {
        return {
            name: this.name.trim(),
            active: this.active,
            web: this.web
        };
    }

    /**
     * Prepara los datos para actualizar en la API (incluye ID)
     * @returns {Object} Datos formateados para actualización
     */
    toUpdateApiFormat() {
        return {
            id: this.id,
            name: this.name.trim(),
            active: this.active,
            web: this.web
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
     * Obtiene el nombre del permiso para mostrar
     * @returns {string} Nombre del permiso
     */
    getDisplayName() {
        return this.name || 'Sin nombre';
    }

    /**
     * Verifica si el permiso está activo
     * @returns {boolean} True si está activo
     */
    isActive() {
        return this.active === true;
    }

    /**
     * Verifica si el permiso es para web
     * @returns {boolean} True si es para web
     */
    isWebPermission() {
        return this.web === true;
    }

    /**
     * Clona el modelo
     * @returns {Permission} Nueva instancia
     */
    clone() {
        return new Permission({
            id: this.id,
            name: this.name,
            active: this.active,
            web: this.web,
            createdAt: this.createdAt,
            updatedAt: this.updatedAt
        });
    }

    /**
     * Crea una instancia desde datos de API
     * @param {Object} apiData - Datos de la API
     * @returns {Permission} Nueva instancia
     */
    static fromApiData(apiData) {
        return new Permission({
            id: apiData.id,
            name: apiData.name,
            active: apiData.active !== undefined ? apiData.active : true,
            web: apiData.web !== undefined ? apiData.web : true,
            createdAt: apiData.created_at ? new Date(apiData.created_at) : new Date(),
            updatedAt: apiData.updated_at ? new Date(apiData.updated_at) : new Date()
        });
    }

    /**
     * Obtiene los estados disponibles
     * @returns {Array} Lista de estados
     */
    static getAvailableStatuses() {
        return [
            { value: true, label: 'Activo', color: '#28a745' },
            { value: false, label: 'Inactivo', color: '#dc3545' }
        ];
    }

    /**
     * Obtiene los tipos de plataforma disponibles
     * @returns {Array} Lista de tipos de plataforma
     */
    static getAvailablePlatforms() {
        return [
            { value: true, label: 'Web', color: '#007bff' },
            { value: false, label: 'Móvil', color: '#28a745' }
        ];
    }
}

// Exportar para uso global (compatibilidad con el resto del sistema)
window.Permission = Permission;
