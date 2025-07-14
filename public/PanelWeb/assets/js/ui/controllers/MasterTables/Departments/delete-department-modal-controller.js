/**
 * Delete Department Modal Controller
 * Controlador para el modal de confirmación de eliminación de departamentos
 */

class DeleteDepartmentModalController {
    constructor(options = {}) {
        this.modalId = 'delete-department-modal';
        this.departmentDeleteService = new DepartmentDeleteService();
        this.currentDepartmentData = null;
        
        // Callbacks
        this.onDepartmentDeleted = options.onDepartmentDeleted || (() => {});
        
        console.log('🗑️ Inicializando DeleteDepartmentModalController...');
        this.init();
    }

    /**
     * Inicializa el controlador
     */
    init() {
        // Crear el modal si no existe
        if (!document.getElementById(this.modalId)) {
            this.createModalHTML();
        }
        
        // Configurar eventos
        this.bindEvents();
        
        console.log('✅ Modal de eliminación de departamento inicializado');
    }

    /**
     * Crea el HTML del modal con Tabler
     */
    createModalHTML() {
        const modalHTML = `
            <div class="modal modal-blur fade" id="${this.modalId}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="modal-status bg-danger"></div>
                        <div class="modal-body text-center py-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 9v2m0 4v.01"/>
                                <path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75"/>
                            </svg>
                            
                            <h3>¿Está seguro de eliminar?</h3>
                            
                            <div id="delete-department-content">
                                <div class="card card-sm">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="bg-primary text-white avatar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M3 7v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2 -2v-10"/>
                                                        <path d="M3 7l9 6l9 -6"/>
                                                        <path d="M21 7l-9 6l-9 -6h18z"/>
                                                        <path d="M12 3v4"/>
                                                        <path d="M8 7l4 -4l4 4"/>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <div class="font-weight-medium" id="delete-department-name">
                                                    <span class="placeholder col-6"></span>
                                                </div>
                                                <div class="text-muted" id="delete-department-ubigeo">
                                                    <span class="placeholder col-8"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="delete-department-loading" style="display: none;" class="py-3">
                                <div class="progress progress-sm mb-3">
                                    <div class="progress-bar progress-bar-indeterminate bg-danger"></div>
                                </div>
                                <div class="text-muted">Eliminando departamento...</div>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <div class="w-100">
                                <div class="row">
                                    <div class="col">
                                        <a href="#" class="btn w-100" data-bs-dismiss="modal" id="delete-department-cancel-btn">
                                            Cancelar
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a href="#" class="btn btn-danger w-100" id="delete-department-confirm-btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M4 7l16 0"/>
                                                <path d="M10 11l0 6"/>
                                                <path d="M14 11l0 6"/>
                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                                            </svg>
                                            Eliminar
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHTML);
    }

    /**
     * Configura los eventos
     */
    bindEvents() {
        const modal = document.getElementById(this.modalId);
        const confirmBtn = document.getElementById('delete-department-confirm-btn');

        if (confirmBtn) {
            confirmBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleDelete();
            });
        }

        if (modal) {
            modal.addEventListener('hidden.bs.modal', () => {
                this.resetModal();
            });
        }
    }

    /**
     * Abre el modal para eliminar un departamento
     */
    async openModal(departmentId) {
        if (!departmentId) {
            console.error('❌ ID de departamento no proporcionado');
            return;
        }

        try {
            await this.loadDepartmentData(departmentId);
            this.showModal();
        } catch (error) {
            console.error('❌ Error abriendo modal:', error);
            this.showError('Error al abrir el modal de eliminación');
        }
    }

    /**
     * Abre el modal con datos ya disponibles
     */
    openModalWithData(departmentData) {
        if (!departmentData) {
            console.error('❌ Datos de departamento no proporcionados');
            return;
        }

        try {
            this.currentDepartmentData = departmentData;
            this.populateModal(departmentData);
            this.showModal();
            console.log('✅ Modal abierto con datos existentes');
        } catch (error) {
            console.error('❌ Error abriendo modal con datos:', error);
            this.showError('Error al abrir el modal de eliminación');
        }
    }

    /**
     * Carga los datos del departamento
     */
    async loadDepartmentData(departmentId) {
        try {
            // Asumiendo que tenemos un servicio que puede obtener un departamento por ID
            // Si no existe, usaremos datos básicos
            const departmentData = {
                id: departmentId,
                name: `Departamento ${departmentId}`,
                ubigeo: '--'
            };
            
            this.currentDepartmentData = departmentData;
            this.populateModal(departmentData);
        } catch (error) {
            console.error('❌ Error loading department data:', error);
            this.showError('Error al cargar los datos del departamento');
            throw error;
        }
    }

    /**
     * Puebla el modal con los datos del departamento
     */
    populateModal(departmentData) {
        const nameElement = document.getElementById('delete-department-name');
        const ubigeoElement = document.getElementById('delete-department-ubigeo');

        if (nameElement) {
            nameElement.textContent = departmentData.name || 'Departamento sin nombre';
            nameElement.classList.remove('placeholder');
        }

        if (ubigeoElement) {
            ubigeoElement.textContent = `Ubigeo: ${departmentData.ubigeo || '--'}`;
            ubigeoElement.classList.remove('placeholder');
        }
    }

    /**
     * Muestra el modal
     */
    showModal() {
        const modal = document.getElementById(this.modalId);
        if (modal) {
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        }
    }

    /**
     * Maneja la eliminación del departamento
     */
    async handleDelete() {
        if (!this.currentDepartmentData) {
            console.error('❌ No hay datos de departamento para eliminar');
            return;
        }

        try {
            this.showLoading(true);
            
            // Validar antes de eliminar
            const validation = await this.departmentDeleteService.validateDeletion(this.currentDepartmentData.id);
            if (!validation.canDelete) {
                throw new Error(validation.reason);
            }

            // Realizar la eliminación
            const result = await this.departmentDeleteService.deleteDepartment(this.currentDepartmentData.id);

            if (result.success) {
                console.log('✅ Departamento eliminado exitosamente');
                
                // Mostrar notificación de éxito
                this.showSuccess('Departamento eliminado correctamente');
                
                // Cerrar modal
                this.closeModal();
                
                // Ejecutar callback
                this.onDepartmentDeleted(this.currentDepartmentData.id, result);
                
            } else {
                throw new Error(result.message || 'Error al eliminar el departamento');
            }

        } catch (error) {
            console.error('❌ Error eliminando departamento:', error);
            this.showError(error.message || 'Error al eliminar el departamento');
        } finally {
            this.showLoading(false);
        }
    }

    /**
     * Muestra/oculta el estado de carga
     */
    showLoading(show) {
        const content = document.getElementById('delete-department-content');
        const loading = document.getElementById('delete-department-loading');
        const confirmBtn = document.getElementById('delete-department-confirm-btn');
        const cancelBtn = document.getElementById('delete-department-cancel-btn');

        if (content) content.style.display = show ? 'none' : 'block';
        if (loading) loading.style.display = show ? 'block' : 'none';
        if (confirmBtn) confirmBtn.style.display = show ? 'none' : 'block';
        if (cancelBtn) cancelBtn.textContent = show ? 'Esperar...' : 'Cancelar';
        if (cancelBtn) cancelBtn.disabled = show;
    }

    /**
     * Cierra el modal
     */
    closeModal() {
        const modal = document.getElementById(this.modalId);
        if (modal) {
            const bsModal = bootstrap.Modal.getInstance(modal);
            if (bsModal) {
                bsModal.hide();
            }
        }
    }

    /**
     * Resetea el modal a su estado inicial
     */
    resetModal() {
        this.currentDepartmentData = null;
        this.showLoading(false);
        
        // Resetear placeholders
        const nameElement = document.getElementById('delete-department-name');
        const ubigeoElement = document.getElementById('delete-department-ubigeo');
        
        if (nameElement) {
            nameElement.innerHTML = '<span class="placeholder col-6"></span>';
        }
        
        if (ubigeoElement) {
            ubigeoElement.innerHTML = '<span class="placeholder col-8"></span>';
        }
    }

    /**
     * Muestra un mensaje de error
     */
    showError(message) {
        if (typeof window.showRecoveryToast === 'function') {
            window.showRecoveryToast(message, 'error');
        } else {
            alert(`Error: ${message}`);
        }
    }

    /**
     * Muestra un mensaje de éxito
     */
    showSuccess(message) {
        if (typeof window.showRecoveryToast === 'function') {
            window.showRecoveryToast(message, 'success');
        } else {
            alert(`Éxito: ${message}`);
        }
    }
}

// Hacer el controlador disponible globalmente
window.DeleteDepartmentModalController = DeleteDepartmentModalController;

console.log('✅ DeleteDepartmentModalController cargado correctamente');
