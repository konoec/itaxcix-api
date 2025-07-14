/**
 * Controlador para ver detalles completos de usuario
 * Endpoint: /api/v1/users/{userId}
 * Funcionalidades:
 * - Mostrar información completa del usuario
 * - Modal con detalles organizados por secciones
 * - Información básica, contactos, roles, perfiles, vehículo
 */
class UserDetailsController {
    constructor() {
        console.log('👁️ UserDetailsController constructor ejecutado');
        
        // Estado del modal
        this.isModalOpen = false;
        this.currentUserId = null;
        this.currentUserData = null;
        
        // Referencias a elementos del DOM
        this.modal = null;
        this.loadingIndicator = null;
        this.errorDiv = null;
        
        // Inicializar automáticamente
        this.init();
    }

    /**
     * Inicializa el controlador
     */
    init() {
        console.log('👁️ Inicializando UserDetailsController...');
        try {
            // Obtener referencias a elementos del DOM
            this.setupDOMReferences();
            
            // Configurar event listeners
            this.setupEventListeners();
            
            console.log('✅ UserDetailsController inicializado correctamente');
        } catch (error) {
            console.error('❌ Error al inicializar UserDetailsController:', error);
        }
    }

    /**
     * Configura las referencias a elementos del DOM
     */
    setupDOMReferences() {
        this.modal = document.getElementById('user-details-modal');
        this.loadingIndicator = document.getElementById('user-details-loading');
        this.errorDiv = document.getElementById('user-details-error');
        
        if (!this.modal) {
            console.warn('⚠️ Modal de detalles de usuario no encontrado en el DOM');
        }
    }

