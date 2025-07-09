/**
 * Servicio para la gestión de usuarios
 * Maneja todas las operaciones CRUD de usuarios con filtros avanzados
 */

class UserService {
    static get API_BASE_URL() {
        return 'https://149.130.161.148/api/v1';
    }

    static getAuthToken() {
        // Compatibilidad: busca 'token' o 'authToken'
        return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
    }

    /**
     * Obtiene la lista de usuarios con paginación y filtros avanzados
     * @param {number} page - Número de página (default: 1)
     * @param {number} limit - Cantidad de elementos por página (default: 20)
     * @param {string} search - Término de búsqueda por nombre, documento o email (opcional)
     * @param {string|null} userType - Tipo de usuario: 'citizen', 'driver', 'admin' (opcional)
     * @param {string|null} driverStatus - Estado del conductor: 'PENDIENTE', 'APROBADO', 'RECHAZADO' (opcional)
     * @param {boolean|null} hasVehicle - Filtrar usuarios con vehículo asociado (opcional)
     * @param {boolean|null} contactVerified - Filtrar usuarios con contactos verificados (opcional)
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async getUsers(page = 1, limit = 20, search = null, userType = null, driverStatus = null, hasVehicle = null, contactVerified = null) {
        try {
            console.log(`🔄 UserService: Obteniendo usuarios - página ${page}, límite ${limit}`);
            if (search) console.log(`🔍 Filtro de búsqueda: "${search}"`);
            console.log(`🔧 Filtros: userType=${userType}, driverStatus=${driverStatus}, hasVehicle=${hasVehicle}, contactVerified=${contactVerified}`);
            
            const token = UserService.getAuthToken();
            
            // Construir URL con parámetros
            const url = new URL(`${UserService.API_BASE_URL}/users`);
            
            // Parámetros obligatorios
            url.searchParams.append('page', page.toString());
            url.searchParams.append('limit', limit.toString());
            
            // Parámetros opcionales
            if (search && search.trim()) {
                url.searchParams.append('search', search.trim());
                console.log(`🔍 Parámetro de búsqueda agregado a URL: "${search.trim()}"`);
            }
            
            // Filtros específicos - solo agregar si no son null
            if (userType !== null) {
                url.searchParams.append('userType', userType);
            }
            if (driverStatus !== null) {
                url.searchParams.append('driverStatus', driverStatus);
            }
            if (hasVehicle !== null) {
                url.searchParams.append('hasVehicle', hasVehicle.toString());
            }
            if (contactVerified !== null) {
                url.searchParams.append('contactVerified', contactVerified.toString());
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
                let usersArray = null;
                
                if (responseData.data.users) {
                    // Estructura nueva: data.users
                    usersArray = responseData.data.users;
                    console.log('🏗️ Estructura detectada: data.users (nueva API)');
                } else if (responseData.data.data && responseData.data.data.items) {
                    // Estructura: data.data.items
                    usersArray = responseData.data.data.items;
                    console.log('🏗️ Estructura detectada: data.data.items');
                } else if (responseData.data.items) {
                    // Estructura: data.items
                    usersArray = responseData.data.items;
                    console.log('🏗️ Estructura detectada: data.items');
                } else if (Array.isArray(responseData.data)) {
                    // Estructura: data (array directo)
                    usersArray = responseData.data;
                    console.log('🏗️ Estructura detectada: data (array directo)');
                }
                
                if (usersArray && Array.isArray(usersArray)) {
                    console.log(`🔍 ANÁLISIS DE USUARIOS (${usersArray.length} usuarios encontrados):`);
                    
                    let citizenCount = 0;
                    let driverCount = 0;
                    let adminCount = 0;
                    let withVehicleCount = 0;
                    let verifiedContactCount = 0;
                    
                    usersArray.forEach((user, index) => {
                        const userRoles = user.roles || [];
                        const hasVehicle = user.vehicle !== null;
                        const hasVerifiedContact = user.contacts && user.contacts.some(contact => contact.confirmed);
                        
                        console.log(`   Usuario ${index + 1}: "${user.person?.name} ${user.person?.lastName}" - ID: ${user.id}`);
                        console.log(`     - Roles: [${userRoles.map(r => r.name).join(', ')}]`);
                        console.log(`     - Vehículo: ${hasVehicle ? 'Sí' : 'No'}`);
                        console.log(`     - Contacto verificado: ${hasVerifiedContact ? 'Sí' : 'No'}`);
                        console.log(`     - Estado: ${user.status?.name || 'N/A'}`);
                        
                        // 🔍 LOG DETALLADO DEL DOCUMENTO
                        console.log(`     - 📄 Documento:`);
                        console.log(`       • documentNumber: "${user.person?.documentNumber}" (tipo: ${typeof user.person?.documentNumber})`);
                        console.log(`       • documentType: "${user.person?.documentType}" (tipo: ${typeof user.person?.documentType})`);
                        console.log(`       • person completo:`, user.person);
                        
                        // Contar tipos de usuario
                        if (userRoles.some(r => r.name === 'CIUDADANO')) citizenCount++;
                        if (userRoles.some(r => r.name === 'CONDUCTOR')) driverCount++;
                        if (userRoles.some(r => r.name === 'ADMINISTRADOR')) adminCount++;
                        
                        if (hasVehicle) withVehicleCount++;
                        if (hasVerifiedContact) verifiedContactCount++;
                    });
                    
                    console.log('📊 RESUMEN DE USUARIOS:');
                    console.log(`   👥 Ciudadanos: ${citizenCount}`);
                    console.log(`   🚗 Conductores: ${driverCount}`);
                    console.log(`   👨‍💼 Administradores: ${adminCount}`);
                    console.log(`   🚙 Con vehículo: ${withVehicleCount}`);
                    console.log(`   ✅ Contacto verificado: ${verifiedContactCount}`);
                    
                } else {
                    console.warn('⚠️ No se pudo encontrar el array de usuarios en la respuesta');
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
            console.error('❌ Error de red en UserService.getUsers:', error);
            return { success: false, message: 'Error de red', error };
        }
    }

    /**
     * Busca usuarios por nombre, documento o email
     * @param {string} searchTerm - Término de búsqueda
     * @param {number} page - Número de página (default: 1)
     * @param {number} limit - Cantidad de elementos por página (default: 20)
     * @param {string|null} userType - Tipo de usuario: 'citizen', 'driver', 'admin' (opcional)
     * @param {string|null} driverStatus - Estado del conductor (opcional)
     * @param {boolean|null} hasVehicle - Filtrar usuarios con vehículo (opcional)
     * @param {boolean|null} contactVerified - Filtrar usuarios con contactos verificados (opcional)
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async searchUsers(searchTerm, page = 1, limit = 20, userType = null, driverStatus = null, hasVehicle = null, contactVerified = null) {
        try {
            console.log(`🔍 UserService: Buscando usuarios - término: "${searchTerm}", página ${page}, límite ${limit}`);
            console.log(`🔧 Filtros aplicados: userType=${userType}, driverStatus=${driverStatus}, hasVehicle=${hasVehicle}, contactVerified=${contactVerified}`);
            
            // Usar el método getUsers que ya tiene toda la lógica actualizada
            return await UserService.getUsers(page, limit, searchTerm, userType, driverStatus, hasVehicle, contactVerified);
        } catch (error) {
            console.error('❌ Error de red en UserService.searchUsers:', error);
            return { success: false, message: 'Error de red', error };
        }
    }

    /**
     * Obtiene los detalles de un usuario específico junto con todos sus roles asignados
     * @param {number} userId - ID del usuario
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async getUserWithRoles(userId) {
        try {
            console.log(`🔄 UserService: Obteniendo detalles del usuario ${userId} con roles`);
            
            const token = UserService.getAuthToken();
            
            const response = await fetch(`${UserService.API_BASE_URL}/users/${userId}/roles`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });
            
            console.log('📡 Status de respuesta:', response.status);
            
            if (!response.ok) {
                if (response.status === 404) {
                    console.error('❌ Usuario no encontrado');
                    return { success: false, message: 'Usuario no encontrado' };
                }
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const responseData = await response.json();
            console.log('✅ Detalles del usuario obtenidos exitosamente:', responseData);
            console.log('🔍 Estructura de datos:', {
                hasSuccess: 'success' in responseData,
                hasData: 'data' in responseData,
                hasUser: responseData.data && 'user' in responseData.data,
                hasRoles: responseData.data && 'roles' in responseData.data,
                dataKeys: responseData.data ? Object.keys(responseData.data) : 'N/A'
            });
            
            return responseData;
        } catch (error) {
            console.error('❌ Error al obtener detalles del usuario:', error);
            return { success: false, message: 'Error de red', error };
        }
    }

    /**
     * Asigna o desasigna roles a un usuario
     * @param {number} userId - ID del usuario
     * @param {Array<number>} roleIds - Array de IDs de roles a asignar
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async updateUserRoles(userId, roleIds) {
        try {
            console.log(`🔄 UserService: Actualizando roles del usuario ${userId}`, roleIds);
            
            const token = UserService.getAuthToken();
            
            const response = await fetch(`${UserService.API_BASE_URL}/users/${userId}/roles`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({ roleIds })
            });
            
            console.log('📡 Status de respuesta:', response.status);
            
            if (!response.ok) {
                let errorMessage = `Error HTTP ${response.status}: ${response.statusText}`;
                
                if (response.status === 404) {
                    errorMessage = 'Usuario no encontrado';
                } else if (response.status === 403) {
                    errorMessage = 'Sin permisos para actualizar roles del usuario';
                } else if (response.status === 401) {
                    errorMessage = 'No autorizado - Token inválido';
                }
                
                console.error('❌ Error al actualizar roles:', errorMessage);
                return { success: false, message: errorMessage };
            }
            
            // La API no devuelve contenido, solo confirmar éxito
            console.log('✅ Roles del usuario actualizados exitosamente');
            return { success: true, message: 'Roles actualizados correctamente' };
        } catch (error) {
            console.error('❌ Error al actualizar roles del usuario:', error);
            return { success: false, message: 'Error de red', error };
        }
    }

    /**
     * Cambia el estado de un usuario (activo/inactivo)
     * @param {number} userId - ID del usuario
     * @param {number} statusId - ID del estado (1 = activo, 2 = inactivo)
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async updateUserStatus(userId, statusId) {
        try {
            console.log(`🔄 UserService: Cambiando estado del usuario ${userId} a statusId: ${statusId}`);
            
            const token = UserService.getAuthToken();
            
            const response = await fetch(`${UserService.API_BASE_URL}/users/${userId}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({ statusId })
            });
            
            console.log('📡 Status de respuesta:', response.status);
            
            if (!response.ok) {
                let errorMessage = `Error HTTP ${response.status}: ${response.statusText}`;
                
                // Intentar obtener más detalles del error del servidor
                let errorDetails = null;
                try {
                    errorDetails = await response.text();
                    if (errorDetails) {
                        console.log('📝 Detalles del error del servidor:', errorDetails);
                        // Si hay detalles JSON, intentar parsearlos
                        try {
                            const jsonError = JSON.parse(errorDetails);
                            if (jsonError.message) {
                                errorMessage += ` - ${jsonError.message}`;
                            }
                        } catch (e) {
                            // Si no es JSON válido, agregar como texto
                            if (errorDetails.length < 200) { // Solo si no es muy largo
                                errorMessage += ` - ${errorDetails}`;
                            }
                        }
                    }
                } catch (e) {
                    console.log('📝 No se pudieron obtener detalles del error');
                }
                
                if (response.status === 404) {
                    errorMessage = 'Usuario no encontrado';
                } else if (response.status === 403) {
                    errorMessage = 'Sin permisos para cambiar el estado del usuario';
                } else if (response.status === 401) {
                    errorMessage = 'No autorizado - Token inválido';
                } else if (response.status === 500) {
                    // Verificar si es un error específico de que el usuario ya tiene el estado
                    if (errorDetails && errorDetails.includes('"El usuario ya tiene el estado:')) {
                        console.log('ℹ️ El usuario ya tiene el estado solicitado - tratando como éxito');
                        
                        // Extraer el estado actual del mensaje de error
                        try {
                            const errorJson = JSON.parse(errorDetails);
                            const currentState = errorJson.error?.message || '';
                            console.log('📊 Estado actual según backend:', currentState);
                            
                            // Si el usuario ya está inactivo y estamos verificando al usuario actual
                            const currentUserId = sessionStorage.getItem('userId');
                            if (currentUserId && userId.toString() === currentUserId.toString() && 
                                currentState.includes('INACTIVO')) {
                                console.log('🚨 El usuario actual ya estaba inactivo - forzando logout');
                                
                                // Retornar éxito pero activar verificación inmediata
                                setTimeout(() => {
                                    if (typeof window.UserService !== 'undefined' && 
                                        typeof window.UserService.forceUserStatusCheck === 'function') {
                                        window.UserService.forceUserStatusCheck(500); // Verificar rápidamente
                                    }
                                }, 100);
                                
                                return { 
                                    success: true, 
                                    message: 'Usuario ya estaba inactivo - aplicando logout',
                                    alreadyInactive: true 
                                };
                            }
                            
                            // Para otros casos, tratar como éxito
                            return { 
                                success: true, 
                                message: 'El estado ya estaba aplicado',
                                alreadySet: true 
                            };
                        } catch (e) {
                            console.log('⚠️ No se pudo parsear detalles del error de estado duplicado');
                        }
                    }
                    
                    errorMessage = 'Error interno del servidor al cambiar estado del usuario';
                    console.error('🚨 Error 500 al cambiar estado - Posibles causas:', {
                        userId,
                        statusId,
                        endpoint: `${UserService.API_BASE_URL}/users/${userId}/status`,
                        details: errorDetails
                    });
                }
                
                console.error('❌ Error al cambiar estado:', errorMessage);
                return { success: false, message: errorMessage, httpStatus: response.status };
            }
            
            // Si la API no devuelve contenido (204 No Content o respuesta vacía)
            let responseData = { success: true, message: 'Estado actualizado correctamente' };
            
            // Intentar leer el contenido de la respuesta si existe
            const contentLength = response.headers.get('content-length');
            if (contentLength && contentLength !== '0') {
                try {
                    responseData = await response.json();
                    console.log('📋 Respuesta completa del cambio de estado:', responseData);
                    
                    // Verificar si la respuesta incluye el usuario actualizado
                    if (responseData.data && responseData.data.user) {
                        console.log('👤 Usuario actualizado en respuesta:', responseData.data.user);
                        console.log('📊 Estado del usuario en respuesta:', responseData.data.user.userStatus || responseData.data.user.status);
                    }
                } catch (e) {
                    // Si no puede parsear JSON, usar respuesta por defecto
                    console.log('📝 Respuesta sin contenido JSON, usando respuesta por defecto');
                }
            } else {
                console.log('📝 Respuesta sin contenido (204 No Content)');
            }
            
            console.log('✅ Estado del usuario actualizado exitosamente');
            return responseData;
        } catch (error) {
            console.error('❌ Error al cambiar estado del usuario:', error);
            return { success: false, message: 'Error de red', error };
        }
    }

    /**
     * Crea un nuevo usuario en el sistema
     * @param {Object} userData - Datos del usuario a crear
     * @param {string} userData.document - Documento de identidad
     * @param {string} userData.email - Correo electrónico
     * @param {string} userData.password - Contraseña
     * @param {string} userData.area - Área de trabajo (opcional)
     * @param {string} userData.position - Posición/cargo (opcional)
     * @returns {Promise<Object>} Respuesta de la API
     */
    static async createUser(userData) {
        try {
            console.log('🔄 UserService: Creando nuevo usuario para documento:', userData.document);
            
            const token = UserService.getAuthToken();
            if (!token) {
                throw new Error('Token de autenticación no disponible');
            }
            
            // Validar campos obligatorios
            const requiredFields = ['document', 'email', 'password'];
            for (const field of requiredFields) {
                if (!userData[field] || !userData[field].toString().trim()) {
                    throw new Error(`El campo ${field} es obligatorio`);
                }
            }
            
            // Validar formato de email
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(userData.email)) {
                throw new Error('El formato del correo electrónico no es válido');
            }
            
            // Validar longitud de contraseña
            if (userData.password.length < 6) {
                throw new Error('La contraseña debe tener al menos 6 caracteres');
            }
            
            // Validar complejidad de contraseña
            const passwordRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/;
            if (!passwordRegex.test(userData.password)) {
                throw new Error('La contraseña debe contener al menos: 1 mayúscula, 1 número y 1 carácter especial (@$!%*?&)');
            }
            
            // Preparar datos para envío según la nueva API
            const requestData = {
                document: userData.document.trim(),
                email: userData.email.trim(),
                password: userData.password,
                area: userData.area ? userData.area.trim() : '',
                position: userData.position ? userData.position.trim() : ''
            };
            
            console.log('📤 Enviando datos del usuario:', {
                ...requestData,
                password: '[OCULTA]' // No mostrar contraseña en logs
            });
            
            const response = await fetch(`${UserService.API_BASE_URL}/users`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(requestData)
            });
            
