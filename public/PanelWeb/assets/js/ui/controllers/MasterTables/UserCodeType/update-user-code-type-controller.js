/**
 * Controlador para editar tipos de código de usuario
 * Maneja el modal de edición y la actualización de tipos
 * 
 * @author Sistema
 * @version 1.0.0
 */

class UpdateUserCodeTypeController {
    constructor(onUpdateCallback = null) {
        console.log('🎯 UpdateUserCodeTypeController inicializado');
        this.currentUserCodeTypeId = null;
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
        console.log('🔧 Inicializando UpdateUserCodeTypeController...');
        
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
        
        console.log('✅ UpdateUserCodeTypeController inicializado correctamente');
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
            this.modal = new bootstrap.Modal(document.getElementById('editUserCodeTypeModal'));
            this.form = document.getElementById('editUserCodeTypeForm');
            
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
            <!-- Modal Editar Tipo de Código de Usuario -->
            <div class="modal modal-blur fade" id="editUserCodeTypeModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content shadow-lg border-0">
                        <div class="modal-header bg-blue text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-edit me-2"></i>
                                Editar Tipo de Código de Usuario
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="editUserCodeTypeForm" autocomplete="off">
                            <div class="modal-body">
                                <!-- Campo Nombre -->
                                <div class="mb-3">
                                    <label for="editUserCodeTypeName" class="form-label required">
                                        <i class="fas fa-barcode me-1 text-primary"></i>
                                        Nombre del Tipo
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary-lt">
                                            <i class="fas fa-font text-primary"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control" 
                                               id="editUserCodeTypeName" 
                                               name="name" 
                                               placeholder="Ej: Código A, Código B..."
                                               maxlength="50" 
                                               required>
                                        <div class="invalid-feedback" id="edit-name-error"></div>
                                    </div>
                                    <small class="form-hint text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Nombre único para el tipo de código (máximo 50 caracteres)
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
                                               id="editUserCodeTypeActive" 
                                               name="active">
                                        <label class="form-check-label d-flex align-items-center" for="editUserCodeTypeActive">
                                            <i id="editUserCodeTypeActiveIcon" class="fas fa-check-circle text-success me-2"></i>
                                            <span id="editUserCodeTypeActiveText">Activo</span>
                                        </label>
                                    </div>
                                    <small class="form-hint text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Los tipos inactivos no estarán disponibles para asignación
                                    </small>
                                </div>
                            </div>
                            <div class="modal-footer bg-light border-0">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i>
                                    Cancelar
                                </button>
                                <button type="submit" class="btn btn-blue" id="editUserCodeTypeSubmitBtn">
                                    <i class="fas fa-save me-1"></i>
                                    Actualizar Tipo
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
        const activeInput = document.getElementById('editUserCodeTypeActive');

        if (activeInput) {
            activeInput.addEventListener('change', () => {
                this.updateActiveToggle();
            });
        }

        // Limpiar formulario al cerrar modal
        const modalElement = document.getElementById('editUserCodeTypeModal');
        if (modalElement) {
            modalElement.addEventListener('hidden.bs.modal', () => {
                this.resetForm();
            });
        }
    }

    /**
     * Muestra el modal de edición con los datos del tipo
     * @param {number} userCodeTypeId - ID del tipo de código de usuario
     * @param {Object} userCodeTypeData - Datos del tipo de código de usuario
     */
    async showEditModal(userCodeTypeId, userCodeTypeData = null) {
        console.log('📝 Abriendo modal de edición para tipo ID:', userCodeTypeId);

        try {
            // Convertir ID a número para asegurar el tipo correcto
            this.currentUserCodeTypeId = parseInt(userCodeTypeId, 10);

            // Si no se proporcionan datos, mostrar error (ya no obtenemos del servidor)
            if (!userCodeTypeData) {
                throw new Error('No se proporcionaron datos del tipo de código de usuario');
            }

            // Llenar formulario
            this.populateForm(userCodeTypeData);

            // Actualizar toggle del estado activo
            this.updateActiveToggle();

            // Mostrar modal
            this.modal.show();

        } catch (error) {
            console.error('❌ Error al abrir modal de edición:', error);
            window.GlobalToast?.show('Error al cargar datos del tipo de código de usuario', 'error');
        }
    }

