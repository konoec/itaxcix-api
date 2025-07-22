// Archivo: DeleteVehicleClassController.js
// Ubicación: assets/js/ui/controllers/MasterTables/VehicleClass/delete-vehicle-class-controller.js

/**
 * Controlador para eliminar clases de vehículo usando modal de confirmación global
 */
class DeleteVehicleClassController {
    constructor() {
        this.deleteService = window.DeleteVehicleClassService;
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
     * @param {Object} vehicleClassData - Datos de la clase de vehículo
     */
    handleDeleteButtonClick(button, vehicleClassData) {
        const onSuccess = () => {
            if (window.vehicleClassListController && typeof window.vehicleClassListController.load === 'function') {
                window.vehicleClassListController.load();
            }
            if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
                window.GlobalToast.show(`Clase de vehículo "${vehicleClassData.name}" eliminada exitosamente`, 'success');
            }
        };
        const onError = (error) => {
            if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
                window.GlobalToast.show(`Error al eliminar clase de vehículo: ${error.message}`, 'error');
            }
        };
        this.requestDelete(vehicleClassData, onSuccess, onError);
    }

    /**
     * Solicita confirmación y elimina una clase de vehículo
     * @param {Object} vehicleClassData - Datos de la clase a eliminar
     * @param {Function} onSuccess - Callback en caso de éxito
     * @param {Function} onError - Callback en caso de error
     */
    async requestDelete(vehicleClassData, onSuccess = null, onError = null) {
        try {
            if (!vehicleClassData.id || typeof vehicleClassData.id !== 'number' || vehicleClassData.id <= 0) {
                const errorMessage = 'ID de la clase de vehículo es requerido y debe ser un número válido';
                if (onError) onError(new Error(errorMessage));
                return;
            }
            const confirmConfig = {
                title: 'Eliminar Clase de Vehículo',
                name: vehicleClassData.name || 'Clase de Vehículo',
                subtitle: `¿Está seguro de que desea eliminar la clase "${vehicleClassData.name}"? Esta acción no se puede deshacer.`,
                onConfirm: async () => {
                    await this.executeDelete(vehicleClassData, onSuccess, onError);
                }
            };
            this.confirmationModal.showConfirmation(confirmConfig);
        } catch (error) {
            if (onError) onError(error);
        }
    }

    /**
     * Ejecuta la eliminación de la clase de vehículo
     * @param {Object} vehicleClassData - Datos de la clase
     * @param {Function} onSuccess - Callback de éxito
     * @param {Function} onError - Callback de error
     */
    async executeDelete(vehicleClassData, onSuccess = null, onError = null) {
        try {
            const response = await this.deleteService.deleteVehicleClass(vehicleClassData.id);
            if (onSuccess) onSuccess(response);
        } catch (error) {
            if (onError) onError(error);
        }
    }
}

// Hacer disponible globalmente
window.DeleteVehicleClassController = DeleteVehicleClassController;
