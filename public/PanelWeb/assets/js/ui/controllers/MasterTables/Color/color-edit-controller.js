/**
 * Controlador para el modal de edición de colores
 * Maneja la apertura del modal, carga de datos, validación y envío del formulario
 */
class ColorEditController {
  constructor() {
    this.isSubmitting = false;
    this.currentColorId = null;
    this.bindEvents();
  }

  /**
   * Abre el modal de edición de color
   */
  async openEditModal(id, colorData = null) {
    try {
      this.currentColorId = id;
      // Si no vienen datos, los sacamos de la lista
      if (!colorData && window.colorListController?.colorList) {
        colorData = window.colorListController.colorList.find(c => c.id === id);
      }
      const modalHtml = this.generateModalHtml();
      document.getElementById('modal-container').innerHTML = modalHtml;
      this.populateFormWithData(colorData);
      this.setupModalEvents();
      new bootstrap.Modal(document.getElementById('editColorModal')).show();
    } catch (err) {
      console.error(err);
      window.GlobalToast?.show(`Error al abrir modal de edición: ${err.message}`, 'error');
    }
  }

  populateFormWithData(color) {
    const nameInput = document.getElementById('editColorName');
    const activeSwitch = document.getElementById('editColorActive');
    if (nameInput && color) nameInput.value = color.name || '';
    if (activeSwitch && color) activeSwitch.checked = !!color.active;
    setTimeout(() => nameInput?.focus(), 100);
  }

  generateModalHtml() {
    return `
      <div class="modal modal-blur fade" id="editColorModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">
                <i class="fas fa-edit text-primary me-2"></i> Editar Color
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editColorForm">
              <div class="modal-body">
                <div class="mb-3">
                  <label class="form-label required">
                    <i class="fas fa-tag text-muted me-1"></i> Nombre del Color
                  </label>
                  <input type="text" class="form-control" id="editColorName" name="name"
                         placeholder="Ej: Azul, Rojo..." maxlength="100" required>
                  <div class="form-hint">Máximo 100 caracteres</div>
                  <div class="invalid-feedback"></div>
                </div>
                <div class="form-check form-switch mb-3">
                  <input class="form-check-input" type="checkbox" id="editColorActive" name="active">
                  <label class="form-check-label" for="editColorActive">
                    <strong>Color Activo</strong>
                  </label>
                  <div class="form-hint text-muted">
                    Los colores inactivos no estarán disponibles para asignación
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary me-auto" data-bs-dismiss="modal">
                  <i class="fas fa-times me-1"></i> Cancelar
                </button>
                <button type="submit" class="btn btn-primary" id="updateColorBtn">
                  <i class="fas fa-save me-1"></i> Actualizar Color
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>`;
  }

  setupModalEvents() {
    document.getElementById('editColorForm')
      .addEventListener('submit', e => this.handleSubmit(e));
    document.getElementById('editColorName')
      .addEventListener('input', e => this.validateField(e.target));
  }

  async handleSubmit(event) {
    event.preventDefault();
    if (this.isSubmitting || this.currentColorId == null) return;

    const data = {
      name: new FormData(event.target).get('name').trim(),
      active: document.getElementById('editColorActive').checked
    };
    const validation = window.ColorUpdateService.validateColorData(data);
    if (!validation.isValid) {
      this.showValidationErrors(validation.errors);
      return;
    }

    this.setSubmittingState(true);
    try {
      const res = await window.ColorUpdateService.updateColor(this.currentColorId, data);
      if (res.success) {
        window.GlobalToast?.show(res.message || 'Color actualizado', 'success');
        bootstrap.Modal.getInstance(document.getElementById('editColorModal')).hide();
        window.colorListController?.load(window.colorListController.currentPage);
      }
    } catch (err) {
      console.error(err);
      window.GlobalToast?.show(err.message || 'Error al actualizar color', 'error');
    } finally {
      this.setSubmittingState(false);
    }
  }

  validateField(field) {
    const v = field.value.trim();
    let valid = true, msg = '';
    if (v.length < 2) valid = false, msg = 'Debe tener al menos 2 caracteres';
    else if (v.length > 100) valid = false, msg = 'No puede exceder 100 caracteres';
    field.classList.toggle('is-invalid', !valid);
    field.parentNode.querySelector('.invalid-feedback').textContent = msg;
    return valid;
  }

  showValidationErrors(errors) {
    const nameField = document.getElementById('editColorName');
    const msg = errors.find(e => /nombre/i.test(e)) || errors[0];
    nameField.classList.add('is-invalid');
    nameField.parentNode.querySelector('.invalid-feedback').textContent = msg;
  }

  setSubmittingState(flag) {
    this.isSubmitting = flag;
    document.getElementById('updateColorBtn').disabled = flag;
  }

  bindEvents() {
    document.addEventListener('click', e => {
      const btn = e.target.closest('[data-action="edit-color"]');
      if (!btn) return;
      const id = Number(btn.dataset.id);
      const colorData = window.colorListController?.colorList.find(c => c.id === id) || null;
      this.openEditModal(id, colorData);
    });
  }
}

// Exportar globalmente la clase
window.ColorEditController = ColorEditController;
