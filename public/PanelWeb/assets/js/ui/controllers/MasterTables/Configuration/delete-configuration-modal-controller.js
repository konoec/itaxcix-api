/**
 * Delete Configuration Modal Controller
 * Controlador especializado para el modal de eliminación de configuraciones
 * 
 * @author Sistema
 * @version 1.0.0
 */

class DeleteConfigurationModalController {
    constructor() {
        this.configurationService = new ConfigurationService();
        this.modalId = 'deleteConfigurationModal';
        this.isDeleting = false;
        this.currentConfigId = null;
        this.currentConfigData = null;
        this.parentController = null;
        
        this.initializeModal();
        console.log('🗑️ DeleteConfigurationModalController inicializado');
    }

    /**
     * Establece el controlador padre para comunicación
     * @param {Object} controller - Controlador padre
     */
    setParentController(controller) {
        this.parentController = controller;
    }

    /**
     * Inicializa el modal y sus event listeners
     */
    initializeModal() {
        console.log('🏗️ Inicializando modal de eliminación...');
        
        // Crear el modal si no existe
        if (!document.getElementById(this.modalId)) {
            console.log('🏗️ Creando HTML del modal...');
            this.createModalHTML();
            
            // Esperar un momento para que el DOM se actualice antes de configurar eventos
            setTimeout(() => {
                console.log('🏗️ Configurando eventos (diferido)...');
                this.bindEvents();
                console.log('✅ Modal de eliminación inicializado correctamente');
            }, 100);
        } else {
            console.log('🏗️ Modal ya existe en el DOM');
            this.bindEvents();
            console.log('✅ Modal de eliminación inicializado correctamente');
        }
    }

