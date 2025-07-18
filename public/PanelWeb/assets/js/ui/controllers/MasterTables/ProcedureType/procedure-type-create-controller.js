/**
 * Controlador para el modal de creación de tipos de trámite
 * Maneja la apertura del modal, validación y envío del formulario
 */
class ProcedureTypeCreateController {
    constructor() {
        this.isSubmitting = false;
        this.bindEvents();
    }

    /**
     * Abre el modal de creación de tipo de trámite
     */
    openCreateModal() {
        const modalHtml = this.generateModalHtml();
        const modalContainer = document.getElementById('modal-container');
        if (modalContainer) {
            modalContainer.innerHTML = modalHtml;
            const modal = new bootstrap.Modal(document.getElementById('createProcedureTypeModal'));
            modal.show();
            this.setupModalEvents();
            setTimeout(() => {
                const nameInput = document.getElementById('procedureTypeName');
                if (nameInput) nameInput.focus();
            }, 300);
        }
    }

    /**
     * Genera el HTML del modal
     */
    generateModalHtml() {
        return `
            <div class="modal modal-blur fade" id="createProcedureTypeModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-plus-circle text-primary me-2"></i>
                                Crear Tipo de Trámite
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="createProcedureTypeForm">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label required">
                                                <i class="fas fa-tag text-muted me-1"></i>
                                                Nombre del Tipo de Trámite
                                            </label>
                                            <input type="text"
                                                   class="form-control"
                                                   id="procedureTypeName"
                                                   name="name"
                                                   placeholder="Ej: Licencia, Permiso, Autorización..."
                                                   maxlength="100"
                                                   required>
                                            <div class="form-hint">Máximo 100 caracteres</div>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox" id="procedureTypeActive" name="active" checked>
                                                <label class="form-check-label" for="procedureTypeActive">Activo</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary me-auto" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i>
                                    Cancelar
                                </button>
                                <button type="submit" class="btn btn-primary" id="submitProcedureTypeBtn">
                                    <i class="fas fa-save me-1"></i>
                                    Crear Tipo
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Configura los eventos del modal
     */
    setupModalEvents() {
        const form = document.getElementById('createProcedureTypeForm');
        if (form) {
            form.addEventListener('submit', (e) => this.handleSubmit(e));
        }
        const nameInput = document.getElementById('procedureTypeName');
        if (nameInput) {
            nameInput.addEventListener('input', () => this.validateField(nameInput));
            nameInput.addEventListener('blur', () => this.validateField(nameInput));
        }
    }

    /**
     * Maneja el envío del formulario
     */
    async handleSubmit(event) {
        event.preventDefault();
        if (this.isSubmitting) return;
        const formData = new FormData(event.target);
        const data = {
            name: formData.get('name').trim(),
            active: document.getElementById('procedureTypeActive').checked
        };
        const validation = window.procedureTypeCreateService.validateProcedureTypeData(data);
        if (!validation.isValid) {
            this.showValidationErrors(validation.errors);
            return;
        }
        try {
            this.setSubmittingState(true);
            const result = await window.procedureTypeCreateService.createProcedureType(data);
            this.showSuccessToast(result.message || 'Tipo de procedimiento creado exitosamente');
            const modal = bootstrap.Modal.getInstance(document.getElementById('createProcedureTypeModal'));
            if (modal) modal.hide();
            if (window.procedureTypeListControllerInstance && typeof window.procedureTypeListControllerInstance.load === 'function') {
                window.procedureTypeListControllerInstance.load();
            }
        } catch (error) {
            this.showErrorToast(error.message || 'Error al crear tipo de trámite');
        } finally {
            this.setSubmittingState(false);
        }
    }

    /**
     * Valida un campo individual
     */
    validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let message = '';
        if (field.id === 'procedureTypeName') {
            if (value.length === 0) {
                isValid = false;
                message = 'El nombre es requerido';
            } else if (value.length > 100) {
                isValid = false;
                message = 'El nombre no puede exceder 100 caracteres';
            }
        }
        const feedback = field.nextElementSibling?.nextElementSibling;
        if (isValid) {
            field.classList.remove('is-invalid');
            if (feedback) feedback.textContent = '';
        } else {
            field.classList.add('is-invalid');
            if (feedback) feedback.textContent = message;
        }
        return isValid;
    }

    /**
     * Muestra errores de validación
     */
    showValidationErrors(errors) {
        const nameInput = document.getElementById('procedureTypeName');
        errors.forEach(error => {
            if (error.includes('nombre')) {
                nameInput.classList.add('is-invalid');
                const feedback = nameInput.nextElementSibling?.nextElementSibling;
                if (feedback) feedback.textContent = error;
            }
        });
    }

    /**
     * Controla el estado de envío del formulario
     */
    setSubmittingState(isSubmitting) {
        this.isSubmitting = isSubmitting;
        const submitBtn = document.getElementById('submitProcedureTypeBtn');
        const form = document.getElementById('createProcedureTypeForm');
        if (isSubmitting) {
            submitBtn.disabled = true;
            form.classList.add('was-validated');
        } else {
            submitBtn.disabled = false;
            form.classList.remove('was-validated');
        }
    }

    /**
     * Muestra toast de éxito
     */
    showSuccessToast(message) {
        if (window.GlobalToast) {
            window.GlobalToast.show(message, 'success');
        }
    }

    /**
     * Muestra toast de error
     */
    showErrorToast(message) {
        if (window.GlobalToast) {
            window.GlobalToast.show(message, 'error');
        }
    }

    /**
     * Configura eventos globales
     */
    bindEvents() {
        document.addEventListener('click', (e) => {
            const target = e.target.closest('#createProcedureTypeBtn');
            if (target) {
                e.preventDefault();
                this.openCreateModal();
            }
        });
    }
}

// Inicializar controlador
window.procedureTypeCreateController = new ProcedureTypeCreateController();
