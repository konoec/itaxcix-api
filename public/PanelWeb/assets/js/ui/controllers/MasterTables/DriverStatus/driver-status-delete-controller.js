/**
 * Controlador para eliminar estados de conductor
 * Maneja la confirmación y eliminación de estados de conductor usando modal global
 */
class DriverStatusDeleteController {
    constructor() {
        this.isDeleting = false;
        this.confirmationModal = null;
        this.ensureConfirmationModal();
        this.bindEvents();
    }

    /**
     * Asegura que el modal de confirmación esté disponible
     */
    ensureConfirmationModal() {
        // Buscar modal de confirmación global en diferentes referencias
        if (window.globalConfirmationModal) {
            this.confirmationModal = window.globalConfirmationModal;
            console.log('✅ Modal de confirmación global encontrado (globalConfirmationModal)');
        } else if (window.GlobalConfirmationModalController) {
            this.confirmationModal = window.GlobalConfirmationModalController;
            console.log('✅ Modal de confirmación global encontrado (GlobalConfirmationModalController)');
        } else if (window.globalConfirmationModalController) {
            this.confirmationModal = window.globalConfirmationModalController;
            console.log('✅ Modal de confirmación global encontrado (globalConfirmationModalController)');
        } else {
            // Intentar crear instancia del modal global
            if (typeof GlobalConfirmationModalController !== 'undefined') {
                console.log('🔧 Creando instancia de GlobalConfirmationModalController...');
                window.globalConfirmationModal = new GlobalConfirmationModalController();
                this.confirmationModal = window.globalConfirmationModal;
            } else {
                console.warn('⚠️ Modal de confirmación global no disponible');
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
            <div class="modal modal-blur fade" id="driver-status-delete-confirmation-modal" tabindex="-1" role="dialog" aria-hidden="true">
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
                                <h4 id="driver-status-confirmation-title">¿Eliminar estado de conductor?</h4>
                                <p id="driver-status-confirmation-message" class="text-muted">Esta acción no se puede deshacer.</p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>
                                Cancelar
                            </button>
                            <button type="button" class="btn btn-danger" id="driver-status-confirm-delete-btn">
                                <i class="fas fa-trash-alt me-1"></i>
                                Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Agregar modal al DOM si no existe
        if (!document.getElementById('driver-status-delete-confirmation-modal')) {
            document.body.insertAdjacentHTML('beforeend', modalHTML);
        }

        // Crear objeto de control básico
        this.confirmationModal = {
            showConfirmation: (config, onConfirm) => {
                const modal = document.getElementById('driver-status-delete-confirmation-modal');
                const titleEl = document.getElementById('driver-status-confirmation-title');
                const messageEl = document.getElementById('driver-status-confirmation-message');
                const confirmBtn = document.getElementById('driver-status-confirm-delete-btn');

                // Configurar contenido
                if (titleEl) titleEl.textContent = config.title || 'Confirmar eliminación';
                if (messageEl) messageEl.innerHTML = config.message || `¿Eliminar "${config.name}"?`;

                // Configurar evento de confirmación
                const handleConfirm = async () => {
                    confirmBtn.removeEventListener('click', handleConfirm);
                    
                    // Ejecutar callback
                    if (onConfirm) {
                        await onConfirm();
                    }
                    
                    // Cerrar modal
                    const bsModal = bootstrap.Modal.getInstance(modal);
                    if (bsModal) bsModal.hide();
                };

                confirmBtn.addEventListener('click', handleConfirm);

                // Mostrar modal
                const bsModal = new bootstrap.Modal(modal);
                bsModal.show();
            }
        };
    }

    /**
     * Abre modal de confirmación para eliminar estado de conductor
     * @param {number} id - ID del estado de conductor a eliminar
     * @param {object} driverStatusData - Datos del estado de conductor
     */
    async confirmDelete(id, driverStatusData = null) {
        try {
            console.log('🗑️ Iniciando confirmación de eliminación para estado:', id);

            // Si no se proporcionan datos, buscarlos en la lista actual
            if (!driverStatusData) {
                driverStatusData = this.findDriverStatusInList(id);
            }

            if (!driverStatusData) {
                throw new Error('No se encontraron los datos del estado de conductor');
            }

            // Generar configuración del modal de confirmación
            const confirmConfig = window.DriverStatusDeleteService.generateConfirmationConfig(driverStatusData);

            // Mostrar modal de confirmación
            if (this.confirmationModal && typeof this.confirmationModal.showConfirmation === 'function') {
                this.confirmationModal.showConfirmation({
                    title: 'Confirmar Eliminación',
                    name: driverStatusData.name,
                    subtitle: 'Esta acción no se puede deshacer',
                    iconSvg: `
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon text-primary" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0"/>
                            <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6"/>
                        </svg>
                    `,
                    avatarColor: 'bg-primary',
                    confirmText: 'Sí, Eliminar',
                    loadingText: 'Eliminando...',
                    onConfirm: () => this.executeDelete(id, driverStatusData),
                    data: { id, driverStatusData }
                });
            } else {
                // Fallback si no existe el modal de confirmación
                const confirmed = confirm(`¿Estás seguro de eliminar el estado "${driverStatusData.name}"?\n\nEsta acción no se puede deshacer.`);
                if (confirmed) {
                    await this.executeDelete(id, driverStatusData);
                }
            }

        } catch (error) {
            console.error('❌ Error al abrir confirmación de eliminación:', error);
            if (window.GlobalToast) {
                window.GlobalToast.show(error.message, 'error');
            }
        }
    }

    /**
     * Ejecuta la eliminación del estado de conductor
     * @param {number} id - ID del estado de conductor
     * @param {object} driverStatusData - Datos del estado de conductor
     */
    async executeDelete(id, driverStatusData) {
        if (this.isDeleting) return;

        try {
            this.setDeletingState(true);
            console.log('🗑️ Ejecutando eliminación del estado:', id);

            // Mostrar toast de progreso
            if (window.GlobalToast) {
                window.GlobalToast.show('Eliminando estado de conductor...', 'info');
            }

            // Realizar eliminación
            const response = await window.DriverStatusDeleteService.deleteDriverStatus(id);

            if (response.success) {
                // Mostrar mensaje de éxito
                if (window.GlobalToast) {
                    window.GlobalToast.show(
                        response.message || `Estado "${driverStatusData.name}" eliminado correctamente`,
                        'success'
                    );
                }

                // Recargar lista si existe el controlador
                await this.refreshDriverStatusList();

                console.log('✅ Estado de conductor eliminado exitosamente');
            }

        } catch (error) {
            console.error('❌ Error al eliminar estado de conductor:', error);
            if (window.GlobalToast) {
                window.GlobalToast.show(
                    error.message || 'Error al eliminar el estado de conductor',
                    'error'
                );
            }
        } finally {
            this.setDeletingState(false);
        }
    }

    /**
     * Busca los datos del estado en la lista actual
     * @param {number} id - ID del estado de conductor
     * @returns {object|null} Datos del estado encontrado
     */
    findDriverStatusInList(id) {
        try {
            // Verificar múltiples referencias posibles del controlador
            const listController = window.driverStatusListController || window.driverStatusListControllerInstance;
            
            if (!listController) {
                console.warn('⚠️ Controlador de lista no disponible');
                return null;
            }

            if (!listController.driverStatuses || !Array.isArray(listController.driverStatuses)) {
                console.warn('⚠️ No hay datos en la lista de estados');
                return null;
            }

            const driverStatus = listController.driverStatuses.find(status => status.id === id);
            
            if (!driverStatus) {
                console.warn('⚠️ Estado no encontrado en la lista. ID:', id);
                return null;
            }

            return driverStatus;

        } catch (error) {
            console.error('❌ Error al buscar estado en la lista:', error);
            return null;
        }
    }

    /**
     * Recarga la lista de estados de conductor
     */
    async refreshDriverStatusList() {
        try {
            const listController = window.driverStatusListController || window.driverStatusListControllerInstance;
            
            if (listController && typeof listController.loadDriverStatuses === 'function') {
                console.log('🔄 Recargando lista de estados de conductor...');
                await listController.loadDriverStatuses();
                console.log('✅ Lista recargada exitosamente');
            } else {
                console.warn('⚠️ No se pudo recargar la lista - controlador no disponible');
            }
        } catch (error) {
            console.error('❌ Error al recargar lista:', error);
        }
    }

    /**
     * Controla el estado de eliminación
     * @param {boolean} isDeleting - Si está en proceso de eliminación
     */
    setDeletingState(isDeleting) {
        this.isDeleting = isDeleting;
        
        // Deshabilitar todos los botones de eliminar durante el proceso
        const deleteButtons = document.querySelectorAll('[data-action="delete-driver-status"]');
        deleteButtons.forEach(btn => {
            btn.disabled = isDeleting;
            if (isDeleting) {
                btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            } else {
                btn.innerHTML = '<i class="fas fa-trash"></i>';
            }
        });
    }

    /**
     * Configura eventos globales
     */
    bindEvents() {
        // Evento para botón eliminar
        document.addEventListener('click', (e) => {
            if (e.target.closest('[data-action="delete-driver-status"]')) {
                e.preventDefault();
                
                if (this.isDeleting) {
                    console.log('⏳ Eliminación en progreso, ignorando click');
                    return;
                }

                const btn = e.target.closest('[data-action="delete-driver-status"]');
                const id = btn.getAttribute('data-driver-status-id');
                
                console.log('🖱️ Click en botón eliminar. ID:', id);
                
                if (id) {
                    // Buscar los datos en la lista actual
                    const driverStatusData = this.findDriverStatusInList(parseInt(id));
                    console.log('📊 Datos encontrados:', !!driverStatusData);
                    
                    this.confirmDelete(parseInt(id), driverStatusData);
                } else {
                    console.error('❌ No se encontró ID en el botón de eliminar');
                    if (window.GlobalToast) {
                        window.GlobalToast.show('Error: ID del estado no válido', 'error');
                    }
                }
            }
        });
    }

    /**
     * Método público para eliminar desde código
     * @param {number} id - ID del estado de conductor
     * @param {object} driverStatusData - Datos opcionales del estado
     */
    async deleteDriverStatus(id, driverStatusData = null) {
        await this.confirmDelete(id, driverStatusData);
    }
}

// Inicializar controlador
window.DriverStatusDeleteController = new DriverStatusDeleteController();
window.driverStatusDeleteController = window.DriverStatusDeleteController;

console.log('✅ DriverStatusDeleteController cargado y disponible globalmente');
