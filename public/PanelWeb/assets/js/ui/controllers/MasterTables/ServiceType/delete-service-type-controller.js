// Archivo: DeleteServiceTypeController.js
// Ubicación: assets/js/ui/controllers/MasterTables/ServiceType/delete-service-type-controller.js

/**
 * Controlador para eliminar tipos de servicio usando modal de confirmación global
 */
class DeleteServiceTypeController {
    constructor() {
        this.deleteService = window.DeleteServiceTypeService;
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
     * @param {Object} serviceTypeData - Datos del tipo de servicio
     */
    handleDeleteButtonClick(button, serviceTypeData) {
        const onSuccess = () => {
            if (window.serviceTypeController && typeof window.serviceTypeController.refresh === 'function') {
                window.serviceTypeController.refresh();
            }
            if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
                window.GlobalToast.show(`Tipo de servicio "${serviceTypeData.name}" eliminado exitosamente`, 'success');
            }
        };
        const onError = (error) => {
            if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
                window.GlobalToast.show(`Error al eliminar tipo de servicio: ${error.message}`, 'error');
            }
        };
        this.requestDelete(serviceTypeData, onSuccess, onError);
    }

    /**
     * Solicita confirmación y elimina un tipo de servicio
     * @param {Object} serviceTypeData - Datos del tipo de servicio a eliminar
     * @param {Function} onSuccess - Callback en caso de éxito
     * @param {Function} onError - Callback en caso de error
     */
    async requestDelete(serviceTypeData, onSuccess = null, onError = null) {
        try {
            if (!serviceTypeData.id || typeof serviceTypeData.id !== 'number' || serviceTypeData.id <= 0) {
                const errorMessage = 'ID del tipo de servicio es requerido y debe ser un número válido';
                if (onError) onError(new Error(errorMessage));
                return;
            }
            const confirmConfig = {
                title: 'Eliminar Tipo de Servicio',
                name: serviceTypeData.name || 'Tipo de Servicio',
                subtitle: `¿Está seguro de que desea eliminar el tipo de servicio "${serviceTypeData.name}"? Esta acción no se puede deshacer.`,
                onConfirm: async () => {
                    await this.executeDelete(serviceTypeData, onSuccess, onError);
                }
            };
            this.confirmationModal.showConfirmation(confirmConfig);
        } catch (error) {
            if (onError) onError(error);
        }
    }

    /**
     * Ejecuta la eliminación del tipo de servicio
     * @param {Object} serviceTypeData - Datos del tipo de servicio
     * @param {Function} onSuccess - Callback de éxito
     * @param {Function} onError - Callback de error
     */
    async executeDelete(serviceTypeData, onSuccess = null, onError = null) {
        try {
            const response = await this.deleteService.deleteServiceType(serviceTypeData.id);
            if (onSuccess) onSuccess(response);
        } catch (error) {
            if (onError) onError(error);
        }
    }
}

// Hacer disponible globalmente
window.DeleteServiceTypeController = DeleteServiceTypeController;
