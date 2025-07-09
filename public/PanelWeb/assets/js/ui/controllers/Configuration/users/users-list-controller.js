/**
 * Controlador para listar usuarios con filtros avanzados
 * Endpoint: /api/v1/users
 * Funcionalidades:
 * - Lista paginada de usuarios
 * - Filtros avanzados (tipo, estado conductor, vehículo, contacto verificado)
 * - Búsqueda por nombre, apellido, documento
 * - Transformación de datos de API a formato UI
 */
class UsersListController {
    constructor() {
        console.log('📋 UsersListController constructor ejecutado');
        
        // Estado de la lista
        this.users = [];
        this.currentPage = 1;
        this.itemsPerPage = 10; // Valor inicial por defecto
        this.totalUsers = 0;
        this.totalPages = 0;
        this.searchTerm = '';
        this.searchTimeout = null;
        
        // Control de origen de carga para notificaciones
        this.lastAction = 'initial_load'; // 'initial_load', 'filter_change', 'manual_refresh', 'auto_refresh'
        
        // Filtros específicos de usuarios según la API
        this.activeFilters = {
            userType: null,        // 'citizen', 'driver', 'admin'
            driverStatus: null,    // 'PENDIENTE', 'APROBADO', 'RECHAZADO'
            hasVehicle: null,      // true/false
            contactVerified: null  // true/false
        };
        
        // Estados de UI
        this.isLoading = false;
        this.isInitialized = false;
    }

    /**
     * Inicializa el controlador
     */
    async init() {
        console.log('📋 Inicializando UsersListController...');
        try {
            // Verificar que UserService esté disponible
            await this.waitForServices();
            
            // Configurar event listeners
            this.setupEventListeners();
            
            // Cargar usuarios inicialmente
            await this.loadUsers();
            
            // Sincronizar el selector de elementos por página
            this.syncItemsPerPageSelector();

            this.isInitialized = true;
            console.log('✅ UsersListController inicializado correctamente');
        } catch (error) {
            console.error('❌ Error al inicializar UsersListController:', error);
            this.showError('Error al inicializar la lista de usuarios: ' + error.message);
        }
    }

    /**
     * Espera a que los servicios necesarios estén disponibles
     */
    async waitForServices() {
        let attempts = 0;
        const maxAttempts = 20;
        
        while (typeof UserService === 'undefined' && attempts < maxAttempts) {
            console.log(`⏳ Esperando UserService... intento ${attempts + 1}/${maxAttempts}`);
            await new Promise(resolve => setTimeout(resolve, 250));
            attempts++;
        }
        
        if (typeof UserService === 'undefined') {
            throw new Error('UserService no está disponible después de esperar');
        }
        
        console.log('✅ UserService está disponible para UsersListController');
    }

    /**
     * Configura los event listeners para la lista
     */
    setupEventListeners() {
        console.log('⚙️ Configurando event listeners para lista de usuarios...');
        
        // Búsqueda
        const searchInput = document.getElementById('search-users');
        const clearSearchBtn = document.getElementById('clear-search');
        
        if (searchInput) {
            searchInput.addEventListener('input', (e) => this.handleSearch(e.target.value));
        }
        if (clearSearchBtn) {
            clearSearchBtn.addEventListener('click', () => this.clearSearch());
        }

        // Filtros según la API
        this.setupFilterListeners();

        // Paginación
        const prevBtn = document.getElementById('prev-page');
        const nextBtn = document.getElementById('next-page');
        
        if (prevBtn) {
            prevBtn.addEventListener('click', () => this.previousPage());
        }
        if (nextBtn) {
            nextBtn.addEventListener('click', () => this.nextPage());
        }

        // Botón refrescar
        const refreshBtn = document.getElementById('refresh-users-btn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => this.handleRefreshUsers());
        }

        // Limpiar filtros
        const clearFiltersBtn = document.getElementById('clear-filters');
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', () => this.clearAllFilters());
        }

        // Selector de elementos por página
        const itemsPerPageSelect = document.getElementById('items-per-page');
        if (itemsPerPageSelect) {
            itemsPerPageSelect.addEventListener('change', (e) => this.handleItemsPerPageChange(parseInt(e.target.value)));
        }

