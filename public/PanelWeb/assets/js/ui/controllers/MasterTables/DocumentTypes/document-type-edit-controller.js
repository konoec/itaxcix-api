/**
 * Controlador para el modal de edición de tipos de documento
 * Maneja la apertura del modal, carga de datos, validación y envío del formulario
 * Usa Tabler para el diseño del modal
 */
class DocumentTypeEditController {
    constructor() {
        this.isSubmitting = false;
        this.currentDocumentTypeId = null;
        this.bindEvents();
    }

    /**
     * Abre el modal de edición de tipo de documento
     * @param {number} id - ID del tipo de documento a editar
     * @param {object} documentTypeData - Datos del tipo de documento obtenidos de la lista
     */
    async openEditModal(id, documentTypeData = null) {
        try {
            console.log('📝 Abriendo modal de edición para tipo de documento:', id);
            console.log('📊 Datos proporcionados:', documentTypeData);
            this.currentDocumentTypeId = id;
            // Si no se proporcionan datos, buscarlos en la lista actual
            if (!documentTypeData) {
                const listController = window.documentTypesListController || window.documentTypesListControllerInstance;
                if (!listController || !listController.documentTypes) {
                    throw new Error('No se encontraron datos del tipo de documento');
                }
                documentTypeData = listController.documentTypes.find(dt => dt.id === id);
                if (!documentTypeData) {
                    throw new Error('Tipo de documento no encontrado en la lista');
                }
            }
            // Mostrar modal con datos directamente
            const modalHtml = this.generateModalHtml();
            const modalContainer = document.getElementById('modal-container');
            if (modalContainer) {
                modalContainer.innerHTML = modalHtml;
                this.populateFormWithData(documentTypeData);
                this.setupModalEvents();
                const modal = new bootstrap.Modal(document.getElementById('editDocumentTypeModal'));
                modal.show();
            }
        } catch (error) {
            console.error('❌ Error al abrir modal de edición:', error);
            if (window.GlobalToast) {
                window.GlobalToast.showError(error.message || 'Error al abrir el modal de edición');
            }
        }
    }

    /**
     * Llena el formulario con los datos proporcionados
     * @param {object} documentType - Datos del tipo de documento
     */
    populateFormWithData(documentType) {
        try {
            const nameInput = document.getElementById('editDocumentTypeName');
            const activeSwitch = document.getElementById('editDocumentTypeActive');
            if (nameInput) nameInput.value = documentType.name || '';
            if (activeSwitch) activeSwitch.checked = !!documentType.active;
            setTimeout(() => {
                if (nameInput) nameInput.focus();
            }, 100);
        } catch (error) {
            console.error('❌ Error al llenar formulario:', error);
        }
    }

    /**
     * Genera el HTML del modal usando Tabler
     */
    generateModalHtml() {
        return `
            <div class="modal modal-blur fade" id="editDocumentTypeModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-edit text-primary me-2"></i>
                                Editar Tipo de Documento
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editDocumentTypeForm">
                                <div class="mb-3">
                                    <label class="form-label required">
                                        <i class="fas fa-tag text-muted me-1"></i>
                                        Nombre del Tipo de Documento
                                    </label>
                                    <input type="text" class="form-control" id="editDocumentTypeName" name="name" placeholder="Ej: Cédula de Ciudadanía" maxlength="100" required>
                                    <div class="form-hint">Máximo 100 caracteres</div>
                                    <div class="invalid-feedback"></div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="editDocumentTypeActive" name="active">
                                        <label class="form-check-label" for="editDocumentTypeActive">
                                            <strong>Estado Activo</strong>
                                        </label>
                                        <div class="form-hint text-muted">
                                            Los tipos inactivos no estarán disponibles para asignación
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
                            <button type="submit" class="btn btn-primary" id="updateDocumentTypeBtn" form="editDocumentTypeForm">
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
        const form = document.getElementById('editDocumentTypeForm');
        if (form) {
            form.addEventListener('submit', (e) => this.handleSubmit(e));
        }
        const nameInput = document.getElementById('editDocumentTypeName');
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
        if (this.isSubmitting || !this.currentDocumentTypeId) return;
        const formData = new FormData(event.target);
        const data = {
            name: formData.get('name').trim(),
            active: document.getElementById('editDocumentTypeActive').checked
        };
        const validation = window.DocumentTypeUpdateService.validateDocumentTypeData(data);
        if (!validation.isValid) {
            this.showValidationErrors(validation.errors);
            return;
        }
        try {
            this.setSubmittingState(true);
            const response = await window.DocumentTypeUpdateService.updateDocumentType(this.currentDocumentTypeId, data);
            if (response.success) {
                if (window.GlobalToast) {
                    window.GlobalToast.show(response.message || 'Tipo de documento actualizado correctamente', 'success');
                }
                // Actualizar la lista si existe el controlador
                if (window.documentTypesListController && typeof window.documentTypesListController.refreshData === 'function') {
                    window.documentTypesListController.refreshData();
                }
                // Cerrar el modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('editDocumentTypeModal'));
                if (modal) modal.hide();
            }
        } catch (error) {
            console.error('Error al actualizar tipo de documento:', error);
            if (window.GlobalToast) {
                window.GlobalToast.show(error.message || 'Error al actualizar el tipo de documento', 'error');
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
        if (field.id === 'editDocumentTypeName') {
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
        const submitBtn = document.getElementById('updateDocumentTypeBtn');
        const form = document.getElementById('editDocumentTypeForm');
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

    /**
     * Configura eventos globales
     */
    bindEvents() {
        // Evento para abrir modal desde botón editar
        document.addEventListener('click', (e) => {
            if (e.target.closest('[data-action="edit-document-type"]')) {
                const btn = e.target.closest('[data-action="edit-document-type"]');
                const id = parseInt(btn.getAttribute('data-id'));
                if (id) {
                    this.openEditModal(id);
                }
            }
        });
    }
}
// Inicializar controlador
window.DocumentTypeEditController = new DocumentTypeEditController();
window.documentTypeEditController = window.DocumentTypeEditController;
console.log('✅ DocumentTypeEditController cargado y disponible globalmente');
