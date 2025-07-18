/**
 * Delete Company Modal Controller
 * Controlador para el modal de confirmación de eliminación de empresas
 */

class DeleteCompanyModalController {
    constructor(options = {}) {
        this.modalId = 'delete-company-modal';
        this.companyService = new CompanyService();
        this.currentCompanyData = null;
        
        // Callbacks
        this.onCompanyDeleted = options.onCompanyDeleted || (() => {});
        
        console.log('🗑️ Inicializando DeleteCompanyModalController...');
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
        
        console.log('✅ Modal de eliminación de empresa inicializado');
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
                            
                            <div id="delete-company-content">
                                <div class="card card-sm">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="bg-blue text-white avatar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <line x1="3" y1="21" x2="21" y2="21"/>
                                                        <line x1="9" y1="8" x2="10" y2="8"/>
                                                        <line x1="9" y1="12" x2="10" y2="12"/>
                                                        <line x1="9" y1="16" x2="10" y2="16"/>
                                                        <line x1="14" y1="8" x2="15" y2="8"/>
                                                        <line x1="14" y1="12" x2="15" y2="12"/>
                                                        <line x1="14" y1="16" x2="15" y2="16"/>
                                                        <path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16"/>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <div class="font-weight-medium" id="delete-company-name">
                                                    <span class="placeholder col-6"></span>
                                                </div>
                                                <div class="text-muted" id="delete-company-ruc">
                                                    <span class="placeholder col-8"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="delete-company-loading" style="display: none;" class="py-3">
                                <div class="progress progress-sm mb-3">
                                    <div class="progress-bar progress-bar-indeterminate bg-danger"></div>
                                </div>
                                <div class="text-muted">Eliminando empresa...</div>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <div class="w-100">
                                <div class="row">
                                    <div class="col">
                                        <a href="#" class="btn w-100" data-bs-dismiss="modal" id="delete-company-cancel-btn">
                                            Cancelar
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a href="#" class="btn btn-danger w-100" id="delete-company-confirm-btn">
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
        const confirmBtn = document.getElementById('delete-company-confirm-btn');

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
     * Abre el modal para eliminar una empresa
     */
    async openModal(companyId) {
        if (!companyId) {
            console.error('❌ ID de empresa no proporcionado');
            return;
        }

        try {
            await this.loadCompanyData(companyId);
            this.showModal();
        } catch (error) {
            console.error('❌ Error abriendo modal:', error);
            this.showError('Error al abrir el modal de eliminación');
        }
    }

    /**
     * Abre el modal con datos ya disponibles
     */
    openModalWithData(companyData) {
        if (!companyData) {
            console.error('❌ Datos de empresa no proporcionados');
            return;
        }

        try {
            this.currentCompanyData = companyData;
            this.populateModal(companyData);
            this.showModal();
            console.log('✅ Modal abierto con datos existentes');
        } catch (error) {
            console.error('❌ Error abriendo modal con datos:', error);
            this.showError('Error al abrir el modal de eliminación');
        }
    }

    /**
     * Carga los datos de la empresa
     */
    async loadCompanyData(companyId) {
        try {
            const response = await this.companyService.getCompanyById(companyId);
            
            if (response.success && response.data) {
                const companyData = response.data.company || response.data;
                this.currentCompanyData = companyData;
                this.populateModal(companyData);
            } else {
                throw new Error(response.message || 'No se encontró la empresa');
            }
        } catch (error) {
            console.error('❌ Error loading company data:', error);
            this.showError('Error al cargar los datos de la empresa');
        }
    }

    /**
     * Rellena el modal con los datos de la empresa
     */
    populateModal(companyData) {
        console.log('📝 Rellenando modal con datos de empresa:', companyData);
        
        const nameElement = document.getElementById('delete-company-name');
        const rucElement = document.getElementById('delete-company-ruc');
        
        console.log('🔍 Elementos encontrados:', {
            nameElement: !!nameElement,
            rucElement: !!rucElement
        });
        
        if (nameElement) {
            const companyName = companyData.name || 'Empresa desconocida';
            nameElement.innerHTML = `<strong class="text-danger">${companyName}</strong>`;
            console.log('✅ Nombre de empresa establecido:', companyName);
        } else {
            console.error('❌ Elemento delete-company-name no encontrado');
        }
        
        if (rucElement) {
            const ruc = companyData.ruc || 'Sin RUC';
            rucElement.innerHTML = `<small class="text-muted">RUC: <code>${ruc}</code></small>`;
            console.log('✅ RUC de empresa establecido:', ruc);
        } else {
            console.error('❌ Elemento delete-company-ruc no encontrado');
        }
        
        console.log('✅ Modal rellenado correctamente');
    }

    /**
     * Muestra el modal
     */
    showModal() {
        const modalElement = document.getElementById(this.modalId);
        
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        } else {
            modalElement.style.display = 'block';
            modalElement.classList.add('show');
        }
    }

