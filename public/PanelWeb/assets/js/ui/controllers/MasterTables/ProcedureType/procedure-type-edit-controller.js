/**
 * Controlador para el modal de edición de tipos de trámite
 * Maneja la apertura del modal, carga de datos, validación y envío del formulario
 */
class ProcedureTypeEditController {
    constructor() {
        this.isSubmitting = false;
        this.currentProcedureTypeId = null;
        this.bindEvents();
    }

    /**
     * Abre el modal de edición de tipo de trámite
     * @param {number} id - ID del tipo de trámite a editar
     * @param {object} procedureTypeData - Datos del tipo de trámite obtenidos de la lista
     */
    async openEditModal(id, procedureTypeData = null) {
        try {
            this.currentProcedureTypeId = id;
            // Generar y mostrar el modal
            const modalHtml = this.generateModalHtml();
            const modalContainer = document.getElementById('modal-container');
            modalContainer.innerHTML = modalHtml;
            // Llenar el formulario si hay datos
            if (procedureTypeData) {
                this.populateFormWithData(procedureTypeData);
            }
            // Configurar eventos del modal
            this.setupModalEvents();
            // Mostrar el modal usando Tabler
            const modal = new bootstrap.Modal(document.getElementById('editProcedureTypeModal'));
            modal.show();
        } catch (error) {
            console.error('❌ Error abriendo modal de edición:', error);
        }
    }

    /**
     * Llena el formulario con los datos proporcionados
     * @param {object} procedureType - Datos del tipo de trámite
     */
    populateFormWithData(procedureType) {
        try {
            document.getElementById('editProcedureTypeName').value = procedureType.name || '';
            document.getElementById('editProcedureTypeActive').checked = !!procedureType.active;
        } catch (error) {
            console.error('❌ Error llenando formulario:', error);
        }
    }

    /**
     * Genera el HTML del modal
     */
    generateModalHtml() {
        return `
            <div class="modal modal-blur fade" id="editProcedureTypeModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-edit text-primary me-2"></i>
                                Editar Tipo de Trámite
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="editModalForm">
                                <form id="editProcedureTypeForm">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label required">
                                                    <i class="fas fa-tag text-muted me-1"></i>
                                                    Nombre del Tipo de Trámite
                                                </label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="editProcedureTypeName" 
                                                       name="name"
                                                       placeholder="Ej: Licencia, Renovación, Inscripción..."
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
                                                    <input class="form-check-input" type="checkbox" id="editProcedureTypeActive" name="active">
                                                    <label class="form-check-label" for="editProcedureTypeActive">
                                                        <strong>Estado Activo</strong>
                                                    </label>
                                                    <div class="form-hint text-muted">
                                                        Los tipos de trámite inactivos no estarán disponibles para asignación
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
                            <button type="submit" class="btn btn-primary" id="updateProcedureTypeBtn" form="editProcedureTypeForm">
                                <i class="fas fa-save me-1"></i>
                                Actualizar Tipo de Trámite
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
        const form = document.getElementById('editProcedureTypeForm');
        if (form) {
            form.addEventListener('submit', this.handleSubmit.bind(this));
        }
        // Validación en tiempo real
        const nameInput = document.getElementById('editProcedureTypeName');
        if (nameInput) {
            nameInput.addEventListener('input', () => this.validateField(nameInput));
        }
    }

    /**
     * Maneja el envío del formulario
     */
    async handleSubmit(event) {
        event.preventDefault();
        if (this.isSubmitting || !this.currentProcedureTypeId) return;
        const formData = new FormData(event.target);
        const data = {
            name: formData.get('name').trim(),
            active: document.getElementById('editProcedureTypeActive').checked
        };
        // Validar datos
        const validation = window.ProcedureTypeUpdateService.validateProcedureTypeData(data);
        if (!validation.isValid) {
            this.showValidationErrors(validation.errors);
            return;
        }
        this.setSubmittingState(true);
        try {
            const result = await window.ProcedureTypeUpdateService.updateProcedureType(this.currentProcedureTypeId, data);
            if (result.success) {
                // Cerrar modal y refrescar lista
                bootstrap.Modal.getInstance(document.getElementById('editProcedureTypeModal')).hide();
                if (window.procedureTypeListControllerInstance) {
                    window.procedureTypeListControllerInstance.load();
                }
                // Mostrar notificación toast de éxito
                if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
                    window.GlobalToast.show(result.message || 'Tipo de trámite actualizado correctamente', 'success');
                } else {
                    // Fallback: alert si no existe GlobalToast
                    alert(result.message || 'Tipo de trámite actualizado correctamente');
                }
            }
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
        if (field.id === 'editProcedureTypeName') {
            if (value.length === 0) {
                isValid = false;
                message = 'El nombre no puede estar vacío';
            } else if (value.length > 100) {
                isValid = false;
                message = 'El nombre no puede exceder 100 caracteres';
            } else if (value.length < 2) {
                isValid = false;
                message = 'El nombre debe tener al menos 2 caracteres';
            }
        }
        // Aplicar estilos de validación
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
        const nameInput = document.getElementById('editProcedureTypeName');
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
        const btn = document.getElementById('updateProcedureTypeBtn');
        if (btn) {
            btn.disabled = isSubmitting;
            btn.innerHTML = isSubmitting
                ? '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Actualizando...'
                : '<i class="fas fa-save me-1"></i> Actualizar Tipo de Trámite';
        }
    }

    /**
     * Configura eventos globales
     */
    bindEvents() {
        // Escuchar evento global para abrir el modal de edición
        document.addEventListener('openProcedureTypeEditModal', (e) => {
            const { id, procedureTypeData } = e.detail || {};
            this.openEditModal(id, procedureTypeData);
        });
    }
}
// Exportar la clase globalmente para uso en el inicializador
window.ProcedureTypeEditControllerClass = ProcedureTypeEditController;
console.log('✅ ProcedureTypeEditController definido y exportado globalmente');
