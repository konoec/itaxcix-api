/**
 * Controlador para editar estados de usuario
 * Maneja el modal de edición y la actualización de estados
 * 
 * @author Sistema
 * @version 1.0.0
 */

class UpdateUserStatusController {
    constructor(onUpdateCallback = null) {
        console.log('🎯 UpdateUserStatusController inicializado');
        this.currentUserStatusId = null;
        this.modal = null;
        this.form = null;
        this.isUpdating = false;
        this.onUpdateCallback = onUpdateCallback;
        
        // Vincular métodos
        this.handleSubmit = this.handleSubmit.bind(this);
        this.showEditModal = this.showEditModal.bind(this);
        
        // Inicializar automáticamente
        this.init();
    }

    /**
     * Inicializa el controlador
     */
    init() {
        console.log('🔧 Inicializando UpdateUserStatusController...');
        
        // Verificar si el DOM está listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.createModal();
                this.setupEventListeners();
            });
        } else {
            this.createModal();
            this.setupEventListeners();
        }
        
        console.log('✅ UpdateUserStatusController inicializado correctamente');
    }

    /**
     * Crea el modal de edición dinámicamente
     */
    createModal() {
        const modalContainer = document.getElementById('modal-container');
        if (!modalContainer) {
            console.error('❌ No se encontró el contenedor de modales');
            // Intentar nuevamente en un momento
            setTimeout(() => this.createModal(), 500);
            return;
        }

        const modalHTML = this.createModalHTML();
        modalContainer.insertAdjacentHTML('beforeend', modalHTML);

        // Obtener referencias
        try {
            this.modal = new bootstrap.Modal(document.getElementById('editUserStatusModal'));
            this.form = document.getElementById('editUserStatusForm');
            
            if (!this.form) {
                throw new Error('No se pudo obtener referencia al formulario');
            }
            
            console.log('✅ Modal de edición creado correctamente');
        } catch (error) {
            console.error('❌ Error al crear modal:', error);
        }
    }

    /**
     * Genera el HTML del modal de edición
     * @returns {string} HTML del modal
     */
    createModalHTML() {
        return `
            <!-- Modal Editar Estado de Usuario -->
            <div class="modal modal-blur fade" id="editUserStatusModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content shadow-lg border-0">
                        <div class="modal-header bg-blue text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-edit me-2"></i>
                                Editar Estado de Usuario
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="editUserStatusForm" autocomplete="off">
                            <div class="modal-body">
                                <!-- Campo Nombre -->
                                <div class="mb-3">
                                    <label for="editUserStatusName" class="form-label required">
                                        <i class="fas fa-user-tag me-1 text-primary"></i>
                                        Nombre del Estado
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary-lt">
                                            <i class="fas fa-font text-primary"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control" 
                                               id="editUserStatusName" 
                                               name="name" 
                                               placeholder="Ej: Suspendido, Verificado..."
                                               maxlength="50" 
                                               required>
                                        <div class="invalid-feedback" id="edit-name-error"></div>
                                    </div>
                                    <small class="form-hint text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Nombre único para el estado de usuario (máximo 50 caracteres)
                                    </small>
                                </div>

                                <!-- Campo Estado Activo -->
                                <div class="mb-3">
                                    <label class="form-label">
                                        Estado del Registro
                                    </label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="editUserStatusActive" 
                                               name="active">
                                        <label class="form-check-label d-flex align-items-center" for="editUserStatusActive">
                                            <i id="editUserStatusActiveIcon" class="fas fa-check-circle text-success me-2"></i>
                                            <span id="editUserStatusActiveText">Activo</span>
                                        </label>
                                    </div>
                                    <small class="form-hint text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Los estados inactivos no estarán disponibles para asignación
                                    </small>
                                </div>
                            </div>
                            <div class="modal-footer bg-light border-0">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i>
                                    Cancelar
                                </button>
                                <button type="submit" class="btn btn-blue" id="editUserStatusSubmitBtn">
                                    <i class="fas fa-save me-1"></i>
                                    Actualizar Estado
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Configura los event listeners
     */
    setupEventListeners() {
        if (!this.form) return;

        // Submit del formulario
        this.form.addEventListener('submit', this.handleSubmit);

        // Actualización en tiempo real del estado activo
        const activeInput = document.getElementById('editUserStatusActive');

        if (activeInput) {
            activeInput.addEventListener('change', () => {
                this.updateActiveToggle();
            });
        }

        // Limpiar formulario al cerrar modal
        const modalElement = document.getElementById('editUserStatusModal');
        if (modalElement) {
            modalElement.addEventListener('hidden.bs.modal', () => {
                this.resetForm();
            });
        }
    }

    /**
     * Muestra el modal de edición con los datos del estado
     * @param {number} userStatusId - ID del estado de usuario
     * @param {Object} userStatusData - Datos del estado de usuario
     */
    async showEditModal(userStatusId, userStatusData = null) {
        console.log('📝 Abriendo modal de edición para estado ID:', userStatusId);

        try {
            this.currentUserStatusId = userStatusId;

            // Si no se proporcionan datos, mostrar error (ya no obtenemos del servidor)
            if (!userStatusData) {
                throw new Error('No se proporcionaron datos del estado de usuario');
            }

            // Llenar formulario
            this.populateForm(userStatusData);

            // Actualizar toggle del estado activo
            this.updateActiveToggle();

            // Mostrar modal
            this.modal.show();

        } catch (error) {
            console.error('❌ Error al abrir modal de edición:', error);
            window.GlobalToast?.show('Error al cargar datos del estado de usuario', 'error');
        }
    }

    /**
     * Llena el formulario con los datos del estado de usuario
     * @param {Object} userStatusData - Datos del estado
     */
    populateForm(userStatusData) {
        const nameInput = document.getElementById('editUserStatusName');
        const activeInput = document.getElementById('editUserStatusActive');

        if (nameInput) {
            nameInput.value = userStatusData.name || '';
        }

        if (activeInput) {
            activeInput.checked = Boolean(userStatusData.active);
        }

        // Limpiar errores previos
        this.clearValidationErrors();
    }

    /**
     * Actualiza el toggle visual del estado activo
     */
    updateActiveToggle() {
        const activeInput = document.getElementById('editUserStatusActive');
        const icon = document.getElementById('editUserStatusActiveIcon');
        const text = document.getElementById('editUserStatusActiveText');

        if (activeInput && icon && text) {
            if (activeInput.checked) {
                icon.className = 'fas fa-check-circle text-success me-2';
                text.textContent = 'Activo';
            } else {
                icon.className = 'fas fa-times-circle text-danger me-2';
                text.textContent = 'Inactivo';
            }
        }
    }

    /**
     * Maneja el envío del formulario
     * @param {Event} event - Evento de submit
     */
    async handleSubmit(event) {
        event.preventDefault();

        if (this.isUpdating) {
            console.log('⏳ Actualización en progreso, ignorando...');
            return;
        }

        console.log('📤 Enviando formulario de edición...');

        try {
            this.isUpdating = true;
            this.setSubmitButton(true);

            // Obtener datos del formulario
            const formData = new FormData(this.form);
            const userStatusData = {
                name: formData.get('name').trim(),
                active: formData.has('active')
            };

            console.log('📋 Datos a enviar:', userStatusData);

            // Validar datos
            this.validateForm(userStatusData);

            // Enviar actualización
            const response = await window.updateUserStatusService.updateUserStatus(
                this.currentUserStatusId, 
                userStatusData
            );

            console.log('✅ Estado de usuario actualizado exitosamente:', response);

            // Mostrar mensaje de éxito
            window.GlobalToast?.show('Estado de usuario actualizado exitosamente', 'success');

            // Cerrar modal
            this.modal.hide();

            // Llamar callback si existe
            if (this.onUpdateCallback && typeof this.onUpdateCallback === 'function') {
                this.onUpdateCallback(response.data);
            }

            // Fallback: recargar lista si existe el controlador
            if (window.userStatusListController && typeof window.userStatusListController.loadUserStatuses === 'function') {
                await window.userStatusListController.loadUserStatuses();
            }

        } catch (error) {
            console.error('❌ Error al actualizar estado de usuario:', error);
            this.handleSubmitError(error);
        } finally {
            this.isUpdating = false;
            this.setSubmitButton(false);
        }
    }

    /**
     * Valida los datos del formulario
     * @param {Object} userStatusData - Datos a validar
     */
    validateForm(userStatusData) {
        this.clearValidationErrors();

        if (!userStatusData.name || userStatusData.name.length === 0) {
            this.setFieldError('editUserStatusName', 'El nombre del estado es requerido');
            throw new Error('Campos requeridos incompletos');
        }

        if (userStatusData.name.length > 50) {
            this.setFieldError('editUserStatusName', 'El nombre no puede exceder 50 caracteres');
            throw new Error('Datos inválidos');
        }
    }

    /**
     * Maneja errores del envío del formulario
     * @param {Error} error - Error ocurrido
     */
    handleSubmitError(error) {
        if (error.message.includes('nombre') && error.message.includes('existe')) {
            this.setFieldError('editUserStatusName', 'Ya existe un estado con este nombre');
        } else {
            window.GlobalToast?.show(error.message || 'Error al actualizar estado de usuario', 'error');
        }
    }

    /**
     * Establece un error en un campo específico
     * @param {string} fieldId - ID del campo
     * @param {string} message - Mensaje de error
     */
    setFieldError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const errorDiv = document.getElementById(`${fieldId.replace('editUserStatus', 'edit-').toLowerCase()}-error`);

        if (field) {
            field.classList.add('is-invalid');
        }

        if (errorDiv) {
            errorDiv.textContent = message;
        }
    }

    /**
     * Limpia todos los errores de validación
     */
    clearValidationErrors() {
        if (!this.form) {
            console.warn('⚠️ Formulario no disponible para limpiar errores de validación');
            return;
        }
        
        const invalidFields = this.form.querySelectorAll('.is-invalid');
        invalidFields.forEach(field => field.classList.remove('is-invalid'));

        const errorDivs = this.form.querySelectorAll('.invalid-feedback');
        errorDivs.forEach(div => div.textContent = '');
    }

    /**
     * Controla el estado del botón de envío
     * @param {boolean} loading - Si está cargando
     */
    setSubmitButton(loading) {
        const submitBtn = document.getElementById('editUserStatusSubmitBtn');
        if (!submitBtn) return;

        if (loading) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Actualizando...';
        } else {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>Actualizar Estado';
        }
    }

    /**
     * Resetea el formulario
     */
    resetForm() {
        if (this.form) {
            this.form.reset();
            this.clearValidationErrors();
            this.currentUserStatusId = null;
        }
    }
}

// Hacer disponible globalmente
window.UpdateUserStatusController = UpdateUserStatusController;

console.log('✅ UpdateUserStatusController cargado correctamente');
