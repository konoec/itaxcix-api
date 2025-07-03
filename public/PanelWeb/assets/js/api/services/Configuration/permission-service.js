// Servicio para la gestión de permisos

class PermissionService {
    static get API_BASE_URL() {
        return 'https://149.130.161.148/api/v1';
    }

    static getAuthToken() {
        // Compatibilidad: busca 'token' o 'authToken'
        return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
    }
    /**
     * Crea un nuevo permiso
     * @param {Object} permissionData - Datos del permiso: { name: string, active: boolean, web: boolean }
     * @returns {Promise<Object>} Respuesta de la API: { success: boolean, message: string, data: { id, name, active, web } }
     */
    static async createPermission(permissionData) {
        try {
            console.log('🔄 PermissionService: Creando nuevo permiso...');
            console.log('📋 Datos originales recibidos:', permissionData);
            console.log('📋 Tipo de datos:', typeof permissionData);
            console.log('📋 Keys disponibles:', Object.keys(permissionData));
            
            const token = PermissionService.getAuthToken();
            console.log('🔑 Token disponible:', token ? 'Sí' : 'No');
            if (token) {
                console.log('🔑 Primeros 20 caracteres del token:', token.substring(0, 20) + '...');
                console.log('🔑 Longitud del token:', token.length);
            } else {
                console.warn('⚠️ No hay token de autorización disponible');
                console.log('🔍 Verificando sessionStorage:', {
                    token: sessionStorage.getItem('token'),
                    authToken: sessionStorage.getItem('authToken'),
                    keys: Object.keys(sessionStorage)
                });
            }
            
            const requestBody = JSON.stringify(permissionData);
            console.log('📦 Cuerpo de la petición (JSON):', requestBody);
            
            const response = await fetch(`${PermissionService.API_BASE_URL}/permissions`, {
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
            
            // Si es 400, loggear más detalles
            if (response.status === 400) {
                console.error('❌ Error 400 - Bad Request');
                console.error('❌ Posibles causas:');
                console.error('   - Datos faltantes o inválidos');
                console.error('   - Formato JSON incorrecto'); 
                console.error('   - Headers incorrectos');
                console.error('   - Token de autorización inválido');
                console.error('❌ Respuesta del servidor:', result);
            }
            
            // Si es 500, loggear el error del servidor
            if (response.status === 500) {
                console.error('❌ Error 500 - Internal Server Error');
                console.error('❌ Error en el servidor:', result);
                
                // Si es un error de duplicación, hacer el mensaje más claro
                if (result.message && (result.message.includes('duplicate key') || 
                    result.message.includes('unique constraint') || 
                    result.message.includes('already exists'))) {
                    result.message = `El nombre ya existe. Error interno de base de datos.`;
                }
            }
            
            return result;
        } catch (error) {
            console.error('❌ Error en PermissionService.createPermission:', error);
            return { success: false, message: 'Error de red', error };
        }
    }

    /**
     * Obtiene la lista de permisos con paginación y filtros
     * @param {number} page - Número de página (default: 1)
     * @param {number} limit - Cantidad de elementos por página (default: 20)
     * @param {string} search - Término de búsqueda por nombre (opcional)
     * @param {boolean|null} webOnly - Filtrar solo permisos web (null = no filtrar, true = solo web, false = solo app)
     * @param {boolean|null} activeOnly - Filtrar solo permisos activos (null = no filtrar, true = solo activos, false = solo inactivos)
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async getPermissions(page = 1, limit = 20, search = null, webOnly = null, activeOnly = null) {
        try {
            console.log(`🔄 PermissionService: Obteniendo permisos - página ${page}, límite ${limit}`);
            if (search) console.log(`🔍 Filtro de búsqueda: "${search}"`);
            console.log(`🔧 Filtros: webOnly=${webOnly}, activeOnly=${activeOnly}`);
            
            const token = PermissionService.getAuthToken();
            
            // Construir URL con parámetros
            const url = new URL(`${PermissionService.API_BASE_URL}/permissions`);
            
            // Parámetros obligatorios
            url.searchParams.append('page', page.toString());
            url.searchParams.append('limit', limit.toString());
            
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
            
            // 🔍 DIAGNÓSTICO DETALLADO DE LA RESPUESTA
            if (responseData.success && responseData.data) {
                // Detectar la estructura real de los datos
                let permissionsArray = null;
                
                if (responseData.data.permissions) {
                    // Estructura nueva: data.permissions
                    permissionsArray = responseData.data.permissions;
                    console.log('🏗️ Estructura detectada: data.permissions (nueva API)');
                } else if (responseData.data.data && responseData.data.data.items) {
                    // Estructura: data.data.items
                    permissionsArray = responseData.data.data.items;
                    console.log('🏗️ Estructura detectada: data.data.items');
                } else if (responseData.data.items) {
                    // Estructura: data.items
                    permissionsArray = responseData.data.items;
                    console.log('🏗️ Estructura detectada: data.items');
                } else if (Array.isArray(responseData.data)) {
                    // Estructura: data (array directo)
                    permissionsArray = responseData.data;
                    console.log('🏗️ Estructura detectada: data (array directo)');
                }
                
                if (permissionsArray && Array.isArray(permissionsArray)) {
                    console.log(`🔍 ANÁLISIS DE PERMISOS (${permissionsArray.length} permisos encontrados):`);
                    
                    let webTrueCount = 0;
                    let webFalseCount = 0;
                    let webUndefinedCount = 0;
                    let activeTrueCount = 0;
                    let activeFalseCount = 0;
                    
                    permissionsArray.forEach((permission, index) => {
                        const webValue = permission.web;
                        const activeValue = permission.active;
                        console.log(`   Permiso ${index + 1}: "${permission.name}" - web: ${webValue} (${typeof webValue}), active: ${activeValue} (${typeof activeValue}), id: ${permission.id}`);
                        
                        if (webValue === true) webTrueCount++;
                        else if (webValue === false) webFalseCount++;
                        else webUndefinedCount++;
                        
                        if (activeValue === true) activeTrueCount++;
                        else if (activeValue === false) activeFalseCount++;
                    });
                    
                    console.log('📊 RESUMEN DE CAMPOS:');
                    console.log(`   ✅ web: true  -> ${webTrueCount} permisos`);
                    console.log(`   ❌ web: false -> ${webFalseCount} permisos`);
                    console.log(`   ❓ web: undefined -> ${webUndefinedCount} permisos`);
                    console.log(`   ✅ active: true  -> ${activeTrueCount} permisos`);
                    console.log(`   ❌ active: false -> ${activeFalseCount} permisos`);
                    
                    if (webTrueCount === 0 && webFalseCount > 0) {
                        console.warn('⚠️ PROBLEMA DETECTADO: Todos los permisos tienen web=false');
                        console.warn('⚠️ Esto indica un problema en el backend/base de datos');
                        console.warn('⚠️ Debería haber al menos algunos permisos con web=true');
                    }
                    
                    if (activeTrueCount === 0 && activeFalseCount > 0) {
                        console.warn('⚠️ PROBLEMA DETECTADO: Todos los permisos tienen active=false');
                        console.warn('⚠️ Esto indica un problema en el backend/base de datos');
                        console.warn('⚠️ Debería haber al menos algunos permisos con active=true');
                    }
                } else {
                    console.warn('⚠️ No se pudo encontrar el array de permisos en la respuesta');
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
            console.error('❌ Error de red en PermissionService.getPermissions:', error);
            return { success: false, message: 'Error de red', error };
        }
    }

    /**
     * Obtiene todos los permisos del sistema sin paginación
     * Este método está diseñado para endpoints que no aceptan parámetros page/limit
     * @param {string} search - Término de búsqueda opcional por nombre
     * @param {boolean|null} webOnly - Filtrar solo permisos web (null = no filtrar, true = solo web, false = solo app)
     * @param {boolean|null} activeOnly - Filtrar solo permisos activos (null = no filtrar, true = solo activos, false = solo inactivos)
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async getAllPermissions(search = null, webOnly = null, activeOnly = null) {
        try {
            console.log('🔄 PermissionService: Obteniendo TODOS los permisos sin paginación...');
            if (search) console.log(`� Filtro de búsqueda: "${search}"`);
            console.log(`🔧 Filtros: webOnly=${webOnly}, activeOnly=${activeOnly}`);
            
            const token = PermissionService.getAuthToken();
            
            // Construir URL con parámetros
            const url = new URL(`${PermissionService.API_BASE_URL}/permissions`);
            
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
            
            // 🔍 DIAGNÓSTICO DETALLADO DE LA RESPUESTA PARA getAllPermissions
            if (responseData) {
                // Detectar la estructura real de los datos
                let permissionsArray = null;
                
                if (responseData.data && responseData.data.permissions) {
                    // Estructura nueva: data.permissions
                    permissionsArray = responseData.data.permissions;
                    console.log('🏗️ Estructura detectada: data.permissions (nueva API)');
                } else if (responseData.data && Array.isArray(responseData.data)) {
                    // Estructura: data (array directo)
                    permissionsArray = responseData.data;
                    console.log('🏗️ Estructura detectada: data (array directo)');
                } else if (Array.isArray(responseData)) {
                    // Estructura: array directo en raíz
                    permissionsArray = responseData;
                    console.log('🏗️ Estructura detectada: array directo en raíz');
                } else if (responseData.permissions && Array.isArray(responseData.permissions)) {
                    // Estructura: permissions en raíz
                    permissionsArray = responseData.permissions;
                    console.log('🏗️ Estructura detectada: permissions en raíz');
                }
                
                if (permissionsArray && Array.isArray(permissionsArray)) {
                    console.log(`🔍 ANÁLISIS DE PERMISOS getAllPermissions (${permissionsArray.length} permisos encontrados):`);
                    
                    let webTrueCount = 0;
                    let webFalseCount = 0;
                    let webUndefinedCount = 0;
                    let activeTrueCount = 0;
                    let activeFalseCount = 0;
                    
                    permissionsArray.forEach((permission, index) => {
                        const webValue = permission.web;
                        const activeValue = permission.active;
                        console.log(`   Permiso ${index + 1}: "${permission.name}" - web: ${webValue} (${typeof webValue}), active: ${activeValue} (${typeof activeValue}), id: ${permission.id}`);
                        
                        if (webValue === true) webTrueCount++;
                        else if (webValue === false) webFalseCount++;
                        else webUndefinedCount++;
                        
                        if (activeValue === true) activeTrueCount++;
                        else if (activeValue === false) activeFalseCount++;
                    });
                    
                    console.log('📊 RESUMEN DE CAMPOS (getAllPermissions):');
                    console.log(`   ✅ web: true  -> ${webTrueCount} permisos`);
                    console.log(`   ❌ web: false -> ${webFalseCount} permisos`);
                    console.log(`   ❓ web: undefined -> ${webUndefinedCount} permisos`);
                    console.log(`   ✅ active: true  -> ${activeTrueCount} permisos`);
                    console.log(`   ❌ active: false -> ${activeFalseCount} permisos`);
                    
                    console.log('✅ Permisos obtenidos exitosamente (getAllPermissions)');
                    console.log('📊 Total de permisos encontrados:', permissionsArray.length);
                } else {
                    console.warn('⚠️ No se pudo encontrar el array de permisos en la respuesta (getAllPermissions)');
                }
            }
            
            // Estructura de respuesta exitosa
            return {
                success: true,
                data: responseData,
                message: 'Permisos obtenidos exitosamente'
            };
            
        } catch (error) {
            console.error('❌ Error de red en PermissionService.getAllPermissions:', error);
            return { 
                success: false, 
                message: 'Error de red al obtener los permisos', 
                error: error.message 
            };
        }
    }

    /**
     * Actualiza un permiso existente
     * @param {Object} permission - Objeto completo del permiso: { id: number, name: string, active: boolean, web: boolean }
     * @returns {Promise<Object>} Respuesta de la API: { success: boolean, message: string, data: { id, name, active, web } }
     */
    static async updatePermission(permission) {
        try {
            console.log('🔄 PermissionService: Actualizando permiso...', permission);
            const token = PermissionService.getAuthToken();
            console.log('🔑 Token disponible:', token ? 'Sí' : 'No');
            console.log('🌐 Haciendo petición a:', `${PermissionService.API_BASE_URL}/permissions/${permission.id}`);
            console.log('📋 Datos enviados:', JSON.stringify(permission, null, 2));
            
            const response = await fetch(`${PermissionService.API_BASE_URL}/permissions/${permission.id}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    ...(token ? { 'Authorization': `Bearer ${token}` } : {})
                },
                body: JSON.stringify(permission)
            });
            
            console.log('📡 Respuesta HTTP status:', response.status);
            console.log('📡 Headers de respuesta:', Object.fromEntries(response.headers.entries()));
            
            const result = await response.json();
            console.log('📋 Respuesta parseada:', result);
            
            // Verificar si la respuesta HTTP fue exitosa
            if (!response.ok) {
                console.error('❌ Error HTTP:', response.status, response.statusText);
                return { 
                    success: false, 
                    message: result.message || `Error HTTP ${response.status}: ${response.statusText}`,
                    error: result 
                };
            }
            
            // Si no hay campo success explícito y el status HTTP es exitoso, considerarlo exitoso
            if (result.success === undefined && response.ok) {
                return { ...result, success: true };
            }
            
            return result;
        } catch (error) {
            console.error('❌ Error de red en PermissionService.updatePermission:', error);
            return { success: false, message: 'Error de red', error };
        }
    }

    /**
     * Elimina un permiso (desactivación lógica)
     * Endpoint: DELETE /api/v1/permissions/{id}
     * Respuestas de la API:
     * - Éxito: HTTP 200 (sin body o body vacío)
     * - Error con roles asignados: HTTP 500 con mensaje específico
     * @param {number} permissionId - ID del permiso
     * @returns {Promise<Object>} Respuesta de la API: { success: boolean, message: string }
     */
    static async deletePermission(permissionId) {
        try {
            console.log('🔄 PermissionService: Eliminando permiso ID:', permissionId);
            const token = PermissionService.getAuthToken();
            console.log('🔑 Token disponible:', token ? 'Sí' : 'No');
            console.log('🌐 Haciendo petición a:', `${PermissionService.API_BASE_URL}/permissions/${permissionId}`);
            
            const response = await fetch(`${PermissionService.API_BASE_URL}/permissions/${permissionId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    ...(token ? { 'Authorization': `Bearer ${token}` } : {})
                },
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
                console.log('✅ Permiso eliminado exitosamente');
                return {
                    success: true,
                    message: 'Permiso eliminado correctamente'
                };
            }
            
            // Manejar errores HTTP específicos
            let errorMessage = 'Error desconocido';
            
            switch (response.status) {
                case 404:
                    errorMessage = (result && result.message) || 'Permiso no encontrado';
                    console.error('❌ Error 404: Permiso no encontrado');
                    break;
                case 409:
                    errorMessage = (result && result.message) || 'No se puede eliminar: El permiso está en uso por uno o más roles';
                    console.error('❌ Error 409: Conflicto - Permiso en uso por roles');
                    break;
                case 500:
                    // Detectar el caso específico de roles asignados
                    if (result && result.error && result.error.message) {
                        if (result.error.message.includes('roles asignados') || result.error.message.includes('en uso')) {
                            errorMessage = 'No se puede eliminar el permiso porque está asignado a uno o más roles';
                            console.error('❌ Error 500: Permiso con roles asignados');
                        } else {
                            errorMessage = result.error.message;
                            console.error('❌ Error 500: Error interno del servidor');
                        }
                    } else if (result && result.message) {
                        if (result.message.includes('roles asignados') || result.message.includes('en uso')) {
                            errorMessage = 'No se puede eliminar el permiso porque está asignado a uno o más roles';
                            console.error('❌ Error 500: Permiso con roles asignados');
                        } else {
                            errorMessage = result.message;
                            console.error('❌ Error 500: Error interno del servidor');
                        }
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
                    errorMessage = (result && result.message) || 'Acceso denegado - Sin permisos para eliminar';
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
            console.error('❌ Error de red en PermissionService.deletePermission:', error);
            return { 
                success: false, 
                message: 'Error de red al eliminar el permiso', 
                error: error.message 
            };
        }
    }

    /**
     * Busca permisos por nombre
     * @param {string} searchTerm - Término de búsqueda por nombre
     * @param {number} page - Número de página (default: 1)
     * @param {number} limit - Cantidad de elementos por página (default: 20)
     * @param {boolean|null} webOnly - Filtrar solo permisos web (null = no filtrar, true = solo web, false = solo app)
     * @param {boolean|null} activeOnly - Filtrar solo permisos activos (null = no filtrar, true = solo activos, false = solo inactivos)
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async searchPermissions(searchTerm, page = 1, limit = 20, webOnly = null, activeOnly = null) {
        try {
            console.log(`🔍 PermissionService: Buscando permisos - término: "${searchTerm}", página ${page}, límite ${limit}`);
            console.log(`🔧 Filtros aplicados: webOnly=${webOnly}, activeOnly=${activeOnly}`);
            
            // Usar el método getPermissions que ya tiene toda la lógica actualizada
            return await PermissionService.getPermissions(page, limit, searchTerm, webOnly, activeOnly);
        } catch (error) {
            console.error('❌ Error de red en PermissionService.searchPermissions:', error);
            return { success: false, message: 'Error de red', error };
        }
    }
}

// Para compatibilidad global
if (typeof window !== 'undefined') {
    window.PermissionService = PermissionService;
}

console.log('✅ PermissionService cargado y disponible globalmente');