    /**
     * Configura los event listeners
     */
    setupEventListeners() {
        // Cerrar modal con botón X (Tabler usa data-bs-dismiss)
        const closeBtn = this.modal?.querySelector('[data-bs-dismiss="modal"]');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.closeModal());
        }

        // Cerrar modal al hacer clic fuera
        if (this.modal) {
            this.modal.addEventListener('click', (e) => {
                if (e.target === this.modal) {
                    this.closeModal();
                }
            });
        }

        // Cerrar con tecla Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isModalOpen) {
                this.closeModal();
            }
        });
    }

    /**
     * Muestra los detalles completos de un usuario
     * @param {number} userId - ID del usuario
     * @param {Object} userBasicData - Datos básicos del usuario (opcional)
     */
    async viewUser(userId, userBasicData = null) {
        console.log('👁️ Abriendo detalles completos del usuario:', userId);
        
        if (!this.modal) {
            console.error('❌ Modal de detalles completos no encontrado');
            this.showToast('Error: Modal no disponible', 'error');
            return;
        }

        // Guardar estado
        this.currentUserId = userId;
        this.isModalOpen = true;

        // Mostrar el modal usando Bootstrap
        if (this.modal) {
            // Usar Bootstrap modal
            const bootstrapModal = new bootstrap.Modal(this.modal);
            bootstrapModal.show();
        }
        
        // Mostrar loading
        this.showLoading(true);
        
        // Ocultar todas las secciones
        this.hideAllDetailsSections();

        try {
            // Verificar que UserService esté disponible
            if (typeof UserService === 'undefined') {
                throw new Error('UserService no está disponible');
            }

            console.log('📡 Obteniendo detalles del usuario desde /api/v1/users/' + userId);
            
            // Llamar a la API para obtener detalles del usuario
            const response = await UserService.getUserFullDetails(userId);
            
            console.log('📡 Respuesta de /api/v1/users/' + userId + ':', response);
            
            if (response && response.success && response.data) {
                this.currentUserData = response.data;
                this.populateUserDetailsModal(response.data, userId);
            } else {
                throw new Error(response?.message || 'Error al obtener detalles del usuario');
            }
        } catch (error) {
            console.error('❌ Error al cargar detalles del usuario:', error);
            this.showUserDetailsError(error.message);
        } finally {
            this.showLoading(false);
        }
    }

    /**
     * Muestra/oculta el indicador de carga
     */
    showLoading(show) {
        const contentContainer = document.getElementById('user-details-content');
        if (contentContainer) {
            if (show) {
                contentContainer.innerHTML = `
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <div class="mt-2">Cargando detalles del usuario...</div>
                    </div>
                `;
            }
        }
    }

    /**
     * Oculta todas las secciones de detalles del modal
     */
    hideAllDetailsSections() {
        const contentContainer = document.getElementById('user-details-content');
        if (contentContainer) {
            // Limpiar el contenido del contenedor
            contentContainer.innerHTML = '';
        }
    }

    /**
     * Muestra un error en el modal usando el estilo de Tabler
     */
    showUserDetailsError(message) {
        const contentContainer = document.getElementById('user-details-content');
        if (contentContainer) {
            contentContainer.innerHTML = `
                <div class="empty">
                    <div class="empty-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-danger" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 9v2m0 4v.01"/>
                            <path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75"/>
                        </svg>
                    </div>
                    <p class="empty-title">Error al cargar detalles</p>
                    <p class="empty-subtitle text-secondary">${message}</p>
                    <div class="empty-action">
                        <button class="btn btn-outline-primary" onclick="window.UserDetailsController.refresh()">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"/>
                                <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"/>
                            </svg>
                            Reintentar
                        </button>
                    </div>
                </div>
            `;
        }
    }

    /**
     * Llena el modal con los datos del usuario según la estructura de la API
     */
    populateUserDetailsModal(data, userId) {
        console.log('📋 Poblando modal con datos del usuario:', data);

        // Actualizar el título del modal con el nombre del usuario
        const modalTitle = this.modal?.querySelector('.modal-title');
        if (modalTitle && data.person) {
            const fullName = `${data.person.name || ''} ${data.person.lastName || ''}`.trim();
            modalTitle.innerHTML = `<i class="fas fa-user me-2"></i>${fullName || 'Usuario ' + userId}`;
        }
        
        // Obtener el contenedor de contenido
        const contentContainer = document.getElementById('user-details-content');
        if (!contentContainer) {
            console.error('❌ Contenedor de detalles no encontrado');
            return;
        }

        // Generar HTML con los detalles del usuario
        contentContainer.innerHTML = this.generateUserDetailsHTML(data);
    }

    /**
     * Genera el HTML con los detalles del usuario usando componentes de Tabler
     * Muestra los campos según la estructura real de la API
     */
    generateUserDetailsHTML(data) {
        console.log('🔍 Datos recibidos de la API:', data);
        
        let html = `<div class="px-3 py-2">`;
        
        // 1. Información del Usuario - ID y Estado
        html += `
            <div class="card mb-3">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>Usuario
                    </h4>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-4">ID:</dt>
                        <dd class="col-8"><span class="badge bg-primary text-white">${data.userId || '-'}</span></dd>
                        <dt class="col-4">Estado:</dt>
                        <dd class="col-8">
                            <span class="badge ${data.userStatus?.name === 'ACTIVO' ? 'bg-success' : 'bg-danger'} text-white">
                                ${data.userStatus?.name || '-'}
                            </span>
                        </dd>
                    </dl>
                </div>
            </div>
        `;
        
        // 2. Información Personal (person)
        if (data.person) {
            html += `
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-id-card me-2"></i>Información Personal
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <dl class="row mb-0">
                                    <dt class="col-4">Nombre:</dt>
                                    <dd class="col-8">${data.person.name || '-'}</dd>
                                    <dt class="col-4">Apellido:</dt>
                                    <dd class="col-8">${data.person.lastName || '-'}</dd>
                                    <dt class="col-4">Documento:</dt>
                                    <dd class="col-8">${data.person.document || '-'}</dd>
                                    <dt class="col-4">Tipo Documento:</dt>
                                    <dd class="col-8">${data.person.documentType?.name || '-'}</dd>
                                    <dt class="col-4">Fecha Validación:</dt>
                                    <dd class="col-8">${data.person.validationDate ? this.formatDateTime(data.person.validationDate) : '-'}</dd>
                                    <dt class="col-4">Estado:</dt>
                                    <dd class="col-8">
                                        <span class="badge ${data.person.active ? 'bg-success' : 'bg-danger'} text-white">
                                            ${data.person.active ? 'Activo' : 'Inactivo'}
                                        </span>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        // 3. Contactos
        if (data.contacts && data.contacts.length > 0) {
            // Agrupar contactos por tipo
            const contactsByType = {};
            data.contacts.forEach(contact => {
                const typeName = contact.type?.name || 'Sin tipo';
                if (!contactsByType[typeName]) {
                    contactsByType[typeName] = [];
                }
                contactsByType[typeName].push(contact);
            });
            
            html += `
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-address-book me-2"></i>Contactos (${data.contacts.length})
                        </h4>
                    </div>
                    <div class="card-body">
            `;
            
            Object.entries(contactsByType).forEach(([typeName, contacts]) => {
                html += `
                    <div class="mb-4">
                        <h5 class="mb-3">
                            <i class="fas fa-${typeName.includes('CORREO') ? 'envelope' : 'phone'} me-2"></i>
                            ${typeName}
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Valor</th>
                                        <th>Estado</th>
                                        <th>Verificado</th>
                                    </tr>
                                </thead>
                                <tbody>
                `;
                
                contacts.forEach(contact => {
                    html += `
                        <tr>
                            <td>${contact.value || '-'}</td>
                            <td>
                                <span class="badge ${contact.active ? 'bg-success' : 'bg-danger'} text-white">
                                    <i class="fas fa-${contact.active ? 'check' : 'times'} me-1"></i>
                                    ${contact.active ? 'Activo' : 'Inactivo'}
                                </span>
                            </td>
                            <td>
                                <span class="badge ${contact.confirmed ? 'bg-success' : 'bg-danger'} text-white">
                                    <i class="fas fa-${contact.confirmed ? 'check' : 'times'} me-1"></i>
                                    ${contact.confirmed ? 'Verificado' : 'No Verificado'}
                                </span>
                            </td>
                        </tr>
                    `;
                });
                
                html += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
            });
            
            html += `
                    </div>
                </div>
            `;
        }
        
        // 4. Roles
        if (data.roles && data.roles.length > 0) {
            html += `
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-user-tag me-2"></i>Roles (${data.roles.length})
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
            `;
            
            data.roles.forEach(role => {
                html += `
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card border">
                            <div class="card-body p-3">
                                <h6 class="card-title mb-2">
                                    <i class="fas fa-shield-alt me-1"></i>
                                    <span class="badge ${this.getRoleBadgeClass(role.name)}" style="${this.getRoleBadgeStyle(role.name)}">
                                        ${role.name || 'Sin nombre'}
                                    </span>
                                </h6>
                                <div class="mb-2">
                                    <span class="badge ${role.active ? 'bg-success' : 'bg-danger'} text-white">
                                        <i class="fas fa-${role.active ? 'check' : 'times'} me-1"></i>
                                        ${role.active ? 'Activo' : 'Inactivo'}
                                    </span>
                                </div>
                                <div>
                                    <span class="badge ${role.web ? 'text-white' : 'bg-info text-white'}" ${role.web ? 'style="background-color: #d4af37;"' : ''}>
                                        <i class="fas fa-${role.web ? 'desktop' : 'mobile-alt'} me-1"></i>
                                        ${role.web ? 'Web' : 'Móvil'}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += `
                        </div>
                    </div>
                </div>
            `;
        }
        
        // 5. Perfil de Ciudadano (si existe)
        if (data.citizenProfile) {
            html += `
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-user-friends me-2"></i>Perfil de Ciudadano
                        </h4>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-4">Rating Promedio:</dt>
                            <dd class="col-8">
                                <span class="badge bg-warning text-white">
                                    <i class="fas fa-star me-1"></i>
                                    ${data.citizenProfile.averageRating || '0'}
                                </span>
                            </dd>
                            <dt class="col-4">Total Ratings:</dt>
                            <dd class="col-8">${data.citizenProfile.ratingCount || '0'}</dd>
                        </dl>
                    </div>
                </div>
            `;
        }
        
        // 6. Perfil de Conductor (si existe)
        if (data.driverProfile) {
            html += `
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-car me-2"></i>Perfil de Conductor
                        </h4>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-4">Estado:</dt>
                            <dd class="col-8">
                                <span class="badge ${data.driverProfile.status?.name === 'DISPONIBLE' ? 'bg-success' : 
                                    data.driverProfile.status?.name === 'OCUPADO' ? 'bg-warning' : 'bg-danger'} text-white">
                                    ${data.driverProfile.status?.name || '-'}
                                </span>
                            </dd>
                            <dt class="col-4">Rating Promedio:</dt>
                            <dd class="col-8">
                                <span class="badge bg-warning text-white">
                                    <i class="fas fa-star me-1"></i>
                                    ${data.driverProfile.averageRating || '0'}
                                </span>
                            </dd>
                            <dt class="col-4">Total Ratings:</dt>
                            <dd class="col-8">${data.driverProfile.ratingCount || '0'}</dd>
                        </dl>
                    </div>
                </div>
            `;
        }
        
        // 7. Información del Vehículo (si existe)
        if (data.vehicle) {
            html += `
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title mb-0">
                            <i class="fas fa-car-side me-2"></i>Vehículo
                        </h4>
                    </div>
                    <div class="card-body">
                        <dl class="row mb-0">
                            <dt class="col-4">Placa:</dt>
                            <dd class="col-8">
                                <span class="badge bg-dark text-white">${data.vehicle.plate || '-'}</span>
                            </dd>
                            <dt class="col-4">Marca:</dt>
                            <dd class="col-8">${data.vehicle.brand?.name || '-'}</dd>
                            <dt class="col-4">Modelo:</dt>
                            <dd class="col-8">${data.vehicle.model?.name || '-'}</dd>
                            <dt class="col-4">Año:</dt>
                            <dd class="col-8">${data.vehicle.year || '-'}</dd>
                            <dt class="col-4">Color:</dt>
                            <dd class="col-8">${data.vehicle.color?.name || data.vehicle.color?.id || '-'}</dd>
                        </dl>
                    </div>
                </div>
            `;
        }
        
        html += `</div>`;
        
        return html;
    }

    /**
     * Llena la información básica del usuario
     */
    populateBasicInfo(data, userId) {
        const basicInfoSection = document.getElementById('user-basic-info');
        
        if (data.person) {
            const fullName = `${data.person.name || ''} ${data.person.lastName || ''}`.trim();
            this.setElementText('detail-user-fullname', fullName || '-');
            this.setElementText('detail-user-document', data.person.document || '-');
            this.setElementText('detail-user-doctype', data.person.documentType?.name || '-');
            
            // Manejar fecha de validación que puede ser null o no existir
            const validationDate = data.person.validationDate ? 
                this.formatDateTime(data.person.validationDate) : '-';
            this.setElementText('detail-user-validation', validationDate);
            
            this.setElementText('detail-person-active', data.person.active ? 'Activo' : 'Inactivo');
        } else {
            this.setElementText('detail-user-fullname', '-');
            this.setElementText('detail-user-document', '-');
            this.setElementText('detail-user-doctype', '-');
            this.setElementText('detail-user-validation', '-');
            this.setElementText('detail-person-active', '-');
        }
        
        // Estado del usuario (userStatus según la API)
        this.setElementText('detail-user-status', data.userStatus?.name || '-');
        
        if (basicInfoSection) {
            basicInfoSection.style.display = 'block';
        }
    }

    /**
     * Llena la información de contactos
     */
    populateContactsInfo(contacts) {
        const contactsSection = document.getElementById('contacts-info-section');
        const contactsGrid = document.getElementById('contacts-profile-grid');
        const noContactsMessage = document.getElementById('no-contacts-message');
        
        if (!contactsGrid) {
            console.warn('⚠️ Grid de contactos no encontrado');
            return;
        }

        // Limpiar grid
        contactsGrid.innerHTML = '';
        
        if (contacts.length === 0) {
            if (noContactsMessage) {
                noContactsMessage.style.display = 'block';
            }
        } else {
            if (noContactsMessage) {
                noContactsMessage.style.display = 'none';
            }
            
            contacts.forEach(contact => {
                const contactCard = document.createElement('div');
                contactCard.className = 'contact-profile-card';
                
                contactCard.innerHTML = `
                    <div class="contact-profile-header">
                        <i class="fas fa-address-book"></i>
                        <h4>${contact.type?.name || contact.type || 'Contacto'}</h4>
                    </div>
                    <div class="contact-profile-info">
                        <div class="contact-profile-item">
                            <label>Valor:</label>
                            <span>${contact.value || '-'}</span>
                        </div>
                        <div class="contact-profile-item">
                            <label>ID Contacto:</label>
                            <span class="contact-id">${contact.id || '-'}</span>
                        </div>
                        <div class="contact-profile-item">
                            <label>Confirmado:</label>
                            <span class="status-${contact.confirmed ? 'confirmed' : 'not-confirmed'}">
                                ${contact.confirmed ? 'Confirmado' : 'No Confirmado'}
                            </span>
                        </div>
                        <div class="contact-profile-item">
                            <label>Estado:</label>
                            <span class="status-${contact.active ? 'active' : 'inactive'}">
                                ${contact.active ? 'Activo' : 'Inactivo'}
                            </span>
                        </div>
                    </div>
                `;
                
                contactsGrid.appendChild(contactCard);
            });
        }
        
        if (contactsSection) {
            contactsSection.style.display = 'block';
        }
    }

    /**
     * Llena la información de roles
     */
    populateRolesInfo(roles) {
        const rolesSection = document.getElementById('roles-info-section');
        const rolesGrid = document.getElementById('roles-profile-grid');
        const noRolesMessage = document.getElementById('no-roles-message-details');
        
        if (!rolesGrid) {
            console.warn('⚠️ Grid de roles no encontrado');
            return;
        }

        // Limpiar grid
        rolesGrid.innerHTML = '';
        
        if (roles.length === 0) {
            if (noRolesMessage) {
                noRolesMessage.style.display = 'block';
            }
        } else {
            if (noRolesMessage) {
                noRolesMessage.style.display = 'none';
            }
            
            roles.forEach(role => {
                const roleCard = document.createElement('div');
                roleCard.className = 'role-profile-card';
                
                roleCard.innerHTML = `
                    <div class="role-profile-header">
                        <i class="fas fa-user-tag"></i>
                        <h4>${role.name || 'Rol'}</h4>
                    </div>
                    <div class="role-profile-info">
                        <div class="role-profile-item">
                            <label>Estado:</label>
                            <span class="status-${role.active ? 'active' : 'inactive'}">
                                ${role.active ? 'Activo' : 'Inactivo'}
                            </span>
                        </div>
                        <div class="role-profile-item">
                            <label>Plataforma:</label>
                            <span class="role-${role.web ? 'web' : 'mobile'}">
                                ${role.web ? 'Web' : 'Móvil'}
                            </span>
                        </div>
                    </div>
                `;
                
                rolesGrid.appendChild(roleCard);
            });
        }
        
        if (rolesSection) {
            rolesSection.style.display = 'block';
        }
    }

    /**
     * Llena la información de perfiles
     */
    populateProfilesInfo(data) {
        const profilesSection = document.getElementById('profiles-info-section');
        const citizenProfile = document.getElementById('citizen-profile');
        const driverProfile = document.getElementById('driver-profile');
        
        let hasProfiles = false;
        
        // Perfil de ciudadano
        if (data.citizenProfile) {
            this.setElementText('citizen-rating', data.citizenProfile.averageRating || '0');
            this.setElementText('citizen-rating-count', data.citizenProfile.ratingCount || '0');
            if (citizenProfile) {
                citizenProfile.style.display = 'block';
            }
            hasProfiles = true;
        } else {
            if (citizenProfile) {
                citizenProfile.style.display = 'none';
            }
        }
        
        // Perfil de conductor
        if (data.driverProfile) {
            // Estado del conductor con styling
            const statusElement = document.getElementById('driver-status');
            if (statusElement) {
                const statusName = data.driverProfile.status?.name || '-';
                const statusBadgeClass = statusName.toLowerCase() === 'disponible' ? 'bg-success' : 
                                        statusName.toLowerCase() === 'ocupado' ? 'bg-warning' :
                                        statusName.toLowerCase() === 'suspendido' ? 'bg-danger' : 'bg-secondary';
                statusElement.innerHTML = `<span class="badge ${statusBadgeClass} text-white">${statusName}</span>`;
            }
            
            this.setElementText('driver-rating', data.driverProfile.averageRating || '0');
            this.setElementText('driver-rating-count', data.driverProfile.ratingCount || '0');
            if (driverProfile) {
                driverProfile.style.display = 'block';
            }
            hasProfiles = true;
        } else {
            if (driverProfile) {
                driverProfile.style.display = 'none';
            }
        }
        
        if (hasProfiles && profilesSection) {
            profilesSection.style.display = 'block';
        }
    }

    /**
     * Llena la información del vehículo
     */
    populateVehicleInfo(vehicle) {
        const vehicleSection = document.getElementById('vehicle-info-section');
        
        this.setElementText('detail-vehicle-plate', vehicle?.plate || '-');
        this.setElementText('detail-vehicle-brand', vehicle?.brand?.name || '-');
        this.setElementText('detail-vehicle-model', vehicle?.model?.name || '-');
        this.setElementText('detail-vehicle-year', vehicle?.year || '-');
        this.setElementText('detail-vehicle-color', vehicle?.color?.name || vehicle?.color?.id || '-');
        
        if (vehicleSection) {
            vehicleSection.style.display = 'block';
        }
    }

    /**
     * Formatea una fecha y hora
     */
    formatDateTime(dateTimeString) {
        if (!dateTimeString) return '-';
        try {
            const date = new Date(dateTimeString);
            return date.toLocaleString('es-PE', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        } catch (error) {
            return dateTimeString;
        }
    }

    /**
     * Muestra un error en el modal de detalles
     */
    showUserDetailsError(message) {
        if (this.errorDiv) {
            const errorText = document.getElementById('user-details-error-text');
            if (errorText) {
                errorText.textContent = message;
            }
            this.errorDiv.style.display = 'block';
        }
        
        this.hideAllDetailsSections();
    }

    /**
     * Cierra el modal
     */
    closeModal() {
        if (this.modal) {
            // Usar Bootstrap modal
            const bootstrapModal = bootstrap.Modal.getInstance(this.modal);
            if (bootstrapModal) {
                bootstrapModal.hide();
            }
        }
        
        this.isModalOpen = false;
        this.currentUserId = null;
        this.currentUserData = null;
        
        console.log('👁️ Modal de detalles cerrado');
    }

    /**
     * Función helper para establecer texto en un elemento
     */
    setElementText(elementId, text) {
        const element = document.getElementById(elementId);
        if (element) {
            element.textContent = text;
        }
    }

    /**
     * Muestra un mensaje toast
     */
    showToast(message, type = 'info') {
        console.log(`🔔 UserDetailsController.showToast: ${type} - ${message}`);
        
        // Utilizar el sistema global de toasts si está disponible
        if (window.GlobalToast) {
            window.GlobalToast.show(message, type);
        } else {
            console.log(`Toast ${type}: ${message}`);
        }
    }

    /**
     * Refresca los detalles del usuario actual
     */
    async refresh() {
        if (this.currentUserId) {
            await this.viewUser(this.currentUserId);
        }
    }

    /**
     * Obtiene los datos actuales del usuario
     */
    getCurrentUserData() {
        return this.currentUserData;
    }

    /**
     * Verifica si el modal está abierto
     */
    isOpen() {
        return this.isModalOpen;
    }

    /**
     * Obtiene la clase CSS para el badge del rol
     */
    getRoleBadgeClass(roleName) {
        switch (roleName?.toUpperCase()) {
            case 'ADMINISTRADOR': return 'text-white'; // Dorado (se aplica style inline)
            case 'CONDUCTOR': return 'bg-warning text-dark'; // Amarillo
            case 'CIUDADANO': return 'bg-info text-white'; // Azul celeste
            default: return 'bg-secondary text-white';
        }
    }

    /**
     * Obtiene el estilo inline para el badge del rol
     */
    getRoleBadgeStyle(roleName) {
        switch (roleName?.toUpperCase()) {
            case 'ADMINISTRADOR': return 'background-color: #d4af37;'; // Dorado
            default: return '';
        }
    }
}

// Instancia global para acceso desde otros controladores
window.UserDetailsController = null;
