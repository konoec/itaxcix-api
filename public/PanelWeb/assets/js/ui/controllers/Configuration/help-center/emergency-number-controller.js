/**
 * Controlador para la gestión de número de emergencia
 * Maneja todas las funcionalidades relacionadas con el número de emergencia
 */
class EmergencyNumberController {
    constructor() {
        this.isInitialized = false;
        
        // Referencias a elementos del DOM para emergencia (modal)
        this.emergencyModal = document.getElementById('emergency-modal');
        this.closeEmergencyModal = document.getElementById('close-emergency-modal');
        this.cancelEmergency = document.getElementById('cancel-emergency');
        this.emergencyForm = document.getElementById('emergency-number-form');
        this.emergencyNumberInput = document.getElementById('emergency-number');
        this.emergencyMessage = document.getElementById('emergency-number-message');
        
        // Referencias a elementos del formulario inline de emergencia
        this.emergencyInlineForm = document.getElementById('emergency-inline-form');
        this.emergencyInlineNumberInput = document.getElementById('emergencia');
        this.emergencyInlineMessage = document.getElementById('emergency-inline-message');
        this.cancelInlineEmergency = document.getElementById('cancel-inline-emergency');
        
        // Referencias a botones de la card de emergencia
        this.saveEmergencyBtn = document.querySelector('#card-emergencia .btn-save');
        this.cancelEmergencyBtn = document.querySelector('#card-emergencia .btn-cancel');
        
        // Inicializar
        this.init();
    }

    /**
     * Inicializa el controlador
     */
    async init() {
        console.log('🚨 Inicializando EmergencyNumberController...');
        try {
            // Verificar que el servicio real esté disponible
            if (!window.EmergencyNumberService) {
                console.error('❌ EmergencyNumberService no está disponible. Verifica que emergency-number-service.js se haya cargado correctamente.');
                console.log('🔍 Servicios disponibles en window:', Object.keys(window).filter(key => key.includes('Service')));
                return;
            }
            
            console.log('✅ EmergencyNumberService disponible:', typeof window.EmergencyNumberService);
            this.setupEventListeners();
            await this.loadEmergencyNumberInline();
            this.isInitialized = true;
            console.log('✅ EmergencyNumberController inicializado correctamente');
        } catch (error) {
            console.error('❌ Error al inicializar EmergencyNumberController:', error);
        }
    }

