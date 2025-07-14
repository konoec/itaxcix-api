/**
 * District Update Controller
 * Controlador para manejar la actualización de distritos con modal de Tabler
 * 
 * Funciones principales:
 * - Mostrar modal de edición con datos pre-cargados
 * - Validar y procesar formulario de actualización
 * - Cargar lista de provincias para selección
 * - Manejar estados de loading y errores
 * - Actualizar la lista después de editar
 */

class DistrictUpdateController {
    constructor() {
        this.updateService = null;
        this.currentDistrict = null;
        this.selectedProvinceId = null; // Para recordar qué provincia seleccionar
        this.isUpdating = false;
        this.modalId = 'editDistrictModal';
        
        console.log('✏️ Inicializando DistrictUpdateController...');
        
        // Inicializar servicio
        this.initializeService();
        
        // Crear modal si no existe
        this.createModal();
        
        // Configurar eventos
        this.bindEvents();
    }

    /**
     * Inicializa el servicio de actualización
     */
    initializeService() {
        try {
            this.updateService = new DistrictUpdateService();
            console.log('✅ Servicio de actualización de distritos inicializado');
        } catch (error) {
            console.error('❌ Error inicializando servicio:', error);
        }
    }

    /**
     * Crea el modal de edición con diseño de Tabler
     */
    createModal() {
        // Verificar si el modal ya existe
        if (document.getElementById(this.modalId)) {
            console.log('ℹ️ Modal de edición ya existe');
            return;
        }

        const modalHTML = `
            <div class="modal modal-blur fade" id="${this.modalId}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-orange text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-edit me-2"></i>
                                Editar Distrito
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form id="editDistrictForm">
                            <div class="modal-body">
                                <div class="row g-2">
                                    <!-- Campo Nombre -->
                                    <div class="col-12">
                                        <label for="editDistrictName" class="form-label required mb-1">
                                            <i class="fas fa-map-marker-alt me-1 text-primary"></i>
                                            Nombre del Distrito
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-primary-lt">
                                                <i class="fas fa-font text-primary"></i>
                                            </span>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="editDistrictName" 
                                                   name="name"
                                                   placeholder="Ejemplo: Miraflores, San Isidro..."
                                                   required
                                                   maxlength="100"
                                                   autocomplete="off">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <small class="form-hint text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Nombre oficial del distrito
                                        </small>
                                    </div>

                                    <!-- Campo Provincia -->
                                    <div class="col-12">
                                        <label for="editDistrictProvince" class="form-label required mb-1">
                                            <i class="fas fa-map me-1 text-success"></i>
                                            Provincia
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-success-lt">
                                                <i class="fas fa-layer-group text-success"></i>
                                            </span>
                                            <select class="form-select" 
                                                    id="editDistrictProvince" 
                                                    name="provinceId"
                                                    required>
                                                <option value="">Cargando provincias...</option>
                                            </select>
                                            <div class="invalid-feedback"></div>
                                        </div>
                                    </div>

                                    <!-- Campo Ubigeo -->
                                    <div class="col-12">
                                        <label for="editDistrictUbigeo" class="form-label required mb-1">
                                            <i class="fas fa-barcode me-1 text-info"></i>
                                            Código Ubigeo
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-info-lt">
                                                <i class="fas fa-hashtag text-info"></i>
                                            </span>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="editDistrictUbigeo" 
                                                   name="ubigeo"
                                                   placeholder="Ej: 150122, 150101..."
                                                   required
                                                   pattern="^[0-9]{6}$"
                                                   maxlength="6"
                                                   autocomplete="off">
                                            <div class="invalid-feedback"></div>
                                        </div>
                                        <small class="form-hint text-muted">
                                            <i class="fas fa-info-circle me-1"></i>
                                            6 dígitos según INEI
                                        </small>
                                    </div>

                                    <!-- Preview Card Compacta -->
                                    <div class="col-12 mt-2">
                                        <div class="card bg-orange-lt border-orange">
                                            <div class="card-body py-2">
                                                <div class="row align-items-center">
                                                    <div class="col-auto">
                                                        <div class="avatar bg-orange text-white">
                                                            <i class="fas fa-eye"></i>
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="fw-bold" id="editPreviewDistrictName">
                                                            <span class="text-muted">Vista previa</span>
                                                        </div>
                                                        <div class="text-muted small">
                                                            <span id="editPreviewProvince" class="badge bg-success-lt me-1">--</span>
                                                            <span id="editPreviewUbigeo" class="badge bg-info-lt">--</span>
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
                                <button type="submit" class="btn btn-orange" id="updateDistrictBtn">
                                    <i class="fas fa-save me-1"></i>
                                    Actualizar Distrito
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHTML);
        console.log('✅ Modal de edición de distrito creado');
    }

    /**
     * Configura los eventos del modal
     */
    bindEvents() {
        // Event listener para el formulario
        const form = document.getElementById('editDistrictForm');
        if (form) {
            form.addEventListener('submit', (e) => this.handleFormSubmit(e));
        }

        // Event listeners para vista previa
        const nameInput = document.getElementById('editDistrictName');
        const provinceSelect = document.getElementById('editDistrictProvince');
        const ubigeoInput = document.getElementById('editDistrictUbigeo');

        if (nameInput) {
            nameInput.addEventListener('input', () => this.updatePreview());
        }
        if (provinceSelect) {
            provinceSelect.addEventListener('change', () => this.updatePreview());
        }
        if (ubigeoInput) {
            ubigeoInput.addEventListener('input', () => this.updatePreview());
        }

        // Event listener para limpiar formulario al cerrar
        const modal = document.getElementById(this.modalId);
        if (modal) {
            modal.addEventListener('hidden.bs.modal', () => this.resetModal());
        }

        console.log('✅ Eventos del modal configurados');
    }

    /**
     * Abre el modal de edición para un distrito específico
     * @param {number} districtId - ID del distrito a editar
     * @param {Object} districtData - Datos opcionales del distrito (para evitar llamada adicional)
     */
    async openEditModal(districtId, districtData = null) {
        console.log(`✏️ Abriendo modal de edición para distrito: ${districtId}`);

        try {
            // Mostrar loading
            this.showLoading(true);

            let district = districtData;
            
            // Si no se proporcionaron datos, mostrar error
            if (!district) {
                throw new Error('Se requieren los datos del distrito para editar');
            }

            // Guardar referencia del distrito actual
            this.currentDistrict = district;

            // Cargar provincias
            await this.loadProvinces();

            // Llenar formulario con datos del distrito
            this.populateForm(district);

            // Mostrar modal
            const modal = new bootstrap.Modal(document.getElementById(this.modalId));
            modal.show();

            console.log('✅ Modal de edición abierto exitosamente');

        } catch (error) {
            console.error('❌ Error abriendo modal de edición:', error);
            this.showError(`Error al abrir el editor: ${error.message}`);
        } finally {
            this.showLoading(false);
        }
    }

    /**
     * Abre el modal de edición con datos proporcionados directamente
     * @param {Object} districtData - Datos del distrito
     */
    async openEditModalWithData(districtData) {
        console.log(`✏️ Abriendo modal de edición con datos:`, districtData);

        // Convertir al formato esperado por el modal
        const district = {
            id: districtData.id,
            name: districtData.name,
            provinceId: districtData.provinceId,
            ubigeo: districtData.ubigeo
        };

        // Usar el método existente pero sin hacer llamada al servicio
        await this.openEditModal(districtData.id, district);
    }

    /**
     * Llena el formulario con los datos del distrito
     * @param {Object} district - Datos del distrito
     */
    populateForm(district) {
        console.log('📝 Llenando formulario con datos:', district);

        const nameInput = document.getElementById('editDistrictName');
        const provinceSelect = document.getElementById('editDistrictProvince');
        const ubigeoInput = document.getElementById('editDistrictUbigeo');

        if (nameInput) {
            nameInput.value = district.name || '';
        }

        if (ubigeoInput) {
            ubigeoInput.value = district.ubigeo || '';
        }

        // Para la provincia, necesitamos esperar a que se carguen las provincias
        // Guardar el ID de la provincia para seleccionarla después
        this.selectedProvinceId = district.provinceId || district.province?.id || null;

        // Si las provincias ya están cargadas, seleccionar la provincia
        if (provinceSelect && provinceSelect.options.length > 1 && this.selectedProvinceId) {
            provinceSelect.value = this.selectedProvinceId;
        }

        // Actualizar vista previa
        this.updatePreview();

        console.log('✅ Formulario llenado exitosamente');
    }

    /**
     * Carga las provincias en el select
     */
    async loadProvinces() {
        console.log('🔄 Cargando provincias para select...');

        const provinceSelect = document.getElementById('editDistrictProvince');
        if (!provinceSelect) {
            console.error('❌ Select de provincias no encontrado');
            return;
        }

        try {
            provinceSelect.innerHTML = '<option value="">Cargando provincias...</option>';
            provinceSelect.disabled = true;

            // Usar el servicio de provincias
            const response = await ProvincesService.getProvinces(1, 200, '', null, null, null, 'name', 'ASC');

            if (response && response.success && response.data) {
                const provinces = response.data.data || response.data.items || response.data || [];

                provinceSelect.innerHTML = '<option value="">Seleccione provincia...</option>';
                
                provinces.forEach(province => {
                    const option = document.createElement('option');
                    option.value = province.id;
                    option.textContent = province.name;
                    option.setAttribute('data-department-name', province.department?.name || 'N/A');
                    provinceSelect.appendChild(option);
                });

                // Seleccionar la provincia correcta si se había guardado
                if (this.selectedProvinceId) {
                    provinceSelect.value = this.selectedProvinceId;
                    console.log(`✅ Provincia ${this.selectedProvinceId} seleccionada automáticamente`);
                    
                    // Actualizar vista previa después de seleccionar
                    this.updatePreview();
                }

                provinceSelect.disabled = false;
                console.log('✅ Provincias cargadas exitosamente');
            } else {
                throw new Error('Respuesta de API inválida');
            }
        } catch (error) {
            console.error('❌ Error cargando provincias:', error);
            provinceSelect.innerHTML = '<option value="">Error al cargar provincias</option>';
            provinceSelect.disabled = false;
        }
    }

    /**
     * Maneja el envío del formulario
     * @param {Event} e - Evento del formulario
     */
    async handleFormSubmit(e) {
        e.preventDefault();

        if (this.isUpdating || !this.currentDistrict) {
            return;
        }

        this.isUpdating = true;

        try {
            console.log('📝 Procesando actualización del distrito...');

            // Obtener datos del formulario
            const formData = {
                name: document.getElementById('editDistrictName').value.trim(),
                provinceId: parseInt(document.getElementById('editDistrictProvince').value),
                ubigeo: document.getElementById('editDistrictUbigeo').value.trim()
            };

            console.log('📤 Datos a actualizar:', formData);

            // Mostrar loading en botón
            const updateBtn = document.getElementById('updateDistrictBtn');
            if (updateBtn) {
                updateBtn.disabled = true;
                updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Actualizando...';
            }

            // Llamar al servicio de actualización
            const result = await this.updateService.updateDistrict(this.currentDistrict.id, formData);

            if (result.success) {
                console.log('✅ Distrito actualizado exitosamente');

                // Mostrar mensaje de éxito
                this.showSuccess('Distrito actualizado correctamente');

                // Cerrar modal
                this.closeModal();

                // Recargar la lista de distritos
                this.reloadDistrictsList();
            } else {
                throw new Error(result.message || 'Error al actualizar el distrito');
            }

        } catch (error) {
            console.error('❌ Error actualizando distrito:', error);
            this.showError(error.message || 'Error al actualizar el distrito');
        } finally {
            this.isUpdating = false;
            
            // Restaurar botón
            const updateBtn = document.getElementById('updateDistrictBtn');
            if (updateBtn) {
                updateBtn.disabled = false;
                updateBtn.innerHTML = '<i class="fas fa-save me-1"></i> Actualizar Distrito';
            }
        }
    }

    /**
     * Actualiza la vista previa del distrito
     */
    updatePreview() {
        const nameInput = document.getElementById('editDistrictName');
        const provinceSelect = document.getElementById('editDistrictProvince');
        const ubigeoInput = document.getElementById('editDistrictUbigeo');

        const previewName = document.getElementById('editPreviewDistrictName');
        const previewProvince = document.getElementById('editPreviewProvince');
        const previewUbigeo = document.getElementById('editPreviewUbigeo');

        // Actualizar nombre
        if (nameInput && previewName) {
            if (nameInput.value.trim()) {
                previewName.innerHTML = `<span class="text-orange">${nameInput.value}</span>`;
            } else {
                previewName.innerHTML = '<span class="text-muted">Vista previa</span>';
            }
        }

        // Actualizar provincia
        if (provinceSelect && previewProvince) {
            if (provinceSelect.value) {
                const selectedOption = provinceSelect.options[provinceSelect.selectedIndex];
                previewProvince.textContent = selectedOption.text;
                previewProvince.className = 'badge bg-success-lt me-1';
            } else {
                previewProvince.textContent = '--';
                previewProvince.className = 'badge bg-success-lt me-1';
            }
        }

        // Actualizar UBIGEO
        if (ubigeoInput && previewUbigeo) {
            if (ubigeoInput.value.trim()) {
                previewUbigeo.textContent = ubigeoInput.value;
                previewUbigeo.className = 'badge bg-info-lt';
            } else {
                previewUbigeo.textContent = '--';
                previewUbigeo.className = 'badge bg-info-lt';
            }
        }
    }

    /**
     * Muestra el estado de loading
     * @param {boolean} show - Mostrar o ocultar loading
     */
    showLoading(show) {
        // Implementar si es necesario
        console.log(show ? '⏳ Mostrando loading...' : '✅ Ocultando loading...');
    }

    /**
     * Resetea el modal a su estado inicial
     */
    resetModal() {
        console.log('🧹 Reseteando modal de edición');

        const form = document.getElementById('editDistrictForm');
        if (form) {
            form.reset();
        }

        // Resetear vista previa
        const previewName = document.getElementById('editPreviewDistrictName');
        const previewProvince = document.getElementById('editPreviewProvince');
        const previewUbigeo = document.getElementById('editPreviewUbigeo');

        if (previewName) {
            previewName.innerHTML = '<span class="text-muted">Vista previa</span>';
        }
        if (previewProvince) {
            previewProvince.textContent = '--';
        }
        if (previewUbigeo) {
            previewUbigeo.textContent = '--';
        }

        // Limpiar referencias
        this.currentDistrict = null;
        this.selectedProvinceId = null;
    }

    /**
     * Cierra el modal
     */
    closeModal() {
        const modal = bootstrap.Modal.getInstance(document.getElementById(this.modalId));
        if (modal) {
            modal.hide();
        }
    }

    /**
     * Recarga la lista de distritos
     */
    reloadDistrictsList() {
        try {
            // Intentar recargar usando el controlador de lista si está disponible
            if (window.districtsController && typeof window.districtsController.load === 'function') {
                console.log('🔄 Recargando lista de distritos...');
                window.districtsController.load();
            } else if (window.DistrictsListController && typeof window.DistrictsListController.load === 'function') {
                console.log('🔄 Recargando lista usando DistrictsListController...');
                window.DistrictsListController.load();
            } else {
                console.warn('⚠️ Controlador de lista no encontrado, recargando página...');
                setTimeout(() => location.reload(), 1500);
            }
        } catch (error) {
            console.error('❌ Error recargando lista:', error);
            setTimeout(() => location.reload(), 1500);
        }
    }

    /**
     * Muestra mensaje de éxito
     * @param {string} message - Mensaje a mostrar
     */
    showSuccess(message) {
        if (window.GlobalToast) {
            GlobalToast.show(message, 'success');
        } else {
            console.log('✅', message);
        }
    }

    /**
     * Muestra mensaje de error
     * @param {string} message - Mensaje de error
     */
    showError(message) {
        if (window.GlobalToast) {
            GlobalToast.show(message, 'error');
        } else {
            console.error('❌', message);
            alert(message);
        }
    }
}

// Crear instancia global cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔄 DOM cargado, creando DistrictUpdateController...');
    window.districtUpdateController = new DistrictUpdateController();
    console.log('✅ DistrictUpdateController cargado correctamente');
});

// También exportar la clase
window.DistrictUpdateController = DistrictUpdateController;
