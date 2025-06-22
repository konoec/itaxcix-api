/**
 * Controlador UI para la página de Configuration
 * Maneja todas las opciones de configuración del sistema
 */
class ConfigurationController {
    constructor() {
        this.isInitialized = false;
        
        // Referencias a elementos del DOM para emergencia (modal)
        this.emergencyModal = document.getElementById('emergency-modal');
        this.closeEmergencyModal = document.getElementById('close-emergency-modal');
        this.cancelEmergency = document.getElementById('cancel-emergency');
        this.emergencyForm = document.getElementById('emergency-number-form');
        this.emergencyNumberInput = document.getElementById('emergency-number');
        this.emergencyMessage = document.getElementById('emergency-number-message');
        
        // Referencias a elementos del DOM para contenedor rectangular
        this.emergencyContainer = document.getElementById('emergency-config-container');
        this.emergencyHeader = document.getElementById('emergency-config-header');
        this.emergencyContent = document.getElementById('emergency-config-content');
        this.emergencyInlineForm = document.getElementById('emergency-inline-form');
        this.emergencyInlineNumberInput = document.getElementById('emergency-inline-number');
        this.emergencyInlineMessage = document.getElementById('emergency-inline-message');
        this.cancelInlineEmergency = document.getElementById('cancel-inline-emergency');
        
        // Inicializar
        this.init();
    }

    /**
     * Inicializa el controlador
     */
    async init() {
        console.log('⚙️ Inicializando ConfigurationController...');
        try {
            this.setupEventListeners();
            this.loadEmergencyNumberInline();
            this.isInitialized = true;
            console.log('✅ ConfigurationController inicializado correctamente');
        } catch (error) {
            console.error('❌ Error al inicializar ConfigurationController:', error);
        }
    }

