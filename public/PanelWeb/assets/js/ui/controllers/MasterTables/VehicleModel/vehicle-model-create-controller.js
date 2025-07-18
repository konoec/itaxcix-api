// Controlador para el modal de creación de modelos de vehículos
function showToast(msg, type = 'success') {
    if (window.GlobalToast) {
        window.GlobalToast.show(msg, type);
    }
}

class VehicleModelCreateController {
    constructor({ modalId = 'vehicleModelCreateModal', onCreated = null } = {}) {
        this.modalId = modalId;
        this.onCreated = onCreated;
        this.modal = null;
        this.form = null;
        this.loading = false;
        this.allBrands = [];
        this.init();
    }

    attachEvents() {
        if (this.form) {
            this.form.addEventListener('submit', async (e) => {
                e.preventDefault();
                this.clearError();
                const name = this.nameInput?.value?.trim();
                const brandId = this.brandSelect?.value;
                const active = this.activeCheckbox?.checked;
                if (!name || !brandId) {
                    this.showError('Debe completar todos los campos obligatorios.');
                    return;
                }
                if (!window.vehicleModelCreateService || typeof window.vehicleModelCreateService.createVehicleModel !== 'function') {
                    this.showError('Servicio de creación de modelo no disponible.');
                    return;
                }
                this.saveBtn.disabled = true;
                try {
                    await window.vehicleModelCreateService.createVehicleModel({ name, brandId: Number(brandId), active });
                    showToast('Modelo de vehículo creado correctamente', 'success');
                    this.resetForm();
                    if (typeof this.onCreated === 'function') this.onCreated();
                    this.hideModal();
                } catch (error) {
                    this.showError(error.message || 'Error al crear el modelo de vehículo');
                }
                this.saveBtn.disabled = false;
            });
        }
        if (this.cancelBtn) {
            this.cancelBtn.addEventListener('click', () => this.resetForm());
        }
        if (this.closeBtns && this.closeBtns.length) {
            this.closeBtns.forEach(btn => {
                btn.addEventListener('click', () => this.resetForm());
            });
        }
        // Búsqueda en vivo
        if (this.brandSearchInput) {
            this.brandSearchInput.addEventListener('input', (e) => {
                this.renderBrandOptions(e.target.value);
                // (Opcional: si el input cambia, puedes limpiar la selección del select si ya no coincide)
                if (this.brandSelect) {
                    this.brandSelect.value = "";
                }
            });
        }
        // Autocompletar el input al seleccionar una marca en el select
        if (this.brandSelect) {
            this.brandSelect.addEventListener('change', (e) => {
                const selectedOption = this.brandSelect.options[this.brandSelect.selectedIndex];
                if (selectedOption && selectedOption.value) {
                    this.brandSearchInput.value = selectedOption.textContent;
                }
            });
        }
    }

    async init() {
        this.modal = document.getElementById(this.modalId);
        if (!this.modal) return;
        this.form = this.modal.querySelector('form');
        this.brandSelect = this.modal.querySelector('#vehicleModelBrandSelect');
        this.brandSearchInput = this.modal.querySelector('#vehicleModelBrandSearch');
        this.nameInput = this.modal.querySelector('#vehicleModelNameInput');
        this.activeCheckbox = this.modal.querySelector('#vehicleModelActiveCheckbox');
        this.saveBtn = this.modal.querySelector('#vehicleModelSaveBtn');
        this.cancelBtn = this.modal.querySelector('#vehicleModelCancelBtn');
        this.closeBtns = this.modal.querySelectorAll('[data-bs-dismiss]');
        this.errorMsg = this.modal.querySelector('#vehicleModelCreateErrorMsg');

        // Cargar marcas y limpiar búsqueda cada vez que se abre el modal
        if (this.modal) {
            this.modal.addEventListener('show.bs.modal', () => {
                this.populateBrandSelect();
                if (this.brandSearchInput) this.brandSearchInput.value = '';
            });
        }
        this.attachEvents();
    }

    async populateBrandSelect() {
        if (!this.brandSelect) return;
        this.brandSelect.innerHTML = '<option value="">Cargando marcas...</option>';
        this.brandSelect.disabled = true;
        const BrandService = window.BrandService;
        if (!BrandService || typeof BrandService.getBrands !== 'function') {
            this.brandSelect.innerHTML = '<option value="">Servicio de marcas no disponible</option>';
            this.showError('Servicio de marcas no disponible.');
            this.brandSelect.disabled = true;
            console.error('❌ BrandService no está disponible en window.');
            return;
        }
        try {
            const params = {
                page: 1,
                perPage: 100,
                sortBy: 'name',
                sortDirection: 'asc',
                onlyActive: true
            };
            const response = await BrandService.getBrands(params);
            let brands = [];
            if (response.success && response.data && response.data.items) {
                brands = response.data.items;
            }
            if (!Array.isArray(brands)) brands = [];
            this.allBrands = brands;
            if (!brands.length) {
                this.brandSelect.innerHTML = '<option value="">No hay marcas activas</option>';
                this.showError('No hay marcas activas para seleccionar.');
                this.brandSelect.disabled = true;
                return;
            }
            this.renderBrandOptions();
            this.brandSelect.disabled = false;
            this.clearError();
        } catch (err) {
            this.brandSelect.innerHTML = '<option value="">No se pudieron cargar las marcas</option>';
            this.showError('No se pudieron cargar las marcas.');
            this.brandSelect.disabled = true;
        }
    }

    // Renderiza marcas según filtro de búsqueda
    renderBrandOptions(filter = '') {
        if (!this.brandSelect || !Array.isArray(this.allBrands)) return;
        const filterLower = filter.trim().toLowerCase();
        this.brandSelect.innerHTML = '<option value="">Seleccione una marca</option>';
        this.allBrands.forEach(brand => {
            if (!filterLower || brand.name.toLowerCase().includes(filterLower)) {
                const opt = document.createElement('option');
                opt.value = brand.id;
                opt.textContent = brand.name;
                this.brandSelect.appendChild(opt);
            }
        });
    }

    showError(msg) {
        if (this.errorMsg) {
            this.errorMsg.textContent = msg;
            this.errorMsg.classList.remove('d-none');
        }
    }

    clearError() {
        if (this.errorMsg) {
            this.errorMsg.textContent = '';
            this.errorMsg.classList.add('d-none');
        }
    }

    resetForm() {
        if (this.form) this.form.reset();
        this.clearError();
    }

    hideModal() {
        if (window.bootstrap && this.modal) {
            const modalInstance = window.bootstrap.Modal.getInstance(this.modal);
            if (modalInstance) modalInstance.hide();
        }
    }
}

window.VehicleModelCreateController = VehicleModelCreateController;