    /**
     * Oculta el modal
     */
    hideModal() {
        const modalElement = document.getElementById(this.modalId);
        
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
        } else {
            modalElement.style.display = 'none';
            modalElement.classList.remove('show');
        }
    }

    /**
     * Maneja la eliminación de la empresa
     */
    async handleDelete() {
        if (!this.currentCompanyData || !this.currentCompanyData.id) {
            console.error('❌ No hay datos de empresa para eliminar');
            this.showError('No se puede eliminar: datos de empresa no disponibles');
            return;
        }

        const companyId = this.currentCompanyData.id;
        console.log('🗑️ Iniciando eliminación de empresa ID:', companyId);

        try {
            this.setLoadingState(true);
            
            console.log('📡 Llamando al servicio de eliminación...');
            const response = await this.companyService.delete(companyId);
            console.log('📥 Respuesta del servicio:', response);

            if (response && response.success) {
                console.log('✅ Empresa eliminada correctamente');
                this.hideModal();
                this.showSuccess(`Empresa "${this.currentCompanyData.name}" eliminada correctamente`);
                this.onCompanyDeleted(this.currentCompanyData);
            } else {
                const errorMsg = response?.message || 'Error desconocido al eliminar la empresa';
                console.error('❌ Error en respuesta del servicio:', errorMsg);
                throw new Error(errorMsg);
            }

        } catch (error) {
            console.error('❌ Error eliminando empresa:', error);
            this.showError(`Error al eliminar la empresa: ${error.message}`);
        } finally {
            this.setLoadingState(false);
            console.log('🔄 Estado de loading desactivado');
        }
    }

    /**
     * Establece el estado de carga
     */
    setLoadingState(loading) {
        const content = document.getElementById('delete-company-content');
        const loadingElement = document.getElementById('delete-company-loading');
        const confirmBtn = document.getElementById('delete-company-confirm-btn');
        const cancelBtn = document.getElementById('delete-company-cancel-btn');

        if (loading) {
            if (content) content.style.display = 'none';
            if (loadingElement) loadingElement.style.display = 'block';
            
            if (confirmBtn) {
                confirmBtn.disabled = true;
                confirmBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                    Eliminando...
                `;
            }
            if (cancelBtn) cancelBtn.disabled = true;
            
        } else {
            if (content) content.style.display = 'block';
            if (loadingElement) loadingElement.style.display = 'none';
            
            if (confirmBtn) {
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M4 7l16 0"/>
                        <path d="M10 11l0 6"/>
                        <path d="M14 11l0 6"/>
                        <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                        <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                    </svg>
                    Eliminar
                `;
            }
            if (cancelBtn) cancelBtn.disabled = false;
        }
    }

    /**
     * Resetea el modal
     */
    resetModal() {
        this.currentCompanyData = null;
        this.setLoadingState(false);
        
        const nameElement = document.getElementById('delete-company-name');
        const rucElement = document.getElementById('delete-company-ruc');
        
        if (nameElement) {
            nameElement.innerHTML = '<span class="placeholder col-6"></span>';
        }
        if (rucElement) {
            rucElement.innerHTML = '<span class="placeholder col-8"></span>';
        }
    }

    /**
     * Muestra mensaje de éxito
     */
    showSuccess(message) {
        if (window.globalToast) {
            window.globalToast.show(message, 'success');
        } else if (window.showRecoveryToast) {
            window.showRecoveryToast(message, 'success');
        }
    }

    /**
     * Muestra mensaje de error
     */
    showError(message) {
        if (window.globalToast) {
            window.globalToast.show(message, 'error');
        } else if (window.showRecoveryToast) {
            window.showRecoveryToast(message, 'error');
        }
    }
}

// Exportar controlador
window.DeleteCompanyModalController = DeleteCompanyModalController;
