// Controlador dedicado para los detalles de roles y gestión de permisos
// Se encarga de toda la lógica de gestión de permisos: carga, filtrado, renderizado y guardado

const RoleDetailsController = (() => {
    // Variables de estado
    let currentRoleId = null;
    let currentRoleName = '';
    let permissionsData = null; // Datos completos del servicio de permisos
    let filteredPermissions = [];

    /**
     * Inicializa el controlador y configura eventos del modal de asignación de permisos
     */
    function init() {
        console.log('🚀 RoleDetailsController inicializado');
        setupModalEvents();
    }

    /**
     * Abre el modal de asignación de permisos para un rol específico
     * @param {string|number} roleId - ID del rol
     * @param {string} roleName - Nombre del rol
     */
    function openPermissionAssignmentModal(roleId, roleName) {
        console.log('🔍 Abriendo modal de asignación de permisos para rol:', { id: roleId, name: roleName });
        
        currentRoleId = roleId;
        currentRoleName = roleName;
        
        const modal = document.getElementById('role-details-modal');
        const titleElement = document.getElementById('role-details-title');
        
        if (titleElement) titleElement.textContent = roleName;
        if (modal) modal.style.display = 'block';
        
        // Limpiar formulario de búsqueda
        const searchInput = document.getElementById('search-permissions-modal');
        if (searchInput) searchInput.value = '';
        
        // Cargar permisos usando el servicio especializado
        loadRolePermissions();
    }

    /**
     * Cierra el modal de asignación de permisos
     */
    function closePermissionAssignmentModal() {
        const modal = document.getElementById('role-details-modal');
        if (modal) modal.style.display = 'none';
        
        // Limpiar estado
        currentRoleId = null;
        currentRoleName = '';
        permissionsData = null;
        filteredPermissions = [];
        
        console.log('✅ Modal de asignación de permisos cerrado');
    }

    /**
     * Carga los permisos del rol usando el servicio especializado
     */
    async function loadRolePermissions() {
        const loadingIndicator = document.getElementById('permissions-assignment-loading');
        const permissionsList = document.getElementById('permissions-assignment-list');
        const noPermissionsMessage = document.getElementById('no-permissions-message');
        
        // Mostrar indicador de carga
        if (loadingIndicator) loadingIndicator.style.display = 'block';
        if (permissionsList) permissionsList.style.display = 'none';
        if (noPermissionsMessage) noPermissionsMessage.style.display = 'none';
        
        try {
            console.log('📋 Cargando permisos para gestión del rol:', currentRoleId);
            
            // Verificar que el servicio esté disponible
            if (!window.assignPermissionService) {
                throw new Error('El servicio de asignación de permisos no está disponible');
            }
            
            // Usar el servicio para obtener datos completos de permisos
            const result = await window.assignPermissionService.getRolePermissionsForManagement(currentRoleId);
            
            if (result.success && result.data) {
                permissionsData = result.data;
                
                console.log('✅ Permisos cargados del servicio:', {
                    role: permissionsData.roleInfo.name,
                    totalPermissions: permissionsData.availablePermissions.length,
                    assignedPermissions: permissionsData.assignedPermissionIds.length
                });
                
                // Aplicar filtro inicial (mostrar todos)
                filterPermissions('');
                
            } else {
                throw new Error(result.error || 'Error al obtener los permisos del rol');
            }
            
        } catch (error) {
            console.error('❌ Error cargando permisos:', error);
            
            // Mostrar mensaje de error al usuario
            if (noPermissionsMessage) {
                noPermissionsMessage.textContent = 'Error al cargar los permisos del rol. Verifique su conexión e intente nuevamente.';
                noPermissionsMessage.style.display = 'block';
            }
            
            // Mostrar toast de error
            if (typeof window.showToast === 'function') {
                window.showToast('Error al cargar los permisos. Verifique su conexión.', 'error');
            }
            
        } finally {
            if (loadingIndicator) loadingIndicator.style.display = 'none';
        }
    }

    /**
     * Filtra los permisos disponibles según el término de búsqueda
     * @param {string} searchTerm - Término de búsqueda
     */
    function filterPermissions(searchTerm) {
        if (!permissionsData) {
            console.warn('⚠️ No hay datos de permisos disponibles para filtrar');
            return;
        }
        
        console.log('🔍 Filtrando permisos con término:', searchTerm);
        
        // Usar el método del servicio para filtrar
        filteredPermissions = window.assignPermissionService.filterPermissions(
            permissionsData.availablePermissions, 
            searchTerm
        );
        
        console.log('✅ Permisos filtrados:', filteredPermissions.length, 'de', permissionsData.availablePermissions.length);
        
        renderPermissionsList();
    }

    /**
     * Renderiza la lista de permisos en el modal
     */
    function renderPermissionsList() {
        const permissionsList = document.getElementById('permissions-assignment-list');
        const noPermissionsMessage = document.getElementById('no-permissions-message');
        
        if (!permissionsList || !permissionsData) {
            console.warn('⚠️ No se puede renderizar: lista de permisos o datos no disponibles');
            return;
        }
        
        // Limpiar lista actual
        permissionsList.innerHTML = '';
        
        // Verificar si hay permisos filtrados
        if (filteredPermissions.length === 0) {
            permissionsList.style.display = 'none';
            if (noPermissionsMessage) {
                noPermissionsMessage.textContent = 'No se encontraron permisos que coincidan con la búsqueda';
                noPermissionsMessage.style.display = 'block';
            }
            return;
        }
        
        // Ocultar mensaje de "no hay permisos" y mostrar lista
        if (noPermissionsMessage) noPermissionsMessage.style.display = 'none';
        permissionsList.style.display = 'block';
        
        // Renderizar cada permiso
        filteredPermissions.forEach(permission => {
            const isAssigned = permissionsData.assignedPermissionIds.includes(permission.id.toString());
            
            const permissionItem = document.createElement('div');
            permissionItem.className = 'permission-assignment-item';
            permissionItem.innerHTML = `
                <div class="permission-name">${permission.name}</div>
                <div class="permission-type">
                    <span class="type-badge ${permission.type.toLowerCase()}">${permission.type}</span>
                </div>
                <div class="permission-checkbox-container">
                    <input type="checkbox" 
                           class="permission-checkbox" 
                           data-permission-id="${permission.id}"
                           ${isAssigned ? 'checked' : ''}>
                </div>
            `;
            
            permissionsList.appendChild(permissionItem);
        });
        
        console.log('✅ Lista de permisos renderizada:', filteredPermissions.length, 'items');
    }

    /**
     * Maneja el cambio de estado de un checkbox de permiso
     * @param {string} permissionId - ID del permiso
     * @param {boolean} isChecked - Estado del checkbox
     */
    function handlePermissionToggle(permissionId, isChecked) {
        if (!permissionsData) {
            console.warn('⚠️ No hay datos de permisos disponibles para actualizar');
            return;
        }
        
        console.log('🔄 Cambiando estado del permiso:', { id: permissionId, checked: isChecked });
        
        if (isChecked) {
            // Agregar permiso si no está ya asignado
            if (!permissionsData.assignedPermissionIds.includes(permissionId)) {
                permissionsData.assignedPermissionIds.push(permissionId);
            }
        } else {
            // Remover permiso si está asignado
            const index = permissionsData.assignedPermissionIds.indexOf(permissionId);
            if (index > -1) {
                permissionsData.assignedPermissionIds.splice(index, 1);
            }
        }
        
        console.log('✅ Permisos actualizados:', permissionsData.assignedPermissionIds.length, 'permisos asignados');
    }

    /**
     * Guarda los cambios de permisos del rol usando el servicio
     * @param {Function} onSuccess - Callback ejecutado tras guardado exitoso
     */
    async function saveRolePermissions(onSuccess) {
        if (!permissionsData) {
            console.error('❌ No hay datos de permisos para guardar');
            if (typeof window.showToast === 'function') {
                window.showToast('No hay cambios para guardar', 'warning');
            }
            return;
        }
        
        console.log('💾 Guardando permisos del rol:', {
            roleId: currentRoleId,
            roleName: currentRoleName,
            permissions: permissionsData.assignedPermissionIds
        });
        
        try {
            // Usar el servicio con validación para actualizar los permisos
            const result = await window.assignPermissionService.updateRolePermissionsValidated(
                currentRoleId, 
                permissionsData.assignedPermissionIds,
                permissionsData.availablePermissions
            );
            
            if (result.success) {
                const successMessage = result.message || `Permisos del rol "${currentRoleName}" actualizados correctamente`;
                
                console.log('✅ Permisos guardados exitosamente');
                
                // Mostrar mensaje de éxito
                if (typeof window.showToast === 'function') {
                    window.showToast(successMessage, 'success');
                } else {
                    alert(successMessage);
                }
                
                // Limpiar cache del servicio para forzar recarga
                window.assignPermissionService.clearCache();
                
                // Cerrar modal
                closePermissionAssignmentModal();
                
                // Ejecutar callback de éxito si se proporciona
                if (typeof onSuccess === 'function') {
                    onSuccess();
                }
                
            } else {
                throw new Error(result.error || 'Error al guardar los permisos');
            }
            
        } catch (error) {
            console.error('❌ Error guardando permisos:', error);
            
            const errorMessage = `Error al guardar los permisos del rol "${currentRoleName}": ${error.message}`;
            
            if (typeof window.showToast === 'function') {
                window.showToast(errorMessage, 'error');
            } else {
                alert(errorMessage);
            }
        }
    }

    /**
     * Configura todos los eventos del modal de asignación de permisos
     */
    function setupModalEvents() {
        const modal = document.getElementById('role-details-modal');
        const closeBtn = document.getElementById('close-role-details-modal');
        const cancelBtn = document.getElementById('cancel-role-details');
        const saveBtn = document.getElementById('save-role-permissions');
        const searchInput = document.getElementById('search-permissions-modal');
        const permissionsList = document.getElementById('permissions-assignment-list');
        
        // Evento: Cerrar modal con X
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                closePermissionAssignmentModal();
            });
        }
        
        // Evento: Cerrar modal con botón Cancelar
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                closePermissionAssignmentModal();
            });
        }
        
        // Evento: Cerrar modal al hacer click fuera
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    closePermissionAssignmentModal();
                }
            });
        }
        
        // Evento: Búsqueda de permisos
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                filterPermissions(e.target.value);
            });
        }
        
        // Evento: Manejar cambios en checkboxes de permisos
        if (permissionsList) {
            permissionsList.addEventListener('change', (e) => {
                if (e.target.classList.contains('permission-checkbox')) {
                    const permissionId = e.target.getAttribute('data-permission-id');
                    const isChecked = e.target.checked;
                    
                    handlePermissionToggle(permissionId, isChecked);
                }
            });
        }
        
        // Evento: Guardar cambios
        if (saveBtn) {
            saveBtn.addEventListener('click', async () => {
                // Pasar función callback para recargar datos tras guardado exitoso
                await saveRolePermissions(() => {
                    // Notificar al controlador de roles para que recargue si es necesario
                    if (typeof window.RolesController !== 'undefined' && 
                        typeof window.RolesController.refreshCurrentView === 'function') {
                        window.RolesController.refreshCurrentView();
                    }
                });
            });
        }
        
        console.log('✅ Eventos del modal de asignación de permisos configurados');
    }

    /**
     * Obtiene el estado actual de los permisos (para debugging)
     * @returns {Object} Estado actual de permisos
     */
    function getCurrentPermissionsState() {
        return {
            roleId: currentRoleId,
            roleName: currentRoleName,
            hasData: !!permissionsData,
            totalPermissions: permissionsData ? permissionsData.availablePermissions.length : 0,
            assignedPermissions: permissionsData ? permissionsData.assignedPermissionIds.length : 0,
            filteredPermissions: filteredPermissions.length
        };
    }

    // API pública del controlador
    return {
        init,
        openPermissionAssignmentModal,
        closePermissionAssignmentModal,
        getCurrentPermissionsState
    };
})();

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    if (typeof AssignPermissionController !== 'undefined') {
        AssignPermissionController.init();
    }
});

// Exponer globalmente para que otros módulos puedan usar
window.AssignPermissionController = AssignPermissionController;
