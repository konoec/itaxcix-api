/**
 * Controlador para el modal de edición de estados de conductor
 * Maneja la apertura del modal, carga de datos, validación y envío del formulario
 */
class DriverStatusEditController {
    constructor() {
        this.isSubmitting = false;
        this.currentDriverStatusId = null;
        this.bindEvents();
    }

    /**
     * Abre el modal de edición de estado de conductor
     * @param {number} id - ID del estado de conductor a editar
     * @param {object} driverStatusData - Datos del estado de conductor obtenidos de la lista
     */
    async openEditModal(id, driverStatusData = null) {
        try {
            console.log('📝 Abriendo modal de edición para estado de conductor:', id);
            console.log('📊 Datos proporcionados:', driverStatusData);
            
            this.currentDriverStatusId = id;
            
            // Si no se proporcionan datos, buscarlos en la lista actual
            if (!driverStatusData) {
                console.log('🔍 Buscando datos en el controlador de lista...');
                
                // Verificar si el controlador de lista existe (múltiples referencias posibles)
                const listController = window.driverStatusListController || window.driverStatusListControllerInstance;
                
                if (!listController) {
                    console.error('❌ Controlador de lista no está disponible');
                    console.log('🔍 window.driverStatusListController:', !!window.driverStatusListController);
                    console.log('🔍 window.driverStatusListControllerInstance:', !!window.driverStatusListControllerInstance);
                    throw new Error('El controlador de lista no está disponible. Recarga la página e intenta nuevamente.');
                }
                
                // Verificar si hay datos en la lista
                if (!listController.driverStatuses || !Array.isArray(listController.driverStatuses)) {
                    console.error('❌ No hay datos en la lista de estados');
                    throw new Error('No hay datos cargados en la lista. Recarga la página e intenta nuevamente.');
                }
                
                console.log('📋 Estados disponibles:', listController.driverStatuses.length);
                
                // Buscar el estado específico
                driverStatusData = listController.driverStatuses.find(status => status.id === id);
                
                if (!driverStatusData) {
                    console.error('❌ Estado no encontrado en la lista. ID buscado:', id);
                    console.log('📋 IDs disponibles:', listController.driverStatuses.map(s => s.id));
                    throw new Error(`No se encontró el estado de conductor con ID ${id}. Recarga la lista e intenta nuevamente.`);
                }
                
                console.log('✅ Estado encontrado:', driverStatusData);
            }
            
            // Mostrar modal con datos directamente
            const modalHtml = this.generateModalHtml(false);
            const modalContainer = document.getElementById('modal-container');
            if (modalContainer) {
                modalContainer.innerHTML = modalHtml;
                
                const modal = new bootstrap.Modal(document.getElementById('editDriverStatusModal'));
                modal.show();
                
                // Cargar datos del estado de conductor
                this.populateFormWithData(driverStatusData);
                
                // Configurar eventos del modal
                this.setupModalEvents();
            }
        } catch (error) {
            console.error('❌ Error al abrir modal de edición:', error);
            if (window.GlobalToast) {
                window.GlobalToast.show(error.message, 'error');
            }
        }
    }

    /**
     * Llena el formulario con los datos proporcionados
     * @param {object} driverStatus - Datos del estado de conductor
     */
    populateFormWithData(driverStatus) {
        try {
            console.log('📥 Llenando formulario con datos:', driverStatus);
            
            // Llenar formulario con datos
            const nameInput = document.getElementById('editDriverStatusName');
            const activeSwitch = document.getElementById('editDriverStatusActive');
            
            if (nameInput) nameInput.value = driverStatus.name || '';
            if (activeSwitch) activeSwitch.checked = Boolean(driverStatus.active);
            
            // Actualizar título del modal
            const modalTitle = document.querySelector('#editDriverStatusModal .modal-title');
            if (modalTitle) {
                modalTitle.innerHTML = `
                    <i class="fas fa-edit text-primary me-2"></i>
                    Editar Estado: ${driverStatus.name}
                `;
            }
            
            // Focus en el primer campo
            setTimeout(() => {
                if (nameInput) nameInput.focus();
            }, 100);
            
            console.log('✅ Formulario llenado exitosamente');
            
        } catch (error) {
            console.error('❌ Error al llenar formulario:', error);
            
            // Mostrar error en el modal
            const modalBody = document.querySelector('#editDriverStatusModal .modal-body');
            if (modalBody) {
                modalBody.innerHTML = `
                    <div class="text-center py-4">
                        <div class="text-danger">
                            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                            <h4>Error al cargar datos</h4>
                            <p class="text-muted">${error.message}</p>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Cerrar
                            </button>
                        </div>
                    </div>
                `;
            }
        }
    }

