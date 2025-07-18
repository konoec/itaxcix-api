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
        
        // Referencia al botón de emergencia principal
        this.emergencyCallBtn = document.getElementById('emergency-call-btn');
        
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
                console.warn('⚠️ EmergencyNumberService no está disponible aún. Reintentando en 1 segundo...');
                setTimeout(() => this.init(), 1000);
                return;
            }
            
            console.log('✅ EmergencyNumberService disponible:', typeof window.EmergencyNumberService);
            this.setupEventListeners();
            // No cargar formulario inline ya que ahora solo usamos modal
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

        // Event listener para el botón de emergencia principal
        if (this.emergencyCallBtn) {
            this.emergencyCallBtn.addEventListener('click', () => {
                this.showEmergencyModal();
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
                // Asegurar que el número tenga el prefijo +51
                if (!emergencyNumber.startsWith('+51')) {
                    // Si el número no tiene prefijo, añadir +51
                    if (emergencyNumber.startsWith('51')) {
                        emergencyNumber = '+' + emergencyNumber;
                    } else if (/^\d{9}$/.test(emergencyNumber)) {
                        // Si es solo un número de 9 dígitos, añadir +51
                        emergencyNumber = '+51' + emergencyNumber;
                    } else {
                        // Si tiene otro formato, intentar extraer los últimos 9 dígitos
                        const digits = emergencyNumber.replace(/\D/g, '');
                        if (digits.length >= 9) {
                            emergencyNumber = '+51' + digits.slice(-9);
                        } else {
                            emergencyNumber = '+51';
                        }
                    }
                }
                
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
        let emergencyNumber = this.emergencyInlineNumberInput?.value?.trim();

        if (!emergencyNumber) {
            this.showInlineMessage('Por favor ingresa un número válido', 'error');
            return;
        }

        // Asegurar que el número tenga el prefijo +51
        if (!emergencyNumber.startsWith('+51')) {
            // Si es solo números, añadir +51
            const digits = emergencyNumber.replace(/\D/g, '');
            if (digits.length === 9) {
                emergencyNumber = '+51' + digits;
            } else if (digits.length > 9) {
                emergencyNumber = '+51' + digits.slice(-9);
            } else {
                this.showInlineMessage('El formato del número no es válido. Debe tener 9 dígitos después de +51', 'error');
                return;
            }
        }

        // Validar formato final
        if (!this.isValidPhoneNumber(emergencyNumber)) {
            this.showInlineMessage('El formato del número no es válido', 'error');
            return;
        }

        try {
            console.log('💾 Guardando número de emergencia inline:', emergencyNumber);
            this.showInlineMessage('Guardando...', 'info');
            
            const response = await window.EmergencyNumberService.updateEmergencyNumber(emergencyNumber);
            
            if (response.success) {
                // Actualizar el input con el formato correcto
                this.emergencyInlineNumberInput.value = emergencyNumber;
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
        if (!phoneNumber) return false;
        
        // Formato peruano: +51 seguido de 9 dígitos
        const peruPhoneRegex = /^\+51\d{9}$/;
        return peruPhoneRegex.test(phoneNumber.trim());
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

    /**
     * Muestra directamente el modal de configuración del número de emergencia
     */
    async showEmergencyModal() {
        console.log('🚨 Mostrando modal de configuración de emergencia...');
        this.showConfigModal();
    }

    /**
     * Muestra el modal de configuración de número de emergencia
     */
    showConfigModal() {
        console.log('🔧 Mostrando modal de configuración...');
        
        // Crear modal de configuración dinámicamente
        const configModalHtml = `
            <div class="modal fade" id="emergency-config-modal" tabindex="-1" aria-labelledby="emergency-config-modal-label" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="emergency-config-modal-label">
                                <i class="fas fa-cog me-2"></i>
                                Configurar Número de Emergencia
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="modal-emergency-form">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label" for="modal-emergency-number">
                                        <i class="fas fa-phone me-1"></i>
                                        Número de emergencia:
                                    </label>
                                    <input type="tel" class="form-control" id="modal-emergency-number" name="emergencyNumber" 
                                           placeholder="Ej: +51911111111" maxlength="15" required value="+51">
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Número de contacto para emergencias.
                                    </div>
                                    <div class="invalid-feedback" id="modal-emergency-message"></div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i>
                                    Cancelar
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>
                                    Guardar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;
        
        // Insertar modal en el contenedor de modales
        const modalContainer = document.getElementById('modal-container');
        if (modalContainer) {
            modalContainer.insertAdjacentHTML('beforeend', configModalHtml);
        } else {
            document.body.insertAdjacentHTML('beforeend', configModalHtml);
        }
        
        // Configurar eventos del formulario
        const form = document.getElementById('modal-emergency-form');
        const input = document.getElementById('modal-emergency-number');
        
        // Configurar validación y mantenimiento del prefijo +51
        this.setupPhoneInput(input);
        
        // Cargar número actual
        this.loadCurrentNumber(input);
        
        // Configurar envío del formulario
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.saveEmergencyNumberFromModal(input.value);
        });
        
        // Mostrar modal
        const modal = new bootstrap.Modal(document.getElementById('emergency-config-modal'));
        modal.show();
        
        // Limpiar modal al cerrarse
        document.getElementById('emergency-config-modal').addEventListener('hidden.bs.modal', function () {
            this.remove();
        });
    }

    /**
     * Carga el número actual en el input del modal
     */
    async loadCurrentNumber(input) {
        try {
            const response = await EmergencyNumberService.getEmergencyNumber();
            if (response.success && response.data && response.data.number) {
                let number = response.data.number.toString();
                
                // Asegurar que el número tenga el prefijo +51
                if (!number.startsWith('+51')) {
                    // Si el número no tiene prefijo, añadir +51
                    if (number.startsWith('51')) {
                        number = '+' + number;
                    } else if (/^\d{9}$/.test(number)) {
                        // Si es solo un número de 9 dígitos, añadir +51
                        number = '+51' + number;
                    } else {
                        // Si tiene otro formato, intentar extraer los últimos 9 dígitos
                        const digits = number.replace(/\D/g, '');
                        if (digits.length >= 9) {
                            number = '+51' + digits.slice(-9);
                        } else {
                            number = '+51';
                        }
                    }
                }
                
                input.value = number;
            } else {
                // Si no hay número configurado, inicializar con +51
                input.value = '+51';
            }
        } catch (error) {
            console.error('❌ Error al cargar número actual:', error);
            // En caso de error, inicializar con +51
            input.value = '+51';
        }
    }

    /**
     * Guarda el número de emergencia desde el modal
     */
    async saveEmergencyNumberFromModal(number) {
        try {
            // Validar que el número tenga el formato correcto con +51
            if (!number.startsWith('+51')) {
                this.showToast('El número debe comenzar con +51', 'error');
                return;
            }
            
            // Validar formato completo
            const peruPhoneRegex = /^\+51\d{9}$/;
            if (!peruPhoneRegex.test(number)) {
                this.showToast('Formato inválido. Debe ser +51 seguido de 9 dígitos', 'error');
                return;
            }
            
            console.log('💾 Guardando número de emergencia:', number);
            
            const response = await EmergencyNumberService.updateEmergencyNumber(number);
            
            if (response.success) {
                this.showToast('Número de emergencia guardado exitosamente', 'success');
                
                // Cerrar modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('emergency-config-modal'));
                if (modal) {
                    modal.hide();
                }
            } else {
                this.showToast(response.message || 'Error al guardar número de emergencia', 'error');
            }
        } catch (error) {
            console.error('❌ Error al guardar:', error);
            this.showToast('Error inesperado al guardar', 'error');
        }
    }

    /**
     * Muestra un toast de notificación
     */
    showToast(message, type = 'info') {
        console.log(`🍞 Toast: ${message} (${type})`);
        
        // Usar el sistema de toast global si está disponible
        if (window.GlobalToast) {
            window.GlobalToast.show(message, type);
        } else if (window.showToast) {
            window.showToast(message, type);
        } else {
            // Fallback simple
            alert(message);
        }
    }

    /**
     * Configura el input de teléfono para mantener siempre el prefijo +51
     */
    setupPhoneInput(input) {
        if (!input) return;
        
        // Asegurar que siempre comience con +51
        if (!input.value.startsWith('+51')) {
            input.value = '+51';
        }
        
        // Event listener para mantener el prefijo +51
        input.addEventListener('input', (e) => {
            let value = e.target.value;
            
            // Si el usuario borra todo o no tiene +51, restaurarlo
            if (!value.startsWith('+51')) {
                // Extraer solo los números después de cualquier código existente
                const numbers = value.replace(/\D/g, '');
                
                // Si hay números, mantener solo los últimos 9 dígitos (número peruano)
                if (numbers.length > 0) {
                    const lastNineDigits = numbers.slice(-9);
                    e.target.value = '+51' + lastNineDigits;
                } else {
                    e.target.value = '+51';
                }
            }
            
            // Limitar la longitud total (13 caracteres: +51 + 9 dígitos)
            if (e.target.value.length > 13) {
                e.target.value = e.target.value.slice(0, 13);
            }
            
            // Validar formato en tiempo real
            this.validatePhoneNumber(e.target.value, 'modal-emergency-message');
        });
        
        // Event listener para prevenir que se borre +51 con backspace
        input.addEventListener('keydown', (e) => {
            const value = e.target.value;
            const cursorPosition = e.target.selectionStart;
            
            // Si intenta borrar parte del prefijo +51, prevenir
            if ((e.key === 'Backspace' || e.key === 'Delete') && cursorPosition <= 3) {
                e.preventDefault();
            }
        });
        
        // Event listener para focus - colocar cursor después del prefijo
        input.addEventListener('focus', (e) => {
            if (e.target.value === '+51') {
                // Colocar cursor al final
                setTimeout(() => {
                    e.target.setSelectionRange(3, 3);
                }, 10);
            }
        });
    }

    /**
     * Valida el formato del número de teléfono
     */
    validatePhoneNumber(phoneNumber, messageElementId) {
        const messageElement = document.getElementById(messageElementId);
        if (!messageElement) return;
        
        // Limpiar mensaje anterior
        messageElement.textContent = '';
        messageElement.style.display = 'none';
        
        // Validar formato peruano: +51 seguido de 9 dígitos
        const peruPhoneRegex = /^\+51\d{9}$/;
        
        if (phoneNumber === '+51') {
            // Estado inicial válido
            return true;
        }
        
        if (!peruPhoneRegex.test(phoneNumber)) {
            messageElement.textContent = 'Formato inválido. Debe ser +51 seguido de 9 dígitos (ej: +51987654321)';
            messageElement.style.color = '#dc3545';
            messageElement.style.display = 'block';
            return false;
        }
        
        messageElement.textContent = 'Formato válido';
        messageElement.style.color = '#28a745';
        messageElement.style.display = 'block';
        return true;
    }
}

// Exportar para uso global
window.EmergencyNumberController = EmergencyNumberController;
// Mantener compatibilidad con el nombre anterior
window.EmergencyContactController = EmergencyNumberController;

// Auto-inicialización solo si estamos en la página de configuración
document.addEventListener('DOMContentLoaded', () => {
    // Verificar si estamos en la página de configuración buscando elementos específicos
    const emergencyElements = document.getElementById('emergency-call-btn') || 
                             document.getElementById('card-emergencia') || 
                             document.getElementById('emergency-inline-form');
    
    // Solo auto-inicializar si no existe una instancia global
    if (emergencyElements && !window.emergencyNumberController && !window.emergencyContactController) {
        window.emergencyNumberController = new EmergencyNumberController();
        console.log('🚨 EmergencyNumberController auto-inicializado');
    }
});
