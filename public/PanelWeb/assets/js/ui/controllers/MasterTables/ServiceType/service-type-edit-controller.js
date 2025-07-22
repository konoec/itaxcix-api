/**
 * Controlador para el modal de edición de tipos de servicio
 * Maneja la apertura del modal, carga de datos, validación y envío del formulario
 */
class ServiceTypeEditController {
    constructor() {
        this.isSubmitting = false;
        this.currentServiceTypeId = null;
        this.bindEvents();
    }

    /**
     * Abre el modal de edición de tipo de servicio
     * @param {number} id - ID del tipo de servicio a editar
     * @param {object} serviceTypeData - Datos del tipo de servicio obtenidos de la lista
     */
    async openEditModal(id, serviceTypeData = null) {
        try {
            this.currentServiceTypeId = id;
            // Generar y mostrar el modal
            const modalHtml = this.generateModalHtml();
            const modalContainer = document.getElementById('modal-container');
            modalContainer.innerHTML = modalHtml;
            // Llenar el formulario si hay datos
            if (serviceTypeData) {
                this.populateFormWithData(serviceTypeData);
            }
            // Configurar eventos del modal
            this.setupModalEvents();
            // Mostrar el modal usando Tabler
            const modal = new bootstrap.Modal(document.getElementById('editServiceTypeModal'));
            modal.show();
        } catch (error) {
            console.error('❌ Error abriendo modal de edición:', error);
        }
    }

    /**
     * Llena el formulario con los datos proporcionados
     * @param {object} serviceType - Datos del tipo de servicio
     */
    populateFormWithData(serviceType) {
        try {
            document.getElementById('editServiceTypeName').value = serviceType.name || '';
            document.getElementById('editServiceTypeActive').checked = !!serviceType.active;
        } catch (error) {
            console.error('❌ Error llenando formulario:', error);
        }
    }

    /**
     * Genera el HTML del modal
     */
    generateModalHtml() {
        return `
            <div class="modal modal-blur fade" id="editServiceTypeModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-edit text-primary me-2"></i>
                                Editar Tipo de Servicio
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="editModalForm">
                                <form id="editServiceTypeForm">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label required">
                                                    <i class="fas fa-tag text-muted me-1"></i>
                                                    Nombre del Tipo de Servicio
                                                </label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="editServiceTypeName" 
                                                       name="name"
                                                       placeholder="Ej: Taxi, Delivery, VIP..."
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
                                                    <input class="form-check-input" type="checkbox" id="editServiceTypeActive" name="active">
                                                    <label class="form-check-label" for="editServiceTypeActive">
                                                        <strong>Estado Activo</strong>
                                                    </label>
                                                    <div class="form-hint text-muted">
                                                        Los tipos de servicio inactivos no estarán disponibles para asignación
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
                            <button type="submit" class="btn btn-primary" id="updateServiceTypeBtn" form="editServiceTypeForm">
                                <i class="fas fa-save me-1"></i>
                                Actualizar Tipo de Servicio
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
        const form = document.getElementById('editServiceTypeForm');
        if (form) {
            form.addEventListener('submit', this.handleSubmit.bind(this));
        }
        // Validación en tiempo real
        const nameInput = document.getElementById('editServiceTypeName');
        if (nameInput) {
            nameInput.addEventListener('input', () => this.validateField(nameInput));
        }
    }

    /**
     * Maneja el envío del formulario
     */
    async handleSubmit(event) {
        event.preventDefault();
        if (this.isSubmitting || !this.currentServiceTypeId) return;
        const formData = new FormData(event.target);
        const data = {
            name: formData.get('name').trim(),
            active: document.getElementById('editServiceTypeActive').checked
        };
        // Validar datos
        const validation = window.ServiceTypeUpdateService.validateServiceTypeData(data);
        if (!validation.isValid) {
            this.showValidationErrors(validation.errors);
            return;
        }
        this.setSubmittingState(true);
        try {
            const result = await window.ServiceTypeUpdateService.updateServiceType(this.currentServiceTypeId, data);
            if (result.success) {
                // Cerrar modal y refrescar lista
                bootstrap.Modal.getInstance(document.getElementById('editServiceTypeModal')).hide();
                if (window.serviceTypeController) {
                    window.serviceTypeController.refresh();
                }
                if (window.GlobalToast) {
                    window.GlobalToast.showToast(result.message || 'Tipo de servicio actualizado correctamente', 'success');
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
        if (field.id === 'editServiceTypeName') {
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
        const nameInput = document.getElementById('editServiceTypeName');
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
        const btn = document.getElementById('updateServiceTypeBtn');
        if (btn) {
            btn.disabled = isSubmitting;
            btn.innerHTML = isSubmitting
                ? '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Actualizando...'
                : '<i class="fas fa-save me-1"></i> Actualizar Tipo de Servicio';
        }
    }

    /**
     * Configura eventos globales
     */
    bindEvents() {
        // Escuchar evento global para abrir el modal de edición
        document.addEventListener('openServiceTypeEditModal', (e) => {
            const { id, serviceTypeData } = e.detail || {};
            this.openEditModal(id, serviceTypeData);
        });
    }
}
// Exportar la clase globalmente para uso en el inicializador
window.ServiceTypeEditControllerClass = ServiceTypeEditController;
console.log('✅ ServiceTypeEditController definido y exportado globalmente');
