// Archivo: /controllers/DeleteDocumentTypeController.js
// Controlador para eliminar tipos de documento usando modal de confirmación global
class DeleteDocumentTypeController {
    constructor() {
        this.deleteService = window.DeleteDocumentTypeService;
        this.confirmationModal = null;
        this.init();
    }

    // Inicializa el controlador y asegura el modal de confirmación
    init() {
        this.ensureConfirmationModal();
    }

    // Busca el modal de confirmación global o crea uno básico si no existe
    ensureConfirmationModal() {
        if (window.globalConfirmationModal) {
            this.confirmationModal = window.globalConfirmationModal;
        } else {
            // Modal básico de emergencia
            const modalHTML = `
                <div class="modal modal-blur fade" id="basic-delete-confirmation-modal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Confirmar Eliminación
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-trash-alt text-danger" style="font-size: 3rem;"></i>
                                    </div>
                                    <h4 id="basic-confirmation-title">¿Eliminar tipo de documento?</h4>
                                    <p id="basic-confirmation-message" class="text-muted">Esta acción no se puede deshacer.</p>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i>
                                    Cancelar
                                </button>
                                <button type="button" class="btn btn-danger" id="basic-confirm-delete-btn">
                                    <i class="fas fa-trash-alt me-1"></i>
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            if (!document.getElementById('basic-delete-confirmation-modal')) {
                document.body.insertAdjacentHTML('beforeend', modalHTML);
            }
            this.confirmationModal = {
                showConfirmation: (config, onConfirm) => {
                    const modal = document.getElementById('basic-delete-confirmation-modal');
                    const titleEl = document.getElementById('basic-confirmation-title');
                    const messageEl = document.getElementById('basic-confirmation-message');
                    const confirmBtn = document.getElementById('basic-confirm-delete-btn');
                    if (titleEl) titleEl.textContent = config.title || '¿Eliminar tipo de documento?';
                    // Mostrar el nombre del elemento a eliminar en el mensaje
                    if (messageEl) messageEl.textContent = config.name ? `¿Eliminar "${config.name}"?` : (config.details || 'Esta acción no se puede deshacer.');
                    // Limpiar listeners previos para evitar duplicados
                    const newBtn = confirmBtn.cloneNode(true);
                    confirmBtn.parentNode.replaceChild(newBtn, confirmBtn);
                    const handleConfirm = async () => {
                        newBtn.removeEventListener('click', handleConfirm);
                        if (typeof onConfirm === 'function') await onConfirm();
                        const bsModal = bootstrap.Modal.getInstance(modal);
                        if (bsModal) bsModal.hide();
                    };
                    newBtn.addEventListener('click', handleConfirm);
                    const bsModal = new bootstrap.Modal(modal);
                    bsModal.show();
                }
            };
        }
    }

    /**
     * Maneja el click del botón eliminar desde la tabla
     * @param {HTMLElement} button - Botón que disparó la acción
     * @param {Object} documentTypeData - Datos del tipo de documento (debe tener id y name)
     */
    handleDeleteButtonClick(button, documentTypeData) {
        const name = documentTypeData.name || '';
        const config = {
            title: 'Eliminar Tipo de Documento',
            details: `¿Está seguro de que desea eliminar el tipo "${name}"?`,
            subtitle: 'Esta acción no se puede deshacer.',
            confirmText: 'Sí, eliminar',
            cancelText: 'Cancelar',
            confirmClass: 'btn-danger',
            icon: 'fas fa-exclamation-triangle',
            iconClass: 'text-danger',
            name, // Para el modal básico
            onConfirm: async () => {
                try {
                    await this.deleteService.deleteDocumentType(documentTypeData.id);
                    if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
                        window.GlobalToast.show(`Tipo de documento "${name}" eliminado correctamente.`, 'success');
                    }
                    // Recargar tabla usando ambos posibles controladores
                    if (window.documentTypeListController && typeof window.documentTypeListController.load === 'function') {
                        window.documentTypeListController.load();
                    } else if (window.documentTypesListController && typeof window.documentTypesListController.load === 'function') {
                        window.documentTypesListController.load();
                    }
                } catch (error) {
                    if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
                        window.GlobalToast.show(error.message || 'Error al eliminar tipo de documento', 'error');
                    }
                }
            }
        };
        // Soporta ambos: modal global (usa config.onConfirm) y modal básico (usa callback como 2do argumento)
        if (this.confirmationModal && typeof this.confirmationModal.showConfirmation === 'function') {
            if (this.confirmationModal === window.globalConfirmationModal) {
                this.confirmationModal.showConfirmation(config);
            } else {
                this.confirmationModal.showConfirmation(config, config.onConfirm);
            }
        }
    }
}
// Exportar como global
window.DeleteDocumentTypeController = new DeleteDocumentTypeController();