        // Botón crear usuario (desktop y mobile)
        const createUserBtn = document.getElementById('create-user-btn');
        const createUserBtnMobile = document.getElementById('create-user-btn-mobile');
        
        if (createUserBtn) {
            createUserBtn.addEventListener('click', () => this.handleCreateUser());
        }
        if (createUserBtnMobile) {
            createUserBtnMobile.addEventListener('click', () => this.handleCreateUser());
        }
    }

    /**
     * Configura los filtros según la documentación de la API
     */
    setupFilterListeners() {
        // Filtro tipo de usuario (userType) - select dropdown
        const userTypeFilter = document.getElementById('filter-user-type');
        if (userTypeFilter) {
            userTypeFilter.addEventListener('change', () => this.handleUserTypeFilter());
        }

        // Filtro estado conductor (driverStatus) - select dropdown
        const driverStatusFilter = document.getElementById('filter-driver-status');
        if (driverStatusFilter) {
            driverStatusFilter.addEventListener('change', () => this.handleDriverStatusFilter());
        }

        // Filtro vehículo asociado (hasVehicle) - checkbox en dropdown
        const hasVehicleFilter = document.getElementById('filter-has-vehicle');
        if (hasVehicleFilter) {
            hasVehicleFilter.addEventListener('change', () => this.handleHasVehicleFilter());
        }

        // Filtro contacto verificado (contactVerified) - checkbox en dropdown
        const contactVerifiedFilter = document.getElementById('filter-contact-verified');
        if (contactVerifiedFilter) {
            contactVerifiedFilter.addEventListener('change', () => this.handleContactVerifiedFilter());
        }
    }

    /**
     * Maneja filtro de tipo de usuario
     */
    handleUserTypeFilter() {
        const userTypeSelect = document.getElementById('filter-user-type');
        const selectedType = userTypeSelect?.value;
        
        this.activeFilters.userType = selectedType || null;
        
        console.log('🔍 Filtro userType actualizado:', this.activeFilters.userType);
        this.applyFiltersAndReload();
    }

    /**
     * Maneja filtro de estado de conductor
     */
    handleDriverStatusFilter() {
        const driverStatusSelect = document.getElementById('filter-driver-status');
        const selectedStatus = driverStatusSelect?.value;
        
        this.activeFilters.driverStatus = selectedStatus || null;
        
        console.log('🔍 Filtro driverStatus actualizado:', this.activeFilters.driverStatus);
        this.applyFiltersAndReload();
    }

    /**
     * Maneja filtro de vehículo asociado
     */
    handleHasVehicleFilter() {
        const checked = document.getElementById('filter-has-vehicle')?.checked;
        this.activeFilters.hasVehicle = checked ? true : null;
        
        console.log('🔍 Filtro hasVehicle actualizado:', this.activeFilters.hasVehicle);
        this.applyFiltersAndReload();
    }

    /**
     * Maneja filtro de contacto verificado
     */
    handleContactVerifiedFilter() {
        const checked = document.getElementById('filter-contact-verified')?.checked;
        this.activeFilters.contactVerified = checked ? true : null;
        
        console.log('🔍 Filtro contactVerified actualizado:', this.activeFilters.contactVerified);
        this.applyFiltersAndReload();
    }

    /**
     * Aplica filtros y recarga la lista
     */
    applyFiltersAndReload() {
        this.lastAction = 'filter_change';
        this.currentPage = 1;
        this.loadUsers();
    }

    /**
     * Carga la lista de usuarios desde la API /api/v1/users
     */
    async loadUsers() {
        console.log('📡 Cargando usuarios desde /api/v1/users...');
        
        if (typeof UserService === 'undefined') {
            console.error('❌ UserService no está disponible');
            this.showError('UserService no está disponible. Por favor, recarga la página.');
            return;
        }
        
        this.showLoading(true);
        
        try {
            // Preparar parámetros según la documentación de la API
            const params = {
                page: this.currentPage,
                limit: this.itemsPerPage
            };

            // Agregar búsqueda si existe
            if (this.searchTerm && this.searchTerm.trim()) {
                params.search = this.searchTerm.trim();
            }

            // Agregar filtros activos
            if (this.activeFilters.userType) {
                params.userType = this.activeFilters.userType;
            }
            if (this.activeFilters.driverStatus) {
                params.driverStatus = this.activeFilters.driverStatus;
            }
            if (this.activeFilters.hasVehicle !== null) {
                params.hasVehicle = this.activeFilters.hasVehicle;
            }
            if (this.activeFilters.contactVerified !== null) {
                params.contactVerified = this.activeFilters.contactVerified;
            }

            console.log('📋 Parámetros de consulta:', params);

            // Llamar al servicio (adaptado para usar los parámetros individuales)
            const response = await UserService.getUsers(
                params.page,
                params.limit,
                params.search || null,
                params.userType || null,
                params.driverStatus || null,
                params.hasVehicle || null,
                params.contactVerified || null
            );
            
            console.log('📡 Respuesta de /api/v1/users:', response);
            
            if (response.success && response.data) {
                await this.processUsersResponse(response.data);
            } else {
                throw new Error(response.message || 'Error al cargar usuarios');
            }
            
        } catch (error) {
            console.error('❌ Error al cargar usuarios:', error);
            this.showError('Error de conexión al cargar usuarios: ' + error.message);
        } finally {
            this.showLoading(false);
        }
    }

    /**
     * Procesa la respuesta de la API según el formato documentado
     */
    async processUsersResponse(data) {
        let usersArray = [];
        let paginationData = {};

        // Según la documentación, la respuesta tiene esta estructura:
        // { users: [...], total: 150, page: 1, limit: 20, totalPages: 8 }
        if (data.users && Array.isArray(data.users)) {
            usersArray = data.users;
            paginationData = {
                total: data.total || 0,
                page: data.page || this.currentPage,
                limit: data.limit || this.itemsPerPage,
                totalPages: data.totalPages || Math.ceil((data.total || 0) / this.itemsPerPage)
            };
        } else if (Array.isArray(data)) {
            // Fallback si viene directamente como array
            usersArray = data;
            paginationData = { total: data.length };
        } else {
            throw new Error('Estructura de datos inesperada en la respuesta');
        }

        // Detectar cambios para notificaciones
        if (this.users && this.users.length > 0 && this.lastAction === 'auto_refresh') {
            this.detectUserChanges(this.users, usersArray.map(user => this.transformApiUser(user)));
        }

        // Transformar usuarios al formato interno
        this.users = usersArray.map(user => this.transformApiUser(user));
        
        // Actualizar información de paginación
        this.totalUsers = paginationData.total || 0;
        this.totalPages = paginationData.totalPages || 0;
        this.currentPage = paginationData.page || this.currentPage;

        console.log(`✅ ${this.users.length} usuarios cargados. Total: ${this.totalUsers}, Página: ${this.currentPage}/${this.totalPages}`);

        // Renderizar en la UI
        this.renderUsers();
        this.updatePaginationControls();
        this.updateCounters();
    }

    /**
     * Transforma un usuario de la API al formato interno de la UI
     */
    transformApiUser(apiUser) {
        const person = apiUser.person || {};
        const roles = apiUser.roles || [];
        const vehicle = apiUser.vehicle;
        const contacts = apiUser.contacts || [];
        const status = apiUser.userStatus || apiUser.status;
        const profiles = apiUser.profiles || {};
        
        console.log(`🔄 Transformando usuario ID ${apiUser.id}:`);
        console.log(`   - Nombre: ${person.name} ${person.lastName}`);
        console.log(`   - Roles:`, roles);
        console.log(`   - Status:`, status);
        
        // Determinar tipo de usuario basado en roles
        let userType = 'citizen'; // por defecto
        if (roles.some(r => r.name === 'ADMINISTRADOR')) {
            userType = 'admin';
        } else if (roles.some(r => r.name === 'CONDUCTOR')) {
            userType = 'driver';
        }
        
        // Estado del conductor (solo para conductores)
        let driverStatus = null;
        if (profiles.driver && profiles.driver.status) {
            driverStatus = profiles.driver.status;
        }
        
        // Obtener contactos
        let email = 'Sin email';
        let emailVerified = false;
        let emailContactId = null;
        const emailContact = contacts.find(c => c.type === 'CORREO ELECTRÓNICO');
        if (emailContact && emailContact.value) {
            email = emailContact.value;
            emailVerified = emailContact.confirmed || false;
            emailContactId = emailContact.id || null;
        }
        
        let phone = 'Sin teléfono';
        let phoneVerified = false;
        let phoneContactId = null;
        const phoneContact = contacts.find(c => c.type === 'TELÉFONO MÓVIL');
        if (phoneContact && phoneContact.value) {
            phone = phoneContact.value;
            phoneVerified = phoneContact.confirmed || false;
            phoneContactId = phoneContact.id || null;
        }
        
        const contactVerified = emailVerified || phoneVerified;
        
        // Información del vehículo
        let vehiclePlate = null;
        if (vehicle && vehicle.plate) {
            vehiclePlate = vehicle.plate;
        }

        const result = {
            id: apiUser.id,
            firstname: person.name || person.firstName || 'Sin nombre',
            lastname: person.lastName || 'Sin apellido',
            email: email,
            emailVerified: emailVerified,
            emailContactId: emailContactId,
            document: person.document || 'Sin documento',
            doctype: person.documentType || 'N/A',
            phone: phone,
            phoneVerified: phoneVerified,
            phoneContactId: phoneContactId,
            userType: userType,
            driverStatus: driverStatus,
            active: status ? (status.id === 1) : true,
            hasVehicle: vehicle !== null,
            contactVerified: contactVerified,
            vehicle: vehicle,
            vehiclePlate: vehiclePlate,
            roles: roles.map(r => r.name),
            rawData: apiUser
        };
        
        return result;
    }

    /**
     * Renderiza la lista de usuarios en la tabla
     */
    renderUsers() {
        console.log('🎨 === RENDERIZANDO USUARIOS ===');
        console.log('📊 Usuarios a renderizar:', this.users.length);
        
        const tbody = document.getElementById('users-list');
        if (!tbody) {
            console.error('❌ No se encontró el elemento users-list');
            return;
        } else {
            console.log('✅ Elemento users-list encontrado');
        }

        tbody.innerHTML = '';
        console.log('🧹 Tabla limpiada');

        if (this.users.length === 0) {
            console.log('📭 No hay usuarios para mostrar');
            const message = this.searchTerm ? 
                `No se encontraron usuarios que coincidan con "${this.searchTerm}"` : 
                'No se encontraron usuarios';
            
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <div class="text-muted">${message}</div>
                    </td>
                </tr>
            `;
            return;
        }

        console.log('👥 Renderizando', this.users.length, 'usuarios...');
        this.users.forEach((user, index) => {
            console.log(`🧑 Renderizando usuario ${index + 1}:`, {
                id: user.id,
                firstname: user.firstname,
                lastname: user.lastname,
                email: user.email,
                active: user.active
            });
            
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <div class="user-info">
                        <div class="user-name">${user.firstname} ${user.lastname}</div>
                        <div class="user-document d-sm-none">${user.document}</div>
                    </div>
                </td>
                <td class="d-none d-md-table-cell">${user.document}</td>
                <td class="d-none d-lg-table-cell">
                    <div class="user-email" title="${user.email}">
                        ${user.email}
                        ${user.emailVerified ? 
                            '<i class="fas fa-check-circle text-success ms-1" title="Verificado"></i>' : 
                            '<i class="fas fa-times-circle text-danger ms-1" title="No Verificado"></i>'
                        }
                    </div>
                </td>
                <td class="d-none d-lg-table-cell">
                    ${user.phone !== 'Sin teléfono' ? `
                        <div>
                            ${user.phone}
                            ${user.phoneVerified ? 
                                '<i class="fas fa-check-circle text-success ms-1" title="Verificado"></i>' : 
                                '<i class="fas fa-times-circle text-danger ms-1" title="No Verificado"></i>'
                            }
                        </div>
                    ` : '<span class="text-muted">Sin teléfono</span>'}
                </td>
                <td class="col-status">
                    <span class="badge ${user.active ? 'bg-success' : 'bg-danger'} text-white" 
                          onclick="usersListController.toggleUserStatus(${user.id})" 
                          style="cursor: pointer;" 
                          title="Clic para cambiar estado">
                        <i class="fas fa-${user.active ? 'check' : 'times'} me-1"></i>
                        ${user.active ? 'Activo' : 'Inactivo'}
                    </span>
                </td>
                <td class="col-actions">
                    <div class="action-buttons">
                        <button class="btn btn-sm btn-outline-primary" onclick="usersListController.viewUserDetails(${user.id})" title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="usersListController.editUser(${user.id})" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
        
        console.log('✅ Renderizado completado. Filas agregadas:', this.users.length);
        console.log('📊 Contenido actual de tbody:', tbody.children.length, 'filas');
    }

    /**
     * Obtiene la clase CSS para el badge del tipo de usuario
     */
    getUserTypeBadgeClass(userType) {
        switch (userType) {
            case 'admin': return 'text-white'; // Dorado para administradores (se aplica style inline)
            case 'driver': return 'bg-warning text-dark'; // Amarillo para conductores
            case 'citizen': return 'bg-info text-white'; // Azul celeste para ciudadanos
            default: return 'bg-secondary text-white';
        }
    }

    /**
     * Obtiene el estilo inline para el badge del tipo de usuario
     */
    getUserTypeBadgeStyle(userType) {
        switch (userType) {
            case 'admin': return 'background-color: #d4af37;'; // Dorado para administradores
            default: return '';
        }
    }

    /**
     * Obtiene la etiqueta del tipo de usuario
     */
    getUserTypeLabel(userType) {
        switch (userType) {
            case 'admin': return 'Administrador';
            case 'driver': return 'Conductor';
            case 'citizen': return 'Ciudadano';
            default: return 'Usuario';
        }
    }

    /**
     * Maneja la búsqueda con debounce
     */
    handleSearch(searchTerm) {
        console.log('🔍 Buscando:', searchTerm);
        
        if (this.searchTimeout) {
            clearTimeout(this.searchTimeout);
        }
        
        this.searchTerm = searchTerm.trim();
        
        // Mostrar/ocultar botón limpiar
        const clearBtn = document.getElementById('clear-search');
        if (clearBtn) {
            clearBtn.style.display = this.searchTerm ? 'block' : 'none';
        }
        
        // Búsqueda con delay
        this.searchTimeout = setTimeout(() => {
            this.currentPage = 1;
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
     * Limpia todos los filtros
     */
    clearAllFilters() {
        console.log('🧹 Limpiando todos los filtros');
        
        // Resetear filtros activos
        this.activeFilters = {
            userType: null,
            driverStatus: null,
            hasVehicle: null,
            contactVerified: null
        };
        
        // Resetear selects de filtros
        const filterSelects = [
            'filter-user-type',
            'filter-driver-status'
        ];
        
        filterSelects.forEach(selectId => {
            const select = document.getElementById(selectId);
            if (select) {
                select.value = '';
            }
        });
        
        // Desmarcar checkboxes de filtros
        const filterCheckboxes = [
            'filter-has-vehicle',
            'filter-contact-verified'
        ];
        
        filterCheckboxes.forEach(checkboxId => {
            const checkbox = document.getElementById(checkboxId);
            if (checkbox) {
                checkbox.checked = false;
            }
        });
        
        this.currentPage = 1;
        this.loadUsers();
    }

    /**
     * Sincroniza el selector de elementos por página con el valor actual
     */
    syncItemsPerPageSelector() {
        const itemsPerPageSelect = document.getElementById('items-per-page');
        if (itemsPerPageSelect) {
            itemsPerPageSelect.value = this.itemsPerPage.toString();
        }
    }

    /**
     * Navegación - página anterior
     */
    previousPage() {
        if (this.currentPage > 1) {
            this.currentPage--;
            this.loadUsers();
        }
    }

    /**
     * Navegación - página siguiente
     */
    nextPage() {
        if (this.currentPage < this.totalPages) {
            this.currentPage++;
            this.loadUsers();
        }
    }

    /**
     * Maneja el cambio de elementos por página
     */
    handleItemsPerPageChange(newItemsPerPage) {
        // Validar que no exceda el máximo permitido
        const maxItemsPerPage = 20;
        if (newItemsPerPage > maxItemsPerPage) {
            console.warn(`⚠️ Intentando establecer ${newItemsPerPage} elementos por página, limitando a ${maxItemsPerPage}`);
            newItemsPerPage = maxItemsPerPage;
        }
        
        console.log(`📄 Cambiando elementos por página de ${this.itemsPerPage} a ${newItemsPerPage}`);
        this.itemsPerPage = newItemsPerPage;
        this.currentPage = 1; // Resetear a la primera página
        this.syncItemsPerPageSelector(); // Sincronizar el selector
        this.loadUsers();
    }

    /**
     * Navega a una página específica
     */
    goToPage(pageNumber) {
        if (pageNumber >= 1 && pageNumber <= this.totalPages) {
            this.currentPage = pageNumber;
            this.loadUsers();
        }
    }

    /**
     * Actualiza los controles de paginación
     */
    updatePaginationControls() {
        this.updatePaginationInfo();
        this.generatePaginationButtons();
    }

    /**
     * Genera los botones de paginación dinámicamente
     */
    generatePaginationButtons() {
        const paginationContainer = document.getElementById('pagination-container');
        if (!paginationContainer) return;

        paginationContainer.innerHTML = '';

        // No mostrar paginación si hay una página o menos
        if (this.totalPages <= 1) return;

        // Botón anterior
        const prevItem = document.createElement('li');
        prevItem.className = `page-item ${this.currentPage <= 1 ? 'disabled' : ''}`;
        prevItem.innerHTML = `
            <a class="page-link" href="#" aria-label="Anterior">
                <i class="fas fa-chevron-left"></i>
                <span class="sr-only">Anterior</span>
            </a>`;
        if (this.currentPage > 1) {
            prevItem.addEventListener('click', (e) => {
                e.preventDefault();
                this.previousPage();
            });
        }
        paginationContainer.appendChild(prevItem);

        // Calcular rango de páginas a mostrar
        const maxVisiblePages = 5;
        let startPage = Math.max(1, this.currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(this.totalPages, startPage + maxVisiblePages - 1);

        // Ajustar si no hay suficientes páginas al final
        if (endPage - startPage + 1 < maxVisiblePages) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
        }

        // Primera página si no está visible
        if (startPage > 1) {
            const firstItem = document.createElement('li');
            firstItem.className = 'page-item';
            firstItem.innerHTML = '<a class="page-link" href="#">1</a>';
            firstItem.addEventListener('click', (e) => {
                e.preventDefault();
                this.goToPage(1);
            });
            paginationContainer.appendChild(firstItem);

            if (startPage > 2) {
                const dotsItem = document.createElement('li');
                dotsItem.className = 'page-item disabled';
                dotsItem.innerHTML = '<span class="page-link">...</span>';
                paginationContainer.appendChild(dotsItem);
            }
        }

        // Páginas numeradas
        for (let i = startPage; i <= endPage; i++) {
            const pageItem = document.createElement('li');
            pageItem.className = `page-item ${i === this.currentPage ? 'active' : ''}`;
            pageItem.innerHTML = `<a class="page-link" href="#">${i}</a>`;
            
            if (i !== this.currentPage) {
                pageItem.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.goToPage(i);
                });
            }
            paginationContainer.appendChild(pageItem);
        }

        // Última página si no está visible
        if (endPage < this.totalPages) {
            if (endPage < this.totalPages - 1) {
                const dotsItem = document.createElement('li');
                dotsItem.className = 'page-item disabled';
                dotsItem.innerHTML = '<span class="page-link">...</span>';
                paginationContainer.appendChild(dotsItem);
            }

            const lastItem = document.createElement('li');
            lastItem.className = 'page-item';
            lastItem.innerHTML = `<a class="page-link" href="#">${this.totalPages}</a>`;
            lastItem.addEventListener('click', (e) => {
                e.preventDefault();
                this.goToPage(this.totalPages);
            });
            paginationContainer.appendChild(lastItem);
        }

        // Botón siguiente
        const nextItem = document.createElement('li');
        nextItem.className = `page-item ${this.currentPage >= this.totalPages ? 'disabled' : ''}`;
        nextItem.innerHTML = `
            <a class="page-link" href="#" aria-label="Siguiente">
                <i class="fas fa-chevron-right"></i>
                <span class="sr-only">Siguiente</span>
            </a>`;
        if (this.currentPage < this.totalPages) {
            nextItem.addEventListener('click', (e) => {
                e.preventDefault();
                this.nextPage();
            });
        }
        paginationContainer.appendChild(nextItem);
    }

    /**
     * Actualiza la información de paginación
     */
    updatePaginationInfo() {
        const showingStart = document.getElementById('showing-start');
        const showingEnd = document.getElementById('showing-end');
        const totalUsersSpan = document.getElementById('total-users');
        
        if (showingStart && showingEnd && totalUsersSpan) {
            if (this.totalUsers === 0) {
                showingStart.textContent = '0';
                showingEnd.textContent = '0';
                totalUsersSpan.textContent = '0';
            } else {
                // Calcular el rango basado en la paginación
                const start = (this.currentPage - 1) * this.itemsPerPage + 1;
                const end = Math.min(this.currentPage * this.itemsPerPage, this.totalUsers);
                
                showingStart.textContent = start.toString();
                showingEnd.textContent = end.toString();
                totalUsersSpan.textContent = this.totalUsers.toString();
            }
        }
    }

    /**
     * Actualiza contadores de la UI
     */
    updateCounters() {
        const counter = document.getElementById('users-count');
        if (counter) {
            counter.textContent = this.totalUsers;
        }
    }

    /**
     * Muestra/oculta indicador de carga
     */
    showLoading(show) {
        this.isLoading = show;
        
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
     * Muestra un error en la tabla
     */
    showError(message) {
        const tbody = document.getElementById('users-list');
        if (tbody) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-5 text-danger">
                        <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                        <div><strong>Error:</strong> ${message}</div>
                        <button class="btn btn-outline-primary mt-3" onclick="usersListController.loadUsers()">
                            <i class="fas fa-refresh"></i> Reintentar
                        </button>
                    </td>
                </tr>
            `;
        }
    }

    /**
     * Muestra un mensaje toast
     */
    showToast(message, type = 'success') {
        if (window.GlobalToast) {
            window.GlobalToast.show(message, type);
        } else {
            // Fallback si GlobalToast no está disponible
            const prefix = type === 'error' ? '❌' : type === 'warning' ? '⚠️' : '✅';
            console.log(`${prefix} Toast: ${message} (${type})`);
        }
    }

    /**
     * Manejo de refresh manual
     */
    handleRefreshUsers() {
        console.log('🔄 Refrescando lista manualmente...');
        this.lastAction = 'manual_refresh';
        
        const refreshBtn = document.getElementById('refresh-users-btn');
        if (refreshBtn) {
            const icon = refreshBtn.querySelector('i');
            if (icon) {
                icon.style.animation = 'spin 1s linear infinite';
            }
            refreshBtn.disabled = true;
        }
        
        this.loadUsers().finally(() => {
            if (refreshBtn) {
                const icon = refreshBtn.querySelector('i');
                if (icon) {
                    icon.style.animation = '';
                }
                refreshBtn.disabled = false;
            }
        });
    }

    /**
     * Alias para refresh de usuarios (usado por controlador de roles)
     */
    refreshUsersList() {
        console.log('🔄 Refrescando lista de usuarios...');
        this.lastAction = 'manual_refresh';
        this.loadUsers();
    }

    /**
     * Detecta cambios para notificaciones (solo para refresh automático)
     */
    detectUserChanges(oldUsers, newUsers) {
        if (this.lastAction !== 'auto_refresh') return;
        
        // Implementar detección de cambios si es necesario
        console.log('🔍 Detectando cambios en usuarios...');
    }

    /**
     * Ver detalles de usuario (usar el controlador especializado)
     */
    viewUserDetails(userId) {
        console.log('👁️ Ver detalles del usuario:', userId);
        
        // Verificar que el controlador de detalles esté disponible
        if (window.UserDetailsController) {
            const user = this.users.find(u => u.id === userId);
            window.UserDetailsController.viewUser(userId, user);
        } else {
            console.error('❌ UserDetailsController no está disponible');
            this.showToast('Error: Controlador de detalles no disponible', 'error');
        }
    }

    /**
     * Editar usuario - abre modal de asignación de roles
     */
    editUser(userId) {
        console.log('✏️ Editar usuario (asignar roles):', userId);
        
        const user = this.users.find(u => u.id === userId);
        if (!user) {
            console.error('❌ Usuario no encontrado:', userId);
            this.showToast('Usuario no encontrado', 'error');
            return;
        }
        
        // Abrir modal de asignación de roles
        if (window.assignRolesController) {
            window.assignRolesController.openAssignRolesModal(userId, user);
        } else {
            console.error('❌ assignRolesController no está disponible');
            this.showToast('Error: Controlador de roles no disponible', 'error');
        }
    }

    /**
     * Cambiar estado de usuario
     */
    async toggleUserStatus(userId) {
        console.log('🔄 Cambiando estado del usuario:', userId);
        
        try {
            // Encontrar el usuario actual
            const user = this.users.find(u => u.id === userId);
            if (!user) {
                console.error('❌ Usuario no encontrado:', userId);
                this.showToast('Usuario no encontrado', 'error');
                return;
            }

            // Determinar el nuevo estado (1 = activo, 2 = inactivo)
            const newStatusId = user.active ? 2 : 1;
            const statusText = user.active ? 'inactivo' : 'activo';
            
            console.log(`🔄 Cambiando usuario ${userId} de ${user.active ? 'activo' : 'inactivo'} a ${statusText}`);

            // Llamar al servicio para cambiar el estado
            const result = await UserService.updateUserStatus(userId, newStatusId);
            
            if (result.success) {
                // Actualizar el estado local del usuario
                user.active = !user.active;
                
                // Volver a renderizar la lista
                this.renderUsers();
                
                // Mostrar mensaje de éxito
                this.showToast(`Usuario marcado como ${statusText} correctamente`, 'success');
                
                console.log(`✅ Estado del usuario ${userId} cambiado a ${statusText}`);
            } else {
                console.error('❌ Error al cambiar estado:', result.message);
                this.showToast(result.message || 'Error al cambiar el estado del usuario', 'error');
            }
            
        } catch (error) {
            console.error('❌ Error al cambiar estado del usuario:', error);
            this.showToast('Error al cambiar el estado del usuario', 'error');
        }
    }

    /**
     * Obtener usuario por ID
     */
    getUserById(userId) {
        return this.users.find(u => u.id === userId);
    }

    /**
     * Recargar la lista
     */
    reload() {
        this.loadUsers();
    }

    /**
     * Obtener estado actual
     */
    getState() {
        return {
            users: this.users,
            currentPage: this.currentPage,
            totalUsers: this.totalUsers,
            totalPages: this.totalPages,
            filters: { ...this.activeFilters },
            searchTerm: this.searchTerm,
            isLoading: this.isLoading
        };
    }

    /**
     * Maneja la creación de un nuevo usuario
     */
    handleCreateUser() {
        console.log('🆕 Abriendo modal de crear usuario...');
        
        // Buscar instancia del controlador de creación
        if (window.userCreateController && typeof window.userCreateController.openModal === 'function') {
            window.userCreateController.openModal();
        } else {
            console.error('❌ userCreateController no está disponible');
            this.showToast('Error: No se pudo abrir el formulario de creación', 'error');
        }
    }

    /**
     * Refresca la lista de usuarios (llamado después de crear un usuario)
     */
    refreshUsers() {
        console.log('🔄 Refrescando lista de usuarios...');
        this.loadUsers();
    }
}

// Instancia global para acceso desde HTML
window.usersListController = null;
