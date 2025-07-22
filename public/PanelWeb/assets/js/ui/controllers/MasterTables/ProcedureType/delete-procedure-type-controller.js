// Archivo: DeleteProcedureTypeController.js
// Ubicación: assets/js/ui/controllers/MasterTables/ProcedureType/delete-procedure-type-controller.js

/**
 * Controlador para eliminar tipos de trámite usando modal de confirmación global
 */
class DeleteProcedureTypeController {
    constructor() {
        this.deleteService = window.DeleteProcedureTypeService;
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
     * @param {Object} procedureTypeData - Datos del tipo de trámite
     */
    handleDeleteButtonClick(button, procedureTypeData) {
        const onSuccess = () => {
            // Recarga robusta igual que el controlador de edición
            if (window.procedureTypeListControllerInstance && typeof window.procedureTypeListControllerInstance.load === 'function') {
                window.procedureTypeListControllerInstance.load();
            } else if (window.procedureTypeListController && typeof window.procedureTypeListController.load === 'function') {
                window.procedureTypeListController.load();
            }
            if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
                window.GlobalToast.show(`Tipo de trámite "${procedureTypeData.name}" eliminado exitosamente`, 'success');
            }
        };
        const onError = (error) => {
            if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
                window.GlobalToast.show(`Error al eliminar tipo de trámite: ${error.message}`, 'error');
            }
        };
        this.requestDelete(procedureTypeData, onSuccess, onError);
    }

    /**
     * Solicita confirmación y elimina un tipo de trámite
     * @param {Object} procedureTypeData - Datos del tipo de trámite a eliminar
     * @param {Function} onSuccess - Callback en caso de éxito
     * @param {Function} onError - Callback en caso de error
     */
    async requestDelete(procedureTypeData, onSuccess = null, onError = null) {
        try {
            if (!procedureTypeData.id || typeof procedureTypeData.id !== 'number' || procedureTypeData.id <= 0) {
                const errorMessage = 'ID del tipo de trámite es requerido y debe ser un número válido';
                if (onError) onError(new Error(errorMessage));
                return;
            }
            const confirmConfig = {
                title: 'Eliminar Tipo de Trámite',
                name: procedureTypeData.name || 'Tipo de Trámite',
                subtitle: `¿Está seguro de que desea eliminar el tipo de trámite "${procedureTypeData.name}"? Esta acción no se puede deshacer.`,
                onConfirm: async () => {
                    await this.executeDelete(procedureTypeData, onSuccess, onError);
                }
            };
            this.confirmationModal.showConfirmation(confirmConfig);
        } catch (error) {
            if (onError) onError(error);
        }
    }

    /**
     * Ejecuta la eliminación del tipo de trámite
     * @param {Object} procedureTypeData - Datos del tipo de trámite
     * @param {Function} onSuccess - Callback de éxito
     * @param {Function} onError - Callback de error
     */
    async executeDelete(procedureTypeData, onSuccess = null, onError = null) {
        try {
            const response = await this.deleteService.deleteProcedureType(procedureTypeData.id);
            if (onSuccess) onSuccess(response);
        } catch (error) {
            if (onError) onError(error);
        }
    }
}

// Hacer disponible globalmente
window.DeleteProcedureTypeController = DeleteProcedureTypeController;
