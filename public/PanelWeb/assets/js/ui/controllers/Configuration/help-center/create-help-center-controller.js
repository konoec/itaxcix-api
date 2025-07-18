/**
 * Controlador para el modal de creación de elementos del centro de ayuda
 * Maneja la interfaz y lógica del formulario de creación
 */
class CreateHelpCenterController {
    constructor(onSuccess) {
        this.onSuccess = onSuccess || (() => {});
        this.modal = null;
        this.bsModal = null;
        this.form = null;
        
        // Bind methods
        this.handleSubmit = this.handleSubmit.bind(this);
        this.open = this.open.bind(this);
        this.close = this.close.bind(this);
        this.resetForm = this.resetForm.bind(this);
        
        this.init();
    }

    /**
     * Inicializa el controlador
     */
    init() {
        console.log('🔧 Inicializando CreateHelpCenterController...');
        this.createModal();
        this.setupEventListeners();
    }

    /**
     * Crea el modal HTML dinámicamente
     */
    createModal() {
        const modalHTML = `
            <!-- Modal Crear Elemento del Centro de Ayuda -->
            <div class="modal modal-blur fade" id="createHelpCenterModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content shadow-lg border-0">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus me-2" width="20" height="20" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M12 5l0 14"/>
                                    <path d="M5 12l14 0"/>
                                </svg>
                                Crear Elemento del Centro de Ayuda
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="createHelpCenterForm" autocomplete="off">
                            <div class="modal-body">
                                <!-- Campo Título -->
                                <div class="mb-3">
                                    <label for="createHelpCenterTitle" class="form-label required">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-heading me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M7 12h10"/>
                                            <path d="M7 4v16"/>
                                            <path d="M17 4v16"/>
                                            <path d="M15 20h4"/>
                                            <path d="M15 4h4"/>
                                            <path d="M5 20h4"/>
                                            <path d="M5 4h4"/>
                                        </svg>
                                        Título <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="createHelpCenterTitle" name="title" required 
                                           placeholder="Ej: Viajes" maxlength="255">
                                    <div class="invalid-feedback" id="title-error"></div>
                                </div>

                                <!-- Campo Subtítulo -->
                                <div class="mb-3">
                                    <label for="createHelpCenterSubtitle" class="form-label required">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-text-caption me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M4 15h16"/>
                                            <path d="M4 4h12"/>
                                            <path d="M4 9h16"/>
                                        </svg>
                                        Subtítulo <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="createHelpCenterSubtitle" name="subtitle" required 
                                           placeholder="Ej: ¿Cómo solicitar un viaje?" maxlength="255">
                                    <div class="invalid-feedback" id="subtitle-error"></div>
                                </div>

                                <!-- Campo Respuesta -->
                                <div class="mb-3">
                                    <label for="createHelpCenterAnswer" class="form-label required">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-message-circle me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M3 20l1.3 -3.9c-2.324 -3.437 -1.426 -7.872 2.1 -10.374c3.526 -2.501 8.59 -2.296 11.845 .48c3.255 2.777 3.695 7.266 1.029 10.501c-2.666 3.235 -7.615 4.215 -11.574 2.293l-4.7 1"/>
                                        </svg>
                                        Respuesta <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" id="createHelpCenterAnswer" name="answer" rows="4" required 
                                              placeholder="Ej: Para solicitar un viaje, abre la aplicación y selecciona tu destino." maxlength="1000"></textarea>
                                    <div class="form-text">Máximo 1000 caracteres</div>
                                    <div class="invalid-feedback" id="answer-error"></div>
                                </div>

                                <!-- Campo Estado Activo -->
                                <div class="mb-3">
                                    <label class="form-label">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-toggle-left me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M8 16a4 4 0 1 1 0 -8a4 4 0 0 1 0 8z"/>
                                            <path d="M4 12h16"/>
                                        </svg>
                                        Estado
                                    </label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="createHelpCenterActive" name="active" checked>
                                        <label class="form-check-label" for="createHelpCenterActive">
                                            Elemento activo
                                        </label>
                                    </div>
                                    <div class="form-text">Solo los elementos activos son visibles para los usuarios</div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M18 6l-12 12"/>
                                        <path d="M6 6l12 12"/>
                                    </svg>
                                    Cancelar
                                </button>
                                <button type="submit" class="btn btn-primary" id="createHelpCenterSubmitBtn">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M12 5l0 14"/>
                                        <path d="M5 12l14 0"/>
                                    </svg>
                                    Crear Elemento
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;

        // Agregar el modal al contenedor
        const modalContainer = document.getElementById('modal-container');
        if (modalContainer) {
            modalContainer.insertAdjacentHTML('beforeend', modalHTML);
        } else {
            document.body.insertAdjacentHTML('beforeend', modalHTML);
        }

        // Obtener referencias
        this.modal = document.getElementById('createHelpCenterModal');
        this.form = document.getElementById('createHelpCenterForm');
        
        if (this.modal) {
            this.bsModal = new bootstrap.Modal(this.modal);
        }
    }

    /**
     * Configura los event listeners
     */
    setupEventListeners() {
        if (!this.form) return;

        // Submit del formulario
        this.form.addEventListener('submit', this.handleSubmit);

        // Validación en tiempo real
        const titleInput = document.getElementById('createHelpCenterTitle');
        const subtitleInput = document.getElementById('createHelpCenterSubtitle');
        const answerTextarea = document.getElementById('createHelpCenterAnswer');

        if (titleInput) {
            titleInput.addEventListener('input', () => this.validateField('title', titleInput.value));
        }

        if (subtitleInput) {
            subtitleInput.addEventListener('input', () => this.validateField('subtitle', subtitleInput.value));
        }

        if (answerTextarea) {
            answerTextarea.addEventListener('input', () => this.validateField('answer', answerTextarea.value));
        }

        // Limpiar formulario al cerrar modal
        if (this.modal) {
            this.modal.addEventListener('hidden.bs.modal', this.resetForm);
        }

        console.log('✅ Event listeners configurados para CreateHelpCenterController');
    }

    /**
     * Abre el modal
     */
    open() {
        if (this.bsModal) {
            this.resetForm();
            this.bsModal.show();
            console.log('📖 Modal de creación abierto');
        }
    }

    /**
     * Cierra el modal
     */
    close() {
        if (this.bsModal) {
            this.bsModal.hide();
        }
    }

    /**
     * Maneja el envío del formulario
     */
    async handleSubmit(e) {
        e.preventDefault();
        
        const titleInput = document.getElementById('createHelpCenterTitle');
        const subtitleInput = document.getElementById('createHelpCenterSubtitle');
        const answerTextarea = document.getElementById('createHelpCenterAnswer');
        const activeCheckbox = document.getElementById('createHelpCenterActive');
        const submitBtn = document.getElementById('createHelpCenterSubmitBtn');

        try {
            // Recopilar datos del formulario
            const formData = {
                title: titleInput.value.trim(),
                subtitle: subtitleInput.value.trim(),
                answer: answerTextarea.value.trim(),
                active: activeCheckbox.checked
            };

            console.log('📤 Enviando datos de creación:', formData);

            // Validar formulario
            if (!this.validateForm(formData)) {
                return;
            }

            // Mostrar estado de carga
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                    Creando...
                `;
            }

