/**
 * Controlador para el modal de edición de categorías de vehículo
 * Maneja la apertura del modal, carga de datos, validación y envío del formulario
 * Diseño y lógica siguiendo el patrón de DriverStatusEditController
 */
class CategoryEditController {
    constructor() {
        this.isSubmitting = false;
        this.currentCategoryId = null;
        this.bindEvents();
    }

    /**
     * Abre el modal de edición de categoría
     * @param {number} id - ID de la categoría a editar
     * @param {object} categoryData - Datos de la categoría obtenidos de la lista
     */
    async openEditModal(id, categoryData = null) {
        try {
            console.log('📝 Abriendo modal de edición para categoría:', id);
            console.log('📊 Datos proporcionados:', categoryData);
            this.currentCategoryId = id;
            // Si no se proporcionan datos, buscarlos en el array global actual
            if (!categoryData && window.categoryListController && Array.isArray(window.categoryListController.currentData)) {
                console.log('🔎 Buscando en currentData:', window.categoryListController.currentData);
                categoryData = window.categoryListController.currentData.find(item => Number(item.id) === Number(id));
            }
            // Mostrar modal con datos directamente
            const modalHtml = this.generateModalHtml();
            const modalContainer = document.getElementById('modal-container');
            if (modalContainer) {
                modalContainer.innerHTML = modalHtml;
                const modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
                modal.show();
                this.populateFormWithData(categoryData);
                this.setupModalEvents();
            }
        } catch (error) {
            console.error('❌ Error abriendo modal de edición:', error);
        }
    }

    /**
     * Llena el formulario con los datos proporcionados
     * @param {object} category - Datos de la categoría
     */
    populateFormWithData(category) {
        try {
            if (!category) return;
            document.getElementById('editCategoryName').value = category.name || '';
            document.getElementById('editCategoryActive').checked = !!category.active;
        } catch (error) {
            console.error('❌ Error llenando datos en el formulario:', error);
        }
    }

    /**
     * Genera el HTML del modal
     */
    generateModalHtml() {
        return `
            <div class="modal modal-blur fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-edit text-primary me-2"></i>
                                Editar Categoría
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Form -->
                            <div id="editModalForm">
                                <form id="editCategoryForm">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label class="form-label required">
                                                    <i class="fas fa-tag text-muted me-1"></i>
                                                    Nombre de la Categoría
                                                </label>
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="editCategoryName" 
                                                       name="name"
                                                       placeholder="Ej: Automóvil, Camioneta..."
                                                       maxlength="100"
                                                       required>
                                                <div class="form-hint">Máximo 100 caracteres</div>
                                                <div class="invalid-feedback"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="editCategoryActive" name="active">
                                                    <label class="form-check-label" for="editCategoryActive">
                                                        <strong>Estado Activo</strong>
                                                    </label>
                                                    <div class="form-hint text-muted">
                                                        Las categorías inactivas no estarán disponibles para asignación
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="modal-footer" id="editModalFooter">
                            <button type="button" class="btn btn-outline-secondary me-auto" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary" id="updateCategoryBtn" form="editCategoryForm">
                                <i class="fas fa-save me-1"></i>
                                Actualizar Categoría
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    /**
     * Configura los eventos del modal
     */
    setupModalEvents() {
        const form = document.getElementById('editCategoryForm');
        if (form) {
            form.addEventListener('submit', (e) => this.handleSubmit(e));
        }
        // Validación en tiempo real
        const nameInput = document.getElementById('editCategoryName');
        if (nameInput) {
            nameInput.addEventListener('input', () => this.validateField(nameInput));
        }
    }

    /**
     * Maneja el envío del formulario
     */
    async handleSubmit(event) {
        event.preventDefault();
        if (this.isSubmitting || !this.currentCategoryId) return;
        const formData = new FormData(event.target);
        const data = {
            name: formData.get('name').trim(),
            active: document.getElementById('editCategoryActive').checked
        };
        // Validar datos
        if (!window.CategoryUpdateService || typeof window.CategoryUpdateService.validateCategoryData !== 'function') {
            this.showValidationErrors(['No se encontró el servicio de actualización. Recarga la página.']);
            return;
        }
        const validation = window.CategoryUpdateService.validateCategoryData(data);
        if (!validation.isValid) {
            this.showValidationErrors([validation.message]);
            return;
        }
        this.setSubmittingState(true);
        try {
            const result = await window.CategoryUpdateService.updateCategory(this.currentCategoryId, data);
            if (result && result.success) {
                if (window.showRecoveryToast) window.showRecoveryToast('Categoría actualizada exitosamente');
                // Actualizar la lista
                if (window.categoryListController) window.categoryListController.load();
                // Cerrar el modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('editCategoryModal'));
                if (modal) modal.hide();
            } else {
                this.showValidationErrors([result.message || 'Error desconocido']);
            }
        } catch (error) {
            this.showValidationErrors([error.message]);
        } finally {
            this.setSubmittingState(false);
        }
    }

    /**
     * Valida un campo individual
     */
    validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let message = '';
        if (field.id === 'editCategoryName') {
            if (value.length === 0) {
                isValid = false;
                message = 'El nombre no puede estar vacío';
            } else if (value.length > 100) {
                isValid = false;
                message = 'El nombre no puede exceder 100 caracteres';
            }
        }
        // Aplicar estilos de validación
        if (isValid) {
            field.classList.remove('is-invalid');
            field.nextElementSibling.nextElementSibling.textContent = '';
        } else {
            field.classList.add('is-invalid');
            field.nextElementSibling.nextElementSibling.textContent = message;
        }
        return isValid;
    }

    /**
     * Muestra errores de validación
     */
    showValidationErrors(errors) {
        const nameInput = document.getElementById('editCategoryName');
        if (nameInput && errors.length > 0) {
            nameInput.classList.add('is-invalid');
            nameInput.nextElementSibling.nextElementSibling.textContent = errors[0];
        }
    }

    /**
     * Controla el estado de envío del formulario
     */
    setSubmittingState(isSubmitting) {
        this.isSubmitting = isSubmitting;
        const submitBtn = document.getElementById('updateCategoryBtn');
        const form = document.getElementById('editCategoryForm');
        if (isSubmitting) {
            submitBtn.disabled = true;
            form.classList.add('was-validated');
        } else {
            submitBtn.disabled = false;
            form.classList.remove('was-validated');
        }
    }

    /**
     * Configura eventos globales
     */
    bindEvents() {
        // Evento para abrir modal desde botón editar (captura click en botón o ícono)
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.edit-category-btn');
            if (btn) {
                const id = Number(btn.getAttribute('data-id'));
                if (!isNaN(id)) {
                    // Buscar datos en la lista actual
                    let categoryData = null;
                    if (window.categoryListController && Array.isArray(window.categoryListController.currentData)) {
                        categoryData = window.categoryListController.currentData.find(item => item.id === id);

                    }
                    this.openEditModal(id, categoryData);
                }
            }
        });
    }
}

// Exportar la clase globalmente para uso en el inicializador
window.CategoryEditControllerClass = CategoryEditController;
console.log('✅ CategoryEditController definido y exportado globalmente');
