// Controlador para el modal de creación de distrito
class CreateDistrictModalController {
    constructor(modalId) {
        this.modalId = modalId;
        this.modal = document.getElementById(modalId);
        this.form = this.modal.querySelector('form');
        this.nameInput = this.form.querySelector('#districtName');
        this.provinceSelect = this.form.querySelector('#districtProvince');
        this.ubigeoInput = this.form.querySelector('#districtUbigeo');
        this.saveBtn = this.form.querySelector('#saveDistrictBtn');
        this.closeBtns = this.modal.querySelectorAll('[data-bs-dismiss="modal"]');
        this.toast = document.getElementById('recovery-toast');
        this.toastMsg = document.getElementById('recovery-toast-message');
        this.loading = this.form.querySelector('.modal-loading');
        this.initEvents();
        this.loadProvinces();
    }

    initEvents() {
        this.form.addEventListener('submit', e => {
            e.preventDefault();
            this.createDistrict();
        });
        this.closeBtns.forEach(btn => btn.addEventListener('click', () => this.resetForm()));
    }

    async createDistrict() {
        this.setLoading(true);
        const name = this.nameInput.value.trim();
        const provinceId = parseInt(this.provinceSelect.value);
        const ubigeo = this.ubigeoInput.value.trim();
        if (!name || !provinceId || !ubigeo) {
            this.showToast('Completa todos los campos obligatorios', true);
            this.setLoading(false);
            return;
        }
        try {
            const result = await CreateDistrictService.createDistrict({ name, provinceId, ubigeo });
            this.showToast('Distrito creado exitosamente');
            this.resetForm();
            // Cerrar modal
            const modal = bootstrap.Modal.getOrCreateInstance(this.modal);
            modal.hide();
            // Recargar listado
            if (window.DistrictsListController && typeof window.DistrictsListController.load === 'function') {
                window.DistrictsListController.load();
            }
        } catch (err) {
            this.showToast(err.message || 'Error al crear distrito', true);
        }
        this.setLoading(false);
    }

    setLoading(isLoading) {
        this.saveBtn.disabled = isLoading;
        this.loading.style.display = isLoading ? 'inline-block' : 'none';
    }

    showToast(msg, error = false) {
        this.toastMsg.textContent = msg;
        this.toast.classList.remove('error-toast');
        if (error) this.toast.classList.add('error-toast');
        this.toast.classList.add('show');
        setTimeout(() => this.toast.classList.remove('show'), 2500);
    }

    resetForm() {
        this.form.reset();
        this.setLoading(false);
    }

    async loadProvinces() {
        // Asume que existe ProvincesService global
        if (window.ProvincesService) {
            const res = await ProvincesService.getProvinces({ perPage: 100 });
            const select = this.provinceSelect;
            select.innerHTML = '<option value="">Todas las provincias</option>';
            (res.data?.data || res.data?.items || []).forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.id;
                opt.textContent = p.name;
                select.appendChild(opt);
            });
        }
    }
}
window.CreateDistrictModalController = CreateDistrictModalController;
