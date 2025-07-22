// Archivo: /controllers/DeleteContactTypeController.js
// Controlador para eliminar tipos de contacto usando modal de confirmación global
class DeleteContactTypeController {
    constructor() {
        this.deleteService = window.DeleteContactTypeService;
        this.confirmationModal = null;
        this.init();
    }

    init() {
        this.ensureConfirmationModal();
    }

    // Solo usa el modal global, si no existe muestra error y no permite eliminar
    ensureConfirmationModal() {
        if (window.globalConfirmationModal) {
            this.confirmationModal = window.globalConfirmationModal;
        } else {
            this.confirmationModal = null;
            console.error('❌ No se encontró el modal de confirmación global. No se puede eliminar.');
        }
    }

    /**
     * Maneja el click del botón eliminar desde la tabla
     * @param {HTMLElement} button - Botón que disparó la acción
     * @param {Object} contactTypeData - Datos del tipo de contacto
     */
    handleDeleteButtonClick(button, contactTypeData) {
        // Si no viene el nombre, intentar obtenerlo del DOM (segunda columna de la fila)
        let name = contactTypeData.name;
        if (!name && button && button.closest('tr')) {
            const cells = button.closest('tr').querySelectorAll('td');
            if (cells.length > 1) {
                name = cells[1].textContent.trim();
            }
        }
        contactTypeData.name = name || 'Tipo de Contacto';
        const onSuccess = () => {
            // Recargar la tabla usando la instancia más actual
            if (window.contactTypeListControllerInstance && typeof window.contactTypeListControllerInstance.load === 'function') {
                window.contactTypeListControllerInstance.load();
            } else if (window.contactTypeListController && typeof window.contactTypeListController.load === 'function') {
                window.contactTypeListController.load();
            }
            if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
                window.GlobalToast.show('Tipo de contacto eliminado exitosamente', 'success');
            }
        };
        const onError = (error) => {
            if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
                window.GlobalToast.show(error.message || 'Error al eliminar tipo de contacto', 'error');
            }
        };
        this.requestDelete(contactTypeData, onSuccess, onError);
    }

    /**
     * Solicita confirmación y elimina un tipo de contacto
     * @param {Object} contactTypeData - Datos del tipo de contacto a eliminar
     * @param {Function} onSuccess - Callback ejecutado en caso de éxito
     * @param {Function} onError - Callback ejecutado en caso de error
     */
    async requestDelete(contactTypeData, onSuccess, onError) {
        if (!this.confirmationModal) {
            if (onError) onError(new Error('No se encontró el modal de confirmación global. No se puede eliminar.'));
            return;
        }
        const contactTypeName = contactTypeData.name || 'Tipo de Contacto';
        this.confirmationModal.showConfirmation({
            title: 'Eliminar Tipo de Contacto',
            name: contactTypeName,
            subtitle: 'Esta acción no se puede deshacer.',
            avatarColor: 'bg-blue',
            confirmText: 'Eliminar',
            loadingText: 'Eliminando...',
            onConfirm: async () => {
                try {
                    await this.deleteService.deleteContactType(contactTypeData.id);
                    if (onSuccess) onSuccess();
                } catch (error) {
                    if (onError) onError(error);
                }
            }
        });
    }
}
// Exportar globalmente
window.DeleteContactTypeController = DeleteContactTypeController;
