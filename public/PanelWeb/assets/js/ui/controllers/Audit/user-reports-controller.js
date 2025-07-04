/**
 * Controlador para reportes de usuarios
 * Extiende BaseReportsController para manejar reportes de usuarios específicamente
 */
class UserReportsController extends BaseReportsController {
    constructor() {
        super();
        console.log('👥 Inicializando UserReportsController...');
        
        // Filtros específicos para usuarios - solo valores por defecto
        this.filters = {
            sortBy: 'name',
            sortDirection: 'ASC'
        };
        
        // Servicio de datos
        this.service = window.userReportsService;
        
        // Elementos específicos del DOM
        this.initializeUserElements();
        
        // Event listeners específicos
        this.initializeUserEventListeners();
    }
    
    /**
     * Inicializa los elementos específicos para usuarios
     */
    initializeUserElements() {
        // Sección de filtros específica
        this.filtersSection = document.getElementById('users-filters');
        this.headers = document.getElementById('users-headers');
        
        // Filtros específicos
        this.filterName = document.getElementById('filter-user-name');
        this.filterLastName = document.getElementById('filter-user-lastname');
        this.filterDocument = document.getElementById('filter-user-document');
        this.filterDocumentTypeId = document.getElementById('filter-user-document-type-id');
        this.filterStatusId = document.getElementById('filter-user-status-id');
        this.filterEmail = document.getElementById('filter-user-email');
        this.filterPhone = document.getElementById('filter-user-phone');
        this.filterActive = document.getElementById('filter-user-active');
        this.filterValidationStartDate = document.getElementById('filter-user-validation-start-date');
        this.filterValidationEndDate = document.getElementById('filter-user-validation-end-date');
        this.userSortBy = document.getElementById('user-sort-by');
        this.userSortDirection = document.getElementById('user-sort-direction');
        
        // Botones de acción específicos
        this.applyFiltersBtn = document.getElementById('apply-filters-btn-users');
        this.clearFiltersBtn = document.getElementById('clear-filters-btn-users');
        this.refreshDataBtn = document.getElementById('refresh-data-btn-users');
        
        console.log('✅ Elementos específicos de usuarios inicializados');
    }
    
