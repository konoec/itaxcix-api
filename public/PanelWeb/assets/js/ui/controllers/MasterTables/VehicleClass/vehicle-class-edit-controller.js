/**
 * Controlador para el modal de edición de clase de vehículo
 * Maneja la apertura del modal, carga de datos, validación y envío del formulario
 * Usa Tabler para el diseño del modal
 */
class VehicleClassEditController {
    constructor() {
        this.isSubmitting = false;
        this.currentVehicleClassId = null;
        this.bindEvents();
    }

    /**
     * Abre el modal de edición de clase de vehículo
     * @param {number} id - ID de la clase de vehículo a editar
     * @param {object} vehicleClassData - Datos de la clase obtenidos de la lista
     */
    async openEditModal(id, vehicleClassData = null) {
        this.currentVehicleClassId = id;
        const modalHtml = this.generateModalHtml();
        const modalContainer = document.getElementById('modal-container');
        modalContainer.innerHTML = modalHtml;
        const modalElement = document.getElementById('editVehicleClassModal');
        this.populateFormWithData(vehicleClassData);
        const tablerModal = new bootstrap.Modal(modalElement);
        tablerModal.show();
        this.setupModalEvents(tablerModal);
    }

    /**
     * Llena el formulario con los datos proporcionados
     * @param {object} vehicleClass - Datos de la clase de vehículo
     */
    populateFormWithData(vehicleClass) {
        if (!vehicleClass) return;
        document.getElementById('editVehicleClassName').value = vehicleClass.name || '';
        document.getElementById('editVehicleClassActive').checked = !!vehicleClass.active;
    }

    /**
     * Genera el HTML del modal usando Tabler
     */
    generateModalHtml() {
        return `
            <div class="modal modal-blur fade" id="editVehicleClassModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-edit text-primary me-2"></i>
                                Editar Clase de Vehículo
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div id="editModalForm">
                                <form id="editVehicleClassForm">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label required">
                                                    <i class="fas fa-tag text-muted me-1"></i>
                                                    Nombre de la Clase
                                                </label>
                                                <input type="text" class="form-control" id="editVehicleClassName" name="name" placeholder="Ej: SUV, Camión, Motocicleta..." maxlength="100" required>
                                                <div class="form-hint">Máximo 100 caracteres</div>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="editVehicleClassActive" name="active">
                                                    <label class="form-check-label" for="editVehicleClassActive">
                                                        <strong>Estado Activo</strong>
                                                    </label>
                                                    <div class="form-hint text-muted">Las clases inactivas no estarán disponibles para asignación</div>
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
                            <button type="submit" class="btn btn-primary" id="updateVehicleClassBtn" form="editVehicleClassForm">
                                <i class="fas fa-save me-1"></i>
                                Actualizar Clase
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
    setupModalEvents(tablerModal) {
        const form = document.getElementById('editVehicleClassForm');
        if (form) {
            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                if (this.isSubmitting) return;
                this.setSubmittingState(true);
                const name = document.getElementById('editVehicleClassName').value.trim();
                const active = document.getElementById('editVehicleClassActive').checked;
                const errors = this.validateForm({ name, active });
                if (errors.length) {
                    this.showValidationErrors(errors);
                    this.setSubmittingState(false);
                    return;
                }
                try {
                    const response = await window.vehicleClassUpdateService.updateVehicleClass(this.currentVehicleClassId, { name, active });
                    if (response.success) {
                        window.showRecoveryToast(response.message, 'success');
                        tablerModal.hide();
                        setTimeout(() => {
                            if (window.vehicleClassListControllerInstance) {
                                window.vehicleClassListControllerInstance.load();
                            }
                        }, 400);
                    } else {
                        window.showRecoveryToast(response.message || 'Error al actualizar la clase', 'error');
                    }
                } catch (error) {
                    window.showRecoveryToast(error.message, 'error');
                } finally {
                    this.setSubmittingState(false);
                }
            });
        }
    }

    /**
     * Valida el formulario
     */
    validateForm(data) {
        const errors = [];
        if (!data.name || data.name.length === 0) {
            errors.push('El nombre es requerido');
        } else if (data.name.length > 100) {
            errors.push('El nombre no puede superar los 100 caracteres');
        }
        return errors;
    }

    /**
     * Muestra errores de validación
     */
    showValidationErrors(errors) {
        const nameInput = document.getElementById('editVehicleClassName');
        const feedback = nameInput.nextElementSibling.nextElementSibling;
        if (errors.length) {
            nameInput.classList.add('is-invalid');
            feedback.textContent = errors.join(', ');
        } else {
            nameInput.classList.remove('is-invalid');
            feedback.textContent = '';
        }
    }

    /**
     * Controla el estado de envío del formulario
     */
    setSubmittingState(isSubmitting) {
        this.isSubmitting = isSubmitting;
        const btn = document.getElementById('updateVehicleClassBtn');
        if (btn) {
            btn.disabled = isSubmitting;
            btn.innerHTML = isSubmitting ? '<span class="spinner-border spinner-border-sm me-2"></span> Guardando...' : '<i class="fas fa-save me-1"></i> Actualizar Clase';
        }
    }

    /**
     * Configura eventos globales
     */
    bindEvents() {
        document.addEventListener('click', (e) => {
            const editBtn = e.target.closest('.btn-outline-warning');
            if (editBtn && editBtn.querySelector('i.fa-edit')) {
                const row = editBtn.closest('tr');
                if (!row) return;
                const idCell = row.querySelector('td.text-center');
                const nameCell = row.querySelector('td:nth-child(2)');
                const activeCell = row.querySelector('span.badge');
                const id = idCell ? parseInt(idCell.textContent.trim()) : null;
                const name = nameCell ? nameCell.textContent.trim() : '';
                const active = activeCell ? activeCell.classList.contains('bg-success-lt') : false;
                this.openEditModal(id, { id, name, active });
            }
        });
    }
}

// Exportar el controlador para uso global
window.vehicleClassEditController = window.VehicleClassEditController;

console.log('✅ VehicleClassEditController cargado y disponible globalmente');
