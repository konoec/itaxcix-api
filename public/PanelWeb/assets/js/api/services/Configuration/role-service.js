/**
 * Servicio para la gestión de roles
 * Maneja todas las operaciones CRUD de roles de forma independiente
 */

class RoleService {
    static get API_BASE_URL() {
        return 'https://149.130.161.148/api/v1';
    }

    // Función helper para obtener el token de autenticación
    static getAuthToken() {
        // Compatibilidad: busca 'token' o 'authToken'
        return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
    }

    /**
     * Crea un nuevo rol
     * @param {Object} roleData - Datos del rol
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async createRole(roleData) {
        try {
            console.log('🔄 RoleService: Creando nuevo rol...', roleData);
            const token = RoleService.getAuthToken();
            console.log('🔑 Token disponible:', token ? 'Sí' : 'No');
            console.log('🔑 Token value (primeros 20 chars):', token ? token.substring(0, 20) + '...' : 'No token');
            
            // Validar datos antes de enviar
            if (!roleData.name || roleData.name.trim().length === 0) {
                console.error('❌ Datos inválidos: nombre es requerido');
                return { success: false, message: 'El nombre del rol es requerido' };
            }
            
            // Convertir a boolean si no lo es (más permisivo)
            const active = roleData.active === true || roleData.active === 'true';
            const web = roleData.web === true || roleData.web === 'true';
            
            console.log('🔄 Conversión de datos:');
            console.log('   - active original:', roleData.active, typeof roleData.active);
            console.log('   - active convertido:', active, typeof active);
            console.log('   - web original:', roleData.web, typeof roleData.web);
            console.log('   - web convertido:', web, typeof web);
            
            // Preparar datos finales (algunos APIs esperan campos específicos)
            const finalRoleData = {
                name: roleData.name.trim(),
                active: active,
                web: web
            };
            
            const requestBody = JSON.stringify(finalRoleData);
            console.log('📋 Datos finales enviados:', requestBody);
            console.log('🌐 Haciendo petición a:', `${RoleService.API_BASE_URL}/roles`);
            
            const response = await fetch(`${RoleService.API_BASE_URL}/roles`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    ...(token ? { 'Authorization': `Bearer ${token}` } : {})
                },
                body: requestBody
            });
            
            console.log('📡 Respuesta HTTP status:', response.status);
            console.log('📡 Respuesta HTTP statusText:', response.statusText);
            console.log('📡 Respuesta Headers:', Object.fromEntries(response.headers.entries()));
            
            const result = await response.json();
            console.log('📋 Respuesta parseada:', result);
            
            // Verificar si la respuesta HTTP fue exitosa
            if (!response.ok) {
                console.error('❌ Error HTTP:', response.status, response.statusText);
                
                // Manejar errores específicos
                if (response.status === 409 || (result.error && result.error.message && result.error.message.includes('already exists'))) {
                    // Error de duplicado - el rol ya existe
                    return { 
                        success: false, 
                        message: 'Ya existe un rol con ese nombre. Por favor, elija un nombre diferente.',
                        error: result 
                    };
                } else if (response.status === 500 && result.error && result.error.message && result.error.message.includes('already exists')) {
                    // Error 500 pero por duplicado (problema del backend)
                    return { 
                        success: false, 
                        message: 'Ya existe un rol con ese nombre. Por favor, elija un nombre diferente.',
                        error: result 
                    };
                } else {
                    // Otros errores
                    return { 
                        success: false, 
                        message: result.message || `Error HTTP ${response.status}: ${response.statusText}`,
                        error: result 
                    };
                }
            }
            
            // La API devuelve una estructura estándar con success, message y data
            return result;
        } catch (error) {
            console.error('❌ Error de red en RoleService.createRole:', error);
            return { success: false, message: 'Error de red', error };
        }
    }

    /**
     * Obtiene la lista de roles con paginación y filtros
     * @param {number} page - Número de página (default: 1)
     * @param {number} perPage - Cantidad de elementos por página (default: 10)
     * @param {string} search - Término de búsqueda por nombre (opcional)
     * @param {boolean|null} webOnly - Filtrar solo roles web (null = no filtrar, true = solo web, false = solo app)
     * @param {boolean|null} activeOnly - Filtrar solo roles activos (null = no filtrar, true = solo activos, false = solo inactivos)
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async getRoles(page = 1, perPage = 10, search = null, webOnly = null, activeOnly = null) {
        try {
            console.log(`🔄 RoleService: Obteniendo roles - página ${page}, por página ${perPage}`);
            if (search) console.log(`🔍 Filtro de búsqueda: "${search}"`);
            console.log(`🔧 Filtros: webOnly=${webOnly}, activeOnly=${activeOnly}`);
            
            const token = RoleService.getAuthToken();
            
            // Construir URL con parámetros
            const url = new URL(`${RoleService.API_BASE_URL}/roles`);
            
            // Parámetros obligatorios
            url.searchParams.append('page', page.toString());
            url.searchParams.append('limit', perPage.toString());
            
            // Parámetros opcionales
            if (search && search.trim()) {
                url.searchParams.append('search', search.trim());
            }
            
            // Filtros booleanos - solo agregar si no son null
            if (webOnly !== null) {
                url.searchParams.append('webOnly', webOnly.toString());
            }
            if (activeOnly !== null) {
                url.searchParams.append('activeOnly', activeOnly.toString());
            }
            
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
            
            // Manejar errores HTTP específicos
            if (!response.ok) {
                let errorMessage = `Error HTTP ${response.status}: ${response.statusText}`;

                
                switch (response.status) {
                    case 401:
                        errorMessage = responseData.message || 'No autorizado - Token inválido o expirado';
                        break;
                    case 403:
                        errorMessage = responseData.message || 'Acceso denegado - Sin permisos de CONFIGURACIÓN';
                        break;
                    default:
                        errorMessage = responseData.message || errorMessage;
                }
                
                console.error('❌ Error HTTP:', response.status, errorMessage);
                return { 
                    success: false, 
                    message: errorMessage,
                    error: responseData 
                };
            }
            
            // 🔍 DIAGNÓSTICO DETALLADO DEL PROBLEMA "web": false
            if (responseData.success && responseData.data) {
                // Detectar la estructura real de los datos
                let rolesArray = null;
                
                if (responseData.data.roles) {
                    // Estructura nueva: data.roles
                    rolesArray = responseData.data.roles;
                    console.log('🏗️ Estructura detectada: data.roles (nueva API)');
                } else if (responseData.data.data && responseData.data.data.items) {
                    // Estructura: data.data.items
                    rolesArray = responseData.data.data.items;
                    console.log('🏗️ Estructura detectada: data.data.items');
                } else if (responseData.data.items) {
                    // Estructura: data.items
                    rolesArray = responseData.data.items;
                    console.log('🏗️ Estructura detectada: data.items');
                } else if (Array.isArray(responseData.data)) {
                    // Estructura: data (array directo)
                    rolesArray = responseData.data;
                    console.log('🏗️ Estructura detectada: data (array directo)');
                }
                
                if (rolesArray && Array.isArray(rolesArray)) {
                    console.log(`🔍 ANÁLISIS DE ROLES (${rolesArray.length} roles encontrados):`);
                    
                    let webTrueCount = 0;
                    let webFalseCount = 0;
                    let webUndefinedCount = 0;
                    
                    rolesArray.forEach((role, index) => {
                        const webValue = role.web;
                        console.log(`   Rol ${index + 1}: "${role.name}" - web: ${webValue} (${typeof webValue}), active: ${role.active}, id: ${role.id}`);
                        
                        if (webValue === true) webTrueCount++;
                        else if (webValue === false) webFalseCount++;
                        else webUndefinedCount++;
                    });
                    
                    console.log('📊 RESUMEN DEL CAMPO "web":');
                    console.log(`   ✅ web: true  -> ${webTrueCount} roles`);
                    console.log(`   ❌ web: false -> ${webFalseCount} roles`);
                    console.log(`   ❓ web: undefined -> ${webUndefinedCount} roles`);
                    
                    if (webTrueCount === 0 && webFalseCount > 0) {
                        console.warn('⚠️ PROBLEMA DETECTADO: Todos los roles tienen web=false');
                        console.warn('⚠️ Esto indica un problema en el backend/base de datos');
                        console.warn('⚠️ Debería haber al menos algunos roles con web=true');
                    }
                } else {
                    console.warn('⚠️ No se pudo encontrar el array de roles en la respuesta');
                }
            }
            
            // Verificar si la respuesta es exitosa según el campo success
            if (responseData.success === false) {
                console.error('❌ API devolvió success: false:', responseData.message);
                return responseData;
            }
            
            // Si no hay campo success explícito y el status HTTP es exitoso, considerarlo exitoso
            if (responseData.success === undefined && response.ok) {
                return { ...responseData, success: true };
            }
            
            return responseData;
        } catch (error) {
            console.error('❌ Error de red en RoleService.getRoles:', error);
            return { success: false, message: 'Error de red', error };
        }
    }

    /**
     * Busca roles por nombre
     * @param {string} searchTerm - Término de búsqueda
     * @param {number} page - Número de página (default: 1)
     * @param {number} perPage - Cantidad de elementos por página (default: 10)
     * @param {boolean|null} webOnly - Filtrar solo roles web (null = no filtrar, true = solo web, false = solo app)
     * @param {boolean|null} activeOnly - Filtrar solo roles activos (null = no filtrar, true = solo activos, false = solo inactivos)
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async searchRoles(searchTerm, page = 1, perPage = 10, webOnly = null, activeOnly = null) {
        try {
            console.log(`🔍 RoleService: Buscando roles - término: "${searchTerm}", página ${page}, por página ${perPage}`);
            
            // Usar el método getRoles que ya tiene toda la lógica actualizada
            return await RoleService.getRoles(page, perPage, searchTerm, webOnly, activeOnly);
        } catch (error) {
            console.error('❌ Error de red en RoleService.searchRoles:', error);
            return { success: false, message: 'Error de red', error };
        }
    }

    /**
     * Actualiza un rol existente
     * @param {Object} roleData - Datos del rol (debe incluir id)
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async updateRole(roleData) {
        try {
            console.log('🔄 RoleService: Actualizando rol...', roleData);
            const token = RoleService.getAuthToken();
            console.log('🔑 Token disponible:', token ? 'Sí' : 'No');
            
            // Validar que el roleData incluya el ID
            if (!roleData.id) {
                console.error('❌ ID del rol es requerido para actualización');
                return { success: false, message: 'ID del rol es requerido' };
            }
            
            // Preparar datos del body (sin incluir el ID en el body, va en la URL)
            const { id, ...updateData } = roleData;
            const requestBody = JSON.stringify(updateData);
            console.log('📋 Request body preparado:', requestBody);
            console.log('🌐 Haciendo petición a:', `${RoleService.API_BASE_URL}/roles/${id}`);
            
            const response = await fetch(`${RoleService.API_BASE_URL}/roles/${id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    ...(token ? { 'Authorization': `Bearer ${token}` } : {})
                },
                body: requestBody
            });
            
            console.log('📡 Respuesta HTTP status:', response.status);
            console.log('📡 Headers de respuesta:', Object.fromEntries(response.headers.entries()));
            
            const result = await response.json();
            console.log('📋 Respuesta parseada:', result);
            
            // Verificar si la respuesta HTTP fue exitosa
            if (!response.ok) {
                console.error('❌ Error HTTP:', response.status, response.statusText);
                
                // Manejar errores específicos para actualización
                if (response.status === 409 || (result.error && result.error.message && result.error.message.includes('already exists'))) {
                    return { 
                        success: false, 
                        message: 'Ya existe un rol con ese nombre. Por favor, elija un nombre diferente.',
                        error: result 
                    };
                } else if (response.status === 500 && result.error && result.error.message && result.error.message.includes('already exists')) {
                    return { 
                        success: false, 
                        message: 'Ya existe un rol con ese nombre. Por favor, elija un nombre diferente.',
                        error: result 
                    };
                } else {
                    return { 
                        success: false, 
                        message: result.message || `Error HTTP ${response.status}: ${response.statusText}`,
                        error: result 
                    };
                }
            }
            
            return result;
        } catch (error) {
            console.error('❌ Error de red en RoleService.updateRole:', error);
            return { success: false, message: 'Error de red', error };
        }
    }

    /**
     * Elimina un rol
     * Endpoint: DELETE /api/v1/roles/{roleId}
     * Respuestas de la API:
     * - Éxito: HTTP 200 (sin body o body vacío)
     * - Error con usuarios asignados: HTTP 500 con mensaje específico
     * @param {number} roleId - ID del rol
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async deleteRole(roleId) {
        try {
            console.log('🔄 RoleService: Eliminando rol ID:', roleId);
            const token = RoleService.getAuthToken();
            console.log('🔑 Token disponible:', token ? 'Sí' : 'No');
            console.log('🌐 Haciendo petición a:', `${RoleService.API_BASE_URL}/roles/${roleId}`);
            
            const response = await fetch(`${RoleService.API_BASE_URL}/roles/${roleId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    ...(token ? { 'Authorization': `Bearer ${token}` } : {})
                }
            });
            
            console.log('📡 Respuesta HTTP status:', response.status);
            console.log('📡 Headers de respuesta:', Object.fromEntries(response.headers.entries()));
            
            let result = null;
            const contentType = response.headers.get('content-type');
            
            // Intentar parsear la respuesta si tiene contenido
            if (contentType && contentType.includes('application/json')) {
                try {
                    result = await response.json();
                    console.log('📋 Respuesta parseada:', result);
                } catch (parseError) {
                    console.warn('⚠️ No se pudo parsear la respuesta JSON:', parseError);
                    result = null;
                }
            } else {
                console.log('📋 Respuesta sin contenido JSON o vacía');
                result = null;
            }
            
            // Manejar respuesta exitosa (200)
            if (response.ok) {
                console.log('✅ Rol eliminado exitosamente');
                return {
                    success: true,
                    message: 'Rol eliminado correctamente'
                };
            }
            
            // Manejar errores HTTP específicos
            let errorMessage = 'Error desconocido';
            
            switch (response.status) {
                case 404:
                    errorMessage = (result && result.message) || 'Rol no encontrado';
                    console.error('❌ Error 404: Rol no encontrado');
                    break;
                case 409:
                    errorMessage = (result && result.message) || 'No se puede eliminar: El rol está en uso';
                    console.error('❌ Error 409: Conflicto - Rol en uso');
                    break;
                case 500:
                    // Detectar el caso específico de usuarios asignados
                    if (result && result.error && result.error.message) {
                        if (result.error.message.includes('usuarios asignados')) {
                            errorMessage = 'No se puede eliminar el rol porque tiene usuarios asignados';
                            console.error('❌ Error 500: Rol con usuarios asignados');
                        } else {
                            errorMessage = result.error.message;
                            console.error('❌ Error 500: Error interno del servidor');
                        }
                    } else if (result && result.message) {
                        errorMessage = result.message;
                        console.error('❌ Error 500: Error interno del servidor');
                    } else {
                        errorMessage = 'Error interno del servidor';
                        console.error('❌ Error 500: Error interno del servidor');
                    }
                    break;
                case 401:
                    errorMessage = (result && result.message) || 'No autorizado - Token inválido o expirado';
                    console.error('❌ Error 401: No autorizado');
                    break;
                case 403:
                    errorMessage = (result && result.message) || 'Acceso denegado - Sin permisos para eliminar roles';
                    console.error('❌ Error 403: Acceso denegado');
                    break;
                default:
                    errorMessage = (result && result.message) || `Error HTTP ${response.status}: ${response.statusText}`;
                    console.error('❌ Error HTTP:', response.status, response.statusText);
            }
            
            return { 
                success: false, 
                message: errorMessage,
                error: result,
                statusCode: response.status
            };
            
        } catch (error) {
            console.error('❌ Error de red en RoleService.deleteRole:', error);
            return { 
                success: false, 
                message: 'Error de red al eliminar el rol', 
                error: error.message 
            };
        }
    }

    /**
     * Obtiene un rol específico por ID
     * @param {number} roleId - ID del rol
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async getRoleById(roleId) {
        try {
            console.log('🔄 RoleService: Obteniendo rol por ID:', roleId);
            console.log('ℹ️ No existe endpoint específico, usando /admin/role/list y filtrando...');
            
            // Usar getRoles para obtener la lista completa y filtrar por ID
            const rolesResponse = await this.getRoles(1, 100); // Obtener suficientes roles
            
            if (!rolesResponse.success || !rolesResponse.data) {
                console.error('❌ Error al obtener lista de roles');
                return { 
                    success: false, 
                    message: 'Error al obtener la lista de roles',
                    error: rolesResponse.error 
                };
            }
            
            // Detectar estructura de datos y extraer roles
            let rolesArray = null;
            if (rolesResponse.data.roles) {
                // Estructura nueva: data.roles
                rolesArray = rolesResponse.data.roles;
            } else if (rolesResponse.data.data && rolesResponse.data.data.items) {
                rolesArray = rolesResponse.data.data.items;
            } else if (rolesResponse.data.items) {
                rolesArray = rolesResponse.data.items;
            } else if (Array.isArray(rolesResponse.data)) {
                rolesArray = rolesResponse.data;
            }
            
            if (!rolesArray || !Array.isArray(rolesArray)) {
                console.error('❌ No se pudo extraer array de roles');
                return { 
                    success: false, 
                    message: 'Estructura de datos inesperada',
                    error: 'No se encontró array de roles válido' 
                };
            }
            
            // Buscar el rol específico por ID
            const targetRoleId = Number(roleId);
            const foundRole = rolesArray.find(role => Number(role.id) === targetRoleId);
            
            console.log(`🔍 Buscando rol con ID ${targetRoleId} en ${rolesArray.length} roles...`);
            
            if (foundRole) {
                console.log('✅ Rol encontrado:', foundRole);
                return { 
                    success: true, 
                    message: 'Rol encontrado exitosamente',
                    data: foundRole 
                };
            } else {
                console.warn(`⚠️ Rol con ID ${targetRoleId} no encontrado`);
                return { 
                    success: false, 
                    message: `Rol con ID ${roleId} no encontrado`,
                    error: 'Role not found' 
                };
            }
            
        } catch (error) {
            console.error('❌ Error de red en RoleService.getRoleById:', error);
            return { success: false, message: 'Error de red', error };
        }
    }

    /**
     * Asigna permisos masivamente a un rol
     * @param {number} roleId - ID del rol
     * @param {number[]} permissionIds - Array de IDs de permisos a asignar
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async assignPermissionsToRole(roleId, permissionIds) {
        try {
            console.log(`🔄 RoleService: Asignando permisos al rol ID ${roleId}...`);
            console.log('📋 Permisos a asignar:', permissionIds);
            
            const token = RoleService.getAuthToken();
            console.log('🔑 Token disponible:', token ? 'Sí' : 'No');
            
            // Validar datos de entrada
            if (!roleId) {
                console.error('❌ ID del rol es requerido');
                return { success: false, message: 'ID del rol es requerido' };
            }
            
            if (!Array.isArray(permissionIds) || permissionIds.length === 0) {
                console.error('❌ Array de IDs de permisos es requerido y no puede estar vacío');
                return { success: false, message: 'Debe proporcionar al menos un permiso para asignar' };
            }
            
            // Validar que todos los IDs sean números válidos
            const invalidIds = permissionIds.filter(id => !Number.isInteger(id) || id <= 0);
            if (invalidIds.length > 0) {
                console.error('❌ IDs de permisos inválidos:', invalidIds);
                return { success: false, message: 'Todos los IDs de permisos deben ser números enteros positivos' };
            }
            
            // Preparar datos del request
            const requestData = {
                permissionIds: permissionIds
            };
            
            const requestBody = JSON.stringify(requestData);
            console.log('📋 Request body preparado:', requestBody);
            console.log('🌐 Haciendo petición a:', `${RoleService.API_BASE_URL}/roles/${roleId}/permissions`);
            
            const response = await fetch(`${RoleService.API_BASE_URL}/roles/${roleId}/permissions`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    ...(token ? { 'Authorization': `Bearer ${token}` } : {})
                },
                body: requestBody
            });
            
            console.log('📡 Respuesta HTTP status:', response.status);
            console.log('📡 Headers de respuesta:', Object.fromEntries(response.headers.entries()));
            
            const result = await response.json();
            console.log('📋 Respuesta parseada:', result);
            
            // Verificar si la respuesta HTTP fue exitosa
            if (!response.ok) {
                console.error('❌ Error HTTP:', response.status, response.statusText);
                
                // Manejar errores específicos
                let errorMessage = 'Error desconocido';
                
                switch (response.status) {
                    case 404:
                        errorMessage = result.message || 'Rol no encontrado';
                        console.error('❌ Error 404: Rol no encontrado');
                        break;
                    case 400:
                        errorMessage = result.message || 'Datos de entrada inválidos';
                        console.error('❌ Error 400: Datos inválidos');
                        break;
                    case 409:
                        errorMessage = result.message || 'Conflicto al asignar permisos';
                        console.error('❌ Error 409: Conflicto');
                        break;
                    case 401:
                        errorMessage = result.message || 'No autorizado - Token inválido o expirado';
                        console.error('❌ Error 401: No autorizado');
                        break;
                    case 403:
                        errorMessage = result.message || 'Acceso denegado - Sin permisos para asignar permisos a roles';
                        console.error('❌ Error 403: Acceso denegado');
                        break;
                    case 500:
                        errorMessage = result.message || 'Error interno del servidor';
                        console.error('❌ Error 500: Error interno del servidor');
                        break;
                    default:
                        errorMessage = result.message || `Error HTTP ${response.status}: ${response.statusText}`;
                        console.error('❌ Error HTTP:', response.status, response.statusText);
                }
                
                return { 
                    success: false, 
                    message: errorMessage,
                    error: result,
                    statusCode: response.status
                };
            }
            
            // Verificar el campo success en la respuesta
            if (result.success === false) {
                console.error('❌ API devolvió success: false:', result.message);
                return result;
            }
            
            console.log('✅ Permisos asignados exitosamente al rol');
            console.log('📊 Rol actualizado:', result.data);
            
            return result;
            
        } catch (error) {
            console.error('❌ Error de red en RoleService.assignPermissionsToRole:', error);
            return { 
                success: false, 
                message: 'Error de red al asignar permisos al rol', 
                error: error.message 
            };
        }
    }
}

// Para compatibilidad global
if (typeof window !== 'undefined') {
    window.RoleService = RoleService;
}
