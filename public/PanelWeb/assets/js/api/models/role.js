/**
 * Modelo Role
 * Maneja la estructura y validación de roles del sistema
 */
class Role {
    constructor(data = {}) {
        this.id = data.id || null;
        this.name = data.name || '';
        this.code = data.code || '';
        this.description = data.description || '';
        this.level = data.level || 1; // Nivel jerárquico del rol
        this.color = data.color || '#6c757d'; // Color para la UI
        this.permissions = data.permissions || []; // Array de permisos
        this.isActive = data.isActive !== undefined ? data.isActive : true;
        this.isSystem = data.isSystem !== undefined ? data.isSystem : false; // Rol del sistema (no editable)
        this.createdAt = data.createdAt || new Date();
        this.updatedAt = data.updatedAt || new Date();
        this.createdBy = data.createdBy || null;
        this.updatedBy = data.updatedBy || null;
    }

    /**
     * Valida los datos del rol
     * @returns {Object} {isValid: boolean, errors: Array}
     */
    validate() {
        const errors = [];

        if (!this.name || this.name.trim() === '') {
            errors.push('El nombre del rol es requerido');
        }

        if (this.name && this.name.length < 3) {
            errors.push('El nombre debe tener al menos 3 caracteres');
        }

        if (!this.code || this.code.trim() === '') {
            errors.push('El código del rol es requerido');
        }

        if (this.code && !/^[A-Z][A-Z0-9_]*$/.test(this.code)) {
            errors.push('El código debe estar en mayúsculas y solo contener letras, números y guiones bajos');
        }

        if (this.level && (this.level < 1 || this.level > 10)) {
            errors.push('El nivel debe estar entre 1 y 10');
        }

        if (this.color && !/^#[0-9A-Fa-f]{6}$/.test(this.color)) {
            errors.push('El color debe ser un código hexadecimal válido');
        }

        if (this.permissions && !Array.isArray(this.permissions)) {
            errors.push('Los permisos deben ser un array');
        }

        return {
            isValid: errors.length === 0,
            errors
        };
    }

    /**
     * Prepara los datos para enviar al servidor
     * @returns {Object} Datos formateados
     */
    toApiFormat() {
        return {
            id: this.id,
            name: this.name.trim(),
            code: this.code.trim().toUpperCase(),
            description: this.description.trim(),
            level: this.level,
            color: this.color,
            permissions: this.permissions.map(p => p.id || p),
            isActive: this.isActive,
            isSystem: this.isSystem,
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
     * Verifica si el rol tiene un permiso específico
     * @param {string|Permission} permission - Permiso a verificar
     * @returns {boolean} True si tiene el permiso
     */
    hasPermission(permission) {
        if (typeof permission === 'string') {
            return this.permissions.some(p => 
                (p.code && p.code === permission) || 
                (p.id && p.id === permission) ||
                (typeof p === 'string' && p === permission)
            );
        }
        
        if (permission && permission.id) {
            return this.permissions.some(p => 
                (p.id && p.id === permission.id) ||
                (typeof p === 'string' && p === permission.id)
            );
        }

        return false;
    }

    /**
     * Verifica si el rol puede realizar una acción en un módulo/recurso
     * @param {string} module - Módulo
     * @param {string} action - Acción
     * @param {string} resource - Recurso
     * @returns {boolean} True si puede realizar la acción
     */
    canPerform(module, action, resource) {
        return this.permissions.some(permission => {
            if (typeof permission === 'object' && permission.module) {
                return permission.module === module && 
                       permission.resource === resource &&
                       (permission.action === action || permission.action === 'manage');
            }
            return false;
        });
    }

    /**
     * Agrega un permiso al rol
     * @param {Permission|string} permission - Permiso a agregar
     */
    addPermission(permission) {
        const permissionId = typeof permission === 'object' ? permission.id : permission;
        if (!this.hasPermission(permissionId)) {
            this.permissions.push(permission);
            this.updatedAt = new Date();
        }
    }

    /**
     * Remueve un permiso del rol
     * @param {Permission|string} permission - Permiso a remover
     */
    removePermission(permission) {
        const permissionId = typeof permission === 'object' ? permission.id : permission;
        this.permissions = this.permissions.filter(p => {
            if (typeof p === 'object') {
                return p.id !== permissionId;
            }
            return p !== permissionId;
        });
        this.updatedAt = new Date();
    }

    /**
     * Obtiene los nombres de los permisos
     * @returns {Array} Array de nombres de permisos
     */
    getPermissionNames() {
        return this.permissions.map(permission => {
            if (typeof permission === 'object' && permission.name) {
                return permission.name;
            }
            return permission;
        });
    }

    /**
     * Obtiene el número de permisos
     * @returns {number} Cantidad de permisos
     */
    getPermissionCount() {
        return this.permissions.length;
    }

    /**
     * Clona el modelo
     * @returns {Role} Nueva instancia
     */
    clone() {
        return new Role({
            id: this.id,
            name: this.name,
            code: this.code,
            description: this.description,
            level: this.level,
            color: this.color,
            permissions: [...this.permissions],
            isActive: this.isActive,
            isSystem: this.isSystem,
            createdAt: this.createdAt,
            updatedAt: this.updatedAt,
            createdBy: this.createdBy,
            updatedBy: this.updatedBy
        });
    }

    /**
     * Crea una instancia desde datos de API
     * @param {Object} apiData - Datos de la API
     * @returns {Role} Nueva instancia
     */
    static fromApiData(apiData) {
        return new Role({
            id: apiData.id,
            name: apiData.name,
            code: apiData.code,
            description: apiData.description || '',
            level: apiData.level || 1,
            color: apiData.color || '#6c757d',
            permissions: apiData.permissions || [],
            isActive: apiData.is_active !== undefined ? apiData.is_active : true,
            isSystem: apiData.is_system !== undefined ? apiData.is_system : false,
            createdAt: apiData.created_at ? new Date(apiData.created_at) : new Date(),
            updatedAt: apiData.updated_at ? new Date(apiData.updated_at) : new Date(),
            createdBy: apiData.created_by,
            updatedBy: apiData.updated_by
        });
    }

    /**
     * Obtiene los niveles disponibles
     * @returns {Array} Lista de niveles
     */
    static getAvailableLevels() {
        return [
            { value: 1, label: 'Básico', description: 'Acceso básico al sistema' },
            { value: 2, label: 'Operador', description: 'Operaciones básicas de módulos' },
            { value: 3, label: 'Supervisor', description: 'Supervisión de operaciones' },
            { value: 4, label: 'Administrador', description: 'Administración de módulos' },
            { value: 5, label: 'Super Admin', description: 'Administración total del sistema' }
        ];
    }

    /**
     * Obtiene colores predefinidos para roles
     * @returns {Array} Lista de colores
     */
    static getAvailableColors() {
        return [
            { value: '#007bff', label: 'Azul' },
            { value: '#28a745', label: 'Verde' },
            { value: '#ffc107', label: 'Amarillo' },
            { value: '#dc3545', label: 'Rojo' },
            { value: '#6f42c1', label: 'Púrpura' },
            { value: '#fd7e14', label: 'Naranja' },
            { value: '#20c997', label: 'Verde azulado' },
            { value: '#6c757d', label: 'Gris' }
        ];
    }
}
