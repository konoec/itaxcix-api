// Archivo: DeleteCategoryController.js
// Ubicación: assets/js/ui/controllers/MasterTables/Category/delete-category-controller.js

/**
 * Controlador para eliminar categorías de vehículo usando modal de confirmación global
 */
class DeleteCategoryController {
    constructor() {
        this.deleteService = window.DeleteCategoryService;
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
     * @param {Object} categoryData - Datos de la categoría
     */
    handleDeleteButtonClick(button, categoryData) {
        const onSuccess = () => {
            if (window.categoryListController && typeof window.categoryListController.load === 'function') {
                window.categoryListController.load();
            }
            if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
                window.GlobalToast.show(`Categoría "${categoryData.name}" eliminada exitosamente`, 'success');
            }
        };
        const onError = (error) => {
            if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
                window.GlobalToast.show(`Error al eliminar categoría: ${error.message}`, 'error');
            }
        };
        this.requestDelete(categoryData, onSuccess, onError);
    }

    /**
     * Solicita confirmación y elimina una categoría
     * @param {Object} categoryData - Datos de la categoría a eliminar
     * @param {Function} onSuccess - Callback en caso de éxito
     * @param {Function} onError - Callback en caso de error
     */
    async requestDelete(categoryData, onSuccess = null, onError = null) {
        try {
            if (!categoryData.id || typeof categoryData.id !== 'number' || categoryData.id <= 0) {
                const errorMessage = 'ID de la categoría es requerido y debe ser un número válido';
                if (onError) onError(new Error(errorMessage));
                return;
            }
            const confirmConfig = {
                title: 'Eliminar Categoría de Vehículo',
                name: categoryData.name || 'Categoría de Vehículo',
                subtitle: `¿Está seguro de que desea eliminar la categoría "${categoryData.name}"? Esta acción no se puede deshacer.`,
                onConfirm: async () => {
                    await this.executeDelete(categoryData, onSuccess, onError);
                }
            };
            this.confirmationModal.showConfirmation(confirmConfig);
        } catch (error) {
            if (onError) onError(error);
        }
    }

    /**
     * Ejecuta la eliminación de la categoría
     * @param {Object} categoryData - Datos de la categoría
     * @param {Function} onSuccess - Callback de éxito
     * @param {Function} onError - Callback de error
     */
    async executeDelete(categoryData, onSuccess = null, onError = null) {
        try {
            const response = await this.deleteService.deleteCategory(categoryData.id);
            if (onSuccess) onSuccess(response);
        } catch (error) {
            if (onError) onError(error);
        }
    }
}

// Hacer disponible globalmente
window.DeleteCategoryController = DeleteCategoryController;
