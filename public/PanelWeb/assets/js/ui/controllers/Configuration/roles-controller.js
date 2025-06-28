/**
 * Controlador para la gestión de roles
 * Maneja todas las funcionalidades relacionadas con roles del sistema
 */
class RolesController {
    constructor() {
        this.isInitialized = false;
        this.roles = [];
        this.permissions = [];
        
        // Referencias a elementos del DOM para roles
        this.rolesCard = document.getElementById('card-roles');
        this.rolesContainer = document.getElementById('roles-container');
        this.rolesModal = document.getElementById('roles-modal');
        this.createRoleBtn = document.getElementById('create-role');
        this.listRolesBtn = document.getElementById('list-roles');
        this.updateRoleBtn = document.getElementById('update-role');
        this.deleteRoleBtn = document.getElementById('delete-role');
        
        // Referencias para formularios de roles
        this.roleForm = document.getElementById('role-form');
        this.roleIdInput = document.getElementById('role-id');
        this.roleNameInput = document.getElementById('role-name');
        this.roleCodeInput = document.getElementById('role-code');
        this.roleDescriptionInput = document.getElementById('role-description');
        this.rolePermissionsSelect = document.getElementById('role-permissions');
        this.roleActiveInput = document.getElementById('role-active');
        
        // Inicializar
        this.init();
    }

    /**
     * Inicializa el controlador
     */
    async init() {
        console.log('👥 Inicializando RolesController...');
        try {
            this.setupEventListeners();
            await this.loadPermissions(); // Cargar permisos disponibles para asignar a roles
            this.isInitialized = true;
            console.log('✅ RolesController inicializado correctamente');
        } catch (error) {
            console.error('❌ Error al inicializar RolesController:', error);
        }
    }