    /**
     * Genera el HTML del modal
     * @param {boolean} showLoading - Si mostrar estado de carga (ya no se usa)
     */
    generateModalHtml(showLoading = false) {
        return `
            <div class="modal modal-blur fade" id="editDriverStatusModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-edit text-primary me-2"></i>
                                Editar Estado de Conductor
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        
                        <div class="modal-body">
                            <!-- Form -->
                            <div id="editModalForm">
                                <form id="editDriverStatusForm">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label required">
                                                    <i class="fas fa-tag text-muted me-1"></i>
                                                    Nombre del Estado
                                                </label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="editDriverStatusName" 
                                                       name="name"
                                                       placeholder="Ej: Disponible, Ocupado, Descanso..."
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
                                                    <input class="form-check-input" type="checkbox" id="editDriverStatusActive" name="active">
                                                    <label class="form-check-label" for="editDriverStatusActive">
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
                            <button type="submit" class="btn btn-primary" id="updateDriverStatusBtn" form="editDriverStatusForm">
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
        const form = document.getElementById('editDriverStatusForm');
        if (form) {
            form.addEventListener('submit', (e) => this.handleSubmit(e));
        }

        // Validación en tiempo real
        const nameInput = document.getElementById('editDriverStatusName');
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
        
        if (this.isSubmitting || !this.currentDriverStatusId) return;

        const formData = new FormData(event.target);
        const data = {
            name: formData.get('name').trim(),
            active: document.getElementById('editDriverStatusActive').checked
        };

        // Validar datos
        const validation = window.DriverStatusUpdateService.validateDriverStatusData(data);
        if (!validation.isValid) {
            this.showValidationErrors(validation.errors);
            return;
        }

        try {
            this.setSubmittingState(true);
            
            const response = await window.DriverStatusUpdateService.updateDriverStatus(this.currentDriverStatusId, data);
            
            if (response.success) {
                // Mostrar mensaje de éxito
                if (window.GlobalToast) {
                    window.GlobalToast.show(
                        response.message || 'Estado de conductor actualizado correctamente',
                        'success'
                    );
                }

                // Cerrar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('editDriverStatusModal'));
                if (modal) modal.hide();

                // Recargar lista si existe el controlador
                const listController = window.driverStatusListController || window.driverStatusListControllerInstance;
                if (listController && typeof listController.loadDriverStatuses === 'function') {
                    await listController.loadDriverStatuses();
                }
            }
        } catch (error) {
            console.error('Error al actualizar estado de conductor:', error);
            if (window.GlobalToast) {
                window.GlobalToast.show(
                    error.message || 'Error al actualizar el estado de conductor',
                    'error'
                );
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

        if (field.id === 'editDriverStatusName') {
            if (!value) {
                isValid = false;
                message = 'El nombre del estado es requerido';
            } else if (value.length > 100) {
                isValid = false;
                message = 'El nombre no puede exceder 100 caracteres';
            } else if (value.length < 2) {
                isValid = false;
                message = 'El nombre debe tener al menos 2 caracteres';
            }
        }

        // Aplicar estilos de validación
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
        const submitBtn = document.getElementById('updateDriverStatusBtn');
        const form = document.getElementById('editDriverStatusForm');

        if (isSubmitting) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Actualizando...';
            form.style.pointerEvents = 'none';
        } else {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>Actualizar Estado';
            form.style.pointerEvents = 'auto';
        }
    }

    /**
     * Configura eventos globales
     */
    bindEvents() {
        // Evento para abrir modal desde botón editar
        document.addEventListener('click', (e) => {
            if (e.target.closest('[data-action="edit-driver-status"]')) {
                e.preventDefault();
                const btn = e.target.closest('[data-action="edit-driver-status"]');
                const id = btn.getAttribute('data-driver-status-id');
                
                console.log('🖱️ Click en botón editar. ID:', id);
                
                if (id) {
                    // Buscar los datos en la lista actual
                    let driverStatusData = null;
                    
                    console.log('🔍 Verificando controlador de lista...');
                    
                    // Verificar múltiples referencias posibles del controlador
                    const listController = window.driverStatusListController || window.driverStatusListControllerInstance;
                    console.log('📋 Controlador de lista encontrado:', !!listController);
                    
                    if (listController && listController.driverStatuses) {
                        console.log('📊 Datos disponibles:', listController.driverStatuses.length, 'estados');
                        driverStatusData = listController.driverStatuses.find(
                            status => status.id === parseInt(id)
                        );
                        console.log('🎯 Estado encontrado localmente:', !!driverStatusData);
                    }
                    
                    this.openEditModal(parseInt(id), driverStatusData);
                } else {
                    console.error('❌ No se encontró ID en el botón de editar');
                    if (window.GlobalToast) {
                        window.GlobalToast.show('Error: ID del estado no válido', 'error');
                    }
                }
            }
        });
    }
}

// Inicializar controlador
window.DriverStatusEditController = new DriverStatusEditController();
window.driverStatusEditController = window.DriverStatusEditController;

console.log('✅ DriverStatusEditController cargado y disponible globalmente');
