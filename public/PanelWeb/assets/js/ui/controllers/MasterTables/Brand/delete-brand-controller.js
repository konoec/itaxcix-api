// Archivo: DeleteBrandController.js
// Ubicación: assets/js/ui/controllers/MasterTables/Brand/delete-brand-controller.js

/**
 * Controlador para eliminar marcas de vehículo usando modal de confirmación reutilizable
 */
class DeleteBrandController {
    constructor() {
        this.deleteService = window.DeleteBrandService;
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
        // Buscar el controlador de modal de confirmación global de diferentes formas
        if (window.globalConfirmationModal) {
            this.confirmationModal = window.globalConfirmationModal;
            console.log('✅ Modal de confirmación global encontrado (globalConfirmationModal)');
        } else if (window.GlobalConfirmationModalController) {
            this.confirmationModal = window.GlobalConfirmationModalController;
            console.log('✅ Modal de confirmación global encontrado (GlobalConfirmationModalController)');
        } else if (window.globalConfirmationModalController) {
            this.confirmationModal = window.globalConfirmationModalController;
            console.log('✅ Modal de confirmación global encontrado (instancia global)');
        } else if (typeof GlobalConfirmationModalController !== 'undefined') {
            console.log('🔧 Creando instancia de GlobalConfirmationModalController...');
            window.globalConfirmationModal = new GlobalConfirmationModalController();
            this.confirmationModal = window.globalConfirmationModal;
            console.log('✅ Modal de confirmación global creado exitosamente');
        } else {
            throw new Error('No se encontró el modal global de confirmación (globalConfirmationModal, GlobalConfirmationModalController, globalConfirmationModalController)');
        }
    }

    /**
     * Maneja el click del botón eliminar desde la tabla
     * @param {HTMLElement} button - Botón que disparó la acción
     * @param {Object} brandData - Datos de la marca
     */
    handleDeleteButtonClick(button, brandData) {
        const onSuccess = () => {
            if (window.brandListController && typeof window.brandListController.load === 'function') {
                window.brandListController.load();
            }
            if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
                window.GlobalToast.show(`Marca "${brandData.name}" eliminada exitosamente`, 'success');
            }
        };
        const onError = (error) => {
            if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
                window.GlobalToast.show(`Error al eliminar marca: ${error.message}`, 'error');
            }
        };
        this.requestDelete(brandData, onSuccess, onError);
    }

    /**
     * Solicita confirmación y elimina una marca
     * @param {Object} brandData - Datos de la marca a eliminar
     * @param {Function} onSuccess - Callback en caso de éxito
     * @param {Function} onError - Callback en caso de error
     */
    async requestDelete(brandData, onSuccess = null, onError = null) {
        try {
            console.log('🗑️ Solicitando eliminación de marca:', brandData);
            // Validación básica
            if (!brandData.id || typeof brandData.id !== 'number' || brandData.id <= 0) {
                const errorMessage = 'ID de la marca es requerido y debe ser un número válido';
                console.error('❌ Validación fallida:', errorMessage);
                if (onError) onError(new Error(errorMessage));
                return;
            }
            // Configuración del modal de confirmación
            const confirmConfig = {
                title: 'Eliminar Marca de Vehículo',
                name: brandData.name || 'Marca de Vehículo',
                subtitle: `¿Está seguro de que desea eliminar la marca "${brandData.name}"? Esta acción no se puede deshacer.`,
                onConfirm: async () => {
                    await this.executeDelete(brandData, onSuccess, onError);
                }
            };
            this.confirmationModal.showConfirmation(confirmConfig);
        } catch (error) {
            console.error('❌ Error al solicitar eliminación:', error);
            if (onError) onError(error);
        }
    }

    /**
     * Ejecuta la eliminación de la marca
     * @param {Object} brandData - Datos de la marca
     * @param {Function} onSuccess - Callback de éxito
     * @param {Function} onError - Callback de error
     */
    async executeDelete(brandData, onSuccess = null, onError = null) {
        try {
            const response = await this.deleteService.deleteBrand(brandData.id);
            if (onSuccess) onSuccess(response);
        } catch (error) {
            if (onError) onError(error);
        }
    }
}

// Hacer disponible globalmente
window.DeleteBrandController = DeleteBrandController;