    /**
     * Configura los event listeners
     */
    setupEventListeners() {
        // Event listeners para cancelar emergencia en card
        if (this.cancelInlineEmergency) {
            this.cancelInlineEmergency.addEventListener('click', () => {
                this.resetEmergencyCard();
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

        // Event listeners para botones de la card de emergencia
        if (this.saveEmergencyBtn) {
            this.saveEmergencyBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.saveEmergencyNumberInline();
            });
        }

        if (this.cancelEmergencyBtn) {
            this.cancelEmergencyBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.resetEmergencyCard();
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
     * Resetea la card de emergencia
     */
    resetEmergencyCard() {
        if (this.emergencyInlineNumberInput) {
            this.emergencyInlineNumberInput.value = '';
        }
        if (this.emergencyInlineMessage) {
            this.emergencyInlineMessage.textContent = '';
        }
        // Cargar el valor actual nuevamente
        this.loadEmergencyNumberInline();
    }

    /**
     * Carga el número de emergencia actual en el formulario inline
     */
    async loadEmergencyNumberInline() {
        if (!this.emergencyInlineNumberInput) {
            console.warn('⚠️ Input inline de número de emergencia no encontrado');
            return;
        }

        try {
            console.log('🔄 Cargando número de emergencia desde API...');
            const response = await window.EmergencyNumberService.getEmergencyNumber();
            
            console.log('📡 Respuesta del servidor:', response);
            
            // Manejar diferentes estructuras de respuesta posibles
            let emergencyNumber = null;
            
            if (response.success) {
                // Estructura: { success: true, data: { number: "..." } }
                if (response.data && response.data.number) {
                    emergencyNumber = response.data.number;
                }
                // Estructura: { success: true, number: "..." }
                else if (response.number) {
                    emergencyNumber = response.number;
                }
                // Estructura: { success: true, data: "..." }
                else if (response.data && typeof response.data === 'string') {
                    emergencyNumber = response.data;
                }
            }
            
            if (emergencyNumber) {
                this.emergencyInlineNumberInput.value = emergencyNumber;
                console.log('✅ Número de emergencia cargado:', emergencyNumber);
                
                // Limpiar mensajes de error previos
                if (this.emergencyInlineMessage) {
                    this.emergencyInlineMessage.textContent = '';
                }
            } else {
                console.warn('⚠️ No se encontró número de emergencia en la respuesta:', response);
                this.emergencyInlineNumberInput.value = '';
                
                if (this.emergencyInlineMessage) {
                    this.emergencyInlineMessage.textContent = response.message || 'No hay número configurado';
                    this.emergencyInlineMessage.style.color = '#6b7280';
                }
            }
        } catch (error) {
            console.error('❌ Error al cargar número de emergencia:', error);
            this.emergencyInlineNumberInput.value = '';
            if (this.emergencyInlineMessage) {
                this.emergencyInlineMessage.textContent = 'Error al conectar con el servidor';
                this.emergencyInlineMessage.style.color = '#dc3545';
            }
        }
    }

    /**
     * Guarda el número de emergencia desde el formulario inline
     */
    async saveEmergencyNumberInline() {
        const emergencyNumber = this.emergencyInlineNumberInput?.value?.trim();

        if (!emergencyNumber) {
            this.showInlineMessage('Por favor ingresa un número válido', 'error');
            return;
        }

        if (!this.isValidPhoneNumber(emergencyNumber)) {
            this.showInlineMessage('El formato del número no es válido', 'error');
            return;
        }

        try {
            console.log('💾 Guardando número de emergencia inline:', emergencyNumber);
            this.showInlineMessage('Guardando...', 'info');
            
            const response = await window.EmergencyNumberService.updateEmergencyNumber(emergencyNumber);
            
            if (response.success) {
                // Limpiar mensaje inline y mostrar toast de éxito
                this.showInlineMessage('', '');
                window.showRecoveryToast('Configuración guardada exitosamente', 'success');
                console.log('✅ Número de emergencia guardado correctamente');
            } else {
                this.showInlineMessage('Error al guardar el número', 'error');
                console.error('❌ Error en la respuesta del servidor');
            }
        } catch (error) {
            console.error('❌ Error al guardar número de emergencia:', error);
            this.showInlineMessage('Error de conexión', 'error');
        }
    }

    /**
     * Valida el número de emergencia en tiempo real (formulario inline)
     */
    validateEmergencyNumberInline() {
        const emergencyNumber = this.emergencyInlineNumberInput?.value?.trim();
        
        if (!emergencyNumber) {
            this.showInlineMessage('', '');
            return;
        }

        if (this.isValidPhoneNumber(emergencyNumber)) {
            this.showInlineMessage('Número válido', 'success');
        } else {
            this.showInlineMessage('Formato inválido. Ejemplo: +51987654321', 'error');
        }
    }

    /**
     * Muestra un mensaje en el formulario inline
     * @param {string} message - Mensaje a mostrar
     * @param {string} type - Tipo de mensaje (success, error, info)
     */
    showInlineMessage(message, type) {
        if (!this.emergencyInlineMessage) return;

        this.emergencyInlineMessage.textContent = message;
        
        // Remover clases anteriores
        this.emergencyInlineMessage.classList.remove('success', 'error', 'info');
        
        // Aplicar estilos según el tipo
        switch (type) {
            case 'success':
                this.emergencyInlineMessage.style.color = '#28a745';
                break;
            case 'error':
                this.emergencyInlineMessage.style.color = '#dc3545';
                break;
            case 'info':
                this.emergencyInlineMessage.style.color = '#17a2b8';
                break;
            default:
                this.emergencyInlineMessage.style.color = '#2b3962';
        }
    }

    /**
     * Valida si un número de teléfono tiene un formato válido
     * @param {string} phoneNumber - Número a validar
     * @returns {boolean} - True si es válido
     */
    isValidPhoneNumber(phoneNumber) {
        // Patrón simple para validar números de teléfono (modificar según necesidades)
        const phonePattern = /^\+?\d{10,15}$/;
        return phonePattern.test(phoneNumber);
    }

    // ===== MÉTODOS PARA MODAL DE EMERGENCIA =====

    /**
     * Cierra el modal de emergencia
     */
    closeModal() {
        if (this.emergencyModal) {
            this.emergencyModal.style.display = 'none';
            console.log('🚫 Modal de emergencia cerrado');
        }
        
        // Limpiar formulario y mensajes
        if (this.emergencyNumberInput) {
            this.emergencyNumberInput.value = '';
        }
        if (this.emergencyMessage) {
            this.emergencyMessage.textContent = '';
        }
    }

    /**
     * Guarda el número de emergencia desde el modal
     */
    async saveEmergencyNumber() {
        const emergencyNumber = this.emergencyNumberInput?.value?.trim();

        if (!emergencyNumber) {
            this.showModalMessage('Por favor ingresa un número válido', 'error');
            return;
        }

        if (!this.isValidPhoneNumber(emergencyNumber)) {
            this.showModalMessage('El formato del número no es válido', 'error');
            return;
        }

        try {
            console.log('💾 Guardando número de emergencia desde modal:', emergencyNumber);
            this.showModalMessage('Guardando...', 'info');
            
            const response = await window.EmergencyNumberService.updateEmergencyNumber(emergencyNumber);
            
            if (response.success) {
                this.showModalMessage('Número guardado exitosamente', 'success');
                console.log('✅ Número de emergencia guardado correctamente desde modal');
                
                // Cerrar modal después de un momento
                setTimeout(() => {
                    this.closeModal();
                }, 1500);
            } else {
                this.showModalMessage('Error al guardar el número', 'error');
                console.error('❌ Error en la respuesta del servidor');
            }
        } catch (error) {
            console.error('❌ Error al guardar número de emergencia desde modal:', error);
            this.showModalMessage('Error de conexión', 'error');
        }
    }

    /**
     * Valida el número de emergencia en tiempo real (modal)
     */
    validateEmergencyNumber() {
        const emergencyNumber = this.emergencyNumberInput?.value?.trim();
        
        if (!emergencyNumber) {
            this.showModalMessage('', '');
            return;
        }

        if (this.isValidPhoneNumber(emergencyNumber)) {
            this.showModalMessage('Número válido', 'success');
        } else {
            this.showModalMessage('Formato inválido. Ejemplo: +51987654321', 'error');
        }
    }

    /**
     * Muestra un mensaje en el modal
     * @param {string} message - Mensaje a mostrar
     * @param {string} type - Tipo de mensaje (success, error, info)
     */
    showModalMessage(message, type) {
        if (!this.emergencyMessage) return;

        this.emergencyMessage.textContent = message;
        
        // Aplicar estilos según el tipo
        switch (type) {
            case 'success':
                this.emergencyMessage.style.color = '#28a745';
                break;
            case 'error':
                this.emergencyMessage.style.color = '#dc3545';
                break;
            case 'info':
                this.emergencyMessage.style.color = '#17a2b8';
                break;
            default:
                this.emergencyMessage.style.color = '#2b3962';
        }
    }
}

// Exportar para uso global
window.EmergencyNumberController = EmergencyNumberController;
// Mantener compatibilidad con el nombre anterior
window.EmergencyContactController = EmergencyNumberController;

// Auto-inicialización solo si estamos en la página de configuración
document.addEventListener('DOMContentLoaded', () => {
    // Verificar si estamos en la página de configuración buscando elementos específicos
    const emergencyElements = document.getElementById('card-emergencia') || 
                             document.getElementById('emergency-inline-form');
    
    // Solo auto-inicializar si no existe una instancia global
    if (emergencyElements && !window.emergencyNumberController && !window.emergencyContactController) {
        window.emergencyNumberController = new EmergencyNumberController();
        console.log('🚨 EmergencyNumberController auto-inicializado');
    }
});