    /**
     * Configura los event listeners
     */
    setupEventListeners() {
        // Event listeners para contenedor rectangular
        if (this.emergencyHeader) {
            this.emergencyHeader.addEventListener('click', () => {
                this.toggleEmergencyContainer();
            });
        }

        if (this.cancelInlineEmergency) {
            this.cancelInlineEmergency.addEventListener('click', () => {
                this.collapseEmergencyContainer();
            });
        }

        // Manejar envío del formulario inline
        if (this.emergencyInlineForm) {
            this.emergencyInlineForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.saveEmergencyNumberInline();
            });
        }

        // Validación en tiempo real para formulario inline
        if (this.emergencyInlineNumberInput) {
            this.emergencyInlineNumberInput.addEventListener('input', () => {
                this.validateEmergencyNumberInline();
            });
        }

        // Event listeners para modal (mantener compatibilidad)
        if (this.closeEmergencyModal) {
            this.closeEmergencyModal.addEventListener('click', () => {
                this.closeModal();
            });
        }

        if (this.cancelEmergency) {
            this.cancelEmergency.addEventListener('click', () => {
                this.closeModal();
            });
        }

        // Cerrar modal al hacer clic fuera
        if (this.emergencyModal) {
            this.emergencyModal.addEventListener('click', (e) => {
                if (e.target === this.emergencyModal) {
                    this.closeModal();
                }
            });
        }

        // Manejar envío del formulario modal
        if (this.emergencyForm) {
            this.emergencyForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.saveEmergencyNumber();
            });
        }

        // Validación en tiempo real para modal
        if (this.emergencyNumberInput) {
            this.emergencyNumberInput.addEventListener('input', () => {
                this.validateEmergencyNumber();
            });
        }
    }

    /**
     * Alterna la expansión del contenedor de emergencia
     */
    toggleEmergencyContainer() {
        if (this.emergencyContent && this.emergencyHeader) {
            const isExpanded = this.emergencyContent.classList.contains('expanded');
            
            if (isExpanded) {
                this.collapseEmergencyContainer();
            } else {
                this.expandEmergencyContainer();
            }
        }
    }

    /**
     * Expande el contenedor de emergencia
     */
    expandEmergencyContainer() {
        if (this.emergencyContent && this.emergencyHeader) {
            this.emergencyContent.classList.add('expanded');
            this.emergencyHeader.classList.add('expanded');
            this.loadEmergencyNumberInline();
        }
    }

    /**
     * Colapsa el contenedor de emergencia
     */
    collapseEmergencyContainer() {
        if (this.emergencyContent && this.emergencyHeader) {
            this.emergencyContent.classList.remove('expanded');
            this.emergencyHeader.classList.remove('expanded');
            // Limpiar mensajes
            if (this.emergencyInlineMessage) {
                this.emergencyInlineMessage.textContent = '';
            }
        }
    }    /**
     * Carga el número de emergencia actual en el formulario inline
     */
    async loadEmergencyNumberInline() {
        if (this.emergencyInlineNumberInput && window.ConfigurationService) {
            try {
                const res = await window.ConfigurationService.getEmergencyNumber();
                if (res.success && res.data && res.data.number) {
                    this.emergencyInlineNumberInput.value = res.data.number;
                } else {
                    this.emergencyInlineNumberInput.value = '';
                }
            } catch (error) {
                console.error('Error al cargar número de emergencia:', error);
                this.emergencyInlineNumberInput.value = '';
            }
        }
    }

    /**
     * Valida el número de emergencia del formulario inline
     */
    validateEmergencyNumberInline() {
        const number = this.emergencyInlineNumberInput?.value?.trim();
        if (!number) return true;

        // Expresión regular para validar número de teléfono
        const phoneRegex = /^(\+?51)?[0-9]{9}$/;
        const isValid = phoneRegex.test(number.replace(/\s/g, ''));

        if (!isValid && this.emergencyInlineMessage) {
            this.emergencyInlineMessage.textContent = 'Formato inválido. Use: 999999999 o +51999999999';
            this.emergencyInlineMessage.style.color = '#dc3545';
        } else if (this.emergencyInlineMessage) {
            this.emergencyInlineMessage.textContent = '';
        }

        return isValid;
    }

    /**
     * Guarda el número de emergencia del formulario inline
     */
    async saveEmergencyNumberInline() {
        const number = this.emergencyInlineNumberInput?.value?.trim();
        
        if (!number) {
            this.showMessageInline('El número de emergencia es requerido', 'error');
            return;
        }

        if (!this.validateEmergencyNumberInline()) {
            return;
        }

        try {
            if (window.ConfigurationService) {
                const res = await window.ConfigurationService.updateEmergencyNumber(number);
                if (res.success) {
                    this.showMessageInline('Número de emergencia guardado exitosamente', 'success');
                    setTimeout(() => {
                        this.collapseEmergencyContainer();
                    }, 1500);
                } else {
                    this.showMessageInline(res.error?.message || res.message || 'Error al guardar', 'error');
                }
            } else {
                this.showMessageInline('Servicio no disponible', 'error');
            }
        } catch (error) {
            console.error('Error al guardar número de emergencia:', error);
            this.showMessageInline('Error al guardar el número', 'error');
        }
    }

    /**
     * Muestra mensajes en el formulario inline
     */
    showMessageInline(message, type = 'info') {
        if (this.emergencyInlineMessage) {
            this.emergencyInlineMessage.textContent = message;
            this.emergencyInlineMessage.style.color = type === 'error' ? '#dc3545' : 
                                                      type === 'success' ? '#28a745' : '#2b3962';
        }
    }

    /**
     * Abre el modal (método mantenido para compatibilidad)
     */
    openModal() {
        if (this.emergencyModal) {
            this.emergencyModal.style.display = 'block';
            // Cargar número actual
            this.loadEmergencyNumber();
        }
    }

    /**
     * Cierra el modal
     */
    closeModal() {
        if (this.emergencyModal) {
            this.emergencyModal.style.display = 'none';
            // Limpiar mensajes
            if (this.emergencyMessage) {
                this.emergencyMessage.textContent = '';
            }
        }
    }

    /**
     * Carga el número de emergencia actual
     */
    async loadEmergencyNumber() {
        if (this.emergencyNumberInput && window.ConfigurationService) {
            try {
                const res = await window.ConfigurationService.getEmergencyNumber();
                if (res.success && res.data && res.data.number) {
                    this.emergencyNumberInput.value = res.data.number;
                } else {
                    this.emergencyNumberInput.value = '';
                }
            } catch (error) {
                console.error('Error al cargar número de emergencia:', error);
                this.emergencyNumberInput.value = '';
            }
        }
    }

    /**
     * Valida el número de emergencia
     */
    validateEmergencyNumber() {
        const number = this.emergencyNumberInput?.value?.trim();
        if (!number) return true;

        // Expresión regular para validar número de teléfono
        const phoneRegex = /^(\+?51)?[0-9]{9}$/;
        const isValid = phoneRegex.test(number.replace(/\s/g, ''));

        if (!isValid && this.emergencyMessage) {
            this.emergencyMessage.textContent = 'Formato inválido. Use: 999999999 o +51999999999';
            this.emergencyMessage.style.color = '#dc3545';
        } else if (this.emergencyMessage) {
            this.emergencyMessage.textContent = '';
        }

        return isValid;
    }

    /**
     * Guarda el número de emergencia
     */
    async saveEmergencyNumber() {
        const number = this.emergencyNumberInput?.value?.trim();
        
        if (!number) {
            this.showMessage('El número de emergencia es requerido', 'error');
            return;
        }

        if (!this.validateEmergencyNumber()) {
            return;
        }

        try {
            if (window.ConfigurationService) {
                const res = await window.ConfigurationService.updateEmergencyNumber(number);
                if (res.success) {
                    this.showMessage('Número de emergencia guardado exitosamente', 'success');
                    setTimeout(() => {
                        this.closeModal();
                    }, 1500);
                } else {
                    this.showMessage(res.error?.message || res.message || 'Error al guardar', 'error');
                }
            } else {
                this.showMessage('Servicio no disponible', 'error');
            }
        } catch (error) {
            console.error('Error al guardar número de emergencia:', error);
            this.showMessage('Error al guardar el número', 'error');
        }
    }

    /**
     * Muestra mensajes en el modal
     */
    showMessage(message, type = 'info') {
        if (this.emergencyMessage) {
            this.emergencyMessage.textContent = message;
            this.emergencyMessage.style.color = type === 'error' ? '#dc3545' : 
                                                type === 'success' ? '#28a745' : '#2b3962';
        }
    }

    /**
     * Muestra una notificación toast
     */
    showToast(message, type = 'info') {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toast-message');
        
        if (toast && toastMessage) {
            toastMessage.textContent = message;
            toast.className = `toast ${type}`;
            toast.style.display = 'block';
            
            setTimeout(() => {
                toast.style.display = 'none';
            }, 3000);
        }
    }
}

// Variable global para el controlador
window.configurationController = null;