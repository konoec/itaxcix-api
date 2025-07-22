/**
 * Controlador para el modal de edición de tipos de combustible
 * Maneja la apertura del modal, carga de datos, validación y envío del formulario
 * Diseño y lógica siguiendo el patrón de DriverStatusEditController
 */
class FuelTypeEditController {
    constructor() {
        this.isSubmitting = false;
        this.currentFuelTypeId = null;
        this.bindEvents();
    }

    /**
     * Abre el modal de edición de tipo de combustible
     * @param {number} id - ID del tipo de combustible a editar
     * @param {object} fuelTypeData - Datos del tipo de combustible obtenidos de la lista
     */
    async openEditModal(id, fuelTypeData = null) {
        try {
            console.log('📝 Abriendo modal de edición para tipo de combustible:', id);
            console.log('📊 Datos proporcionados:', fuelTypeData);
            this.currentFuelTypeId = id;
            // Si no se proporcionan datos, buscarlos en la lista actual
            if (!fuelTypeData && window.fuelTypeController?.currentData) {
                fuelTypeData = window.fuelTypeController.currentData.find(item => item.id === id);
            }
            // Mostrar modal con datos directamente
            const modalHtml = this.generateModalHtml();
            const modalContainer = document.getElementById('modal-container');
            if (modalContainer) {
                modalContainer.innerHTML = modalHtml;
                // Usar Tabler para mostrar el modal
                const modal = new bootstrap.Modal(document.getElementById('editFuelTypeModal'));
                modal.show();
                this.populateFormWithData(fuelTypeData);
                this.setupModalEvents();
            }
        } catch (error) {
            console.error('❌ Error abriendo modal de edición:', error);
        }
    }

    /**
     * Llena el formulario con los datos proporcionados
     * @param {object} fuelType - Datos del tipo de combustible
     */
    populateFormWithData(fuelType) {
        try {
            if (!fuelType) return;
            document.getElementById('editFuelTypeName').value = fuelType.name || '';
            document.getElementById('editFuelTypeActive').checked = !!fuelType.active;
        } catch (error) {
            console.error('❌ Error llenando datos en el formulario:', error);
        }
    }

    /**
     * Genera el HTML del modal
     */
    generateModalHtml() {
        return `
            <div class="modal modal-blur fade" id="editFuelTypeModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-edit text-primary me-2"></i>
                                Editar Tipo de Combustible
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Form -->
                            <div id="editModalForm">
                                <form id="editFuelTypeForm">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label required">
                                                    <i class="fas fa-tag text-muted me-1"></i>
                                                    Nombre del Tipo de Combustible
                                                </label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="editFuelTypeName" 
                                                       name="name"
                                                       placeholder="Ej: Gasolina, Diesel, GLP..."
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
                                                    <input class="form-check-input" type="checkbox" id="editFuelTypeActive" name="active">
                                                    <label class="form-check-label" for="editFuelTypeActive">
                                                        <strong>Estado Activo</strong>
                                                    </label>
                                                    <div class="form-hint text-muted">
                                                        Los tipos inactivos no estarán disponibles para asignación
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
                            <button type="submit" class="btn btn-primary" id="updateFuelTypeBtn" form="editFuelTypeForm">
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
        const form = document.getElementById('editFuelTypeForm');
        if (form) {
            form.addEventListener('submit', (e) => this.handleSubmit(e));
        }
        // Validación en tiempo real
        const nameInput = document.getElementById('editFuelTypeName');
        if (nameInput) {
            nameInput.addEventListener('input', () => this.validateField(nameInput));
        }
    }

    /**
     * Maneja el envío del formulario
     */
    async handleSubmit(event) {
        event.preventDefault();
        if (this.isSubmitting || !this.currentFuelTypeId) return;
        const formData = new FormData(event.target);
        const data = {
            name: formData.get('name').trim(),
            active: document.getElementById('editFuelTypeActive').checked
        };
        // Validar datos
        if (!window.FuelTypeUpdateService || typeof window.FuelTypeUpdateService.validateFuelTypeData !== 'function') {
            this.showValidationErrors(['No se encontró el servicio de actualización. Recarga la página.']);
            return;
        }
        const validation = window.FuelTypeUpdateService.validateFuelTypeData(data);
        if (!validation.isValid) {
            this.showValidationErrors([validation.message]);
            return;
        }
        this.setSubmittingState(true);
        try {
            const result = await window.FuelTypeUpdateService.updateFuelType(this.currentFuelTypeId, data);
            if (result && result.success) {
                if (window.showRecoveryToast) window.showRecoveryToast('Tipo de combustible actualizado exitosamente');
                // Actualizar la lista
                if (window.fuelTypeController) window.fuelTypeController.refresh();
                // Cerrar el modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('editFuelTypeModal'));
                if (modal) modal.hide();
            } else {
                this.showValidationErrors([result.message || 'Error desconocido']);
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
        if (field.id === 'editFuelTypeName') {
            if (value.length === 0) {
                isValid = false;
                message = 'El nombre no puede estar vacío';
            } else if (value.length > 100) {
                isValid = false;
                message = 'El nombre no puede exceder 100 caracteres';
            }
        }
        // Aplicar estilos de validación
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
        const nameInput = document.getElementById('editFuelTypeName');
        if (nameInput && errors.length > 0) {
            nameInput.classList.add('is-invalid');
            nameInput.nextElementSibling.nextElementSibling.textContent = errors[0];
        }
    }

    /**
     * Controla el estado de envío del formulario
     */
    setSubmittingState(isSubmitting) {
        this.isSubmitting = isSubmitting;
        const submitBtn = document.getElementById('updateFuelTypeBtn');
        const form = document.getElementById('editFuelTypeForm');
        if (isSubmitting) {
            submitBtn.disabled = true;
            form.classList.add('was-validated');
        } else {
            submitBtn.disabled = false;
            form.classList.remove('was-validated');
        }
    }

    /**
     * Configura eventos globales
     */
    bindEvents() {
        // Evento para abrir modal desde botón editar (captura click en botón o ícono)
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.edit-fuel-type-btn');
            if (btn) {
                const id = parseInt(btn.getAttribute('data-id'));
                if (!isNaN(id)) {
                    this.openEditModal(id);
                }
            }
        });
    }
}

// Exportar la clase globalmente para uso en el inicializador
window.FuelTypeEditControllerClass = FuelTypeEditController;
console.log('✅ FuelTypeEditController definido y exportado globalmente');
