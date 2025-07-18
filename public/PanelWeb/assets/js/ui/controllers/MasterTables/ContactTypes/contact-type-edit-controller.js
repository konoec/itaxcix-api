/**
 * Controlador para el modal de edición de tipos de contacto
 * Maneja la apertura del modal, carga de datos, validación y envío del formulario
 * Usa Tabler para el diseño
 */
class ContactTypeEditController {
    constructor() {
        this.isSubmitting = false;
        this.currentContactTypeId = null;
        this.bindEvents();
    }

    /**
     * Abre el modal de edición de tipo de contacto
     * @param {number} id - ID del tipo de contacto a editar
     * @param {object} contactTypeData - Datos del tipo de contacto obtenidos de la lista
     */
    async openEditModal(id, contactTypeData = null) {
        try {
            this.currentContactTypeId = id;
            // Si no se proporcionan datos, buscarlos en la lista actual
            if (!contactTypeData) {
                const listController = window.contactTypeListController || window.contactTypeListControllerInstance;
                if (listController && Array.isArray(listController.contactTypes)) {
                    contactTypeData = listController.contactTypes.find(t => t.id === id);
                }
            }
            // Si ya existe el modal, no lo vuelvas a crear ni mostrar
            let modalEl = document.getElementById('editContactTypeModal');
            if (modalEl) {
                // Si ya está visible, no hacer nada
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) {
                    return;
                } else {
                    // Si existe pero no tiene instancia, lo eliminamos para limpiar
                    modalEl.parentNode.removeChild(modalEl);
                }
            }
            // Mostrar modal con datos directamente
            const modalHtml = this.generateModalHtml(false);
            const modalContainer = document.getElementById('modal-container');
            if (modalContainer) {
                modalContainer.insertAdjacentHTML('beforeend', modalHtml);
                this.populateFormWithData(contactTypeData);
                this.setupModalEvents();
                modalEl = document.getElementById('editContactTypeModal');
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
                // Eliminar el modal del DOM al cerrarse
                modalEl.addEventListener('hidden.bs.modal', function() {
                    if (modalEl && modalEl.parentNode) {
                        modalEl.parentNode.removeChild(modalEl);
                    }
                });
            }
        } catch (error) {
            console.error('❌ Error al abrir modal de edición:', error);
            if (window.GlobalToast) {
                window.GlobalToast.show('Error al abrir el modal de edición', 'error');
            }
        }
    }

    /**
     * Llena el formulario con los datos proporcionados
     * @param {object} contactType - Datos del tipo de contacto
     */
    populateFormWithData(contactType) {
        if (!contactType) return;
        const nameInput = document.getElementById('editContactTypeName');
        const activeSwitch = document.getElementById('editContactTypeActive');
        if (nameInput) nameInput.value = contactType.name || '';
        if (activeSwitch) activeSwitch.checked = !!contactType.active;
    }

    /**
     * Genera el HTML del modal
     * @param {boolean} showLoading - Si mostrar estado de carga (no usado)
     */
    generateModalHtml(showLoading = false) {
        return `
            <div class="modal modal-blur fade" id="editContactTypeModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-edit text-primary me-2"></i>
                                Editar Tipo de Contacto
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editContactTypeForm">
                                <div class="mb-3">
                                    <label class="form-label required">
                                        <i class="fas fa-tag text-muted me-1"></i>
                                        Nombre del Tipo de Contacto
                                    </label>
                                    <input type="text" class="form-control" id="editContactTypeName" name="name" placeholder="Ej: Teléfono móvil, Email personal..." maxlength="100" required>
                                    <div class="form-hint">Máximo 100 caracteres</div>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="editContactTypeActive" name="active">
                                        <label class="form-check-label" for="editContactTypeActive">
                                            <strong>Activo</strong>
                                        </label>
                                        <div class="form-hint text-muted">
                                            Los tipos de contacto inactivos no aparecerán en los formularios
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary me-auto" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary" id="updateContactTypeBtn" form="editContactTypeForm">
                                <i class="fas fa-save me-1"></i>
                                Actualizar Tipo
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
        const form = document.getElementById('editContactTypeForm');
        if (form) {
            form.addEventListener('submit', (e) => this.handleSubmit(e));
        }
        const nameInput = document.getElementById('editContactTypeName');
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
        if (this.isSubmitting || !this.currentContactTypeId) return;
        const formData = new FormData(event.target);
        const data = {
            name: formData.get('name').trim(),
            active: document.getElementById('editContactTypeActive').checked
        };
        // Validar datos
        const validation = window.ContactTypeUpdateService.validateContactTypeData(data);
        if (!validation.isValid) {
            this.showValidationErrors(validation.errors);
            return;
        }
        try {
            this.setSubmittingState(true);
            const response = await window.ContactTypeUpdateService.updateContactType(this.currentContactTypeId, data);
            if (response.success) {
                if (window.GlobalToast) window.GlobalToast.show('Tipo de contacto actualizado correctamente', 'success');
                // Cerrar modal y refrescar lista
                const modal = bootstrap.Modal.getInstance(document.getElementById('editContactTypeModal'));
                if (modal) modal.hide();
                setTimeout(() => {
                    if (window.contactTypeListController) window.contactTypeListController.load();
                }, 300);
            } else {
                this.showValidationErrors([response.message || 'Error desconocido']);
            }
        } catch (error) {
            this.showValidationErrors([error.message || 'Error al actualizar tipo de contacto']);
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
        if (field.id === 'editContactTypeName') {
            if (!value) {
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
            if (window.GlobalToast) window.GlobalToast.show(error, 'error');
        });
    }

    /**
     * Controla el estado de envío del formulario
     */
    setSubmittingState(isSubmitting) {
        this.isSubmitting = isSubmitting;
        const submitBtn = document.getElementById('updateContactTypeBtn');
        const form = document.getElementById('editContactTypeForm');
        if (submitBtn && form) {
            if (isSubmitting) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Actualizando...';
                form.style.pointerEvents = 'none';
            } else {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>Actualizar Tipo';
                form.style.pointerEvents = 'auto';
            }
        }
    }

    /**
     * Configura eventos globales
     */
    bindEvents() {
        // Evento para abrir modal desde botón editar
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('[data-action="edit-contact-type"]');
            if (btn) {
                const id = parseInt(btn.getAttribute('data-contact-type-id'));
                if (id) {
                    this.openEditModal(id);
                }
            }
        });
    }
}

window.ContactTypeEditController = new ContactTypeEditController();
window.contactTypeEditController = window.ContactTypeEditController;

console.log('✅ ContactTypeEditController cargado y disponible globalmente');
