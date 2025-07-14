/**
 * Controlador para crear departamentos
 * Maneja la interfaz de usuario para crear nuevos departamentos
 */
class DepartmentCreateController {
    constructor() {
        console.log('🆕 DepartmentCreateController constructor ejecutado');
        
        this.form = null;
        this.modal = null;
        this.isSubmitting = false;
        this.previewUpdateTimeout = null;
    }

    /**
     * Inicializa el controlador
     */
    init() {
        console.log('🆕 Inicializando DepartmentCreateController...');
        try {
            this.form = document.getElementById('createDepartmentForm');
            this.modal = document.getElementById('createDepartmentModal');
            
            if (!this.form || !this.modal) {
                console.error('❌ Elementos del modal no encontrados');
                return;
            }

            this.setupEventListeners();
            this.setupValidation();
            console.log('✅ DepartmentCreateController inicializado correctamente');
            
        } catch (error) {
            console.error('❌ Error al inicializar DepartmentCreateController:', error);
        }
    }

    /**
     * Configura los event listeners
     */
    setupEventListeners() {
        console.log('🔧 Configurando event listeners para crear departamento');

        // Submit del formulario
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleSubmit();
        });

        // Evento al abrir el modal
        this.modal.addEventListener('show.bs.modal', () => {
            this.resetForm();
            this.loadSuggestions();
        });

        // Evento al cerrar el modal
        this.modal.addEventListener('hidden.bs.modal', () => {
            this.resetForm();
        });

        // Preview en tiempo real
        const nameInput = document.getElementById('departmentName');
        const ubigeoInput = document.getElementById('departmentUbigeo');

        if (nameInput) {
            nameInput.addEventListener('input', () => {
                this.updatePreview();
                this.validateField(nameInput);
            });
        }

        if (ubigeoInput) {
            ubigeoInput.addEventListener('input', () => {
                this.updatePreview();
                this.validateField(ubigeoInput);
            });
        }

        // Validación en tiempo real
        this.form.addEventListener('input', (e) => {
            if (e.target.classList.contains('form-control')) {
                this.validateField(e.target);
            }
        });
    }

    /**
     * Configura la validación del formulario
     */
    setupValidation() {
        // Validación de Bootstrap
        this.form.classList.add('needs-validation');
        
        const nameInput = document.getElementById('departmentName');
        const ubigeoInput = document.getElementById('departmentUbigeo');

        // Configurar validadores personalizados
        if (nameInput) {
            nameInput.addEventListener('blur', () => this.validateNameField(nameInput));
        }

        if (ubigeoInput) {
            ubigeoInput.addEventListener('blur', () => this.validateUbigeoField(ubigeoInput));
        }
    }

    /**
     * Actualiza la vista previa
     */
    updatePreview() {
        clearTimeout(this.previewUpdateTimeout);
        this.previewUpdateTimeout = setTimeout(() => {
            const nameValue = document.getElementById('departmentName').value.trim();
            const ubigeoValue = document.getElementById('departmentUbigeo').value.trim();

            const previewName = document.getElementById('previewName');
            const previewUbigeo = document.getElementById('previewUbigeo');

            if (previewName) {
                previewName.textContent = nameValue || 'Nombre del departamento';
                previewName.className = nameValue ? 'fw-bold fs-4 text-dark' : 'fw-bold fs-4 text-muted';
            }

            if (previewUbigeo) {
                previewUbigeo.textContent = ubigeoValue || '--';
                previewUbigeo.className = ubigeoValue ? 'badge bg-info' : 'badge bg-info-lt';
            }
        }, 300);
    }

    /**
     * Valida un campo específico
     */
    validateField(field) {
        const isValid = field.checkValidity();
        
        if (isValid) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
        } else {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
        }

        return isValid;
    }

    /**
     * Valida el campo nombre
     */
    async validateNameField(nameInput) {
        const value = nameInput.value.trim();
        const feedback = nameInput.nextElementSibling;

        // Validaciones básicas
        if (!value) {
            this.setFieldError(nameInput, 'El nombre es requerido');
            return false;
        }

        if (value.length < 2) {
            this.setFieldError(nameInput, 'El nombre debe tener al menos 2 caracteres');
            return false;
        }

        if (value.length > 100) {
            this.setFieldError(nameInput, 'El nombre no puede exceder 100 caracteres');
            return false;
        }

        if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(value)) {
            this.setFieldError(nameInput, 'Solo se permiten letras y espacios');
            return false;
        }

        this.setFieldSuccess(nameInput, 'Nombre válido');
        return true;
    }

    /**
     * Valida el campo ubigeo
     */
    async validateUbigeoField(ubigeoInput) {
        const value = ubigeoInput.value.trim();

        if (!value) {
            this.setFieldError(ubigeoInput, 'El código ubigeo es requerido');
            return false;
        }

        if (!/^[0-9]{2}$/.test(value)) {
            this.setFieldError(ubigeoInput, 'Debe ser un número de exactamente 2 dígitos');
            return false;
        }

        this.setFieldSuccess(ubigeoInput, 'Código válido');
        return true;
    }

    /**
     * Establece error en un campo
     */
    setFieldError(field, message) {
        field.classList.remove('is-valid');
        field.classList.add('is-invalid');
        const feedback = field.nextElementSibling;
        if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.textContent = message;
        }
    }

    /**
     * Establece éxito en un campo
     */
    setFieldSuccess(field, message = '') {
        field.classList.remove('is-invalid');
        field.classList.add('is-valid');
    }

    /**
     * Maneja el envío del formulario
     */
    async handleSubmit() {
        if (this.isSubmitting) {
            console.log('⏳ Ya hay un envío en progreso');
            return;
        }

        console.log('📝 Procesando envío del formulario');

        // Validar formulario completo
        if (!this.validateForm()) {
            console.log('❌ Formulario inválido');
            return;
        }

        this.isSubmitting = true;
        this.showSubmittingState();

        try {
            // Obtener datos directamente de los campos del formulario
            const nameInput = document.getElementById('departmentName');
            const ubigeoInput = document.getElementById('departmentUbigeo');
            
            const departmentData = {
                name: (nameInput?.value || '').trim(),
                ubigeo: (ubigeoInput?.value || '').trim()
            };

            // Validar que los datos no estén vacíos
            if (!departmentData.name) {
                throw new Error('El nombre del departamento es requerido');
            }

            if (!departmentData.ubigeo) {
                throw new Error('El código ubigeo es requerido');
            }

            console.log('📤 Enviando datos:', departmentData);

            // Verificar duplicados antes de enviar
            const exists = await DepartmentCreateService.checkDepartmentExists(
                departmentData.name, 
                departmentData.ubigeo
            );

            if (exists) {
                throw new Error('Ya existe un departamento con ese nombre o código ubigeo');
            }

            // Crear departamento
            const response = await DepartmentCreateService.createDepartment(departmentData);
            const transformedData = DepartmentCreateService.transformCreateResponse(response);

            console.log('✅ Departamento creado:', transformedData);

            // Mostrar éxito
            this.showSuccess(transformedData.message);

            // Cerrar modal
            const modalInstance = bootstrap.Modal.getInstance(this.modal);
            modalInstance.hide();

            // Actualizar lista principal
            if (window.departmentsController) {
                await window.departmentsController.loadDepartments();
            }

        } catch (error) {
            console.error('❌ Error al crear departamento:', error);
            this.showError(error.message);
        } finally {
            this.isSubmitting = false;
            this.hideSubmittingState();
        }
    }

    /**
     * Valida todo el formulario
     */
    validateForm() {
        const nameInput = document.getElementById('departmentName');
        const ubigeoInput = document.getElementById('departmentUbigeo');

        const nameValid = this.validateField(nameInput);
        const ubigeoValid = this.validateField(ubigeoInput);

        return nameValid && ubigeoValid;
    }

    /**
     * Muestra estado de envío
     */
    showSubmittingState() {
        const submitBtn = document.getElementById('saveDepartmentBtn');
        if (submitBtn) {
            submitBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                Creando...
            `;
            submitBtn.disabled = true;
        }

        // Deshabilitar campos del formulario
        const inputs = this.form.querySelectorAll('input, button');
        inputs.forEach(input => {
            if (input.id !== 'saveDepartmentBtn') {
                input.disabled = true;
            }
        });
    }

    /**
     * Oculta estado de envío
     */
    hideSubmittingState() {
        const submitBtn = document.getElementById('saveDepartmentBtn');
        if (submitBtn) {
            submitBtn.innerHTML = `
                <i class="fas fa-save me-1"></i>
                Crear Departamento
            `;
            submitBtn.disabled = false;
        }

        // Rehabilitar campos del formulario
        const inputs = this.form.querySelectorAll('input, button');
        inputs.forEach(input => {
            input.disabled = false;
        });
    }

    /**
     * Resetea el formulario
     */
    resetForm() {
        this.form.reset();
        this.form.classList.remove('was-validated');
        
        // Limpiar clases de validación
        const fields = this.form.querySelectorAll('.form-control');
        fields.forEach(field => {
            field.classList.remove('is-valid', 'is-invalid');
        });

        // Resetear preview
        const previewName = document.getElementById('previewName');
        const previewUbigeo = document.getElementById('previewUbigeo');

        if (previewName) {
            previewName.textContent = 'Nombre del departamento';
            previewName.className = 'fw-bold fs-4 text-muted';
        }

        if (previewUbigeo) {
            previewUbigeo.textContent = '--';
            previewUbigeo.className = 'badge bg-info-lt';
        }
    }

    /**
     * Carga sugerencias de códigos ubigeo
     */
    async loadSuggestions() {
        try {
            const suggestions = await DepartmentCreateService.getSuggestedUbigeoCodes();
            if (suggestions.length > 0) {
                const ubigeoInput = document.getElementById('departmentUbigeo');
                const hint = ubigeoInput.parentElement.nextElementSibling;
                if (hint && hint.classList.contains('form-hint')) {
                    hint.innerHTML = `
                        <i class="fas fa-lightbulb me-1"></i>
                        Códigos disponibles: ${suggestions.slice(0, 3).join(', ')}...
                    `;
                }
            }
        } catch (error) {
            console.warn('⚠️ No se pudieron cargar sugerencias:', error);
        }
    }

    /**
     * Muestra mensaje de éxito
     */
    showSuccess(message) {
        if (window.GlobalToast) {
            GlobalToast.show(message, 'success');
        }
    }

    /**
     * Muestra mensaje de error
     */
    showError(message) {
        if (window.GlobalToast) {
            GlobalToast.show(message, 'error');
        }
    }

    /**
     * Limpia recursos
     */
    destroy() {
        if (this.previewUpdateTimeout) {
            clearTimeout(this.previewUpdateTimeout);
        }
        console.log('🧹 DepartmentCreateController destruido');
    }
}

// Crear instancia global
window.departmentCreateController = new DepartmentCreateController();
