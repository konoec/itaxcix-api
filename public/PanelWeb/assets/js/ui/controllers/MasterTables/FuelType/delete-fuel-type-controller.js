// Archivo: DeleteFuelTypeController.js
// Ubicación: assets/js/ui/controllers/MasterTables/FuelType/delete-fuel-type-controller.js

/**
 * Controlador para eliminar tipos de combustible usando modal de confirmación global
 */
class DeleteFuelTypeController {
    constructor() {
        this.deleteService = window.DeleteFuelTypeService;
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
     * @param {Object} fuelTypeData - Datos del tipo de combustible
     */
    handleDeleteButtonClick(button, fuelTypeData) {
        const onSuccess = () => {
            if (window.fuelTypeController && typeof window.fuelTypeController.refresh === 'function') {
                window.fuelTypeController.refresh();
            }
            if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
                window.GlobalToast.show(`Tipo de combustible "${fuelTypeData.name}" eliminado exitosamente`, 'success');
            }
        };
        const onError = (error) => {
            if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
                window.GlobalToast.show(`Error al eliminar tipo de combustible: ${error.message}`, 'error');
            }
        };
        this.requestDelete(fuelTypeData, onSuccess, onError);
    }

    /**
     * Solicita confirmación y elimina un tipo de combustible
     * @param {Object} fuelTypeData - Datos del tipo de combustible a eliminar
     * @param {Function} onSuccess - Callback en caso de éxito
     * @param {Function} onError - Callback en caso de error
     */
    async requestDelete(fuelTypeData, onSuccess = null, onError = null) {
        try {
            // Validación básica
            if (!fuelTypeData.id || typeof fuelTypeData.id !== 'number' || fuelTypeData.id <= 0) {
                const errorMessage = 'ID del tipo de combustible es requerido y debe ser un número válido';
                if (onError) onError(new Error(errorMessage));
                return;
            }
            // Configuración del modal de confirmación
            const confirmConfig = {
                title: 'Eliminar Tipo de Combustible',
                name: fuelTypeData.name || 'Tipo de Combustible',
                subtitle: `¿Está seguro de que desea eliminar el tipo de combustible "${fuelTypeData.name}"? Esta acción no se puede deshacer.`,
                onConfirm: async () => {
                    await this.executeDelete(fuelTypeData, onSuccess, onError);
                }
            };
            this.confirmationModal.showConfirmation(confirmConfig);
        } catch (error) {
            if (onError) onError(error);
        }
    }

    /**
     * Ejecuta la eliminación del tipo de combustible
     * @param {Object} fuelTypeData - Datos del tipo de combustible
     * @param {Function} onSuccess - Callback de éxito
     * @param {Function} onError - Callback de error
     */
    async executeDelete(fuelTypeData, onSuccess = null, onError = null) {
        try {
            // Forzar parseo de id
            const fuelTypeId = parseInt(fuelTypeData.id, 10);
            if (!fuelTypeId || isNaN(fuelTypeId) || fuelTypeId <= 0) {
                console.warn('ID de tipo de combustible inválido en executeDelete:', fuelTypeData.id);
                if (onError) onError(new Error('ID del tipo de combustible es requerido y debe ser un número válido'));
                return;
            }
            const response = await this.deleteService.deleteFuelType(fuelTypeId);
            if (onSuccess) onSuccess(response);
        } catch (error) {
            if (onError) onError(error);
        }
    }
}

// Hacer disponible globalmente
window.DeleteFuelTypeController = DeleteFuelTypeController;
