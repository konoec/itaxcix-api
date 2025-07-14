/**
 * Servicio para la asignación de permisos a roles
 * Maneja todas las operaciones relacionadas con permisos de roles
 */
class AssignPermissionService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        
        // Cache local para optimizar rendimiento
        this.cache = {
            allPermissions: null,
            rolePermissions: new Map(),
            lastFetch: null
        };
    }

    /**
     * Obtiene todos los permisos disponibles en el sistema
     * @returns {Promise} - Promise con la lista de permisos
     */
    async getAllPermissions() {
        try {
            const token = sessionStorage.getItem('authToken');
            if (!token) {
                throw new Error('Token de autenticación no encontrado. Por favor, inicie sesión nuevamente.');
            }

            // Verificar cache (válido por 5 minutos)
            if (this.cache.allPermissions && this.cache.lastFetch && 
                (Date.now() - this.cache.lastFetch) < 300000) {
                console.log('🏃‍♂️ Usando permisos desde cache');
                return {
                    success: true,
                    data: this.cache.allPermissions
                };
            }

            console.log('📡 Obteniendo todos los permisos disponibles...');
            const response = await fetch(`${this.baseUrl}/permissions`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            if (!response.ok) {
                if (response.status === 401) {
                    throw new Error('Su sesión ha expirado. Por favor, inicie sesión nuevamente.');
                }
                if (response.status === 403) {
                    throw new Error('No tiene permisos para acceder a esta información.');
                }
                if (response.status === 404) {
                    throw new Error('Endpoint de permisos no encontrado. Contacte al administrador.');
                }
                
                try {
                    const errorData = await response.json();
                    throw new Error(errorData.message || `Error del servidor (${response.status})`);
                } catch {
                    throw new Error(`Error del servidor (${response.status}): ${response.statusText}`);
                }
            }

            const data = await response.json();
            
            // Normalizar estructura de permisos
            let permissions = [];
            if (data.data && Array.isArray(data.data)) {
                permissions = data.data;
            } else if (Array.isArray(data)) {
                permissions = data;
            } else if (data.permissions && Array.isArray(data.permissions)) {
                permissions = data.permissions;
            }

            // Agregar tipo basado en 'web' property y normalizar estructura
            const normalizedPermissions = permissions.map(permission => ({
                id: permission.id,
                name: permission.name,
                active: permission.active,
                web: permission.web,
                type: permission.web ? 'Web' : 'App'
            }));

            // Actualizar cache
            this.cache.allPermissions = normalizedPermissions;
            this.cache.lastFetch = Date.now();

            console.log('✅ Permisos obtenidos:', normalizedPermissions.length);
            return {
                success: true,
                data: normalizedPermissions
            };

        } catch (error) {
            console.error('❌ Error obteniendo permisos:', error);
            return {
                success: false,
                error: error.message || 'Error de conexión con el servidor'
            };
        }
    }

    /**
     * Obtiene un rol específico con todos sus permisos asignados
     * @param {number} roleId - ID del rol
     * @returns {Promise} - Promise con la respuesta del servidor
     */
    async getRoleWithPermissions(roleId) {
        try {
            const token = sessionStorage.getItem('authToken');
            console.log('🔐 Token de autenticación:', token ? 'Presente' : 'NO ENCONTRADO');
            
            if (!token) {
                throw new Error('Token de autenticación no encontrado. Por favor, inicie sesión nuevamente.');
            }

            const response = await fetch(`${this.baseUrl}/roles/${roleId}/permissions`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            console.log('📡 Respuesta del servidor:', response.status, response.statusText);

            if (!response.ok) {
                if (response.status === 401) {
                    throw new Error('Su sesión ha expirado. Por favor, inicie sesión nuevamente.');
                }
                if (response.status === 403) {
                    throw new Error('No tiene permisos para acceder a esta información.');
                }
                if (response.status === 404) {
                    throw new Error('El rol especificado no fue encontrado.');
                }
                
                // Intentar obtener el mensaje de error del servidor
                try {
                    const errorData = await response.json();
                    throw new Error(errorData.message || `Error del servidor (${response.status})`);
                } catch {
                    throw new Error(`Error del servidor (${response.status}): ${response.statusText}`);
                }
            }

            const data = await response.json();
            console.log('✅ Datos recibidos del servidor:', data);

            return {
                success: true,
                data: data.data
            };

        } catch (error) {
            console.error('❌ Error en getRoleWithPermissions:', error);
            return {
                success: false,
                error: error.message || 'Error de conexión con el servidor'
            };
        }
    }

    /**
     * Actualiza los permisos asignados a un rol
     * @param {number} roleId - ID del rol
     * @param {Array} permissions - Array de permisos a asignar
     * @returns {Promise} - Promise con la respuesta del servidor
     */
    async updateRolePermissions(roleId, permissions) {
        try {
            const token = sessionStorage.getItem('authToken');
            console.log('🔐 Token de autenticación para actualización:', token ? 'Presente' : 'NO ENCONTRADO');
            
            if (!token) {
                throw new Error('Token de autenticación no encontrado. Por favor, inicie sesión nuevamente.');
            }

            console.log('📤 Enviando datos:', { roleId, permissions });

            const response = await fetch(`${this.baseUrl}/roles/${roleId}/permissions`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    permissionIds: permissions
                })
            });

            console.log('📡 Respuesta del servidor (POST):', response.status, response.statusText);

            if (!response.ok) {
                if (response.status === 401) {
                    throw new Error('Su sesión ha expirado. Por favor, inicie sesión nuevamente.');
                }
                if (response.status === 403) {
                    throw new Error('No tiene permisos para realizar esta operación.');
                }
                if (response.status === 404) {
                    throw new Error('El rol especificado no fue encontrado.');
                }
                
                // Intentar obtener el mensaje de error del servidor
                try {
                    const errorData = await response.json();
                    throw new Error(errorData.message || `Error del servidor (${response.status})`);
                } catch {
                    throw new Error(`Error del servidor (${response.status}): ${response.statusText}`);
                }
            }

            const data = await response.json();
            console.log('✅ Actualización exitosa:', data);

            return {
                success: true,
                data: data.data,
                message: data.message
            };

        } catch (error) {
            console.error('❌ Error en updateRolePermissions:', error);
            return {
                success: false,
                error: error.message || 'Error de conexión con el servidor'
            };
        }
    }

    /**
     * Obtiene los permisos completos para la gestión de un rol
     * Combina permisos disponibles con los asignados al rol
     * @param {number} roleId - ID del rol
     * @returns {Promise} - Promise con permisos disponibles y asignados
     */
    async getRolePermissionsForManagement(roleId) {
        try {
            console.log('🔍 Obteniendo permisos para gestión del rol:', roleId);
            
            // Primero obtener los permisos del rol
            const roleResult = await this.getRoleWithPermissions(roleId);
            
            if (!roleResult.success) {
                throw new Error(roleResult.error || 'Error al obtener permisos del rol');
            }

            const roleData = roleResult.data;
            const assignedPermissions = roleData.permissions || [];
            const assignedIds = assignedPermissions.map(p => p.id.toString());

            // Intentar obtener todos los permisos disponibles
            console.log('🔍 Intentando obtener todos los permisos disponibles...');
            const allPermissionsResult = await this.getAllPermissions();

            let availablePermissions;
            
            if (allPermissionsResult.success && allPermissionsResult.data.length > 0) {
                console.log('✅ Usando todos los permisos disponibles del sistema');
                availablePermissions = allPermissionsResult.data;
            } else {
                console.warn('⚠️ Endpoint de todos los permisos no disponible, usando solo permisos del rol');
                // Usar solo los permisos asignados al rol con tipo agregado
                availablePermissions = assignedPermissions.map(p => ({
                    id: p.id,
                    name: p.name,
                    active: p.active,
                    web: p.web,
                    type: p.web ? 'Web' : 'App'
                }));
            }

            console.log('✅ Datos combinados:', {
                totalPermissions: availablePermissions.length,
                assignedPermissions: assignedIds.length,
                roleInfo: roleData.name
            });

            return {
                success: true,
                data: {
                    roleInfo: {
                        id: roleData.id,
                        name: roleData.name,
                        active: roleData.active,
                        web: roleData.web
                    },
                    availablePermissions: availablePermissions,
                    assignedPermissionIds: assignedIds
                }
            };

        } catch (error) {
            console.error('❌ Error en getRolePermissionsForManagement:', error);
            return {
                success: false,
                error: error.message || 'Error al obtener permisos para gestión'
            };
        }
    }

    /**
     * Filtra permisos según término de búsqueda
     * @param {Array} permissions - Array de permisos
     * @param {string} searchTerm - Término de búsqueda
     * @returns {Array} - Permisos filtrados
     */
    filterPermissions(permissions, searchTerm) {
        if (!searchTerm || !searchTerm.trim()) {
            return [...permissions];
        }
        
        const term = searchTerm.toLowerCase();
        return permissions.filter(permission =>
            permission.name.toLowerCase().includes(term) ||
            permission.type.toLowerCase().includes(term)
        );
    }

    /**
     * Valida una lista de IDs de permisos
     * @param {Array} permissionIds - Array de IDs de permisos
     * @param {Array} availablePermissions - Permisos disponibles
     * @returns {Object} - Resultado de validación
     */
    validatePermissionIds(permissionIds, availablePermissions) {
        const availableIds = availablePermissions.map(p => p.id.toString());
        const invalidIds = permissionIds.filter(id => !availableIds.includes(id.toString()));
        
        return {
            isValid: invalidIds.length === 0,
            invalidIds: invalidIds,
            validIds: permissionIds.filter(id => availableIds.includes(id.toString()))
        };
    }

    /**
     * Actualiza los permisos de un rol con validaciones
     * @param {number} roleId - ID del rol
     * @param {Array} permissionIds - Array de IDs de permisos
     * @param {Array} availablePermissions - Permisos disponibles (opcional, para validación)
     * @returns {Promise} - Promise con el resultado
     */
    async updateRolePermissionsValidated(roleId, permissionIds, availablePermissions = null) {
        try {
            console.log('🔄 Actualizando permisos con validación:', {
                roleId,
                permissionIds,
                count: permissionIds.length
            });

            // Validar IDs si se proporcionan permisos disponibles
            if (availablePermissions) {
                const validation = this.validatePermissionIds(permissionIds, availablePermissions);
                if (!validation.isValid) {
                    console.warn('⚠️ IDs de permisos inválidos encontrados:', validation.invalidIds);
                    // Usar solo IDs válidos
                    permissionIds = validation.validIds;
                }
            }

            // Llamar al método base de actualización
            return await this.updateRolePermissions(roleId, permissionIds.map(id => parseInt(id, 10)));

        } catch (error) {
            console.error('❌ Error en updateRolePermissionsValidated:', error);
            return {
                success: false,
                error: error.message || 'Error al actualizar permisos'
            };
        }
    }

    /**
     * Limpia el cache de permisos
     */
    clearCache() {
        this.cache.allPermissions = null;
        this.cache.rolePermissions.clear();
        this.cache.lastFetch = null;
        console.log('🧹 Cache de permisos limpiado');
    }
}

// Crear instancia global del servicio
window.assignPermissionService = new AssignPermissionService();
