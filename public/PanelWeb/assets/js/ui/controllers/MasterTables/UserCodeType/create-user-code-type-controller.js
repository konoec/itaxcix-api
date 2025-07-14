// Controlador para el modal de creación de tipo de código de usuario
class CreateUserCodeTypeController {
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
            const result = await CreateUserCodeTypeService.createUserCodeType({ name, active });
            this.setLoading(false);
            if (result && result.success) {
                this.bsModal.hide();
                if (typeof this.onSuccess === 'function') this.onSuccess(result.data);
                this.showToast('Tipo de código de usuario creado exitosamente.', 'success');
            } else {
                this.showError('No se pudo crear el tipo de código de usuario.');
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
            btn.innerHTML = '<i class="fas fa-save me-1"></i>Crear Tipo';
        }
    }

    showError(msg) {
        if (window.showRecoveryToast) {
            window.showRecoveryToast(msg, 'error');
        } else if (window.showToast) {
            window.showToast(msg, 'error');
        } else if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
            window.GlobalToast.show(msg, 'error');
        } else {
            alert(msg);
        }
    }

    showToast(msg, type = 'success') {
        if (window.showRecoveryToast) {
            window.showRecoveryToast(msg, type);
        } else if (window.showToast) {
            window.showToast(msg, type);
        } else if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
            window.GlobalToast.show(msg, type);
        } else {
            alert(msg);
        }
    }
}
window.CreateUserCodeTypeController = CreateUserCodeTypeController;