    /**
     * Llena el formulario con los datos del tipo de código de usuario
     * @param {Object} userCodeTypeData - Datos del tipo
     */
    populateForm(userCodeTypeData) {
        const nameInput = document.getElementById('editUserCodeTypeName');
        const activeInput = document.getElementById('editUserCodeTypeActive');

        if (nameInput) {
            nameInput.value = userCodeTypeData.name || '';
        }

        if (activeInput) {
            activeInput.checked = userCodeTypeData.active === true;
        }

        // Limpiar errores previos
        this.clearValidationErrors();

        console.log('📝 Formulario poblado con datos:', userCodeTypeData);
    }

    /**
     * Actualiza el toggle visual del estado activo
     */
    updateActiveToggle() {
        const activeInput = document.getElementById('editUserCodeTypeActive');
        const icon = document.getElementById('editUserCodeTypeActiveIcon');
        const text = document.getElementById('editUserCodeTypeActiveText');

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
            console.warn('⚠️ Ya hay una actualización en proceso');
            return;
        }

        try {
            this.isUpdating = true;
            this.setSubmitButton(true);

            // Obtener datos del formulario
            const formData = new FormData(this.form);
            const userCodeTypeData = {
                name: formData.get('name')?.trim(),
                active: formData.has('active') // checkbox estará presente solo si está marcado
            };

            console.log('📤 Enviando datos de actualización:', userCodeTypeData);

            // Validar datos localmente
            if (!this.validateForm(userCodeTypeData)) {
                return;
            }

            // Llamar al servicio de actualización
            const response = await window.UpdateUserCodeTypeService.updateUserCodeType(
                this.currentUserCodeTypeId,
                userCodeTypeData
            );

            console.log('✅ Tipo de código de usuario actualizado exitosamente:', response);

            // Mostrar mensaje de éxito
            window.GlobalToast?.show('Tipo de código de usuario actualizado exitosamente', 'success');

            // Cerrar modal
            this.modal.hide();

            // Llamar callback si existe
            if (this.onUpdateCallback && typeof this.onUpdateCallback === 'function') {
                this.onUpdateCallback(response.data);
            }

            // Fallback: recargar lista si existe el controlador
            if (window.userCodeTypeListController && typeof window.userCodeTypeListController.load === 'function') {
                await window.userCodeTypeListController.load();
            }

        } catch (error) {
            console.error('❌ Error al actualizar tipo de código de usuario:', error);
            this.handleSubmitError(error);
        } finally {
            this.isUpdating = false;
            this.setSubmitButton(false);
        }
    }

    /**
     * Valida los datos del formulario
     * @param {Object} userCodeTypeData - Datos a validar
     */
    validateForm(userCodeTypeData) {
        let isValid = true;

        // Limpiar errores previos
        this.clearValidationErrors();

        // Validar nombre
        if (!userCodeTypeData.name || userCodeTypeData.name.length === 0) {
            this.setFieldError('editUserCodeTypeName', 'El nombre es requerido');
            isValid = false;
        } else if (userCodeTypeData.name.length > 50) {
            this.setFieldError('editUserCodeTypeName', 'El nombre no puede exceder 50 caracteres');
            isValid = false;
        }

        return isValid;
    }

    /**
     * Maneja errores del envío del formulario
     * @param {Error} error - Error ocurrido
     */
    handleSubmitError(error) {
        if (error.message.includes('nombre') && error.message.includes('existe')) {
            this.setFieldError('editUserCodeTypeName', 'Ya existe un tipo con este nombre');
        } else {
            window.GlobalToast?.show(error.message || 'Error al actualizar tipo de código de usuario', 'error');
        }
    }

    /**
     * Establece un error en un campo específico
     * @param {string} fieldId - ID del campo
     * @param {string} message - Mensaje de error
     */
    setFieldError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const errorDiv = document.getElementById('edit-name-error');

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
     * @param {boolean} loading - Si está en estado de carga
     */
    setSubmitButton(loading) {
        const submitBtn = document.getElementById('editUserCodeTypeSubmitBtn');
        
        if (submitBtn) {
            if (loading) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                    Actualizando...
                `;
            } else {
                submitBtn.disabled = false;
                submitBtn.innerHTML = `
                    <i class="fas fa-save me-1"></i>
                    Actualizar Tipo
                `;
            }
        }
    }

    /**
     * Resetea el formulario
     */
    resetForm() {
        if (this.form) {
            this.form.reset();
            this.clearValidationErrors();
            this.currentUserCodeTypeId = null;
        }
    }
}

// Hacer disponible globalmente
window.UpdateUserCodeTypeController = UpdateUserCodeTypeController;

console.log('✅ UpdateUserCodeTypeController cargado correctamente');
