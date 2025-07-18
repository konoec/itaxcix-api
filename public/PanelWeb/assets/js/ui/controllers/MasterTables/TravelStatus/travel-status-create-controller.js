/**
 * Controlador para el modal de creación de estados de viaje
 * Maneja la apertura del modal, validación y envío del formulario
 */
class TravelStatusCreateController {
    constructor() {
        this.isSubmitting = false;
        this.bindEvents();
    }

    /**
     * Abre el modal de creación de estado de viaje
     */
    openCreateModal() {
        const modalHtml = this.generateModalHtml();
        const modalContainer = document.getElementById('modal-container');
        if (modalContainer) {
            modalContainer.innerHTML = modalHtml;
            const modal = new bootstrap.Modal(document.getElementById('createTravelStatusModal'));
            modal.show();
            this.setupModalEvents();
            setTimeout(() => {
                const nameInput = document.getElementById('travelStatusName');
                if (nameInput) nameInput.focus();
            }, 300);
        }
    }

    /**
     * Genera el HTML del modal
     */
    generateModalHtml() {
        return `
            <div class="modal modal-blur fade" id="createTravelStatusModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-plus-circle text-primary me-2"></i>
                                Crear Estado de Viaje
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="createTravelStatusForm">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label required">
                                                <i class="fas fa-tag text-muted me-1"></i>
                                                Nombre del Estado de Viaje
                                            </label>
                                            <input type="text"
                                                   class="form-control"
                                                   id="travelStatusName"
                                                   name="name"
                                                   placeholder="Ej: En curso, Finalizado, Cancelado..."
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
                                                <input class="form-check-input" type="checkbox" id="travelStatusActive" name="active" checked>
                                                <label class="form-check-label" for="travelStatusActive">Activo</label>
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
                                <button type="submit" class="btn btn-primary" id="submitTravelStatusBtn">
                                    <i class="fas fa-save me-1"></i>
                                    Crear Estado
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
        const form = document.getElementById('createTravelStatusForm');
        if (form) {
            form.addEventListener('submit', (e) => this.handleSubmit(e));
        }
        const nameInput = document.getElementById('travelStatusName');
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
            active: document.getElementById('travelStatusActive').checked
        };
        const validation = window.travelStatusCreateService.validateTravelStatusData(data);
        if (!validation.isValid) {
            this.showValidationErrors(validation.errors);
            return;
        }
        try {
            this.setSubmittingState(true);
            const result = await window.travelStatusCreateService.createTravelStatus(data);
            this.showSuccessToast(result.message || 'Estado de viaje creado exitosamente');
            const modal = bootstrap.Modal.getInstance(document.getElementById('createTravelStatusModal'));
            if (modal) modal.hide();
            if (window.travelStatusListControllerInstance && typeof window.travelStatusListControllerInstance.load === 'function') {
                window.travelStatusListControllerInstance.load();
            }
        } catch (error) {
            this.showErrorToast(error.message || 'Error al crear estado de viaje');
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
        if (field.id === 'travelStatusName') {
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
        const nameInput = document.getElementById('travelStatusName');
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
        const submitBtn = document.getElementById('submitTravelStatusBtn');
        const form = document.getElementById('createTravelStatusForm');
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
            const target = e.target.closest('#createTravelStatusBtn');
            if (target) {
                e.preventDefault();
                this.openCreateModal();
            }
        });
    }
}

// Inicializar controlador
window.travelStatusCreateController = new TravelStatusCreateController();
