/**
 * Delete User Status Controller
 * Controlador para eliminar estados de usuario usando modal de confirmación reutilizable
 */

class DeleteUserStatusController {
    constructor() {
        this.deleteService = new DeleteUserStatusService();
        this.confirmationModal = null;
        
        console.log('🗑️ Inicializando DeleteUserStatusController...');
        this.init();
    }

    /**
     * Inicializa el controlador
     */
    init() {
        // Verificar que el modal de confirmación esté disponible
        this.ensureConfirmationModal();
        
        console.log('✅ DeleteUserStatusController inicializado');
    }

    /**
     * Verifica que el modal de confirmación global esté disponible
     */
    ensureConfirmationModal() {
        // Buscar el controlador de modal de confirmación global de diferentes formas
        if (window.GlobalConfirmationModalController) {
            this.confirmationModal = window.GlobalConfirmationModalController;
            console.log('✅ Modal de confirmación global encontrado (GlobalConfirmationModalController)');
        } else if (window.globalConfirmationModalController) {
            this.confirmationModal = window.globalConfirmationModalController;
            console.log('✅ Modal de confirmación global encontrado (instancia global)');
        } else {
            // Intentar crear una instancia si la clase está disponible
            if (typeof GlobalConfirmationModalController !== 'undefined') {
                console.log('🔧 Creando instancia de GlobalConfirmationModalController...');
                window.globalConfirmationModalController = new GlobalConfirmationModalController();
                this.confirmationModal = window.globalConfirmationModalController;
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
        // Modal básico de confirmación si no existe el global
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

        // Agregar modal al DOM si no existe
        if (!document.getElementById('basic-delete-confirmation-modal')) {
            document.body.insertAdjacentHTML('beforeend', modalHTML);
        }

        // Crear objeto de control básico
        this.confirmationModal = {
            showConfirmation: (config, onConfirm) => {
                const modal = document.getElementById('basic-delete-confirmation-modal');
                const titleEl = document.getElementById('basic-confirmation-title');
                const messageEl = document.getElementById('basic-confirmation-message');
                const confirmBtn = document.getElementById('basic-confirm-delete-btn');

                // Configurar contenido
                if (titleEl) titleEl.textContent = config.title || 'Confirmar eliminación';
                if (messageEl) messageEl.textContent = `¿Eliminar "${config.name}"?` || '¿Está seguro?';

                // Configurar evento de confirmación
                const handleConfirm = async () => {
                    confirmBtn.removeEventListener('click', handleConfirm);
                    
                    // Ejecutar callback
                    if (config.onConfirm) {
                        await config.onConfirm();
                    }
                    
                    // Cerrar modal
                    bootstrap.Modal.getInstance(modal).hide();
                };

                confirmBtn.addEventListener('click', handleConfirm);

                // Mostrar modal
                const bsModal = new bootstrap.Modal(modal);
                bsModal.show();
            }
        };
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

            // Validar datos
            const validation = this.deleteService.validateDeletion(userStatusData);
            if (!validation.isValid) {
                const errorMessage = validation.errors.join(', ');
                console.error('❌ Validación fallida:', errorMessage);
                this.showErrorToast(errorMessage);
                if (onError) onError(new Error(errorMessage));
                return;
            }

            // Obtener configuración del modal de confirmación
            const confirmConfig = this.deleteService.getConfirmationConfig(userStatusData);

            // Mostrar modal de confirmación usando el método correcto
            this.confirmationModal.showConfirmation({
                title: confirmConfig.title || 'Confirmar Eliminación',
                name: userStatusData.name || 'Estado de Usuario',
                subtitle: confirmConfig.details || 'Esta acción no se puede deshacer y podría afectar a los usuarios que tengan asignado este estado.',
                confirmText: confirmConfig.confirmText || 'Sí, Eliminar',
                loadingText: 'Eliminando estado...',
                onConfirm: async () => {
                    await this.executeDelete(userStatusData, onSuccess, onError);
                },
                data: userStatusData
            });

        } catch (error) {
            console.error('❌ Error en requestDelete:', error);
            this.showErrorToast('Error al procesar la solicitud de eliminación');
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
            console.log('⚡ Ejecutando eliminación de estado de usuario...');

            // Mostrar indicador de carga global si está disponible
            this.setGlobalLoading(true);

            // Llamar al servicio de eliminación
            const result = await this.deleteService.deleteUserStatus(userStatusData.id);

            if (result.success) {
                console.log('✅ Estado de usuario eliminado exitosamente');
                
                // Mostrar mensaje de éxito
                this.showSuccessToast(result.message || 'Estado de usuario eliminado correctamente');
                
                // Ejecutar callback de éxito
                if (onSuccess) {
                    onSuccess(result);
                } else {
                    // Comportamiento por defecto: recargar lista
                    this.reloadUserStatusList();
                }

            } else {
                // Error en la eliminación
                console.error('❌ Error al eliminar:', result.message);
                this.showErrorToast(result.message || 'Error al eliminar el estado de usuario');
                
                if (onError) onError(new Error(result.message));
            }

        } catch (error) {
            console.error('❌ Error en executeDelete:', error);
            this.showErrorToast('Error interno al eliminar el estado de usuario');
            
            if (onError) onError(error);

        } finally {
            // Ocultar indicador de carga
            this.setGlobalLoading(false);
        }
    }

    /**
     * Controla el estado de carga global
     * @param {boolean} loading - Estado de carga
     */
    setGlobalLoading(loading) {
        // Intentar usar el sistema de loading global si está disponible
        if (window.LoadingScreenUtil) {
            if (loading) {
                window.LoadingScreenUtil.show('Eliminando estado de usuario...');
            } else {
                window.LoadingScreenUtil.hide();
            }
        }
    }

    /**
     * Recarga la lista de estados de usuario
     */
    reloadUserStatusList() {
        // Intentar recargar usando el controlador de lista si está disponible
        if (window.userStatusListController && typeof window.userStatusListController.loadUserStatuses === 'function') {
            console.log('🔄 Recargando lista usando controlador...');
            window.userStatusListController.loadUserStatuses();
        } else if (window.UserStatusListController && typeof window.UserStatusListController.load === 'function') {
            console.log('🔄 Recargando lista usando UserStatusListController...');
            window.UserStatusListController.load();
        } else {
            // Si no hay controlador disponible, recargar la página
            console.log('🔄 Recargando página para mostrar cambios...');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
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
            // Fallback: mostrar alert si no hay sistema de toast
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
            // Fallback: mostrar alert si no hay sistema de toast
            alert(`Error: ${message}`);
        }
    }

    /**
     * Método de conveniencia para eliminar desde un botón
     * @param {HTMLElement} button - Botón que disparó la acción
     * @param {Object} userStatusData - Datos del estado de usuario
     */
    handleDeleteButtonClick(button, userStatusData) {
        // Deshabilitar botón temporalmente
        const originalHTML = button.innerHTML;
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        // Restaurar botón después de la operación
        const restoreButton = () => {
            button.disabled = false;
            button.innerHTML = originalHTML;
        };

        // Ejecutar eliminación
        this.requestDelete(
            userStatusData,
            restoreButton, // onSuccess
            restoreButton  // onError
        );
    }
}

// Hacer disponible globalmente
window.DeleteUserStatusController = DeleteUserStatusController;

console.log('✅ DeleteUserStatusController cargado correctamente');
