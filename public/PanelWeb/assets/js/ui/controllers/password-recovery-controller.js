// Controlador dedicado para la funcionalidad de recuperación de contraseñas
class PasswordRecoveryController {
    constructor() {
        console.log('Inicializando PasswordRecoveryController...');
        
        // Elementos para recuperar contraseña
        this.forgotPasswordBtn = document.getElementById('forgot-password-btn');
        this.forgotPasswordModal = document.getElementById('forgot-password-modal');
        this.closeModalBtn = document.getElementById('close-modal');
        this.forgotPasswordForm = document.getElementById('forgot-password-form');
        this.recoveryContactInput = document.getElementById('recovery-contact');
        this.recoveryMessage = document.getElementById('recovery-message');
        
        // Verificar que los elementos críticos existan
        console.log('Elementos encontrados:', {
            forgotPasswordBtn: !!this.forgotPasswordBtn,
            forgotPasswordModal: !!this.forgotPasswordModal,
            closeModalBtn: !!this.closeModalBtn,
            recoveryContactInput: !!this.recoveryContactInput
        });
        
        this.recoveryService = new PasswordRecoveryService();
        
        // Elementos del selector de tipo de contacto
        this.contactTypeRadios = document.querySelectorAll('input[name="contactType"]');
          // Elementos para verificación de código
        this.verifyCodeModal = document.getElementById('verify-code-modal');
        this.backToRecoveryBtn = document.getElementById('back-to-recovery');
        this.verifyCodeForm = document.getElementById('verify-code-form');
        this.verificationCodeInput = document.getElementById('verification-code');
        this.contactDisplay = document.getElementById('contact-display');
        this.resendCodeBtn = document.getElementById('resend-code-btn');
        this.resendTimer = document.getElementById('resend-timer');
        this.verificationMessage = document.getElementById('verification-message');
        
        // Elementos para cambio de contraseña
        this.changePasswordModal = document.getElementById('change-password-modal');
        this.backToVerificationBtn = document.getElementById('back-to-verification');
        this.changePasswordForm = document.getElementById('change-password-form');
        this.newPasswordInput = document.getElementById('new-password');
        this.repeatPasswordInput = document.getElementById('repeat-password');
        this.newPasswordEye = document.getElementById('new-password-eye');
        this.repeatPasswordEye = document.getElementById('repeat-password-eye');
        this.changePasswordMessage = document.getElementById('change-password-message');
          // Variables para el flujo de verificación
        this.currentUserId = null;
        this.currentContactValue = null;
        this.currentContactType = null;
        this.currentToken = null;
        this.resendTimeout = null;
        this.resendCountdown = 60; // 60 segundos
        
        this.init();
    }    init() {
        // Configurar funcionalidad de recuperar contraseña
        this.setupPasswordRecovery();
        
        // Configurar funcionalidad de verificación de código
        this.setupCodeVerification();
        
        // Configurar funcionalidad de cambio de contraseña
        this.setupChangePassword();
    }setupPasswordRecovery() {
        console.log('Configurando eventos de recuperación de contraseña...');
        
        // Abrir modal al hacer clic en "¿Olvidaste tu contraseña?"
        if (this.forgotPasswordBtn) {
            console.log('Añadiendo event listener al botón de contraseña olvidada');
            this.forgotPasswordBtn.addEventListener('click', (e) => {
                console.log('Click en botón de contraseña olvidada detectado');
                e.preventDefault();
                this.openRecoveryModal();
            });
        } else {
            console.error('¡Elemento forgot-password-btn no encontrado!');
        }

        // Cerrar modal
        if (this.closeModalBtn) {
            this.closeModalBtn.addEventListener('click', () => {
                this.closeRecoveryModal();
            });
        }

        // Cerrar modal al hacer clic fuera de él
        if (this.forgotPasswordModal) {
            this.forgotPasswordModal.addEventListener('click', (e) => {
                if (e.target === this.forgotPasswordModal) {
                    this.closeRecoveryModal();
                }
            });
        }

        // Manejar envío del formulario de recuperación
        if (this.forgotPasswordForm) {
            this.forgotPasswordForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.handlePasswordRecovery();
            });
        }

        // Configurar cambio de tipo de contacto
        this.setupContactTypeSelector();
    }

    setupContactTypeSelector() {
        this.contactTypeRadios.forEach(radio => {
            radio.addEventListener('change', () => {
                this.updateContactInput();
            });
        });
        
        // Configurar el input inicial
        this.updateContactInput();
    }

    updateContactInput() {
        const selectedType = document.querySelector('input[name="contactType"]:checked')?.value;
        
        if (selectedType === 'email') {
            this.recoveryContactInput.type = 'email';
            this.recoveryContactInput.placeholder = 'Correo electrónico';
            this.recoveryContactInput.value = '';
        } else if (selectedType === 'phone') {
            this.recoveryContactInput.type = 'tel';
            this.recoveryContactInput.placeholder = 'Número de teléfono';
            this.recoveryContactInput.value = '';
        }
    }    openRecoveryModal() {
        console.log('Intentando abrir modal de recuperación...');
        if (this.forgotPasswordModal) {
            console.log('Modal encontrado, mostrando...');
            this.forgotPasswordModal.style.display = 'flex';
            this.recoveryContactInput.focus();
            this.clearRecoveryMessage();
            this.updateContactInput(); // Asegurar que el input esté configurado correctamente
        } else {
            console.error('¡Modal de recuperación no encontrado!');
        }
    }

    closeRecoveryModal() {
        if (this.forgotPasswordModal) {
            this.forgotPasswordModal.style.display = 'none';
            this.recoveryContactInput.value = '';
            this.clearRecoveryMessage();
            // Resetear a email por defecto
            document.querySelector('input[name="contactType"][value="email"]').checked = true;
            this.updateContactInput();
            // Limpiar datos del flujo de recuperación
            this.resetRecoveryFlow();
        }
    }

    async handlePasswordRecovery() {
        const contactValue = this.recoveryContactInput.value.trim();
        const selectedType = document.querySelector('input[name="contactType"]:checked')?.value;
        
        // Validar campo vacío
        if (!contactValue) {
            const fieldName = selectedType === 'email' ? 'correo electrónico' : 'número de teléfono';
            this.showRecoveryMessage(`Por favor, ingresa tu ${fieldName}.`, 'error');
            return;
        }

        // Validación específica según el tipo
        if (selectedType === 'email') {
            if (!this.recoveryService.validateEmailFormat(contactValue)) {
                this.showRecoveryMessage('Por favor, ingresa un correo electrónico válido.', 'error');
                return;
            }
        } else if (selectedType === 'phone') {
            if (!this.recoveryService.validatePhoneFormat(contactValue)) {
                this.showRecoveryMessage('Por favor, ingresa un número de teléfono válido (9-15 dígitos).', 'error');
                return;
            }
        }

        try {
            this.setRecoveryLoading(true);
            this.clearRecoveryMessage();

            const result = await this.recoveryService.requestPasswordReset(contactValue, selectedType);

            if (result.success) {
                this.showRecoveryMessage(result.message, 'success');
                // Abrir modal de verificación de código después de 1 segundo
                setTimeout(() => {
                    this.closeRecoveryModal();
                    this.openVerifyCodeModal(contactValue, selectedType, result.userId);
                }, 1000);
            } else {
                this.showRecoveryMessage(result.message, 'error');
            }

        } catch (error) {
            console.error('Error en recuperación de contraseña:', error);
            this.showRecoveryMessage('Error interno. Inténtalo más tarde.', 'error');
        } finally {
            this.setRecoveryLoading(false);
        }
    }

    setRecoveryLoading(isLoading) {
        const submitBtn = this.forgotPasswordForm.querySelector('.btn-recovery');
        const btnText = submitBtn.querySelector('.btn-text');
        const btnLoading = submitBtn.querySelector('.btn-loading');

        submitBtn.disabled = isLoading;
        submitBtn.classList.toggle('loading', isLoading);
        btnText.style.display = isLoading ? 'none' : 'inline';
        btnLoading.style.display = isLoading ? 'inline' : 'none';
    }

    showRecoveryMessage(message, type) {
        if (this.recoveryMessage) {
            this.recoveryMessage.textContent = message;
            this.recoveryMessage.className = type;
            this.recoveryMessage.style.display = 'block';
        }
    }

    clearRecoveryMessage() {
        if (this.recoveryMessage) {
            this.recoveryMessage.style.display = 'none';
            this.recoveryMessage.textContent = '';
            this.recoveryMessage.className = '';
        }
    }

    setupCodeVerification() {
        // Botón para volver al modal de recuperación
        if (this.backToRecoveryBtn) {
            this.backToRecoveryBtn.addEventListener('click', () => {
                this.closeVerifyCodeModal();
                this.openRecoveryModal();
            });
        }

        // Cerrar modal al hacer clic fuera de él
        if (this.verifyCodeModal) {
            this.verifyCodeModal.addEventListener('click', (e) => {
                if (e.target === this.verifyCodeModal) {
                    this.closeVerifyCodeModal();
                }
            });
        }

        // Manejar envío del formulario de verificación
        if (this.verifyCodeForm) {
            this.verifyCodeForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.handleCodeVerification();
            });
        }

        // Formatear automáticamente el código mientras se escribe
        if (this.verificationCodeInput) {
            this.verificationCodeInput.addEventListener('input', (e) => {
                e.target.value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            });
        }

        // Botón de reenviar código
        if (this.resendCodeBtn) {
            this.resendCodeBtn.addEventListener('click', async () => {
                await this.handleResendCode();
            });
        }
    }

    async handleCodeVerification() {
        const code = this.verificationCodeInput.value.trim();
        
        // Validar código vacío
        if (!code) {
            this.showVerificationMessage('Por favor, ingresa el código de verificación.', 'error');
            return;
        }

        // Validar formato del código
        if (!this.recoveryService.validateCodeFormat(code)) {
            this.showVerificationMessage('El código debe tener entre 4 y 8 caracteres alfanuméricos.', 'error');
            return;
        }

        if (!this.currentUserId) {
            this.showVerificationMessage('Error interno. Vuelve a solicitar la recuperación.', 'error');
            return;
        }

        try {
            this.setVerificationLoading(true);
            this.clearVerificationMessage();

            const result = await this.recoveryService.verifyRecoveryCode(this.currentUserId, code);            if (result.success) {
                this.showVerificationMessage('¡Código verificado correctamente!', 'success');
                
                // Cerrar modal de verificación y abrir modal de cambio de contraseña
                setTimeout(() => {
                    this.closeVerifyCodeModal();
                    this.openChangePasswordModal(result.token);
                }, 1500);
            } else {
                this.showVerificationMessage(result.message, 'error');
            }

        } catch (error) {
            console.error('Error en verificación de código:', error);
            this.showVerificationMessage('Error interno. Inténtalo más tarde.', 'error');
        } finally {
            this.setVerificationLoading(false);
        }
    }

    async handleResendCode() {
        try {
            console.log('Solicitando reenvío de código...');
              // Verificar que tenemos los datos necesarios
            if (!this.currentUserId || !this.currentContactValue || !this.currentContactType) {
                this.showVerificationMessage('Error: No se pudieron recuperar los datos del usuario. Reinicia el proceso.', 'error');
                return;
            }
            
            // Mostrar estado de carga
            this.resendCodeBtn.disabled = true;
            this.resendCodeBtn.textContent = 'Reenviando...';
            
            // Usar el método específico para reenvío con todos los parámetros necesarios
            const result = await this.recoveryService.resendVerificationCode(this.currentUserId, this.currentContactValue, this.currentContactType);
            
            if (result.success) {
                this.showVerificationMessage('Código reenviado exitosamente. Revisa tu bandeja de entrada.', 'success');
                // Reiniciar el contador
                this.startResendCountdown();
            } else {
                this.showVerificationMessage(result.message, 'error');
                // Rehabilitar el botón después de 3 segundos en caso de error
                setTimeout(() => {
                    this.resendCodeBtn.disabled = false;
                    this.resendCodeBtn.textContent = 'Reenviar código';
                }, 3000);
            }
            
        } catch (error) {
            console.error('Error al reenviar código:', error);
            this.showVerificationMessage('Error inesperado al reenviar código. Inténtalo de nuevo.', 'error');
            
            // Rehabilitar el botón después de 3 segundos
            setTimeout(() => {
                this.resendCodeBtn.disabled = false;
                this.resendCodeBtn.textContent = 'Reenviar código';
            }, 3000);
        }
    }

    openVerifyCodeModal(contactValue, contactType, userId) {
        // Guardar datos para el flujo de verificación
        this.currentContactValue = contactValue;
        this.currentContactType = contactType;
        this.currentUserId = userId;

        // Actualizar la pantalla con el contacto
        if (this.contactDisplay) {
            this.contactDisplay.textContent = contactValue;
        }

        // Limpiar campos
        this.verificationCodeInput.value = '';
        this.clearVerificationMessage();

        // Mostrar modal
        if (this.verifyCodeModal) {
            this.verifyCodeModal.style.display = 'flex';
            this.verificationCodeInput.focus();
            this.startResendCountdown();
        }
    }

    closeVerifyCodeModal() {
        if (this.verifyCodeModal) {
            this.verifyCodeModal.style.display = 'none';
        }
        
        if (this.resendTimeout) {
            clearInterval(this.resendTimeout);
            this.resendTimeout = null;
        }
    }

    startResendCountdown() {
        this.resendCountdown = 60;
        this.resendCodeBtn.disabled = true;
        this.resendTimer.style.display = 'inline';
        
        this.resendTimeout = setInterval(() => {
            this.resendCountdown--;
            this.resendTimer.textContent = `(${this.resendCountdown}s)`;
            
            if (this.resendCountdown <= 0) {
                clearInterval(this.resendTimeout);
                this.resendTimeout = null;
                this.resendCodeBtn.disabled = false;
                this.resendTimer.style.display = 'none';
            }
        }, 1000);
    }

    setVerificationLoading(isLoading) {
        const submitBtn = this.verifyCodeForm.querySelector('.btn-verify');
        const btnText = submitBtn.querySelector('.btn-text');
        const btnLoading = submitBtn.querySelector('.btn-loading');

        submitBtn.disabled = isLoading;
        submitBtn.classList.toggle('loading', isLoading);
        btnText.style.display = isLoading ? 'none' : 'inline';
        btnLoading.style.display = isLoading ? 'inline' : 'none';
    }

    showVerificationMessage(message, type) {
        if (this.verificationMessage) {
            this.verificationMessage.textContent = message;
            this.verificationMessage.className = type;
            this.verificationMessage.style.display = 'block';
        }
    }

    clearVerificationMessage() {
        if (this.verificationMessage) {
            this.verificationMessage.style.display = 'none';
            this.verificationMessage.textContent = '';
            this.verificationMessage.className = '';
        }
    }

    setupChangePassword() {
        // Botón para volver al modal de verificación
        if (this.backToVerificationBtn) {
            this.backToVerificationBtn.addEventListener('click', () => {
                this.closeChangePasswordModal();
                this.openVerifyCodeModal(this.currentContactValue, this.currentContactType, this.currentUserId);
            });
        }

        // Cerrar modal al hacer clic fuera de él
        if (this.changePasswordModal) {
            this.changePasswordModal.addEventListener('click', (e) => {
                if (e.target === this.changePasswordModal) {
                    this.closeChangePasswordModal();
                }
            });
        }

        // Manejar envío del formulario de cambio de contraseña
        if (this.changePasswordForm) {
            this.changePasswordForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.handleChangePassword();
            });
        }

        // Mostrar/ocultar contraseña nueva
        if (this.newPasswordEye) {
            this.newPasswordEye.addEventListener('click', () => {
                this.togglePasswordVisibility(this.newPasswordInput, this.newPasswordEye);
            });
        }

        // Mostrar/ocultar repetir contraseña
        if (this.repeatPasswordEye) {
            this.repeatPasswordEye.addEventListener('click', () => {
                this.togglePasswordVisibility(this.repeatPasswordInput, this.repeatPasswordEye);
            });
        }

        // Validación en tiempo real de la contraseña
        if (this.newPasswordInput) {
            this.newPasswordInput.addEventListener('input', () => {
                this.validatePasswordRequirements();
            });
        }

        if (this.repeatPasswordInput) {
            this.repeatPasswordInput.addEventListener('input', () => {
                this.validatePasswordMatch();
            });
        }
    }

    openChangePasswordModal(token) {
        this.currentToken = token;
        
        // Limpiar campos
        if (this.newPasswordInput) this.newPasswordInput.value = '';
        if (this.repeatPasswordInput) this.repeatPasswordInput.value = '';
        this.clearChangePasswordMessage();
        this.resetPasswordRequirements();

        // Mostrar modal
        if (this.changePasswordModal) {
            this.changePasswordModal.style.display = 'flex';
            this.newPasswordInput.focus();
        }
    }

    closeChangePasswordModal() {
        if (this.changePasswordModal) {
            this.changePasswordModal.style.display = 'none';
        }
    }

    async handleChangePassword() {
        const newPassword = this.newPasswordInput.value;
        const repeatPassword = this.repeatPasswordInput.value;

        // Validar campos vacíos
        if (!newPassword || !repeatPassword) {
            this.showChangePasswordMessage('Por favor, completa ambos campos de contraseña.', 'error');
            return;
        }

        // Validar que las contraseñas coincidan
        if (newPassword !== repeatPassword) {
            this.showChangePasswordMessage('Las contraseñas no coinciden.', 'error');
            return;
        }

        // Validar fortaleza de la contraseña
        const validation = this.recoveryService.validatePasswordStrength(newPassword);
        if (!validation.isValid) {
            this.showChangePasswordMessage('La contraseña no cumple con todos los requisitos de seguridad.', 'error');
            return;
        }        if (!this.currentUserId) {
            this.showChangePasswordMessage('Error interno. Vuelve a solicitar la recuperación.', 'error');
            return;
        }

        if (!this.currentToken) {
            this.showChangePasswordMessage('Error interno. Token de autorización no encontrado.', 'error');
            return;
        }

        try {
            this.setChangePasswordLoading(true);
            this.clearChangePasswordMessage();

            const result = await this.recoveryService.changePassword(this.currentUserId, newPassword, repeatPassword, this.currentToken);            if (result.success) {
                this.showChangePasswordMessage('¡Contraseña cambiada exitosamente!', 'success');
                
                // Cerrar modal y resetear flujo después de 1.5 segundos
                setTimeout(() => {
                    this.closeChangePasswordModal();
                    this.resetRecoveryFlow();
                    
                    // Mostrar notificación personalizada
                    this.showSuccessToast('Contraseña cambiada con éxito, ya puedes iniciar sesión');
                }, 1500);
            } else {
                this.showChangePasswordMessage(result.message, 'error');
            }

        } catch (error) {
            console.error('Error al cambiar contraseña:', error);
            this.showChangePasswordMessage('Error interno. Inténtalo más tarde.', 'error');
        } finally {
            this.setChangePasswordLoading(false);
        }
    }

    togglePasswordVisibility(input, eyeIcon) {
        if (input.type === 'password') {
            input.type = 'text';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    }

    validatePasswordRequirements() {
        const password = this.newPasswordInput.value;
        const validation = this.recoveryService.validatePasswordStrength(password);
        
        // Actualizar indicadores visuales
        const requirements = {
            'length-req': validation.requirements.length,
            'uppercase-req': validation.requirements.uppercase,
            'lowercase-req': validation.requirements.lowercase,
            'number-req': validation.requirements.number,
            'special-req': validation.requirements.special
        };

        Object.entries(requirements).forEach(([id, isValid]) => {
            const element = document.getElementById(id);
            if (element) {
                element.classList.toggle('valid', isValid);
                element.classList.toggle('invalid', !isValid);
            }
        });
    }

    validatePasswordMatch() {
        const newPassword = this.newPasswordInput.value;
        const repeatPassword = this.repeatPasswordInput.value;
        
        if (repeatPassword.length > 0) {
            if (newPassword === repeatPassword) {
                this.repeatPasswordInput.classList.remove('error');
                this.repeatPasswordInput.classList.add('success');
            } else {
                this.repeatPasswordInput.classList.remove('success');
                this.repeatPasswordInput.classList.add('error');
            }
        } else {
            this.repeatPasswordInput.classList.remove('success', 'error');
        }
    }

    resetPasswordRequirements() {
        const requirementIds = ['length-req', 'uppercase-req', 'lowercase-req', 'number-req', 'special-req'];
        requirementIds.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.classList.remove('valid', 'invalid');
            }
        });

        if (this.newPasswordInput) {
            this.newPasswordInput.classList.remove('success', 'error');
        }
        if (this.repeatPasswordInput) {
            this.repeatPasswordInput.classList.remove('success', 'error');
        }
    }

    setChangePasswordLoading(isLoading) {
        const submitBtn = this.changePasswordForm.querySelector('.btn-change-password');
        const btnText = submitBtn.querySelector('.btn-text');
        const btnLoading = submitBtn.querySelector('.btn-loading');

        submitBtn.disabled = isLoading;
        submitBtn.classList.toggle('loading', isLoading);
        btnText.style.display = isLoading ? 'none' : 'inline';
        btnLoading.style.display = isLoading ? 'inline' : 'none';
    }

    showChangePasswordMessage(message, type) {
        if (this.changePasswordMessage) {
            this.changePasswordMessage.textContent = message;
            this.changePasswordMessage.className = type;
            this.changePasswordMessage.style.display = 'block';
        }
    }

    clearChangePasswordMessage() {
        if (this.changePasswordMessage) {
            this.changePasswordMessage.style.display = 'none';
            this.changePasswordMessage.textContent = '';
            this.changePasswordMessage.className = '';
        }
    }    resetRecoveryFlow() {
        this.currentUserId = null;
        this.currentContactValue = null;
        this.currentContactType = null;
        this.currentToken = null;
          if (this.resendTimeout) {
            clearInterval(this.resendTimeout);
            this.resendTimeout = null;
        }
    }

    // Método para mostrar notificación toast personalizada
    showSuccessToast(message) {
        const toast = document.getElementById('recovery-toast');
        const toastMessage = document.getElementById('recovery-toast-message');
        
        if (toast && toastMessage) {
            // Configurar el mensaje
            toastMessage.textContent = message;
            
            // Mostrar la notificación
            toast.classList.add('show');
            
            // Ocultar automáticamente después de 4 segundos
            setTimeout(() => {
                toast.classList.remove('show');
            }, 4000);
        } else {
            // Fallback en caso de que no se encuentre el elemento
            console.warn('Toast element not found, using alert fallback');
            alert(message);
        }
    }
}

// Exportar la clase para que esté disponible en otros archivos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PasswordRecoveryController;
} else {
    // Para navegadores sin soporte de módulos
    window.PasswordRecoveryController = PasswordRecoveryController;
}

// Inicializar el controlador cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM cargado, inicializando PasswordRecoveryController...');
    try {
        new PasswordRecoveryController();
        console.log('PasswordRecoveryController inicializado exitosamente');
    } catch (error) {
        console.error('Error al inicializar PasswordRecoveryController:', error);
    }
});
