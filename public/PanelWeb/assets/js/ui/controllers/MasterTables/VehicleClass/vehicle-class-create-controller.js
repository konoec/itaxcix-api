/**
 * Controlador para el modal de creación de clase de vehículo
 * Maneja la apertura del modal, validación y envío del formulario
 */
class VehicleClassCreateController {
    constructor() {
        this.isSubmitting = false;
        this.bindEvents();
    }

    openCreateModal() {
        const modalHtml = this.generateModalHtml();
        const modalContainer = document.getElementById('modal-container');
        if (modalContainer) {
            modalContainer.innerHTML = modalHtml;
            const modal = new bootstrap.Modal(document.getElementById('createVehicleClassModal'));
            modal.show();
            this.setupModalEvents();
            setTimeout(() => {
                const nameInput = document.getElementById('vehicleClassName');
                if (nameInput) nameInput.focus();
            }, 300);
        }
    }

    generateModalHtml() {
        return `
            <div class="modal modal-blur fade" id="createVehicleClassModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-plus-circle me-2"></i>
                                Crear Clase de Vehículo
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="createVehicleClassForm">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label required">
                                        <i class="fas fa-tag text-muted me-1"></i>
                                        Nombre de la Clase
                                    </label>
                                    <input type="text" class="form-control" id="vehicleClassName" name="name" placeholder="Ej: SUV, Sedán, Hatchback..." maxlength="100" required>
                                    <div class="form-hint">Máximo 100 caracteres</div>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="vehicleClassActive" name="active" checked>
                                        <label class="form-check-label" for="vehicleClassActive">Activo</label>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary me-auto" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i>
                                    Cancelar
                                </button>
                                <button type="submit" class="btn btn-primary" id="submitVehicleClassBtn">
                                    <i class="fas fa-save me-1"></i>
                                    Crear Clase
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;
    }

    setupModalEvents() {
        const form = document.getElementById('createVehicleClassForm');
        if (form) {
            form.addEventListener('submit', (e) => this.handleSubmit(e));
        }
        const nameInput = document.getElementById('vehicleClassName');
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
            active: document.getElementById('vehicleClassActive').checked
        };
        const validation = window.VehicleClassService.validateVehicleClassData(data);
        if (!validation.isValid) {
            this.showValidationErrors(validation.errors);
            return;
        }
        try {
            this.setSubmittingState(true);
            const response = await window.VehicleClassService.createVehicleClass(data);
            if (response.success) {
                if (window.GlobalToast) {
                    window.GlobalToast.show(
                        response.message || 'Clase de vehículo creada correctamente',
                        'success'
                    );
                }
                const modal = bootstrap.Modal.getInstance(document.getElementById('createVehicleClassModal'));
                if (modal) modal.hide();
                if (window.vehicleClassListController && typeof window.vehicleClassListController.loadVehicleClasses === 'function') {
                    await window.vehicleClassListController.loadVehicleClasses();
                }
            }
        } catch (error) {
            console.error('Error al crear clase de vehículo:', error);
            if (window.GlobalToast) {
                window.GlobalToast.show(
                    error.message || 'Error al crear la clase de vehículo',
                    'error'
                );
            }
        } finally {
            this.setSubmittingState(false);
        }
    }

    validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let message = '';
        if (field.id === 'vehicleClassName') {
            if (!value) {
                isValid = false;
                message = 'El nombre de la clase es requerido';
            } else if (value.length > 100) {
                isValid = false;
                message = 'El nombre no puede exceder 100 caracteres';
            }
        }
        if (isValid) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
            const feedback = field.parentNode.querySelector('.invalid-feedback');
            if (feedback) feedback.textContent = message;
        }
        return isValid;
    }

    showValidationErrors(errors) {
        errors.forEach(error => {
            if (window.GlobalToast) {
                window.GlobalToast.show(error, 'error');
            }
        });
    }

    setSubmittingState(isSubmitting) {
        this.isSubmitting = isSubmitting;
        const submitBtn = document.getElementById('submitVehicleClassBtn');
        const form = document.getElementById('createVehicleClassForm');
        if (isSubmitting) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creando...';
            form.style.pointerEvents = 'none';
        } else {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>Crear Clase';
            form.style.pointerEvents = 'auto';
        }
    }

    bindEvents() {
        document.addEventListener('click', (e) => {
            if (e.target.closest('[data-action="create-vehicle-class"]')) {
                e.preventDefault();
                this.openCreateModal();
            }
        });
    }
}
window.vehicleClassCreateController = new VehicleClassCreateController();
