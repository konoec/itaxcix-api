/**
 * Delete User Code Type Controller
 * Controlador para eliminar tipos de código de usuario usando modal de confirmación reutilizable
 */

class DeleteUserCodeTypeController {
    constructor() {
        this.deleteService = window.DeleteUserCodeTypeService;
        this.confirmationModal = null;
        
        console.log('🗑️ Inicializando DeleteUserCodeTypeController...');
        this.init();
    }

    /**
     * Inicializa el controlador
     */
    init() {
        // Verificar que el modal de confirmación esté disponible
        this.ensureConfirmationModal();
        
        console.log('✅ DeleteUserCodeTypeController inicializado');
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
        } else {
            // Intentar crear una instancia si la clase está disponible
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
     * Maneja el click del botón eliminar desde la tabla
     * @param {HTMLElement} button - Botón que disparó la acción
     * @param {Object} userCodeTypeData - Datos del tipo de código de usuario
     */
    handleDeleteButtonClick(button, userCodeTypeData) {
        console.log('🎯 Manejando click de eliminación desde botón:', userCodeTypeData);
        
        // Callback de éxito que recarga la lista
        const onSuccess = (deletedData) => {
            console.log('✅ Tipo de código de usuario eliminado, recargando lista...');
            
            // Recargar la lista si el controlador está disponible
            if (window.UserCodeTypeListController && typeof window.UserCodeTypeListController.load === 'function') {
                window.UserCodeTypeListController.load();
            }
        };

        // Callback de error
        const onError = (error) => {
            console.error('❌ Error en eliminación desde botón:', error);
        };

        // Ejecutar eliminación
        this.requestDelete(userCodeTypeData, onSuccess, onError);
    }

    /**
     * Solicita confirmación y elimina un tipo de código de usuario
     * @param {Object} userCodeTypeData - Datos del tipo de código de usuario a eliminar
     * @param {Function} onSuccess - Callback ejecutado en caso de éxito
     * @param {Function} onError - Callback ejecutado en caso de error
     */
    async requestDelete(userCodeTypeData, onSuccess = null, onError = null) {
        try {
            console.log('🗑️ Solicitando eliminación de tipo de código de usuario:', userCodeTypeData);

            // Validar datos
            const validation = this.deleteService.validateDeletion(userCodeTypeData);
            if (!validation.isValid) {
                const errorMessage = validation.errors.join(', ');
                console.error('❌ Validación fallida:', errorMessage);
                this.showErrorToast(errorMessage);
                if (onError) onError(new Error(errorMessage));
                return;
            }

            // Obtener configuración del modal de confirmación
            const confirmConfig = this.deleteService.getConfirmationConfig(userCodeTypeData);

            // Mostrar modal de confirmación usando el método correcto
            this.confirmationModal.showConfirmation({
                title: confirmConfig.title || 'Confirmar Eliminación',
                name: userCodeTypeData.name || 'Tipo de Código de Usuario',
                subtitle: confirmConfig.details || 'Esta acción no se puede deshacer y podría afectar a los usuarios que tengan asignado este tipo de código.',
                onConfirm: async () => {
                    await this.executeDelete(userCodeTypeData, onSuccess, onError);
                }
            });

        } catch (error) {
            console.error('❌ Error al solicitar eliminación:', error);
            this.showErrorToast('Error al procesar solicitud de eliminación');
            if (onError) onError(error);
        }
    }

    /**
     * Ejecuta la eliminación del tipo de código de usuario
     * @param {Object} userCodeTypeData - Datos del tipo de código de usuario
     * @param {Function} onSuccess - Callback de éxito
     * @param {Function} onError - Callback de error
     */
    async executeDelete(userCodeTypeData, onSuccess = null, onError = null) {
        try {
            console.log('⚡ Ejecutando eliminación del tipo de código de usuario:', userCodeTypeData);

            // Mostrar estado de carga global
            this.setGlobalLoading(true);

            // Llamar al servicio de eliminación
            const response = await this.deleteService.deleteUserCodeType(userCodeTypeData.id);

            console.log('✅ Tipo de código de usuario eliminado exitosamente:', response);

            // Mostrar mensaje de éxito
            this.showSuccessToast(`Tipo de código de usuario "${userCodeTypeData.name}" eliminado exitosamente`);

            // Ejecutar callback de éxito
            if (onSuccess && typeof onSuccess === 'function') {
                onSuccess(response.data);
            }

        } catch (error) {
            console.error('❌ Error al ejecutar eliminación:', error);
            this.showErrorToast(error.message || 'Error al eliminar tipo de código de usuario');
            
            // Ejecutar callback de error
            if (onError && typeof onError === 'function') {
                onError(error);
            }
        } finally {
            // Ocultar estado de carga global
            this.setGlobalLoading(false);
        }
    }

    /**
     * Controla el estado de carga global
     * @param {boolean} loading - Estado de carga
     */
    setGlobalLoading(loading) {
        // Implementar según el sistema de loading global disponible
        if (window.globalLoadingController) {
            if (loading) {
                window.globalLoadingController.show();
            } else {
                window.globalLoadingController.hide();
            }
        }
        
        console.log(`🔄 Estado de carga global: ${loading ? 'MOSTRADO' : 'OCULTO'}`);
    }

    /**
     * Recarga la lista de tipos de código de usuario
     */
    reloadUserCodeTypeList() {
        if (window.userCodeTypeListController && typeof window.userCodeTypeListController.load === 'function') {
            console.log('🔄 Recargando lista de tipos de código de usuario...');
            window.userCodeTypeListController.load();
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
            alert('Error: ' + message);
        }
    }
}

// Hacer disponible globalmente
window.DeleteUserCodeTypeController = DeleteUserCodeTypeController;

console.log('✅ DeleteUserCodeTypeController cargado correctamente');
