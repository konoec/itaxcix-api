/**
 * Controlador para la creación de usuarios administradores
 * Maneja el modal de creación y la validación de formularios
 */
class UserCreateController {
    constructor() {
        // Estado del controlador
        this.isModalOpen = false;
        this.isSubmitting = false;
        
        // Referencias a elementos del DOM
        this.modal = null;
        this.form = null;
        this.submitButton = null;
        
        // Inicializar automáticamente
        try {
            this.init();
        } catch (error) {
            console.error('❌ Error al inicializar UserCreateController:', error);
        }
    }

    /**
     * Inicializa el controlador
     */
    init() {
        try {
            // Obtener referencias a elementos del DOM
            this.setupDOMReferences();
            
            // Configurar event listeners
            this.setupEventListeners();
        } catch (error) {
            console.error('❌ Error al inicializar UserCreateController:', error);
        }
    }

    /**
     * Configura las referencias a elementos del DOM
     */
    setupDOMReferences() {
        this.modal = document.getElementById('user-create-modal');
        this.form = document.getElementById('user-create-form');
        this.submitButton = document.getElementById('save-user-btn');
    }

    /**
     * Configura los event listeners
     */
    setupEventListeners() {
        // Event listener para abrir el modal
        const createUserBtn = document.getElementById('create-user-btn');
        if (createUserBtn) {
            createUserBtn.addEventListener('click', () => this.openModal());
        }

        // Event listener para el formulario (submit)
        if (this.form) {
            this.form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleSubmit();
            });
        }

        // Botón submit del modal (backup)
        if (this.submitButton) {
            this.submitButton.addEventListener('click', (e) => {
                if (e.target.type !== 'submit') {
                    e.preventDefault();
                    this.handleSubmit();
                }
            });
        }

        // Toggle password visibility
        const togglePassword = document.getElementById('toggle-password');
        if (togglePassword) {
            togglePassword.addEventListener('click', () => this.togglePasswordVisibility());
        }

        // Event listener para cuando se muestre el modal (Bootstrap)
        if (this.modal) {
            // SOLO manejar el evento shown - eliminar hidden que causa problemas
            this.modal.addEventListener('shown.bs.modal', () => {
                try {
                    this.isModalOpen = true;
                    this.focusFirstField();
                } catch (error) {
                    console.error('❌ Error en shown.bs.modal:', error);
                }
            });
        }
    }

    /**
     * Abre el modal de creación
     */
    openModal() {
        if (!this.modal) {
            this.showToast('Error: Modal no disponible', 'error');
            return;
        }

        // Obtener o crear instancia de Bootstrap Modal
        let bootstrapModal = bootstrap.Modal.getInstance(this.modal);
        if (!bootstrapModal) {
            bootstrapModal = new bootstrap.Modal(this.modal);
        }
        
        try {
            bootstrapModal.show();
            // Resetear formulario DESPUÉS de mostrar, igual que en roles-controller
            this.resetForm();
            this.isModalOpen = true;
        } catch (error) {
            console.error('❌ Error mostrando modal:', error);
            return;
        }
        
        // Enfocar el primer campo inmediatamente
        const firstInput = document.getElementById('user-document');
        if (firstInput) {
            firstInput.focus();
        }
    }

    /**
     * Cierra el modal - versión ultra simplificada
     */
    closeModal() {
        // Solo cambiar el estado sin operaciones complejas
        this.isModalOpen = false;
        this.isSubmitting = false;
        
        // Reset básico del formulario
        if (this.form) {
            this.form.reset();
        }
    }

    /**
     * Resetea el formulario - versión simplificada
     */
    resetForm() {
        try {
            if (this.form) {
                this.form.reset();
            }
        } catch (error) {
            // Ignorar errores de reset
        }
    }

    /**
     * Maneja el envío del formulario
     */
    async handleSubmit() {
        if (this.isSubmitting) {
            return;
        }

        // Validar formulario
        if (!this.validateForm()) {
            this.showToast('Por favor, corrija los errores en el formulario', 'error');
            return;
        }

        this.isSubmitting = true;
        this.updateSubmitButton();

        try {
            // Recopilar datos del formulario
            const userData = this.collectFormData();

            // Verificar que UserService esté disponible
            if (typeof UserService === 'undefined') {
                throw new Error('UserService no está disponible');
            }

            // Enviar datos al servicio principal
            const response = await UserService.createUser(userData);

            if (response.success) {
                // Usar el mensaje específico de la API
                const message = response.data?.message || response.message || 'Usuario creado exitosamente';
                this.showToast(message, 'success');
                
                // Seguir el mismo patrón que roles-controller
                const bootstrapModal = bootstrap.Modal.getInstance(this.modal);
                if (bootstrapModal) {
                    bootstrapModal.hide();
                }
                
                // Reset del formulario y estado DESPUÉS de cerrar
                this.resetForm();
                this.isModalOpen = false;
                
                // NO refrescar automáticamente - dejar que el usuario lo haga manualmente
                console.log('✅ Usuario creado exitosamente - refresque manualmente la tabla');
            } else {
                // Manejar errores de validación del servidor
                if (response.errors) {
                    this.handleFormErrors(response.errors);
                }
                this.showToast(response.message || 'Error al crear el usuario', 'error');
            }

        } catch (error) {
            console.error('❌ Error al crear usuario:', error);
            this.showToast('Error de conexión al crear el usuario', 'error');
        } finally {
            this.isSubmitting = false;
            this.updateSubmitButton();
        }
    }

    /**
     * Recopila los datos del formulario
     */
    collectFormData() {
        return {
            document: document.getElementById('user-document')?.value?.trim() || '',
            email: document.getElementById('user-email')?.value?.trim() || '',
            password: document.getElementById('user-password')?.value || '',
            area: document.getElementById('user-area')?.value?.trim() || '',
            position: document.getElementById('user-position')?.value?.trim() || ''
        };
    }

    /**
     * Valida todo el formulario - SOLO validación básica sin manipulación del DOM
     */
    validateForm() {
        // Validación muy básica - solo verificar que los campos tengan valor
        const requiredFields = ['user-document', 'user-email', 'user-password', 'user-area', 'user-position'];
        
        for (const fieldId of requiredFields) {
            const input = document.getElementById(fieldId);
            if (!input || !input.value.trim()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Maneja los errores del formulario desde el servidor - simplificado
     */
    handleFormErrors(errors) {
        // Solo logear los errores, no manipular el DOM
        console.log('Errores del servidor:', errors);
    }

    /**
     * Actualiza el estado del botón de envío
     */
    updateSubmitButton() {
        if (!this.submitButton) return;
        
        const btnText = this.submitButton.querySelector('.btn-text');
        const btnIcon = this.submitButton.querySelector('svg');
        
        if (this.isSubmitting) {
            this.submitButton.disabled = true;
            
            // Actualizar icono a spinner
            if (btnIcon) {
                btnIcon.outerHTML = `
                    <div class="spinner-border spinner-border-sm me-1" role="status" style="width: 18px; height: 18px;">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                `;
            }
            
            // Actualizar texto
            if (btnText) {
                btnText.textContent = 'Creando...';
            } else {
                // Fallback si no hay estructura específica
                this.submitButton.innerHTML = `
                    <div class="spinner-border spinner-border-sm me-2" role="status" style="width: 18px; height: 18px;">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    Creando...
                `;
            }
        } else {
            this.submitButton.disabled = false;
            
            // Restaurar contenido original
            this.submitButton.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy me-1" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-16a2 2 0 0 1 2 -2"></path>
                    <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                    <path d="M14 4l0 4l-6 0l0 -4"></path>
                </svg>
                <span class="btn-text">Crear Usuario</span>
            `;
        }
    }

    /**
     * Toggle de visibilidad de contraseña
     */
    togglePasswordVisibility() {
        const passwordInput = document.getElementById('user-password');
        const toggleButton = document.getElementById('toggle-password');
        const toggleIcon = toggleButton?.querySelector('svg');
        
        if (passwordInput && toggleIcon) {
            const currentType = passwordInput.getAttribute('type');
            const newType = currentType === 'password' ? 'text' : 'password';
            
            passwordInput.setAttribute('type', newType);
            
            // Cambiar el icono SVG
            if (newType === 'text') {
                // Cambiar a icono de ojo cerrado (eye-off)
                toggleIcon.innerHTML = `
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M10.585 10.587a2 2 0 0 0 2.829 2.828"></path>
                    <path d="M16.681 16.673a8.717 8.717 0 0 1 -4.681 1.327c-3.6 0 -6.6 -2 -9 -6c1.272 -2.12 2.712 -3.678 4.32 -4.674m2.86 -1.146a9.055 9.055 0 0 1 1.82 -.18c3.6 0 6.6 2 9 6c-.666 1.11 -1.379 2.067 -2.138 2.87"></path>
                    <path d="M3 3l18 18"></path>
                `;
                toggleButton.setAttribute('title', 'Ocultar contraseña');
            } else {
                // Cambiar a icono de ojo abierto (eye)
                toggleIcon.innerHTML = `
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"></path>
                    <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"></path>
                `;
                toggleButton.setAttribute('title', 'Mostrar contraseña');
            }
        }
    }

    /**
     * Enfoca el primer campo del formulario
     */
    focusFirstField() {
        const firstField = document.getElementById('user-document');
        if (firstField) {
            firstField.focus();
        }
    }

    /**
     * Muestra un mensaje toast
     */
    showToast(message, type = 'info') {
        console.log(`🔔 UserCreateController.showToast: ${type} - ${message}`);
        
        // Utilizar el sistema global de toasts si está disponible
        if (window.GlobalToast) {
            window.GlobalToast.show(message, type);
        } else {
            console.log(`Toast ${type}: ${message}`);
        }
    }
}

// Hacer disponible globalmente
if (typeof window !== 'undefined') {
    window.UserCreateController = UserCreateController;
    
    // Solo crear instancia si no existe y estamos en la página correcta
    if (!window.userCreateController && document.getElementById('user-create-modal')) {
        try {
            window.userCreateController = new UserCreateController();
        } catch (error) {
            console.error('❌ Error creando instancia de UserCreateController:', error);
        }
    }
}
