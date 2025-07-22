/**
 * Controlador para el modal de edición de estado TUC
 * Maneja la apertura del modal, carga de datos, validación y envío del formulario
 */
class TucStatusEditController {
    constructor() {
        this.isSubmitting = false;
        this.currentTucStatusId = null;
    }

    /**
     * Abre el modal de edición de estado TUC
     * @param {number} id - ID del estado TUC a editar
     * @param {object} tucStatusData - Datos del estado TUC obtenidos de la lista
     */
    async openEditModal(id, tucStatusData = null) {
        this.currentTucStatusId = id;
        const modalContainer = document.getElementById('modal-container');
        if (!modalContainer) return;
        modalContainer.innerHTML = this.generateModalHtml();
        this.populateFormWithData(tucStatusData);
        this.setupModalEvents();
        const modal = new bootstrap.Modal(document.getElementById('editTucStatusModal'));
        modal.show();
    }

    /**
     * Llena el formulario con los datos proporcionados
     * @param {object} tucStatus - Datos del estado TUC
     */
    populateFormWithData(tucStatus) {
        if (!tucStatus) return;
        document.getElementById('editTucStatusName').value = tucStatus.name || '';
        document.getElementById('editTucStatusActive').checked = !!tucStatus.active;
    }

    /**
     * Genera el HTML del modal
     */
    generateModalHtml() {
        return `
            <div class="modal modal-blur fade" id="editTucStatusModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-edit text-primary me-2"></i>
                                Editar Estado TUC
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="editModalForm">
                                <form id="editTucStatusForm">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label required">
                                                    <i class="fas fa-tag text-muted me-1"></i>
                                                    Nombre del Estado
                                                </label>
                                                <input type="text" class="form-control" id="editTucStatusName" name="name" placeholder="Ej: Activo, Inactivo..." maxlength="100" required>
                                                <div class="form-hint">Máximo 100 caracteres</div>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="editTucStatusActive" name="active">
                                                    <label class="form-check-label" for="editTucStatusActive">
                                                        <strong>Estado Activo</strong>
                                                    </label>
                                                    <div class="form-hint text-muted">
                                                        Los estados inactivos no estarán disponibles para asignación
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="modal-footer" id="editModalFooter">
                            <button type="button" class="btn btn-outline-secondary me-auto" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary" id="updateTucStatusBtn" form="editTucStatusForm">
                                <i class="fas fa-save me-1"></i>
                                Actualizar Estado
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Configura los eventos del modal
     */
    setupModalEvents() {
        const form = document.getElementById('editTucStatusForm');
        if (form) {
            form.addEventListener('submit', this.handleSubmit.bind(this));
        }
        const nameInput = document.getElementById('editTucStatusName');
        if (nameInput) {
            nameInput.addEventListener('input', () => this.validateField(nameInput));
        }
    }

    /**
     * Maneja el envío del formulario
     */
    async handleSubmit(event) {
        event.preventDefault();
        if (this.isSubmitting || !this.currentTucStatusId) return;
        const formData = new FormData(event.target);
        const data = {
            name: formData.get('name').trim(),
            active: document.getElementById('editTucStatusActive').checked
        };
        const validation = window.TucStatusUpdateService.validateTucStatusData(data);
        if (!validation.isValid) {
            this.showValidationErrors(validation.errors);
            return;
        }
        this.setSubmittingState(true);
        try {
            await window.TucStatusUpdateService.updateTucStatus(this.currentTucStatusId, data);
            this.setSubmittingState(false);
            const modal = bootstrap.Modal.getInstance(document.getElementById('editTucStatusModal'));
            if (modal) modal.hide();
            // Mostrar toast de éxito y recargar la tabla automáticamente
            GlobalToast.show('Estado TUC actualizado correctamente', 'success');
            if (window.tucStatusListControllerInstance && typeof window.tucStatusListControllerInstance.load === 'function') {
                window.tucStatusListControllerInstance.load();
            }
        } catch (error) {
            this.setSubmittingState(false);
            this.showValidationErrors([error.message]);
        }
    }

    /**
     * Valida un campo individual
     */
    validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let message = '';
        if (field.id === 'editTucStatusName') {
            if (value.length === 0) {
                isValid = false;
                message = 'El nombre es requerido';
            } else if (value.length > 100) {
                isValid = false;
                message = 'El nombre no puede exceder 100 caracteres';
            } else if (value.length < 2) {
                isValid = false;
                message = 'El nombre debe tener al menos 2 caracteres';
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
        const nameInput = document.getElementById('editTucStatusName');
        if (nameInput && errors.length > 0) {
            nameInput.classList.add('is-invalid');
            const feedback = nameInput.nextElementSibling?.nextElementSibling;
            if (feedback) feedback.textContent = errors[0];
        }
    }

    /**
     * Controla el estado de envío del formulario
     */
    setSubmittingState(isSubmitting) {
        this.isSubmitting = isSubmitting;
        const btn = document.getElementById('updateTucStatusBtn');
        if (btn) btn.disabled = isSubmitting;
    }
}
window.TucStatusEditControllerClass = TucStatusEditController;
