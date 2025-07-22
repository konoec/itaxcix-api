/**
 * Controlador para el modal de edición de estados de viaje
 * Maneja la apertura del modal, carga de datos, validación y envío del formulario
 */
class TravelStatusEditController {
    constructor() {
        this.isSubmitting = false;
        this.currentTravelStatusId = null;
        this.bindEvents();
    }

    /**
     * Abre el modal de edición de estado de viaje
     * @param {number} id - ID del estado de viaje a editar
     * @param {object} travelStatusData - Datos del estado de viaje obtenidos de la lista
     */
    async openEditModal(id, travelStatusData = null) {
        this.currentTravelStatusId = id;
        const modalContainer = document.getElementById('modal-container');
        if (!modalContainer) return;
        modalContainer.innerHTML = this.generateModalHtml();
        this.populateFormWithData(travelStatusData);
        this.setupModalEvents();
        const modal = new bootstrap.Modal(document.getElementById('editTravelStatusModal'));
        modal.show();
    }

    /**
     * Llena el formulario con los datos proporcionados
     * @param {object} travelStatus - Datos del estado de viaje
     */
    populateFormWithData(travelStatus) {
        if (!travelStatus) return;
        document.getElementById('editTravelStatusName').value = travelStatus.name || '';
        document.getElementById('editTravelStatusActive').checked = !!travelStatus.active;
    }

    /**
     * Genera el HTML del modal
     */
    generateModalHtml() {
        return `
            <div class="modal modal-blur fade" id="editTravelStatusModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-edit text-primary me-2"></i>
                                Editar Estado de Viaje
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="editModalForm">
                                <form id="editTravelStatusForm">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label required">
                                                    <i class="fas fa-tag text-muted me-1"></i>
                                                    Nombre del Estado
                                                </label>
                                                <input type="text"
                                                       class="form-control"
                                                       id="editTravelStatusName"
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
                                                    <input class="form-check-input" type="checkbox" id="editTravelStatusActive" name="active">
                                                    <label class="form-check-label" for="editTravelStatusActive">
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
                            <button type="submit" class="btn btn-primary" id="updateTravelStatusBtn" form="editTravelStatusForm">
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
        const form = document.getElementById('editTravelStatusForm');
        if (form) {
            form.addEventListener('submit', this.handleSubmit.bind(this));
        }
        const nameInput = document.getElementById('editTravelStatusName');
        if (nameInput) {
            nameInput.addEventListener('input', () => this.validateField(nameInput));
        }
    }

    /**
     * Maneja el envío del formulario
     */
    async handleSubmit(event) {
        event.preventDefault();
        if (this.isSubmitting || !this.currentTravelStatusId) return;
        const formData = new FormData(event.target);
        const data = {
            name: formData.get('name').trim(),
            active: document.getElementById('editTravelStatusActive').checked
        };
        const validation = window.TravelStatusUpdateService.validateTravelStatusData(data);
        if (!validation.isValid) {
            this.showValidationErrors(validation.errors);
            return;
        }
        this.setSubmittingState(true);
        try {
            const result = await window.TravelStatusUpdateService.updateTravelStatus(this.currentTravelStatusId, data);
            if (result.success) {
                this.showSuccessToast('Estado de viaje actualizado correctamente');
                setTimeout(() => {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editTravelStatusModal'));
                    if (modal) modal.hide();
                    if (window.travelStatusListController) window.travelStatusListController.refresh();
                },);
            }
        } catch (error) {
            this.showErrorToast(error.message);
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
        if (field.id === 'editTravelStatusName') {
            if (value.length === 0) {
                isValid = false;
                message = 'El nombre es requerido';
            } else if (value.length < 2) {
                isValid = false;
                message = 'Debe tener al menos 2 caracteres';
            } else if (value.length > 100) {
                isValid = false;
                message = 'No puede exceder 100 caracteres';
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
        const nameInput = document.getElementById('editTravelStatusName');
        if (nameInput && errors.some(e => e.includes('nombre'))) {
            this.validateField(nameInput);
        }
    }

    /**
     * Controla el estado de envío del formulario
     */
    setSubmittingState(isSubmitting) {
        this.isSubmitting = isSubmitting;
        const btn = document.getElementById('updateTravelStatusBtn');
        if (btn) btn.disabled = isSubmitting;
    }

    /**
     * Toast de éxito
     */
    showSuccessToast(message) {
        if (window.GlobalToast) {
            window.GlobalToast.show(message, 'success');
        } else {
            alert(message);
        }
    }

    /**
     * Toast de error
     */
    showErrorToast(message) {
        if (window.GlobalToast) {
            window.GlobalToast.show(message, 'error');
        } else {
            alert(message);
        }
    }

    /**
     * Configura eventos globales
     */
    bindEvents() {
        // No global events needed for now
    }
}
window.TravelStatusEditControllerClass = TravelStatusEditController;
console.log('✅ TravelStatusEditController definido y exportado globalmente');
