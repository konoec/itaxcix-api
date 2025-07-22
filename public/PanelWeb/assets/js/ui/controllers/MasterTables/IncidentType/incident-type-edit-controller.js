/**
 * Controlador para el modal de edición de tipos de incidencia
 * Maneja la apertura del modal, carga de datos, validación y envío del formulario
 */
class IncidentTypeEditController {
    constructor() {
        this.isSubmitting = false;
        this.currentIncidentTypeId = null;
        this.bindEvents();
    }

    /**
     * Abre el modal de edición de tipo de incidencia
     * @param {number} id - ID del tipo de incidencia a editar
     * @param {object} incidentTypeData - Datos del tipo de incidencia obtenidos de la lista
     */
    async openEditModal(id, incidentTypeData = null) {
        this.currentIncidentTypeId = id;
        const modalContainer = document.getElementById('incidentTypeEditModalContainer');
        if (modalContainer) {
            modalContainer.innerHTML = this.generateModalHtml();
            this.populateFormWithData(incidentTypeData);
            this.setupModalEvents();
            if (window.bootstrap) {
                const modal = document.getElementById('incidentTypeEditModal');
                window.bootstrap.Modal.getOrCreateInstance(modal).show();
            }
        }
    }

    /**
     * Llena el formulario con los datos proporcionados
     * @param {object} incidentType - Datos del tipo de incidencia
     */
    populateFormWithData(incidentType) {
        if (!incidentType) return;
        document.getElementById('editIncidentTypeName').value = incidentType.name || '';
        document.getElementById('editIncidentTypeActive').checked = !!incidentType.active;
    }

    /**
     * Genera el HTML del modal
     */
    generateModalHtml() {
        return `
            <div class="modal modal-blur fade" id="incidentTypeEditModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-edit text-primary me-2"></i>
                                Editar Tipo de Incidencia
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editIncidentTypeForm">
                                <div class="mb-3">
                                    <label class="form-label required">
                                        <i class="fas fa-tag text-muted me-1"></i>
                                        Nombre del Tipo
                                    </label>
                                    <input type="text" class="form-control" id="editIncidentTypeName" name="name" maxlength="100" required>
                                    <div class="form-hint">Máximo 100 caracteres</div>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="editIncidentTypeActive" name="active">
                                        <label class="form-check-label" for="editIncidentTypeActive">
                                            <strong>Estado Activo</strong>
                                        </label>
                                        <div class="form-hint text-muted">Los tipos inactivos no estarán disponibles</div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary me-auto" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i> Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary" id="updateIncidentTypeBtn" form="editIncidentTypeForm">
                                <i class="fas fa-save me-1"></i> Actualizar Tipo
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
        const form = document.getElementById('editIncidentTypeForm');
        if (form) {
            form.addEventListener('submit', (e) => this.handleSubmit(e));
        }
        const nameInput = document.getElementById('editIncidentTypeName');
        if (nameInput) {
            nameInput.addEventListener('input', () => this.validateField(nameInput));
        }
    }

    /**
     * Maneja el envío del formulario
     */
    async handleSubmit(event) {
        event.preventDefault();
        if (this.isSubmitting || !this.currentIncidentTypeId) return;
        const formData = new FormData(event.target);
        const data = {
            name: formData.get('name').trim(),
            active: document.getElementById('editIncidentTypeActive').checked
        };
        const validation = window.IncidentTypeUpdateService.validateIncidentTypeData(data);
        if (!validation.isValid) {
            this.showValidationErrors(validation.errors);
            return;
        }
        this.setSubmittingState(true);
        try {
            const result = await window.IncidentTypeUpdateService.updateIncidentType(this.currentIncidentTypeId, data);
            if (result.success) {
                GlobalToast.show(result.message, 'success');
                document.getElementById('incidentTypeEditModal').addEventListener('hidden.bs.modal', () => {
                    if (window.incidentTypeListControllerInstance) {
                        window.incidentTypeListControllerInstance.load();
                    }
                }, { once: true });
                window.bootstrap.Modal.getOrCreateInstance(document.getElementById('incidentTypeEditModal')).hide();
            }
        } catch (error) {
            GlobalToast.show(error.message, 'error');
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
        if (field.id === 'editIncidentTypeName') {
            if (value.length < 2) {
                isValid = false;
                message = 'Debe tener al menos 2 caracteres';
            } else if (value.length > 100) {
                isValid = false;
                message = 'No puede exceder 100 caracteres';
            }
        }
        if (isValid) {
            field.classList.remove('is-invalid');
            field.nextElementSibling.nextElementSibling.textContent = '';
        } else {
            field.classList.add('is-invalid');
            field.nextElementSibling.nextElementSibling.textContent = message;
        }
        return isValid;
    }

    /**
     * Muestra errores de validación
     */
    showValidationErrors(errors) {
        const nameInput = document.getElementById('editIncidentTypeName');
        if (nameInput && errors.length) {
            nameInput.classList.add('is-invalid');
            nameInput.nextElementSibling.nextElementSibling.textContent = errors.join(', ');
        }
    }

    /**
     * Controla el estado de envío del formulario
     */
    setSubmittingState(isSubmitting) {
        this.isSubmitting = isSubmitting;
        const btn = document.getElementById('updateIncidentTypeBtn');
        if (btn) btn.disabled = isSubmitting;
    }

    /**
     * Configura eventos globales
     */
    bindEvents() {
        // Evento para abrir el modal desde la tabla
        document.addEventListener('click', (e) => {
            if (e.target.closest('.btn-outline-warning')) {
                const row = e.target.closest('tr');
                if (row) {
                    const id = parseInt(row.children[0].textContent);
                    const name = row.children[1].textContent;
                    const active = row.querySelector('.badge').classList.contains('bg-success-lt');
                    this.openEditModal(id, { id, name, active });
                }
            }
        });
    }
}
window.IncidentTypeEditControllerClass = IncidentTypeEditController;
