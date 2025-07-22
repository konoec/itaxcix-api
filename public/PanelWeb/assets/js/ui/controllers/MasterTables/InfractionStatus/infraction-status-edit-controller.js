/**
 * Controlador para el modal de edición de estado de infracción
 * Maneja la apertura del modal, carga de datos, validación y envío del formulario
 */
class InfractionStatusEditController {
    constructor() {
        this.isSubmitting = false;
        this.currentInfractionStatusId = null;
        this.bindEvents();
    }

    /**
     * Abre el modal de edición
     * @param {number} id - ID del estado a editar
     * @param {object} infractionStatusData - Datos del estado
     */
    async openEditModal(id, infractionStatusData = null) {
        try {
            this.currentInfractionStatusId = id;
            const modalHtml = this.generateModalHtml();
            const editContainer = document.getElementById('edit-modal-container');
            if (editContainer) {
                editContainer.innerHTML = modalHtml;
                this.populateFormWithData(infractionStatusData);
                this.setupModalEvents();
                const modal = new bootstrap.Modal(document.getElementById('editInfractionStatusModal'));
                modal.show();
            } else {
                window.GlobalToast?.showErrorToast('No se encontró el contenedor de edición');
            }
        } catch (error) {
            window.GlobalToast?.showErrorToast('Error al abrir el modal de edición');
        }
    }

    /**
     * Llena el formulario con los datos proporcionados
     */
    populateFormWithData(infractionStatus) {
        if (!infractionStatus) return;
        document.getElementById('editInfractionStatusName').value = infractionStatus.name || '';
        document.getElementById('editInfractionStatusActive').checked = !!infractionStatus.active;
    }

    /**
     * Genera el HTML del modal
     */
    generateModalHtml() {
        return `
            <div class=\"modal modal-blur fade\" id=\"editInfractionStatusModal\" tabindex=\"-1\" role=\"dialog\" aria-hidden=\"true\">
                <div class=\"modal-dialog modal-dialog-centered\" role=\"document\">
                    <div class=\"modal-content\">
                        <div class=\"modal-header\">
                            <h5 class=\"modal-title\">
                                <i class=\"fas fa-edit text-primary me-2\"></i>
                                Editar Estado de Infracción
                            </h5>
                            <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"modal\" aria-label=\"Close\"></button>
                        </div>
                        <div class=\"modal-body\">
                            <div id=\"editModalForm\">
                                <form id=\"editInfractionStatusForm\">
                                    <div class=\"row\">
                                        <div class=\"col-12\">
                                            <div class=\"mb-3\">
                                                <label class=\"form-label required\">
                                                    <i class=\"fas fa-tag text-muted me-1\"></i>
                                                    Nombre del Estado
                                                </label>
                                                <input type=\"text\"
                                                       class=\"form-control\"
                                                       id=\"editInfractionStatusName\"
                                                       name=\"name\"
                                                       placeholder=\"Ej: Pendiente, Resuelto...\"
                                                       maxlength=\"100\"
                                                       required>
                                                <div class=\"form-hint\">Máximo 100 caracteres</div>
                                                <div class=\"invalid-feedback\"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class=\"row\">
                                        <div class=\"col-12\">
                                            <div class=\"mb-3\">
                                                <div class=\"form-check form-switch\">
                                                    <input class=\"form-check-input\" type=\"checkbox\" id=\"editInfractionStatusActive\" name=\"active\">
                                                    <label class=\"form-check-label\" for=\"editInfractionStatusActive\">
                                                        <strong>Estado Activo</strong>
                                                    </label>
                                                    <div class=\"form-hint text-muted\">
                                                        Los estados inactivos no estarán disponibles para asignación
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class=\"modal-footer\" id=\"editModalFooter\">
                            <button type=\"button\" class=\"btn btn-outline-secondary me-auto\" data-bs-dismiss=\"modal\">
                                <i class=\"fas fa-times me-1\"></i>
                                Cancelar
                            </button>
                            <button type=\"submit\" class=\"btn btn-primary\" id=\"updateInfractionStatusBtn\" form=\"editInfractionStatusForm\">
                                <i class=\"fas fa-save me-1\"></i>
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
        const form = document.getElementById('editInfractionStatusForm');
        if (form) {
            form.addEventListener('submit', this.handleSubmit.bind(this));
        }
        const nameInput = document.getElementById('editInfractionStatusName');
        if (nameInput) {
            nameInput.addEventListener('input', () => this.validateField(nameInput));
        }
    }

    /**
     * Maneja el envío del formulario
     */
    async handleSubmit(event) {
        event.preventDefault();
        if (this.isSubmitting || !this.currentInfractionStatusId) return;
        const formData = new FormData(event.target);
        const data = {
            name: formData.get('name').trim(),
            active: document.getElementById('editInfractionStatusActive').checked
        };
        const validation = window.InfractionStatusUpdateService.validateInfractionStatusData(data);
        if (!validation.isValid) {
            this.showValidationErrors(validation.errors);
            return;
        }
        this.setSubmittingState(true);
        try {
            const result = await window.InfractionStatusUpdateService.updateInfractionStatus(this.currentInfractionStatusId, data);
            window.GlobalToast?.show(result.message || 'Estado actualizado correctamente');
            document.querySelector('.modal.show .btn-close')?.click();
            window.infractionStatusListController?.load();
        } catch (error) {
            this.showValidationErrors([error.message]);
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
        if (field.id === 'editInfractionStatusName') {
            if (value.length === 0) {
                isValid = false;
                message = 'El nombre es requerido';
            } else if (value.length > 100) {
                isValid = false;
                message = 'No puede exceder 100 caracteres';
            } else if (value.length < 2) {
                isValid = false;
                message = 'Debe tener al menos 2 caracteres';
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
        const nameInput = document.getElementById('editInfractionStatusName');
        if (nameInput && errors.length) {
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
        const btn = document.getElementById('updateInfractionStatusBtn');
        if (btn) btn.disabled = isSubmitting;
    }

    /**
     * Configura eventos globales
     */
    bindEvents() {
        // No global events needed for now
    }
}
window.InfractionStatusEditControllerClass = InfractionStatusEditController;
console.log('✅ InfractionStatusEditController definido y exportado globalmente');
