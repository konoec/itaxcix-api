/**
 * Modelo User
 * Maneja la estructura y validación de usuarios del sistema
 */
class User {
    constructor(data = {}) {
        this.id = data.id || null;
        this.username = data.username || '';
        this.email = data.email || '';
        this.firstName = data.firstName || '';
        this.lastName = data.lastName || '';
        this.phone = data.phone || '';
        this.avatar = data.avatar || null;
        this.status = data.status || 'active'; // active, inactive, suspended, pending
        this.emailVerified = data.emailVerified !== undefined ? data.emailVerified : false;
        this.phoneVerified = data.phoneVerified !== undefined ? data.phoneVerified : false;
        this.lastLogin = data.lastLogin || null;
        this.loginAttempts = data.loginAttempts || 0;
        this.lockedUntil = data.lockedUntil || null;
        this.roles = data.roles || []; // Array de roles
        this.permissions = data.permissions || []; // Permisos adicionales directos
        this.preferences = data.preferences || {}; // Configuraciones personales
        this.createdAt = data.createdAt || new Date();
        this.updatedAt = data.updatedAt || new Date();
        this.createdBy = data.createdBy || null;
        this.updatedBy = data.updatedBy || null;
    }

    /**
     * Valida los datos del usuario
     * @returns {Object} {isValid: boolean, errors: Array}
     */
    validate() {
        const errors = [];

        if (!this.username || this.username.trim() === '') {
            errors.push('El nombre de usuario es requerido');
        }

        if (this.username && this.username.length < 3) {
            errors.push('El nombre de usuario debe tener al menos 3 caracteres');
        }

        if (this.username && !/^[a-zA-Z0-9._-]+$/.test(this.username)) {
            errors.push('El nombre de usuario solo puede contener letras, números, puntos, guiones y guiones bajos');
        }

        if (!this.email || this.email.trim() === '') {
            errors.push('El email es requerido');
        }

        if (this.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.email)) {
            errors.push('El email no tiene un formato válido');
        }

        if (!this.firstName || this.firstName.trim() === '') {
            errors.push('El nombre es requerido');
        }

        if (!this.lastName || this.lastName.trim() === '') {
            errors.push('El apellido es requerido');
        }

        if (this.phone && !/^[\+]?[0-9\s\-\(\)]{8,15}$/.test(this.phone)) {
            errors.push('El teléfono no tiene un formato válido');
        }

