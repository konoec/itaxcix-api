// Controlador dedicado para los detalles de roles y gestión de permisos
// Se encarga de toda la lógica de gestión de permisos: carga, filtrado, renderizado y guardado

const RoleDetailsController = (() => {
    // Variables de estado
    let currentRoleId = null;
    let currentRoleName = '';
    let permissionsData = null; // Datos completos del servicio de permisos
    let filteredPermissions = [];
    let showOnlyAssigned = false; // Nueva variable para el filtro de asignados

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
        
        if (titleElement) titleElement.textContent = `Detalles del Rol: ${roleName}`;
        
        // Usar Bootstrap Modal API
        if (modal) {
            const modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();
        }
        
        // Limpiar formulario de búsqueda y filtros
        const searchInput = document.getElementById('search-permissions-modal');
        const assignedFilter = document.getElementById('filter-assigned');
        if (searchInput) searchInput.value = '';
        if (assignedFilter) assignedFilter.checked = false;
        showOnlyAssigned = false;
        
        // Cargar permisos usando el servicio especializado
        loadRolePermissions();
    }

    /**
     * Cierra el modal de asignación de permisos
     */
    function closePermissionAssignmentModal() {
        const modal = document.getElementById('role-details-modal');
        if (modal) {
            const modalInstance = bootstrap.Modal.getInstance(modal);
            if (modalInstance) {
                modalInstance.hide();
            }
        }
        
        // Limpiar estado
        currentRoleId = null;
        currentRoleName = '';
        permissionsData = null;
        filteredPermissions = [];
        showOnlyAssigned = false;
        
        console.log('✅ Modal de asignación de permisos cerrado');
    }

    /**
     * Carga todos los permisos del sistema y filtra por el tipo del rol
     * - Si el rol es Web: muestra solo permisos Web
     * - Si el rol es App: muestra solo permisos App
     * Luego marca como seleccionados los permisos ya asignados al rol
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
            console.log('📋 Cargando todos los permisos del sistema para el rol:', currentRoleId);
            
            // Verificar que los servicios estén disponibles
            if (!window.PermissionService) {
                throw new Error('El servicio global de permisos no está disponible');
            }
            if (!window.assignPermissionService) {
                throw new Error('El servicio de asignación de permisos no está disponible');
            }
            
            // 1. Obtener TODOS los permisos del sistema usando la misma lógica que el controlador de permisos
            console.log('🌐 Obteniendo todos los permisos del sistema...');
            
            // Cargar primera página de permisos
            const firstPageResult = await window.PermissionService.getPermissions(1, 100);
            
            if (!firstPageResult.success) {
                throw new Error(firstPageResult.message || 'Error al obtener los permisos del sistema');
            }
            
            // Verificar estructura de respuesta según como lo hace el controlador de permisos
            if (!firstPageResult.data || !firstPageResult.data.permissions) {
                throw new Error('Respuesta sin datos de permisos válidos');
            }
            
            // Empezar con los permisos de la primera página
            let allPermissions = [...firstPageResult.data.permissions];
            console.log(`✅ Primera página cargada: ${allPermissions.length} permisos`);
            
            // Si hay más páginas, cargar todas iterativamente (igual que el controlador de permisos)
            const totalPages = firstPageResult.data.totalPages || 1;
            if (totalPages > 1) {
                console.log(`📄 Cargando ${totalPages - 1} páginas adicionales de permisos...`);
                
                for (let page = 2; page <= totalPages; page++) {
                    const additionalResponse = await window.PermissionService.getPermissions(page, 100);
                    if (additionalResponse.success && additionalResponse.data && additionalResponse.data.permissions) {
                        allPermissions = allPermissions.concat(additionalResponse.data.permissions);
                        console.log(`✅ Página ${page} cargada, total: ${allPermissions.length} permisos`);
                    }
                }
            }
            
            // Normalizar estructura de permisos del sistema
            const normalizedPermissions = allPermissions.map(permission => ({
                id: permission.id,
                name: permission.name,
                active: permission.active,
                web: permission.web,
                type: permission.web ? 'Web' : 'App'
            }));
            
            // 2. Obtener información del rol y sus permisos asignados
            console.log('👤 Obteniendo permisos asignados al rol...');
            const roleResult = await window.assignPermissionService.getRoleWithPermissions(currentRoleId);
            
            if (!roleResult.success) {
                throw new Error(roleResult.error || 'Error al obtener los datos del rol');
            }
            
            const roleData = roleResult.data;
            const assignedPermissions = roleData.permissions || [];
            const assignedIds = assignedPermissions.map(p => p.id.toString());
            
            // 3. Filtrar permisos por tipo según el rol (Web o App)
            const roleIsWeb = roleData.web;
            const filteredPermissionsByType = normalizedPermissions.filter(permission => {
                // Si el rol es Web, mostrar solo permisos Web
                // Si el rol es App, mostrar solo permisos App
                return permission.web === roleIsWeb;
            });
            
            console.log(`🔍 Filtrando permisos por tipo de rol (${roleIsWeb ? 'Web' : 'App'}):`, {
                totalPermissions: normalizedPermissions.length,
                filteredPermissions: filteredPermissionsByType.length,
                roleType: roleIsWeb ? 'Web' : 'App'
            });
            
            // 4. Combinar datos para mostrar solo los permisos del tipo correcto
            permissionsData = {
                roleInfo: {
                    id: roleData.id,
                    name: roleData.name,
                    active: roleData.active,
                    web: roleData.web
                },
                availablePermissions: filteredPermissionsByType,
                assignedPermissionIds: assignedIds
            };
            
            console.log('✅ Datos combinados exitosamente:', {
                role: permissionsData.roleInfo.name,
                roleType: roleIsWeb ? 'Web' : 'App',
                availablePermissionsForRoleType: filteredPermissionsByType.length,
                assignedToRole: assignedIds.length
            });
            
            // Aplicar filtro inicial (mostrar todos)
            filterPermissions('');
            
        } catch (error) {
            console.error('❌ Error cargando permisos:', error);
            
            // Mostrar mensaje de error al usuario
            if (noPermissionsMessage) {
                noPermissionsMessage.textContent = 'Error al cargar los permisos. Verifique su conexión e intente nuevamente.';
                noPermissionsMessage.style.display = 'block';
            }
            
            // Mostrar toast de error
            if (typeof window.showToast === 'function') {
                window.showToast('Error al cargar los permisos del sistema. Verifique su conexión.', 'error');
            }
            
        } finally {
            if (loadingIndicator) loadingIndicator.style.display = 'none';
        }
    }

    /**
     * Filtra los permisos disponibles según el término de búsqueda y filtros activos
     * @param {string} searchTerm - Término de búsqueda
     */
    function filterPermissions(searchTerm) {
        if (!permissionsData) {
            console.warn('⚠️ No hay datos de permisos disponibles para filtrar');
            return;
        }
        
        console.log('🔍 Filtrando permisos con término:', searchTerm, 'showOnlyAssigned:', showOnlyAssigned);
        
        // Primero filtrar por búsqueda de texto usando el método del servicio
        let textFilteredPermissions = window.assignPermissionService.filterPermissions(
            permissionsData.availablePermissions, 
            searchTerm
        );
        
        // Luego aplicar filtro de asignados si está activo
        if (showOnlyAssigned) {
            filteredPermissions = textFilteredPermissions.filter(permission => 
                permissionsData.assignedPermissionIds.includes(permission.id.toString())
            );
            console.log('🎯 Aplicando filtro de asignados:', filteredPermissions.length, 'de', textFilteredPermissions.length);
        } else {
            filteredPermissions = textFilteredPermissions;
        }
        
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
        const saveBtn = document.getElementById('save-role-permissions');
        const searchInput = document.getElementById('search-permissions-modal');
        const assignedFilter = document.getElementById('filter-assigned');
        const permissionsList = document.getElementById('permissions-assignment-list');
        
        // Bootstrap maneja automáticamente los eventos de cerrar modal
        // Solo configuramos los eventos específicos de funcionalidad
        
        // Evento: Búsqueda de permisos
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                filterPermissions(e.target.value);
            });
        }
        
        // Evento: Filtro de permisos asignados
        if (assignedFilter) {
            assignedFilter.addEventListener('change', (e) => {
                handleAssignedFilterToggle(e.target.checked);
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

    /**
     * Maneja el cambio en el filtro de permisos asignados
     * @param {boolean} showAssignedOnly - Si debe mostrar solo los asignados
     */
    function handleAssignedFilterToggle(showAssignedOnly) {
        showOnlyAssigned = showAssignedOnly;
        console.log('🔄 Filtro de asignados:', showAssignedOnly ? 'activado' : 'desactivado');
        
        // Reaplicar filtros con el término de búsqueda actual
        const searchInput = document.getElementById('search-permissions-modal');
        const currentSearchTerm = searchInput ? searchInput.value : '';
        
        filterPermissions(currentSearchTerm);
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
    if (typeof RoleDetailsController !== 'undefined') {
        RoleDetailsController.init();
    }
});

// Exponer globalmente para que otros módulos puedan usar
window.RoleDetailsController = RoleDetailsController;
