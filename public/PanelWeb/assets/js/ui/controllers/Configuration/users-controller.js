/**
 * Controlador básico para la gestión de usuarios
 */
class UsersController {
    constructor() {
        console.log('👥 UsersController constructor ejecutado');
        this.isInitialized = false;
        this.users = [];
        this.currentPage = 1;
        this.itemsPerPage = 10;
        this.totalUsers = 0;
        this.searchTerm = '';
        this.searchTimeout = null;
        
        // Control de cómo se originó la carga de usuarios (para controlar notificaciones)
        this.lastAction = 'initial_load'; // 'initial_load', 'filter_change', 'manual_refresh', 'auto_refresh'
        
        // Filtros específicos de usuarios - ahora soportan múltiples selecciones
        this.activeFilters = {
            userTypes: [],         // ['citizen', 'driver', 'admin'] - array para múltiples tipos
            driverStatuses: [],    // ['PENDIENTE', 'APROBADO', 'RECHAZADO'] - array para múltiples estados
            hasVehicle: null,      // true/false
            contactVerified: null  // true/false
        };
        
        // Inicializar automáticamente
        this.init();
    }

    /**
     * Inicializa el controlador
     */
    async init() {
        console.log('👥 Inicializando UsersController...');
        try {
            // Verificar que UserService esté disponible
            await this.waitForServices();
            
            this.setupEventListeners();
            this.loadUsers();
            this.isInitialized = true;
            console.log('✅ UsersController inicializado correctamente');
        } catch (error) {
            console.error('❌ Error al inicializar UsersController:', error);
            // Mostrar error en la interfaz
            this.showError('Error al inicializar: ' + error.message);
        }
    }

    /**
     * Espera a que los servicios necesarios estén disponibles
     */
    async waitForServices() {
        let attempts = 0;
        const maxAttempts = 20; // Aumentar intentos
        
        while (typeof UserService === 'undefined' && attempts < maxAttempts) {
            console.log(`⏳ Esperando UserService... intento ${attempts + 1}/${maxAttempts}`);
            await new Promise(resolve => setTimeout(resolve, 250)); // Esperar 250ms
            attempts++;
        }
        
        if (typeof UserService === 'undefined') {
            throw new Error('UserService no está disponible después de esperar');
        }
        
        console.log('✅ UserService está disponible');
    }

    /**
     * Configura los event listeners básicos
     */
    setupEventListeners() {
        console.log('⚙️ Configurando event listeners...');
        
        // Botón crear usuario
        const createBtn = document.getElementById('create-user-btn');
        if (createBtn) {
            createBtn.addEventListener('click', () => this.showCreateUserModal());
        }

        // Botón refrescar usuarios
        const refreshBtn = document.getElementById('refresh-users-btn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => this.handleRefreshUsers());
        }

        // Búsqueda
        const searchInput = document.getElementById('search-users');
        const clearSearchBtn = document.getElementById('clear-search');
        
        if (searchInput) {
            searchInput.addEventListener('input', (e) => this.handleSearch(e.target.value));
        }
        if (clearSearchBtn) {
            clearSearchBtn.addEventListener('click', () => this.clearSearch());
        }

        // Limpiar filtros (si existe el botón)
        const clearFiltersBtn = document.getElementById('clear-filters');
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', () => this.clearAllFilters());
        }

        // Paginación
        const prevBtn = document.getElementById('prev-page');
        const nextBtn = document.getElementById('next-page');
        
        if (prevBtn) {
            prevBtn.addEventListener('click', () => this.previousPage());
        }
        if (nextBtn) {
            nextBtn.addEventListener('click', () => this.nextPage());
        }