            console.log('📡 Respuesta del servidor:', response.status, response.statusText);
            
            if (!response.ok) {
                let errorMessage = `Error HTTP: ${response.status}`;
                
                try {
                    const errorData = await response.json();
                    errorMessage = errorData.message || errorMessage;
                    console.log('📄 Detalles del error:', errorData);
                } catch (e) {
                    console.log('⚠️ No se pudo parsear respuesta de error');
                }
                
                throw new Error(errorMessage);
            }
            
            const responseData = await response.json();
            console.log('✅ Usuario creado exitosamente:', responseData);
            
            // Extraer información del usuario creado para logs detallados
            if (responseData.data && responseData.data.user) {
                console.log('👤 Usuario creado - Detalles:', {
                    id: responseData.data.user.id,
                    nombre: `${responseData.data.user.firstName} ${responseData.data.user.lastName}`,
                    documento: responseData.data.user.document,
                    email: responseData.data.user.email,
                    rol: responseData.data.user.role,
                    area: responseData.data.user.area,
                    posicion: responseData.data.user.position
                });
            }
            
            return {
                success: true,
                message: responseData.data?.message || responseData.message || 'Usuario creado exitosamente',
                data: responseData.data,
                user: responseData.data?.user // Incluir datos específicos del usuario
            };
            
        } catch (error) {
            console.error('❌ Error al crear usuario:', error);
            
            return {
                success: false,
                message: error.message || 'Error desconocido al crear usuario',
                error: error
            };
        }
    }

    /**
     * Verifica el estado actual del usuario autenticado
     * Útil para verificar si el usuario sigue activo en el sistema
     * @returns {Promise<Object>} Respuesta con el estado del usuario
     */
    static async getCurrentUserStatus() {
        try {
            const token = UserService.getAuthToken();
            const userId = sessionStorage.getItem('userId');
            
            if (!token || !userId) {
                console.log('🔍 UserService: No hay token o userId en sesión');
                return { success: false, message: 'No hay sesión activa', needsLogin: true };
            }
            
            console.log(`🔍 UserService: Verificando estado del usuario actual ${userId}`);
            
            const response = await fetch(`${UserService.API_BASE_URL}/users/${userId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });
            
            console.log('📡 Status de verificación de usuario:', response.status);
            
            if (!response.ok) {
                if (response.status === 401) {
                    console.log('🔒 Token inválido o expirado');
                    return { success: false, message: 'Token inválido', needsLogin: true };
                } else if (response.status === 404) {
                    console.log('👤 Usuario no encontrado');
                    return { success: false, message: 'Usuario no encontrado', needsLogin: true };
                } else {
                    console.log(`❌ Error ${response.status} al verificar usuario`);
                    return { success: false, message: `Error ${response.status}`, needsLogin: false };
                }
            }
            
            const userData = await response.json();
            console.log('📋 Datos del usuario actual:', userData);
            
            // Verificar múltiples ubicaciones donde puede estar el estado del usuario
            let userActive = true; // Por defecto activo
            
            console.log(`🔍 ANÁLISIS COMPLETO DE ESTRUCTURA:`);
            console.log(`   - userData completo:`, userData);
            console.log(`   - userData.data:`, userData.data);
            console.log(`   - userData.data.userId: ${userData.data?.userId}`);
            console.log(`   - userData.data.person:`, userData.data?.person);
            console.log(`   - userData.data.userStatus:`, userData.data?.userStatus);
            
            // COHERENCIA: Usar status.id para control de acceso (igual que para badge)
            // Verificar diferentes posibles ubicaciones del estado
            if (userData.data?.status) {
                // Estructura con status directo (preferido)
                const statusId = userData.data.status.id;
                const statusName = userData.data.status.name;
                userActive = statusId === 1 || statusName?.toLowerCase() === 'activo';
                console.log(`   📊 Estado desde status: id=${statusId}, name="${statusName}", activo=${userActive}`);
            } else if (userData.data?.userStatus) {
                // Estructura con userStatus
                const statusId = userData.data.userStatus.id;
                const statusName = userData.data.userStatus.name;
                userActive = statusId === 1 || statusName?.toLowerCase() === 'activo';
                console.log(`   📊 Estado desde userStatus: id=${statusId}, name="${statusName}", activo=${userActive}`);
            } else if (userData.data?.person?.active !== undefined) {
                // Fallback: Estructura con person.active (deprecated)
                userActive = userData.data.person.active === true;
                console.log(`   📊 Estado desde person.active (fallback): ${userData.data.person.active}, activo=${userActive}`);
            } else {
                console.log(`   ⚠️ No se encontró información de estado, asumiendo activo`);
            }
            
            console.log(`🔍 DETALLE DE VERIFICACIÓN:`);
            console.log(`   - Usuario ID: ${userData.data?.userId || userData.data?.id}`);
            console.log(`   - Nombre: ${userData.data?.person?.name} ${userData.data?.person?.lastName}`);
            console.log(`   - Estado final calculado: ${userActive}`);
            
            // Solo desloguear si el usuario está INACTIVO
            // Los permisos y roles se manejan en auth-permissions.js
            if (!userActive) {
                console.log('🚫 Usuario desactivado por administrador - requiere login');
                return { 
                    success: false, 
                    message: 'Usuario desactivado por administrador', 
                    needsLogin: true,
                    userDeactivated: true
                };
            }
            
            console.log('✅ Usuario activo - acceso permitido');
            return { 
                success: true, 
                message: 'Usuario activo', 
                data: userData.data,
                userActive: true
            };
            
        } catch (error) {
            console.error('❌ Error al verificar estado del usuario:', error);
            return { success: false, message: 'Error de red', needsLogin: false };
        }
    }

    /**
     * Fuerza una verificación inmediata del estado del usuario actual
     * Útil para verificar el estado después de cambios administrativos
     * @param {number} delayMs - Delay en milisegundos antes de verificar (para esperar actualización del backend)
     * @returns {Promise<Object>} Resultado de la verificación
     */
    static async forceUserStatusCheck(delayMs = 1500) {
        console.log(`🔍 UserService: Forzando verificación del estado del usuario (esperando ${delayMs}ms)...`);
        
        try {
            // Esperar un poco para que el backend procese el cambio
            if (delayMs > 0) {
                console.log(`⏳ Esperando ${delayMs}ms para que el backend actualice el estado...`);
                await new Promise(resolve => setTimeout(resolve, delayMs));
            }
            
            // Intentar verificar hasta 3 veces con intervalos
            let attempts = 0;
            let maxAttempts = 3;
            let statusResult;
            
            while (attempts < maxAttempts) {
                attempts++;
                console.log(`🔄 Intento ${attempts}/${maxAttempts} de verificación de estado...`);
                
                statusResult = await UserService.getCurrentUserStatus();
                
                // Si encontramos que el usuario está inactivo, proceder inmediatamente
                if (statusResult.needsLogin || statusResult.userDeactivated) {
                    console.log(`✅ Usuario detectado como inactivo en intento ${attempts}`);
                    break;
                }
                
                // Si aún está activo y no es el último intento, esperar un poco más
                if (attempts < maxAttempts) {
                    console.log(`⏳ Usuario aún activo en intento ${attempts}, esperando 1s más...`);
                    await new Promise(resolve => setTimeout(resolve, 1000));
                }
            }
            
            if (statusResult.needsLogin || statusResult.userDeactivated) {
                console.log('🚫 Usuario requiere login - iniciando proceso de cierre de sesión...');
                
                // Activar el manejador de usuario desactivado inmediatamente
                if (window.authChecker && window.authChecker.userStatusMonitor) {
                    window.authChecker.userStatusMonitor.handleUserDeactivated(
                        statusResult.message || 'Usuario desactivado'
                    );
                } else {
                    // Fallback manual si el monitor no está disponible
                    console.log('⚠️ Monitor no disponible, ejecutando fallback manual...');
                    
                    // Mostrar mensaje con color celeste específico para desactivación
                    if (typeof window.showToast === 'function') {
                        window.showToast('Tu cuenta ha sido desactivada. Serás redirigido al login.', 'deactivated');
                    } else if (typeof window.GlobalToast !== 'undefined' && window.GlobalToast.show) {
                        window.GlobalToast.show('Tu cuenta ha sido desactivada. Serás redirigido al login.', 'deactivated');
                    } else {
                        // Usar el sistema de toast del controlador si está disponible
                        const usersController = window.usersController || window.UsersController;
                        if (usersController && typeof usersController.showToast === 'function') {
                            usersController.showToast('Tu cuenta ha sido desactivada. Serás redirigido al login.', 'warning');
                        } else {
                            console.log('⚠️ No hay sistema de notificaciones disponible - usando console');
                            console.warn('NOTIFICACIÓN: Tu cuenta ha sido desactivada. Serás redirigido al login.');
                        }
                    }
                    
                    // Limpiar sesión y redirigir
                    setTimeout(() => {
                        if (typeof cleanSession === 'function') {
                            cleanSession();
                        }
                        window.location.href = "../../index.html";
                    }, 2000);
                }
                
                return { needsRedirect: true, message: statusResult.message };
            }
            
            console.log(`⚠️ Usuario sigue activo después de ${maxAttempts} intentos`);
            return { needsRedirect: false, message: 'Usuario sigue activo' };
            
        } catch (error) {
            console.error('❌ Error en verificación forzada:', error);
            return { needsRedirect: false, message: 'Error en verificación', error };
        }
    }

    /**
     * Verificación ligera del estado del usuario (solo verifica si está activo)
     * No verifica roles para evitar logout innecesario durante el uso normal
     * @returns {Promise<Object>} Respuesta con el estado del usuario
     */
    static async getCurrentUserStatusLight() {
        try {
            const token = UserService.getAuthToken();
            const userId = sessionStorage.getItem('userId');
            
            if (!token || !userId) {
                console.log('🔍 UserService: No hay token o userId en sesión');
                return { success: false, message: 'No hay sesión activa', needsLogin: true };
            }
            
            console.log(`🔍 UserService: Verificación ligera del usuario ${userId} - ${new Date().toLocaleTimeString()}`);
            
            const response = await fetch(`${UserService.API_BASE_URL}/users/${userId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });
            
            console.log('📡 Status de verificación ligera:', response.status);
            
            if (!response.ok) {
                if (response.status === 401) {
                    console.log('🔒 Token inválido o expirado');
                    return { success: false, message: 'Token inválido', needsLogin: true };
                } else if (response.status === 404) {
                    console.log('👤 Usuario no encontrado');
                    return { success: false, message: 'Usuario no encontrado', needsLogin: true };
                } else {
                    console.log(`❌ Error ${response.status} al verificar usuario`);
                    return { success: false, message: `Error ${response.status}`, needsLogin: false };
                }
            }
            
            const userData = await response.json();
            
            // Solo verificar si el usuario está activo (no verificar roles)
            // CORRECCIÓN: Usar status.id para consistencia, como en getCurrentUserStatus
            let userActive = true; // Por defecto activo
            
            console.log(`🔍 VERIFICACIÓN LIGERA DETALLADA:`);
            console.log(`   - Usuario ID: ${userId}`);
            console.log(`   - Timestamp: ${new Date().toLocaleTimeString()}`);
            console.log(`   - userData.data:`, userData.data);
            console.log(`   - userData.data.status:`, userData.data?.status);
            console.log(`   - userData.data.person:`, userData.data?.person);
            
            // COHERENCIA: Usar status.id para control de acceso (igual que para badge)
            // Verificar diferentes posibles ubicaciones del estado
            if (userData.data?.status) {
                // Estructura con status directo (preferido)
                const statusId = userData.data.status.id;
                const statusName = userData.data.status.name;
                userActive = statusId === 1 || statusName?.toLowerCase() === 'activo';
                console.log(`   📊 Estado desde status: id=${statusId}, name="${statusName}", activo=${userActive}`);
            } else if (userData.data?.userStatus) {
                // Estructura con userStatus
                const statusId = userData.data.userStatus.id;
                const statusName = userData.data.userStatus.name;
                userActive = statusId === 1 || statusName?.toLowerCase() === 'activo';
                console.log(`   📊 Estado desde userStatus: id=${statusId}, name="${statusName}", activo=${userActive}`);
            } else if (userData.data?.person?.active !== undefined) {
                // Fallback: Estructura con person.active (deprecated)
                userActive = userData.data.person.active === true;
                console.log(`   📊 Estado desde person.active (fallback): ${userData.data.person.active}, activo=${userActive}`);
            } else {
                console.log(`   ⚠️ No se encontró información de estado, asumiendo activo`);
            }
            
            console.log(`🔍 VERIFICACIÓN LIGERA - Usuario activo: ${userActive}`);
            
            // Solo desloguear si el usuario está completamente inactivo
            if (!userActive) {
                console.log('🚫 Usuario desactivado - requiere login');
                return { 
                    success: false, 
                    message: 'Usuario desactivado por administrador', 
                    needsLogin: true,
                    userDeactivated: true
                };
            }
            
            console.log('✅ Usuario activo - continuando sesión');
            return { 
                success: true, 
                message: 'Usuario activo', 
                data: userData.data,
                userActive: true
            };
            
        } catch (error) {
            console.error('❌ Error en verificación ligera:', error);
            return { success: false, message: 'Error de red', needsLogin: false };
        }
    }

    /**
     * Obtiene los detalles completos de un usuario específico
     * @param {number} userId - ID del usuario
     * @returns {Promise<Object>} Respuesta de la API con detalles completos
     */
    static async getUserFullDetails(userId) {
        try {
            console.log(`🔄 UserService: Obteniendo detalles completos del usuario ${userId}`);
            
            const token = UserService.getAuthToken();
            if (!token) {
                console.error('❌ No hay token de autenticación');
                return { success: false, message: 'No autorizado' };
            }

            const response = await fetch(`${UserService.API_BASE_URL}/users/${userId}`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });

            console.log(`📡 Respuesta del servidor para usuario ${userId}:`, response.status);

            if (!response.ok) {
                if (response.status === 401) {
                    console.error('🚫 Token expirado o inválido');
                    return { success: false, message: 'Sesión expirada', needsLogin: true };
                }
                
                if (response.status === 404) {
                    console.error('🚫 Usuario no encontrado');
                    return { success: false, message: 'Usuario no encontrado' };
                }
                
                const errorText = await response.text();
                console.error('❌ Error HTTP:', response.status, errorText);
                return { success: false, message: `Error del servidor: ${response.status}` };
            }

            const data = await response.json();
            console.log('✅ Detalles del usuario obtenidos exitosamente:', {
                userId: userId,
                hasVehicle: !!data.data?.vehicle,
                vehiclePlate: data.data?.vehicle?.plate || 'No disponible',
                rolesCount: data.data?.roles?.length || 0,
                contactsCount: data.data?.contacts?.length || 0
            });

            return {
                success: true,
                data: data.data,
                message: data.message || 'Detalles obtenidos exitosamente'
            };

        } catch (error) {
            console.error('❌ Error al obtener detalles del usuario:', error);
            return { 
                success: false, 
                message: error.message || 'Error de conexión',
                needsLogin: false 
            };
        }
    }
}

// Para compatibilidad global
if (typeof window !== 'undefined') {
    window.UserService = UserService;
}

console.log('✅ UserService cargado y disponible globalmente');
