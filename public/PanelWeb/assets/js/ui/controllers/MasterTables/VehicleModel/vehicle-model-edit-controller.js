/**
 * Controlador para el modal de edición de modelos de vehículo
 * Maneja la apertura del modal, carga de datos, validación y envío del formulario
 */
class VehicleModelEditController {
  constructor() {
    this.isSubmitting    = false;
    this.currentModelId  = null;
    this.currentBrandId  = null;    // guardamos la marca internamente
    this.currentBrandName= '';      // para mostrarla en el modal
    this.bindEvents();
  }

  /**
   * Abre el modal de edición de modelo
   * @param {number} id - ID del modelo a editar
   */
  async openEditModal(id) {
    try {
      console.log('📝 Abriendo modal de edición para modelo:', id);
      this.currentModelId = id;

      // Recuperar datos del controlador de lista
      const listCtrl = window.vehicleModelListControllerInstance
                    || window.vehicleModelListController;
      if (!listCtrl || !Array.isArray(listCtrl.items)) {
        throw new Error('No se encontraron datos de modelos');
      }
      const model = listCtrl.items.find(m => m.id === id);
      if (!model) {
        throw new Error('Modelo no encontrado en la lista');
      }

      // Guardar la marca para el envío
      this.currentBrandId   = model.brandId;
      this.currentBrandName = model.brandName;

      // Inyectar HTML del modal
      document.getElementById('modal-container').innerHTML = this.generateModalHtml();

      // Poblamos formulario con los datos
      this.populateForm(model);
      this.setupModalEvents();

      // Mostrar modal
      new bootstrap.Modal(document.getElementById('editVehicleModelModal')).show();
    } catch (err) {
      console.error('❌ Error al abrir modal de edición:', err);
      window.GlobalToast?.show(err.message, 'error');
    }
  }

  /**
   * Llena los campos del formulario
   */
  populateForm(model) {
    document.getElementById('editVehicleModelName').value     = model.name;
    document.getElementById('editVehicleModelBrandName').textContent = this.currentBrandName;
    document.getElementById('editVehicleModelActive').checked = !!model.active;
    setTimeout(() => document.getElementById('editVehicleModelName').focus(), 100);
  }

  /**
   * Genera el HTML del modal (sin campo de ID de marca)
   */
  generateModalHtml() {
    return `
      <div class="modal modal-blur fade" id="editVehicleModelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">
                <i class="fas fa-edit text-primary me-2"></i>
                Editar Modelo de Vehículo
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
              <form id="editVehicleModelForm">
                <div class="mb-3">
                  <label class="form-label required">Nombre del Modelo</label>
                  <input type="text" id="editVehicleModelName" name="name"
                         class="form-control" maxlength="100" required>
                  <div class="invalid-feedback"></div>
                </div>
                <div class="mb-3">
                  <label class="form-label">Marca</label>
                  <p id="editVehicleModelBrandName" class="form-control-plaintext">
                    <!-- rellena con populateForm -->
                  </p>
                </div>
                <div class="form-check form-switch mb-3">
                  <input class="form-check-input" type="checkbox"
                         id="editVehicleModelActive" name="active">
                  <label class="form-check-label" for="editVehicleModelActive">
                    <strong>Activo</strong>
                  </label>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-outline-secondary me-auto"
                      data-bs-dismiss="modal">
                <i class="fas fa-times me-1"></i> Cancelar
              </button>
              <button type="submit" class="btn btn-primary" id="updateVehicleModelBtn"
                      form="editVehicleModelForm">
                <i class="fas fa-save me-1"></i> Guardar Cambios
              </button>
            </div>
          </div>
        </div>
      </div>`;
  }

  /**
   * Configura listeners del formulario
   */
  setupModalEvents() {
    const form = document.getElementById('editVehicleModelForm');
    form.addEventListener('submit', e => this.handleSubmit(e));

    ['editVehicleModelName'].forEach(id => {
      const fld = document.getElementById(id);
      fld.addEventListener('input', () => this.validateField(fld));
      fld.addEventListener('blur',  () => this.validateField(fld));
    });
  }

  /**
   * Maneja el envío del formulario
   */
  async handleSubmit(event) {
    event.preventDefault();
    if (this.isSubmitting || !this.currentModelId) return;

    const name   = document.getElementById('editVehicleModelName').value.trim();
    const active = document.getElementById('editVehicleModelActive').checked;

    // Validar campos
    const errors = [];
    if (name.length < 2 || name.length > 100) errors.push('El nombre debe tener entre 2 y 100 caracteres');
    if (errors.length) {
      errors.forEach(err => window.GlobalToast?.show(err, 'error'));
      return;
    }

    try {
      this.setSubmitting(true);
      // Enviamos solo name, brandId (guardado internamente), active
      const resp = await window.ModelUpdateService.updateModel(
        this.currentModelId,
        { name, brandId: this.currentBrandId, active }
      );
      window.GlobalToast?.show(resp.message, 'success');
      // Refrescar lista y cerrar modal
      window.vehicleModelListControllerInstance.load(window.vehicleModelListControllerInstance.currentPage);
      bootstrap.Modal.getInstance(document.getElementById('editVehicleModelModal')).hide();
    } catch (err) {
      console.error('❌ Error al actualizar modelo:', err);
      window.GlobalToast?.show(err.message, 'error');
    } finally {
      this.setSubmitting(false);
    }
  }

  /**
   * Valida un solo campo y muestra feedback
   */
  validateField(field) {
    let ok = true, msg = '';
    const v = field.value.trim();
    if (field.id === 'editVehicleModelName' && (v.length < 2 || v.length > 100)) {
      ok = false; msg = '2–100 caracteres';
    }
    field.classList.toggle('is-valid', ok);
    field.classList.toggle('is-invalid', !ok);
    field.nextElementSibling.textContent = msg;
    return ok;
  }

  /**
   * Habilita/deshabilita el botón y muestra spinner
   */
  setSubmitting(flag) {
    this.isSubmitting = flag;
    const btn = document.getElementById('updateVehicleModelBtn');
    if (flag) {
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';
    } else {
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-save me-1"></i>Guardar Cambios';
    }
  }

  /**
   * Delegación: escucha clicks en botones de editar
   */
  bindEvents() {
    document.addEventListener('click', e => {
      const btn = e.target.closest('button[title="Editar"]');
      if (!btn) return;
      const tr = btn.closest('tr');
      const id = parseInt(tr.querySelector('td.text-center').textContent, 10);
      if (id) this.openEditModal(id);
    });
  }
}

// Exportar la instancia global
window.vehicleModelEditController = window.VehicleModelEditController;
console.log('✅ VehicleModelEditController disponible globalmente');
