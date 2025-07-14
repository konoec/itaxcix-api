/**
 * Controlador para la actualización de departamentos
 * Maneja la lógica de la interfaz del modal de edición de departamentos
 * Sigue el patrón del modal de empresas con Tabler
 */
class DepartmentUpdateController {
    constructor() {
        console.log('🏗️ Inicializando DepartmentUpdateController...');
        
        // Servicios
        this.updateService = new DepartmentUpdateService();
        
        // Estado del controlador
        this.isUpdating = false;
        this.currentDepartmentId = null;
        
        // Referencias del DOM
        this.initializeElements();
        
        // Event listeners
        this.initializeEventListeners();
    }

    /**
     * Inicializa las referencias a elementos del DOM
     */
    initializeElements() {
        // Modal y formulario
        this.modal = document.getElementById('edit-department-modal');
        this.form = document.getElementById('edit-department-form');
        this.submitBtn = document.getElementById('edit-department-submit');
        
        // Campos del formulario
        this.departmentIdField = document.getElementById('edit-department-id');
        this.nameField = document.getElementById('edit-department-name');
        this.ubigeoField = document.getElementById('edit-department-ubigeo');
        
        console.log('✅ Elementos del DOM inicializados para edición');
    }

    /**
     * Inicializa los event listeners
     */
    initializeEventListeners() {
        // Envío del formulario
        if (this.form) {
            this.form.addEventListener('submit', (e) => this.handleFormSubmit(e));
        }

        // Limpiar formulario al cerrar modal
        if (this.modal) {
            this.modal.addEventListener('hidden.bs.modal', () => this.resetForm());
        }

        // Event listeners para vista previa en tiempo real
        this.setupPreviewListeners();

        console.log('✅ Event listeners inicializados para edición');
    }

    /**
     * Configura los event listeners para la vista previa
     */
    setupPreviewListeners() {
        const nameInput = document.getElementById('edit-department-name');
        const ubigeoInput = document.getElementById('edit-department-ubigeo');

        // Preview en tiempo real
        if (nameInput) {
            nameInput.addEventListener('input', () => this.updatePreview());
        }
        if (ubigeoInput) {
            ubigeoInput.addEventListener('input', () => this.updatePreview());
        }
    }

    /**
     * Actualiza la vista previa del modal
     */
    updatePreview() {
        const nameInput = document.getElementById('edit-department-name');
        const ubigeoInput = document.getElementById('edit-department-ubigeo');
        
        const previewName = document.getElementById('editPreviewDepartmentName');
        const previewUbigeo = document.getElementById('editPreviewDepartmentUbigeo');

        if (previewName) {
            const name = nameInput?.value.trim() || '';
            previewName.textContent = name || 'Vista previa';
        }

        if (previewUbigeo) {
            const ubigeo = ubigeoInput?.value.trim() || '';
            previewUbigeo.textContent = ubigeo || '--';
        }
    }

    /**
     * Abre el modal de edición con los datos del departamento
     * @param {number} departmentId - ID del departamento a editar
     * @param {Object} departmentData - Datos actuales del departamento
     */
    async openEditModal(departmentId, departmentData) {
        console.log('📝 Abriendo modal de edición para departamento:', departmentId, departmentData);

        try {
            // Guardar ID del departamento actual
            this.currentDepartmentId = departmentId;

            // Rellenar el formulario con los datos
            this.populateForm(departmentData);

            // Mostrar el modal
            const bootstrapModal = new bootstrap.Modal(this.modal);
            bootstrapModal.show();

        } catch (error) {
            console.error('❌ Error al abrir modal de edición:', error);
            
            // Mostrar error al usuario
            if (window.GlobalToast) {
                GlobalToast.show(
                    'Error al cargar datos del departamento',
                    'error'
                );
            }
        }
    }

    /**
     * Rellena el formulario con los datos del departamento
     * @param {Object} departmentData - Datos del departamento
     */
    populateForm(departmentData) {
        if (!departmentData) return;

        console.log('📝 Rellenando formulario con datos:', departmentData);

        // Llenar campos
        if (this.departmentIdField) {
            this.departmentIdField.value = departmentData.id || '';
        }

        if (this.nameField) {
            this.nameField.value = departmentData.name || '';
        }

        if (this.ubigeoField) {
            this.ubigeoField.value = departmentData.ubigeo || '';
        }

        // Limpiar errores de validación
        this.clearValidationErrors();

        // Actualizar vista previa después de poblar los datos
        setTimeout(() => this.updatePreview(), 100);
    }

