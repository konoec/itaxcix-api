/**
 * Create Configuration Modal Controller
 * Controlador especializado para el modal de creación de configuraciones
 * 
 * @author Sistema
 * @version 1.0.0
 */

class CreateConfigurationModalController {
    constructor() {
        this.createService = new CreateConfigurationService();
        this.modalId = 'createConfigurationModal';
        this.formId = 'createConfigurationForm';
        this.isSubmitting = false;
        
        this.initializeModal();
        console.log('➕ CreateConfigurationModalController inicializado');
    }

    /**
     * Inicializa el modal y sus event listeners
     */
    initializeModal() {
        // Crear el modal si no existe
        if (!document.getElementById(this.modalId)) {
            this.createModalHTML();
        }

        this.bindEvents();
    }

    /**
     * Crea el HTML del modal
     */
    createModalHTML() {
        const modalHTML = `
            <div class="modal modal-blur fade" id="${this.modalId}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-settings-plus me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M12.5 21c-.329 .032 -.676 -.008 -1.058 -.152a2.999 2.999 0 0 1 -1.902 -2.298l-.355 -2.594a12.688 12.688 0 0 1 -1.536 -.885l-2.318 1.336a2.999 2.999 0 0 1 -3.582 -.73l-.5 -.865a3 3 0 0 1 .73 -3.583l2.318 -1.336a12.686 12.686 0 0 1 0 -1.77l-2.318 -1.336a2.999 2.999 0 0 1 -.73 -3.583l.5 -.865a3 3 0 0 1 3.582 -.73l2.318 1.336a12.688 12.688 0 0 1 1.536 -.885l.355 -2.594a2.999 2.999 0 0 1 2.96 -2.45h1a3 3 0 0 1 2.96 2.45l.355 2.594c.532 .269 1.034 .574 1.536 .885l2.318 -1.336a2.999 2.999 0 0 1 3.582 .73l.5 .865a3 3 0 0 1 -.73 3.583l-2.318 1.336a12.686 12.686 0 0 1 0 1.77l2.318 1.336a2.999 2.999 0 0 1 .73 3.583l-.5 .865a3 3 0 0 1 -3.582 .73l-2.318 -1.336a12.688 12.688 0 0 1 -1.536 .885l-.355 2.594a2.999 2.999 0 0 1 -2.96 2.45"/>
                                    <path d="M16 19h6"/>
                                    <path d="M19 16v6"/>
                                </svg>
                                Nueva Configuración
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                        </div>
                        <div class="modal-body">
                            <form id="${this.formId}" class="needs-validation" novalidate>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="configKey" class="form-label">
                                                Clave de Configuración
                                                <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="configKey" 
                                                   name="key" 
                                                   placeholder="Ej: app.timeout" 
                                                   required
                                                   maxlength="100"
                                                   pattern="^[a-zA-Z0-9._-]+$">
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
                                            <label for="configActive" class="form-label">Estado</label>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       id="configActive" 
                                                       name="active" 
                                                       checked>
                                                <label class="form-check-label" for="configActive">
                                                    <span class="status-text">Activo</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="configValue" class="form-label">
                                        Valor de Configuración
                                        <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" 
                                              id="configValue" 
                                              name="value" 
                                              rows="3" 
                                              placeholder="Ingrese el valor de la configuración..."
                                              required
                                              maxlength="1000"></textarea>
                                    <div class="invalid-feedback">
                                        El valor es requerido.
                                    </div>
                                    <div class="form-text">
                                        <span class="char-count">0/1000 caracteres</span>
                                    </div>
                                </div>
                                
                                <!-- Preview Card -->
                                <div class="card card-sm bg-light d-none" id="configPreview">
                                    <div class="card-header">
                                        <h3 class="card-title">Vista Previa</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <strong>Clave:</strong>
                                                <div class="text-muted" id="previewKey">-</div>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Valor:</strong>
                                                <div class="text-muted" id="previewValue">-</div>
                                            </div>
                                            <div class="col-md-2">
                                                <strong>Estado:</strong>
                                                <div>
                                                    <span class="badge" id="previewStatus">-</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M18 6l-12 12"/>
                                    <path d="M6 6l12 12"/>
                                </svg>
                                Cancelar
                            </button>
                            <button type="button" class="btn btn-primary" id="createConfigBtn">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check me-1" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 12l5 5l10 -10"/>
                                </svg>
                                <span class="btn-text">Crear Configuración</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHTML);
    }

    /**
     * Vincula los event listeners
     */
    bindEvents() {
        const modal = document.getElementById(this.modalId);
        const form = document.getElementById(this.formId);
        const createBtn = document.getElementById('createConfigBtn');
        const keyInput = document.getElementById('configKey');
        const valueInput = document.getElementById('configValue');
        const activeInput = document.getElementById('configActive');

        // Event listener para el botón de crear
        createBtn?.addEventListener('click', () => this.handleCreateConfiguration());

        // Event listeners para la vista previa en tiempo real
        keyInput?.addEventListener('input', () => this.updatePreview());
        valueInput?.addEventListener('input', () => this.updatePreview());
        activeInput?.addEventListener('change', () => this.updatePreview());

        // Event listener para el contador de caracteres
        valueInput?.addEventListener('input', () => this.updateCharacterCount());

        // Event listener para el switch de estado
        activeInput?.addEventListener('change', () => this.updateActiveLabel());

        // Event listener para reset del modal
        modal?.addEventListener('hidden.bs.modal', () => this.resetModal());

        // Event listener para validación en tiempo real
        form?.addEventListener('input', () => this.validateForm());
    }

    /**
     * Abre el modal
     */
    openModal() {
        const modal = new bootstrap.Modal(document.getElementById(this.modalId));
        modal.show();
        
        // Focus en el primer campo
        setTimeout(() => {
            document.getElementById('configKey')?.focus();
        }, 300);
    }

    /**
     * Cierra el modal
     */
    closeModal() {
        const modal = bootstrap.Modal.getInstance(document.getElementById(this.modalId));
        modal?.hide();
    }

    /**
     * Maneja la creación de configuración
     */
    async handleCreateConfiguration() {
        if (this.isSubmitting) return;

        const form = document.getElementById(this.formId);
        const createBtn = document.getElementById('createConfigBtn');
        
        // Validar formulario
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            return;
        }

        try {
            this.isSubmitting = true;
            this.setButtonLoading(createBtn, true);

            // Obtener datos del formulario
            const formData = new FormData(form);
            const configData = {
                key: formData.get('key'),
                value: formData.get('value'),
                active: formData.get('active') === 'on'
            };

            // Crear configuración
            const result = await this.createService.createConfiguration(configData);

            // Mostrar éxito
            this.showSuccessMessage('Configuración creada exitosamente');

            // Cerrar modal
            this.closeModal();

            // Refrescar tabla (si existe el controlador principal)
            if (window.configurationController && typeof window.configurationController.refreshTable === 'function') {
                window.configurationController.refreshTable();
            }

        } catch (error) {
            console.error('Error creando configuración:', error);
            this.showErrorMessage(error.message || 'Error al crear la configuración');
        } finally {
            this.isSubmitting = false;
            this.setButtonLoading(createBtn, false);
        }
    }

    /**
     * Actualiza la vista previa en tiempo real
     */
    updatePreview() {
        const key = document.getElementById('configKey').value;
        const value = document.getElementById('configValue').value;
        const active = document.getElementById('configActive').checked;

        if (key || value) {
            const preview = document.getElementById('configPreview');
            const previewKey = document.getElementById('previewKey');
            const previewValue = document.getElementById('previewValue');
            const previewStatus = document.getElementById('previewStatus');

            previewKey.textContent = key || '-';
            previewValue.textContent = value || '-';
            previewStatus.textContent = active ? 'Activo' : 'Inactivo';
            previewStatus.className = `badge badge-${active ? 'success' : 'danger'}`;

            preview.classList.remove('d-none');
        } else {
            document.getElementById('configPreview').classList.add('d-none');
        }
    }

    /**
     * Actualiza el contador de caracteres
     */
    updateCharacterCount() {
        const valueInput = document.getElementById('configValue');
        const charCount = document.querySelector('.char-count');
        
        if (valueInput && charCount) {
            const length = valueInput.value.length;
            charCount.textContent = `${length}/1000 caracteres`;
            
            if (length > 900) {
                charCount.classList.add('text-warning');
            } else if (length > 950) {
                charCount.classList.add('text-danger');
            } else {
                charCount.classList.remove('text-warning', 'text-danger');
            }
        }
    }

    /**
     * Actualiza el label del switch de estado
     */
    updateActiveLabel() {
        const activeInput = document.getElementById('configActive');
        const statusText = document.querySelector('.status-text');
        
        if (activeInput && statusText) {
            statusText.textContent = activeInput.checked ? 'Activo' : 'Inactivo';
        }
    }

    /**
     * Valida el formulario
     */
    validateForm() {
        const form = document.getElementById(this.formId);
        const keyInput = document.getElementById('configKey');
        const valueInput = document.getElementById('configValue');

        // Validación personalizada para la clave
        if (keyInput.value && !/^[a-zA-Z0-9._-]+$/.test(keyInput.value)) {
            keyInput.setCustomValidity('La clave solo puede contener letras, números, puntos, guiones y guiones bajos');
        } else {
            keyInput.setCustomValidity('');
        }

        // Validación de longitud mínima para la clave
        if (keyInput.value && keyInput.value.length < 3) {
            keyInput.setCustomValidity('La clave debe tener al menos 3 caracteres');
        }
    }

    /**
     * Resetea el modal
     */
    resetModal() {
        const form = document.getElementById(this.formId);
        form.reset();
        form.classList.remove('was-validated');
        
        // Resetear vista previa
        document.getElementById('configPreview').classList.add('d-none');
        
        // Resetear contador de caracteres
        document.querySelector('.char-count').textContent = '0/1000 caracteres';
        document.querySelector('.char-count').classList.remove('text-warning', 'text-danger');
        
        // Resetear label de estado
        document.querySelector('.status-text').textContent = 'Activo';
        
        // Resetear estado del botón
        const createBtn = document.getElementById('createConfigBtn');
        this.setButtonLoading(createBtn, false);
    }

    /**
     * Establece el estado de loading del botón
     */
    setButtonLoading(button, loading) {
        if (!button) return;

        const btnText = button.querySelector('.btn-text');
        const btnIcon = button.querySelector('.icon');

        if (loading) {
            button.disabled = true;
            if (btnText) btnText.textContent = 'Creando...';
            
            // Reemplazar el icono con un spinner
            if (btnIcon) {
                btnIcon.innerHTML = `
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M12 3a9 9 0 1 0 9 9"/>
                `;
                btnIcon.setAttribute('class', 'icon icon-tabler icon-tabler-loader animate-spin me-1');
            }
        } else {
            button.disabled = false;
            if (btnText) btnText.textContent = 'Crear Configuración';
            
            // Restaurar el icono de check
            if (btnIcon) {
                btnIcon.innerHTML = `
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M5 12l5 5l10 -10"/>
                `;
                btnIcon.setAttribute('class', 'icon icon-tabler icon-tabler-check me-1');
            }
        }
    }

    /**
     * Muestra mensaje de éxito
     */
    showSuccessMessage(message) {
        // Usar el sistema de toast global si está disponible
        if (window.showToast) {
            window.showToast(message, 'success');
        } else {
            alert(message);
        }
    }

    /**
     * Muestra mensaje de error
     */
    showErrorMessage(message) {
        // Usar el sistema de toast global si está disponible
        if (window.showToast) {
            window.showToast(message, 'error');
        } else {
            alert(message);
        }
    }
}

// Exportar la clase
window.CreateConfigurationModalController = CreateConfigurationModalController;