            // Llamar al servicio de creación
            const response = await window.CreateHelpCenterService.createHelpCenterItem(formData);

            console.log('✅ Elemento del centro de ayuda creado exitosamente:', response);

            // Mostrar mensaje de éxito
            this.showToast('Elemento creado exitosamente', 'success');

            // Cerrar modal
            this.close();

            // Ejecutar callback de éxito
            if (typeof this.onSuccess === 'function') {
                this.onSuccess(response.data);
            }

        } catch (error) {
            console.error('❌ Error al crear elemento:', error);
            this.showToast(error.message || 'Error al crear elemento', 'error');
        } finally {
            // Restaurar botón
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M12 5l0 14"/>
                        <path d="M5 12l14 0"/>
                    </svg>
                    Crear Elemento
                `;
            }
        }
    }

    /**
     * Valida el formulario completo
     * @param {Object} formData - Datos del formulario
     * @returns {boolean} - True si es válido
     */
    validateForm(formData) {
        let isValid = true;

        // Validar título
        if (!formData.title || formData.title.length === 0) {
            this.showFieldError('title', 'El título es requerido');
            isValid = false;
        } else if (formData.title.length > 255) {
            this.showFieldError('title', 'El título no puede exceder 255 caracteres');
            isValid = false;
        } else {
            this.clearFieldError('title');
        }

        // Validar subtítulo
        if (!formData.subtitle || formData.subtitle.length === 0) {
            this.showFieldError('subtitle', 'El subtítulo es requerido');
            isValid = false;
        } else if (formData.subtitle.length > 255) {
            this.showFieldError('subtitle', 'El subtítulo no puede exceder 255 caracteres');
            isValid = false;
        } else {
            this.clearFieldError('subtitle');
        }

        // Validar respuesta
        if (!formData.answer || formData.answer.length === 0) {
            this.showFieldError('answer', 'La respuesta es requerida');
            isValid = false;
        } else if (formData.answer.length > 1000) {
            this.showFieldError('answer', 'La respuesta no puede exceder 1000 caracteres');
            isValid = false;
        } else {
            this.clearFieldError('answer');
        }

        return isValid;
    }

    /**
     * Valida un campo específico
     * @param {string} fieldName - Nombre del campo
     * @param {string} value - Valor del campo
     */
    validateField(fieldName, value) {
        switch (fieldName) {
            case 'title':
                if (!value || value.length === 0) {
                    this.showFieldError('title', 'El título es requerido');
                } else if (value.length > 255) {
                    this.showFieldError('title', 'El título no puede exceder 255 caracteres');
                } else {
                    this.clearFieldError('title');
                }
                break;
            case 'subtitle':
                if (!value || value.length === 0) {
                    this.showFieldError('subtitle', 'El subtítulo es requerido');
                } else if (value.length > 255) {
                    this.showFieldError('subtitle', 'El subtítulo no puede exceder 255 caracteres');
                } else {
                    this.clearFieldError('subtitle');
                }
                break;
            case 'answer':
                if (!value || value.length === 0) {
                    this.showFieldError('answer', 'La respuesta es requerida');
                } else if (value.length > 1000) {
                    this.showFieldError('answer', 'La respuesta no puede exceder 1000 caracteres');
                } else {
                    this.clearFieldError('answer');
                }
                break;
        }
    }

    /**
     * Muestra error en un campo
     * @param {string} fieldName - Nombre del campo
     * @param {string} message - Mensaje de error
     */
    showFieldError(fieldName, message) {
        const input = document.getElementById(`createHelpCenter${fieldName.charAt(0).toUpperCase() + fieldName.slice(1)}`);
        const errorDiv = document.getElementById(`${fieldName}-error`);

        if (input) {
            input.classList.add('is-invalid');
        }

        if (errorDiv) {
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
        }
    }

    /**
     * Limpia error de un campo
     * @param {string} fieldName - Nombre del campo
     */
    clearFieldError(fieldName) {
        const input = document.getElementById(`createHelpCenter${fieldName.charAt(0).toUpperCase() + fieldName.slice(1)}`);
        const errorDiv = document.getElementById(`${fieldName}-error`);

        if (input) {
            input.classList.remove('is-invalid');
        }

        if (errorDiv) {
            errorDiv.textContent = '';
            errorDiv.style.display = 'none';
        }
    }

    /**
     * Resetea el formulario
     */
    resetForm() {
        if (this.form) {
            this.form.reset();
            
            // Limpiar errores
            this.clearFieldError('title');
            this.clearFieldError('subtitle');
            this.clearFieldError('answer');
            
            // Marcar como activo por defecto
            const activeCheckbox = document.getElementById('createHelpCenterActive');
            if (activeCheckbox) {
                activeCheckbox.checked = true;
            }
        }
    }

    /**
     * Muestra un toast de notificación
     * @param {string} message - Mensaje a mostrar
     * @param {string} type - Tipo de mensaje (success, error, info)
     */
    showToast(message, type = 'info') {
        console.log(`🍞 Toast: ${message} (${type})`);
        
        // Usar el sistema de toast global si está disponible
        if (window.showRecoveryToast) {
            window.showRecoveryToast(message, type);
        } else if (window.GlobalToast) {
            window.GlobalToast.show(message, type);
        } else if (window.showToast) {
            window.showToast(message, type);
        } else {
            // Fallback simple
            alert(message);
        }
    }
}

// Exportar controlador globalmente
window.CreateHelpCenterController = CreateHelpCenterController;
