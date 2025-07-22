// Archivo: DeleteModelController.js
// Ubicación: assets/js/ui/controllers/MasterTables/VehicleModel/delete-model-controller.js

/**
 * Controlador para eliminar modelos de vehículo usando modal de confirmación global
 */
class DeleteModelController {
    constructor() {
        this.deleteService = window.DeleteModelService;
        this.confirmationModal = null;
        this.init();
    }

    /**
     * Inicializa el controlador y asegura el modal de confirmación global
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
     * @param {Object} modelData - Datos del modelo
     */
    handleDeleteButtonClick(button, modelData) {
        const onSuccess = () => {
            if (window.vehicleModelListControllerInstance && typeof window.vehicleModelListControllerInstance.load === 'function') {
                window.vehicleModelListControllerInstance.load();
            }
            if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
                window.GlobalToast.show(`Modelo "${modelData.name}" eliminado exitosamente`, 'success');
            }
        };
        const onError = (error) => {
            if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
                window.GlobalToast.show(`Error al eliminar modelo: ${error.message}`, 'error');
            }
        };
        this.requestDelete(modelData, onSuccess, onError);
    }

    /**
     * Solicita confirmación y elimina un modelo
     * @param {Object} modelData - Datos del modelo a eliminar
     * @param {Function} onSuccess - Callback en caso de éxito
     * @param {Function} onError - Callback en caso de error
     */
    async requestDelete(modelData, onSuccess = null, onError = null) {
        try {
            if (!modelData.id || typeof modelData.id !== 'number' || modelData.id <= 0) {
                const errorMessage = 'ID del modelo es requerido y debe ser un número válido';
                if (onError) onError(new Error(errorMessage));
                return;
            }
            const confirmConfig = {
                title: 'Eliminar Modelo de Vehículo',
                name: modelData.name || 'Modelo de Vehículo',
                subtitle: `¿Está seguro de que desea eliminar el modelo "${modelData.name}"? Esta acción no se puede deshacer.`,
                onConfirm: async () => {
                    await this.executeDelete(modelData, onSuccess, onError);
                }
            };
            this.confirmationModal.showConfirmation(confirmConfig);
        } catch (error) {
            if (onError) onError(error);
        }
    }

    /**
     * Ejecuta la eliminación del modelo
     * @param {Object} modelData - Datos del modelo
     * @param {Function} onSuccess - Callback de éxito
     * @param {Function} onError - Callback de error
     */
    async executeDelete(modelData, onSuccess = null, onError = null) {
        try {
            const response = await this.deleteService.deleteModel(modelData.id);
            if (onSuccess) onSuccess(response);
        } catch (error) {
            if (onError) onError(error);
        }
    }
}

// Hacer disponible globalmente
window.DeleteModelController = DeleteModelController;