    /**
     * Inicializa los event listeners específicos para usuarios
     */
    initializeUserEventListeners() {
        // Botones de acción
        if (this.applyFiltersBtn) {
            this.applyFiltersBtn.addEventListener('click', () => this.applyFilters());
        }
        
        if (this.clearFiltersBtn) {
            this.clearFiltersBtn.addEventListener('click', () => this.clearFilters());
        }
        
        if (this.refreshDataBtn) {
            this.refreshDataBtn.addEventListener('click', () => this.refreshData());
        }
        
        // Enter en campos de texto principales
        [this.filterName, this.filterLastName, this.filterDocument, this.filterEmail, this.filterPhone].forEach(input => {
            if (input) {
                input.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        this.applyFilters();
                    }
                });
            }
        });
        
        // Validación en tiempo real para email
        if (this.filterEmail) {
            this.filterEmail.addEventListener('input', (e) => {
                const value = e.target.value.trim();
                const feedback = this.getEmailFeedback(value);
                this.showEmailFeedback(feedback);
            });
        }
        
        // Validación en tiempo real para documento
        if (this.filterDocument) {
            this.filterDocument.addEventListener('input', (e) => {
                const value = e.target.value.trim();
                const feedback = this.getDocumentFeedback(value);
                this.showDocumentFeedback(feedback);
            });
        }
        
        // Validación de fechas
        if (this.filterValidationStartDate) {
            this.filterValidationStartDate.addEventListener('change', () => this.validateDateRange());
        }
        
        if (this.filterValidationEndDate) {
            this.filterValidationEndDate.addEventListener('change', () => this.validateDateRange());
        }
        
        console.log('✅ Event listeners específicos de usuarios inicializados');
    }
    
    /**
     * Carga los reportes de usuarios
     */
    async loadReports() {
        if (!this.service) {
            console.error('❌ Servicio de usuarios no disponible');
            this.showErrorState('Servicio de usuarios no disponible');
            return;
        }

        try {
            this.showLoadingState();
            
            const requestFilters = {
                page: this.currentPage,
                perPage: this.perPage,
                ...this.filters
            };
            
            console.log('🔄 Cargando reportes de usuarios con filtros:', requestFilters);
            
            const response = await this.service.getUserReports(requestFilters);
            console.log('🔍 Respuesta completa de la API:', response);
            
            if (response && response.success && response.data && response.data.data) {
                // La API devuelve data.data como array de usuarios
                this.currentData = response.data.data || [];
                
                // Extraer información de paginación
                const pagination = response.data.pagination;
                if (pagination) {
                    this.totalResults = pagination.total_items || 0;
                    this.totalPages = pagination.total_pages || 1;
                    this.currentPage = pagination.current_page || 1;
                    this.perPage = pagination.per_page || 20;
                } else {
                    this.totalResults = this.currentData.length;
                    this.totalPages = 1;
                    this.currentPage = 1;
                    this.perPage = 20;
                }
                
                console.log('📊 Datos procesados:', {
                    usuarios: this.currentData.length,
                    totalResultados: this.totalResults,
                    paginaActual: this.currentPage,
                    totalPaginas: this.totalPages
                });
                
                this.renderTable();
                this.updatePagination();
                this.showTableState();
                
                console.log('✅ Reportes de usuarios cargados exitosamente');
            } else {
                console.error('❌ Estructura de respuesta inválida:', response);
                throw new Error('Respuesta inválida del servidor');
            }
            
        } catch (error) {
            console.error('❌ Error al cargar reportes de usuarios:', error);
            this.showErrorState(error.message || 'Error al cargar los reportes de usuarios');
        }
    }
    
    /**
     * Aplica los filtros actuales
     */
    applyFilters() {
        // Validar filtros antes de aplicarlos
        const validation = this.validateFilters();
        if (!validation.isValid) {
            this.showValidationErrors(validation.errors);
            return;
        }
        
        this.updateFiltersFromForm();
        this.currentPage = 1;
        this.loadReports();
    }
    
    /**
     * Limpia todos los filtros
     */
    clearFilters() {
        // Limpiar formulario
        if (this.filterName) this.filterName.value = '';
        if (this.filterLastName) this.filterLastName.value = '';
        if (this.filterDocument) this.filterDocument.value = '';
        if (this.filterDocumentTypeId) this.filterDocumentTypeId.value = '';
        if (this.filterStatusId) this.filterStatusId.value = '';
        if (this.filterEmail) this.filterEmail.value = '';
        if (this.filterPhone) this.filterPhone.value = '';
        if (this.filterActive) this.filterActive.value = '';
        if (this.filterValidationStartDate) this.filterValidationStartDate.value = '';
        if (this.filterValidationEndDate) this.filterValidationEndDate.value = '';
        if (this.userSortBy) this.userSortBy.value = 'name';
        if (this.userSortDirection) this.userSortDirection.value = 'ASC';
        if (this.pageSizeSelect) this.pageSizeSelect.value = '20';
        
        // Resetear filtros - solo mantener valores por defecto
        this.filters = {
            sortBy: 'name',
            sortDirection: 'ASC'
        };
        
        this.perPage = 20;
        this.currentPage = 1;
        
        // Limpiar feedback visual
        this.clearValidationFeedback();
        
        this.loadReports();
    }
    
    /**
     * Refresca los datos
     */
    refreshData() {
        this.loadReports();
    }
    
    /**
     * Actualiza los filtros desde el formulario
     */
    updateFiltersFromForm() {
        // Crear objeto de filtros solo con valores válidos
        const formFilters = {};
        
        // Filtros de texto - validar que no esté vacío y que tenga longitud mínima
        const name = this.filterName?.value?.trim();
        if (name && name.length >= 2) {
            formFilters.name = name;
        } else if (name && name.length > 0) {
            console.warn('⚠️ Nombre debe tener al menos 2 caracteres:', name);
        }
        
        const lastName = this.filterLastName?.value?.trim();
        if (lastName && lastName.length >= 2) {
            formFilters.lastName = lastName;
        } else if (lastName && lastName.length > 0) {
            console.warn('⚠️ Apellido debe tener al menos 2 caracteres:', lastName);
        }
        
        const document = this.filterDocument?.value?.trim();
        if (document && document.length >= 6 && document.length <= 20) {
            // Validar que sea alfanumérico
            if (/^[A-Za-z0-9-]+$/.test(document)) {
                formFilters.document = document;
            } else {
                console.warn('⚠️ Documento debe contener solo caracteres alfanuméricos y guiones:', document);
            }
        } else if (document && document.length > 0) {
            console.warn('⚠️ Documento debe tener entre 6 y 20 caracteres:', document);
        }
        
        const email = this.filterEmail?.value?.trim();
        if (email && this.isValidEmail(email)) {
            formFilters.email = email.toLowerCase();
        } else if (email && email.length > 0) {
            console.warn('⚠️ Email no tiene formato válido:', email);
        }
        
        const phone = this.filterPhone?.value?.trim();
        if (phone && phone.length >= 7 && phone.length <= 15) {
            // Validar que contenga solo números, espacios, guiones y paréntesis
            if (/^[\d\s\-\(\)\+]+$/.test(phone)) {
                formFilters.phone = phone;
            } else {
                console.warn('⚠️ Teléfono contiene caracteres inválidos:', phone);
            }
        } else if (phone && phone.length > 0) {
            console.warn('⚠️ Teléfono debe tener entre 7 y 15 caracteres:', phone);
        }
        
        // Filtros numéricos (IDs) - validar que sean números válidos
        const documentTypeId = this.filterDocumentTypeId?.value;
        if (documentTypeId && documentTypeId !== '' && !isNaN(documentTypeId)) {
            formFilters.documentTypeId = parseInt(documentTypeId);
        }
        
        const statusId = this.filterStatusId?.value;
        if (statusId && statusId !== '' && !isNaN(statusId)) {
            formFilters.statusId = parseInt(statusId);
        }
        
        // Filtro boolean para active - asegurar formato correcto
        const active = this.filterActive?.value;
        if (active && active !== '') {
            if (active === 'true' || active === '1' || active === 'active') {
                formFilters.active = true;
            } else if (active === 'false' || active === '0' || active === 'inactive') {
                formFilters.active = false;
            }
        }
        
        // Filtros de fecha - validar formato ISO
        const validationStartDate = this.filterValidationStartDate?.value;
        if (validationStartDate && this.isValidDate(validationStartDate)) {
            formFilters.validationStartDate = validationStartDate;
        }
        
        const validationEndDate = this.filterValidationEndDate?.value;
        if (validationEndDate && this.isValidDate(validationEndDate)) {
            formFilters.validationEndDate = validationEndDate;
        }
        
        // Ordenamiento - validar valores permitidos
        const sortBy = this.userSortBy?.value;
        const allowedSortFields = ['name', 'lastName', 'document', 'email', 'phone', 'validationDate'];
        if (sortBy && allowedSortFields.includes(sortBy)) {
            formFilters.sortBy = sortBy;
        } else {
            formFilters.sortBy = 'name'; // Valor por defecto
        }
        
        const sortDirection = this.userSortDirection?.value;
        if (sortDirection && (sortDirection === 'ASC' || sortDirection === 'DESC')) {
            formFilters.sortDirection = sortDirection;
        } else {
            formFilters.sortDirection = 'ASC'; // Valor por defecto
        }
        
        // Paginación - validar rangos
        const perPageValue = parseInt(this.pageSizeSelect?.value || '20');
        if (perPageValue >= 1 && perPageValue <= 100) {
            this.perPage = perPageValue;
        } else {
            this.perPage = 20; // Valor por defecto
        }
        
        this.filters = formFilters;
        
        console.log('📝 Filtros de usuarios actualizados:', this.filters);
        console.log('📄 Paginación configurada:', { page: this.currentPage, perPage: this.perPage });
    }
    
    /**
     * Renderiza la tabla con los datos actuales
     */
    renderTable() {
        if (!this.incidentsTableBody) {
            console.error('❌ Cuerpo de tabla no encontrado');
            return;
        }

        console.log('🎨 Renderizando tabla de usuarios con', this.currentData.length, 'registros');

        // Limpiar tabla
        this.incidentsTableBody.innerHTML = '';

        if (this.currentData.length === 0) {
            this.showEmptyState();
            return;
        }

        // Generar filas
        this.currentData.forEach(user => {
            const row = this.createTableRow(user);
            this.incidentsTableBody.appendChild(row);
        });

        console.log('✅ Tabla de usuarios renderizada');
    }

    /**
     * Crea una fila de tabla para un usuario
     */
    createTableRow(user) {
        const row = document.createElement('tr');
        row.className = 'data-row';

        const statusBadge = user.active 
            ? '<span class="status-badge active">Activo</span>'
            : '<span class="status-badge inactive">Inactivo</span>';

        // Formatear fecha de validación
        const validationDate = user.validationDate 
            ? new Date(user.validationDate).toLocaleDateString('es-ES', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit'
            })
            : 'N/A';

        row.innerHTML = `
            <td>${user.id || ''}</td>
            <td><strong>${user.name || ''}</strong></td>
            <td>${user.lastName || ''}</td>
            <td>${user.document || ''}</td>
            <td>${user.documentTypeName || 'N/A'}</td>
            <td>${statusBadge}</td>
            <td>${user.email || ''}</td>
            <td>${user.phone || 'N/A'}</td>
            <td>${user.statusName || 'N/A'}</td>
            <td>${validationDate}</td>
        `;

        return row;
    }
    
    /**
     * Verifica si hay filtros activos
     */
    hasActiveFilters() {
        return Object.keys(this.filters).some(key => {
            // Excluir los parámetros de ordenamiento por defecto
            if (key === 'sortBy' && this.filters[key] === 'name') return false;
            if (key === 'sortDirection' && this.filters[key] === 'ASC') return false;
            
            // Verificar si hay algún filtro con valor
            const value = this.filters[key];
            return value !== undefined && value !== null && value !== '';
        });
    }
    
    /**
     * Muestra los filtros y headers específicos para usuarios
     */
    showFiltersAndHeaders() {
        if (this.filtersSection) {
            this.filtersSection.style.display = 'block';
        }
        
        if (this.headers) {
            this.headers.style.display = 'table-row';
        }
    }
    
    /**
     * Oculta los filtros y headers específicos para usuarios
     */
    hideFiltersAndHeaders() {
        if (this.filtersSection) {
            this.filtersSection.style.display = 'none';
        }
        
        if (this.headers) {
            this.headers.style.display = 'none';
        }
    }
    
    /**
     * Valida los filtros antes de aplicarlos
     * @returns {Object} { isValid: boolean, errors: string[] }
     */
    validateFilters() {
        const errors = [];
        
        // Validar nombre
        const name = this.filterName?.value?.trim();
        if (name && name.length < 2) {
            errors.push('El nombre debe tener al menos 2 caracteres');
        }
        
        // Validar apellido
        const lastName = this.filterLastName?.value?.trim();
        if (lastName && lastName.length < 2) {
            errors.push('El apellido debe tener al menos 2 caracteres');
        }
        
        // Validar documento
        const document = this.filterDocument?.value?.trim();
        if (document) {
            if (document.length < 6 || document.length > 20) {
                errors.push('El documento debe tener entre 6 y 20 caracteres');
            } else if (!/^[A-Za-z0-9-]+$/.test(document)) {
                errors.push('El documento debe contener solo caracteres alfanuméricos y guiones');
            }
        }
        
        // Validar email
        const email = this.filterEmail?.value?.trim();
        if (email && !this.isValidEmail(email)) {
            errors.push('El email no tiene un formato válido');
        }
        
        // Validar teléfono
        const phone = this.filterPhone?.value?.trim();
        if (phone) {
            if (phone.length < 7 || phone.length > 15) {
                errors.push('El teléfono debe tener entre 7 y 15 caracteres');
            } else if (!/^[\d\s\-\(\)\+]+$/.test(phone)) {
                errors.push('El teléfono contiene caracteres inválidos');
            }
        }
        
        // Validar rango de fechas
        const startDate = this.filterValidationStartDate?.value;
        const endDate = this.filterValidationEndDate?.value;
        
        if (startDate && !this.isValidDate(startDate)) {
            errors.push('La fecha de inicio no es válida');
        }
        
        if (endDate && !this.isValidDate(endDate)) {
            errors.push('La fecha de fin no es válida');
        }
        
        if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
            errors.push('La fecha de inicio no puede ser posterior a la fecha de fin');
        }
        
        return {
            isValid: errors.length === 0,
            errors: errors
        };
    }

    /**
     * Muestra mensajes de error de validación
     * @param {string[]} errors 
     */
    showValidationErrors(errors) {
        // Crear o encontrar el contenedor de errores
        let errorContainer = document.getElementById('validation-errors-users');
        if (!errorContainer) {
            errorContainer = document.createElement('div');
            errorContainer.id = 'validation-errors-users';
            errorContainer.className = 'alert alert-danger mt-2';
            errorContainer.style.display = 'none';
            
            // Insertar después del contenedor de filtros
            if (this.filtersSection) {
                this.filtersSection.parentNode.insertBefore(errorContainer, this.filtersSection.nextSibling);
            }
        }
        
        if (errors.length > 0) {
            errorContainer.innerHTML = `
                <strong>Errores de validación:</strong>
                <ul class="mb-0 mt-1">
                    ${errors.map(error => `<li>${error}</li>`).join('')}
                </ul>
            `;
            errorContainer.style.display = 'block';
            
            // Auto-ocultar después de 5 segundos
            setTimeout(() => {
                errorContainer.style.display = 'none';
            }, 5000);
        } else {
            errorContainer.style.display = 'none';
        }
    }
    
    /**
     * Obtiene feedback sobre la validez de un email
     * @param {string} value 
     * @returns {Object}
     */
    getEmailFeedback(value) {
        if (!value) {
            return { isValid: true, message: '', type: 'info' };
        }
        
        if (!this.isValidEmail(value)) {
            return { 
                isValid: false, 
                message: 'Formato de email inválido', 
                type: 'error' 
            };
        }
        
        return { 
            isValid: true, 
            message: 'Email válido', 
            type: 'success' 
        };
    }

    /**
     * Muestra feedback visual para el email
     * @param {Object} feedback 
     */
    showEmailFeedback(feedback) {
        if (!this.filterEmail) return;
        
        // Crear o encontrar el elemento de feedback
        let feedbackElement = document.getElementById('email-feedback');
        if (!feedbackElement) {
            feedbackElement = document.createElement('small');
            feedbackElement.id = 'email-feedback';
            feedbackElement.className = 'form-text';
            this.filterEmail.parentNode.appendChild(feedbackElement);
        }
        
        // Limpiar clases previas
        this.filterEmail.classList.remove('is-valid', 'is-invalid');
        feedbackElement.classList.remove('text-success', 'text-warning', 'text-danger');
        
        if (feedback.message) {
            feedbackElement.textContent = feedback.message;
            feedbackElement.style.display = 'block';
            
            switch (feedback.type) {
                case 'success':
                    this.filterEmail.classList.add('is-valid');
                    feedbackElement.classList.add('text-success');
                    break;
                case 'warning':
                    feedbackElement.classList.add('text-warning');
                    break;
                case 'error':
                    this.filterEmail.classList.add('is-invalid');
                    feedbackElement.classList.add('text-danger');
                    break;
            }
        } else {
            feedbackElement.style.display = 'none';
        }
    }
    
    /**
     * Obtiene feedback sobre la validez de un documento
     * @param {string} value 
     * @returns {Object}
     */
    getDocumentFeedback(value) {
        if (!value) {
            return { isValid: true, message: '', type: 'info' };
        }
        
        if (value.length < 6) {
            return { 
                isValid: false, 
                message: `Documento muy corto (${value.length}/6 caracteres mínimo)`, 
                type: 'warning' 
            };
        }
        
        if (value.length > 20) {
            return { 
                isValid: false, 
                message: `Documento muy largo (${value.length}/20 caracteres máximo)`, 
                type: 'error' 
            };
        }
        
        if (!/^[A-Za-z0-9-]+$/.test(value)) {
            return { 
                isValid: false, 
                message: 'Solo se permiten caracteres alfanuméricos y guiones', 
                type: 'error' 
            };
        }
        
        return { 
            isValid: true, 
            message: `Documento válido (${value.length} caracteres)`, 
            type: 'success' 
        };
    }

    /**
     * Muestra feedback visual para el documento
     * @param {Object} feedback 
     */
    showDocumentFeedback(feedback) {
        if (!this.filterDocument) return;
        
        // Crear o encontrar el elemento de feedback
        let feedbackElement = document.getElementById('document-feedback');
        if (!feedbackElement) {
            feedbackElement = document.createElement('small');
            feedbackElement.id = 'document-feedback';
            feedbackElement.className = 'form-text';
            this.filterDocument.parentNode.appendChild(feedbackElement);
        }
        
        // Limpiar clases previas
        this.filterDocument.classList.remove('is-valid', 'is-invalid');
        feedbackElement.classList.remove('text-success', 'text-warning', 'text-danger');
        
        if (feedback.message) {
            feedbackElement.textContent = feedback.message;
            feedbackElement.style.display = 'block';
            
            switch (feedback.type) {
                case 'success':
                    this.filterDocument.classList.add('is-valid');
                    feedbackElement.classList.add('text-success');
                    break;
                case 'warning':
                    feedbackElement.classList.add('text-warning');
                    break;
                case 'error':
                    this.filterDocument.classList.add('is-invalid');
                    feedbackElement.classList.add('text-danger');
                    break;
            }
        } else {
            feedbackElement.style.display = 'none';
        }
    }

    /**
     * Valida el rango de fechas
     */
    validateDateRange() {
        const startDate = this.filterValidationStartDate?.value;
        const endDate = this.filterValidationEndDate?.value;
        
        let startFeedback = document.getElementById('start-date-feedback');
        let endFeedback = document.getElementById('end-date-feedback');
        
        if (!startFeedback && this.filterValidationStartDate) {
            startFeedback = document.createElement('small');
            startFeedback.id = 'start-date-feedback';
            startFeedback.className = 'form-text';
            this.filterValidationStartDate.parentNode.appendChild(startFeedback);
        }
        
        if (!endFeedback && this.filterValidationEndDate) {
            endFeedback = document.createElement('small');
            endFeedback.id = 'end-date-feedback';
            endFeedback.className = 'form-text';
            this.filterValidationEndDate.parentNode.appendChild(endFeedback);
        }
        
        // Limpiar estados previos
        [this.filterValidationStartDate, this.filterValidationEndDate].forEach(input => {
            if (input) {
                input.classList.remove('is-valid', 'is-invalid');
            }
        });
        
        [startFeedback, endFeedback].forEach(feedback => {
            if (feedback) {
                feedback.style.display = 'none';
                feedback.classList.remove('text-success', 'text-warning', 'text-danger');
            }
        });
        
        if (startDate && endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            
            if (start > end) {
                // Error: fecha inicio posterior a fecha fin
                if (this.filterValidationStartDate) {
                    this.filterValidationStartDate.classList.add('is-invalid');
                }
                if (this.filterValidationEndDate) {
                    this.filterValidationEndDate.classList.add('is-invalid');
                }
                
                if (startFeedback) {
                    startFeedback.textContent = 'La fecha de inicio debe ser anterior a la fecha de fin';
                    startFeedback.classList.add('text-danger');
                    startFeedback.style.display = 'block';
                }
            } else {
                // Válido
                if (this.filterValidationStartDate) {
                    this.filterValidationStartDate.classList.add('is-valid');
                }
                if (this.filterValidationEndDate) {
                    this.filterValidationEndDate.classList.add('is-valid');
                }
                
                if (endFeedback) {
                    const diffDays = Math.ceil((end - start) / (1000 * 60 * 60 * 24));
                    endFeedback.textContent = `Rango válido: ${diffDays} días`;
                    endFeedback.classList.add('text-success');
                    endFeedback.style.display = 'block';
                }
            }
        }
    }

    /**
     * Limpia todo el feedback de validación
     */
    clearValidationFeedback() {
        // Limpiar feedback de email
        const emailFeedback = document.getElementById('email-feedback');
        if (emailFeedback) {
            emailFeedback.style.display = 'none';
        }
        if (this.filterEmail) {
            this.filterEmail.classList.remove('is-valid', 'is-invalid');
        }
        
        // Limpiar feedback de documento
        const documentFeedback = document.getElementById('document-feedback');
        if (documentFeedback) {
            documentFeedback.style.display = 'none';
        }
        if (this.filterDocument) {
            this.filterDocument.classList.remove('is-valid', 'is-invalid');
        }
        
        // Limpiar feedback de fechas
        const startFeedback = document.getElementById('start-date-feedback');
        const endFeedback = document.getElementById('end-date-feedback');
        
        [startFeedback, endFeedback].forEach(feedback => {
            if (feedback) {
                feedback.style.display = 'none';
            }
        });
        
        [this.filterValidationStartDate, this.filterValidationEndDate].forEach(input => {
            if (input) {
                input.classList.remove('is-valid', 'is-invalid');
            }
        });
        
        // Limpiar errores de validación
        const errorContainer = document.getElementById('validation-errors-users');
        if (errorContainer) {
            errorContainer.style.display = 'none';
        }
    }
    
    /**
     * Valida formato de email
     * @param {string} email 
     * @returns {boolean}
     */
    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    /**
     * Valida formato de fecha
     * @param {string} dateString 
     * @returns {boolean}
     */
    isValidDate(dateString) {
        const date = new Date(dateString);
        return date instanceof Date && !isNaN(date);
    }
}

// Hacer el controlador disponible globalmente
window.UserReportsController = UserReportsController;
