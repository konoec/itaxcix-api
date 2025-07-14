// Controlador para el modal de creación de provincia
class CreateProvinceModalController {
    constructor(modalId) {
        this.modalId = modalId;
        this.modal = document.getElementById(modalId);
        this.form = this.modal.querySelector('form');
        this.nameInput = this.form.querySelector('#provinceName');
        this.departmentSelect = this.form.querySelector('#provinceDepartment');
        this.ubigeoInput = this.form.querySelector('#provinceUbigeo');
        this.saveBtn = this.form.querySelector('#saveProvinceBtn');
        this.closeBtns = this.modal.querySelectorAll('[data-bs-dismiss="modal"]');
        this.toast = document.getElementById('recovery-toast');
        this.toastMsg = document.getElementById('recovery-toast-message');
        this.loading = this.form.querySelector('.modal-loading');
        this.initEvents();
    }

    initEvents() {
        this.form.addEventListener('submit', e => {
            e.preventDefault();
            this.createProvince();
        });
        this.closeBtns.forEach(btn => btn.addEventListener('click', () => this.resetForm()));
    }

    async createProvince() {
        this.setLoading(true);
        const name = this.nameInput.value.trim();
        const departmentId = parseInt(this.departmentSelect.value);
        const ubigeo = this.ubigeoInput.value.trim();
        if (!name || !departmentId || !ubigeo) {
            this.showToast('Completa todos los campos obligatorios', true);
            this.setLoading(false);
            return;
        }
        try {
            const result = await CreateProvinceService.createProvince({ name, departmentId, ubigeo });
            this.showToast('Provincia creada exitosamente');
            this.resetForm();
            // Cerrar modal
            const modal = bootstrap.Modal.getOrCreateInstance(this.modal);
            modal.hide();
            // Recargar listado
            if (window.ProvincesListController) window.ProvincesListController.reload();
        } catch (err) {
            this.showToast(err.message || 'Error al crear provincia', true);
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
        setTimeout(() => this.toast.classList.remove('show'), 3000);
    }

    resetForm() {
        this.form.reset();
        this.setLoading(false);
    }

    // Llenar departamentos dinámicamente
    async fillDepartments(departments) {
        this.departmentSelect.innerHTML = '<option value="">Selecciona un departamento</option>';
        departments.forEach(dep => {
            const opt = document.createElement('option');
            opt.value = dep.id;
            opt.textContent = dep.name;
            this.departmentSelect.appendChild(opt);
        });
    }
}
window.CreateProvinceModalController = CreateProvinceModalController;
