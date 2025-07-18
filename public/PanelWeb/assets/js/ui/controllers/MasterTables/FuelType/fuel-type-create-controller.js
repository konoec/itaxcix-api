/**
 * Controlador para el modal de creación de tipos de combustible
 * Maneja la apertura del modal, validación y envío del formulario
 */
class FuelTypeCreateController {
    constructor() {
        this.isSubmitting = false;
        if (!window.__fuelTypeCreateControllerListenerAdded) {
            this.bindEvents();
            window.__fuelTypeCreateControllerListenerAdded = true;
        }
    }

    /**
     * Abre el modal de creación de tipo de combustible
     */
    openCreateModal() {
        const modalHtml = this.generateModalHtml();
        const modalContainer = document.getElementById('modal-container');
        if (modalContainer) {
            modalContainer.innerHTML = modalHtml;
            const modal = new bootstrap.Modal(document.getElementById('createFuelTypeModal'));
            modal.show();
            this.setupModalEvents();
            setTimeout(() => {
                const nameInput = document.getElementById('fuelTypeName');
                if (nameInput) nameInput.focus();
            }, 300);
        }
    }

    /**
     * Genera el HTML del modal
     */
    generateModalHtml() {
        return `
            <div class="modal modal-blur fade" id="createFuelTypeModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-plus-circle text-primary me-2"></i>
                                Crear Tipo de Combustible
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="createFuelTypeForm">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label required">
                                                <i class="fas fa-tag text-muted me-1"></i>
                                                Nombre del Tipo de Combustible
                                            </label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="fuelTypeName" 
                                                   name="name"
                                                   placeholder="Ej: Gasolina 98, Diesel, GLP..."
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
                                                <input class="form-check-input" type="checkbox" id="fuelTypeActive" name="active" checked>
                                                <label class="form-check-label" for="fuelTypeActive">Activo</label>
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
                                <button type="submit" class="btn btn-primary" id="submitFuelTypeBtn">
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
        const form = document.getElementById('createFuelTypeForm');
        if (form) {
            form.addEventListener('submit', (e) => this.handleSubmit(e));
        }
        const nameInput = document.getElementById('fuelTypeName');
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
            active: document.getElementById('fuelTypeActive').checked
        };
        if (!window.fuelTypeCreateService || typeof window.fuelTypeCreateService.validateFuelTypeData !== 'function') {
            if (window.GlobalToast) {
                window.GlobalToast.show('Error interno: El servicio de creación de tipo de combustible no está disponible.', 'error');
            }
            return;
        }
        const validation = window.fuelTypeCreateService.validateFuelTypeData(data);
        if (!validation.isValid) {
            this.showValidationErrors(validation.errors);
            return;
        }
        try {
            this.setSubmittingState(true);
            const response = await window.fuelTypeCreateService.createFuelType(data);
            if (response.success) {
                if (window.GlobalToast) {
                    window.GlobalToast.show(
                        response.message || 'Tipo de combustible creado exitosamente',
                        'success'
                    );
                }
                const modal = bootstrap.Modal.getInstance(document.getElementById('createFuelTypeModal'));
                if (modal) modal.hide();
                if (window.fuelTypeListController && typeof window.fuelTypeListController.loadFuelTypes === 'function') {
                    await window.fuelTypeListController.loadFuelTypes();
                }
            }
        } catch (error) {
            console.error('Error al crear tipo de combustible:', error);
            if (window.GlobalToast) {
                window.GlobalToast.show(
                    error.message || 'Error al crear el tipo de combustible',
                    'error'
                );
            }
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
        if (field.id === 'fuelTypeName') {
            if (!value) {
                isValid = false;
                message = 'El nombre del tipo de combustible es requerido';
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

    /**
     * Muestra errores de validación
     */
    showValidationErrors(errors) {
        errors.forEach(error => {
            if (window.GlobalToast) {
                window.GlobalToast.show(error, 'error');
            }
        });
    }

    /**
     * Controla el estado de envío del formulario
     */
    setSubmittingState(isSubmitting) {
        this.isSubmitting = isSubmitting;
        const submitBtn = document.getElementById('submitFuelTypeBtn');
        const form = document.getElementById('createFuelTypeForm');
        if (isSubmitting) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creando...';
            form.style.pointerEvents = 'none';
        } else {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>Crear Tipo';
            form.style.pointerEvents = 'auto';
        }
    }

    /**
     * Configura eventos globales
     */
    bindEvents() {
        document.addEventListener('click', (e) => {
            if (e.target.closest('[data-action="create-fuel-type"]')) {
                e.preventDefault();
                const modalEl = document.getElementById('createFuelTypeModal');
                if (!modalEl || !modalEl.classList.contains('show')) {
                    this.openCreateModal();
                }
            }
        });
    }
}

// Inicializar controlador
window.fuelTypeCreateController = new FuelTypeCreateController();
