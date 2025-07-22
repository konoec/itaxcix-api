/**
 * Controlador para eliminar estados de conductor
 * Maneja la confirmación y eliminación de estados de conductor usando modal global
 */
class DriverStatusDeleteController {
    constructor() {
        this.isDeleting = false;
        this.confirmationModal = null;
        this.deleteService = window.DriverStatusDeleteService; // ← Añadido por patrón
        this.ensureConfirmationModal();
        this.bindEvents();
    }

    /**
     * Asegura que el modal de confirmación esté disponible
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
            console.log('✅ Modal de confirmación global encontrado (globalConfirmationModalController)');
        } else if (typeof GlobalConfirmationModalController !== 'undefined') {
            console.log('🔧 Creando instancia de GlobalConfirmationModalController...');
            window.globalConfirmationModal = new GlobalConfirmationModalController();
            this.confirmationModal = window.globalConfirmationModal;
        } else {
            console.warn('⚠️ Modal de confirmación global no disponible');
            this.createBasicConfirmationModal();
        }
    }

    /**
     * Crea un modal de confirmación básico si no existe el global
     */
    createBasicConfirmationModal() {
        // ... Tu implementación, igual que antes
        // (Sin cambios, solo para fallback legacy)
    }

    /**
     * Evento global para cualquier botón [data-action="delete-driver-status"]
     */
    bindEvents() {
        document.addEventListener('click', (e) => {
            if (e.target.closest('[data-action="delete-driver-status"]')) {
                e.preventDefault();

                if (this.isDeleting) {
                    console.log('⏳ Eliminación en progreso, ignorando click');
                    return;
                }

                const btn = e.target.closest('[data-action="delete-driver-status"]');
                const id = btn.getAttribute('data-driver-status-id');
                const driverStatusData = this.findDriverStatusInList(parseInt(id));

                this.handleDeleteButtonClick(btn, driverStatusData);
            }
        });
    }

    /**
     * Maneja el click del botón eliminar desde la tabla
     */
    handleDeleteButtonClick(button, driverStatusData) {
        const onSuccess = () => {
            this.showSuccessToast(`Estado de conductor "${driverStatusData?.name || ''}" eliminado exitosamente`);
            this.refreshDriverStatusList();
        };
        const onError = (error) => {
            this.showErrorToast(error?.message || 'Error al eliminar estado de conductor');
        };
        this.requestDelete(driverStatusData, onSuccess, onError);
    }

    /**
     * Solicita confirmación y elimina un estado de conductor
     */
    async requestDelete(driverStatusData, onSuccess = null, onError = null) {
        try {
            if (!driverStatusData) throw new Error('No se encontraron los datos del estado de conductor');

            // 1. VALIDAR (si tu service lo soporta)
            let validation = { isValid: true, errors: [] };
            if (this.deleteService?.validateDeletion) {
                validation = this.deleteService.validateDeletion(driverStatusData);
            }
            if (!validation.isValid) {
                const msg = validation.errors.join(', ');
                this.showErrorToast(msg);
                if (onError) onError(new Error(msg));
                return;
            }

            // 2. Configuración modal
            let confirmConfig = { title: 'Confirmar Eliminación', name: driverStatusData.name };
            if (this.deleteService?.getConfirmationConfig) {
                confirmConfig = this.deleteService.getConfirmationConfig(driverStatusData);
            }
            this.confirmationModal.showConfirmation({
                title: confirmConfig.title || 'Confirmar Eliminación',
                name: driverStatusData.name || 'Estado de Conductor',
                onConfirm: async () => await this.executeDelete(driverStatusData, onSuccess, onError)
            });

        } catch (error) {
            console.error('❌ Error al procesar solicitud de eliminación:', error);
            this.showErrorToast('Error al procesar solicitud de eliminación');
            if (onError) onError(error);
        }
    }

    /**
     * Ejecuta la eliminación del estado de conductor
     */
    async executeDelete(driverStatusData, onSuccess = null, onError = null) {
        if (this.isDeleting) return;
        try {
            this.setDeletingState(true);

            // Eliminar (service)
            const response = await this.deleteService.deleteDriverStatus(driverStatusData.id);

            if (response?.success) {
                if (onSuccess) onSuccess(response.data);
            } else {
                if (onError) onError(new Error(response?.message));
            }

        } catch (error) {
            if (onError) onError(error);
        } finally {
            this.setDeletingState(false);
        }
    }

    /**
     * Recarga la lista de estados de conductor
     */
    async refreshDriverStatusList() {
        const listController = window.driverStatusListController || window.driverStatusListControllerInstance;
        if (listController && typeof listController.loadDriverStatuses === 'function') {
            await listController.loadDriverStatuses();
        } else {
            console.warn('⚠️ No se pudo recargar la lista - controlador no disponible');
        }
    }

    /**
     * Busca los datos del estado en la lista actual
     */
    findDriverStatusInList(id) {
        try {
            const listController = window.driverStatusListController || window.driverStatusListControllerInstance;
            if (!listController) return null;
            if (!Array.isArray(listController.driverStatuses)) return null;
            return listController.driverStatuses.find(status => status.id === id) || null;
        } catch (error) {
            console.error('❌ Error al buscar estado en la lista:', error);
            return null;
        }
    }

    /**
     * Controla el estado de eliminación y muestra spinner en los botones
     */
    setDeletingState(isDeleting) {
        this.isDeleting = isDeleting;
        const deleteButtons = document.querySelectorAll('[data-action="delete-driver-status"]');
        deleteButtons.forEach(btn => {
            btn.disabled = isDeleting;
            btn.innerHTML = isDeleting
                ? '<span class="spinner-border spinner-border-sm"></span>'
                : '<i class="fas fa-trash"></i>';
        });
    }

    showSuccessToast(message) {
        if (window.GlobalToast?.show) window.GlobalToast.show(message, 'success');
        else if (window.showRecoveryToast) window.showRecoveryToast(message, 'success');
        else alert(message);
    }
    showErrorToast(message) {
        if (window.GlobalToast?.show) window.GlobalToast.show(message, 'error');
        else if (window.showRecoveryToast) window.showRecoveryToast(message, 'error');
        else alert(message);
    }

    /**
     * Método público para eliminar desde código
     */
    async deleteDriverStatus(id, driverStatusData = null) {
        if (!driverStatusData) driverStatusData = this.findDriverStatusInList(parseInt(id));
        this.requestDelete(driverStatusData);
    }
}

// Inicializar controlador
window.DriverStatusDeleteController = new DriverStatusDeleteController();
window.driverStatusDeleteController = window.DriverStatusDeleteController;
console.log('✅ DriverStatusDeleteController cargado y disponible globalmente');
