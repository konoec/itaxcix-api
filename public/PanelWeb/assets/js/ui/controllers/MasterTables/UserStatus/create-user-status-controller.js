// Controlador para el modal de creación de estado de usuario
class CreateUserStatusController {
    constructor(modalId, formId, onSuccess) {
        this.modal = document.getElementById(modalId);
        this.form = document.getElementById(formId);
        this.onSuccess = onSuccess;
        this.bsModal = null;
        this.init();
    }

    init() {
        if (!this.modal || !this.form) return;
        this.bsModal = bootstrap.Modal.getOrCreateInstance(this.modal);
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
        this.modal.addEventListener('hidden.bs.modal', () => this.form.reset());
    }

    open() {
        if (this.bsModal) this.bsModal.show();
    }

    async handleSubmit(e) {
        e.preventDefault();
        const name = this.form.name.value.trim();
        const active = this.form.active.checked;
        if (!name) {
            this.showError('El nombre es obligatorio.');
            return;
        }
        this.setLoading(true);
        try {
            const result = await CreateUserStatusService.createUserStatus({ name, active });
            this.setLoading(false);
            if (result && result.success) {
                this.bsModal.hide();
                if (typeof this.onSuccess === 'function') this.onSuccess(result.data);
                this.showToast('Estado de usuario creado exitosamente.', 'success');
            } else {
                this.showError('No se pudo crear el estado de usuario.');
            }
        } catch (err) {
            this.setLoading(false);
            this.showError(err.message || 'Error inesperado.');
        }
    }

    setLoading(loading) {
        const btn = this.form.querySelector('button[type="submit"]');
        if (loading) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creando...';
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1"></i>Crear Estado';
        }
    }

    showError(msg) {
        window.showGlobalToast && window.showGlobalToast(msg, 'danger');
    }

    showToast(msg, type = 'success') {
        window.showGlobalToast && window.showGlobalToast(msg, type);
    }
}
window.CreateUserStatusController = CreateUserStatusController;
