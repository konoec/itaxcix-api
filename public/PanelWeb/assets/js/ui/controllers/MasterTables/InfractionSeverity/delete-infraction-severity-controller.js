// Archivo: DeleteInfractionSeverityController.js
// Ubicación: assets/js/ui/controllers/MasterTables/InfractionSeverity/delete-infraction-severity-controller.js

/**
 * Controlador para eliminar severidades de infracción usando modal de confirmación global
 */
class DeleteInfractionSeverityController {
    constructor() {
        this.deleteService = window.DeleteInfractionSeverityService;
        this.confirmationModal = null;
        this.init();
    }

    /**
     * Inicializa el controlador y asegura el modal de confirmación
     */
    init() {
        // Usar SIEMPRE el modal global, nunca alternativo
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
     * @param {Object} infractionSeverityData - Datos de la severidad
     */
    handleDeleteButtonClick(button, infractionSeverityData) {
        const onSuccess = () => {
            if (window.infractionSeverityListController && typeof window.infractionSeverityListController.load === 'function') {
                window.infractionSeverityListController.load();
            }
            if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
                window.GlobalToast.show(`Gravedad de infracción "${infractionSeverityData.name}" eliminada exitosamente`, 'success');
            }
        };
        const onError = (error) => {
            if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
                window.GlobalToast.show(`Error al eliminar gravedad de infracción: ${error.message}`, 'error');
            }
        };
        this.requestDelete(infractionSeverityData, onSuccess, onError);
    }

    /**
     * Solicita confirmación y elimina una severidad
     * @param {Object} infractionSeverityData - Datos de la severidad a eliminar
     * @param {Function} onSuccess - Callback en caso de éxito
     * @param {Function} onError - Callback en caso de error
     */
    async requestDelete(infractionSeverityData, onSuccess = null, onError = null) {
        try {
            console.log('🗑️ Solicitando eliminación de gravedad de infracción:', infractionSeverityData);
            // Validación básica
            if (!infractionSeverityData.id || typeof infractionSeverityData.id !== 'number' || infractionSeverityData.id <= 0) {
                const errorMessage = 'ID de la gravedad de infracción es requerido y debe ser un número válido';
                console.error('❌ Validación fallida:', errorMessage);
                if (onError) onError(new Error(errorMessage));
                return;
            }
            // Configuración del modal de confirmación
            const confirmConfig = {
                title: 'Eliminar Gravedad de Infracción',
                name: infractionSeverityData.name || 'Gravedad de Infracción',
                subtitle: `¿Está seguro de que desea eliminar la gravedad "${infractionSeverityData.name}"? Esta acción no se puede deshacer.`,
                onConfirm: async () => {
                    await this.executeDelete(infractionSeverityData, onSuccess, onError);
                }
            };
            this.confirmationModal.showConfirmation(confirmConfig);
        } catch (error) {
            console.error('❌ Error al solicitar eliminación:', error);
            if (onError) onError(error);
        }
    }

    /**
     * Ejecuta la eliminación de la severidad
     * @param {Object} infractionSeverityData - Datos de la severidad
     * @param {Function} onSuccess - Callback de éxito
     * @param {Function} onError - Callback de error
     */
    async executeDelete(infractionSeverityData, onSuccess = null, onError = null) {
        try {
            const response = await this.deleteService.deleteInfractionSeverity(infractionSeverityData.id);
            if (onSuccess) onSuccess(response);
        } catch (error) {
            if (onError) onError(error);
        }
    }
}

// Hacer disponible globalmente
window.DeleteInfractionSeverityController = DeleteInfractionSeverityController;
