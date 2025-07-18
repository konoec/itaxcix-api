/**
 * Controlador para el modal de edición de empresa
 * Se encarga de cargar datos, validar y enviar la actualización
 */
class EditCompanyController {
  /**
   * @param {CompanyService} companyService – instancia compartida de servicio
   * @param {string} modalId – ID del modal de edición
   * @param {string} formId – ID del formulario de edición
   */
  constructor(companyService, modalId = 'edit-company-modal', formId = 'edit-company-form') {
    this.companyService = companyService;
    this.modalEl     = document.getElementById(modalId);
    this.formEl      = document.getElementById(formId);
    this.submitBtn   = document.getElementById('edit-company-submit');
    this.init();
  }

  init() {
    if (!this.modalEl || !this.formEl) {
      console.error('❌ EditCompanyController: modal o form no encontrados');
      return;
    }
    // Listener de envío
    this.formEl.addEventListener('submit', e => this.handleSubmit(e));
  }

  /**
   * Abre el modal y precarga datos de la empresa
   * @param {{id:number,name:string,ruc:string,active:boolean}} company 
   */
  open(company) {
    // Rellenar campos
    this.formEl.elements['id'].value     = company.id;
    this.formEl.elements['name'].value   = company.name;
    this.formEl.elements['ruc'].value    = company.ruc;
    this.formEl.elements['active'].checked = company.active;
    // Reset validaciones
    this.clearValidation();
    // Mostrar modal
    const bsModal = new bootstrap.Modal(this.modalEl);
    bsModal.show();
  }

  clearValidation() {
    this.formEl.querySelectorAll('.is-valid, .is-invalid').forEach(i => i.classList.remove('is-valid','is-invalid'));
    this.formEl.querySelectorAll('.invalid-feedback').forEach(f => f.textContent = '');
  }

  async handleSubmit(event) {
    event.preventDefault();
    const formData = new FormData(this.formEl);
    const companyId   = formData.get('id');
    const companyData = {
      name:   formData.get('name'),
      ruc:    formData.get('ruc'),
      active: formData.get('active') === 'on'
    };

    // Feedback al usuario
    const originalText = this.submitBtn.innerHTML;
    this.submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Actualizando';
    this.submitBtn.disabled = true;

    try {
      const res = await this.companyService.update(companyId, companyData);
      if (res.success) {
        window.showToast?.('Empresa actualizada', 'success');
        bootstrap.Modal.getInstance(this.modalEl).hide();
        // Disparar evento global para recargar listado
        window.dispatchEvent(new CustomEvent('company:updated'));
      } else {
        window.showToast?.('Error al actualizar', 'error');
      }
    } catch (err) {
      console.error(err);
      window.showToast?.('Error inesperado', 'error');
    } finally {
      this.submitBtn.innerHTML = originalText;
      this.submitBtn.disabled = false;
    }
  }
}