    /**
     * Maneja el envío del formulario
     * @param {Event} event - Evento del formulario
     */
    async handleFormSubmit(event) {
        event.preventDefault();

        if (this.isUpdating) {
            console.log('⏳ Actualización en progreso, ignorando nuevo envío');
            return;
        }

        console.log('📤 Procesando actualización de departamento...');

        try {
            // Obtener datos del formulario
            const formData = new FormData(this.form);
            const departmentData = {
                name: formData.get('name')?.trim(),
                ubigeo: formData.get('ubigeo')?.trim()
            };

            // Validar datos
            const validation = this.updateService.validateDepartmentData(departmentData);
            if (!validation.isValid) {
                this.showValidationErrors(validation.errors);
                return;
            }

            // Mostrar estado de carga
            this.setSubmitButtonLoading(true);
            this.isUpdating = true;

            // Enviar actualización
            const response = await this.updateService.updateDepartment(
                this.currentDepartmentId, 
                departmentData
            );

            console.log('✅ Departamento actualizado exitosamente:', response);

            // Mostrar mensaje de éxito
            if (window.GlobalToast) {
                GlobalToast.show(
                    'Departamento actualizado correctamente',
                    'success'
                );
            }

            // Cerrar modal
            const bootstrapModal = bootstrap.Modal.getInstance(this.modal);
            if (bootstrapModal) {
                bootstrapModal.hide();
            }

            // Recargar la lista de departamentos
            if (window.departmentsController) {
                await window.departmentsController.loadDepartments();
            }

        } catch (error) {
            console.error('❌ Error al actualizar departamento:', error);

            // Mostrar error al usuario
            if (window.GlobalToast) {
                GlobalToast.show(
                    error.message || 'No se pudo actualizar el departamento',
                    'error'
                );
            }

        } finally {
            // Restaurar estado del botón
            this.setSubmitButtonLoading(false);
            this.isUpdating = false;
        }
    }

    /**
     * Muestra errores de validación en el formulario
     * @param {Object} errors - Errores de validación
     */
    showValidationErrors(errors) {
        console.log('⚠️ Mostrando errores de validación:', errors);

        // Limpiar errores previos
        this.clearValidationErrors();

        // Mostrar errores específicos
        Object.keys(errors).forEach(field => {
            const fieldElement = this.form.querySelector(`[name="${field}"]`);
            const errorElement = this.form.querySelector(`#edit-${field}-error`);

            if (fieldElement) {
                fieldElement.classList.add('is-invalid');
            }

            if (errorElement) {
                errorElement.textContent = errors[field];
                errorElement.style.display = 'block';
            }
        });

        // Toast general de error
        if (window.GlobalToast) {
            GlobalToast.show(
                'Por favor corrija los errores marcados en el formulario',
                'error'
            );
        }
    }

    /**
     * Limpia los errores de validación del formulario
     */
    clearValidationErrors() {
        // Remover clases de error
        const invalidFields = this.form.querySelectorAll('.is-invalid');
        invalidFields.forEach(field => field.classList.remove('is-invalid'));

        // Ocultar mensajes de error
        const errorElements = this.form.querySelectorAll('.invalid-feedback');
        errorElements.forEach(element => {
            element.style.display = 'none';
            element.textContent = '';
        });
    }

    /**
     * Controla el estado de carga del botón de envío
     * @param {boolean} loading - Si está en estado de carga
     */
    setSubmitButtonLoading(loading) {
        if (!this.submitBtn) return;

        if (loading) {
            this.submitBtn.disabled = true;
            this.submitBtn.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                Actualizando...
            `;
        } else {
            this.submitBtn.disabled = false;
            this.submitBtn.innerHTML = `
                <i class="fas fa-save me-1"></i>
                Actualizar Departamento
            `;
        }
    }

    /**
     * Resetea el formulario al cerrar el modal
     */
    resetForm() {
        console.log('🔄 Reseteando formulario de edición');

        // Resetear formulario
        if (this.form) {
            this.form.reset();
        }

        // Limpiar errores
        this.clearValidationErrors();

        // Resetear estado
        this.currentDepartmentId = null;
        this.isUpdating = false;

        // Resetear botón
        this.setSubmitButtonLoading(false);
    }
}

// Hacer disponible globalmente
window.DepartmentUpdateController = DepartmentUpdateController;