    /**
     * Configura los event listeners para las acciones de roles
     */
    setupEventListeners() {
        if (this.createRoleBtn) {
            this.createRoleBtn.addEventListener('click', () => {
                this.handleCreateRole();
            });
        }

        if (this.listRolesBtn) {
            this.listRolesBtn.addEventListener('click', () => {
                this.handleListRoles();
            });
        }

        if (this.updateRoleBtn) {
            this.updateRoleBtn.addEventListener('click', () => {
                this.handleUpdateRole();
            });
        }

        if (this.deleteRoleBtn) {
            this.deleteRoleBtn.addEventListener('click', () => {
                this.handleDeleteRole();
            });
        }

        // Event listener para el formulario de roles
        if (this.roleForm) {
            this.roleForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.saveRole();
            });
        }

        // Cerrar modal de roles
        if (this.rolesModal) {
            const closeBtn = this.rolesModal.querySelector('.close');
            if (closeBtn) {
                closeBtn.addEventListener('click', () => {
                    this.closeRolesModal();
                });
            }

            // Cerrar modal al hacer clic fuera
            this.rolesModal.addEventListener('click', (e) => {
                if (e.target === this.rolesModal) {
                    this.closeRolesModal();
                }
            });
        }
    }

    // ===== HANDLERS PARA ACCIONES DE ROLES =====

    /**
     * Maneja la creación de un nuevo rol
     */
    handleCreateRole() {
        console.log('👥 Crear nuevo rol');
        this.openRolesModal('create');
    }

    /**
     * Maneja el listado de roles
     */
    async handleListRoles() {
        console.log('📋 Listar roles');
        try {
            await this.loadRoles();
            this.displayRolesList();
        } catch (error) {
            console.error('❌ Error al cargar roles:', error);
            this.showToast('Error al cargar los roles', 'error');
        }
    }

    /**
     * Maneja la actualización de un rol
     */
    handleUpdateRole() {
        console.log('✏️ Actualizar rol');
        const selectedRole = this.getSelectedRole();
        if (selectedRole) {
            this.openRolesModal('edit', selectedRole);
        } else {
            this.showToast('Selecciona un rol para actualizar', 'warning');
        }
    }

    /**
     * Maneja la eliminación de un rol
     */
    async handleDeleteRole() {
        console.log('🗑️ Eliminar rol');
        const selectedRole = this.getSelectedRole();
        if (selectedRole) {
            const confirmed = confirm(`¿Estás seguro de que deseas eliminar el rol "${selectedRole.name}"?`);
            if (confirmed) {
                await this.deleteRole(selectedRole.id);
            }
        } else {
            this.showToast('Selecciona un rol para eliminar', 'warning');
        }
    }

    // ===== MÉTODOS DE GESTIÓN DE ROLES =====

    /**
     * Abre el modal de roles
     * @param {string} mode - Modo: 'create' o 'edit'
     * @param {Object} role - Datos del rol (para modo edit)
     */
    openRolesModal(mode, role = null) {
        if (!this.rolesModal) {
            this.showToast('Modal de roles no encontrado', 'error');
            return;
        }

        // Configurar el modal según el modo
        const modalTitle = this.rolesModal.querySelector('.modal-title');
        if (modalTitle) {
            modalTitle.textContent = mode === 'create' ? 'Crear Nuevo Rol' : 'Editar Rol';
        }

        // Cargar permisos disponibles en el select
        this.populatePermissionsSelect();

        // Limpiar o llenar el formulario
        if (mode === 'create') {
            this.clearRoleForm();
        } else if (mode === 'edit' && role) {
            this.fillRoleForm(role);
        }

        // Mostrar el modal
        this.rolesModal.style.display = 'block';
    }

    /**
     * Cierra el modal de roles
     */
    closeRolesModal() {
        if (this.rolesModal) {
            this.rolesModal.style.display = 'none';
            this.clearRoleForm();
        }
    }

    /**
     * Limpia el formulario de roles
     */
    clearRoleForm() {
        if (this.roleIdInput) this.roleIdInput.value = '';
        if (this.roleNameInput) this.roleNameInput.value = '';
        if (this.roleCodeInput) this.roleCodeInput.value = '';
        if (this.roleDescriptionInput) this.roleDescriptionInput.value = '';
        if (this.roleActiveInput) this.roleActiveInput.checked = true;
        if (this.rolePermissionsSelect) {
            // Desmarcar todas las opciones
            Array.from(this.rolePermissionsSelect.options).forEach(option => {
                option.selected = false;
            });
        }
    }

    /**
     * Llena el formulario con datos de un rol
     * @param {Object} role - Datos del rol
     */
    fillRoleForm(role) {
        if (this.roleIdInput) this.roleIdInput.value = role.id || '';
        if (this.roleNameInput) this.roleNameInput.value = role.name || '';
        if (this.roleCodeInput) this.roleCodeInput.value = role.code || '';
        if (this.roleDescriptionInput) this.roleDescriptionInput.value = role.description || '';
        if (this.roleActiveInput) this.roleActiveInput.checked = role.active !== false;
        
        if (this.rolePermissionsSelect && role.permissions) {
            // Marcar los permisos del rol
            Array.from(this.rolePermissionsSelect.options).forEach(option => {
                option.selected = role.permissions.some(p => 
                    p.id == option.value || p.code === option.value
                );
            });
        }
    }

    /**
     * Puebla el select de permisos
     */
    populatePermissionsSelect() {
        if (!this.rolePermissionsSelect) return;

        const html = this.permissions.map(permission => 
            `<option value="${permission.id}">${permission.name} (${permission.code})</option>`
        ).join('');

        this.rolePermissionsSelect.innerHTML = html;
    }

    /**
     * Guarda un rol (crear o actualizar)
     */
    async saveRole() {
        const selectedPermissions = Array.from(this.rolePermissionsSelect?.selectedOptions || [])
            .map(option => parseInt(option.value));

        const roleData = {
            id: this.roleIdInput?.value || null,
            name: this.roleNameInput?.value?.trim(),
            code: this.roleCodeInput?.value?.trim(),
            description: this.roleDescriptionInput?.value?.trim(),
            active: this.roleActiveInput?.checked !== false,
            permissions: selectedPermissions
        };

        // Validar datos
        const validation = this.validateRoleData(roleData);
        if (!validation.isValid) {
            this.showToast(validation.errors.join(', '), 'error');
            return;
        }

        try {
            let response;
            if (roleData.id) {
                // Actualizar rol existente
                response = await this.updateRoleData(roleData);
            } else {
                // Crear nuevo rol
                response = await this.createRoleData(roleData);
            }

            if (response.success) {
                this.showToast('Rol guardado exitosamente', 'success');
                this.closeRolesModal();
                await this.loadRoles(); // Recargar la lista
            } else {
                this.showToast('Error al guardar el rol', 'error');
            }
        } catch (error) {
            console.error('❌ Error al guardar rol:', error);
            this.showToast('Error de conexión al guardar el rol', 'error');
        }
    }

    /**
     * Valida los datos de un rol
     * @param {Object} roleData - Datos del rol
     * @returns {Object} {isValid: boolean, errors: Array}
     */
    validateRoleData(roleData) {
        const errors = [];

        if (!roleData.name) {
            errors.push('El nombre es requerido');
        }

        if (!roleData.code) {
            errors.push('El código es requerido');
        }

        // Verificar que el código sea único (en contexto real)
        if (roleData.code && !roleData.id) {
            const existingRole = this.roles.find(r => r.code === roleData.code);
            if (existingRole) {
                errors.push('El código ya existe');
            }
        }

        return {
            isValid: errors.length === 0,
            errors
        };
    }

    /**
     * Carga la lista de roles desde la API
     */
    async loadRoles() {
        console.log('🔄 Cargando roles desde API...');
        
        // Datos de ejemplo (reemplazar con llamada real a la API)
        this.roles = [
            {
                id: 1,
                name: 'Administrador',
                code: 'admin',
                description: 'Acceso completo al sistema',
                active: true,
                permissions: [
                    { id: 1, name: 'Ver Usuarios', code: 'users.view' },
                    { id: 2, name: 'Crear Usuarios', code: 'users.create' },
                    { id: 3, name: 'Editar Configuración', code: 'config.edit' }
                ]
            },
            {
                id: 2,
                name: 'Editor',
                code: 'editor',
                description: 'Puede editar contenido pero no administrar usuarios',
                active: true,
                permissions: [
                    { id: 1, name: 'Ver Usuarios', code: 'users.view' },
                    { id: 3, name: 'Editar Configuración', code: 'config.edit' }
                ]
            },
            {
                id: 3,
                name: 'Visualizador',
                code: 'viewer',
                description: 'Solo puede ver información',
                active: true,
                permissions: [
                    { id: 1, name: 'Ver Usuarios', code: 'users.view' }
                ]
            }
        ];

        console.log('✅ Roles cargados:', this.roles);
    }

    /**
     * Carga la lista de permisos disponibles
     */
    async loadPermissions() {
        console.log('🔄 Cargando permisos disponibles...');
        
        // Datos de ejemplo (reemplazar con llamada real a la API)
        this.permissions = [
            {
                id: 1,
                name: 'Ver Usuarios',
                code: 'users.view',
                description: 'Permite ver la lista de usuarios'
            },
            {
                id: 2,
                name: 'Crear Usuarios',
                code: 'users.create',
                description: 'Permite crear nuevos usuarios'
            },
            {
                id: 3,
                name: 'Editar Configuración',
                code: 'config.edit',
                description: 'Permite editar la configuración del sistema'
            },
            {
                id: 4,
                name: 'Eliminar Usuarios',
                code: 'users.delete',
                description: 'Permite eliminar usuarios'
            },
            {
                id: 5,
                name: 'Gestionar Roles',
                code: 'roles.manage',
                description: 'Permite gestionar roles del sistema'
            }
        ];

        console.log('✅ Permisos disponibles cargados:', this.permissions);
    }

    /**
     * Muestra la lista de roles en la interfaz
     */
    displayRolesList() {
        if (!this.rolesContainer) {
            console.warn('⚠️ Contenedor de roles no encontrado');
            return;
        }

        const html = `
            <div class="roles-list">
                <h4>Lista de Roles</h4>
                <div class="roles-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Seleccionar</th>
                                <th>Nombre</th>
                                <th>Código</th>
                                <th>Estado</th>
                                <th>Permisos</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${this.roles.map(role => `
                                <tr>
                                    <td>
                                        <input type="radio" name="selected-role" value="${role.id}" />
                                    </td>
                                    <td>${role.name}</td>
                                    <td>${role.code}</td>
                                    <td>
                                        <span class="status ${role.active ? 'active' : 'inactive'}">
                                            ${role.active ? 'Activo' : 'Inactivo'}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="permissions-list">
                                            ${role.permissions.map(p => `<span class="permission-tag">${p.name}</span>`).join('')}
                                        </div>
                                    </td>
                                    <td>${role.description || '-'}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        `;

        this.rolesContainer.innerHTML = html;
    }

    /**
     * Obtiene el rol seleccionado
     * @returns {Object|null} Rol seleccionado o null
     */
    getSelectedRole() {
        const selectedRadio = document.querySelector('input[name="selected-role"]:checked');
        if (selectedRadio) {
            const roleId = parseInt(selectedRadio.value);
            return this.roles.find(r => r.id === roleId);
        }
        return null;
    }

    /**
     * Crea un nuevo rol (llamada a la API)
     * @param {Object} roleData - Datos del rol
     * @returns {Object} Respuesta de la API
     */
    async createRoleData(roleData) {
        console.log('➕ Creando rol:', roleData);
        // Aquí harías la llamada real a la API
        // const response = await RoleService.create(roleData);
        
        // Simulación
        return { success: true, data: { id: Date.now(), ...roleData } };
    }

    /**
     * Actualiza un rol existente (llamada a la API)
     * @param {Object} roleData - Datos del rol
     * @returns {Object} Respuesta de la API
     */
    async updateRoleData(roleData) {
        console.log('✏️ Actualizando rol:', roleData);
        // Aquí harías la llamada real a la API
        // const response = await RoleService.update(roleData.id, roleData);
        
        // Simulación
        return { success: true, data: roleData };
    }

    /**
     * Elimina un rol (llamada a la API)
     * @param {number} roleId - ID del rol
     * @returns {Object} Respuesta de la API
     */
    async deleteRole(roleId) {
        console.log('🗑️ Eliminando rol:', roleId);
        try {
            // Aquí harías la llamada real a la API
            // const response = await RoleService.delete(roleId);
            
            // Simulación
            const response = { success: true };
            
            if (response.success) {
                this.showToast('Rol eliminado exitosamente', 'success');
                await this.loadRoles(); // Recargar la lista
                this.displayRolesList();
            } else {
                this.showToast('Error al eliminar el rol', 'error');
            }
        } catch (error) {
            console.error('❌ Error al eliminar rol:', error);
            this.showToast('Error de conexión al eliminar el rol', 'error');
        }
    }

    /**
     * Muestra una notificación toast
     * @param {string} message - Mensaje a mostrar
     * @param {string} type - Tipo de notificación (success, error, info, warning)
     */
    showToast(message, type = 'info') {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toast-message');
        
        if (toast && toastMessage) {
            // Configurar el mensaje
            toastMessage.textContent = message;
            
            // Remover clases de tipo anteriores
            toast.classList.remove('success', 'error', 'info', 'warning');
            
            // Agregar la clase del tipo correspondiente
            toast.classList.add(type);
            
            // Mostrar el toast
            toast.classList.add('show');
            
            // Ocultar después de 3 segundos
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }
    }
}

// Exportar para uso global
window.RolesController = RolesController;

// Auto-inicialización solo si estamos en la página de configuración
document.addEventListener('DOMContentLoaded', () => {
    // Verificar si estamos en la página de configuración buscando elementos específicos
    const rolesElements = document.getElementById('card-roles') || 
                         document.getElementById('create-role') ||
                         document.getElementById('list-roles');
    
    if (rolesElements && !window.rolesController) {
        window.rolesController = new RolesController();
        console.log('👥 RolesController auto-inicializado');
    }
});
