// Controlador dedicado para los detalles de usuarios y gestión de roles
// Se encarga de toda la lógica de gestión de roles de usuarios: carga, filtrado, renderizado y guardado

const UserDetailsController = (() => {
    // Variables de estado
    let currentUserId = null;
    let currentUserName = '';
    let rolesData = null; // Datos completos de todos los roles
    let filteredRoles = [];
    let showOnlyAssigned = false; // Variable para el filtro de asignados

    /**
     * Inicializa el controlador y configura eventos del modal de asignación de roles
     */
    function init() {
        console.log('🚀 UserDetailsController inicializado');
        setupModalEvents();
    }

    /**
     * Abre el modal de asignación de roles para un usuario específico
     * @param {string|number} userId - ID del usuario
     * @param {string} userName - Nombre del usuario
     * @param {string} userPhone - Teléfono del usuario (opcional)
     * @param {object} userContactIds - ContactIds dinámicos {email: id, phone: id}
     */
    function openRoleAssignmentModal(userId, userName, userPhone = null, userContactIds = null) {
        console.log('🔍 Abriendo modal de asignación de roles para usuario:', { 
            id: userId, 
            name: userName, 
            phone: userPhone,
            contactIds: userContactIds 
        });
        
        currentUserId = userId;
        currentUserName = userName;
        
        // 🔑 ALMACENAR CONTACTIDS DINÁMICOS GLOBALMENTE
        if (userContactIds) {
            window.currentUserContactIds = {
                email: userContactIds.email,
                phone: userContactIds.phone
            };
            console.log('🔑 ContactIds dinámicos almacenados desde parámetros:', window.currentUserContactIds);
        } else {
            // Inicializar con valores null si no se proporcionan
            window.currentUserContactIds = {
                email: null,
                phone: null
            };
            console.log('⚠️ No se proporcionaron contactIds, inicializando con null');
        }
        
        // Guardar el teléfono para usarlo después
        window.currentUserPhone = userPhone;
        
        const modal = document.getElementById('user-edit-modal');
        const titleElement = document.getElementById('user-edit-title');
        
        console.log('🔍 DEBUG - Modal element:', modal);
        console.log('🔍 DEBUG - Title element:', titleElement);
        console.log('🔍 DEBUG - Setting title to:', userName);
        
        if (titleElement) {
            titleElement.textContent = userName;
            console.log('✅ DEBUG - Title set successfully');
        } else {
            console.error('❌ DEBUG - Title element not found!');
        }
        
        if (modal) {
            modal.style.display = 'flex';
            console.log('✅ DEBUG - Modal displayed');
        } else {
            console.error('❌ DEBUG - Modal element not found!');
        }
        
        // Limpiar formulario de búsqueda y filtros
        const searchInput = document.getElementById('search-roles-modal');
        const assignedFilter = document.getElementById('filter-assigned-roles');
        if (searchInput) searchInput.value = '';
        if (assignedFilter) assignedFilter.checked = false;
        showOnlyAssigned = false;
        
        // Cargar roles del usuario
        loadUserRoles();
    }

    /**
     * Cierra el modal de asignación de roles
     */
    function closeRoleAssignmentModal() {
        const modal = document.getElementById('user-edit-modal');
        if (modal) modal.style.display = 'none';
        
        // Limpiar estado
        currentUserId = null;
        currentUserName = '';
        rolesData = null;
        filteredRoles = [];
        showOnlyAssigned = false;
        
        // Limpiar información adicional del usuario
        window.currentUserPhone = null;
        window.currentUserContactIds = null;  // 🔑 LIMPIAR CONTACTIDS
        
        // Restablecer valores por defecto en el modal
        const emailValue = document.getElementById('user-email-value');
        const phoneValue = document.getElementById('user-phone-value');
        const passwordInput = document.getElementById('user-new-password');
        const toggleIcon = document.getElementById('password-toggle-icon');
        
        if (emailValue) emailValue.textContent = 'Cargando...';
        if (phoneValue) phoneValue.textContent = 'Cargando...';
        if (passwordInput) {
            passwordInput.value = ''; // Limpiar el campo de contraseña
            passwordInput.type = 'password'; // Restablecer tipo a password
        }
        if (toggleIcon) {
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye'); // Restablecer icono a ojo cerrado
        }
        
        console.log('✅ Modal de asignación de roles cerrado');
    }

    /**
     * Carga todos los roles del sistema y marca como seleccionados los roles ya asignados al usuario
     */
    async function loadUserRoles() {
        const loadingIndicator = document.getElementById('roles-assignment-loading');
        const rolesList = document.getElementById('roles-assignment-list');
        const noRolesMessage = document.getElementById('no-roles-message');
        
        // Mostrar indicador de carga
        if (loadingIndicator) loadingIndicator.style.display = 'block';
        if (rolesList) rolesList.style.display = 'none';
        if (noRolesMessage) noRolesMessage.style.display = 'none';
        
        try {
            console.log('📋 Cargando todos los roles del sistema para el usuario:', currentUserId);
            
            // Verificar que los servicios estén disponibles
            if (!window.UserService) {
                throw new Error('El servicio de usuarios no está disponible');
            }
            if (!window.RoleService) {
                throw new Error('El servicio de roles no está disponible');
            }
            
            // 1. Obtener TODOS los roles del sistema
            console.log('🌐 Obteniendo todos los roles del sistema...');
            
            // Cargar primera página de roles
            const firstPageResult = await window.RoleService.getRoles(1, 100);
            
            if (!firstPageResult.success) {
                throw new Error(firstPageResult.message || 'Error al obtener los roles del sistema');
            }
            
            // Verificar estructura de respuesta
            if (!firstPageResult.data || !firstPageResult.data.roles) {
                throw new Error('Respuesta sin datos de roles válidos');
            }
            
            // Empezar con los roles de la primera página
            let allRoles = [...firstPageResult.data.roles];
            console.log(`✅ Primera página cargada: ${allRoles.length} roles`);
            
            // Si hay más páginas, cargar todas iterativamente
            const totalPages = firstPageResult.data.totalPages || 1;
            if (totalPages > 1) {
                console.log(`📄 Cargando ${totalPages - 1} páginas adicionales de roles...`);
                
                for (let page = 2; page <= totalPages; page++) {
                    const additionalResponse = await window.RoleService.getRoles(page, 100);
                    if (additionalResponse.success && additionalResponse.data && additionalResponse.data.roles) {
                        allRoles = allRoles.concat(additionalResponse.data.roles);
                        console.log(`✅ Página ${page} cargada, total: ${allRoles.length} roles`);
                    }
                }
            }
            
            // Normalizar estructura de roles del sistema
            const normalizedRoles = allRoles.map(role => ({
                id: role.id,
                name: role.name,
                active: role.active,
                web: role.web,
                type: role.web ? 'Web' : 'App'
            }));
            
            // 2. Obtener información del usuario y sus roles asignados
            console.log('👤 Obteniendo roles asignados al usuario...');
            const userResult = await window.UserService.getUserWithRoles(currentUserId);
            
            if (!userResult.success) {
                throw new Error(userResult.message || 'Error al obtener los datos del usuario');
            }
            
            console.log('🔍 DEBUG - Respuesta completa del usuario:', userResult);
            
            const userData = userResult.data;
            console.log('🔍 DEBUG - userData:', userData);
            
            // Manejar la estructura real de la API
            let userInfo = null;
            let assignedRoles = [];
            
            if (userData && userData.user) {
                // Nueva estructura de la API: { success, message, data: { user: {...}, roles: [...] } }
                userInfo = {
                    id: userData.user.id,
                    firstName: userData.user.firstName,
                    lastName: userData.user.lastName,
                    document: userData.user.document,
                    email: userData.user.email
                };
                assignedRoles = userData.roles || [];
            } else if (userData) {
                // Estructura anterior de fallback: { userId, userName, userLastName, userDocument, userEmail, roles }
                userInfo = {
                    id: userData.userId,
                    firstName: userData.userName,
                    lastName: userData.userLastName,
                    document: userData.userDocument,
                    email: userData.userEmail
                };
                assignedRoles = userData.roles || [];
            } else {
                throw new Error('Estructura de respuesta del usuario no reconocida');
            }
            
            const assignedIds = assignedRoles.map(r => r.id.toString());
            
            console.log(`📊 Usuario: ${userInfo.firstName} ${userInfo.lastName}`);
            console.log(`✅ Email: ${userInfo.email}`);
            console.log(`📄 Documento: ${userInfo.document}`);
            console.log(`✅ Roles asignados: ${assignedRoles.length}`);
            console.log(`📋 Total roles disponibles: ${normalizedRoles.length}`);
            
            // 3. Marcar roles asignados
            const rolesWithAssignment = normalizedRoles.map(role => {
                const isAssigned = assignedIds.includes(role.id.toString());
                return {
                    ...role,
                    assigned: isAssigned,
                    originallyAssigned: isAssigned // Rastrear el estado original
                };
            });
            
            // 4. Determinar el tipo de usuario basado en sus roles asignados
            const userType = determineUserType(assignedRoles);
            console.log(`🎯 Tipo de usuario determinado: ${userType}`);
            
            // 5. MOSTRAR TODOS LOS ROLES - Sin filtrar por tipo de usuario
            console.log(`🔍 Mostrando todos los roles sin filtro: ${rolesWithAssignment.length} roles`);
            
            // 6. Guardar datos en estado global
            rolesData = {
                allRoles: rolesWithAssignment,
                filteredByType: rolesWithAssignment, // Usar todos los roles, no filtrar
                assignedRoles: assignedRoles,
                user: userInfo,
                userType: userType
            };
            
            console.log(`🎯 Roles procesados: ${rolesWithAssignment.length} roles disponibles`);
            console.log(`🔗 Roles asignados marcados: ${rolesWithAssignment.filter(r => r.assigned).length}`);
            console.log(`👤 Mostrando TODOS los roles (web y app): ${rolesWithAssignment.length}`);
            
            // 7. Actualizar información adicional del usuario en el modal
            updateUserAdditionalInfo(userInfo, window.currentUserPhone);
            
            // 8. Renderizar lista inicial con TODOS los roles
            filteredRoles = [...rolesWithAssignment];
            renderRolesList();
            
        } catch (error) {
            console.error('❌ Error al cargar roles del usuario:', error);
            showError('Error al cargar los roles del usuario: ' + error.message);
        } finally {
            // Ocultar indicador de carga
            if (loadingIndicator) loadingIndicator.style.display = 'none';
        }
    }

    /**
     * Renderiza la lista de roles en el modal
     */
    function renderRolesList() {
        const rolesList = document.getElementById('roles-assignment-list');
        const noRolesMessage = document.getElementById('no-roles-message');
        
        if (!rolesList) {
            console.error('❌ No se encontró el contenedor de roles');
            return;
        }
        
        // Limpiar contenido previo
        rolesList.innerHTML = '';
        
        // Aplicar filtros
        let rolesToShow = [...filteredRoles];
        
        // Filtro de solo asignados
        if (showOnlyAssigned) {
            rolesToShow = rolesToShow.filter(role => role.assigned);
        }
        
        console.log(`🎨 Renderizando ${rolesToShow.length} roles (de ${filteredRoles.length} filtrados)`);
        
        if (rolesToShow.length === 0) {
            rolesList.style.display = 'none';
            if (noRolesMessage) {
                noRolesMessage.textContent = 'No se encontraron roles que coincidan con la búsqueda';
                noRolesMessage.style.display = 'block';
            }
            return;
        }
        
        // Ocultar mensaje de "no hay roles" y mostrar lista
        if (noRolesMessage) noRolesMessage.style.display = 'none';
        rolesList.style.display = 'block';
        
        // Renderizar cada rol usando la misma estructura que roles
        rolesToShow.forEach(role => {
            const roleItem = document.createElement('div');
            roleItem.className = 'permission-assignment-item';
            
            // Determinar si el checkbox debe estar bloqueado
            // Los roles de app deben estar bloqueados para TODOS los usuarios (tanto app como web)
            const userType = rolesData ? rolesData.userType : 'mobile';
            const isAppRole = role.web === false; // roles de app tienen web: false
            const shouldDisable = isAppRole; // Bloquear todos los roles de app independientemente del tipo de usuario
            
            console.log(`🔍 Rol: ${role.name}, Tipo usuario: ${userType}, Es rol app: ${isAppRole}, Bloquear: ${shouldDisable}`);
            
            // Construir el HTML del checkbox con candado si está deshabilitado
            const checkboxHTML = shouldDisable ? `
                <div class="checkbox-with-lock">
                    <input type="checkbox" 
                           class="permission-checkbox role-assignment-checkbox" 
                           data-role-id="${role.id}"
                           ${role.assigned ? 'checked' : ''}
                           disabled>
                </div>
            ` : `
                <input type="checkbox" 
                       class="permission-checkbox role-assignment-checkbox" 
                       data-role-id="${role.id}"
                       ${role.assigned ? 'checked' : ''}>
            `;
            
            roleItem.innerHTML = `
                <div class="permission-name">${role.name}</div>
                <div class="permission-type">
                    <span class="type-badge ${role.type.toLowerCase()}">${role.type}</span>
                </div>
                <div class="permission-checkbox-container">
                    ${checkboxHTML}
                </div>
            `;
            
            rolesList.appendChild(roleItem);
        });
        
        console.log('✅ Lista de roles renderizada:', rolesToShow.length, 'items');
        
        // Configurar eventos de checkboxes
        setupRoleCheckboxEvents();
    }

    /**
     * Configura los eventos de los checkboxes de roles
     */
    function setupRoleCheckboxEvents() {
        const checkboxes = document.querySelectorAll('.role-assignment-checkbox');
        
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                const roleId = e.target.dataset.roleId;
                const isChecked = e.target.checked;
                
                handleRoleToggle(roleId, isChecked);
            });
        });
    }

    /**
     * Maneja el cambio de estado de un checkbox de rol (igual que permissions)
     * @param {string} roleId - ID del rol
     * @param {boolean} isChecked - Estado del checkbox
     */
    function handleRoleToggle(roleId, isChecked) {
        if (!rolesData) {
            console.warn('⚠️ No hay datos de roles disponibles para actualizar');
            return;
        }
        
        console.log('🔄 Cambiando estado del rol:', { id: roleId, checked: isChecked });
        
        // Actualizar el estado en filteredRoles
        const roleIndex = filteredRoles.findIndex(r => r.id.toString() === roleId.toString());
        if (roleIndex !== -1) {
            filteredRoles[roleIndex].assigned = isChecked;
        }
        
        // También actualizar en rolesData.allRoles
        const allRoleIndex = rolesData.allRoles.findIndex(r => r.id.toString() === roleId.toString());
        if (allRoleIndex !== -1) {
            rolesData.allRoles[allRoleIndex].assigned = isChecked;
        }
        
        // También actualizar en rolesData.filteredByType si existe
        if (rolesData.filteredByType) {
            const typeFilteredIndex = rolesData.filteredByType.findIndex(r => r.id.toString() === roleId.toString());
            if (typeFilteredIndex !== -1) {
                rolesData.filteredByType[typeFilteredIndex].assigned = isChecked;
            }
        }
        
        console.log('✅ Estado del rol actualizado:', { roleId, assigned: isChecked });
    }

    /**
     * Filtra los roles por texto de búsqueda
     * @param {string} searchText - Texto a buscar
     */
    function filterRolesBySearch(searchText) {
        if (!rolesData) return;
        
        const searchTerm = searchText.toLowerCase().trim();
        
        // Usar TODOS los roles como base (sin filtrar por tipo)
        const baseRoles = rolesData.allRoles;
        
        if (!searchTerm) {
            filteredRoles = [...baseRoles];
        } else {
            filteredRoles = baseRoles.filter(role =>
                role.name.toLowerCase().includes(searchTerm)
            );
        }
        
        console.log(`🔍 Filtro de búsqueda aplicado: "${searchTerm}" - ${filteredRoles.length} roles encontrados (de ${baseRoles.length} roles totales - web y app)`);
        renderRolesList();
    }

    /**
     * Aplica o quita el filtro de roles asignados
     * @param {boolean} showAssignedOnly - Si mostrar solo roles asignados
     */
    function filterRolesByAssignment(showAssignedOnly) {
        showOnlyAssigned = showAssignedOnly;
        console.log(`🔍 Filtro de asignados: ${showAssignedOnly ? 'activado' : 'desactivado'}`);
        renderRolesList();
    }

    /**
     * Guarda los cambios de roles del usuario (siguiendo el patrón de role-details-controller)
     */
    async function saveUserRoles() {
        if (!rolesData || !currentUserId) {
            console.error('❌ No hay datos para guardar');
            if (typeof window.showToast === 'function') {
                window.showToast('No hay cambios para guardar', 'warning');
            }
            return;
        }
        
        // Obtener roles asignados (marcados como checked)
        const assignedRoles = rolesData.allRoles.filter(role => role.assigned);
        const selectedRoleIds = assignedRoles.map(role => role.id);
        
        // Obtener roles actualmente asignados al usuario (los que ya tenía)
        const currentUserRoles = rolesData.allRoles.filter(role => role.originallyAssigned);
        
        console.log('💾 Guardando roles del usuario:', {
            userId: currentUserId,
            userName: currentUserName,
            selectedRoles: selectedRoleIds,
            currentRoles: currentUserRoles.map(r => r.id),
            allRoles: rolesData.allRoles.length
        });
        
        try {
            // Usar AssignRoleService para filtrar y asignar solo roles web sin duplicados
            const result = await window.AssignRoleService.assignRolesToUser(
                currentUserId, 
                selectedRoleIds, 
                currentUserRoles, 
                rolesData.allRoles
            );
            
            if (result.success) {
                const successMessage = result.message || `Roles del usuario "${currentUserName}" actualizados correctamente`;
                
                console.log('✅ Roles guardados exitosamente');
                
                // Mostrar mensaje de éxito
                if (typeof window.showToast === 'function') {
                    window.showToast(successMessage, 'success');
                } else {
                    showSuccess(successMessage);
                }
                
                // Cerrar modal después de un breve delay (igual que roles)
                setTimeout(() => {
                    closeRoleAssignmentModal();
                    
                    // Recargar la tabla de usuarios si existe el controlador
                    if (window.UsersController && typeof window.UsersController.reloadUsers === 'function') {
                        window.UsersController.reloadUsers();
                    }
                }, 1000);
                
            } else {
                console.error('❌ Error al guardar roles:', result.message);
                const errorMessage = result.message || 'Error desconocido al guardar roles';
                
                if (typeof window.showToast === 'function') {
                    window.showToast(errorMessage, 'error');
                } else {
                    showError('Error al guardar roles: ' + errorMessage);
                }
            }
            
        } catch (error) {
            console.error('❌ Error al guardar roles del usuario:', error);
            const errorMessage = 'Error al guardar roles: ' + error.message;
            
            if (typeof window.showToast === 'function') {
                window.showToast(errorMessage, 'error');
            } else {
                showError(errorMessage);
            }
        }
    }

    /**
     * Configura los eventos del modal
     */
    function setupModalEvents() {
        // Botón de cerrar
        const closeBtn = document.getElementById('close-user-edit-modal');
        const cancelBtn = document.getElementById('cancel-user-edit');
        const saveBtn = document.getElementById('save-user-roles');
        
        if (closeBtn) {
            closeBtn.addEventListener('click', closeRoleAssignmentModal);
        }
        
        if (cancelBtn) {
            cancelBtn.addEventListener('click', closeRoleAssignmentModal);
        }
        
        if (saveBtn) {
            saveBtn.addEventListener('click', saveUserRoles);
        }
        
        // Cerrar modal al hacer clic fuera
        const modal = document.getElementById('user-edit-modal');
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    closeRoleAssignmentModal();
                }
            });
        }
        
        // Búsqueda de roles
        const searchInput = document.getElementById('search-roles-modal');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                filterRolesBySearch(e.target.value);
            });
        }
        
        // Filtro de asignados
        const assignedFilter = document.getElementById('filter-assigned-roles');
        if (assignedFilter) {
            assignedFilter.addEventListener('change', (e) => {
                filterRolesByAssignment(e.target.checked);
            });
        }
        
        // Botón de actualizar contraseña
        const updatePasswordBtn = document.getElementById('update-password');
        if (updatePasswordBtn) {
            updatePasswordBtn.addEventListener('click', handlePasswordUpdate);
        }
        
        // Toggle de visibilidad de contraseña
        const passwordToggleBtn = document.getElementById('toggle-password-visibility');
        if (passwordToggleBtn) {
            passwordToggleBtn.addEventListener('click', togglePasswordVisibility);
        }

        // Botones de verificación de contactos
        const verifyEmailBtn = document.getElementById('verify-email');
        const verifyPhoneBtn = document.getElementById('verify-phone');
        
        if (verifyEmailBtn) {
            verifyEmailBtn.addEventListener('click', handleEmailVerification);
        }
        
        if (verifyPhoneBtn) {
            verifyPhoneBtn.addEventListener('click', handlePhoneVerification);
        }
    }

    /**
     * Maneja la actualización de contraseña del usuario
     */
    async function handlePasswordUpdate() {
        const passwordInput = document.getElementById('user-new-password');
        
        if (!passwordInput || !passwordInput.value.trim()) {
            const message = 'Por favor ingrese una nueva contraseña';
            if (typeof window.showToast === 'function') {
                window.showToast(message, 'warning');
            } else {
                showError(message);
            }
            return;
        }
        
        const newPassword = passwordInput.value.trim();
        
        if (newPassword.length < 6) {
            const message = 'La contraseña debe tener al menos 6 caracteres';
            if (typeof window.showToast === 'function') {
                window.showToast(message, 'warning');
            } else {
                showError(message);
            }
            return;
        }
        
        if (!currentUserId) {
            console.error('❌ No hay usuario seleccionado para actualizar contraseña');
            return;
        }
        
        console.log(`🔑 Actualizando contraseña del usuario ${currentUserId}`);
        
        try {
            // Deshabilitar botón mientras se procesa
            const updateBtn = document.getElementById('update-password');
            if (updateBtn) {
                updateBtn.disabled = true;
                updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualizando...';
            }
            
            // Verificar que ResetPasswordService esté disponible
            if (!window.ResetPasswordService) {
                console.error('❌ ResetPasswordService no está disponible');
                throw new Error('Servicio de reseteo de contraseña no disponible');
            }
            
            if (typeof window.ResetPasswordService.resetUserPassword !== 'function') {
                console.error('❌ método resetUserPassword no está disponible en ResetPasswordService');
                throw new Error('Método de reseteo de contraseña no disponible');
            }
            
            // Usar ResetPasswordService para resetear contraseña
            const result = await window.ResetPasswordService.resetUserPassword(
                currentUserId, 
                newPassword, 
                true, 
                'Contraseña actualizada por administrador desde panel web'
            );
            
            if (result.success) {
                const successMessage = result.message || 'Contraseña actualizada correctamente';
                console.log('✅ Contraseña actualizada exitosamente');
                
                // Limpiar el campo de contraseña
                passwordInput.value = '';
                
                // Mostrar mensaje de éxito
                if (typeof window.showToast === 'function') {
                    window.showToast(successMessage, 'success');
                } else {
                    showSuccess(successMessage);
                }
            } else {
                console.error('❌ Error al actualizar contraseña:', result.message);
                const errorMessage = result.message || 'Error desconocido al actualizar contraseña';
                
                if (typeof window.showToast === 'function') {
                    window.showToast(errorMessage, 'error');
                } else {
                    showError('Error al actualizar contraseña: ' + errorMessage);
                }
            }
            
        } catch (error) {
            console.error('❌ Error al actualizar contraseña:', error);
            const errorMessage = 'Error al actualizar contraseña: ' + error.message;
            
            if (typeof window.showToast === 'function') {
                window.showToast(errorMessage, 'error');
            } else {
                showError(errorMessage);
            }
        } finally {
            // Rehabilitar botón
            const updateBtn = document.getElementById('update-password');
            if (updateBtn) {
                updateBtn.disabled = false;
                updateBtn.innerHTML = '<i class="fas fa-key"></i> Actualizar';
            }
        }
    }

    /**
     * Alterna la visibilidad de la contraseña (mostrar/ocultar)
     */
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('user-new-password');
        const toggleIcon = document.getElementById('password-toggle-icon');
        
        if (!passwordInput || !toggleIcon) {
            console.error('❌ Elementos de toggle de contraseña no encontrados');
            return;
        }
        
        if (passwordInput.type === 'password') {
            // Mostrar contraseña
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            // Ocultar contraseña
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }

    /**
     * Muestra un mensaje de éxito
     * @param {string} message - Mensaje a mostrar
     */
    function showSuccess(message) {
        if (window.globalToast && typeof window.globalToast.show === 'function') {
            window.globalToast.show(message, 'success');
        } else {
            alert(message);
        }
    }

    /**
     * Muestra un mensaje de error
     * @param {string} message - Mensaje a mostrar
     */
    function showError(message) {
        if (window.globalToast && typeof window.globalToast.show === 'function') {
            window.globalToast.show(message, 'error');
        } else {
            alert(message);
        }
    }

    /**
     * Determina el tipo de usuario (web o mobile) basado en sus roles asignados
     * @param {Array} assignedRoles - Roles asignados al usuario
     * @returns {string} 'web' o 'mobile'
     */
    function determineUserType(assignedRoles) {
        if (!assignedRoles || assignedRoles.length === 0) {
            console.log('📋 Usuario sin roles, asumiendo tipo mobile por defecto');
            return 'mobile';
        }
        
        // Verificar si tiene algún rol web
        const hasWebRoles = assignedRoles.some(role => role.web === true);
        
        if (hasWebRoles) {
            console.log('🌐 Usuario tiene roles web');
            return 'web';
        } else {
            console.log('📱 Usuario solo tiene roles mobile');
            return 'mobile';
        }
    }

    /**
     * [FUNCIÓN DESHABILITADA] Filtra los roles disponibles según el tipo de usuario
     * NOTA: Se deshabilitó para mostrar todos los roles (web y app) en el modal
     * @param {Array} allRoles - Todos los roles disponibles
     * @param {string} userType - Tipo de usuario ('web' o 'mobile')
     * @returns {Array} Roles filtrados
     */
    function filterRolesByUserType(allRoles, userType) {
        console.log('⚠️ filterRolesByUserType: Esta función está deshabilitada. Ahora se muestran todos los roles.');
        return allRoles; // Devolver todos los roles sin filtrar
        
        /* CÓDIGO ORIGINAL COMENTADO:
        if (!allRoles || allRoles.length === 0) {
            return [];
        }
        
        // Si es usuario web, mostrar solo roles web
        // Si es usuario mobile, mostrar solo roles mobile (app)
        const isWebUser = userType === 'web';
        
        const filtered = allRoles.filter(role => {
            return isWebUser ? (role.web === true) : (role.web === false);
        });
        
        console.log(`🔍 Filtrado de roles:`);
        console.log(`   - Tipo de usuario: ${userType}`);
        console.log(`   - Mostrar roles web: ${isWebUser}`);
        console.log(`   - Total roles disponibles: ${allRoles.length}`);
        console.log(`   - Roles filtrados: ${filtered.length}`);
        
        return filtered;
        */
    }

    /**
     * Actualiza la información adicional del usuario en el modal
     * @param {Object} userInfo - Información del usuario
     * @param {string} userPhone - Teléfono del usuario (viene de la lista de usuarios)
     */
    function updateUserAdditionalInfo(userInfo, userPhone = null) {
        console.log('📝 Actualizando información adicional del usuario:', userInfo);
        
        // Actualizar email
        const emailValue = document.getElementById('user-email-value');
        if (emailValue) {
            emailValue.textContent = userInfo.email || 'No cuenta con correo';
        }
        
        // Actualizar teléfono
        const phoneValue = document.getElementById('user-phone-value');
        if (phoneValue) {
            phoneValue.textContent = userPhone || 'No cuenta con teléfono';
        }
        
        console.log('✅ Información adicional actualizada');
    }

    /**
     * Maneja la verificación del email del usuario
     */
    async function handleEmailVerification() {
        if (!currentUserId) {
            console.error('❌ No hay usuario seleccionado para verificar email');
            const message = 'No hay usuario seleccionado';
            if (typeof window.showToast === 'function') {
                window.showToast(message, 'error');
            } else {
                showError(message);
            }
            return;
        }

        console.log(`📧 Iniciando verificación de email para usuario ${currentUserId}`);

        // Verificar que el servicio esté disponible
        if (typeof window.VerifyContactService === 'undefined') {
            console.error('❌ VerifyContactService no está disponible');
            const message = 'Servicio de verificación no disponible';
            if (typeof window.showToast === 'function') {
                window.showToast(message, 'error');
            } else {
                showError(message);
            }
            return;
        }

        try {
            // Deshabilitar botón mientras se procesa
            const verifyBtn = document.getElementById('verify-email');
            if (verifyBtn) {
                verifyBtn.disabled = true;
                verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verificando...';
            }

            // 🔑 USAR CONTACTID DINÁMICO DEL EMAIL
            // Verificar que tenemos el contactId dinámico almacenado
            const emailContactId = window.currentUserContactIds?.email;
            
            console.log(`📧 Verificando email para usuario ${currentUserId} con contactId dinámico: ${emailContactId}`);
            console.log('🔑 ContactIds disponibles:', window.currentUserContactIds);
            console.log('🔍 Estructura completa currentUserContactIds:', JSON.stringify(window.currentUserContactIds, null, 2));
            
            if (!emailContactId) {
                const errorMsg = 'No se encontró contactId de email para este usuario. Asegúrate de que el usuario tenga un email registrado y que se hayan cargado los detalles completos del usuario.';
                console.error('❌ Error verificando email:', errorMsg);
                throw new Error(errorMsg);
            }

            const result = await window.VerifyContactService.verifyEmailContact(currentUserId, emailContactId);

            if (result.success) {
                console.log('✅ Email verificado exitosamente');
                if (typeof window.showToast === 'function') {
                    window.showToast(result.message || 'Email verificado exitosamente', 'success');
                } else {
                    showSuccess(result.message || 'Email verificado exitosamente');
                }
                
                // Actualizar el estado visual del botón
                if (verifyBtn) {
                    verifyBtn.innerHTML = '<i class="fas fa-check-circle"></i> Verificado';
                    verifyBtn.classList.add('verified');
                    verifyBtn.disabled = true;
                }
            } else {
                throw new Error(result.message || 'Error al verificar email');
            }

        } catch (error) {
            console.error('❌ Error al verificar email:', error);
            const message = error.message || 'Error al verificar el email';
            if (typeof window.showToast === 'function') {
                window.showToast(message, 'error');
            } else {
                showError(message);
            }
        } finally {
            // Restaurar botón si no fue verificado exitosamente
            const verifyBtn = document.getElementById('verify-email');
            if (verifyBtn && !verifyBtn.classList.contains('verified')) {
                verifyBtn.disabled = false;
                verifyBtn.innerHTML = '<i class="fas fa-check-circle"></i> Verificar';
            }
        }
    }

    /**
     * Maneja la verificación del teléfono del usuario
     */
    async function handlePhoneVerification() {
        if (!currentUserId) {
            console.error('❌ No hay usuario seleccionado para verificar teléfono');
            const message = 'No hay usuario seleccionado';
            if (typeof window.showToast === 'function') {
                window.showToast(message, 'error');
            } else {
                showError(message);
            }
            return;
        }

        console.log(`📱 Iniciando verificación de teléfono para usuario ${currentUserId}`);

        // Verificar que el servicio esté disponible
        if (typeof window.VerifyContactService === 'undefined') {
            console.error('❌ VerifyContactService no está disponible');
            const message = 'Servicio de verificación no disponible';
            if (typeof window.showToast === 'function') {
                window.showToast(message, 'error');
            } else {
                showError(message);
            }
            return;
        }

        try {
            // Deshabilitar botón mientras se procesa
            const verifyBtn = document.getElementById('verify-phone');
            if (verifyBtn) {
                verifyBtn.disabled = true;
                verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verificando...';
            }

            // 🔑 USAR CONTACTID DINÁMICO DEL TELÉFONO
            // Verificar que tenemos el contactId dinámico almacenado
            const phoneContactId = window.currentUserContactIds?.phone;
            
            console.log(`📱 Verificando teléfono para usuario ${currentUserId} con contactId dinámico: ${phoneContactId}`);
            console.log('🔑 ContactIds disponibles:', window.currentUserContactIds);
            console.log('🔍 Estructura completa currentUserContactIds:', JSON.stringify(window.currentUserContactIds, null, 2));
            
            if (!phoneContactId) {
                const errorMsg = 'No se encontró contactId de teléfono para este usuario. Asegúrate de que el usuario tenga un teléfono registrado y que se hayan cargado los detalles completos del usuario.';
                console.error('❌ Error verificando teléfono:', errorMsg);
                throw new Error(errorMsg);
            }

            const result = await window.VerifyContactService.verifyPhoneContact(currentUserId, phoneContactId);

            if (result.success) {
                console.log('✅ Teléfono verificado exitosamente');
                if (typeof window.showToast === 'function') {
                    window.showToast(result.message || 'Teléfono verificado exitosamente', 'success');
                } else {
                    showSuccess(result.message || 'Teléfono verificado exitosamente');
                }
                
                // Actualizar el estado visual del botón
                if (verifyBtn) {
                    verifyBtn.innerHTML = '<i class="fas fa-check-circle"></i> Verificado';
                    verifyBtn.classList.add('verified');
                    verifyBtn.disabled = true;
                }
            } else {
                throw new Error(result.message || 'Error al verificar teléfono');
            }

        } catch (error) {
            console.error('❌ Error al verificar teléfono:', error);
            const message = error.message || 'Error al verificar el teléfono';
            if (typeof window.showToast === 'function') {
                window.showToast(message, 'error');
            } else {
                showError(message);
            }
        } finally {
            // Restaurar botón si no fue verificado exitosamente
            const verifyBtn = document.getElementById('verify-phone');
            if (verifyBtn && !verifyBtn.classList.contains('verified')) {
                verifyBtn.disabled = false;
                verifyBtn.innerHTML = '<i class="fas fa-check-circle"></i> Verificar';
            }
        }
    }

    // API pública del controlador
    return {
        init,
        openRoleAssignmentModal,
        closeRoleAssignmentModal
    };
})();

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    UserDetailsController.init();
    
    // Exponer el controlador globalmente para que pueda ser usado por otros scripts
    window.UserDetailsController = UserDetailsController;
});

// Exponer globalmente para otros controladores
window.UserDetailsController = UserDetailsController;

console.log('✅ UserDetailsController cargado y disponible globalmente');