        const validStatuses = ['active', 'inactive', 'suspended', 'pending'];
        if (!validStatuses.includes(this.status)) {
            errors.push('El estado debe ser: active, inactive, suspended o pending');
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
            username: this.username.trim().toLowerCase(),
            email: this.email.trim().toLowerCase(),
            firstName: this.firstName.trim(),
            lastName: this.lastName.trim(),
            phone: this.phone.trim(),
            avatar: this.avatar,
            status: this.status,
            emailVerified: this.emailVerified,
            phoneVerified: this.phoneVerified,
            roles: this.roles.map(r => r.id || r),
            permissions: this.permissions.map(p => p.id || p),
            preferences: this.preferences,
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
     * Obtiene el nombre completo del usuario
     * @returns {string} Nombre completo
     */
    getFullName() {
        return `${this.firstName} ${this.lastName}`.trim();
    }

    /**
     * Obtiene las iniciales del usuario
     * @returns {string} Iniciales
     */
    getInitials() {
        const first = this.firstName.charAt(0).toUpperCase();
        const last = this.lastName.charAt(0).toUpperCase();
        return `${first}${last}`;
    }

    /**
     * Verifica si el usuario está activo
     * @returns {boolean} True si está activo
     */
    isActive() {
        return this.status === 'active';
    }

    /**
     * Verifica si el usuario está bloqueado
     * @returns {boolean} True si está bloqueado
     */
    isLocked() {
        return this.lockedUntil && new Date() < new Date(this.lockedUntil);
    }

    /**
     * Verifica si el usuario tiene un rol específico
     * @param {string|Role} role - Rol a verificar
     * @returns {boolean} True si tiene el rol
     */
    hasRole(role) {
        if (typeof role === 'string') {
            return this.roles.some(r => 
                (r.code && r.code === role) || 
                (r.id && r.id === role) ||
                (typeof r === 'string' && r === role)
            );
        }
        
        if (role && role.id) {
            return this.roles.some(r => 
                (r.id && r.id === role.id) ||
                (typeof r === 'string' && r === role.id)
            );
        }

        return false;
    }

    /**
     * Verifica si el usuario tiene un permiso específico
     * @param {string|Permission} permission - Permiso a verificar
     * @returns {boolean} True si tiene el permiso
     */
    hasPermission(permission) {
        // Verificar permisos directos
        const hasDirectPermission = this.permissions.some(p => {
            if (typeof permission === 'string') {
                return (p.code && p.code === permission) || 
                       (p.id && p.id === permission) ||
                       (typeof p === 'string' && p === permission);
            }
            return p.id === permission.id;
        });

        if (hasDirectPermission) return true;

        // Verificar permisos a través de roles
        return this.roles.some(role => {
            if (typeof role === 'object' && role.hasPermission) {
                return role.hasPermission(permission);
            }
            return false;
        });
    }

    /**
     * Verifica si el usuario puede realizar una acción en un módulo/recurso
     * @param {string} module - Módulo
     * @param {string} action - Acción
     * @param {string} resource - Recurso
     * @returns {boolean} True si puede realizar la acción
     */
    canPerform(module, action, resource) {
        // Verificar a través de roles
        return this.roles.some(role => {
            if (typeof role === 'object' && role.canPerform) {
                return role.canPerform(module, action, resource);
            }
            return false;
        });
    }

    /**
     * Agrega un rol al usuario
     * @param {Role|string} role - Rol a agregar
     */
    addRole(role) {
        const roleId = typeof role === 'object' ? role.id : role;
        if (!this.hasRole(roleId)) {
            this.roles.push(role);
            this.updatedAt = new Date();
        }
    }

    /**
     * Remueve un rol del usuario
     * @param {Role|string} role - Rol a remover
     */
    removeRole(role) {
        const roleId = typeof role === 'object' ? role.id : role;
        this.roles = this.roles.filter(r => {
            if (typeof r === 'object') {
                return r.id !== roleId;
            }
            return r !== roleId;
        });
        this.updatedAt = new Date();
    }

    /**
     * Obtiene los nombres de los roles
     * @returns {Array} Array de nombres de roles
     */
    getRoleNames() {
        return this.roles.map(role => {
            if (typeof role === 'object' && role.name) {
                return role.name;
            }
            return role;
        });
    }

    /**
     * Actualiza la preferencia del usuario
     * @param {string} key - Clave de la preferencia
     * @param {any} value - Valor de la preferencia
     */
    updatePreference(key, value) {
        this.preferences[key] = value;
        this.updatedAt = new Date();
    }

    /**
     * Obtiene una preferencia del usuario
     * @param {string} key - Clave de la preferencia
     * @param {any} defaultValue - Valor por defecto
     * @returns {any} Valor de la preferencia
     */
    getPreference(key, defaultValue = null) {
        return this.preferences[key] !== undefined ? this.preferences[key] : defaultValue;
    }

    /**
     * Clona el modelo
     * @returns {User} Nueva instancia
     */
    clone() {
        return new User({
            id: this.id,
            username: this.username,
            email: this.email,
            firstName: this.firstName,
            lastName: this.lastName,
            phone: this.phone,
            avatar: this.avatar,
            status: this.status,
            emailVerified: this.emailVerified,
            phoneVerified: this.phoneVerified,
            lastLogin: this.lastLogin,
            loginAttempts: this.loginAttempts,
            lockedUntil: this.lockedUntil,
            roles: [...this.roles],
            permissions: [...this.permissions],
            preferences: { ...this.preferences },
            createdAt: this.createdAt,
            updatedAt: this.updatedAt,
            createdBy: this.createdBy,
            updatedBy: this.updatedBy
        });
    }

    /**
     * Crea una instancia desde datos de API
     * @param {Object} apiData - Datos de la API
     * @returns {User} Nueva instancia
     */
    static fromApiData(apiData) {
        return new User({
            id: apiData.id,
            username: apiData.username,
            email: apiData.email,
            firstName: apiData.first_name || apiData.firstName,
            lastName: apiData.last_name || apiData.lastName,
            phone: apiData.phone,
            avatar: apiData.avatar,
            status: apiData.status || 'active',
            emailVerified: apiData.email_verified !== undefined ? apiData.email_verified : false,
            phoneVerified: apiData.phone_verified !== undefined ? apiData.phone_verified : false,
            lastLogin: apiData.last_login ? new Date(apiData.last_login) : null,
            loginAttempts: apiData.login_attempts || 0,
            lockedUntil: apiData.locked_until ? new Date(apiData.locked_until) : null,
            roles: apiData.roles || [],
            permissions: apiData.permissions || [],
            preferences: apiData.preferences || {},
            createdAt: apiData.created_at ? new Date(apiData.created_at) : new Date(),
            updatedAt: apiData.updated_at ? new Date(apiData.updated_at) : new Date(),
            createdBy: apiData.created_by,
            updatedBy: apiData.updated_by
        });
    }

    /**
     * Obtiene los estados disponibles
     * @returns {Array} Lista de estados
     */
    static getAvailableStatuses() {
        return [
            { value: 'active', label: 'Activo', color: '#28a745' },
            { value: 'inactive', label: 'Inactivo', color: '#6c757d' },
            { value: 'suspended', label: 'Suspendido', color: '#dc3545' },
            { value: 'pending', label: 'Pendiente', color: '#ffc107' }
        ];
    }
}
