/**
 * Controlador para el modal de edición de modalidad TUC
 * Maneja la apertura del modal, carga de datos, validación y envío del formulario
 */
class TucModalityEditController {
    constructor() {
        this.isSubmitting = false;
        this.currentTucModalityId = null;
        this.bindEvents();
    }

    /**
     * Abre el modal de edición de modalidad TUC
     * @param {number} id - ID de la modalidad TUC a editar
     * @param {object} tucModalityData - Datos de la modalidad TUC obtenidos de la lista
     */
    async openEditModal(id, tucModalityData = null) {
        this.currentTucModalityId = id;
        const modalContainer = document.getElementById('modal-container');
        if (!modalContainer) return;
        modalContainer.innerHTML = this.generateModalHtml();
        this.populateFormWithData(tucModalityData);
        this.setupModalEvents();
        const modal = new bootstrap.Modal(document.getElementById('editTucModalityModal'));
        modal.show();
    }

    /**
     * Llena el formulario con los datos proporcionados
     * @param {object} tucModality - Datos de la modalidad TUC
     */
    populateFormWithData(tucModality) {
        if (!tucModality) return;
        document.getElementById('editTucModalityName').value = tucModality.name || '';
        document.getElementById('editTucModalityActive').checked = !!tucModality.active;
    }

    /**
     * Genera el HTML del modal
     */
    generateModalHtml() {
        return `
            <div class="modal modal-blur fade" id="editTucModalityModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-edit text-primary me-2"></i>
                                Editar Modalidad TUC
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="editModalForm">
                                <form id="editTucModalityForm">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label required">
                                                    <i class="fas fa-tag text-muted me-1"></i>
                                                    Nombre de la Modalidad
                                                </label>
                                                <input type="text" class="form-control" id="editTucModalityName" name="name" placeholder="Ej: Taxi, Remisse..." maxlength="100" required>
                                                <div class="form-hint">Máximo 100 caracteres</div>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="editTucModalityActive" name="active">
                                                    <label class="form-check-label" for="editTucModalityActive">
                                                        <strong>Modalidad Activa</strong>
                                                    </label>
                                                    <div class="form-hint text-muted">
                                                        Las modalidades inactivas no estarán disponibles para asignación
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
                            <button type="submit" class="btn btn-primary" id="updateTucModalityBtn" form="editTucModalityForm">
                                <i class="fas fa-save me-1"></i>
                                Actualizar Modalidad
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
        const form = document.getElementById('editTucModalityForm');
        if (form) {
            form.addEventListener('submit', this.handleSubmit.bind(this));
        }
        const nameInput = document.getElementById('editTucModalityName');
        if (nameInput) {
            nameInput.addEventListener('input', () => this.validateField(nameInput));
        }
    }

    /**
     * Maneja el envío del formulario
     */
    async handleSubmit(event) {
        event.preventDefault();
        if (this.isSubmitting || !this.currentTucModalityId) return;
        const formData = new FormData(event.target);
        const data = {
            name: formData.get('name').trim(),
            active: document.getElementById('editTucModalityActive').checked
        };
        const validation = window.TucModalityUpdateService.validateTucModalityData(data);
        if (!validation.isValid) {
            this.showValidationErrors(validation.errors);
            return;
        }
        this.setSubmittingState(true);
        try {
            await window.TucModalityUpdateService.updateTucModality(this.currentTucModalityId, data);
            this.setSubmittingState(false);
            const modal = bootstrap.Modal.getInstance(document.getElementById('editTucModalityModal'));
            if (modal) modal.hide();
            if (window.tucModalityController && typeof window.tucModalityController.refreshData === 'function') {
                window.tucModalityController.refreshData();
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
        if (field.id === 'editTucModalityName') {
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
        const nameInput = document.getElementById('editTucModalityName');
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
        const btn = document.getElementById('updateTucModalityBtn');
        if (btn) btn.disabled = isSubmitting;
    }

    /**
     * Configura eventos globales
     */
    bindEvents() {
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.edit-tuc-modality-btn');
            if (btn) {
                const id = btn.getAttribute('data-id');
                const name = btn.getAttribute('data-name');
                const active = btn.getAttribute('data-active') === 'true';
                this.openEditModal(id, { id, name, active });
            }
        });
    }
}
window.TucModalityEditControllerClass = TucModalityEditController;
