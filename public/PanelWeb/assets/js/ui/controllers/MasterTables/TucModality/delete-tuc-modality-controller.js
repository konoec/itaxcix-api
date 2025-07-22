// Archivo: DeleteTucModalityController.js
// Ubicación: assets/js/ui/controllers/MasterTables/TucModality/delete-tuc-modality-controller.js

/**
 * Controlador para eliminar modalidades TUC usando modal de confirmación global
 */
class DeleteTucModalityController {
    /**
     * Método público para manejar la eliminación desde el controlador global
     * @param {Object} data - Debe contener id y name
     */
    handleDelete(data) {
        if (!data || !data.id) {
            console.error('No se proporcionó ID para eliminar modalidad TUC');
            return;
        }
        // Simular el botón, solo para compatibilidad
        this.handleDeleteButtonClick(null, {
            id: Number(data.id),
            name: data.name || ''
        });
    }
    constructor() {
        this.deleteService = window.DeleteTucModalityService;
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
     * @param {Object} tucModalityData - Datos de la modalidad TUC
     */
    handleDeleteButtonClick(button, tucModalityData) {
        const onSuccess = () => {
            // Recargar la tabla igual que en edición
            if (window.tucModalityController && typeof window.tucModalityController.refreshData === 'function') {
                window.tucModalityController.refreshData();
            }
            // Mensaje dinámico igual que UserCodeType
            const msg = `Modalidad TUC "${tucModalityData.name}" eliminada exitosamente`;
            if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
                window.GlobalToast.show(msg, 'success');
            } else if (typeof window.showRecoveryToast === 'function') {
                window.showRecoveryToast(msg, 'success');
            } else {
                alert(msg);
            }
        };
        const onError = (error) => {
            if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
                window.GlobalToast.show(`Error al eliminar modalidad TUC: ${error.message}`, 'error');
            }
        };
        this.requestDelete(tucModalityData, onSuccess, onError);
    }

    /**
     * Solicita confirmación y elimina una modalidad TUC
     * @param {Object} tucModalityData - Datos de la modalidad TUC a eliminar
     * @param {Function} onSuccess - Callback en caso de éxito
     * @param {Function} onError - Callback en caso de error
     */
    async requestDelete(tucModalityData, onSuccess = null, onError = null) {
        try {
            if (!tucModalityData.id || typeof tucModalityData.id !== 'number' || tucModalityData.id <= 0) {
                const errorMessage = 'ID de la modalidad TUC es requerido y debe ser un número válido';
                if (onError) onError(new Error(errorMessage));
                return;
            }
            const confirmConfig = {
                title: 'Eliminar Modalidad TUC',
                name: tucModalityData.name || 'Modalidad TUC',
                subtitle: `¿Está seguro de que desea eliminar la modalidad TUC "${tucModalityData.name}"? Esta acción no se puede deshacer.`,
                onConfirm: async () => {
                    await this.executeDelete(tucModalityData, onSuccess, onError);
                }
            };
            this.confirmationModal.showConfirmation(confirmConfig);
        } catch (error) {
            if (onError) onError(error);
        }
    }

    /**
     * Ejecuta la eliminación de la modalidad TUC
     * @param {Object} tucModalityData - Datos de la modalidad TUC
     * @param {Function} onSuccess - Callback de éxito
     * @param {Function} onError - Callback de error
     */
    async executeDelete(tucModalityData, onSuccess = null, onError = null) {
        try {
            const response = await this.deleteService.deleteTucModality(tucModalityData.id);
            if (onSuccess) onSuccess(response);
        } catch (error) {
            if (onError) onError(error);
        }
    }
}

// Hacer disponible globalmente
window.DeleteTucModalityController = DeleteTucModalityController;
