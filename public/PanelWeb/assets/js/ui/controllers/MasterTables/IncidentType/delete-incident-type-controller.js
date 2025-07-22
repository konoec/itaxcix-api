// Archivo: DeleteIncidentTypeController.js
// Ubicación: assets/js/ui/controllers/MasterTables/IncidentType/delete-incident-type-controller.js

/**
 * Controlador para eliminar tipos de incidencia usando modal de confirmación global
 */
class DeleteIncidentTypeController {
    constructor() {
        this.deleteService = window.DeleteIncidentTypeService;
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
     * @param {Object} incidentTypeData - Datos del tipo de incidencia
     */
    handleDeleteButtonClick(button, incidentTypeData) {
        const onSuccess = () => {
            if (window.incidentTypeListControllerInstance && typeof window.incidentTypeListControllerInstance.load === 'function') {
                window.incidentTypeListControllerInstance.load();
            }
            if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
                window.GlobalToast.show(`Tipo de incidencia eliminado exitosamente`, 'success');
            }
        };
        const onError = (error) => {
            if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
                window.GlobalToast.show(`Error al eliminar tipo de incidencia: ${error.message}`, 'error');
            }
        };
        this.requestDelete(incidentTypeData, onSuccess, onError);
    }

    /**
     * Solicita confirmación y elimina un tipo de incidencia
     * @param {Object} incidentTypeData - Datos del tipo de incidencia a eliminar
     * @param {Function} onSuccess - Callback en caso de éxito
     * @param {Function} onError - Callback en caso de error
     */
    async requestDelete(incidentTypeData, onSuccess = null, onError = null) {
        try {
            if (!incidentTypeData.id || typeof incidentTypeData.id !== 'number' || incidentTypeData.id <= 0) {
                const errorMessage = 'ID del tipo de incidencia es requerido y debe ser un número válido';
                if (onError) onError(new Error(errorMessage));
                return;
            }
            const confirmConfig = {
                title: 'Eliminar Tipo de Incidencia',
                name: incidentTypeData.name || 'Tipo de Incidencia',
                subtitle: `¿Está seguro de que desea eliminar el tipo de incidencia "${incidentTypeData.name}"? Esta acción no se puede deshacer.`,
                onConfirm: async () => {
                    await this.executeDelete(incidentTypeData, onSuccess, onError);
                }
            };
            this.confirmationModal.showConfirmation(confirmConfig);
        } catch (error) {
            if (onError) onError(error);
        }
    }

    /**
     * Ejecuta la eliminación del tipo de incidencia
     * @param {Object} incidentTypeData - Datos del tipo de incidencia
     * @param {Function} onSuccess - Callback de éxito
     * @param {Function} onError - Callback de error
     */
    async executeDelete(incidentTypeData, onSuccess = null, onError = null) {
        try {
            const response = await this.deleteService.deleteIncidentType(incidentTypeData.id);
            if (onSuccess) onSuccess(response);
        } catch (error) {
            if (onError) onError(error);
        }
    }
}

// Exportar la clase globalmente para instanciación modular
window.DeleteIncidentTypeController = DeleteIncidentTypeController;
