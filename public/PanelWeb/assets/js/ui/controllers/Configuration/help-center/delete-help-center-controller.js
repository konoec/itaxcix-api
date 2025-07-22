/**
 * Controlador para el modal de eliminación de elementos del centro de ayuda
 * Utiliza el modal global de confirmación del sistema
 */
class DeleteHelpCenterController {
    constructor(onSuccess) {
        this.onSuccess = onSuccess || (() => {});
        this.confirmationModal = null;
        
        // Bind methods
        this.showDeleteConfirmation = this.showDeleteConfirmation.bind(this);
        this.handleDelete = this.handleDelete.bind(this);
        
        console.log('🗑️ DeleteHelpCenterController inicializado');
        
        // Verificar que el modal de confirmación esté disponible
        this.ensureConfirmationModal();
    }

    /**
     * Verifica que el modal de confirmación global esté disponible
     */
    ensureConfirmationModal() {
        if (window.globalConfirmationModal) {
            this.confirmationModal = window.globalConfirmationModal;
            console.log('✅ Modal de confirmación global encontrado (instancia global)');
        } else if (typeof GlobalConfirmationModalController !== 'undefined') {
            console.log('🔧 Creando instancia de GlobalConfirmationModalController...');
            window.globalConfirmationModal = new GlobalConfirmationModalController();
            this.confirmationModal = window.globalConfirmationModal;
            console.log('✅ Modal de confirmación global creado exitosamente');
        } else {
            console.warn('⚠️ Modal de confirmación global no encontrado, usando método básico');
            this.confirmationModal = null;
        }
    }

    /**
     * Muestra el modal de confirmación de eliminación
     * @param {number} itemId - ID del elemento a eliminar
     * @param {Object} itemData - Datos del elemento
     */
    showDeleteConfirmation(itemId, itemData) {
        console.log('🗑️ Mostrando confirmación de eliminación para ID:', itemId);
        
        if (!itemData) {
            console.error('❌ No se proporcionaron datos del elemento');
            this.showToast('Error: Datos del elemento no disponibles', 'error');
            return;
        }

        if (!this.confirmationModal) {
            console.warn('⚠️ Modal de confirmación global no disponible, usando confirmación básica');
            this.showBasicConfirmation(itemId, itemData);
            return;
        }

        // Configurar el modal global de confirmación
        const modalOptions = {
            title: '¿Eliminar elemento del centro de ayuda?',
            name: itemData.title || 'Elemento sin título',
            subtitle: itemData.subtitle || 'Esta acción no se puede deshacer',
            iconSvg: `<svg xmlns="http://www.w3.org/2000/svg" id="global-confirmation-icon" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0"/>
                <path d="M12 17l0 .01"/>
                <path d="M12 13.5a1.5 1.5 0 0 1 1 -1.5a2.6 2.6 0 1 0 -3 -4"/>
            </svg>`,
            avatarColor: 'bg-warning',
            confirmText: 'Eliminar',
            loadingText: 'Eliminando elemento...',
            onConfirm: async () => {
                await this.handleDelete(itemId, itemData);
            },
            data: { id: itemId, item: itemData }
        };

        this.confirmationModal.showConfirmation(modalOptions);
    }

    /**
     * Maneja la eliminación del elemento
     */
    async handleDelete(itemId, itemData) {
        try {
            console.log('🗑️ Eliminando elemento con ID:', itemId);

            // Llamar al servicio de eliminación
            const response = await window.DeleteHelpCenterService.deleteHelpCenterItem(itemId);

            console.log('✅ Elemento eliminado exitosamente:', response);

            // Mostrar mensaje de éxito
            this.showToast('Elemento eliminado correctamente', 'success');

            // Ejecutar callback de éxito
            if (typeof this.onSuccess === 'function') {
                this.onSuccess(itemData);
            }

        } catch (error) {
            console.error('❌ Error al eliminar elemento:', error);
            this.showToast(error.message || 'Error al eliminar elemento', 'error');
            throw error; // Re-lanzar para que el modal global maneje el error
        }
    }

    /**
     * Confirmación básica como fallback si no está disponible el modal global
     */
    showBasicConfirmation(itemId, itemData) {
        const title = itemData.title || 'Elemento sin título';
        const confirmed = confirm(`¿Está seguro de que desea eliminar "${title}"?\n\nEsta acción no se puede deshacer.`);
        
        if (confirmed) {
            this.handleDelete(itemId, itemData).catch(error => {
                console.error('❌ Error en eliminación básica:', error);
            });
        }
    }

    /**
     * Muestra un toast de notificación
     * @param {string} message - Mensaje a mostrar
     * @param {string} type - Tipo de mensaje (success, error, info)
     */
    showToast(message, type = 'info') {
        console.log(`🍞 Toast: ${message} (${type})`);
        
        // Usar el sistema de toast global si está disponible
        if (window.showRecoveryToast) {
            window.showRecoveryToast(message, type);
        } else if (window.GlobalToast) {
            window.GlobalToast.show(message, type);
        } else if (window.showToast) {
            window.showToast(message, type);
        } else {
            // Fallback simple
            alert(message);
        }
    }
}

// Exportar controlador globalmente
window.DeleteHelpCenterController = DeleteHelpCenterController;
window.DeleteHelpCenterController = DeleteHelpCenterController;
