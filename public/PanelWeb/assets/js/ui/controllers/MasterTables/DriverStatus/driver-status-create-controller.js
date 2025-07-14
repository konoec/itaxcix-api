/**
 * Controlador para el modal de creación de estados de conductor
 * Maneja la apertura del modal, validación y envío del formulario
 */
class DriverStatusCreateController {
    constructor() {
        this.isSubmitting = false;
        this.bindEvents();
    }

    /**
     * Abre el modal de creación de estado de conductor
     */
    openCreateModal() {
        const modalHtml = this.generateModalHtml();
        
        // Insertar modal en el contenedor
        const modalContainer = document.getElementById('modal-container');
        if (modalContainer) {
            modalContainer.innerHTML = modalHtml;
            
            // Mostrar modal usando Tabler
            const modal = new bootstrap.Modal(document.getElementById('createDriverStatusModal'));
            modal.show();
            
            // Configurar eventos del modal
            this.setupModalEvents();
            
            // Focus en el primer campo
            setTimeout(() => {
                const nameInput = document.getElementById('driverStatusName');
                if (nameInput) nameInput.focus();
            }, 300);
        }
    }

    /**
     * Genera el HTML del modal
     */
    generateModalHtml() {
        return `
            <div class="modal modal-blur fade" id="createDriverStatusModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-plus-circle text-primary me-2"></i>
                                Crear Estado de Conductor
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="createDriverStatusForm">
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label class="form-label required">
                                                <i class="fas fa-tag text-muted me-1"></i>
                                                Nombre del Estado
                                            </label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="driverStatusName" 
                                                   name="name"
                                                   placeholder="Ej: Disponible, Ocupado, Descanso..."
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
                                            <label class="form-label">
                                                <i class="fas fa-toggle-on text-muted me-1"></i>
                                                Estado
                                            </label>
                                            <div class="form-selectgroup form-selectgroup-boxes d-flex flex-column">
                                                <label class="form-selectgroup-item flex-fill">
                                                    <input type="radio" name="active" value="true" class="form-selectgroup-input" checked>
                                                    <div class="form-selectgroup-label d-flex align-items-center p-3">
                                                        <div class="me-3">
                                                            <span class="form-selectgroup-check"></span>
                                                        </div>
                                                        <div>
                                                            <div class="font-weight-medium">
                                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                                Activo
                                                            </div>
                                                            <div class="text-muted">El estado estará disponible para asignar a conductores</div>
                                                        </div>
                                                    </div>
                                                </label>
                                                <label class="form-selectgroup-item flex-fill">
                                                    <input type="radio" name="active" value="false" class="form-selectgroup-input">
                                                    <div class="form-selectgroup-label d-flex align-items-center p-3">
                                                        <div class="me-3">
                                                            <span class="form-selectgroup-check"></span>
                                                        </div>
                                                        <div>
                                                            <div class="font-weight-medium">
                                                                <i class="fas fa-times-circle text-danger me-2"></i>
                                                                Inactivo
                                                            </div>
                                                            <div class="text-muted">El estado no estará disponible para nuevas asignaciones</div>
                                                        </div>
                                                    </div>
                                                </label>
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
                                <button type="submit" class="btn btn-primary" id="submitDriverStatusBtn">
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
        const form = document.getElementById('createDriverStatusForm');
        if (form) {
            form.addEventListener('submit', (e) => this.handleSubmit(e));
        }

        // Validación en tiempo real
        const nameInput = document.getElementById('driverStatusName');
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
            active: formData.get('active') === 'true'
        };

        // Validar datos
        const validation = window.driverStatusCreateService.validateDriverStatusData(data);
        if (!validation.isValid) {
            this.showValidationErrors(validation.errors);
            return;
        }

        try {
            this.setSubmittingState(true);
            
            const response = await window.driverStatusCreateService.createDriverStatus(data);
            
            if (response.success) {
                // Mostrar mensaje de éxito
                window.globalToast.show(
                    response.message || 'Estado de conductor creado correctamente',
                    'success'
                );

                // Cerrar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('createDriverStatusModal'));
                if (modal) modal.hide();

                // Recargar lista si existe el controlador
                if (window.driverStatusListController && 
                    typeof window.driverStatusListController.loadDriverStatuses === 'function') {
                    await window.driverStatusListController.loadDriverStatuses();
                }
            }
        } catch (error) {
            console.error('Error al crear estado de conductor:', error);
            window.globalToast.show(
                error.message || 'Error al crear el estado de conductor',
                'error'
            );
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

        if (field.id === 'driverStatusName') {
            if (!value) {
                isValid = false;
                message = 'El nombre del estado es requerido';
            } else if (value.length > 100) {
                isValid = false;
                message = 'El nombre no puede exceder 100 caracteres';
            }
        }

        // Aplicar estilos de validación
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
            window.globalToast.show(error, 'error');
        });
    }

    /**
     * Controla el estado de envío del formulario
     */
    setSubmittingState(isSubmitting) {
        this.isSubmitting = isSubmitting;
        const submitBtn = document.getElementById('submitDriverStatusBtn');
        const form = document.getElementById('createDriverStatusForm');

        if (isSubmitting) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creando...';
            form.style.pointerEvents = 'none';
        } else {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>Crear Estado';
            form.style.pointerEvents = 'auto';
        }
    }

    /**
     * Configura eventos globales
     */
    bindEvents() {
        // Evento para abrir modal desde botón +
        document.addEventListener('click', (e) => {
            if (e.target.closest('[data-action="create-driver-status"]')) {
                e.preventDefault();
                this.openCreateModal();
            }
        });
    }
}

// Inicializar controlador
window.driverStatusCreateController = new DriverStatusCreateController();
