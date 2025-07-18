// Controlador para el modal de creación de tipo de incidencia
class CreateIncidentTypeModalController {
    constructor(modalId) {
        this.modalId = modalId;
        this.modal = document.getElementById(modalId);
        this.form = this.modal.querySelector('form');
        this.nameInput = this.form.querySelector('#incidentTypeNameInput');
        this.activeCheckbox = this.form.querySelector('#incidentTypeActiveCheckbox');
        this.saveBtn = this.form.querySelector('#saveIncidentTypeBtn');
        this.closeBtns = this.modal.querySelectorAll('[data-bs-dismiss="modal"]');
        this.toast = document.getElementById('recovery-toast');
        this.toastMsg = document.getElementById('recovery-toast-message');
        this.loading = this.form.querySelector('.modal-loading');
        this.initEvents();
    }

    initEvents() {
        this.form.addEventListener('submit', e => {
            e.preventDefault();
            this.createIncidentType();
        });
        this.closeBtns.forEach(btn => btn.addEventListener('click', () => this.resetForm()));
    }

    async createIncidentType() {
        this.setLoading(true);
        const name = this.nameInput.value.trim();
        const active = this.activeCheckbox.checked;
        if (!name) {
            this.showToast('El nombre es obligatorio', true);
            this.setLoading(false);
            return;
        }
        try {
            const result = await IncidentTypeService.createIncidentType({ name, active });
            this.showToast('Tipo de incidencia creado exitosamente');
            this.resetForm();
            // Cerrar modal
            const modal = bootstrap.Modal.getOrCreateInstance(this.modal);
            modal.hide();
            // Recargar listado
            if (window.IncidentTypeListController && typeof window.IncidentTypeListController.reload === 'function') {
                window.IncidentTypeListController.reload();
            }
        } catch (err) {
            this.showToast(err.message || 'Error al crear tipo de incidencia', true);
        }
        this.setLoading(false);
    }

    setLoading(isLoading) {
        this.saveBtn.disabled = isLoading;
        if (this.loading) this.loading.style.display = isLoading ? 'inline-block' : 'none';
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
}

window.CreateIncidentTypeModalController = CreateIncidentTypeModalController;