    /**
     * Crea el HTML del modal
     */
    createModalHTML() {
        console.log('🏗️ Generando HTML del modal con Tabler...');
        
        const modalHTML = `
            <div class="modal modal-blur fade" id="${this.modalId}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-sm modal-dialog-centered" role="document" style="max-width: 350px;">
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
                            
                            <div id="delete-config-content">
                                <div class="card card-sm">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span class="bg-primary text-white avatar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z"/>
                                                        <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"/>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <div class="font-weight-medium" id="delete-config-key">
                                                    <span class="placeholder col-6"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Loading State -->
                            <div id="delete-config-loading" style="display: none;" class="py-3">
                                <div class="progress progress-sm mb-3">
                                    <div class="progress-bar progress-bar-indeterminate bg-danger"></div>
                                </div>
                                <div class="text-muted">Eliminando configuración...</div>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <div class="w-100">
                                <div class="row">
                                    <div class="col">
                                        <a href="#" class="btn w-100" data-bs-dismiss="modal" id="delete-config-cancel-btn">
                                            Cancelar
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a href="#" class="btn btn-danger w-100" id="delete-config-confirm-btn">
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

        // Agregar el modal al DOM
        console.log('🏗️ Agregando modal al DOM...');
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        console.log('✅ Modal agregado al DOM correctamente');
    }

    /**
     * Configura los event listeners
     */
    bindEvents() {
        console.log('🔗 Configurando eventos del modal...');
        
        const modal = document.getElementById(this.modalId);
        const confirmBtn = document.getElementById('delete-config-confirm-btn');
        const cancelBtn = document.getElementById('delete-config-cancel-btn');

        console.log('🔗 Elementos encontrados:', {
            modal: !!modal,
            confirmBtn: !!confirmBtn,
            cancelBtn: !!cancelBtn
        });

        // Confirmar eliminación
        if (confirmBtn) {
            confirmBtn.addEventListener('click', () => {
                console.log('🗑️ Confirmar eliminación clicked');
                this.handleDelete();
            });
            console.log('✅ Evento confirm configurado');
        }

        // Reset al cerrar modal
        if (modal) {
            modal.addEventListener('hidden.bs.modal', () => {
                console.log('🔄 Modal cerrado, reseteando...');
                this.resetModal();
            });
            console.log('✅ Evento hidden.bs.modal configurado');
        }

        console.log('✅ Todos los eventos configurados correctamente');
    }

    /**
     * Abre el modal para eliminar una configuración
     * @param {number} configId - ID de la configuración a eliminar
     * @param {Object} configData - Datos de la configuración (opcional)
     */
    async openModal(configId, configData = null) {
        console.log('🚀 Abriendo modal de eliminación para ID:', configId);
        
        // Si ya tenemos datos, usar el método más directo
        if (configData) {
            this.openModalWithData(configData);
            return;
        }

        this.currentConfigId = configId;
        
        try {
            // Asegurar que el modal existe en el DOM
            const modalElement = document.getElementById(this.modalId);
            console.log('🔍 Modal element encontrado:', !!modalElement);
            
            if (!modalElement) {
                console.error(`❌ Modal element with ID ${this.modalId} not found`);
                this.showError('Error: No se pudo encontrar el modal');
                return;
            }

            // Como último recurso, intentar cargar datos del servidor
            console.log('📥 Cargando datos de configuración desde servidor...');
            await this.loadConfigurationData(configId);
            this.showModal();

        } catch (error) {
            console.error('❌ Error opening modal:', error);
            this.showError('Error al abrir el modal de eliminación');
        }
    }

    /**
     * Carga los datos de la configuración para mostrar en el modal
     * @param {number} configId - ID de la configuración
     */
    async loadConfigurationData(configId) {
        try {
            const response = await this.configurationService.getConfigurationById(configId);
            
            if (response.success && response.data) {
                const configData = response.data.configuration || response.data;
                this.currentConfigData = configData;
                this.populateModal(configData);
            } else {
                throw new Error(response.message || 'No se encontró la configuración');
            }
        } catch (error) {
            console.error('❌ Error loading configuration data:', error);
            this.showError('Error al cargar los datos de la configuración');
        }
    }

    /**
     * Rellena el modal con los datos de la configuración
     * @param {Object} configData - Datos de la configuración
     */
    populateModal(configData) {
        console.log('📝 Rellenando modal con datos:', configData);
        
        const keyElement = document.getElementById('delete-config-key');
        
        if (keyElement) {
            // Remover placeholder y agregar contenido real
            keyElement.innerHTML = `<code class="text-danger">${configData.key || 'Configuración desconocida'}</code>`;
        }
        
        console.log('✅ Modal rellenado correctamente con componentes Tabler');
    }

    /**
     * Muestra el modal
     */
    showModal() {
        const modalElement = document.getElementById(this.modalId);
        
        // Mostrar modal usando diferentes métodos según disponibilidad
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            console.log('✅ Usando bootstrap.Modal');
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        } else if (window.bootstrap && window.bootstrap.Modal) {
            console.log('✅ Usando window.bootstrap.Modal');
            const modal = new window.bootstrap.Modal(modalElement);
            modal.show();
        } else {
            console.log('⚠️ Bootstrap no disponible, usando fallback manual');
            modalElement.style.display = 'block';
            modalElement.classList.add('show');
            document.body.classList.add('modal-open');
            
            // Crear backdrop
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.id = `${this.modalId}-backdrop`;
            document.body.appendChild(backdrop);
        }
    }

    /**
     * Maneja la eliminación de la configuración
     */
    async handleDelete() {
        if (this.isDeleting) {
            console.log('⚠️ Ya se está eliminando la configuración');
            return;
        }

        console.log('🗑️ Iniciando eliminación de configuración ID:', this.currentConfigId);
        
        this.isDeleting = true;
        this.setLoadingState(true);

        try {
            const response = await this.configurationService.deleteConfiguration(this.currentConfigId);
            console.log('🗑️ Respuesta de eliminación:', response);

            if (response.success) {
                console.log('✅ Configuración eliminada exitosamente');
                this.showSuccess('Configuración eliminada correctamente');
                this.closeModal();
                
                // Notificar al controlador padre para refrescar la tabla
                if (this.parentController && typeof this.parentController.refreshTable === 'function') {
                    console.log('🔄 Refrescando tabla principal');
                    this.parentController.refreshTable();
                } else {
                    console.log('⚠️ No se pudo refrescar la tabla - controlador padre no disponible');
                }
            } else {
                throw new Error(response.message || 'Error al eliminar la configuración');
            }
        } catch (error) {
            console.error('❌ Error deleting configuration:', error);
            this.showError(error.message || 'Error al eliminar la configuración');
        } finally {
            this.isDeleting = false;
            this.setLoadingState(false);
        }
    }

    /**
     * Establece el estado de carga del modal
     * @param {boolean} loading - Si está cargando
     */
    setLoadingState(loading) {
        const content = document.getElementById('delete-config-content');
        const loadingElement = document.getElementById('delete-config-loading');
        const confirmBtn = document.getElementById('delete-config-confirm-btn');
        const cancelBtn = document.getElementById('delete-config-cancel-btn');

        if (loading) {
            if (content) content.style.display = 'none';
            if (loadingElement) loadingElement.style.display = 'block';
            if (confirmBtn) confirmBtn.disabled = true;
            if (cancelBtn) cancelBtn.disabled = true;
        } else {
            if (content) content.style.display = 'block';
            if (loadingElement) loadingElement.style.display = 'none';
            if (confirmBtn) confirmBtn.disabled = false;
            if (cancelBtn) cancelBtn.disabled = false;
        }
    }

    /**
     * Cierra el modal
     */
    closeModal() {
        try {
            const modalElement = document.getElementById(this.modalId);
            if (!modalElement) return;

            // Intentar cerrar usando Bootstrap
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                    return;
                }
            }

            if (window.bootstrap && window.bootstrap.Modal) {
                const modal = window.bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                    return;
                }
            }

            // Fallback: cerrar manualmente
            modalElement.style.display = 'none';
            modalElement.classList.remove('show');
            document.body.classList.remove('modal-open');
            
            // Remover backdrop
            const backdrop = document.getElementById(`${this.modalId}-backdrop`);
            if (backdrop) {
                backdrop.remove();
            }
        } catch (error) {
            console.error('Error closing modal:', error);
        }
    }

    /**
     * Resetea el modal a su estado inicial
     */
    resetModal() {
        this.currentConfigId = null;
        this.currentConfigData = null;
        this.isDeleting = false;
        this.setLoadingState(false);
        
        // Limpiar contenido
        const keyElement = document.getElementById('delete-config-key');
        
        if (keyElement) keyElement.textContent = '-';
    }

    /**
     * Muestra mensaje de éxito
     * @param {string} message - Mensaje a mostrar
     */
    showSuccess(message) {
        console.log('✅ Mostrando mensaje de éxito:', message);
        
        // Intentar múltiples formas de mostrar el toast
        if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
            console.log('📢 Usando window.GlobalToast.show');
            window.GlobalToast.show(message, 'success');
        } else if (window.showRecoveryToast && typeof window.showRecoveryToast === 'function') {
            console.log('📢 Usando window.showRecoveryToast');
            window.showRecoveryToast(message, 'success');
        } else if (window.globalToast && typeof window.globalToast.show === 'function') {
            console.log('📢 Usando window.globalToast.show');
            window.globalToast.show(message, 'success');
        } else {
            console.warn('⚠️ Sistema de toast no disponible, usando alert como fallback');
            alert(`✅ ${message}`);
        }
    }

    /**
     * Muestra mensaje de error
     * @param {string} message - Mensaje a mostrar
     */
    showError(message) {
        console.log('❌ Mostrando mensaje de error:', message);
        
        // Intentar múltiples formas de mostrar el toast
        if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
            console.log('📢 Usando window.GlobalToast.show');
            window.GlobalToast.show(message, 'error');
        } else if (window.showRecoveryToast && typeof window.showRecoveryToast === 'function') {
            console.log('📢 Usando window.showRecoveryToast');
            window.showRecoveryToast(message, 'error');
        } else if (window.globalToast && typeof window.globalToast.show === 'function') {
            console.log('📢 Usando window.globalToast.show');
            window.globalToast.show(message, 'error');
        } else {
            console.warn('⚠️ Sistema de toast no disponible, usando alert como fallback');
            alert(`❌ ${message}`);
        }
    }

    /**
     * Abre el modal con datos de configuración pasados directamente
     */
    openModalWithData(configData) {
        console.log('🗑️ Abriendo modal de eliminación con datos:', configData);
        
        if (!configData) {
            console.error('❌ No se proporcionaron datos de configuración');
            this.showError('Error: No se encontraron datos de la configuración');
            return;
        }

        try {
            // Asegurar que el modal existe en el DOM
            const modalElement = document.getElementById(this.modalId);
            console.log('🔍 Modal element encontrado:', !!modalElement);
            
            if (!modalElement) {
                console.error(`❌ Modal element with ID ${this.modalId} not found`);
                this.showError('Error: No se pudo encontrar el modal');
                return;
            }

            // Guardar los datos para uso posterior
            this.currentConfigId = configData.id;
            this.currentConfigData = configData;
            
            // Rellenar el modal con los datos y mostrarlo
            this.populateModal(configData);
            this.showModal();

        } catch (error) {
            console.error('❌ Error opening modal with data:', error);
            this.showError('Error al abrir el modal de eliminación');
        }
    }
}

// Exportar controlador
window.DeleteConfigurationModalController = DeleteConfigurationModalController;
