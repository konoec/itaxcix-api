/**
 * Service for role assignment operations
 * Handles role assignment functionality for users
 * 
 * FUNCIONALIDADES PRINCIPALES:
 * - Solo envía roles de tipo web (web: true) a la API
 * - Evita duplicados comparando con roles actuales del usuario
 * - Maneja la desasignación completa enviando array vacío
 * - Proporciona mensajes específicos según el tipo de operación
 */

class AssignRoleService {
    static get API_BASE_URL() {
        return 'https://149.130.161.148/api/v1';
    }

    static getAuthToken() {
        return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
    }

    /**
     * Asigna roles a un usuario
     * Solo envía roles de tipo web y evita duplicados
     * @param {string|number} userId - ID del usuario
     * @param {Array} selectedRoleIds - IDs de roles seleccionados
     * @param {Array} currentUserRoles - Roles actuales del usuario
     * @param {Array} allRoles - Todos los roles disponibles
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async assignRolesToUser(userId, selectedRoleIds, currentUserRoles = [], allRoles = []) {
        try {
            console.log('🔄 Iniciando asignación de roles para usuario:', userId);
            console.log('📋 Roles seleccionados:', selectedRoleIds);
            console.log('📋 Roles actuales del usuario:', currentUserRoles.map(r => r.id));
            
            // Filtrar solo roles de tipo web
            const webRoles = allRoles.filter(role => role.web === true);
            console.log('🌐 Roles web disponibles:', webRoles.map(r => `${r.name} (${r.id})`));
            
            // Filtrar roles seleccionados para incluir solo los de tipo web
            const webRoleIds = selectedRoleIds.filter(roleId => {
                const role = allRoles.find(r => r.id === roleId);
                return role && role.web === true;
            });
            console.log('✅ Roles web seleccionados:', webRoleIds);
            
            // Obtener IDs de roles web actualmente asignados
            const currentWebRoleIds = currentUserRoles
                .filter(role => {
                    const roleData = allRoles.find(r => r.id === role.id);
                    return roleData && roleData.web === true;
                })
                .map(role => role.id);
            
            console.log('� Roles web actuales del usuario:', currentWebRoleIds);
            
            // Determinar si hay cambios que aplicar
            const rolesAreEqual = (arr1, arr2) => {
                if (arr1.length !== arr2.length) return false;
                const sorted1 = [...arr1].sort();
                const sorted2 = [...arr2].sort();
                return sorted1.every((val, index) => val === sorted2[index]);
            };
            
            const hasChanges = !rolesAreEqual(webRoleIds, currentWebRoleIds);
            
            if (!hasChanges) {
                console.log('ℹ️ No hay cambios que aplicar');
                return {
                    success: true,
                    message: 'No hay cambios que aplicar',
                    data: { assignedRoles: webRoleIds }
                };
            }
            
            // Determinar el tipo de operación
            let operationType = 'update';
            let roleIdsToSend = webRoleIds;
            
            if (webRoleIds.length === 0) {
                // Si no hay roles seleccionados, enviar array vacío para desasignar todos
                operationType = 'remove_all';
                roleIdsToSend = [];
                console.log('🗑️ Desasignando todos los roles web del usuario');
            } else if (currentWebRoleIds.length === 0) {
                // Si el usuario no tenía roles web y ahora se le asignan
                operationType = 'assign_new';
                console.log('➕ Asignando roles web por primera vez al usuario');
            } else {
                // Actualización normal de roles
                console.log('🔄 Actualizando roles web del usuario');
            }
            
            // Enviar todos los roles web seleccionados (incluyendo array vacío si corresponde)
            const response = await fetch(`${this.API_BASE_URL}/users/${userId}/roles`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${this.getAuthToken()}`
                },
                body: JSON.stringify({
                    roleIds: roleIdsToSend
                })
            });
            
            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || `Error HTTP: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('✅ Roles actualizados exitosamente:', data);
            
            // Mensaje personalizado según el tipo de operación
            let successMessage;
            switch (operationType) {
                case 'remove_all':
                    successMessage = 'Todos los roles han sido desasignados del usuario';
                    break;
                case 'assign_new':
                    successMessage = 'Roles asignados correctamente al usuario';
                    break;
                default:
                    successMessage = 'Roles del usuario actualizados correctamente';
            }
            
            return {
                success: true,
                message: successMessage,
                data
            };
            
        } catch (error) {
            console.error('❌ Error al asignar roles:', error);
            throw new Error(`Error al asignar roles: ${error.message}`);
        }
    }
    
    /**
     * Obtiene los roles asignados a un usuario
     * @param {string|number} userId - ID del usuario
     * @returns {Promise<Array>} Lista de roles del usuario
     */
    static async getUserRoles(userId) {
        try {
            const response = await fetch(`${this.API_BASE_URL}/users/${userId}/roles`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${this.getAuthToken()}`
                }
            });
            
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            
            const data = await response.json();
            return data.roles || [];
            
        } catch (error) {
            console.error('❌ Error al obtener roles del usuario:', error);
            throw error;
        }
    }
}

// Make available globally
if (typeof window !== 'undefined') {
    window.AssignRoleService = AssignRoleService;
}

console.log('✅ AssignRoleService loaded and available globally');

