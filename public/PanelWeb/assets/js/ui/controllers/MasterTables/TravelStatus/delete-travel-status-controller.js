// Archivo: DeleteTravelStatusController.js
// Ubicación: assets/js/ui/controllers/MasterTables/TravelStatus/delete-travel-status-controller.js

/**
 * Controlador para eliminar estados de viaje usando modal de confirmación global
 */
class DeleteTravelStatusController {
    constructor() {
        this.deleteService = window.DeleteTravelStatusService;
        this.confirmationModal = null;
        this.init();
    }

    /**
     * Inicializa el controlador y asegura el modal de confirmación
     */
    init() {
        this.ensureConfirmationModal();
    }

    /**
     * Verifica que el modal de confirmación global esté disponible
     */
    ensureConfirmationModal() {
        if (window.globalConfirmationModal) {
            this.confirmationModal = window.globalConfirmationModal;
            console.log('✅ Modal de confirmación global encontrado (globalConfirmationModal)');
        } else {
            throw new Error('No se encontró el modal global de confirmación (globalConfirmationModal)');
        }
    }

    /**
     * Maneja el click del botón eliminar desde la tabla
     * @param {HTMLElement} button - Botón que disparó la acción
     * @param {Object} travelStatusData - Datos del estado de viaje
     */
    handleDeleteButtonClick(button, travelStatusData) {
        const onSuccess = () => {
            if (window.travelStatusListController && typeof window.travelStatusListController.load === 'function') {
                window.travelStatusListController.load();
            }
            if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
                window.GlobalToast.show(`Estado de viaje "${travelStatusData.name}" eliminado exitosamente`, 'success');
            }
        };
        const onError = (error) => {
            if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
                window.GlobalToast.show(`Error al eliminar estado de viaje: ${error.message}`, 'error');
            }
        };
        this.requestDelete(travelStatusData, onSuccess, onError);
    }

    /**
     * Solicita confirmación y elimina un estado de viaje
     * @param {Object} travelStatusData - Datos del estado de viaje a eliminar
     * @param {Function} onSuccess - Callback en caso de éxito
     * @param {Function} onError - Callback en caso de error
     */
    async requestDelete(travelStatusData, onSuccess = null, onError = null) {
        try {
            if (!travelStatusData.id || typeof travelStatusData.id !== 'number' || travelStatusData.id <= 0) {
                const errorMessage = 'ID del estado de viaje es requerido y debe ser un número válido';
                if (onError) onError(new Error(errorMessage));
                return;
            }
            const confirmConfig = {
                title: 'Eliminar Estado de Viaje',
                name: travelStatusData.name || 'Estado de Viaje',
                subtitle: `¿Está seguro de que desea eliminar el estado de viaje "${travelStatusData.name}"? Esta acción no se puede deshacer.`,
                onConfirm: async () => {
                    await this.executeDelete(travelStatusData, onSuccess, onError);
                }
            };
            this.confirmationModal.showConfirmation(confirmConfig);
        } catch (error) {
            if (onError) onError(error);
        }
    }

    /**
     * Ejecuta la eliminación del estado de viaje
     * @param {Object} travelStatusData - Datos del estado de viaje
     * @param {Function} onSuccess - Callback de éxito
     * @param {Function} onError - Callback de error
     */
    async executeDelete(travelStatusData, onSuccess = null, onError = null) {
        try {
            const response = await this.deleteService.deleteTravelStatus(travelStatusData.id);
            if (onSuccess) onSuccess(response);
        } catch (error) {
            if (onError) onError(error);
        }
    }
}

// Hacer disponible globalmente
window.DeleteTravelStatusController = DeleteTravelStatusController;
