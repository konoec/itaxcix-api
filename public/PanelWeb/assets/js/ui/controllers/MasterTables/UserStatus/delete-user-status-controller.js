/**
 * Delete User Status Controller
 * Controlador para eliminar estados de usuario usando modal de confirmación reutilizable
 */
class DeleteUserStatusController {
    constructor() {
        this.deleteService = window.DeleteUserStatusService;
        this.confirmationModal = null;

        console.log('🗑️ Inicializando DeleteUserStatusController...');
        this.init();
    }

    /**
     * Inicializa el controlador
     */
    init() {
        this.ensureConfirmationModal();
        console.log('✅ DeleteUserStatusController inicializado');
    }

    /**
     * Verifica que el modal de confirmación global esté disponible
     */
    ensureConfirmationModal() {
        if (window.globalConfirmationModal) {
            this.confirmationModal = window.globalConfirmationModal;
            console.log('✅ Modal de confirmación global encontrado (globalConfirmationModal)');
        } else if (window.GlobalConfirmationModalController) {
            this.confirmationModal = window.GlobalConfirmationModalController;
            console.log('✅ Modal de confirmación global encontrado (GlobalConfirmationModalController)');
        } else if (window.globalConfirmationModalController) {
            this.confirmationModal = window.globalConfirmationModalController;
            console.log('✅ Modal de confirmación global encontrado (instancia global)');
        } else {
            if (typeof GlobalConfirmationModalController !== 'undefined') {
                console.log('🔧 Creando instancia de GlobalConfirmationModalController...');
                window.globalConfirmationModal = new GlobalConfirmationModalController();
                this.confirmationModal = window.globalConfirmationModal;
                console.log('✅ Modal de confirmación global creado exitosamente');
            } else {
                console.warn('⚠️ Modal de confirmación global no encontrado, creando uno básico');
                this.createBasicConfirmationModal();
            }
        }
    }

