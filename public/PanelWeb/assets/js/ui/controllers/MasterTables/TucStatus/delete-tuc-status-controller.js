// Archivo: DeleteTucStatusController.js
// Ubicación: assets/js/ui/controllers/MasterTables/TucStatus/delete-tuc-status-controller.js

/**
 * Controlador para eliminar estados TUC usando modal de confirmación global
 */
class DeleteTucStatusController {
    constructor() {
        this.deleteService = window.DeleteTucStatusService;
        this.confirmationModal = null;
        this.init();
    }

    /**
     * Inicializa el controlador y asegura el modal de confirmación
     */
    init() {
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
     * @param {Object} tucStatusData - Datos del estado TUC
     */
    handleDeleteButtonClick(button, tucStatusData) {
        const onSuccess = () => {
            // Recargar la tabla usando la instancia global, igual que el de editar
            if (window.tucStatusListControllerInstance && typeof window.tucStatusListControllerInstance.load === 'function') {
                window.tucStatusListControllerInstance.load();
            }
            if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
                window.GlobalToast.show(`Estado TUC "${tucStatusData.name}" eliminado exitosamente`, 'success');
            }
        };
        const onError = (error) => {
            if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
                window.GlobalToast.show(`Error al eliminar estado TUC: ${error.message}`, 'error');
            }
        };
        this.requestDelete(tucStatusData, onSuccess, onError);
    }

    /**
     * Solicita confirmación y elimina un estado TUC
     * @param {Object} tucStatusData - Datos del estado TUC a eliminar
     * @param {Function} onSuccess - Callback en caso de éxito
     * @param {Function} onError - Callback en caso de error
     */
    async requestDelete(tucStatusData, onSuccess = null, onError = null) {
        try {
            if (!tucStatusData.id || typeof tucStatusData.id !== 'number' || tucStatusData.id <= 0) {
                const errorMessage = 'ID del estado TUC es requerido y debe ser un número válido';
                if (onError) onError(new Error(errorMessage));
                return;
            }
            const confirmConfig = {
                title: 'Eliminar Estado TUC',
                name: tucStatusData.name || 'Estado TUC',
                subtitle: `¿Está seguro de que desea eliminar el estado TUC "${tucStatusData.name}"? Esta acción no se puede deshacer.`,
                onConfirm: async () => {
                    await this.executeDelete(tucStatusData, onSuccess, onError);
                }
            };
            this.confirmationModal.showConfirmation(confirmConfig);
        } catch (error) {
            if (onError) onError(error);
        }
    }

    /**
     * Ejecuta la eliminación del estado TUC
     * @param {Object} tucStatusData - Datos del estado TUC
     * @param {Function} onSuccess - Callback de éxito
     * @param {Function} onError - Callback de error
     */
    async executeDelete(tucStatusData, onSuccess = null, onError = null) {
        try {
            const response = await this.deleteService.deleteTucStatus(tucStatusData.id);
            if (onSuccess) onSuccess(response);
        } catch (error) {
            if (onError) onError(error);
        }
    }
}

// Exportar la clase globalmente para instanciación modular
window.DeleteTucStatusController = DeleteTucStatusController;