        // Filtros de usuario
        this.setupFilterListeners();
    }

    /**
     * Configura los event listeners para los filtros
     */
    setupFilterListeners() {
        // Filtros de tipo de usuario
        const userTypeFilters = ['filter-citizen', 'filter-driver', 'filter-admin'];
        userTypeFilters.forEach(filterId => {
            const checkbox = document.getElementById(filterId);
            if (checkbox) {
                checkbox.addEventListener('change', () => this.handleUserTypeFilter());
            }
        });

        // Filtros de estado de conductor
        const driverStatusFilters = ['filter-pending', 'filter-approved', 'filter-rejected'];
        driverStatusFilters.forEach(filterId => {
            const checkbox = document.getElementById(filterId);
            if (checkbox) {
                checkbox.addEventListener('change', () => this.handleDriverStatusFilter());
            }
        });

        // Filtro de contacto verificado
        const verifiedContactFilter = document.getElementById('filter-verified-contact');
        if (verifiedContactFilter) {
            verifiedContactFilter.addEventListener('change', () => this.handleContactVerifiedFilter());
        }
    }

    /**
     * Maneja filtros de tipo de usuario - ahora soporta múltiples selecciones
     */
    handleUserTypeFilter() {
        const citizenChecked = document.getElementById('filter-citizen')?.checked;
        const driverChecked = document.getElementById('filter-driver')?.checked;
        const adminChecked = document.getElementById('filter-admin')?.checked;

        // Construir array de tipos seleccionados
        this.activeFilters.userTypes = [];
        
        if (citizenChecked) this.activeFilters.userTypes.push('citizen');
        if (driverChecked) this.activeFilters.userTypes.push('driver');
        if (adminChecked) this.activeFilters.userTypes.push('admin');

        console.log('🔍 Filtros de tipo de usuario actualizados:', this.activeFilters.userTypes);

        // Marcar como cambio de filtro para evitar notificaciones
        this.lastAction = 'filter_change';
        this.currentPage = 1;
        this.loadUsers();
    }

    /**
     * Maneja filtros de estado de conductor - ahora soporta múltiples selecciones
     */
    handleDriverStatusFilter() {
        const pendingChecked = document.getElementById('filter-pending')?.checked;
        const approvedChecked = document.getElementById('filter-approved')?.checked;
        const rejectedChecked = document.getElementById('filter-rejected')?.checked;

        // Construir array de estados seleccionados
        this.activeFilters.driverStatuses = [];
        
        if (pendingChecked) this.activeFilters.driverStatuses.push('PENDIENTE');
        if (approvedChecked) this.activeFilters.driverStatuses.push('APROBADO');
        if (rejectedChecked) this.activeFilters.driverStatuses.push('RECHAZADO');

        console.log('🔍 Filtros de estado de conductor actualizados:', this.activeFilters.driverStatuses);

        // Marcar como cambio de filtro para evitar notificaciones
        this.lastAction = 'filter_change';
        this.currentPage = 1;
        this.loadUsers();
    }

    /**
     * Maneja filtro de contacto verificado
     */
    handleContactVerifiedFilter() {
        const checked = document.getElementById('filter-verified-contact')?.checked;
        this.activeFilters.contactVerified = checked ? true : null;
        
        // Marcar como cambio de filtro para evitar notificaciones
        this.lastAction = 'filter_change';
        this.currentPage = 1;
        this.loadUsers();
    }

    /**
     * Carga la lista de usuarios desde la API
     */
    async loadUsers() {
        console.log('📋 Cargando usuarios desde la API...');
        
        // Verificar que UserService esté disponible
        if (typeof UserService === 'undefined') {
            console.error('❌ UserService no está disponible');
            this.showError('UserService no está disponible. Por favor, recarga la página.');
            return;
        }
        
        // Mostrar indicador de carga
        this.showLoading(true);
        
        try {
            // Llamar a la API real - convertir arrays a strings para la API
            const userTypesParam = this.activeFilters.userTypes.length > 0 ? this.activeFilters.userTypes.join(',') : null;
            const driverStatusesParam = this.activeFilters.driverStatuses.length > 0 ? this.activeFilters.driverStatuses.join(',') : null;
            
            const response = await UserService.getUsers(
                this.currentPage, 
                this.itemsPerPage,
                this.searchTerm || null,
                userTypesParam,
                driverStatusesParam,
                this.activeFilters.hasVehicle,
                this.activeFilters.contactVerified
            );
            
            console.log('📡 Respuesta de la API:', response);
            
            if (response.success && response.data) {
                // Detectar estructura de datos
                let usersArray = null;
                let paginationData = null;
                
                if (response.data.users) {
                    usersArray = response.data.users;
                    paginationData = response.data.pagination || response.data;
                } else if (response.data.data && response.data.data.items) {
                    usersArray = response.data.data.items;
                    paginationData = response.data.data;
                } else if (response.data.items) {
                    usersArray = response.data.items;
                    paginationData = response.data;
                } else if (Array.isArray(response.data)) {
                    usersArray = response.data;
                    paginationData = { total: response.data.length };
                }
                
                if (usersArray && Array.isArray(usersArray)) {
                    // Transformar datos de la API al formato esperado
                    const newUsers = usersArray.map(user => this.transformApiUser(user));
                    
                    // Detectar cambios si ya teníamos usuarios cargados
                    // Solo notificar cambios cuando la carga es automática (no por filtro o refresh manual)
                    if (this.users && this.users.length > 0 && this.lastAction === 'auto_refresh') {
                        this.detectUserChanges(this.users, newUsers);
                    } else {
                        console.log('ℹ️ Omitiendo notificaciones de cambios (acción no automática)');
                    }
                    
                    this.users = newUsers;
                    this.totalUsers = paginationData.total || usersArray.length;
                    
                    console.log(`✅ ${this.users.length} usuarios cargados correctamente`);
                    this.renderUsers();
                    this.updatePaginationControls();
                    
                    // NO verificar usuario actual automáticamente al cargar la lista
                    // La verificación solo debe ocurrir cuando se cambie el estado de un usuario
                } else {
                    console.error('❌ Estructura de datos inesperada:', response.data);
                    this.showError('Error: Estructura de datos inesperada');
                }
            } else {
                console.error('❌ Error en la respuesta:', response.message);
                this.showError(response.message || 'Error al cargar usuarios');
            }
            
        } catch (error) {
            console.error('❌ Error al cargar usuarios:', error);
            this.showError('Error de conexión al cargar usuarios: ' + error.message);
        } finally {
            this.showLoading(false);
        }
    }

    /**
     * Transforma un usuario de la API al formato interno
     */
    transformApiUser(apiUser) {
        const person = apiUser.person || {};
        const roles = apiUser.roles || [];
        const vehicle = apiUser.vehicle;
        const contacts = apiUser.contacts || [];
        const status = apiUser.status;
        const profiles = apiUser.profiles || {};
        
        // 🔍 DIAGNÓSTICO: Log completo de la estructura de datos
        console.log(`🔄 Transformando usuario ID ${apiUser.id}:`);
        console.log(`   - Nombre: ${person.name} ${person.lastName}`);
        console.log(`   - status: ${status?.name} (display)`);
        console.log(`   - status.id: ${status?.id} (control unificado badge + logout)`);
        console.log(`   - profiles:`, profiles);
        console.log(`   - profiles.driver:`, profiles.driver);
        console.log(`   - rawData completo:`, apiUser);
        
        // Determinar tipo de usuario basado en roles
        let userType = 'citizen'; // por defecto
        if (roles.some(r => r.name === 'ADMINISTRADOR')) {
            userType = 'admin';
        } else if (roles.some(r => r.name === 'CONDUCTOR')) {
            userType = 'driver';
        }
        
        // Obtener estado del conductor (etapa) SOLO para conductores
        // Para la columna "Etapa" solo debe mostrarse el estado del perfil de conductor
        let driverStatus = null;
        
        // ÚNICAMENTE si es un perfil de conductor y tiene status
        if (profiles.driver && profiles.driver.status) {
            driverStatus = profiles.driver.status;
            console.log(`   ✅ Etapa de conductor encontrada: ${driverStatus}`);
        } else {
            console.log(`   ℹ️ Usuario no es conductor o no tiene etapa definida`);
        }
        
        // Obtener email desde contacts (no desde person.email)
        let email = 'Sin email';
        let emailVerified = false;
        const emailContact = contacts.find(c => c.type === 'CORREO ELECTRÓNICO');
        if (emailContact && emailContact.value) {
            email = emailContact.value;
            emailVerified = emailContact.confirmed || false;
        }
        
        // Obtener teléfono desde contacts
        let phone = 'Sin teléfono';
        let phoneVerified = false;
        const phoneContact = contacts.find(c => c.type === 'TELÉFONO MÓVIL');
        if (phoneContact && phoneContact.value) {
            phone = phoneContact.value;
            phoneVerified = phoneContact.confirmed || false;
        }
        
        // Verificar si algún contacto está verificado (para compatibilidad)
        const contactVerified = emailVerified || phoneVerified;
        
        // Extraer placa del vehículo
        let vehiclePlate = null;
        if (vehicle && vehicle.plate) {
            vehiclePlate = vehicle.plate;
        }

        const result = {
            id: apiUser.id,
            firstname: person.name || 'Sin nombre',  // La API usa "name", no "firstName"
            lastname: person.lastName || 'Sin apellido',
            email: email,
            emailVerified: emailVerified,
            document: person.document || 'Sin documento',
            doctype: person.documentType || 'N/A',
            phone: phone,
            phoneVerified: phoneVerified,
            userType: userType,
            driverStatus: driverStatus,
            // 🔧 COHERENCIA: Usar status.id para TANTO badge como logout
            active: status ? (status.id === 1) : true,  // Coherente con normalizeUserStatus
            hasVehicle: vehicle !== null,
            contactVerified: contactVerified, // Mantener para compatibilidad
            vehicle: vehicle,
            vehiclePlate: vehiclePlate, // Campo específico para la placa
            roles: roles.map(r => r.name), // Mantener todos los roles
            rawData: apiUser // guardar datos originales para referencia
        };
        
        console.log(`   ✅ Resultado transformado:`);
        console.log(`      - active: ${result.active} (de status.id: ${status?.id}) - Usado para badge Y logout`);
        
        return result;
    }

    /**
     * Renderiza la lista de usuarios en la tabla
     */
    renderUsers() {
        const tbody = document.getElementById('users-list');
        if (!tbody) {
            console.error('❌ No se encontró el elemento users-list');
            return;
        }

        tbody.innerHTML = '';

        if (this.users.length === 0) {
            const message = this.searchTerm ? 
                `No se encontraron usuarios que coincidan con "${this.searchTerm}"` : 
                'No se encontraron usuarios';
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" style="text-align: center; padding: 40px; color: #6c757d;">
                        <i class="fas fa-users" style="font-size: 48px; margin-bottom: 16px;"></i>
                        <p>${message}</p>
                    </td>
                </tr>
            `;
            return;
        }

        this.users.forEach(user => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="col-user">
                    <div class="user-info">
                        <div class="user-details">
                            <h6>${user.firstname} ${user.lastname}</h6>
                        </div>
                    </div>
                </td>
                <td class="col-document">${user.document || 'Sin documento'}</td>
                <td class="col-email">
                    <div class="contact-info">
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            ${user.email}
                        </div>
                        ${user.emailVerified ? 
                            '<div class="verified-badge"><i class="fas fa-check-circle"></i>Verificado</div>' : 
                            '<div class="unverified-badge"><i class="fas fa-times-circle"></i>No verificado</div>'
                        }
                    </div>
                </td>
                <td class="col-phone">
                    <div class="contact-info">
                        <div class="contact-item${user.phone ? '' : ' text-muted'}">
                            <i class="fas fa-${user.phone ? 'phone' : 'phone-slash'}"></i>
                            ${user.phone || 'Sin teléfono'}
                        </div>
                        ${user.phone && user.phone !== 'Sin teléfono' ? 
                            (user.phoneVerified ? 
                                '<div class="verified-badge"><i class="fas fa-check-circle"></i>Verificado</div>' : 
                                '<div class="unverified-badge"><i class="fas fa-times-circle"></i>No verificado</div>'
                            ) : ''
                        }
                    </div>
                </td>
                <td class="col-status">
                    <span class="status-badge ${user.active ? 'active' : 'inactive'}" 
                          onclick="usersController.toggleUserStatus(${user.id})" 
                          style="cursor: pointer;" 
                          title="Clic para cambiar estado">
                        ${user.active ? 'Activo' : 'Inactivo'}
                    </span>
                </td>
                <td class="col-stage">
                    ${user.driverStatus ? 
                        `<span class="driver-status ${user.driverStatus.toLowerCase()}">${user.driverStatus}</span>` : 
                        '-'
                    }
                </td>
                <td class="col-vehicle">
                    ${user.hasVehicle ? 
                        `<div class="vehicle-info">
                            <i class="fas fa-car"></i>
                            ${user.vehiclePlate || 'Con vehículo'}
                        </div>` : 
                        `<div class="no-vehicle">
                            <i class="fas fa-times-circle"></i>
                            Sin vehículo
                        </div>`
                    }
                </td>
                <td class="col-actions">
                    <div class="action-buttons">
                        <button class="action-btn edit" onclick="usersController.viewUserDetails(${user.id})" title="Editar usuario">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="action-btn delete" onclick="usersController.deleteUser(${user.id})" title="Eliminar usuario">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });

        this.updatePaginationInfo();
    }

    /**
     * Muestra un mensaje de error
     */
    showError(message) {
        const tbody = document.getElementById('users-list');
        if (tbody) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" style="text-align: center; padding: 40px; color: #dc3545;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 48px; margin-bottom: 16px;"></i>
                        <p><strong>Error:</strong> ${message}</p>
                        <button class="btn-secondary" onclick="usersController.loadUsers()" style="margin-top: 16px;">
                            <i class="fas fa-refresh"></i> Reintentar
                        </button>
                    </td>
                </tr>
            `;
        }
    }

    /**
     * Actualiza los controles de paginación
     */
    updatePaginationControls() {
        const totalPages = Math.ceil(this.totalUsers / this.itemsPerPage);
        
        // Botones prev/next
        const prevBtn = document.getElementById('prev-page');
        const nextBtn = document.getElementById('next-page');
        
        if (prevBtn) {
            prevBtn.disabled = this.currentPage <= 1;
        }
        if (nextBtn) {
            nextBtn.disabled = this.currentPage >= totalPages;
        }
    }

    /**
     * Obtiene la etiqueta del tipo de usuario
     */
    /**
     * Muestra/oculta el indicador de carga
     */
    showLoading(show) {
        const loadingRow = document.getElementById('users-loading-row');
        const usersList = document.getElementById('users-list');
        
        if (loadingRow) {
            loadingRow.style.display = show ? '' : 'none';
        }
        if (usersList) {
            usersList.style.display = show ? 'none' : '';
        }
    }

    /**
     * Maneja la búsqueda de usuarios
     */
    handleSearch(searchTerm) {
        console.log('🔍 Buscando:', searchTerm);
        
        // Limpiar timeout anterior
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
        }
        
        // Actualizar término de búsqueda
        this.searchTerm = searchTerm.trim();
        
        // Log para debug de búsqueda por placa
        if (this.searchTerm) {
            console.log(`🔍 Término de búsqueda: "${this.searchTerm}" - Se enviará a la API para búsqueda en nombre, documento, placa y email`);
        }
        
        // Mostrar/ocultar botón limpiar
        const clearBtn = document.getElementById('clear-search');
        if (clearBtn) {
            clearBtn.style.display = this.searchTerm ? 'block' : 'none';
        }
        
        // Búsqueda con delay para evitar muchas peticiones
        this.searchTimeout = setTimeout(() => {
            this.currentPage = 1; // Resetear a primera página
            console.log('🔄 Iniciando nueva búsqueda con parámetros:', {
                page: this.currentPage,
                limit: this.itemsPerPage,
                search: this.searchTerm || null,
                userTypes: this.activeFilters.userTypes,
                driverStatuses: this.activeFilters.driverStatuses,
                hasVehicle: this.activeFilters.hasVehicle,
                contactVerified: this.activeFilters.contactVerified
            });
            
            // Marcar como cambio de filtro para evitar notificaciones
            this.lastAction = 'filter_change';
            this.loadUsers();
        }, 300);
    }

    /**
     * Limpia la búsqueda
     */
    clearSearch() {
        const searchInput = document.getElementById('search-users');
        const clearBtn = document.getElementById('clear-search');
        
        if (searchInput) {
            searchInput.value = '';
        }
        if (clearBtn) {
            clearBtn.style.display = 'none';
        }
        
        this.searchTerm = '';
        this.currentPage = 1;
        this.loadUsers();
    }

    /**
     * Limpia todos los filtros activos
     */
    clearAllFilters() {
        console.log('🧹 Limpiando todos los filtros');
        
        // Limpiar arrays de filtros
        this.activeFilters.userTypes = [];
        this.activeFilters.driverStatuses = [];
        this.activeFilters.hasVehicle = null;
        this.activeFilters.contactVerified = null;
        
        // Desmarcar todos los checkboxes
        const filterCheckboxes = [
            'filter-citizen', 'filter-driver', 'filter-admin',
            'filter-pending', 'filter-approved', 'filter-rejected',
            'filter-verified-contact'
        ];
        
        filterCheckboxes.forEach(checkboxId => {
            const checkbox = document.getElementById(checkboxId);
            if (checkbox) {
                checkbox.checked = false;
            }
        });
        
        // Recargar usuarios sin filtros
        this.currentPage = 1;
        this.loadUsers();
    }

    /**
     * Gestiona los roles de un usuario
     */
    manageRoles(userId) {
        console.log('👤 Gestionar roles del usuario:', userId);
        // TODO: Implementar gestión de roles
    }

    /**
     * Muestra el modal para crear usuario
     */
    showCreateUserModal() {
        console.log('➕ Abrir modal de crear usuario');
        
        const modal = document.getElementById('create-user-modal');
        if (modal) {
            // Limpiar el formulario
            this.clearCreateUserForm();
            
            // Mostrar el modal
            modal.style.display = 'block';
            
            // Configurar event listeners si no están configurados
            this.setupCreateUserModalListeners();
        } else {
            console.error('❌ Modal de crear usuario no encontrado');
        }
    }

    /**
     * Configura los event listeners del modal de crear usuario
     */
    setupCreateUserModalListeners() {
        // Evitar configurar múltiples veces
        if (this.createModalListenersSetup) return;
        
        // Cerrar modal con X
        const closeBtn = document.getElementById('close-create-user-modal');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.closeCreateUserModal());
        }

        // Cerrar modal con botón cancelar
        const cancelBtn = document.getElementById('cancel-create-user');
        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => this.closeCreateUserModal());
        }

        // Cerrar modal al hacer clic fuera
        const modal = document.getElementById('create-user-modal');
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.closeCreateUserModal();
                }
            });
        }

        // Manejar envío del formulario
        const form = document.getElementById('create-user-form');
        if (form) {
            form.addEventListener('submit', (e) => this.handleCreateUserSubmit(e));
        }

        this.createModalListenersSetup = true;
    }

    /**
     * Cierra el modal de crear usuario
     */
    closeCreateUserModal() {
        const modal = document.getElementById('create-user-modal');
        if (modal) {
            modal.style.display = 'none';
            this.clearCreateUserForm();
        }
    }

    /**
     * Limpia el formulario de crear usuario
     */
    clearCreateUserForm() {
        const form = document.getElementById('create-user-form');
        if (form) {
            form.reset();
        }
    }

    /**
     * Maneja el envío del formulario de crear usuario
     */
    async handleCreateUserSubmit(e) {
        e.preventDefault();
        
        console.log('📤 Enviando formulario de crear usuario...');
        
        // Obtener datos del formulario
        const formData = new FormData(e.target);
        const userData = {
            firstName: formData.get('firstname'), // Cambiar a firstName para coincidir con la API
            lastName: formData.get('lastname'),   // Cambiar a lastName para coincidir con la API
            document: formData.get('document'),
            email: formData.get('email'),
            password: formData.get('password'),
            area: formData.get('area') || '',
            position: formData.get('position') || ''
        };

        // Validar datos requeridos
        if (!userData.firstName || !userData.lastName || !userData.document || !userData.email || !userData.password) {
            this.showToast('Todos los campos marcados son obligatorios', 'error');
            return;
        }

        try {
            // Mostrar indicador de carga
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
            submitBtn.disabled = true;

            console.log('📝 Datos del usuario para crear:', {
                ...userData,
                password: '[OCULTA]' // No mostrar contraseña en logs
            });

            // Usar UserService para crear el usuario
            const result = await window.UserService.createUser(userData);

            if (result.success) {
                console.log('✅ Usuario creado exitosamente:', result.data);
                
                // Mostrar mensaje de éxito
                const successMessage = result.message || 'Usuario creado exitosamente';
                this.showToast(successMessage, 'success');
                
                // Cerrar modal
                this.closeCreateUserModal();
                
                // Recargar la lista de usuarios para mostrar el nuevo usuario
                await this.loadUsers();
                
            } else {
                console.error('❌ Error al crear usuario:', result.message);
                this.showToast('Error al crear usuario: ' + result.message, 'error');
            }

        } catch (error) {
            console.error('❌ Error al crear usuario:', error);
            this.showToast('Error al crear usuario: ' + error.message, 'error');
        } finally {
            // Restaurar botón
            const submitBtn = e.target.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-save"></i> Guardar';
                submitBtn.disabled = false;
            }
        }
    }

    /**
     * Edita un usuario
     */
    editUser(userId) {
        console.log('✏️ Editar usuario:', userId);
        
        // Buscar el usuario en la lista
        const user = this.users.find(u => u.id === userId);
        if (!user) {
            console.error('Usuario no encontrado:', userId);
            return;
        }
        
        // Llamar al controlador de detalles de usuario para abrir el modal de roles
        if (window.UserDetailsController) {
            // Construir nombre completo y obtener teléfono
            const userName = user.fullName || user.name || `${user.firstname || ''} ${user.lastname || ''}`.trim();
            const userPhone = user.phone && user.phone !== 'Sin teléfono' ? user.phone : null;
            
            window.UserDetailsController.openRoleAssignmentModal(userId, userName, userPhone);
        } else {
            // Fallback en caso de que el controlador no esté disponible
            const userDetailsModal = document.getElementById('user-details-modal');
            
            // Actualizar el título del modal
            const modalTitle = document.getElementById('user-details-title');
            if (modalTitle) modalTitle.textContent = user.fullName || user.name;
            
            // Actualizar información de contacto
            if (user.email) {
                const emailValue = document.getElementById('user-email-value');
                if (emailValue) emailValue.textContent = user.email;
            }
            
            if (user.phone) {
                const phoneValue = document.getElementById('user-phone-value');
                if (phoneValue) phoneValue.textContent = user.phone;
            } else {
                const phoneValue = document.getElementById('user-phone-value');
                if (phoneValue) phoneValue.textContent = 'Sin teléfono';
            }
            
            // Mostrar el modal
            if (userDetailsModal) userDetailsModal.style.display = 'flex';
        }
    }

    /**
     * Elimina un usuario
     */
    deleteUser(userId) {
        console.log('🗑️ Eliminar usuario:', userId);
        // TODO: Implementar eliminación
    }

    /**
     * Cambia el estado de un usuario (activo/inactivo)
     */
    async toggleUserStatus(userId) {
        console.log('🔄 Cambiando estado del usuario:', userId);
        
        // Encontrar el usuario en la lista local
        const user = this.users.find(u => u.id === userId);
        if (!user) {
            console.error('❌ Usuario no encontrado en la lista local');
            this.showToast('Error: Usuario no encontrado', 'error');
            return;
        }
        
        // Log adicional para debugging
        console.log('🔍 Usuario encontrado:', {
            id: user.id,
            name: `${user.firstname} ${user.lastname}`,
            currentStatus: user.active,
            userType: user.userType,
            driverStatus: user.driverStatus,
            roles: user.roles
        });
        
        // Determinar el nuevo estado
        const currentStatus = user.active;
        const newStatusId = currentStatus ? 2 : 1; // 1 = activo, 2 = inactivo
        const newStatusText = currentStatus ? 'inactivo' : 'activo';
        
        console.log(`🎯 ANÁLISIS DETALLADO DEL CAMBIO:`);
        console.log(`   - Usuario ID: ${userId}`);
        console.log(`   - Nombre: ${user.firstname} ${user.lastname}`);
        console.log(`   - Estado actual en frontend: ${currentStatus ? 'ACTIVO' : 'INACTIVO'}`);
        console.log(`   - Nuevo estado que se enviará: ${newStatusText} (statusId: ${newStatusId})`);
        console.log(`   - ¿Qué debería quedar?: ${!currentStatus ? 'ACTIVO' : 'INACTIVO'}`);
        console.log(`   - Datos completos del usuario local:`, user);
        
        // Obtener referencia al badge antes de modificarlo
        const statusBadge = document.querySelector(`[onclick="usersController.toggleUserStatus(${userId})"]`);
        const originalBadgeContent = statusBadge ? statusBadge.innerHTML : null;
        
        try {
            // Mostrar loading en el badge
            if (statusBadge) {
                statusBadge.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cambiando...';
                statusBadge.style.pointerEvents = 'none';
            }
            
            // Llamar al servicio para cambiar el estado
            console.log('📡 Enviando petición al UserService...');
            const response = await UserService.updateUserStatus(userId, newStatusId);
            
            console.log('📡 Respuesta del UserService:', response);
            
            // Verificar si fue exitoso
            if (response.success !== false) {
                console.log('✅ Estado cambiado exitosamente en el servidor');
                
                // 🔍 DIAGNÓSTICO DETALLADO DE LA RESPUESTA
                console.log('📊 Respuesta completa del cambio de estado:', response);
                
                // ✅ PRIMERO: Actualizar el estado local del usuario inmediatamente
                user.active = !currentStatus;
                console.log(`� Estado local actualizado: ${user.active ? 'ACTIVO' : 'INACTIVO'}`);
                
                // ✅ SEGUNDO: Actualizar el badge inmediatamente
                if (statusBadge) {
                    statusBadge.classList.remove('active', 'inactive');
                    statusBadge.classList.add(user.active ? 'active' : 'inactive');
                    statusBadge.innerHTML = user.active ? 'Activo' : 'Inactivo';
                    statusBadge.setAttribute('title', 'Clic para cambiar estado');
                    statusBadge.style.pointerEvents = 'auto';
                    console.log(`🎨 Badge actualizado visualmente: ${user.active ? 'Activo' : 'Inactivo'}`);
                }
                
                // ✅ TERCERO: Mostrar mensaje de éxito
                this.showToast(`Usuario ${user.active ? 'activado' : 'desactivado'} exitosamente`, 'success');
                
                // ⚠️ VERIFICACIÓN CRÍTICA: Si el usuario actual fue desactivado, forzar logout
                const currentUserId = sessionStorage.getItem('userId');
                if (currentUserId && currentUserId.toString() === userId.toString() && !user.active) {
                    console.log('🚨 ALERTA: El usuario actual fue desactivado - Forzando logout inmediato');
                    
                    // Usar UserService para forzar verificación inmediata con delay
                    if (typeof window.UserService !== 'undefined' && typeof window.UserService.forceUserStatusCheck === 'function') {
                        window.UserService.forceUserStatusCheck(2000);
                        return; // Salir temprano, el usuario será redirigido
                    } else {
                        // Fallback manual
                        console.log('⚠️ Método de verificación no disponible, ejecutando logout manual...');
                        alert('Tu cuenta ha sido desactivada. Serás redirigido al login.');
                        setTimeout(() => {
                            if (typeof cleanSession === 'function') {
                                cleanSession();
                            }
                            window.location.href = "../../index.html";
                        }, 1000);
                        return;
                    }
                }
                
                // ✅ CUARTO: Recargar la lista para sincronizar con BD (SOLO para verificación)
                console.log('🔄 Recargando lista para sincronizar con base de datos...');
                setTimeout(() => {
                    const originalUsers = [...this.users]; // Copia de seguridad
                    
                    this.loadUsers().then(() => {
                        // Verificar si hubo cambios después de recargar
                        const updatedUser = this.users.find(u => u.id === userId);
                        if (updatedUser) {
                            console.log('🔍 ANÁLISIS DETALLADO POST-RECARGA:');
                            console.log(`   - Usuario ID: ${userId}`);
                            console.log(`   - Estado esperado local: ${user.active ? 'ACTIVO' : 'INACTIVO'}`);
                            console.log(`   - Estado real tras recarga: ${updatedUser.active ? 'ACTIVO' : 'INACTIVO'}`);
                            console.log(`   - StatusId enviado originalmente: ${newStatusId}`);
                            console.log(`   - Estado actual esperado: ${!currentStatus ? 'ACTIVO' : 'INACTIVO'}`);
                            console.log(`   - Datos usuario local original:`, user);
                            console.log(`   - Datos usuario tras recarga:`, updatedUser);
                            
                            if (updatedUser.active !== user.active) {
                                console.warn('⚠️ DISCREPANCIA DETECTADA después de recargar:');
                                console.warn(`   - Estado local antes de recarga: ${user.active ? 'ACTIVO' : 'INACTIVO'}`);
                                console.warn(`   - Estado real BD tras recarga: ${updatedUser.active ? 'ACTIVO' : 'INACTIVO'}`);
                                console.warn(`   - ¿Por qué hay discrepancia?`);
                                console.warn(`     * ¿El endpoint cambió realmente el estado?`);
                                console.warn(`     * ¿El campo person.active se actualiza con el endpoint status?`);
                                console.warn(`     * ¿El backend maneja estos campos por separado?`);
                                
                                this.showToast(`Discrepancia detectada. Estado real: ${updatedUser.active ? 'Activo' : 'Inactivo'}`, 'warning');
                            } else {
                                console.log('✅ Estado local y BD coinciden correctamente después de recarga');
                            }
                        } else {
                            console.error('❌ Usuario no encontrado después de recargar');
                        }
                    }).catch((loadError) => {
                        console.error('❌ Error al recargar lista:', loadError);
                        // Restaurar lista original en caso de error
                        this.users = originalUsers;
                        this.renderUsers();
                    });
                }, 1000); // Dar tiempo para que la BD se actualice
                
            } else {
                console.error('❌ Error al cambiar estado:', response.message);
                this.showToast(response.message || 'Error al cambiar el estado', 'error');
                
                // Restaurar el badge original
                if (statusBadge && originalBadgeContent) {
                    statusBadge.innerHTML = originalBadgeContent;
                    statusBadge.style.pointerEvents = 'auto';
                }
            }
        } catch (error) {
            console.error('❌ Error al cambiar estado del usuario:', error);
            
            // ⚠️ VERIFICACIÓN CRÍTICA: Incluso con error, si intentamos desactivar al usuario actual, forzar verificación
            const currentUserId = sessionStorage.getItem('userId');
            if (currentUserId && currentUserId.toString() === userId.toString() && newStatusId === 2) {
                console.log('🚨 ALERTA: Error al desactivar usuario actual - Verificando estado inmediatamente');
                
                // Forzar verificación inmediata del estado del usuario actual con delay
                if (typeof window.UserService !== 'undefined' && typeof window.UserService.forceUserStatusCheck === 'function') {
                    window.UserService.forceUserStatusCheck(2500);
                    return; // Salir temprano en caso de que se confirme la desactivación
                } else {
                    console.log('⚠️ Método de verificación no disponible, usando verificación manual...');
                    // Verificar manualmente el estado del usuario
                    this.verifyCurrentUserStatusManually();
                    return;
                }
            }
            
            // Si es un error de red pero el cambio pudo haber sido exitoso, recargar para verificar
            if (error.message && (
                error.message.includes('500') || 
                error.message.includes('El usuario ya tiene el estado') ||
                error.message.toLowerCase().includes('already has') ||
                error.message.toLowerCase().includes('ya tiene')
            )) {
                console.log('⚠️ Error 500 detectado o usuario ya en ese estado, recargando para verificar...');
                this.showToast('Verificando estado del usuario...', 'warning');
                
                // Restaurar badge temporalmente
                if (statusBadge && originalBadgeContent) {
                    statusBadge.innerHTML = originalBadgeContent;
                    statusBadge.style.pointerEvents = 'auto';
                }
                
                // Recargar después de un delay para verificar el estado real
                setTimeout(() => {
                    this.loadUsers();
                }, 1500);
            } else {
                // Error real
                this.showToast('Error de conexión al cambiar el estado: ' + error.message, 'error');
                
                // Restaurar el badge en caso de error
                if (statusBadge && originalBadgeContent) {
                    statusBadge.innerHTML = originalBadgeContent;
                    statusBadge.style.pointerEvents = 'auto';
                }
            }
        }
    }

    /**
     * Muestra un mensaje toast
     */
    showToast(message, type = 'info') {
        // Usar el sistema de toast global si está disponible
        if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
            window.GlobalToast.show(message, type);
        } else {
            // Fallback a alert
            alert(message);
        }
    }

    /**
     * Navega a la página anterior
     */
    previousPage() {
        if (this.currentPage > 1) {
            this.currentPage--;
            this.loadUsers();
        }
    }

    /**
     * Navega a la página siguiente
     */
    nextPage() {
        const totalPages = Math.ceil(this.totalUsers / this.itemsPerPage);
        if (this.currentPage < totalPages) {
            this.currentPage++;
            this.loadUsers();
        }
    }

    /**
     * Actualiza la información de paginación
     */
    updatePaginationInfo() {
        const pageInfo = document.getElementById('page-info');
        if (pageInfo) {
            const start = (this.currentPage - 1) * this.itemsPerPage + 1;
            const end = Math.min(start + this.itemsPerPage - 1, this.totalUsers);
            pageInfo.textContent = `Mostrando ${start}-${end} de ${this.totalUsers} usuarios`;
        }
    }

    /**
     * Muestra los detalles de un usuario y permite asignar roles
     */
    viewUserDetails(userId) {
        console.log('👁️ Ver detalles del usuario:', userId);
        
        const user = this.users.find(u => u.id === userId);
        if (!user) {
            console.error('❌ Usuario no encontrado');
            return;
        }
        
        console.log('🔍 DEBUG - Datos del usuario encontrado:', user);
        
        // Construir nombre completo del usuario y obtener teléfono
        const userName = `${user.firstname || ''} ${user.lastname || ''}`.trim() || `Usuario ${userId}`;
        const userPhone = user.phone && user.phone !== 'Sin teléfono' ? user.phone : null;
        console.log('🔍 DEBUG - Nombre construido:', userName);
        console.log('🔍 DEBUG - Teléfono:', userPhone);
        
        // Verificar que el controlador de detalles de usuario esté disponible
        if (window.UserDetailsController && typeof window.UserDetailsController.openRoleAssignmentModal === 'function') {
            console.log('🔍 DEBUG - Abriendo modal con UserDetailsController');
            window.UserDetailsController.openRoleAssignmentModal(userId, userName, userPhone);
        } else {
            console.error('❌ UserDetailsController no está disponible');
            alert('Error: El controlador de detalles de usuario no está disponible');
        }
    }

    /**
     * Recarga la lista de usuarios (usado por el modal de detalles después de guardar)
     */
    reloadUsers() {
        console.log('🔄 Recargando lista de usuarios...');
        this.loadUsers();
    }

    /**
     * Verifica manualmente el estado del usuario actual cuando los métodos automáticos no están disponibles
     */
    async verifyCurrentUserStatusManually() {
        try {
            console.log('🔍 Verificando manualmente el estado del usuario actual...');
            const currentUserId = sessionStorage.getItem('userId');
            
            if (!currentUserId) {
                console.log('⚠️ No hay usuario actual en sesión');
                return;
            }
            
            // Intentar obtener el estado actual del usuario desde el servidor
            const response = await UserService.getCurrentUserStatus();
            
            if (response && response.success && response.data) {
                const userData = response.data;
                console.log('📋 Estado actual del usuario:', {
                    id: userData.id,
                    active: userData.person?.active !== false
                });
                
                // Solo verificar si el usuario está INACTIVO - NO verificar roles
                // Los permisos se manejan en auth-permissions.js
                const userActive = userData.person?.active !== false;
                if (!userActive) {
                    console.log('🚨 Usuario actual está INACTIVO - Forzando logout');
                    alert('Tu cuenta ha sido desactivada. Serás redirigido al login.');
                    
                    setTimeout(() => {
                        if (typeof cleanSession === 'function') {
                            cleanSession();
                        }
                        window.location.href = "../../index.html";
                    }, 1000);
                } else {
                    console.log('✅ Usuario actual está ACTIVO - Continuando sesión');
                }
            } else {
                console.log('⚠️ No se pudo verificar el estado del usuario');
            }
        } catch (error) {
            console.error('❌ Error en verificación manual:', error);
            // En caso de error, asumir que el usuario puede estar desactivado
            console.log('🚨 Error al verificar - Asumiendo posible desactivación y haciendo logout preventivo');
            alert('Tu sesión puede haber sido desactivada. Serás redirigido al login.');
            
            setTimeout(() => {
                if (typeof cleanSession === 'function') {
                    cleanSession();
                }
                window.location.href = "../../index.html";
            }, 1500);
        }
    }

    /**
     * Verifica si el usuario actual sigue activo en la lista recién cargada
     */
    verifyCurrentUserInList() {
        try {
            const currentUserId = sessionStorage.getItem('userId');
            if (!currentUserId) {
                console.log('🔍 No hay usuario actual en sesión para verificar');
                return;
            }
            
            // Buscar el usuario actual en la lista recién cargada
            const currentUser = this.users.find(user => user.id.toString() === currentUserId.toString());
            
            if (currentUser) {
                console.log(`🔍 Usuario actual encontrado en la lista: ${currentUser.firstname} ${currentUser.lastname}`, {
                    active: currentUser.active
                });
                
                // Solo verificar si el usuario está INACTIVO - NO verificar roles
                // Los permisos se manejan en auth-permissions.js
                if (!currentUser.active) {
                    console.log('🚨 ALERTA: Usuario actual está INACTIVO - Forzando logout inmediato');
                    alert('Tu cuenta ha sido desactivada. Serás redirigido al login.');
                    
                    setTimeout(() => {
                        if (typeof cleanSession === 'function') {
                            cleanSession();
                        }
                        window.location.href = "../../index.html";
                    }, 1000);
                } else {
                    console.log('✅ Usuario actual está ACTIVO - Continuando sesión normal');
                }
            } else {
                console.log('🔍 Usuario actual no encontrado en la lista cargada (puede estar en otra página)');
            }
        } catch (error) {
            console.error('❌ Error al verificar usuario actual en la lista:', error);
        }
    }

    /**
     * Detecta cambios en la lista de usuarios comparando con la versión anterior
     * @param {Array} oldUsers - Lista anterior de usuarios
     * @param {Array} newUsers - Nueva lista de usuarios
     */
    detectUserChanges(oldUsers, newUsers) {
        console.log('🔍 Detectando cambios en la lista de usuarios...');
        
        let changesDetected = false;
        const changes = [];
        
        // Crear mapas para comparación rápida
        const oldUsersMap = new Map(oldUsers.map(user => [user.id, user]));
        const newUsersMap = new Map(newUsers.map(user => [user.id, user]));
        
        // Verificar usuarios modificados
        for (const [userId, newUser] of newUsersMap) {
            const oldUser = oldUsersMap.get(userId);
            if (oldUser) {
                // Verificar cambios en estado
                if (oldUser.active !== newUser.active) {
                    changes.push({
                        type: 'status_changed',
                        user: newUser,
                        oldStatus: oldUser.active ? 'Activo' : 'Inactivo',
                        newStatus: newUser.active ? 'Activo' : 'Inactivo'
                    });
                    changesDetected = true;
                }
                
                // Verificar cambios en roles (opcional)
                const oldRoles = (oldUser.roles || []).map(r => r.name).sort().join(',');
                const newRoles = (newUser.roles || []).map(r => r.name).sort().join(',');
                if (oldRoles !== newRoles) {
                    changes.push({
                        type: 'roles_changed',
                        user: newUser,
                        oldRoles: oldRoles,
                        newRoles: newRoles
                    });
                    changesDetected = true;
                }
            }
        }
        
        // Verificar usuarios nuevos
        for (const [userId, newUser] of newUsersMap) {
            if (!oldUsersMap.has(userId)) {
                changes.push({
                    type: 'user_added',
                    user: newUser
                });
                changesDetected = true;
            }
        }
        
        // Verificar usuarios eliminados
        for (const [userId, oldUser] of oldUsersMap) {
            if (!newUsersMap.has(userId)) {
                changes.push({
                    type: 'user_removed',
                    user: oldUser
                });
                changesDetected = true;
            }
        }
        
        if (changesDetected) {
            console.log('📊 Cambios detectados:', changes);
            this.notifyUserChanges(changes);
        } else {
            console.log('✅ No se detectaron cambios en la lista');
        }
    }

    /**
     * Notifica al usuario sobre los cambios detectados
     * @param {Array} changes - Lista de cambios detectados
     */
    notifyUserChanges(changes) {
        const statusChanges = changes.filter(change => change.type === 'status_changed');
        const newUsers = changes.filter(change => change.type === 'user_added');
        const removedUsers = changes.filter(change => change.type === 'user_removed');
        
        let message = '';
        let toastType = 'info';
        
        if (statusChanges.length > 0) {
            const count = statusChanges.length;
            message = `${count} usuario${count > 1 ? 's han' : ' ha'} cambiado de estado`;
            toastType = 'info';
        } else if (newUsers.length > 0) {
            const count = newUsers.length;
            message = `${count} usuario${count > 1 ? 's nuevos' : ' nuevo'} agregado${count > 1 ? 's' : ''}`;
            toastType = 'success';
        } else if (removedUsers.length > 0) {
            const count = removedUsers.length;
            message = `${count} usuario${count > 1 ? 's' : ''} eliminado${count > 1 ? 's' : ''}`;
            toastType = 'warning';
        }
        
        if (message) {
            console.log('📢 Notificando cambios:', message);
            this.showToast(message, toastType);
        }
    }

    /**
     * Inicia una actualización automática periódica de la lista
     * Esta función debe usarse para actualizaciones programadas
     */
    startAutoRefresh() {
        console.log('🔄 Iniciando actualización automática periódica...');
        // Marcar como actualización automática para permitir notificaciones
        this.lastAction = 'auto_refresh';
        return this.loadUsers();
    }
    
    /**
     * Maneja el clic en el botón de refrescar usuarios
     * Recarga la lista completa desde el backend
     */
    handleRefreshUsers() {
        console.log('🔄 Refrescando lista de usuarios manualmente...');
        
        // Marcar como refresh manual para evitar notificaciones
        this.lastAction = 'manual_refresh';
        
        const refreshBtn = document.getElementById('refresh-users-btn');
        const refreshIcon = refreshBtn?.querySelector('i');
        
        // Animación visual del botón
        if (refreshIcon) {
            refreshIcon.style.animation = 'spin 1s linear infinite';
        }
        if (refreshBtn) {
            refreshBtn.disabled = true;
            refreshBtn.style.opacity = '0.7';
        }
        
        // Mostrar toast informativo
        this.showToast('Actualizando lista de usuarios...', 'info');
        
        // Recargar usuarios con un pequeño delay para mostrar la animación
        setTimeout(() => {
            this.loadUsers().finally(() => {
                // Restaurar botón
                if (refreshIcon) {
                    refreshIcon.style.animation = '';
                }
                if (refreshBtn) {
                    refreshBtn.disabled = false;
                    refreshBtn.style.opacity = '1';
                }
                
                console.log('✅ Lista de usuarios actualizada');
                this.showToast('Lista actualizada exitosamente', 'success');
            });
        }, 300);
    }

    /**
     * Normaliza el estado del usuario desde cualquier estructura de respuesta de la API
     * @param {Object} userData - Datos del usuario desde la API
     * @returns {Object} Estado normalizado con {active, statusInfo}
     */
    normalizeUserStatus(userData) {
        // COHERENCIA: Usar status.id para TODO (badge Y logout)
        // status.id = 1 → ACTIVO, status.id = 2 → INACTIVO
        let isActive = true; // por defecto activo si no se encuentra información
        let statusInfo = null;
        
        console.log('🔍 DIAGNÓSTICO COMPLETO DE normalizeUserStatus:');
        console.log('📊 Estructura completa recibida:', JSON.stringify(userData, null, 2));
        
        // Buscar información del status en diferentes ubicaciones
        if (userData.status) {
            statusInfo = userData.status;
            console.log(`✅ status encontrado en userData.status:`, statusInfo);
        } else if (userData.userStatus) {
            statusInfo = userData.userStatus;
            console.log(`✅ status encontrado en userData.userStatus:`, statusInfo);
        } else if (userData.user?.status) {
            statusInfo = userData.user.status;
            console.log(`✅ status encontrado en userData.user.status:`, statusInfo);
        } else if (userData.user?.userStatus) {
            statusInfo = userData.user.userStatus;
            console.log(`✅ status encontrado en userData.user.userStatus:`, statusInfo);
        } else {
            console.log('⚠️ Información de status NO encontrada');
        }
        
        // Determinar si está activo basado en status.id
        if (statusInfo && statusInfo.id !== undefined) {
            isActive = (statusInfo.id === 1); // 1 = ACTIVO, 2 = INACTIVO
            console.log(`🎯 Estado determinado por status.id: ${statusInfo.id} → ${isActive ? 'ACTIVO' : 'INACTIVO'}`);
        } else {
            console.log('⚠️ status.id no encontrado, usando valor por defecto: ACTIVO');
        }
        
        console.log('🔧 Estado normalizado RESULTADO:', {
            statusInfo,
            isActiveReal: isActive,
            criterio: 'status.id',
            estructuraOriginal: Object.keys(userData || {})
        });
        
        return {
            active: isActive,
            statusInfo: statusInfo
        };
    }
}

// Exportar globalmente
if (typeof window !== 'undefined') {
    window.UsersController = UsersController;
    console.log('✅ UsersController exportado globalmente');
}
