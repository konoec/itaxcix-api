/**
 * Controlador para el modal de creación de severidad de infracción
 * Maneja la apertura del modal, validación y envío del formulario
 */
class InfractionSeverityCreateController {
    constructor() {
        this.isSubmitting = false;
        this.bindEvents();
    }

    openCreateModal() {
        const modalHtml = this.generateModalHtml();
        const modalContainer = document.getElementById('modal-container');
        if (modalContainer) {
            modalContainer.innerHTML = modalHtml;
            const modal = new bootstrap.Modal(document.getElementById('createInfractionSeverityModal'));
            modal.show();
            this.setupModalEvents();
            setTimeout(() => {
                const nameInput = document.getElementById('infractionSeverityName');
                if (nameInput) nameInput.focus();
            }, 300);
        }
    }

    generateModalHtml() {
        return `
            <div class="modal modal-blur fade" id="createInfractionSeverityModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-plus-circle text-primary me-2"></i>
                                Crear Severidad de Infracción
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="createInfractionSeverityForm">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label required">
                                                <i class="fas fa-tag text-muted me-1"></i>
                                                Nombre de la Severidad
                                            </label>
                                            <input type="text"
                                                   class="form-control"
                                                   id="infractionSeverityName"
                                                   name="name"
                                                   placeholder="Ej: Leve, Grave, Muy Grave..."
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
                                                <input class="form-check-input" type="checkbox" id="infractionSeverityActive" name="active" checked>
                                                <label class="form-check-label" for="infractionSeverityActive">Activo</label>
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
                                <button type="submit" class="btn btn-primary" id="submitInfractionSeverityBtn">
                                    <i class="fas fa-save me-1"></i>
                                    Crear Severidad
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;
    }

    setupModalEvents() {
        const form = document.getElementById('createInfractionSeverityForm');
        if (form) {
            form.addEventListener('submit', (e) => this.handleSubmit(e));
        }
        const nameInput = document.getElementById('infractionSeverityName');
        if (nameInput) {
            nameInput.addEventListener('input', () => this.validateField(nameInput));
            nameInput.addEventListener('blur', () => this.validateField(nameInput));
        }
    }

    async handleSubmit(event) {
        event.preventDefault();
        if (this.isSubmitting) return;
        const formData = new FormData(event.target);
        const data = {
            name: formData.get('name').trim(),
            active: document.getElementById('infractionSeverityActive').checked
        };
        const validation = window.infractionSeverityCreateService.validateInfractionSeverityData(data);
        if (!validation.isValid) {
            this.showValidationErrors(validation.errors);
            return;
        }
        try {
            this.setSubmittingState(true);
            const result = await window.infractionSeverityCreateService.createInfractionSeverity(data);
            if (window.GlobalToast) {
                window.GlobalToast.show(result.message || 'Gravedad de infracción creada exitosamente', 'success');
            }
            const modal = bootstrap.Modal.getInstance(document.getElementById('createInfractionSeverityModal'));
            if (modal) modal.hide();
            if (window.infractionSeverityController && typeof window.infractionSeverityController.refresh === 'function') {
                window.infractionSeverityController.refresh();
            }
        } catch (error) {
            if (window.GlobalToast) {
                window.GlobalToast.show(error.message || 'Error al crear gravedad de infracción', 'error');
            }
        } finally {
            this.setSubmittingState(false);
        }
    }

    validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let message = '';
        if (field.id === 'infractionSeverityName') {
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

    showValidationErrors(errors) {
        const nameInput = document.getElementById('infractionSeverityName');
        errors.forEach(error => {
            if (error.includes('nombre')) {
                nameInput.classList.add('is-invalid');
                const feedback = nameInput.nextElementSibling?.nextElementSibling;
                if (feedback) feedback.textContent = error;
            }
        });
    }

    setSubmittingState(isSubmitting) {
        this.isSubmitting = isSubmitting;
        const submitBtn = document.getElementById('submitInfractionSeverityBtn');
        const form = document.getElementById('createInfractionSeverityForm');
        if (isSubmitting) {
            submitBtn.disabled = true;
            form.classList.add('was-validated');
        } else {
            submitBtn.disabled = false;
            form.classList.remove('was-validated');
        }
    }

    bindEvents() {
        document.addEventListener('click', (e) => {
            const target = e.target.closest('#createInfractionSeverityBtn');
            if (target) {
                e.preventDefault();
                this.openCreateModal();
            }
        });
    }
}
window.infractionSeverityCreateController = new InfractionSeverityCreateController();
