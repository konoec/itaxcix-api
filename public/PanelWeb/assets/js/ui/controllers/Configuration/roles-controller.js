// Controlador para la gestión de roles
// Basado en la lógica de permisos, con paginación y listado

const RolesController = (() => {
    const API_URL = 'https://149.130.161.148/api/v1/admin/role/list';
    const tableBody = document.getElementById('roles-list');
    const loadingRow = document.getElementById('roles-loading-row');
    const paginationInfo = document.getElementById('pagination-info');
    const prevPageBtn = document.getElementById('prev-page');
    const nextPageBtn = document.getElementById('next-page');

    let currentPage = 1;
    let perPage = 9;
    let lastPage = 1;
    let total = 0;
    
    // Variables para búsqueda dinámica y filtros
    let allRoles = []; // Todos los datos cargados
    let filteredRoles = []; // Datos filtrados por búsqueda y filtros
    let currentSearchTerm = ''; // Término de búsqueda actual
    
    // Filtros adicionales
    let activeFilters = {
        status: [], // ['active', 'inactive']
        type: []    // ['web', 'app']
    };

    function showLoading(show) {
        if (loadingRow) loadingRow.style.display = show ? '' : 'none';
        const rolesList = document.getElementById('roles-list');
        if (rolesList) rolesList.style.display = show ? 'none' : '';
    }

    function renderRows(items) {
        console.log('🔄 renderRows() iniciado');
        console.log('📋 Items recibidos:', items ? items.length : 'null/undefined');
        console.log('🔍 tableBody elemento:', tableBody ? 'Encontrado' : 'NO ENCONTRADO');
        
        if (!tableBody) {
            console.error('❌ ERROR: tableBody no encontrado. Elemento con ID "roles-list" no existe.');
            return;
        }
        
        tableBody.innerHTML = '';
        if (!items || items.length === 0) {
            console.log('⚠️ No hay items para mostrar');
            
            // Si hay filtros activos, mostrar mensaje de no resultados con filtros
            if (currentSearchTerm || activeFilters.status.length > 0 || activeFilters.type.length > 0) {
                const filterInfo = [];
                
                if (currentSearchTerm) {
                    filterInfo.push(`búsqueda: "${currentSearchTerm}"`);
                }
                
                if (activeFilters.status.length > 0) {
                    const statusLabels = activeFilters.status.map(s => s === 'active' ? 'Activo' : 'Inactivo');
                    filterInfo.push(`estado: [${statusLabels.join(', ')}]`);
                }
                
                if (activeFilters.type.length > 0) {
                    const typeLabels = activeFilters.type.map(t => t === 'web' ? 'Web' : 'App');
                    filterInfo.push(`tipo: [${typeLabels.join(', ')}]`);
                }
                
                const filtersText = filterInfo.join(', ');
                
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="no-results-message">
                            <i class="fas fa-search"></i>
                            <h3>No se encontraron resultados</h3>
                            <p>No hay roles que coincidan con los filtros: <span class="search-term">${filtersText}</span></p>
                            <button onclick="RolesController.clearAllFiltersPublic()" class="btn-clear-filters">
                                <i class="fas fa-times"></i> Limpiar filtros
                            </button>
                        </td>
                    </tr>
                `;
            } else {
                tableBody.innerHTML = `<tr><td colspan="5" style="text-align:center;">No hay roles para mostrar</td></tr>`;
            }
            return;
        }
        
        console.log('🔄 Generando filas HTML...');
        items.forEach((role, index) => {
            console.log(`   Rol ${index + 1}:`, role);
            const tr = document.createElement('tr');
            tr.setAttribute('data-id', role.id);
            tr.innerHTML = `
                <td>${role.name}</td>
                <td><span class="status-badge ${role.active ? 'active' : 'inactive'}">${role.active ? 'Activo' : 'Inactivo'}</span></td>
                <td><span class="type-badge ${role.web ? 'web' : 'system'}">${role.web ? 'Web' : 'App'}</span></td>
                <td>
                    <button class="details-btn" title="Ver detalles">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="action-btn edit" title="Editar">
                            <i class="fas fa-pen"></i> Editar
                        </button>
                        <button class="action-btn delete" title="Eliminar">
                            <i class="fas fa-trash-alt"></i> Eliminar
                        </button>
                    </div>
                </td>
            `;
            tableBody.appendChild(tr);
        });
        
        console.log('✅ renderRows() completado -', items.length, 'filas agregadas');
    }

    // Nueva función para actualizar información de paginación con datos de la API
    function updatePaginationInfo() {
        if (paginationInfo) {
            const start = total === 0 ? 0 : (perPage * (currentPage - 1)) + 1;
            const end = Math.min(perPage * currentPage, total);
            
            let infoText = `Mostrando ${start}-${end} de ${total} roles`;
            
            // Agregar información de filtros activos
            const filterInfo = [];
            
            if (currentSearchTerm) {
                filterInfo.push(`búsqueda: "${currentSearchTerm}"`);
            }
            
            if (activeFilters.status.length > 0) {
                const statusLabels = activeFilters.status.map(s => s === 'active' ? 'Activo' : 'Inactivo');
                filterInfo.push(`estado: [${statusLabels.join(', ')}]`);
            }
            
            if (activeFilters.type.length > 0) {
                const typeLabels = activeFilters.type.map(t => t === 'web' ? 'Web' : 'App');
                filterInfo.push(`tipo: [${typeLabels.join(', ')}]`);
            }
            
            if (filterInfo.length > 0) {
                infoText += ` (filtrado por: ${filterInfo.join(', ')})`;
            }
            
            paginationInfo.textContent = infoText;
        }
        
        if (prevPageBtn) prevPageBtn.disabled = currentPage <= 1;
        if (nextPageBtn) nextPageBtn.disabled = currentPage >= lastPage;
    }

    // Variable para debounce de búsqueda
    let searchTimeout = null;

    // Función para aplicar filtros via API en lugar de filtrado local
    function applyFilters() {
        console.log('🔄 applyFilters() iniciado - usando API');
        console.log('� Término de búsqueda actual:', currentSearchTerm);
        console.log('🔧 Filtros activos:', activeFilters);
        
        // Resetear a la primera página cuando se aplican filtros
        currentPage = 1;
        
        // Cargar datos desde API con filtros aplicados
        fetchRoles(currentPage, true);
        
        console.log('✅ applyFilters() completado - datos cargados desde API');
    }

    // Función para aplicar filtros con debounce (especialmente para búsqueda)
    function applyFiltersWithDebounce(delay = 300) {
        // Cancelar timeout anterior si existe
        if (searchTimeout) {
            clearTimeout(searchTimeout);
        }
        
        // Establecer nuevo timeout
        searchTimeout = setTimeout(() => {
            applyFilters();
        }, delay);
    }

    // Mantener función filterRoles legacy para compatibilidad (ahora llama a applyFilters)
    function filterRoles() {
        console.log('� filterRoles() - redirigiendo a applyFilters()');
        applyFilters();
    }

    function setupSearch() {
        const searchInput = document.getElementById('search-roles');
        const clearBtn = document.getElementById('clear-search');
        
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                currentSearchTerm = e.target.value;
                
                if (clearBtn) {
                    clearBtn.style.display = currentSearchTerm ? 'block' : 'none';
                }
                
                // Usar debounce para la búsqueda en tiempo real
                applyFiltersWithDebounce(300);
            });
        }
        
        if (clearBtn) {
            clearBtn.addEventListener('click', () => {
                if (searchInput) {
                    searchInput.value = '';
                    currentSearchTerm = '';
                    clearBtn.style.display = 'none';
                    clearAllFilters();
                    applyFilters(); // Sin debounce para acciones directas
                    searchInput.focus();
                }
            });
        }

        // Configurar filtros de estado y tipo
        setupFilterListeners();
    }

    /**
     * Configura el botón de refrescar
     */
    function setupRefreshButton() {
        const refreshBtn = document.getElementById('refresh-roles-btn');
        
        if (refreshBtn) {
            refreshBtn.addEventListener('click', async () => {
                console.log('🔄 Refrescando tabla de roles...');
                
                // Mostrar indicador de carga en el botón
                const icon = refreshBtn.querySelector('i');
                const originalText = refreshBtn.innerHTML;
                
                if (icon) {
                    icon.classList.add('fa-spin');
                }
                
                try {
                    // Resetear filtros y búsqueda
                    currentSearchTerm = '';
                    clearAllFilters();
                    
                    // Limpiar el input de búsqueda
                    const searchInput = document.getElementById('search-roles');
                    const clearBtn = document.getElementById('clear-search');
                    
                    if (searchInput) {
                        searchInput.value = '';
                    }
                    
                    if (clearBtn) {
                        clearBtn.style.display = 'none';
                    }
                    
                    // Resetear checkboxes de filtros
                    const filterCheckboxes = [
                        'filter-active',
                        'filter-inactive', 
                        'filter-web',
                        'filter-app'
                    ];
                    
                    filterCheckboxes.forEach(filterId => {
                        const checkbox = document.getElementById(filterId);
                        if (checkbox) {
                            checkbox.checked = false;
                        }
                    });
                    
                    // Resetear página a la primera
                    currentPage = 1;
                    
                    // Recargar datos desde la API
                    await fetchRoles(1);
                    
                    // Mostrar mensaje de éxito
                    if (typeof window.showToast === 'function') {
                        window.showToast('Lista de roles actualizada correctamente', 'success');
                    }
                    
                } catch (error) {
                    console.error('❌ Error al refrescar roles:', error);
                    
                    if (typeof window.showToast === 'function') {
                        window.showToast('Error al actualizar la lista de roles', 'error');
                    } else {
                        alert('Error al actualizar la lista de roles');
                    }
                } finally {
                    // Quitar indicador de carga del botón
                    if (icon) {
                        icon.classList.remove('fa-spin');
                    }
                }
            });
        } else {
            console.warn('⚠️ Botón de refrescar no encontrado (refresh-roles-btn)');
        }
    }

    /**
     * Configura los event listeners para los filtros
     */
    function setupFilterListeners() {
        const filterCheckboxes = [
            'filter-active',
            'filter-inactive', 
            'filter-web',
            'filter-app'
        ];

        filterCheckboxes.forEach(filterId => {
            const checkbox = document.getElementById(filterId);
            if (checkbox) {
                checkbox.addEventListener('change', () => handleFilterChange());
            }
        });
    }

    /**
     * Maneja los cambios en los filtros de estado y tipo
     */
    function handleFilterChange() {
        updateActiveFilters();
        applyFilters(); // Llamar directamente sin debounce para filtros tipo checkbox
    }

    /**
     * Actualiza los filtros activos basado en los checkboxes
     */
    function updateActiveFilters() {
        // Filtros de estado
        activeFilters.status = [];
        if (document.getElementById('filter-active')?.checked) {
            activeFilters.status.push('active');
        }
        if (document.getElementById('filter-inactive')?.checked) {
            activeFilters.status.push('inactive');
        }

        // Filtros de tipo
        activeFilters.type = [];
        if (document.getElementById('filter-web')?.checked) {
            activeFilters.type.push('web');
        }
        if (document.getElementById('filter-app')?.checked) {
            activeFilters.type.push('app');
        }

        console.log('🔍 Filtros de roles actualizados:', activeFilters);
    }

    /**
     * Limpia todos los filtros de estado y tipo
     */
    function clearAllFilters() {
        // Desmarcar todos los checkboxes
        const filterCheckboxes = [
            'filter-active',
            'filter-inactive', 
            'filter-web',
            'filter-app'
        ];

        filterCheckboxes.forEach(filterId => {
            const checkbox = document.getElementById(filterId);
            if (checkbox) {
                checkbox.checked = false;
            }
        });

        // Resetear filtros activos
        activeFilters = {
            status: [],
            type: []
        };
    }

    async function fetchRoles(page = 1, applyFilters = true) {
        showLoading(true);
        try {
            console.log(`🔍 Cargando roles: página ${page}, ${perPage} por página`);
            
            // Verificar que el servicio esté disponible
            if (typeof RoleService === 'undefined') {
                throw new Error('RoleService no está disponible');
            }
            
            // Preparar parámetros de filtrado para enviar a la API
            let searchParam = null;
            let webOnlyParam = null;
            let activeOnlyParam = null;
            
            if (applyFilters) {
                // Aplicar término de búsqueda si existe
                if (currentSearchTerm && currentSearchTerm.trim()) {
                    searchParam = currentSearchTerm.trim();
                }
                
                // Aplicar filtros de tipo (web/app)
                if (activeFilters.type.length === 1) {
                    if (activeFilters.type.includes('web')) {
                        webOnlyParam = true;
                    } else if (activeFilters.type.includes('app')) {
                        webOnlyParam = false;
                    }
                }
                // Si están ambos seleccionados o ninguno, no enviar filtro de tipo
                
                // Aplicar filtros de estado (activo/inactivo)
                if (activeFilters.status.length === 1) {
                    if (activeFilters.status.includes('active')) {
                        activeOnlyParam = true;
                    } else if (activeFilters.status.includes('inactive')) {
                        activeOnlyParam = false;
                    }
                }
                // Si están ambos seleccionados o ninguno, no enviar filtro de estado
            }
            
            console.log('🔧 Parámetros de filtrado enviados a la API:');
            console.log(`   - search: "${searchParam}"`);
            console.log(`   - webOnly: ${webOnlyParam}`);
            console.log(`   - activeOnly: ${activeOnlyParam}`);
            
            // Llamar al servicio con los filtros
            const response = await RoleService.getRoles(page, perPage, searchParam, webOnlyParam, activeOnlyParam);
            console.log('📋 Respuesta del servicio de roles:', response);
            
            // Verificar que la respuesta sea exitosa
            if (response.success === false) {
                console.error('❌ API respondió con error explícito:', response.message);
                throw new Error(response.message || 'Error al cargar roles');
            }
            
            // Si no hay campo success pero hay datos válidos, continuar
            if (response.success === undefined && response.data) {
                console.log('⚠️ Respuesta sin campo success pero con datos válidos, continuando...');
            }
            
            // Extraer datos según la estructura real de la API
            if (response.data) {
                console.log('🔍 Analizando estructura de datos recibida...');
                console.log('📋 response.data:', response.data);
                
                // Detectar estructura automáticamente
                let rolesData = null;
                let detectedStructure = 'unknown';
                
                // Opción 1: data.roles (nueva estructura de la API)
                if (response.data.roles && Array.isArray(response.data.roles)) {
                    rolesData = { 
                        items: response.data.roles,
                        meta: {
                            total: response.data.total || response.data.roles.length,
                            perPage: response.data.limit || perPage,
                            currentPage: response.data.page || page,
                            lastPage: response.data.totalPages || Math.ceil((response.data.total || response.data.roles.length) / (response.data.limit || perPage))
                        }
                    };
                    detectedStructure = 'data.roles';
                    console.log('✅ Estructura detectada: data.roles (nueva API)');
                }
                // Opción 2: data.data.items (estructura anidada legacy)
                else if (response.data.data && response.data.data.items && Array.isArray(response.data.data.items)) {
                    rolesData = response.data.data;
                    detectedStructure = 'data.data.items';
                    console.log('✅ Estructura detectada: data.data.items (legacy)');
                }
                // Opción 3: data.items (estructura simple legacy)
                else if (response.data.items && Array.isArray(response.data.items)) {
                    rolesData = response.data;
                    detectedStructure = 'data.items';
                    console.log('✅ Estructura detectada: data.items (legacy)');
                }
                // Opción 4: data.roles.items (estructura con roles legacy)
                else if (response.data.roles && response.data.roles.items && Array.isArray(response.data.roles.items)) {
                    rolesData = response.data.roles;
                    detectedStructure = 'data.roles.items';
                    console.log('✅ Estructura detectada: data.roles.items (legacy)');
                }
                // Opción 5: data como array directo
                else if (Array.isArray(response.data)) {
                    rolesData = { 
                        items: response.data, 
                        meta: { 
                            total: response.data.length, 
                            perPage: perPage, 
                            currentPage: page, 
                            lastPage: 1 
                        } 
                    };
                    detectedStructure = 'data (array)';
                    console.log('✅ Estructura detectada: data (array directo)');
                }
                
                if (rolesData && rolesData.items && Array.isArray(rolesData.items)) {
                    console.log(`🏗️ Usando estructura: ${detectedStructure}`);
                    console.log(`📊 Roles encontrados: ${rolesData.items.length}`);
                    console.log('🔍 Primeros roles:', rolesData.items.slice(0, 3));
                    
                    // Usar directamente los datos filtrados por la API
                    allRoles = rolesData.items;
                    filteredRoles = rolesData.items; // Los datos ya vienen filtrados de la API
                    currentPage = page;
                    
                    // Actualizar información de paginación desde la API
                    if (rolesData.meta) {
                        total = rolesData.meta.total || rolesData.items.length;
                        lastPage = rolesData.meta.lastPage || Math.ceil(total / perPage);
                        console.log(`📋 Paginación desde API: ${total} total, página ${currentPage}/${lastPage}`);
                    } else {
                        // Fallback si no hay meta de la API
                        total = rolesData.items.length;
                        lastPage = Math.ceil(total / perPage);
                    }
                    
                    // Renderizar directamente sin filtrado local
                    console.log('🔄 Renderizando datos desde API...');
                    renderRows(filteredRoles);
                    updatePaginationInfo();
                    
                    console.log(`✅ Roles cargados desde API:`, {
                        total: total,
                        shown: filteredRoles.length,
                        currentPage: currentPage,
                        perPage: perPage
                    });
                } else {
                    console.warn('⚠️ Estructura de datos inesperada:', response);
                    allRoles = [];
                    filteredRoles = [];
                    renderRows([]);
                    updatePaginationInfo();
                }
            } else {
                console.warn('⚠️ Respuesta sin data:', response);
                allRoles = [];
                filteredRoles = [];
                renderRows([]);
                updatePaginationInfo();
            }
            
        } catch (error) {
            console.error('❌ Error cargando roles desde API:', error);
            
            // Limpiar datos locales en caso de error
            allRoles = [];
            filteredRoles = [];
            renderRows([]);
            updatePaginationInfo();
            
            // Mostrar mensaje de error al usuario
            const errorMessage = `Error al cargar roles: ${error.message}`;
            
            // Estrategia 1: Usar showToast si está disponible
            if (typeof window.showToast === 'function') {
                window.showToast(errorMessage, 'error');
            }
            // Estrategia 2: Usar toast básico si existe
            else {
                const toast = document.getElementById('toast');
                const toastMsg = document.getElementById('toast-message');
                if (toast && toastMsg) {
                    toastMsg.textContent = errorMessage;
                    toast.classList.add('show', 'error');
                    setTimeout(() => {
                        toast.classList.remove('show', 'error');
                    }, 4000);
                }
                // Estrategia 3: Alert como último recurso
                else {
                    console.warn('⚠️ No hay sistema de toast disponible, usando alert');
                    alert(errorMessage);
                }
            }
        }
        showLoading(false);
    }

    // --- Crear rol ---
    async function createRole(roleData) {
        if (typeof RoleService === 'undefined') {
            alert('El servicio de roles no está disponible');
            return { success: false };
        }
        console.log('🔨 Creando nuevo rol con datos:', roleData);
        return await RoleService.createRole(roleData);
    }

    // --- Actualizar rol ---
    async function updateRole(roleData) {
        if (typeof RoleService === 'undefined') {
            alert('El servicio de roles no está disponible');
            return { success: false };
        }
        console.log('🔄 Actualizando rol con datos:', roleData);
        return await RoleService.updateRole(roleData);
    }

    // --- Eliminar rol ---
    async function deleteRole(roleId) {
        if (typeof RoleService === 'undefined') {
            alert('El servicio de roles no está disponible');
            return { success: false };
        }
        console.log('🗑️ Eliminando rol con ID:', roleId);
        return await RoleService.deleteRole(roleId);
    }

    // =============================================================================
    // MODAL DE DETALLES DEL ROL - ASIGNACIÓN DE PERMISOS
    // =============================================================================

    // Variables para el modal de detalles
    // Función para abrir el modal de detalles del rol
    function openRoleDetailsModal(roleId, roleName) {
        console.log('🔍 Abriendo modal de detalles para rol:', { id: roleId, name: roleName });
        
        // Delegar al controlador especializado de detalles de rol
        if (typeof window.RoleDetailsController !== 'undefined') {
            window.RoleDetailsController.openPermissionAssignmentModal(roleId, roleName);
        } else {
            console.error('❌ RoleDetailsController no está disponible');
            if (typeof window.showToast === 'function') {
                window.showToast('Error: El módulo de detalles de rol no está disponible', 'error');
            }
        }
    }

    // --- Modal y formulario para crear/editar rol ---
    function setupModalEvents() {
        const openBtn = document.getElementById('create-role-btn');
        const modal = document.getElementById('role-modal');
        const closeBtn = document.getElementById('close-role-modal');
        const cancelBtn = document.getElementById('cancel-role');
        const form = document.getElementById('role-form');
        const toast = document.getElementById('toast');
        const toastMsg = document.getElementById('toast-message');
        let editMode = false;
        let editingRoleId = null;

        if (openBtn) openBtn.addEventListener('click', () => {
            if (modal) modal.style.display = 'block';
            if (form) form.reset();
            editMode = false;
            editingRoleId = null;
        });
        if (closeBtn) closeBtn.addEventListener('click', () => {
            if (modal) modal.style.display = 'none';
        });
        if (cancelBtn) cancelBtn.addEventListener('click', () => {
            if (modal) modal.style.display = 'none';
        });
        if (modal) modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.style.display = 'none';
        });
        if (form) {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                // Protección contra doble envío
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn && submitBtn.disabled) {
                    console.log('⚠️ Formulario ya está siendo procesado, ignorando envío duplicado');
                    return;
                }
                
                // Deshabilitar botón de envío
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = editMode ? 'Actualizando...' : 'Guardando...';
                }
                
                try {
                    // Recopilar datos del formulario
                    const name = document.getElementById('role-name').value.trim();
                    const active = document.getElementById('role-active').value === 'true';
                    const web = document.getElementById('role-web').value === 'true';
                
                // Debug: Mostrar datos del formulario
                console.log('📝 Datos del formulario recopilados:');
                console.log('   - Nombre:', name);
                console.log('   - Estado (active):', active, typeof active);
                console.log('   - Ámbito (web):', web, typeof web);
                console.log('   - Modo edición:', editMode);
                console.log('   - ID editando:', editingRoleId);
                
                // Validación básica
                if (!name || name.length === 0) {
                    alert('El nombre del rol es requerido');
                    return;
                }
                
                // Verificar si el nombre ya existe (solo para creación)
                if (!editMode) {
                    // Obtener los roles actuales de la tabla para verificar duplicados
                    const existingRows = tableBody.querySelectorAll('tr');
                    const existingNames = [];
                    existingRows.forEach(row => {
                        const cells = row.querySelectorAll('td');
                        if (cells.length > 0 && cells[0].textContent !== 'No hay roles para mostrar') {
                            existingNames.push(cells[0].textContent.toLowerCase());
                        }
                    });
                    
                    if (existingNames.includes(name.toLowerCase())) {
                        const errorMessage = `El nombre "${name}" ya existe. Por favor, elija un nombre diferente.`;
                        
                        if (typeof window.showToast === 'function') {
                            window.showToast(errorMessage, 'error');
                        } else {
                            alert(errorMessage);
                        }
                        
                        // Resaltar el campo nombre
                        const nameField = document.getElementById('role-name');
                        if (nameField) {
                            nameField.style.borderColor = '#dc3545';
                            nameField.focus();
                            nameField.select();
                            setTimeout(() => nameField.style.borderColor = '', 3000);
                        }
                        return;
                    }
                }
                
                let result;
                if (editMode && editingRoleId) {
                    const updateData = { id: Number(editingRoleId), name, active, web };
                    console.log('🔄 Actualizando rol con datos:', updateData);
                    result = await RolesController.updateRole(updateData);
                } else {
                    const createData = { name, active, web };
                    console.log('🔨 Creando rol con datos:', createData);
                    result = await RolesController.createRole(createData);
                }
                
                console.log('📋 Resultado de la operación:', result);
                if (result && result.success !== false && !result.error) {
                    // Operación exitosa
                    const successMessage = editMode ? 'Rol actualizado correctamente' : 'Rol guardado correctamente';
                    
                    if (typeof window.showToast === 'function') {
                        window.showToast(successMessage, 'success');
                    } else if (toast && toastMsg) {
                        toastMsg.textContent = successMessage;
                        toast.classList.add('show');
                        setTimeout(() => toast.classList.remove('show'), 2000);
                    }
                    
                    if (modal) modal.style.display = 'none';
                    fetchRoles(1);
                    form.reset();
                    editMode = false;
                    editingRoleId = null;
                } else {
                    // Operación falló - Pero verificar si es un falso positivo reportado por el servicio
                    console.error('❌ Error en la operación:', result);
                    
                    // Verificar si el servicio ya detectó un falso positivo
                    if (result && result.wasFalsePositive) {
                        console.log('✅ Falso positivo confirmado por el servicio');
                        
                        const successMessage = editMode 
                            ? `Rol "${name}" actualizado exitosamente` 
                            : `Rol "${name}" creado exitosamente (corregido desde error del servidor)`;
                        
                        if (typeof window.showToast === 'function') {
                            window.showToast(successMessage, 'success');
                        } else if (toast && toastMsg) {
                            toastMsg.textContent = successMessage;
                            toast.classList.add('show');
                            setTimeout(() => toast.classList.remove('show'), 2000);
                        }
                        
                        if (modal) modal.style.display = 'none';
                        fetchRoles(1);
                        form.reset();
                        editMode = false;
                        editingRoleId = null;
                        return; // Salir aquí ya que fue exitoso
                    }
                    
                    // Si es error 500 pero menciona "ya existe", podría ser un falso positivo no detectado
                    // El rol podría haberse creado a pesar del error
                    const isLikelyFalsePositive = result && result.message && 
                        (result.message.includes('ya existe') || result.message.includes('already exists')) &&
                        !editMode; // Solo para creación, no para edición
                    
                    if (isLikelyFalsePositive) {
                        console.log('🔍 Posible falso positivo detectado. Verificando si el rol se creó...');
                        
                        // Mostrar mensaje de verificación
                        if (typeof window.showToast === 'function') {
                            window.showToast('Verificando si el rol se creó correctamente...', 'info');
                        }
                        
                        // Esperar un momento y recargar la lista para verificar
                        setTimeout(async () => {
                            const previousCount = tableBody.querySelectorAll('tr').length;
                            await fetchRoles(1);
                            const newCount = tableBody.querySelectorAll('tr').length;
                            
                            // Verificar si el rol aparece en la lista actualizada
                            const roleExists = Array.from(tableBody.querySelectorAll('tr')).some(row => {
                                const cells = row.querySelectorAll('td');
                                return cells.length > 0 && cells[0].textContent.toLowerCase() === name.toLowerCase();
                            });
                            
                            if (roleExists || newCount > previousCount) {
                                // El rol se creó exitosamente a pesar del error 500
                                console.log('✅ Falso positivo confirmado: el rol se creó correctamente');
                                
                                const successMessage = 'Rol guardado correctamente';
                                if (typeof window.showToast === 'function') {
                                    window.showToast(successMessage, 'success');
                                } else if (toast && toastMsg) {
                                    toastMsg.textContent = successMessage;
                                    toast.classList.add('show');
                                    setTimeout(() => toast.classList.remove('show'), 2000);
                                }
                                
                                if (modal) modal.style.display = 'none';
                                form.reset();
                                editMode = false;
                                editingRoleId = null;
                            } else {
                                // Error real
                                showRealError(result, name);
                            }
                        }, 1500); // Esperar 1.5 segundos para que la base de datos se actualice
                        
                    } else {
                        // Error definitivo
                        showRealError(result, name);
                    }
                }
                
                function showRealError(result, name) {
                    let errorMessage = 'Error al guardar el rol';
                    if (result && result.message) {
                        errorMessage = result.message;
                        
                        // Personalizar mensajes de error comunes
                        if (result.message.includes('ya existe')) {
                            errorMessage = `El nombre "${name}" ya existe. Por favor, elija un nombre diferente.`;
                            
                            // Resaltar el campo nombre
                            const nameField = document.getElementById('role-name');
                            if (nameField) {
                                nameField.style.borderColor = '#dc3545';
                                nameField.focus();
                                nameField.select();
                                setTimeout(() => nameField.style.borderColor = '', 3000);
                            }
                        } else if (result.message.includes('500')) {
                            errorMessage = 'Error interno del servidor. Verifique que todos los campos estén correctos y que tenga permisos para crear roles.';
                        } else if (result.message.includes('authorization') || result.message.includes('forbidden')) {
                            errorMessage = 'No tiene permisos para realizar esta operación. Contacte al administrador.';
                        }
                    }
                    
                    if (typeof window.showToast === 'function') {
                        window.showToast(errorMessage, 'error');
                    } else {
                        alert(errorMessage);
                    }
                }
                
                } finally {
                    // Re-habilitar botón de envío
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = editMode ? '<i class="fas fa-save"></i> Guardar' : '<i class="fas fa-save"></i> Guardar';
                    }
                }
            });
        }
        // Handler para los botones de detalles, editar y eliminar
        if (tableBody) {
            tableBody.addEventListener('click', async (e) => {
                // Handler para detalles (botón del ojo)
                const detailsBtn = e.target.closest('.details-btn');
                if (detailsBtn) {
                    const row = detailsBtn.closest('tr');
                    const cells = row.querySelectorAll('td');
                    const roleName = cells[0].textContent;
                    const roleId = row.getAttribute('data-id');
                    
                    console.log('👁️ Abriendo detalles del rol:', { id: roleId, name: roleName });
                    openRoleDetailsModal(roleId, roleName);
                    return;
                }
                
                // Handler para editar
                const editBtn = e.target.closest('.action-btn.edit');
                if (editBtn) {
                    const row = editBtn.closest('tr');
                    const cells = row.querySelectorAll('td');
                    const name = cells[0].textContent;
                    const status = cells[1].querySelector('.status-badge').classList.contains('active');
                    // Lee el texto para saber si es Web o App
                    const webText = cells[2].querySelector('.type-badge').textContent.trim();
                    const web = webText === 'Web';
                    // Suponiendo que el id está en un atributo data-id en el tr (ajustar si es necesario)
                    const id = row.getAttribute('data-id');
                    document.getElementById('role-name').value = name;
                    document.getElementById('role-active').value = status ? 'true' : 'false';
                    document.getElementById('role-web').value = web ? 'true' : 'false';
                    editMode = true;
                    editingRoleId = id;
                    if (modal) modal.style.display = 'block';
                }
                
                // Handler para eliminar
                const deleteBtn = e.target.closest('.action-btn.delete');
                if (deleteBtn) {
                    const row = deleteBtn.closest('tr');
                    const cells = row.querySelectorAll('td');
                    const roleName = cells[0].textContent;
                    const roleId = row.getAttribute('data-id');
                    
                    if (confirm(`¿Está seguro de que desea eliminar el rol "${roleName}"?`)) {
                        console.log('🗑️ Eliminando rol:', { id: roleId, name: roleName });
                        
                        // Mostrar indicador de carga en el botón
                        const originalText = deleteBtn.innerHTML;
                        deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Eliminando...';
                        deleteBtn.disabled = true;
                        
                        try {
                            const result = await RolesController.deleteRole(roleId);
                            
                            if (result && result.success === true) {
                                // Éxito: Mostrar toast de éxito
                                const successMessage = result.message || 'Rol eliminado correctamente';
                                console.log('✅ Rol eliminado exitosamente:', successMessage);
                                
                                if (typeof window.showToast === 'function') {
                                    window.showToast(successMessage, 'success');
                                } else if (toast && toastMsg) {
                                    toastMsg.textContent = successMessage;
                                    toast.classList.add('show');
                                    setTimeout(() => toast.classList.remove('show'), 3000);
                                }
                                
                                // Recargar la lista después de eliminar
                                fetchRoles(currentPage);
                                
                            } else {
                                // Error: Mostrar mensaje específico del servidor
                                const errorMessage = result && result.message ? result.message : 'Error desconocido al eliminar el rol';
                                console.error('❌ Error al eliminar rol:', errorMessage);
                                
                                if (typeof window.showToast === 'function') {
                                    window.showToast(errorMessage, 'error');
                                } else if (toast && toastMsg) {
                                    toastMsg.textContent = errorMessage;
                                    toast.classList.add('show', 'error');
                                    setTimeout(() => {
                                        toast.classList.remove('show', 'error');
                                    }, 4000);
                                } else {
                                    alert(errorMessage);
                                }
                            }
                        } catch (error) {
                            // Error de red o excepción
                            console.error('❌ Excepción al eliminar rol:', error);
                            const errorMessage = 'Error de conexión al eliminar el rol';
                            
                            if (typeof window.showToast === 'function') {
                                window.showToast(errorMessage, 'error');
                            } else {
                                alert(errorMessage);
                            }
                        } finally {
                            // Restaurar el botón
                            deleteBtn.innerHTML = originalText;
                            deleteBtn.disabled = false;
                        }
                    }
                }
            });
        }
    }

    // Llamar a setupModalEvents en la inicialización
    function init() {
        console.log('🚀 RolesController.init() iniciando...');
        console.log('🔍 Verificando elementos DOM:');
        console.log('   - tableBody:', tableBody ? 'OK' : 'FALTA');
        console.log('   - loadingRow:', loadingRow ? 'OK' : 'FALTA');
        console.log('   - paginationInfo:', paginationInfo ? 'OK' : 'FALTA');
        console.log('   - prevPageBtn:', prevPageBtn ? 'OK' : 'FALTA');
        console.log('   - nextPageBtn:', nextPageBtn ? 'OK' : 'FALTA');
        
        // Configurar eventos de paginación usando API
        if (prevPageBtn && nextPageBtn) {
            prevPageBtn.addEventListener('click', () => {
                if (currentPage > 1) {
                    currentPage--;
                    fetchRoles(currentPage, true); // Usar API con filtros
                }
            });
            nextPageBtn.addEventListener('click', () => {
                if (currentPage < lastPage) {
                    currentPage++;
                    fetchRoles(currentPage, true); // Usar API con filtros
                }
            });
        }
        
        // Configurar búsqueda
        setupSearch();
        
        // Configurar botón de refrescar
        setupRefreshButton();
        
        // Configurar eventos del modal de crear/editar
        setupModalEvents();
        
        // Cargar datos iniciales
        fetchRoles(1);
    }

    /**
     * Función pública para limpiar todos los filtros (llamada desde HTML)
     */
    function clearAllFiltersPublic() {
        const searchInput = document.getElementById('search-roles');
        const clearBtn = document.getElementById('clear-search');
        
        if (searchInput) {
            searchInput.value = '';
            searchInput.classList.remove('searching');
        }
        
        if (clearBtn) {
            clearBtn.style.display = 'none';
        }
        
        currentSearchTerm = '';
        clearAllFilters();
        applyFilters(); // Usar API en lugar de filterRoles local
        
        if (searchInput) {
            searchInput.focus();
        }
        
        console.log('🔍 Filtros de roles limpiados desde función pública');
    }

    /**
     * Refresca la vista actual (llamado desde otros controladores)
     */
    function refreshCurrentView() {
        fetchRoles(currentPage);
    }

    // Exponer la función para el inicializador y otros módulos
    return { init, createRole, updateRole, deleteRole, refreshCurrentView, clearAllFiltersPublic };
})();

// Exponer globalmente para que otros módulos puedan usarlo
window.RolesController = RolesController;
