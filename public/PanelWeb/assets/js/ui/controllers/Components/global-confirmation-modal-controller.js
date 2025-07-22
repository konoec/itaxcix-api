/**
 * Global Confirmation Modal Controller
 * Controlador universal para modales de confirmación de eliminación
 * Proporciona consistencia visual y funcional en todo el sistema
 */

class GlobalConfirmationModalController {
    constructor() {
        this.modalId = 'global-confirmation-modal';
        this.currentCallback = null;
        this.currentData = null;
        
        console.log('🗑️ Inicializando GlobalConfirmationModalController...');
        this.init();
    }

    /**
     * Inicializa el controlador
     */
    init() {
        // Crear el modal si no existe
        if (!document.getElementById(this.modalId)) {
            this.createModalHTML();
        }
        
        // Configurar eventos
        this.bindEvents();
        
        console.log('✅ Modal global de confirmación inicializado');
    }

    /**
     * Crea el HTML del modal con dimensiones estándar de Tabler
     */
    createModalHTML() {
        const modalHTML = `
            <div class="modal modal-blur fade" id="${this.modalId}" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-sm modal-dialog-centered" role="document" style="max-width: 380px !important;">
                    <div class="modal-content">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        <div class="modal-status bg-danger"></div>
                        <div class="modal-body text-center py-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon mb-2 text-danger icon-lg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M12 9v2m0 4v.01"/>
                                <path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75"/>
                            </svg>
                            
                            <h3 id="global-confirmation-title">¿Está seguro de eliminar?</h3>
                            
                            <div id="global-confirmation-content">
                                <div class="card card-sm">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-auto">
                                                <span id="global-confirmation-avatar" class="bg-blue text-white avatar">
                                                    <svg xmlns="http://www.w3.org/2000/svg" id="global-confirmation-icon" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                        <line x1="3" y1="21" x2="21" y2="21"/>
                                                        <line x1="9" y1="8" x2="10" y2="8"/>
                                                        <line x1="9" y1="12" x2="10" y2="12"/>
                                                        <line x1="9" y1="16" x2="10" y2="16"/>
                                                        <line x1="14" y1="8" x2="15" y2="8"/>
                                                        <line x1="14" y1="12" x2="15" y2="12"/>
                                                        <line x1="14" y1="16" x2="15" y2="16"/>
                                                        <path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16"/>
                                                    </svg>
                                                </span>
                                            </div>
                                            <div class="col">
                                                <div class="font-weight-medium" id="global-confirmation-name">
                                                    <span class="placeholder col-6"></span>
                                                </div>
                                                <div class="text-muted" id="global-confirmation-subtitle">
                                                    <span class="placeholder col-8"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="global-confirmation-loading" style="display: none;" class="py-3">
                                <div class="progress progress-sm mb-3">
                                    <div class="progress-bar progress-bar-indeterminate bg-danger"></div>
                                </div>
                                <div class="text-muted" id="global-confirmation-loading-text">Eliminando...</div>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <div class="w-100">
                                <div class="row">
                                    <div class="col">
                                        <a href="#" class="btn w-100" data-bs-dismiss="modal" id="global-confirmation-cancel-btn">
                                            Cancelar
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a href="#" class="btn btn-danger w-100" id="global-confirmation-confirm-btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M4 7l16 0"/>
                                                <path d="M10 11l0 6"/>
                                                <path d="M14 11l0 6"/>
                                                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"/>
                                                <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"/>
                                            </svg>
                                            <span id="global-confirmation-confirm-text">Eliminar</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHTML);
    }

    /**
     * Configura los eventos
     */
    bindEvents() {
        const modal = document.getElementById(this.modalId);
        const confirmBtn = document.getElementById('global-confirmation-confirm-btn');

        if (confirmBtn) {
            confirmBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleConfirm();
            });
        }

        if (modal) {
            modal.addEventListener('hidden.bs.modal', () => {
                this.resetModal();
            });
        }
    }

    /**
     * Abre el modal de confirmación
     * @param {Object} options - Opciones de configuración
     * @param {string} options.title - Título del modal (opcional)
     * @param {string} options.name - Nombre del elemento a eliminar
     * @param {string} options.subtitle - Subtítulo o información adicional
     * @param {string} options.iconSvg - SVG completo del ícono (Tabler)
     * @param {string} options.avatarColor - Color del avatar (bg-blue, bg-primary, etc.)
     * @param {string} options.confirmText - Texto del botón de confirmación
     * @param {string} options.loadingText - Texto durante la carga
     * @param {Function} options.onConfirm - Callback ejecutado al confirmar
     * @param {Object} options.data - Datos adicionales pasados al callback
     */
    showConfirmation(options = {}) {
        const {
            title = '¿Está seguro de eliminar?',
            name = 'Elemento',
            subtitle = 'Esta acción no se puede deshacer',
            iconSvg = null, // SVG completo del ícono
            avatarColor = 'bg-blue',
            confirmText = 'Eliminar',
            loadingText = 'Eliminando...',
            onConfirm = () => {},
            data = {}
        } = options;

        // Guardar callback y datos
        this.currentCallback = onConfirm;
        this.currentData = data;

        // Configurar contenido del modal
        this.updateModalContent({
            title,
            name,
            subtitle,
            iconSvg,
            avatarColor,
            confirmText,
            loadingText
        });

        // Mostrar modal
        this.showModal();
    }

    /**
     * Actualiza el contenido del modal
     */
    updateModalContent(content) {
        const titleElement = document.getElementById('global-confirmation-title');
        const nameElement = document.getElementById('global-confirmation-name');
        const subtitleElement = document.getElementById('global-confirmation-subtitle');
        const iconElement = document.getElementById('global-confirmation-icon');
        const avatarElement = document.getElementById('global-confirmation-avatar');
        const confirmTextElement = document.getElementById('global-confirmation-confirm-text');
        const loadingTextElement = document.getElementById('global-confirmation-loading-text');

        if (titleElement) titleElement.textContent = content.title;
        if (nameElement) {
            nameElement.textContent = content.name;
            nameElement.classList.remove('placeholder');
        }
        if (subtitleElement) {
            subtitleElement.textContent = content.subtitle;
            subtitleElement.classList.remove('placeholder');
        }
        
        // Actualizar ícono SVG si se proporciona
        if (content.iconSvg && iconElement) {
            iconElement.outerHTML = content.iconSvg;
        }
        
        if (avatarElement) {
            // Limpiar clases de color anteriores
            avatarElement.className = avatarElement.className.replace(/bg-\w+/g, '');
            avatarElement.classList.add(content.avatarColor, 'text-white', 'avatar');
        }
        if (confirmTextElement) confirmTextElement.textContent = content.confirmText;
        if (loadingTextElement) loadingTextElement.textContent = content.loadingText;
    }

    /**
     * Muestra el modal
     */
    showModal() {
        const modal = document.getElementById(this.modalId);
        if (modal) {
            const bsModal = new bootstrap.Modal(modal);
            bsModal.show();
        }
    }

    /**
     * Maneja la confirmación
     */
    async handleConfirm() {
    // Callback seguro: nunca lanza error si no existe, solo función vacía
    const cb = (typeof this.currentCallback === 'function') ? this.currentCallback : () => {};
    try {
        this.showLoading(true);
        await cb(this.currentData);
        this.closeModal();
    } catch (error) {
        console.error('❌ Error en confirmación:', error);
        this.showError(error.message || 'Error al procesar la operación');
    } finally {
        this.showLoading(false);
    }
}


    /**
     * Muestra/oculta el estado de carga
     */
    showLoading(show) {
        const content = document.getElementById('global-confirmation-content');
        const loading = document.getElementById('global-confirmation-loading');
        const confirmBtn = document.getElementById('global-confirmation-confirm-btn');
        const cancelBtn = document.getElementById('global-confirmation-cancel-btn');

        if (content) content.style.display = show ? 'none' : 'block';
        if (loading) loading.style.display = show ? 'block' : 'none';
        if (confirmBtn) confirmBtn.style.display = show ? 'none' : 'block';
        if (cancelBtn) {
            cancelBtn.textContent = show ? 'Esperar...' : 'Cancelar';
            cancelBtn.disabled = show;
        }
    }

    /**
     * Cierra el modal
     */
    closeModal() {
        const modal = document.getElementById(this.modalId);
        if (modal) {
            const bsModal = bootstrap.Modal.getInstance(modal);
            if (bsModal) {
                bsModal.hide();
            }
        }
    }

    /**
     * Resetea el modal a su estado inicial
     */
    resetModal() {
        this.currentCallback = null;
        this.currentData = null;
        this.showLoading(false);
        
        // Resetear placeholders
        const nameElement = document.getElementById('global-confirmation-name');
        const subtitleElement = document.getElementById('global-confirmation-subtitle');
        
        if (nameElement) {
            nameElement.innerHTML = '<span class="placeholder col-6"></span>';
        }
        
        if (subtitleElement) {
            subtitleElement.innerHTML = '<span class="placeholder col-8"></span>';
        }
    }

    /**
     * Muestra un mensaje de error
     */
    showError(message) {
        if (typeof window.showRecoveryToast === 'function') {
            window.showRecoveryToast(message, 'error');
        } else if (typeof window.GlobalToast?.show === 'function') {
            window.GlobalToast.show(message, 'error');
        } else {
            alert(`Error: ${message}`);
        }
    }
}

// Crear instancia global única cuando el DOM esté listo
if (!window.globalConfirmationModal) {
    // Si el DOM ya está cargado, crear inmediatamente
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            if (!window.globalConfirmationModal) {
                window.globalConfirmationModal = new GlobalConfirmationModalController();
                window.GlobalConfirmationModalController = GlobalConfirmationModalController;
            }
        });
    } else {
        // El DOM ya está cargado
        window.globalConfirmationModal = new GlobalConfirmationModalController();
        window.GlobalConfirmationModalController = GlobalConfirmationModalController;
    }
}

console.log('✅ GlobalConfirmationModalController cargado correctamente');
