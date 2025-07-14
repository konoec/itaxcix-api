/**
 * Edit Configuration Modal Controller
 * Controlador especializado para el modal de edición de configuraciones
 * 
 * @author Sistema
 * @version 1.0.0
 */

class EditConfigurationModalController {
    constructor() {
        this.configurationService = new ConfigurationService();
        this.modalId = 'editConfigurationModal';
        this.formId = 'editConfigurationForm';
        this.isSubmitting = false;
        this.currentConfigId = null;
        this.parentController = null;
        
        this.initializeModal();
        console.log('✏️ EditConfigurationModalController inicializado');
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
        console.log('🏗️ Inicializando modal de edición...');
        
        // Crear el modal si no existe
        if (!document.getElementById(this.modalId)) {
            console.log('🏗️ Creando HTML del modal...');
            this.createModalHTML();
            
            // Esperar un momento para que el DOM se actualice antes de configurar eventos
            setTimeout(() => {
                console.log('🏗️ Configurando eventos (diferido)...');
                this.bindEvents();
                console.log('✅ Modal de edición inicializado correctamente');
            }, 100);
        } else {
            console.log('🏗️ Modal ya existe en el DOM');
            this.bindEvents();
            console.log('✅ Modal de edición inicializado correctamente');
        }
    }

    /**
     * Crea el HTML del modal
     */
    createModalHTML() {
        console.log('🏗️ Generando HTML del modal...');
        
        const modalHTML = `
            <div class="modal modal-blur fade" id="${this.modalId}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1"/>
                                    <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z"/>
                                    <path d="M16 5l3 3"/>
                                </svg>
                                Editar Configuración
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Loading State -->
                            <div id="edit-config-loading" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                                <p class="mt-2 text-muted">Cargando configuración...</p>
                            </div>

                            <!-- Form Content -->
                            <div id="edit-config-content" style="display: none;">
                                <form id="${this.formId}" class="needs-validation" novalidate>
                                    <input type="hidden" id="editConfigId" name="id">
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="editConfigKey" class="form-label">
                                                    Clave de Configuración
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="editConfigKey" 
                                                       name="key" 
                                                       placeholder="Ej: app.timeout" 
                                                       required
                                                       maxlength="100"
                                                       pattern="^[a-zA-Z0-9._\\-]+$">
                                                <div class="invalid-feedback">
                                                    La clave es requerida y solo puede contener letras, números, puntos, guiones y guiones bajos.
                                                </div>
                                                <div class="form-text">
                                                    Formato: app.configuracion, system.valor, etc.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="editConfigActive" class="form-label">Estado</label>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" 
                                                           type="checkbox" 
                                                           id="editConfigActive" 
                                                           name="active">
                                                    <label class="form-check-label" for="editConfigActive">
                                                        <span class="status-text">Activo</span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editConfigValue" class="form-label">
                                            Valor de Configuración
                                            <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" 
                                                  id="editConfigValue" 
                                                  name="value" 
                                                  rows="4" 
                                                  placeholder="Ingrese el valor de la configuración"
                                                  required
                                                  maxlength="500"></textarea>
                                        <div class="invalid-feedback">
                                            El valor es requerido.
                                        </div>
                                        <div class="form-text">
                                            Máximo 500 caracteres. Puede ser texto, número, JSON, etc.
                                        </div>
                                    </div>

                                    <!-- Info adicional -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Descripción</label>
                                                <p id="editConfigDescription" class="form-control-plaintext text-muted">
                                                    -
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Categoría</label>
                                                <p id="editConfigCategory" class="form-control-plaintext">
                                                    <span class="badge bg-azure-lt">-</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Error State -->
                            <div id="edit-config-error" style="display: none;" class="text-center py-4">
                                <div class="text-danger mb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg" width="48" height="48" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M12 9v2m0 4v.01"/>
                                        <path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75"/>
                                    </svg>
                                </div>
                                <h4 class="text-danger">Error al cargar</h4>
                                <p id="edit-config-error-message" class="text-muted">No se pudo cargar la configuración</p>
                                <button type="button" class="btn btn-outline-primary" id="retry-load-config">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4"/>
                                        <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4"/>
                                    </svg>
                                    Reintentar
                                </button>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="w-100">
                                <div class="row">
                                    <div class="col">
                                        <button type="button" class="btn w-100" data-bs-dismiss="modal">
                                            Cancelar
                                        </button>
                                    </div>
                                    <div class="col">
                                        <button type="submit" 
                                                form="${this.formId}"
                                                class="btn btn-primary w-100" 
                                                id="edit-config-submit-btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M5 12l5 5l10 -10"/>
                                            </svg>
                                            Actualizar configuración
                                        </button>
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
        
        const form = document.getElementById(this.formId);
        const modal = document.getElementById(this.modalId);
        const retryBtn = document.getElementById('retry-load-config');
        const activeSwitch = document.getElementById('editConfigActive');

        console.log('🔗 Elementos encontrados:', {
            form: !!form,
            modal: !!modal,
            retryBtn: !!retryBtn,
            activeSwitch: !!activeSwitch
        });

        // Submit del formulario
        if (form) {
            form.addEventListener('submit', (e) => {
                console.log('📤 Submit del formulario de edición');
                e.preventDefault();
                this.handleSubmit();
            });
            console.log('✅ Evento submit configurado');
        }

        // Reset al cerrar modal
        if (modal) {
            modal.addEventListener('hidden.bs.modal', () => {
                console.log('🔄 Modal cerrado, reseteando...');
                this.resetModal();
            });
            console.log('✅ Evento hidden.bs.modal configurado');
        }

        // Retry button
        if (retryBtn) {
            retryBtn.addEventListener('click', () => {
                console.log('🔄 Retry button clicked');
                if (this.currentConfigId) {
                    this.loadConfiguration(this.currentConfigId);
                }
            });
            console.log('✅ Evento retry configurado');
        }

        // Switch state change
        if (activeSwitch) {
            activeSwitch.addEventListener('change', (e) => {
                const statusText = e.target.closest('.form-check').querySelector('.status-text');
                if (statusText) {
                    statusText.textContent = e.target.checked ? 'Activo' : 'Inactivo';
                }
            });
            console.log('✅ Evento switch configurado');
        }

        console.log('✅ Todos los eventos configurados correctamente');
    }

    /**
     * Abre el modal para editar una configuración
     * @param {number} configId - ID de la configuración a editar
     */
    async openModal(configId) {
        console.log('🚀 Abriendo modal de edición para ID:', configId);
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

            console.log('🌐 Verificando disponibilidad de Bootstrap...');
            console.log('🌐 window.bootstrap:', typeof window.bootstrap);
            console.log('🌐 bootstrap:', typeof bootstrap);

            // Mostrar modal usando diferentes métodos según disponibilidad
            let modal;
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                console.log('✅ Usando bootstrap.Modal');
                modal = new bootstrap.Modal(modalElement);
                modal.show();
            } else if (window.bootstrap && window.bootstrap.Modal) {
                console.log('✅ Usando window.bootstrap.Modal');
                modal = new window.bootstrap.Modal(modalElement);
                modal.show();
            } else {
                console.log('⚠️ Bootstrap no disponible, usando fallback manual');
                // Fallback: usar atributos data de Bootstrap
                modalElement.setAttribute('data-bs-toggle', 'modal');
                modalElement.style.display = 'block';
                modalElement.classList.add('show');
                document.body.classList.add('modal-open');
                
                // Crear backdrop
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                backdrop.id = `${this.modalId}-backdrop`;
                document.body.appendChild(backdrop);
            }

            console.log('✅ Modal mostrado, cargando configuración...');
            
            // Cargar datos
            await this.loadConfiguration(configId);
        } catch (error) {
            console.error('❌ Error opening modal:', error);
            this.showError('Error al abrir el modal de edición');
        }
    }

    /**
     * Carga los datos de la configuración
     * @param {number} configId - ID de la configuración
     */
    async loadConfiguration(configId) {
        try {
            console.log('📥 Cargando configuración ID:', configId);
            this.showLoadingState();

            const response = await this.configurationService.getConfigurationById(configId);
            console.log('📥 Respuesta del servicio:', response);
            
            if (response.success && response.data) {
                // La API devuelve los datos en data.configuration
                const configData = response.data.configuration || response.data;
                console.log('📥 Datos de configuración:', configData);
                
                this.populateForm(configData);
                this.showContentState();
            } else {
                throw new Error(response.message || 'No se encontró la configuración');
            }
        } catch (error) {
            console.error('❌ Error loading configuration:', error);
            this.showErrorState(error.message);
        }
    }

    /**
     * Rellena el formulario con los datos de la configuración
     * @param {Object} config - Datos de la configuración
     */
    populateForm(config) {
        console.log('📝 Rellenando formulario con datos:', config);
        
        // Campos del formulario
        const editConfigId = document.getElementById('editConfigId');
        const editConfigKey = document.getElementById('editConfigKey');
        const editConfigValue = document.getElementById('editConfigValue');
        const editConfigActive = document.getElementById('editConfigActive');
        
        console.log('📝 Elementos del formulario encontrados:', {
            editConfigId: !!editConfigId,
            editConfigKey: !!editConfigKey,
            editConfigValue: !!editConfigValue,
            editConfigActive: !!editConfigActive
        });

        if (editConfigId) editConfigId.value = config.id || '';
        if (editConfigKey) editConfigKey.value = config.key || '';
        if (editConfigValue) editConfigValue.value = config.value || '';
        if (editConfigActive) editConfigActive.checked = config.active === true;

        // Info adicional
        const editConfigDescription = document.getElementById('editConfigDescription');
        const editConfigCategory = document.getElementById('editConfigCategory');
        
        if (editConfigDescription) {
            editConfigDescription.textContent = config.description || 'Sin descripción';
        }
        
        if (editConfigCategory) {
            editConfigCategory.innerHTML = `<span class="badge bg-azure-lt">${config.category || 'Sin categoría'}</span>`;
        }

        // Actualizar texto del switch
        const statusText = document.querySelector('#editConfigActive + label .status-text');
        if (statusText) {
            statusText.textContent = config.active ? 'Activo' : 'Inactivo';
        }
        
        console.log('✅ Formulario rellenado correctamente');
    }

    /**
     * Maneja el envío del formulario
     */
    async handleSubmit() {
        console.log('📤 Iniciando submit del formulario de edición');
        const form = document.getElementById(this.formId);
        
        if (!form.checkValidity()) {
            console.log('❌ Formulario no válido');
            form.classList.add('was-validated');
            return;
        }

        if (this.isSubmitting) {
            console.log('⚠️ Ya se está enviando el formulario');
            return;
        }

        this.isSubmitting = true;
        this.setSubmitButtonLoading(true);

        try {
            const formData = new FormData(form);
            const configData = {
                id: parseInt(formData.get('id')),
                key: formData.get('key').trim(),
                value: formData.get('value').trim(),
                active: formData.has('active')
            };

            console.log('📤 Datos del formulario preparados:', configData);
            console.log('📤 ID de configuración actual:', this.currentConfigId);

            const response = await this.configurationService.updateConfiguration(this.currentConfigId, configData);
            console.log('📤 Respuesta de actualización:', response);

            if (response.success) {
                console.log('✅ Configuración actualizada exitosamente');
                this.showSuccess('Configuración actualizada correctamente');
                this.closeModal();
                
                // Notificar al controlador padre para refrescar la tabla
                if (this.parentController && typeof this.parentController.refreshTable === 'function') {
                    console.log('🔄 Refrescando tabla principal');
                    this.parentController.refreshTable();
                } else {
                    console.log('⚠️ No se pudo refrescar la tabla - controlador padre no disponible');
                }
            } else {
                throw new Error(response.message || 'Error al actualizar la configuración');
            }
        } catch (error) {
            console.error('❌ Error updating configuration:', error);
            this.showError(error.message || 'Error al actualizar la configuración');
        } finally {
            this.isSubmitting = false;
            this.setSubmitButtonLoading(false);
        }
    }

    /**
     * Muestra el estado de carga
     */
    showLoadingState() {
        document.getElementById('edit-config-loading').style.display = 'block';
        document.getElementById('edit-config-content').style.display = 'none';
        document.getElementById('edit-config-error').style.display = 'none';
    }

    /**
     * Muestra el contenido del formulario
     */
    showContentState() {
        document.getElementById('edit-config-loading').style.display = 'none';
        document.getElementById('edit-config-content').style.display = 'block';
        document.getElementById('edit-config-error').style.display = 'none';
    }

    /**
     * Muestra el estado de error
     * @param {string} message - Mensaje de error
     */
    showErrorState(message) {
        document.getElementById('edit-config-loading').style.display = 'none';
        document.getElementById('edit-config-content').style.display = 'none';
        document.getElementById('edit-config-error').style.display = 'block';
        document.getElementById('edit-config-error-message').textContent = message;
    }

    /**
     * Establece el estado de carga del botón de envío
     * @param {boolean} loading - Si está cargando
     */
    setSubmitButtonLoading(loading) {
        const submitBtn = document.getElementById('edit-config-submit-btn');
        if (!submitBtn) return;

        if (loading) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Actualizando...
            `;
        } else {
            submitBtn.disabled = false;
            submitBtn.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M5 12l5 5l10 -10"/>
                </svg>
                Actualizar configuración
            `;
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
        const form = document.getElementById(this.formId);
        if (form) {
            form.reset();
            form.classList.remove('was-validated');
        }

        this.currentConfigId = null;
        this.isSubmitting = false;
        this.setSubmitButtonLoading(false);
        this.showLoadingState();
    }

    /**
     * Muestra mensaje de éxito
     * @param {string} message - Mensaje a mostrar
     */
    showSuccess(message) {
        if (window.globalToast) {
            window.globalToast.show(message, 'success');
        }
    }

    /**
     * Muestra mensaje de error
     * @param {string} message - Mensaje a mostrar
     */
    showError(message) {
        if (window.globalToast) {
            window.globalToast.show(message, 'error');
        }
    }
}

// Exportar controlador
window.EditConfigurationModalController = EditConfigurationModalController;