    /**
     * Crea un modal de confirmación básico si no existe el global
     */
    createBasicConfirmationModal() {
        const modalHTML = `
            <div class="modal modal-blur fade" id="basic-delete-confirmation-modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Confirmar Eliminación
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="text-center">
                                <div class="mb-3">
                                    <i class="fas fa-trash-alt text-danger" style="font-size: 3rem;"></i>
                                </div>
                                <h4 id="basic-confirmation-title">¿Eliminar estado de usuario?</h4>
                                <p id="basic-confirmation-message" class="text-muted">Esta acción no se puede deshacer.</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>
                                Cancelar
                            </button>
                            <button type="button" class="btn btn-danger" id="basic-confirm-delete-btn">
                                <i class="fas fa-trash-alt me-1"></i>
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        if (!document.getElementById('basic-delete-confirmation-modal')) {
            document.body.insertAdjacentHTML('beforeend', modalHTML);
        }

        this.confirmationModal = {
            showConfirmation: (config) => {
                const modal = document.getElementById('basic-delete-confirmation-modal');
                const titleEl = document.getElementById('basic-confirmation-title');
                const messageEl = document.getElementById('basic-confirmation-message');
                const confirmBtn = document.getElementById('basic-confirm-delete-btn');

                if (titleEl) titleEl.textContent = config.title || 'Confirmar eliminación';
                if (messageEl) messageEl.textContent = `¿Eliminar "${config.name}"?` || '¿Está seguro?';

                const handleConfirm = async () => {
                    confirmBtn.removeEventListener('click', handleConfirm);
                    if (config.onConfirm) await config.onConfirm();
                    bootstrap.Modal.getInstance(modal).hide();
                };

                confirmBtn.addEventListener('click', handleConfirm);

                const bsModal = new bootstrap.Modal(modal);
                bsModal.show();
            }
        };
    }

    /**
     * Maneja el click del botón eliminar desde la tabla
     * @param {HTMLElement} button - Botón que disparó la acción
     * @param {Object} userStatusData - Datos del estado de usuario
     */
    handleDeleteButtonClick(button, userStatusData) {
        console.log('🎯 Manejando click de eliminación desde botón:', userStatusData);

        const onSuccess = (deletedData) => {
            console.log('✅ Estado de usuario eliminado, recargando lista...');
            this.reloadUserStatusList();
        };

        const onError = (error) => {
            console.error('❌ Error en eliminación desde botón:', error);
        };

        this.requestDelete(userStatusData, onSuccess, onError);
    }

    /**
     * Solicita confirmación y elimina un estado de usuario
     * @param {Object} userStatusData - Datos del estado de usuario a eliminar
     * @param {Function} onSuccess - Callback ejecutado en caso de éxito
     * @param {Function} onError - Callback ejecutado en caso de error
     */
    async requestDelete(userStatusData, onSuccess = null, onError = null) {
        try {
            console.log('🗑️ Solicitando eliminación de estado de usuario:', userStatusData);

            const validation = this.deleteService.validateDeletion(userStatusData);
            if (!validation.isValid) {
                const errorMessage = validation.errors.join(', ');
                console.error('❌ Validación fallida:', errorMessage);
                this.showErrorToast(errorMessage);
                if (onError) onError(new Error(errorMessage));
                return;
            }

            const confirmConfig = this.deleteService.getConfirmationConfig(userStatusData);

            // SIEMPRE PASAMOS EL CALLBACK DENTRO DEL OBJETO
            this.confirmationModal.showConfirmation({
                title: confirmConfig.title || 'Confirmar Eliminación',
                name: userStatusData.name || 'Estado de Usuario',
                // No pasar 'subtitle' para que no se muestre el texto adicional
                onConfirm: async () => {
                    await this.executeDelete(userStatusData, onSuccess, onError);
                }
            });

        } catch (error) {
            console.error('❌ Error al solicitar eliminación:', error);
            this.showErrorToast('Error al procesar solicitud de eliminación');
            if (onError) onError(error);
        }
    }

    /**
     * Ejecuta la eliminación del estado de usuario
     * @param {Object} userStatusData - Datos del estado de usuario
     * @param {Function} onSuccess - Callback de éxito
     * @param {Function} onError - Callback de error
     */
    async executeDelete(userStatusData, onSuccess = null, onError = null) {
        try {
            console.log('⚡ Ejecutando eliminación del estado de usuario:', userStatusData);

            this.setGlobalLoading(true);

            const response = await this.deleteService.deleteUserStatus(userStatusData.id);

            if (response.success) {
                this.showSuccessToast(`Estado de usuario "${userStatusData.name}" eliminado exitosamente`);
                if (onSuccess && typeof onSuccess === 'function') onSuccess(response.data);
            } else {
                this.showErrorToast(response.message || 'Error al eliminar el estado de usuario');
                if (onError && typeof onError === 'function') onError(new Error(response.message));
            }

        } catch (error) {
            console.error('❌ Error al ejecutar eliminación:', error);
            this.showErrorToast(error.message || 'Error al eliminar estado de usuario');
            if (onError && typeof onError === 'function') onError(error);
        } finally {
            this.setGlobalLoading(false);
        }
    }

    /**
     * Controla el estado de carga global
     * @param {boolean} loading - Estado de carga
     */
    setGlobalLoading(loading) {
        if (window.globalLoadingController) {
            if (loading) window.globalLoadingController.show();
            else window.globalLoadingController.hide();
        }
        console.log(`🔄 Estado de carga global: ${loading ? 'MOSTRADO' : 'OCULTO'}`);
    }

    /**
     * Recarga la lista de estados de usuario
     */
    reloadUserStatusList() {
        if (window.userStatusListController && typeof window.userStatusListController.load === 'function') {
            console.log('🔄 Recargando lista de estados de usuario...');
            window.userStatusListController.load();
        } else {
            console.warn('⚠️ No se pudo recargar la lista: controlador no encontrado');
        }
    }

    /**
     * Muestra un toast de éxito
     * @param {string} message - Mensaje a mostrar
     */
    showSuccessToast(message) {
        if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
            window.GlobalToast.show(message, 'success');
        } else if (typeof window.showRecoveryToast === 'function') {
            window.showRecoveryToast(message, 'success');
        } else {
            console.log('✅', message);
            alert(message);
        }
    }

    /**
     * Muestra un toast de error
     * @param {string} message - Mensaje a mostrar
     */
    showErrorToast(message) {
        if (window.GlobalToast && typeof window.GlobalToast.show === 'function') {
            window.GlobalToast.show(message, 'error');
        } else if (typeof window.showRecoveryToast === 'function') {
            window.showRecoveryToast(message, 'error');
        } else {
            console.error('❌', message);
            alert('Error: ' + message);
        }
    }
}

// Hacer disponible globalmente
window.DeleteUserStatusController = DeleteUserStatusController;
console.log('✅ DeleteUserStatusController cargado correctamente');
