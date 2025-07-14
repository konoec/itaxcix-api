/**
 * Province Update Modal Controller
 * Controlador para el modal de actualización de provincias
 * Sigue el mismo diseño que el módulo de departamentos
 */

class ProvinceUpdateController {
    constructor() {
        this.modalId = 'edit-province-modal';
        this.updateService = new ProvinceUpdateService();
        this.currentProvinceData = null;
        
        console.log('🔧 Inicializando ProvinceUpdateController...');
        this.init();
    }

    /**
     * Inicializa el controlador
     */
    init() {
        // Crear el modal si no existe
        if (!document.getElementById(this.modalId)) {
            this.createModalHTML();
        }
        
        // Configurar eventos
        this.bindEvents();
        
        console.log('✅ Modal de actualización de provincia inicializado');
    }

    /**
     * Crea el HTML del modal siguiendo el diseño de Tabler (igual que distritos)
     */
    createModalHTML() {
        const modalHTML = `
            <div class="modal modal-blur fade" id="${this.modalId}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-green text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-edit me-2"></i>
                                Editar Provincia
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="edit-province-form" novalidate>
                            <input type="hidden" id="edit-province-id" name="id">
                            <div class="modal-body">
                                <div class="row g-2">
                                    <!-- Campo Nombre -->
                                    <div class="col-12">
                                        <label for="edit-province-name" class="form-label required mb-1">
                                            <i class="fas fa-map me-1 text-primary"></i>
                                            Nombre de la Provincia
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary-lt">
                                                <i class="fas fa-font text-primary"></i>
                                            </span>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="edit-province-name" 
                                                   name="name"
                                                   placeholder="Ej: Huaura, Cañete, Barranca..."
                                                   required
                                                   maxlength="100"
                                                   autocomplete="off">
                                            <div class="invalid-feedback" id="edit-name-error"></div>
                                        </div>
                                        <small class="form-hint text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Nombre oficial de la provincia
                                        </small>
                                    </div>

                                    <!-- Campo Departamento -->
                                    <div class="col-12">
                                        <label for="edit-province-department" class="form-label required mb-1">
                                            <i class="fas fa-layer-group me-1 text-success"></i>
                                            Departamento
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-success-lt">
                                                <i class="fas fa-map-marked-alt text-success"></i>
                                            </span>
                                            <select class="form-select" 
                                                    id="edit-province-department" 
                                                    name="departmentId"
                                                    required>
                                                <option value="">Cargando departamentos...</option>
                                            </select>
                                            <div class="invalid-feedback" id="edit-department-error"></div>
                                        </div>
                                    </div>

                                    <!-- Campo Ubigeo -->
                                    <div class="col-12">
                                        <label for="edit-province-ubigeo" class="form-label required mb-1">
                                            <i class="fas fa-barcode me-1 text-info"></i>
                                            Código Ubigeo
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-info-lt">
                                                <i class="fas fa-hashtag text-info"></i>
                                            </span>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="edit-province-ubigeo" 
                                                   name="ubigeo"
                                                   placeholder="Ej: 1509, 1508, 1501..."
                                                   required
                                                   pattern="^[0-9]{4}$"
                                                   maxlength="4"
                                                   autocomplete="off">
                                            <div class="invalid-feedback" id="edit-ubigeo-error"></div>
                                        </div>
                                        <small class="form-hint text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            4 dígitos según INEI
                                        </small>
                                    </div>

                                    <!-- Preview Card Compacta -->
                                    <div class="col-12 mt-2">
                                        <div class="card bg-green-lt border-green">
                                            <div class="card-body py-2">
                                                <div class="row align-items-center">
                                                    <div class="col-auto">
                                                        <div class="avatar bg-green text-white">
                                                            <i class="fas fa-eye"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="fw-bold" id="editPreviewProvinceName">
                                                            <span class="text-muted">Vista previa</span>
                                                        </div>
                                                        <div class="text-muted small">
                                                            <span id="editPreviewDepartment" class="badge bg-success-lt me-1">--</span>
                                                            <span id="editPreviewProvinceUbigeo" class="badge bg-info-lt">--</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i>
                                    Cancelar
                                </button>
                                <button type="submit" class="btn btn-green" id="edit-province-submit">
                                    <i class="fas fa-save me-1"></i>
                                    Actualizar Provincia
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHTML);
    }

    /**
     * Configura los eventos del modal
     */
    bindEvents() {
        const modal = document.getElementById(this.modalId);
        const form = document.getElementById('edit-province-form');

        // Evento al mostrar el modal - cargar departamentos
        if (modal) {
            modal.addEventListener('shown.bs.modal', () => {
                this.loadDepartments();
            });

            modal.addEventListener('hidden.bs.modal', () => {
                this.resetModal();
            });
        }

        // Evento de envío del formulario
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleUpdate();
            });
        }

        // Event listeners para vista previa en tiempo real
        this.setupPreviewListeners();
    }

    /**
     * Configura los event listeners para la vista previa
     */
    setupPreviewListeners() {
        const nameInput = document.getElementById('edit-province-name');
        const departmentSelect = document.getElementById('edit-province-department');
        const ubigeoInput = document.getElementById('edit-province-ubigeo');

        // Preview en tiempo real
        if (nameInput) {
            nameInput.addEventListener('input', () => this.updatePreview());
        }
        if (departmentSelect) {
            departmentSelect.addEventListener('change', () => this.updatePreview());
        }
        if (ubigeoInput) {
            ubigeoInput.addEventListener('input', () => this.updatePreview());
        }
    }

    /**
     * Actualiza la vista previa del modal
     */
    updatePreview() {
        const nameInput = document.getElementById('edit-province-name');
        const departmentSelect = document.getElementById('edit-province-department');
        const ubigeoInput = document.getElementById('edit-province-ubigeo');
        
        const previewName = document.getElementById('editPreviewProvinceName');
        const previewDepartment = document.getElementById('editPreviewDepartment');
        const previewUbigeo = document.getElementById('editPreviewProvinceUbigeo');

        if (previewName) {
            const name = nameInput?.value.trim() || '';
            previewName.textContent = name || 'Vista previa';
        }

        if (previewDepartment && departmentSelect) {
            const selectedOption = departmentSelect.options[departmentSelect.selectedIndex];
            const departmentName = selectedOption?.text || '--';
            previewDepartment.textContent = departmentName;
        }

        if (previewUbigeo) {
            const ubigeo = ubigeoInput?.value.trim() || '';
            previewUbigeo.textContent = ubigeo || '--';
        }
    }

    /**
     * Abre el modal con los datos de una provincia
     * @param {Object} provinceData - Datos de la provincia a editar
     */
    async openModal(provinceData) {
        if (!provinceData || !provinceData.id) {
            this.showErrorToast('Datos de provincia inválidos');
            return;
        }

        try {
            console.log('📝 Abriendo modal de edición para provincia:', provinceData);
            
            // Guardar datos actuales
            this.currentProvinceData = provinceData;
            
            // Cargar departamentos primero
            await this.loadDepartments();
            
            // Poblar el formulario
            this.populateForm(provinceData);
            
            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById(this.modalId));
            modal.show();
            
        } catch (error) {
            console.error('❌ Error abriendo modal:', error);
            this.showErrorToast('Error al abrir el modal de edición');
        }
    }

    /**
     * Carga los departamentos disponibles usando el mismo endpoint que el modal de crear
     */
    async loadDepartments() {
        const departmentSelect = document.getElementById('edit-province-department');
        
        if (!departmentSelect) {
            console.error('❌ Select de departamentos no encontrado');
            return;
        }

        try {
            // Mostrar estado de carga
            departmentSelect.innerHTML = '<option value="">Cargando departamentos...</option>';
            departmentSelect.disabled = true;

            console.log('🔄 Cargando departamentos...');
            
            // Usar el mismo servicio que el modal de crear provincia
            const response = await DepartmentsService.getDepartments(1, 100, '', 'name', 'ASC');
            console.log('📥 Respuesta de departamentos:', response);
            
            if (response.success && response.data) {
                const departments = response.data.data || response.data.items || response.data || [];
                console.log('📋 Departamentos obtenidos:', departments.length);

                // Limpiar y poblar el select
                departmentSelect.innerHTML = '<option value="">Seleccione departamento...</option>';
                
                departments.forEach(dept => {
                    const option = document.createElement('option');
                    option.value = dept.id;
                    option.textContent = dept.name;
                    departmentSelect.appendChild(option);
                });

                departmentSelect.disabled = false;
                console.log('✅ Departamentos cargados exitosamente');
                
                // Si hay datos de provincia actuales, seleccionar el departamento
                if (this.currentProvinceData && this.currentProvinceData.departmentId) {
                    departmentSelect.value = this.currentProvinceData.departmentId;
                    // Actualizar vista previa después de seleccionar el departamento
                    setTimeout(() => this.updatePreview(), 100);
                }
                
            } else {
                throw new Error('Respuesta de API inválida');
            }
            
        } catch (error) {
            console.error('❌ Error cargando departamentos:', error);
            departmentSelect.innerHTML = '<option value="">Error al cargar departamentos</option>';
            departmentSelect.disabled = false;
            this.showErrorToast('Error al cargar departamentos');
        }
    }

    /**
     * Puebla el formulario con los datos de la provincia
     * @param {Object} provinceData - Datos de la provincia
     */
    populateForm(provinceData) {
        const form = document.getElementById('edit-province-form');
        
        if (!form || !provinceData) {
            console.error('❌ Formulario o datos no válidos');
            return;
        }

        try {
            // Poblar campos del formulario
            document.getElementById('edit-province-id').value = provinceData.id || '';
            document.getElementById('edit-province-name').value = provinceData.name || '';
            
            // Para el UBIGEO, solo mostrar los primeros 4 dígitos (quitar los 2 ceros finales si existen)
            let ubigeoValue = provinceData.ubigeo || '';
            if (ubigeoValue.length === 6 && ubigeoValue.endsWith('00')) {
                ubigeoValue = ubigeoValue.substring(0, 4);
            }
            document.getElementById('edit-province-ubigeo').value = ubigeoValue;
            
            // El departamento se seleccionará después de cargar la lista
            if (provinceData.departmentId) {
                const departmentSelect = document.getElementById('edit-province-department');
                if (departmentSelect && departmentSelect.options.length > 1) {
                    departmentSelect.value = provinceData.departmentId;
                }
            }

            // Actualizar vista previa después de poblar los datos
            setTimeout(() => this.updatePreview(), 100);

            console.log('✅ Formulario poblado con datos de provincia');
            
        } catch (error) {
            console.error('❌ Error poblando formulario:', error);
            this.showErrorToast('Error al cargar datos en el formulario');
        }
    }

    /**
     * Maneja la actualización de la provincia
     */
    async handleUpdate() {
        const submitBtn = document.getElementById('edit-province-submit');
        
        try {
            // Mostrar estado de carga
            this.setLoadingState(true);
            
            // Obtener datos del formulario
            const formData = this.getFormData();
            
            if (!formData) {
                throw new Error('Error al obtener datos del formulario');
            }

            // Validar datos
            const validation = this.updateService.validateProvinceData(formData);
            if (!validation.isValid) {
                this.showValidationErrors(validation.errors);
                return;
            }

            console.log('📤 Enviando actualización de provincia:', formData);
            
            // Llamar al servicio de actualización
            const result = await this.updateService.updateProvince(this.currentProvinceData.id, formData);
            
            if (result.success) {
                console.log('✅ Provincia actualizada exitosamente');
                
                // Mostrar mensaje de éxito
                this.showSuccessToast('Provincia actualizada correctamente');
                
                // Cerrar modal
                this.closeModal();
                
                // Recargar lista de provincias
                this.reloadProvincesList();
                
            } else {
                throw new Error(result.message || 'Error al actualizar la provincia');
            }
            
        } catch (error) {
            console.error('❌ Error actualizando provincia:', error);
            this.showErrorToast(error.message || 'Error al actualizar la provincia');
        } finally {
            this.setLoadingState(false);
        }
    }

    /**
     * Obtiene los datos del formulario
     * @returns {Object|null} Datos del formulario o null si hay error
     */
    getFormData() {
        try {
            const name = document.getElementById('edit-province-name').value.trim();
            const departmentId = document.getElementById('edit-province-department').value;
            const ubigeo = document.getElementById('edit-province-ubigeo').value.trim();

            return {
                name,
                departmentId: parseInt(departmentId),
                ubigeo
            };
        } catch (error) {
            console.error('❌ Error obteniendo datos del formulario:', error);
            return null;
        }
    }

    /**
     * Muestra errores de validación en el formulario
     * @param {Array} errors - Array de errores
     */
    showValidationErrors(errors) {
        // Limpiar errores previos
        this.clearValidationErrors();
        
        errors.forEach(error => {
            console.error('❌ Error de validación:', error);
            
            // Aquí podrías mapear errores específicos a campos específicos
            // Por simplicidad, mostramos el primer error como toast
            if (errors.indexOf(error) === 0) {
                this.showErrorToast(error);
            }
        });
    }

    /**
     * Limpia los errores de validación
     */
    clearValidationErrors() {
        const errorElements = document.querySelectorAll('.invalid-feedback');
        errorElements.forEach(element => {
            element.textContent = '';
        });
        
        const inputElements = document.querySelectorAll('.is-invalid');
        inputElements.forEach(element => {
            element.classList.remove('is-invalid');
        });
    }

    /**
     * Establece el estado de carga del modal
     * @param {boolean} loading - Si está cargando
     */
    setLoadingState(loading) {
        const submitBtn = document.getElementById('edit-province-submit');
        const form = document.getElementById('edit-province-form');
        
        if (loading) {
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                    Actualizando...
                `;
            }
            if (form) {
                form.classList.add('loading');
            }
        } else {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M5 12l5 5l10 -10"></path>
                    </svg>
                    Actualizar Provincia
                `;
            }
            if (form) {
                form.classList.remove('loading');
            }
        }
    }

    /**
     * Cierra el modal
     */
    closeModal() {
        const modal = document.getElementById(this.modalId);
        if (modal) {
            const bsModal = bootstrap.Modal.getInstance(modal);
            if (bsModal) {
                bsModal.hide();
            }
        }
    }

    /**
     * Resetea el modal a su estado inicial
     */
    resetModal() {
        // Limpiar datos actuales
        this.currentProvinceData = null;
        
        // Limpiar formulario
        const form = document.getElementById('edit-province-form');
        if (form) {
            form.reset();
        }
        
        // Limpiar errores de validación
        this.clearValidationErrors();
        
        // Restaurar estado normal
        this.setLoadingState(false);
        
        console.log('🔄 Modal de edición reseteado');
    }

    /**
     * Recarga la lista de provincias
     */
    reloadProvincesList() {
        // Intentar recargar usando el controlador de lista si está disponible
        if (window.provincesController && typeof window.provincesController.loadProvinces === 'function') {
            window.provincesController.loadProvinces();
        } else if (window.ProvincesListController && typeof window.ProvincesListController.load === 'function') {
            window.ProvincesListController.load();
        } else {
            // Si no hay controlador disponible, recargar la página
            console.log('🔄 Recargando página para mostrar cambios...');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    }

    /**
     * Muestra un toast de éxito
     * @param {string} message - Mensaje a mostrar
     */
    showSuccessToast(message) {
        if (typeof window.showRecoveryToast === 'function') {
            window.showRecoveryToast(message, 'success');
        } else if (typeof window.GlobalToast?.show === 'function') {
            window.GlobalToast.show(message, 'success');
        } else {
            console.log('✅', message);
        }
    }

    /**
     * Muestra un toast de error
     * @param {string} message - Mensaje a mostrar
     */
    showErrorToast(message) {
        if (typeof window.showRecoveryToast === 'function') {
            window.showRecoveryToast(message, 'error');
        } else if (typeof window.GlobalToast?.show === 'function') {
            window.GlobalToast.show(message, 'error');
        } else {
            console.error('❌', message);
            alert(`Error: ${message}`);
        }
    }
}

// Hacer disponible globalmente
window.ProvinceUpdateController = ProvinceUpdateController;

console.log('✅ ProvinceUpdateController cargado correctamente');
