// Archivo: delete-infraction-status-controller.js
// Ubicación: assets/js/ui/controllers/MasterTables/InfractionStatus/delete-infraction-status-controller.js

/**
 * Controlador para eliminar estados de infracción usando el modal de confirmación global
 */
class DeleteInfractionStatusController {
    constructor() {
        this.deleteService = window.DeleteInfractionStatusService;
        this.confirmationModal = window.globalConfirmationModal;
        if (!this.confirmationModal) {
            throw new Error('No se encontró el modal global de confirmación (window.globalConfirmationModal)');
        }
    }

    /**
     * Maneja el click del botón eliminar desde la tabla
     * @param {HTMLElement} button - Botón que disparó la acción
     * @param {Object} infractionStatusData - Datos del estado de infracción
     */
    handleDeleteButtonClick(button, infractionStatusData) {
        const onSuccess = () => {
            if (window.infractionStatusListController && typeof window.infractionStatusListController.load === 'function') {
                window.infractionStatusListController.load();
            }
            if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
                window.GlobalToast.show(`Estado de infracción "${infractionStatusData.name}" eliminado exitosamente`, 'success');
            }
        };
        const onError = (error) => {
            if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
                window.GlobalToast.show(`Error al eliminar estado de infracción: ${error.message}`, 'error');
            }
        };
        this.requestDelete(infractionStatusData, onSuccess, onError);
    }

    /**
     * Solicita confirmación y elimina un estado de infracción
     * @param {Object} infractionStatusData - Datos del estado de infracción a eliminar
     * @param {Function} onSuccess - Callback en caso de éxito
     * @param {Function} onError - Callback en caso de error
     */
    async requestDelete(infractionStatusData, onSuccess = null, onError = null) {
        try {
            // Validación básica
            if (!infractionStatusData.id || typeof infractionStatusData.id !== 'number' || infractionStatusData.id <= 0) {
                const errorMessage = 'ID del estado de infracción es requerido y debe ser un número válido';
                if (onError) onError(new Error(errorMessage));
                return;
            }
            // Configuración del modal de confirmación
            const confirmConfig = {
                title: 'Eliminar Estado de Infracción',
                name: infractionStatusData.name || 'Estado de Infracción',
                subtitle: `¿Está seguro de que desea eliminar el estado "${infractionStatusData.name}"? Esta acción no se puede deshacer.`,
                onConfirm: async () => {
                    await this.executeDelete(infractionStatusData, onSuccess, onError);
                }
            };
            this.confirmationModal.showConfirmation(confirmConfig);
        } catch (error) {
            if (onError) onError(error);
        }
    }

    /**
     * Ejecuta la eliminación del estado de infracción
     * @param {Object} infractionStatusData - Datos del estado de infracción
     * @param {Function} onSuccess - Callback de éxito
     * @param {Function} onError - Callback de error
     */
    async executeDelete(infractionStatusData, onSuccess = null, onError = null) {
        try {
            const response = await this.deleteService.deleteInfractionStatus(infractionStatusData.id);
            if (onSuccess) onSuccess(response);
        } catch (error) {
            if (onError) onError(error);
        }
    }
}

// Exportar como clase global
window.DeleteInfractionStatusController = DeleteInfractionStatusController;
