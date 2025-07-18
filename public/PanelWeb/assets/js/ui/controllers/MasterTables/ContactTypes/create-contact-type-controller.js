/**
 * Controlador para el modal de creación de tipos de contacto
 * Maneja el formulario y la comunicación con la API a través de CreateContactTypeService
 */
class CreateContactTypeController {
    /**
     * Inicializa el controlador para crear tipos de contacto
     * @param {string} modalId - ID del elemento modal en el DOM
     * @param {string} formId - ID del formulario dentro del modal
     * @param {Function} onSuccess - Callback que se ejecuta cuando se crea exitosamente
     */
    constructor(modalId, formId, onSuccess) {
        this.modal = document.getElementById(modalId);
        this.form = document.getElementById(formId);
        this.onSuccess = onSuccess;
        this.bsModal = null;
        
        this.init();
    }

    /**
     * Inicializa el controlador configurando los event listeners
     */
    init() {
        if (!this.modal || !this.form) {
            console.error('❌ CreateContactTypeController: Modal o formulario no encontrado');
            return;
        }
        
        // Inicializar el modal de Bootstrap
        this.bsModal = new bootstrap.Modal(this.modal);
        
        // Event listener para el envío del formulario
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
        
        // Restablecer el formulario cuando se cierra el modal
        this.modal.addEventListener('hidden.bs.modal', () => this.form.reset());
        
        console.log('✅ CreateContactTypeController inicializado');
    }

    /**
     * Abre el modal para crear un nuevo tipo de contacto
     */
    open() {
        if (this.bsModal) {
            this.bsModal.show();
        }
    }

    /**
     * Maneja el envío del formulario
     * @param {Event} e - Evento de submit
     */
    async handleSubmit(e) {
        e.preventDefault();
        
        // Obtener datos del formulario
        const name = this.form.querySelector('#contact-type-name').value.trim();
        const active = this.form.querySelector('#contact-type-active').checked;
        
        // Validación básica
        if (!name) {
            this.showError('El nombre del tipo de contacto es obligatorio');
            return;
        }
        
        // Mostrar estado de carga
        this.setLoading(true);
        
        try {
            // Llamar al servicio para crear el tipo de contacto
            const result = await CreateContactTypeService.createContactType({ 
                name, 
                active 
            });
            
            // Procesar resultado exitoso
            if (result && result.success) {
                // Cerrar modal
                this.bsModal.hide();
                
                // Mostrar mensaje de éxito
                this.showToast('Tipo de contacto creado exitosamente', 'success');
                
                // Ejecutar callback de éxito si existe
                if (typeof this.onSuccess === 'function') {
                    this.onSuccess(result.data);
                }
            } else {
                this.showError('No se pudo crear el tipo de contacto');
            }
        } catch (error) {
            console.error('❌ Error al crear tipo de contacto:', error);
            this.showError(error.message || 'Error inesperado al crear el tipo de contacto');
        } finally {
            this.setLoading(false);
        }
    }

    /**
     * Cambia el estado de carga del formulario
     * @param {boolean} loading - Indica si está cargando
     */
    setLoading(loading) {
        const submitBtn = this.form.querySelector('button[type="submit"]');
        if (!submitBtn) return;
        
        if (loading) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Guardando...';
        } else {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Guardar';
        }
    }

    /**
     * Muestra un mensaje de error en el formulario
     * @param {string} message - Mensaje de error
     */
    showError(message) {
        // Seleccionar o crear el elemento de alerta
        let alertElement = this.form.querySelector('.alert-danger');
        
        if (!alertElement) {
            alertElement = document.createElement('div');
            alertElement.className = 'alert alert-danger mt-3';
            alertElement.setAttribute('role', 'alert');
            this.form.prepend(alertElement);
        }
        
        // Mostrar mensaje de error
        alertElement.textContent = message;
        alertElement.style.display = 'block';
        
        // Ocultar después de 5 segundos
        setTimeout(() => {
            alertElement.style.display = 'none';
        }, 5000);
    }

    /**
     * Muestra una notificación toast
     * @param {string} message - Mensaje a mostrar
     * @param {string} type - Tipo de notificación (success, error, warning, info)
     */
    showToast(message, type = 'success') {
        if (window.showToast) {
            window.showToast(message, type);
        } else {
            console.log(`Toast (${type}): ${message}`);
        }
    }
}

// Exportar globalmente
window.CreateContactTypeController = CreateContactTypeController;
