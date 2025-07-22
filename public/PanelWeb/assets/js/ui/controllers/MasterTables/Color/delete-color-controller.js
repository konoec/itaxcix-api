// Archivo: DeleteColorController.js
// Ubicación: assets/js/ui/controllers/MasterTables/Color/delete-color-controller.js

/**
 * Controlador para eliminar colores usando modal de confirmación global
 */
class DeleteColorController {
    constructor() {
        this.deleteService = window.DeleteColorService;
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
     * @param {Object} colorData - Datos del color
     */
    handleDeleteButtonClick(button, colorData) {
        const onSuccess = () => {
            if (window.colorListController && typeof window.colorListController.load === 'function') {
                window.colorListController.load();
            }
            if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
                window.GlobalToast.show(`Color "${colorData.name}" eliminado exitosamente`, 'success');
            }
        };
        const onError = (error) => {
            if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
                window.GlobalToast.show(`Error al eliminar color: ${error.message}`, 'error');
            }
        };
        this.requestDelete(colorData, onSuccess, onError);
    }

    /**
     * Solicita confirmación y elimina un color
     * @param {Object} colorData - Datos del color a eliminar
     * @param {Function} onSuccess - Callback en caso de éxito
     * @param {Function} onError - Callback en caso de error
     */
    async requestDelete(colorData, onSuccess = null, onError = null) {
        try {
            // Validación básica
            if (!colorData.id || typeof colorData.id !== 'number' || colorData.id <= 0) {
                const errorMessage = 'ID del color es requerido y debe ser un número válido';
                if (onError) onError(new Error(errorMessage));
                return;
            }
            // Configuración del modal de confirmación
            const confirmConfig = {
                title: 'Eliminar Color',
                name: colorData.name || 'Color',
                subtitle: `¿Está seguro de que desea eliminar el color "${colorData.name}"? Esta acción no se puede deshacer.`,
                onConfirm: async () => {
                    await this.executeDelete(colorData, onSuccess, onError);
                }
            };
            this.confirmationModal.showConfirmation(confirmConfig);
        } catch (error) {
            if (onError) onError(error);
        }
    }

    /**
     * Ejecuta la eliminación del color
     * @param {Object} colorData - Datos del color
     * @param {Function} onSuccess - Callback de éxito
     * @param {Function} onError - Callback de error
     */
    async executeDelete(colorData, onSuccess = null, onError = null) {
        try {
            // Forzar parseo de id
            const colorId = parseInt(colorData.id, 10);
            if (!colorId || isNaN(colorId) || colorId <= 0) {
                console.warn('ID de color inválido en executeDelete:', colorData.id);
                if (onError) onError(new Error('ID del color es requerido y debe ser un número válido'));
                return;
            }
            const response = await this.deleteService.deleteColor(colorId);
            if (onSuccess) onSuccess(response);
        } catch (error) {
            if (onError) onError(error);
        }
    }
}

// Hacer disponible globalmente
window.DeleteColorController = DeleteColorController;
