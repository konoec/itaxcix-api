/**
 * Controlador para asignación de roles a usuarios
 * Maneja la apertura del modal, carga de roles y asignación
 */
class AssignRolesController {
    constructor() {
        this.currentUserId = null;
        this.currentUserData = null;
        this.allRoles = [];
        this.filteredRoles = [];
        this.selectedRoles = [];
        this.currentUserRoles = []; // Asegurar que siempre sea array
        
        this.init();
    }

    /**
     * Inicializa el controlador y configura eventos
     */
    init() {
        console.log('🚀 AssignRolesController inicializado');
        this.setupModalEvents();
        this.setupSearchEvents();
        this.setupSelectionEvents();
        this.setupSaveEvents();
        this.setupAdminActions();
    }

    /**
     * Configura los eventos del modal
     */
    setupModalEvents() {
        // Cerrar modal
        const modal = document.getElementById('assign-roles-modal');
        if (modal) {
            modal.addEventListener('hidden.bs.modal', () => {
                this.resetModal();
            });
        }

        // Botón cancelar
        const cancelBtn = modal?.querySelector('[data-bs-dismiss="modal"]');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                this.resetModal();
            });
        }
    }

    /**
     * Configura los eventos de búsqueda
     */
    setupSearchEvents() {
        const searchInput = document.getElementById('search-roles-modal');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.filterRoles(e.target.value.trim());
            });
        }
    }

    /**
     * Configura los eventos de selección
     */
    setupSelectionEvents() {
        // Checkbox maestro
        const selectAllCheckbox = document.getElementById('select-all-checkbox');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', (e) => {
                if (e.target.checked) {
                    this.selectAllVisibleRoles();
                } else {
                    this.deselectAllVisibleRoles();
                }
            });
        }

        // Filtro de roles asignados
        const filterAssignedCheckbox = document.getElementById('filter-assigned-roles');
        if (filterAssignedCheckbox) {
            filterAssignedCheckbox.addEventListener('change', (e) => {
                this.filterByAssigned(e.target.checked);
            });
        }
    }

    /**
     * Configura los eventos de guardado
     */
    setupSaveEvents() {
        const saveBtn = document.getElementById('save-roles-btn');
        if (saveBtn) {
            saveBtn.addEventListener('click', () => {
                this.saveRoles();
            });
        }
    }

    /**
     * Abre el modal de asignación de roles
     */
    async openAssignRolesModal(userId, userData) {
        try {
            console.log('🔍 Abriendo modal de asignación de roles para usuario:', { id: userId, data: userData });
            
            // Debug: Verificar servicios disponibles
            console.log('🔧 Debug - Servicios disponibles:');
            console.log('- RoleService:', typeof window.RoleService !== 'undefined');
            console.log('- VerifyContactService:', typeof window.VerifyContactService !== 'undefined');
            console.log('- ResetPasswordService:', typeof window.ResetPasswordService !== 'undefined');
            
            if (typeof window.VerifyContactService !== 'undefined') {
                console.log('- VerifyContactService.verifyEmailContact:', typeof window.VerifyContactService.verifyEmailContact);
                console.log('- VerifyContactService.verifyPhoneContact:', typeof window.VerifyContactService.verifyPhoneContact);
            }
            
            // Verificar que RoleService esté disponible
            if (typeof RoleService === 'undefined') {
                console.error('❌ RoleService no está disponible');
                this.showToast('Error: Servicio de roles no disponible', 'error');
                return;
            }
            
            // Verificar que haya token de autenticación
            const token = sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
            if (!token) {
                console.error('❌ No hay token de autenticación');
                this.showToast('Error: No hay token de autenticación', 'error');
                return;
            }
            
            this.currentUserId = userId;
            this.currentUserData = userData;
            
            // Actualizar información del usuario en el modal
            this.updateUserInfo(userData);
            
            // Mostrar modal
            const modal = document.getElementById('assign-roles-modal');
            if (modal) {
                const bsModal = new bootstrap.Modal(modal);
                bsModal.show();
            }
            
            // Cargar roles
            await this.loadRoles();
            
        } catch (error) {
            console.error('❌ Error al abrir modal de asignación de roles:', error);
            this.showToast('Error al abrir el modal de roles: ' + error.message, 'error');
        }
    }

    /**
     * Actualiza la información del usuario en el modal
     */
    updateUserInfo(userData) {
        const nameElement = document.getElementById('assign-roles-user-name');
        const infoElement = document.getElementById('assign-roles-user-info');
        
        if (nameElement) {
            const fullName = `${userData.firstName || ''} ${userData.lastName || ''}`.trim() || 'Usuario';
            nameElement.textContent = fullName;
        }
        
        if (infoElement) {
            const info = [];
            if (userData.document) info.push(`DNI: ${userData.document}`);
            if (userData.email) info.push(`Email: ${userData.email}`);
            infoElement.textContent = info.join(' • ') || 'Información no disponible';
        }

        // Actualizar campos de contacto en acciones administrativas
        this.updateContactFields(userData);
    }

    /**
     * Actualiza los campos de contacto en las acciones administrativas
     */
    updateContactFields(userData) {
        // Campo de email
        const emailField = document.getElementById('user-email-display');
        if (emailField) {
            emailField.value = userData.email || 'No especificado';
        }

        // Campo de teléfono
        const phoneField = document.getElementById('user-phone-display');
        if (phoneField) {
            phoneField.value = userData.phone || userData.phoneNumber || 'No especificado';
        }

        // Limpiar campo de nueva contraseña
        const newPasswordField = document.getElementById('new-password-input');
        if (newPasswordField) {
            newPasswordField.value = '';
            newPasswordField.type = 'password';
        }

        // Resetear icono del ojo
        const toggleIcon = document.getElementById('toggle-new-password');
        if (toggleIcon) {
            const icon = toggleIcon.querySelector('i');
            if (icon) {
                icon.className = 'fas fa-eye';
            }
        }
    }

    /**
     * Carga los roles desde la API
     */
    async loadRoles() {
        try {
            this.showLoading();
            
            // Cargar todos los roles disponibles
            const rolesResponse = await this.fetchRoles();
            if (rolesResponse && rolesResponse.success) {
                // El servicio puede devolver los datos en diferentes estructuras
                let rolesArray = null;
                
                if (rolesResponse.data && rolesResponse.data.roles) {
                    rolesArray = rolesResponse.data.roles;
                } else if (rolesResponse.data && rolesResponse.data.data && rolesResponse.data.data.items) {
                    rolesArray = rolesResponse.data.data.items;
                } else if (rolesResponse.data && rolesResponse.data.items) {
                    rolesArray = rolesResponse.data.items;
                } else if (rolesResponse.data && Array.isArray(rolesResponse.data)) {
                    rolesArray = rolesResponse.data;
                } else if (Array.isArray(rolesResponse.data)) {
                    rolesArray = rolesResponse.data;
                }
                
                this.allRoles = rolesArray || [];
                console.log('📋 Roles cargados:', this.allRoles.length);
                console.log('📋 Estructura de primer rol:', this.allRoles[0]);
            } else {
                console.error('❌ Error en respuesta de roles:', rolesResponse);
                this.allRoles = [];
            }
            
            // Cargar roles actuales del usuario - con manejo de error mejorado
            try {
                const userRolesResponse = await this.fetchUserRoles(this.currentUserId);
                if (userRolesResponse && userRolesResponse.success && userRolesResponse.data) {
                    // El endpoint devuelve los roles en data.roles
                    if (userRolesResponse.data.roles && Array.isArray(userRolesResponse.data.roles)) {
                        this.currentUserRoles = userRolesResponse.data.roles;
                        console.log('👤 Roles del usuario cargados:', this.currentUserRoles.length);
                        console.log('👤 Estructura de roles del usuario:', this.currentUserRoles);
                    } else {
                        this.currentUserRoles = [];
                    }
                } else {
                    this.currentUserRoles = [];
                }
            } catch (userRolesError) {
                console.error('❌ Error al cargar roles del usuario (continuando sin roles previos):', userRolesError);
                this.currentUserRoles = [];
                
                // Mostrar advertencia pero continuar
                this.showToast('Advertencia: No se pudieron cargar los roles actuales del usuario. Se mostrará sin roles previos.', 'warning');
            }
            
            // Marcar roles ya asignados - asegurar que currentUserRoles sea array
            // Extraer IDs de roles asignados del usuario
            this.selectedRoles = Array.isArray(this.currentUserRoles) ? 
                this.currentUserRoles.map(role => {
                    // Manejar diferentes estructuras de respuesta
                    if (role && typeof role === 'object') {
                        return role.id || role.roleId || role.role_id;
                    }
                    return role;
                }).filter(id => id !== undefined && id !== null) : [];
            
            console.log('✅ Roles seleccionados inicialmente:', this.selectedRoles);
            
            // Aplicar filtros y mostrar roles
            this.applyFilters();
            this.renderRoles();
            
        } catch (error) {
            console.error('❌ Error al cargar roles:', error);
            this.showError('Error al cargar los roles: ' + error.message);
        } finally {
            this.hideLoading();
        }
    }

    /**
     * Obtiene todos los roles desde la API usando RoleService
     */
    async fetchRoles() {
        console.log('🔄 Obteniendo roles usando RoleService...');
        
        if (typeof RoleService === 'undefined') {
            throw new Error('RoleService no está disponible');
        }
        
        // Usar el servicio para obtener todos los roles (página 1, 100 elementos para obtener todos)
        const response = await RoleService.getRoles(1, 100);
        
        if (!response.success) {
            throw new Error(response.message || 'Error al obtener roles');
        }
        
        console.log('📋 Respuesta del RoleService:', response);
        return response;
    }

    /**
     * Obtiene los roles del usuario desde la API
     */
    async fetchUserRoles(userId) {
        console.log('🔄 Obteniendo roles del usuario:', userId);
        
        const token = sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
        if (!token) {
            throw new Error('No hay token de autenticación disponible');
        }
        
        const response = await fetch(`https://149.130.161.148/api/v1/users/${userId}/roles`, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Content-Type': 'application/json'
            }
        });
        
        console.log('📡 Respuesta de roles de usuario - Status:', response.status);
        
        if (!response.ok) {
            const errorText = await response.text();
            console.error('❌ Error HTTP al obtener roles del usuario:', response.status, errorText);
            throw new Error(`Error al obtener roles del usuario: ${response.status} - ${errorText}`);
        }
        
        const result = await response.json();
        console.log('📋 Roles del usuario obtenidos:', result);
        return result;
    }

    /**
     * Filtra los roles según el texto de búsqueda
     */
    filterRoles(searchText) {
        this.applyFilters(); // Esto ahora maneja automáticamente la limpieza de selecciones
        this.renderRoles();
    }

    /**
     * Filtra roles para mostrar solo los asignados
     */
    filterByAssigned(showOnlyAssigned) {
        this.applyFilters(showOnlyAssigned);
        
        // Si se activa el filtro de asignados, limpiar selecciones que no están en la vista filtrada
        if (showOnlyAssigned) {
            const visibleRoleIds = this.filteredRoles.map(role => role.id);
            this.selectedRoles = this.selectedRoles.filter(id => visibleRoleIds.includes(id));
        }
        
        this.renderRoles();
    }

    /**
     * Aplica todos los filtros activos
     */
    applyFilters(showOnlyAssigned = null) {
        // Obtener estado del checkbox si no se especifica
        if (showOnlyAssigned === null) {
            const filterAssignedCheckbox = document.getElementById('filter-assigned-roles');
            showOnlyAssigned = filterAssignedCheckbox ? filterAssignedCheckbox.checked : false;
        }

        // Obtener texto de búsqueda
        const searchInput = document.getElementById('search-roles-modal');
        const searchText = searchInput ? searchInput.value.trim().toLowerCase() : '';

        // Aplicar filtros
        let filtered = [...this.allRoles];

        // Filtro de búsqueda
        if (searchText) {
            filtered = filtered.filter(role => 
                role.name.toLowerCase().includes(searchText) ||
                (role.description && role.description.toLowerCase().includes(searchText))
            );
        }

        // Filtro de roles asignados
        if (showOnlyAssigned) {
            console.log('🔍 Filtrando solo roles asignados...');
            console.log('📋 Roles seleccionados para filtro:', this.selectedRoles);
            
            filtered = filtered.filter(role => this.selectedRoles.includes(role.id));
            console.log('✅ Roles filtrados (solo asignados):', filtered.map(r => r.name));
        }

        this.filteredRoles = filtered;
        
        // Actualizar selecciones para mantener solo las que están visibles
        const visibleRoleIds = this.filteredRoles.map(role => role.id);
        this.selectedRoles = this.selectedRoles.filter(id => visibleRoleIds.includes(id));
    }

    /**
     * Renderiza la lista de roles
     */
    renderRoles() {
        const rolesContainer = document.getElementById('roles-list');
        const emptyState = document.getElementById('roles-empty');
        const tableContainer = document.getElementById('roles-table-container');
        
        if (!rolesContainer) return;
        
        if (this.filteredRoles.length === 0) {
            tableContainer.style.display = 'none';
            emptyState.style.display = 'block';
            return;
        }
        
        tableContainer.style.display = 'block';
        emptyState.style.display = 'none';
        
        const rolesHtml = this.filteredRoles.map(role => {
            // Verificar si este rol está en los roles seleccionados
            const isSelected = this.selectedRoles.includes(role.id);
            const roleType = role.web ? 'Web' : 'Móvil';
            const statusClass = role.active ? 'text-success' : 'text-danger';
            const statusText = role.active ? 'Activo' : 'Inactivo';
            
            // Verificar si es un rol de ciudadano que no se puede asignar
            const isCitizenRole = this.isCitizenRole(role);
            
            // Debug log para verificar la lógica
            console.log(`🔍 Rol ${role.name} (ID: ${role.id}) - ¿Está seleccionado?`, isSelected, '¿Es rol ciudadano?', isCitizenRole);
            console.log('📋 Roles seleccionados actuales:', this.selectedRoles);
            
            return `
                <tr>
                    <td>
                        ${isCitizenRole ? `
                            <i class="fas fa-lock text-muted" 
                               title="Este rol es asignado automáticamente por la app móvil"></i>
                        ` : `
                            <input class="form-check-input role-checkbox" 
                                   type="checkbox" 
                                   value="${role.id}" 
                                   ${isSelected ? 'checked' : ''}>
                        `}
                    </td>
                    <td>
                        ${role.name}
                        ${isSelected ? '<span class="badge bg-success ms-2">Asignado</span>' : ''}
                        ${isCitizenRole ? '<span class="badge bg-secondary text-white ms-2">Solo App Móvil</span>' : ''}
                    </td>
                    <td>
                        <span class="badge ${role.web ? 'text-white' : 'bg-info text-white'}" ${role.web ? 'style="background-color: #d4af37;"' : ''}>${roleType}</span>
                    </td>
                    <td>
                        <span class="${statusClass}">${statusText}</span>
                    </td>
                </tr>
            `;
        }).join('');
        
        rolesContainer.innerHTML = rolesHtml;
        
        // Configurar eventos de los checkboxes
        this.setupRoleCheckboxEvents();
        
        // Actualizar checkbox maestro
        this.updateSelectAllCheckbox();
    }

    /**
     * Verifica si un rol es de ciudadano y no se puede asignar desde el panel web
     */
    isCitizenRole(role) {
        if (!role || !role.name) return false;
        
        // Lista de nombres de roles que son exclusivos de la app móvil
        const citizenRoleNames = [
            'ciudadano',
            'citizen',
            'usuario móvil',
            'mobile user',
            'conductor',
            'driver',
            'propietario',
            'owner',
            'pasajero',
            'passenger'
        ];
        
        const roleName = role.name.toLowerCase().trim();
        
        // Verificar si coincide con algún nombre de rol de ciudadano
        const isCitizen = citizenRoleNames.some(citizenRole => 
            roleName.includes(citizenRole) || citizenRole.includes(roleName)
        );
        
        // También verificar si es un rol móvil (no web) como indicador adicional
        const isMobileOnly = !role.web;
        
        // Considerar como rol de ciudadano si coincide con el nombre O si es solo móvil y no es administrativo
        return isCitizen || (isMobileOnly && !this.isAdminRole(role));
    }

    /**
     * Verifica si un rol es administrativo (aunque sea móvil)
     */
    isAdminRole(role) {
        if (!role || !role.name) return false;
        
        const adminRoleNames = [
            'admin',
            'administrador',
            'supervisor',
            'moderador',
            'moderator',
            'operador',
            'operator',
            'soporte',
            'support',
            'técnico',
            'tecnico',
            'technical'
        ];
        
        const roleName = role.name.toLowerCase().trim();
        
        return adminRoleNames.some(adminRole => 
            roleName.includes(adminRole) || adminRole.includes(roleName)
        );
    }

    /**
     * Configura los eventos de los checkboxes de roles
     */
    setupRoleCheckboxEvents() {
        const checkboxes = document.querySelectorAll('.role-checkbox:not([disabled])');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', (e) => {
                const roleId = parseInt(e.target.value);
                
                // Buscar el rol para verificar si es de ciudadano
                const role = this.allRoles.find(r => r.id === roleId);
                if (role && this.isCitizenRole(role)) {
                    // Si es un rol de ciudadano, revertir el cambio
                    e.target.checked = this.selectedRoles.includes(roleId);
                    this.showToast('⚠️ Los roles de aplicación móvil no se pueden asignar desde el panel web', 'warning');
                    return;
                }
                
                if (e.target.checked) {
                    if (!this.selectedRoles.includes(roleId)) {
                        this.selectedRoles.push(roleId);
                    }
                } else {
                    this.selectedRoles = this.selectedRoles.filter(id => id !== roleId);
                }
                
                this.updateSelectAllCheckbox();
            });
        });
    }

    /**
     * Selecciona todos los roles visibles
     */
    selectAllVisibleRoles() {
        this.filteredRoles.forEach(role => {
            // Solo seleccionar roles que no sean de ciudadano
            if (!this.isCitizenRole(role) && !this.selectedRoles.includes(role.id)) {
                this.selectedRoles.push(role.id);
            }
        });
        
        this.renderRoles();
    }

    /**
     * Deselecciona todos los roles visibles
     */
    deselectAllVisibleRoles() {
        const filteredRoleIds = this.filteredRoles
            .filter(role => !this.isCitizenRole(role)) // Solo deseleccionar roles que no sean de ciudadano
            .map(role => role.id);
            
        this.selectedRoles = this.selectedRoles.filter(id => !filteredRoleIds.includes(id));
        
        this.renderRoles();
    }

    /**
     * Actualiza el estado del checkbox maestro
     */
    updateSelectAllCheckbox() {
        const selectAllCheckbox = document.getElementById('select-all-checkbox');
        if (!selectAllCheckbox) return;
        
        // Solo considerar roles que se pueden seleccionar (no bloqueados)
        const selectableRoles = this.filteredRoles.filter(role => !this.isCitizenRole(role));
        const selectableRoleIds = selectableRoles.map(role => role.id);
        const selectedSelectableRoles = this.selectedRoles.filter(id => selectableRoleIds.includes(id));
        
        if (selectableRoleIds.length === 0) {
            // Si no hay roles seleccionables, deshabilitar el checkbox maestro
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.disabled = true;
        } else if (selectedSelectableRoles.length === 0) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.disabled = false;
        } else if (selectedSelectableRoles.length === selectableRoleIds.length) {
            selectAllCheckbox.checked = true;
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.disabled = false;
        } else {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = true;
            selectAllCheckbox.disabled = false;
        }
    }

    /**
     * Guarda los roles seleccionados
     */
    async saveRoles() {
        try {
            const saveBtn = document.getElementById('save-roles-btn');
            if (saveBtn) {
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Guardando...';
            }
            
            console.log('💾 Guardando roles para usuario:', this.currentUserId);
            console.log('📋 Roles seleccionados:', this.selectedRoles);
            
            // Usar el servicio de asignación de roles
            const result = await AssignRoleService.assignRolesToUser(
                this.currentUserId,
                this.selectedRoles,
                this.currentUserRoles,
                this.allRoles
            );
            
            if (result.success) {
                this.showToast('Roles actualizados correctamente', 'success');
                
                // Cerrar modal
                const modal = document.getElementById('assign-roles-modal');
                if (modal) {
                    const bsModal = bootstrap.Modal.getInstance(modal);
                    if (bsModal) {
                        bsModal.hide();
                    }
                }
                
                // Actualizar la lista de usuarios si existe
                if (window.usersListController && window.usersListController.refreshUsersList) {
                    window.usersListController.refreshUsersList();
                }
                
            } else {
                this.showToast(result.message || 'Error al actualizar roles', 'error');
            }
            
        } catch (error) {
            console.error('❌ Error al guardar roles:', error);
            this.showToast('Error al guardar los roles', 'error');
        } finally {
            const saveBtn = document.getElementById('save-roles-btn');
            if (saveBtn) {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="fas fa-save me-1"></i>Guardar';
            }
        }
    }

    /**
     * Resetea el modal
     */
    resetModal() {
        this.currentUserId = null;
        this.currentUserData = null;
        this.selectedRoles = [];
        this.filteredRoles = [];
        this.currentUserRoles = []; // Asegurar que siempre sea array
        
        // Limpiar búsqueda
        const searchInput = document.getElementById('search-roles-modal');
        if (searchInput) {
            searchInput.value = '';
        }

        // Limpiar filtro de asignados
        const filterAssignedCheckbox = document.getElementById('filter-assigned-roles');
        if (filterAssignedCheckbox) {
            filterAssignedCheckbox.checked = false;
        }
        
        // Limpiar lista
        const rolesContainer = document.getElementById('roles-list');
        if (rolesContainer) {
            rolesContainer.innerHTML = '';
        }
        
        // Resetear estados
        const tableContainer = document.getElementById('roles-table-container');
        const emptyState = document.getElementById('roles-empty');
        if (tableContainer) tableContainer.style.display = 'none';
        if (emptyState) emptyState.style.display = 'none';
    }

    /**
     * Muestra el estado de carga
     */
    showLoading() {
        const loadingElement = document.getElementById('roles-loading');
        const tableContainer = document.getElementById('roles-table-container');
        const emptyState = document.getElementById('roles-empty');
        
        if (loadingElement) loadingElement.style.display = 'block';
        if (tableContainer) tableContainer.style.display = 'none';
        if (emptyState) emptyState.style.display = 'none';
    }

    /**
     * Oculta el estado de carga
     */
    hideLoading() {
        const loadingElement = document.getElementById('roles-loading');
        if (loadingElement) loadingElement.style.display = 'none';
    }

    /**
     * Muestra un mensaje de error
     */
    showError(message) {
        const emptyState = document.getElementById('roles-empty');
        if (emptyState) {
            emptyState.innerHTML = `
                <i class="fas fa-exclamation-triangle text-danger fa-3x mb-3"></i>
                <h3 class="text-danger">Error</h3>
                <p class="text-muted">${message}</p>
            `;
            emptyState.style.display = 'block';
        }
    }

    /**
     * Configura los eventos de los botones de acción administrativa
     */
    setupAdminActions() {
        // Verificar email
        const verifyEmailBtn = document.getElementById('verify-email-btn');
        if (verifyEmailBtn) {
            verifyEmailBtn.addEventListener('click', () => {
                this.handleVerifyEmail();
            });
        }

        // Verificar teléfono
        const verifyPhoneBtn = document.getElementById('verify-phone-btn');
        if (verifyPhoneBtn) {
            verifyPhoneBtn.addEventListener('click', () => {
                this.handleVerifyPhone();
            });
        }

        // Resetear contraseña
        const resetPasswordBtn = document.getElementById('reset-password-btn');
        if (resetPasswordBtn) {
            resetPasswordBtn.addEventListener('click', () => {
                this.handleResetPassword();
            });
        }

        // Toggle para mostrar/ocultar contraseña
        const togglePasswordBtn = document.getElementById('toggle-new-password');
        if (togglePasswordBtn) {
            togglePasswordBtn.addEventListener('click', () => {
                this.togglePasswordVisibility();
            });
        }
    }

    /**
     * Maneja la verificación de email del usuario
     */
    async handleVerifyEmail() {
        if (!this.currentUserId || !this.currentUserData) {
            this.showToast('Error: No hay usuario seleccionado', 'error');
            return;
        }

        try {
            // Verificar que el servicio esté disponible
            if (typeof window.VerifyContactService === 'undefined') {
                console.error('❌ VerifyContactService no está disponible en window');
                this.showToast('Error: Servicio de verificación no disponible', 'error');
                return;
            }

            const email = document.getElementById('user-email-display')?.value;
            if (!email || email === 'No especificado') {
                this.showToast('No hay email para verificar', 'warning');
                return;
            }

            // Confirmar acción con más información
            const confirmed = confirm(
                `¿Está seguro de que desea VERIFICAR MANUALMENTE el email de este usuario?\n\n` +
                `Usuario: ${this.currentUserData.firstName} ${this.currentUserData.lastName}\n` +
                `Email: ${email}\n\n` +
                `Esta acción marcará el email como verificado en el sistema.`
            );
            if (!confirmed) return;

            // Deshabilitar botón y mostrar loading
            const verifyBtn = document.getElementById('verify-email-btn');
            const originalText = verifyBtn?.innerHTML;
            if (verifyBtn) {
                verifyBtn.disabled = true;
                verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Verificando...';
            }

            this.showToast('🔍 Buscando contacto de email del usuario...', 'info');

            const result = await window.VerifyContactService.verifyEmailContact(this.currentUserId);
            
            if (result.success) {
                this.showToast('✅ Email verificado exitosamente', 'success');
                
                // Actualizar la lista de usuarios si existe para reflejar el cambio
                if (window.usersListController && window.usersListController.refreshUsersList) {
                    setTimeout(() => {
                        window.usersListController.refreshUsersList();
                    }, 1000);
                }
            } else {
                console.error('❌ Error al verificar email:', result);
                let errorMessage = result.message || 'Error al verificar email';
                
                // Manejar mensajes de error específicos
                if (errorMessage.includes('contacto no pertenece')) {
                    errorMessage = 'El contacto de email no se encontró o no pertenece a este usuario';
                } else if (errorMessage.includes('No se encontró')) {
                    errorMessage = 'Este usuario no tiene un contacto de email registrado';
                } else if (errorMessage.includes('token')) {
                    errorMessage = 'Error de autenticación. Por favor, inicie sesión nuevamente';
                }
                
                this.showToast('❌ ' + errorMessage, 'error');
            }

        } catch (error) {
            console.error('❌ Error en verificación de email:', error);
            this.showToast('Error inesperado al verificar email: ' + error.message, 'error');
        } finally {
            // Restaurar botón
            const verifyBtn = document.getElementById('verify-email-btn');
            if (verifyBtn) {
                verifyBtn.disabled = false;
                verifyBtn.innerHTML = '<i class="fas fa-envelope-check me-1"></i>Verificar Email';
            }
        }
    }

    /**
     * Maneja la verificación de teléfono del usuario
     */
    async handleVerifyPhone() {
        if (!this.currentUserId || !this.currentUserData) {
            this.showToast('Error: No hay usuario seleccionado', 'error');
            return;
        }

        try {
            // Verificar que el servicio esté disponible
            if (typeof window.VerifyContactService === 'undefined') {
                console.error('❌ VerifyContactService no está disponible en window');
                this.showToast('Error: Servicio de verificación no disponible', 'error');
                return;
            }

            const phone = document.getElementById('user-phone-display')?.value;
            if (!phone || phone === 'No especificado') {
                this.showToast('No hay teléfono para verificar', 'warning');
                return;
            }

            // Confirmar acción con más información
            const confirmed = confirm(
                `¿Está seguro de que desea VERIFICAR MANUALMENTE el teléfono de este usuario?\n\n` +
                `Usuario: ${this.currentUserData.firstName} ${this.currentUserData.lastName}\n` +
                `Teléfono: ${phone}\n\n` +
                `Esta acción marcará el teléfono como verificado en el sistema.`
            );
            if (!confirmed) return;

            // Deshabilitar botón y mostrar loading
            const verifyBtn = document.getElementById('verify-phone-btn');
            const originalText = verifyBtn?.innerHTML;
            if (verifyBtn) {
                verifyBtn.disabled = true;
                verifyBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Verificando...';
            }

            this.showToast('🔍 Buscando contacto de teléfono del usuario...', 'info');

            const result = await window.VerifyContactService.verifyPhoneContact(this.currentUserId);
            
            if (result.success) {
                this.showToast('✅ Teléfono verificado exitosamente', 'success');
                
                // Actualizar la lista de usuarios si existe para reflejar el cambio
                if (window.usersListController && window.usersListController.refreshUsersList) {
                    setTimeout(() => {
                        window.usersListController.refreshUsersList();
                    }, 1000);
                }
            } else {
                console.error('❌ Error al verificar teléfono:', result);
                let errorMessage = result.message || 'Error al verificar teléfono';
                
                // Manejar mensajes de error específicos
                if (errorMessage.includes('contacto no pertenece')) {
                    errorMessage = 'El contacto de teléfono no se encontró o no pertenece a este usuario';
                } else if (errorMessage.includes('No se encontró')) {
                    errorMessage = 'Este usuario no tiene un contacto de teléfono registrado';
                } else if (errorMessage.includes('token')) {
                    errorMessage = 'Error de autenticación. Por favor, inicie sesión nuevamente';
                }
                
                this.showToast('❌ ' + errorMessage, 'error');
            }

        } catch (error) {
            console.error('❌ Error en verificación de teléfono:', error);
            this.showToast('Error inesperado al verificar teléfono: ' + error.message, 'error');
        } finally {
            // Restaurar botón
            const verifyBtn = document.getElementById('verify-phone-btn');
            if (verifyBtn) {
                verifyBtn.disabled = false;
                verifyBtn.innerHTML = '<i class="fas fa-phone-check me-1"></i>Verificar Teléfono';
            }
        }
    }

    /**
     * Maneja el reseteo de contraseña del usuario
     */
    async handleResetPassword() {
        if (!this.currentUserId || !this.currentUserData) {
            this.showToast('Error: No hay usuario seleccionado', 'error');
            return;
        }

        try {
            // Verificar que el servicio esté disponible
            if (typeof window.ResetPasswordService === 'undefined') {
                console.error('❌ ResetPasswordService no está disponible en window');
                this.showToast('Error: Servicio de reseteo no disponible', 'error');
                return;
            }

            // Obtener la nueva contraseña del campo
            const newPasswordField = document.getElementById('new-password-input');
            if (!newPasswordField) {
                this.showToast('Error: Campo de contraseña no encontrado', 'error');
                return;
            }

            const newPassword = newPasswordField.value.trim();
            if (!newPassword) {
                this.showToast('Ingrese una nueva contraseña', 'warning');
                newPasswordField.focus();
                return;
            }

            if (newPassword.length < 8) {
                this.showToast('La contraseña debe tener al menos 8 caracteres', 'error');
                newPasswordField.focus();
                return;
            }

            // Validar formato de contraseña
            if (!this.validatePassword(newPassword)) {
                this.showToast('La contraseña debe contener al menos: 1 mayúscula, 1 minúscula, 1 número y 1 carácter especial', 'error');
                newPasswordField.focus();
                return;
            }

            // Confirmar acción
            const confirmed = confirm(`¿Está seguro de que desea actualizar la contraseña de ${this.currentUserData.firstName} ${this.currentUserData.lastName}?\n\nEl usuario deberá usar la nueva contraseña en el próximo inicio de sesión.`);
            if (!confirmed) return;

            this.showToast('Actualizando contraseña...', 'info');

            const result = await window.ResetPasswordService.resetUserPassword(
                this.currentUserId, 
                newPassword, 
                true, // Forzar cambio en próximo login
                'Actualización manual desde administración de usuarios'
            );

            if (result.success) {
                this.showToast('✅ Contraseña actualizada exitosamente', 'success');
                // Limpiar el campo
                newPasswordField.value = '';
                newPasswordField.type = 'password';
                
                // Resetear icono del ojo
                const toggleIcon = document.getElementById('toggle-new-password');
                if (toggleIcon) {
                    const icon = toggleIcon.querySelector('i');
                    if (icon) {
                        icon.className = 'fas fa-eye';
                    }
                }
            } else {
                this.showToast('❌ Error al actualizar contraseña: ' + result.message, 'error');
            }

        } catch (error) {
            console.error('❌ Error en reseteo de contraseña:', error);
            this.showToast('Error al actualizar contraseña: ' + error.message, 'error');
        }
    }

    /**
     * Valida el formato de la contraseña
     */
    validatePassword(password) {
        const hasUppercase = /[A-Z]/.test(password);
        const hasLowercase = /[a-z]/.test(password);
        const hasNumbers = /\d/.test(password);
        const hasSpecial = /[@$!%*?&]/.test(password);
        
        return hasUppercase && hasLowercase && hasNumbers && hasSpecial;
    }

    /**
     * Alterna la visibilidad de la contraseña
     */
    togglePasswordVisibility() {
        const passwordField = document.getElementById('new-password-input');
        const toggleIcon = document.getElementById('toggle-new-password');
        
        if (!passwordField || !toggleIcon) return;
        
        const icon = toggleIcon.querySelector('i');
        if (!icon) return;
        
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            passwordField.type = 'password';
            icon.className = 'fas fa-eye';
        }
    }

    /**
     * Muestra un mensaje toast
     */
    showToast(message, type = 'success') {
        if (window.GlobalToast) {
            window.GlobalToast.show(message, type);
        } else {
            // Fallback para tipos de mensaje
            const prefix = type === 'error' ? '❌' : type === 'warning' ? '⚠️' : type === 'info' ? 'ℹ️' : '✅';
            console.log(`${prefix} Toast: ${message} (${type})`);
        }
    }
}

// Hacer disponible globalmente
window.AssignRolesController = AssignRolesController;

// Inicializar al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    if (typeof window.assignRolesController === 'undefined') {
        window.assignRolesController = new AssignRolesController();
    }
});
