/**
 * Controlador para el modal de edición de marcas de vehículo
 * Maneja la apertura del modal, carga de datos, validación y envío del formulario
 * Usa Tabler/Bootstrap para el diseño del modal
 */
class BrandEditController {
    constructor() {
        this.isSubmitting = false;
        this.currentBrandId = null;
        this.bindEvents();
    }

    /**
     * Abre el modal de edición de marca
     * @param {number} id - ID de la marca a editar
     * @param {object} brandData - Datos de la marca obtenidos de la lista
     */
    async openEditModal(id, brandData = null) {
        try {
            console.log('📝 Abriendo modal de edición para marca:', id, brandData);
            this.currentBrandId = id;

            // Si no vienen datos, los buscamos en el controlador de lista
            if (!brandData) {
                const listCtrl = window.brandListControllerInstance || window.brandListController;
                if (!listCtrl || !listCtrl.brands) {
                    throw new Error('No se encontraron datos de marcas');
                }
                brandData = listCtrl.brands.find(b => b.id === id);
                if (!brandData) {
                    throw new Error('Marca no encontrada en la lista');
                }
            }

            // Inyectar HTML del modal
            const container = document.getElementById('modal-container');
            container.innerHTML = this.generateModalHtml();

            // Poblamos formulario y mostramos modal
            this.populateFormWithData(brandData);
            this.setupModalEvents();
            const modal = new bootstrap.Modal(document.getElementById('editBrandModal'));
            modal.show();

        } catch (err) {
            console.error('❌ Error al abrir modal de edición de marca:', err);
            window.GlobalToast?.show(err.message || 'Error al abrir el modal', 'error');
        }
    }

    /**
     * Llena el formulario con los datos de la marca
     */
    populateFormWithData(brand) {
        const nameInput    = document.getElementById('editBrandName');
        const activeSwitch = document.getElementById('editBrandActive');
        if (nameInput)    nameInput.value    = brand.name || '';
        if (activeSwitch) activeSwitch.checked = !!brand.active;

        // foco en el input
        setTimeout(() => nameInput?.focus(), 100);
    }

    /**
     * Genera el HTML del modal
     */
    generateModalHtml() {
        return `
        <div class="modal modal-blur fade" id="editBrandModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">
                  <i class="fas fa-edit text-primary me-2"></i> Editar Marca de Vehículo
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <form id="editBrandForm">
                  <div class="mb-3">
                    <label class="form-label required">
                      <i class="fas fa-tag text-muted me-1"></i> Nombre de la Marca
                    </label>
                    <input type="text" id="editBrandName" name="name"
                           class="form-control" placeholder="Ej: Toyota, Nissan..."
                           maxlength="100" required>
                    <div class="form-hint">Máximo 100 caracteres</div>
                    <div class="invalid-feedback"></div>
                  </div>
                  <div class="mb-3 form-check form-switch">
                    <input type="checkbox" id="editBrandActive" name="active"
                           class="form-check-input">
                    <label class="form-check-label" for="editBrandActive">
                      <strong>Marca Activa</strong>
                    </label>
                    <div class="form-hint text-muted">
                      Las marcas inactivas no estarán disponibles para asignación
                    </div>
                  </div>
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary me-auto"
                        data-bs-dismiss="modal">
                  <i class="fas fa-times me-1"></i> Cancelar
                </button>
                <button type="submit" class="btn btn-primary" id="updateBrandBtn"
                        form="editBrandForm">
                  <i class="fas fa-save me-1"></i> Actualizar Marca
                </button>
              </div>
            </div>
          </div>
        </div>`;
    }

    /**
     * Configura los eventos del modal
     */
    setupModalEvents() {
        const form = document.getElementById('editBrandForm');
        form?.addEventListener('submit', e => this.handleSubmit(e));
        const nameInput = document.getElementById('editBrandName');
        nameInput?.addEventListener('input', () => this.validateField(nameInput));
        nameInput?.addEventListener('blur', () => this.validateField(nameInput));
    }

    /**
     * Maneja el envío del formulario
     */
    async handleSubmit(event) {
        event.preventDefault();
        if (this.isSubmitting || !this.currentBrandId) return;

        const formData = new FormData(event.target);
        const data = {
            name:   formData.get('name').trim(),
            active: document.getElementById('editBrandActive').checked
        };

        const validation = window.BrandUpdateService.validateBrandData(data);
        if (!validation.isValid) {
            this.showValidationErrors(validation.errors);
            return;
        }

        try {
            this.setSubmittingState(true);
            const resp = await window.BrandUpdateService.updateBrand(this.currentBrandId, data);

            if (resp.success) {
                window.GlobalToast?.show(resp.message || 'Marca actualizada', 'success');
                // refrescar lista automáticamente
                const listCtrl = window.brandListControllerInstance || window.brandListController;
                if (listCtrl && typeof listCtrl.load === 'function') listCtrl.load();
                // cerrar modal
                bootstrap.Modal.getInstance(document.getElementById('editBrandModal'))?.hide();
            } else {
                window.GlobalToast?.show(resp.message || 'Error al actualizar', 'error');
            }
        } catch (err) {
            console.error('❌ Error al actualizar marca:', err);
            window.GlobalToast?.show(err.message || 'Error al actualizar la marca', 'error');
        } finally {
            this.setSubmittingState(false);
        }
    }

    /**
     * Valida un campo individual y muestra feedback
     */
    validateField(field) {
        const val = field.value.trim();
        let ok = true, msg = '';

        if (val.length === 0) {
            ok = false; msg = 'El nombre no puede estar vacío';
        } else if (val.length < 2) {
            ok = false; msg = 'Debe tener al menos 2 caracteres';
        } else if (val.length > 100) {
            ok = false; msg = 'No puede exceder 100 caracteres';
        }

        field.classList.toggle('is-valid', ok);
        field.classList.toggle('is-invalid', !ok);
        field.parentNode.querySelector('.invalid-feedback').textContent = msg;
        return ok;
    }

    /**
     * Muestra errores globales de validación
     */
    showValidationErrors(errors) {
        errors.forEach(err => {
            window.GlobalToast?.show(err, 'error');
        });
    }

    /**
     * Habilita/deshabilita botón y spinner
     */
    setSubmittingState(flag) {
        this.isSubmitting = flag;
        const btn = document.getElementById('updateBrandBtn');
        const form = document.getElementById('editBrandForm');

        if (flag) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Actualizando...';
            form.style.pointerEvents = 'none';
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1"></i>Actualizar Marca';
            form.style.pointerEvents = 'auto';
        }
    }

    /**
     * Delegación de evento para abrir modal
     */
    bindEvents() {
        document.addEventListener('click', e => {
            const btn = e.target.closest('[data-action="edit-brand"]');
            if (!btn) return;
            const id = parseInt(btn.getAttribute('data-id'), 10);
            if (id) this.openEditModal(id);
        });
    }
}

// Disponibilizar globalmente
window.brandEditController = BrandEditController;
console.log('✅ BrandEditController cargado y disponible globalmente');
