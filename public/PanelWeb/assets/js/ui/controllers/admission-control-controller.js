// Controlador específico para la página de Admission Control
// Maneja toda la funcionalidad relacionada con la aprobación/rechazo de conductores

// Clase principal que controla la interfaz de usuario del admission control
class AdmissionControlController {
    constructor() {
        // Instancia del servicio de conductores que maneja la comunicación con la API real
        this.conductorService = new ConductorService();

        // Referencias a elementos del DOM
        this.driversList = document.getElementById('drivers-list');
        this.loadingIndicator = document.getElementById('loading-indicator');
        
        // Modal usando Tabler
        this.modal = null; // Se inicializará después
        this.modalElement = document.getElementById('driver-modal');
        this.modalName = document.getElementById('modal-name');
        this.modalDni = document.getElementById('modal-dni');
        this.modalPlaca = document.getElementById('modal-placa');
        this.modalContacto = document.getElementById('modal-contacto');
        this.modalEstado = document.getElementById('modal-estado');
        this.modalEstadoBadge = document.getElementById('modal-estado-badge');
        this.modalAvatar = document.getElementById('modal-avatar');
        this.modalRuc = document.getElementById('modal-ruc');
        this.modalTucIssue = document.getElementById('modal-tuc-issue');
        this.modalTucExpiration = document.getElementById('modal-tuc-expiration');
        this.modalTucModality = document.getElementById('modal-tuc-modality');
        this.modalTucType = document.getElementById('modal-tuc-type');
        this.modalApproveBtn = document.getElementById('modal-approve-btn');
        this.modalRejectBtn = document.getElementById('modal-reject-btn');
        
        // Toast de notificación
        this.toast = document.getElementById('toast');
        this.toastMessage = document.getElementById('toast-message');
        
        // Referencias para el sidebar - REMOVIDAS (ahora manejadas por SidebarController y TopBarController)
        // this.sidebar = document.getElementById('sidebar');
        // this.openSidebarBtn = document.getElementById('open-sidebar');
        // this.closeSidebarBtn = document.getElementById('close-sidebar');
        // this.mainContent = document.querySelector('.main-content');
        
        // Referencias para el modal de confirmación
        this.confirmationModal = document.getElementById('confirmation-modal');
        this.confirmationModalInstance = null; // Se inicializará después
        this.confirmationTitle = document.getElementById('confirmation-title');
        this.confirmationMessage = document.getElementById('confirmation-message');
        this.confirmationIcon = document.getElementById('confirmation-icon');
        this.confirmationCancel = document.getElementById('confirmation-cancel');
        this.confirmationConfirm = document.getElementById('confirmation-confirm');

        // Variables para paginación
        this.allDrivers = [];
        this.currentPage = 0;
        this.perPage = 9;
        this.totalPages = 1;
        this.prevPageBtn = document.getElementById('prev-page-btn');
        this.nextPageBtn = document.getElementById('next-page-btn');
        this.paginationInfo = document.getElementById('pagination-info');
        
        // Variable para almacenar el conductor actual
        this.currentDriver = null;
    }    // Método principal que inicializa la aplicación - CORREGIDO
    // Es async porque realiza operaciones asíncronas (como cargar datos de la API)
    async init() {
        try {
            console.log('🚗 Inicializando AdmissionControlController...');
            
            // Debug: verificar estado de autenticación
            const isLoggedIn = sessionStorage.getItem("isLoggedIn");
            const authToken = sessionStorage.getItem("authToken");
            const userId = sessionStorage.getItem("userId");
            
            console.log(`🔐 Estado de autenticación:`, {
                isLoggedIn: isLoggedIn,
                hasToken: !!authToken,
                tokenPreview: authToken ? authToken.substring(0, 20) + '...' : 'N/A',
                userId: userId
            });
            
            this.showLoading(true);
            
            try {
                // Obtener todos los conductores pendientes de la API (sin paginación)
                const conductoresData = await this.conductorService.obtenerConductoresPendientes();
                this.allDrivers = conductoresData.map(data => Conductor.fromApiData(data));
                
                this.totalPages = Math.ceil(this.allDrivers.length / this.perPage);
                this.currentPage = 0;
                this.renderDriversPage();
                this.initializeEvents();
                this.showLoading(false);
            } catch (error) {
                console.error('Error al cargar conductores desde API:', error);
                this.showToast('Error al cargar los datos de conductores', 'error');
                this.showLoading(false);
                this.driversList.innerHTML = `
                    <tr>
                        <td colspan="5" class="no-data">
                            <i class="fas fa-exclamation-circle"></i>
                            Error al cargar los datos. Intente nuevamente.
                        </td>
                    </tr>
                `;
            }
        } catch (error) {
            console.error('Error general al inicializar la aplicación:', error);
            this.showToast('Error crítico al inicializar la aplicación', 'error');
            this.showLoading(false);
        }
    }

    // Método para mostrar u ocultar el indicador de carga
    showLoading(show) {
        if (this.loadingIndicator) {
            this.loadingIndicator.style.display = show ? 'flex' : 'none';
        }
    }

    // Método para renderizar conductor - ACTUALIZADO con solo iconos
    renderConductor(conductor) {
        // Verificar si es una instancia de la clase, si no, convertirlo
        if (!(conductor instanceof Conductor)) {
            conductor = Conductor.fromApiData(conductor);
        }
        
        const row = document.createElement('tr');
        row.className = 'driver-row';
        row.dataset.id = conductor.driverId;

        // Crear estructura HTML completamente nueva con celdas simples
        row.innerHTML = `
            <td>
                <div class="d-flex align-items-center">
                    <span class="avatar avatar-sm me-3" style="background-image: url('${conductor.getImagenUrl()}')">
                        <img src="${conductor.getImagenUrl()}" 
                             alt="Foto de perfil"
                             onerror="this.onerror=null; this.src='../../assets/Recourse/Imagenes/register_foto_defecto.png';">
                    </span>
                    <div>
                        <div class="conductor-name">${conductor.getNombreCompleto()}</div>
                    </div>
                </div>
            </td>
            <td>
                <span class="text-muted">${conductor.documentValue || 'N/A'}</span>
            </td>
            <td>
                <span class="text-muted">${conductor.plateValue || 'N/A'}</span>
            </td>
            <td>
                <span class="text-muted">${conductor.contactValue || 'N/A'}</span>
            </td>
            <td>
                <span class="badge bg-azure-lt">${conductor.tucStatus || 'PENDIENTE'}</span>
            </td>
            <td>
                <div class="btn-list">
                    <button class="btn btn-outline-primary btn-sm btn-details" data-id="${conductor.driverId}" title="Ver detalles">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-outline-success btn-sm btn-approve" data-id="${conductor.driverId}" title="Aprobar conductor">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="btn btn-outline-danger btn-sm btn-reject" data-id="${conductor.driverId}" title="Rechazar conductor">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </td>
        `;
        
        this.driversList.appendChild(row);
    }

    // Método para renderizar la página de conductores según la paginación
    renderDriversPage() {
        this.driversList.innerHTML = '';
        const start = this.currentPage * this.perPage;
        const end = start + this.perPage;
        const driversToShow = this.allDrivers.slice(start, end);
        if (driversToShow.length === 0) {
            this.driversList.innerHTML = `
                <tr>
                    <td colspan="5" class="no-data">
                        <i class="fas fa-info-circle"></i>
                        Aún no hay solicitudes de conductores pendientes por revisar.
                    </td>
                </tr>
            `;
        } else {
            driversToShow.forEach(conductor => this.renderConductor(conductor));
        }
        this.updatePaginationControls();
    }

    // Método para actualizar los controles de paginación
    updatePaginationControls() {
        if (this.prevPageBtn && this.nextPageBtn && this.paginationInfo) {
            this.prevPageBtn.disabled = this.currentPage <= 0;
            this.nextPageBtn.disabled = this.currentPage >= this.totalPages - 1;
            this.paginationInfo.textContent = `Página ${this.totalPages === 0 ? 0 : this.currentPage + 1} de ${this.totalPages}`;
        }
    }

    // Método para inicializar eventos - LIMPIO
    initializeEvents() {
        // Inicializar el modal de Tabler
        if (this.modalElement) {
            this.modal = new bootstrap.Modal(this.modalElement);
        }
        
        // Inicializar el modal de confirmación
        if (this.confirmationModal) {
            this.confirmationModalInstance = new bootstrap.Modal(this.confirmationModal);
        }

        // DELEGACIÓN DE EVENTOS para todos los botones de la tabla
        this.driversList.addEventListener('click', async (e) => {
            const button = e.target.closest('button');
            if (!button) return;
            
            const driverId = button.dataset.id; // Mantener como string
            console.log('🔍 Acción en conductor con ID:', driverId);
            
            // Obtener el nombre del conductor desde la tabla
            const row = button.closest('tr');
            const nombreConductor = row ? row.querySelector('.conductor-name')?.textContent || 'el conductor' : 'el conductor';
            
            if (button.classList.contains('btn-details')) {
                // BOTÓN DETALLES - OBTENER DESDE API
                try {
                    console.log('🔍 Obteniendo detalles del conductor desde API, ID:', driverId);
                    
                    // Obtener detalles directamente de la API
                    const conductor = await this.conductorService.obtenerConductorPendientePorId(driverId);
                    
                    if (conductor && conductor.driverId) {
                        console.log('👤 Detalles del conductor obtenidos:', conductor);
                        this.showDriverDetails(conductor);
                    } else {
                        console.warn('❌ No se pudieron obtener los detalles del conductor');
                        this.showToast('No se encontraron los detalles del conductor', 'error');
                    }
                } catch (error) {
                    console.error('Error al obtener detalles:', error);
                    this.showToast('Error al cargar los detalles del conductor', 'error');
                }
            } else if (button.classList.contains('btn-approve')) {
                // BOTÓN APROBAR - CON NOMBRE DESDE LA TABLA
                await this.aprobarConductor(driverId, nombreConductor);
            } else if (button.classList.contains('btn-reject')) {
                // BOTÓN RECHAZAR - CON NOMBRE DESDE LA TABLA
                await this.rechazarConductor(driverId, nombreConductor);
            }
        });

        // Eventos para los botones del modal
        if (this.modalApproveBtn) {
            this.modalApproveBtn.addEventListener('click', async () => {
                if (this.currentDriver) {
                    const nombreConductor = this.currentDriver.getNombreCompleto();
                    await this.aprobarConductor(this.currentDriver.driverId, nombreConductor);
                }
            });
        }

        if (this.modalRejectBtn) {
            this.modalRejectBtn.addEventListener('click', async () => {
                if (this.currentDriver) {
                    const nombreConductor = this.currentDriver.getNombreCompleto();
                    await this.rechazarConductor(this.currentDriver.driverId, nombreConductor);
                }
            });
        }

        // SIDEBAR TOGGLE - REMOVIDO (ahora manejado por TopBarController)
        // if (this.openSidebarBtn) {
        //     this.openSidebarBtn.addEventListener('click', () => this.toggleSidebar());
        // }
        // if (this.closeSidebarBtn) {
        //     this.closeSidebarBtn.addEventListener('click', () => this.toggleSidebar());
        // }

        // Eventos de paginación
        if (this.prevPageBtn) {
            this.prevPageBtn.addEventListener('click', () => {
                if (this.currentPage > 0) {
                    this.currentPage--;
                    this.renderDriversPage();
                }
            });
        }
        if (this.nextPageBtn) {
            this.nextPageBtn.addEventListener('click', () => {
                if (this.currentPage < this.totalPages - 1) {
                    this.currentPage++;
                    this.renderDriversPage();
                }
            });
        }

        // Botón de actualizar datos
        const refreshBtn = document.getElementById('refresh-drivers-btn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', async () => {
                console.log('🔄 Actualizando lista de conductores...');
                this.showLoading(true);
                
                try {
                    const conductoresData = await this.conductorService.obtenerConductoresPendientes();
                    this.allDrivers = conductoresData.map(data => Conductor.fromApiData(data));
                    
                    this.totalPages = Math.ceil(this.allDrivers.length / this.perPage);
                    this.currentPage = 0;
                    this.renderDriversPage();
                    this.showLoading(false);
                    this.showToast('Lista de conductores actualizada', 'success');
                } catch (error) {
                    console.error('Error al actualizar conductores:', error);
                    this.showToast('Error al actualizar la lista de conductores', 'error');
                    this.showLoading(false);
                }
            });
        }
    }

    // Método para mostrar los detalles de un conductor en el modal - SOLO INFORMACIÓN
    showDriverDetails(conductor) {
        console.log('📋 Mostrando detalles del conductor:', conductor);
        
        // Si recibe datos planos, convertir a clase
        if (!(conductor instanceof Conductor)) {
            conductor = Conductor.fromApiData(conductor);
        }
        
        // Almacenar el conductor actual para las acciones del modal
        this.currentDriver = conductor;
        
        console.log('🔍 Elementos del modal encontrados:', {
            modal: !!this.modal,
            modalName: !!this.modalName,
            modalDni: !!this.modalDni,
            modalPlaca: !!this.modalPlaca,
            modalContacto: !!this.modalContacto,
            modalEstado: !!this.modalEstado,
            modalAvatar: !!this.modalAvatar
        });
        
        // Llenar los campos básicos
        if (this.modalName) this.modalName.textContent = conductor.getNombreCompleto();
        if (this.modalDni) this.modalDni.textContent = conductor.documentValue || 'No disponible';
        if (this.modalPlaca) this.modalPlaca.textContent = conductor.plateValue || 'No disponible';
        if (this.modalContacto) this.modalContacto.textContent = conductor.contactValue || 'No disponible';
        if (this.modalEstado) this.modalEstado.textContent = conductor.tucStatus || 'PENDIENTE';
        
        // Llenar el badge de estado
        if (this.modalEstadoBadge) {
            this.modalEstadoBadge.textContent = conductor.tucStatus || 'PENDIENTE';
            // Cambiar clase según el estado
            this.modalEstadoBadge.className = 'badge';
            switch (conductor.tucStatus) {
                case 'APROBADO':
                    this.modalEstadoBadge.classList.add('bg-success-lt');
                    break;
                case 'RECHAZADO':
                    this.modalEstadoBadge.classList.add('bg-danger-lt');
                    break;
                case 'VENCIDO':
                    this.modalEstadoBadge.classList.add('bg-warning-lt');
                    break;
                default:
                    this.modalEstadoBadge.classList.add('bg-azure-lt');
            }
        }
        
        // Llenar campos adicionales del TUC
        if (this.modalRuc) this.modalRuc.textContent = conductor.rucCompany || 'No disponible';
        if (this.modalTucIssue) this.modalTucIssue.textContent = conductor.getFechaEmisionFormateada();
        if (this.modalTucExpiration) this.modalTucExpiration.textContent = conductor.getFechaVencimientoFormateada();
        if (this.modalTucModality) this.modalTucModality.textContent = conductor.tucModality || 'No disponible';
        if (this.modalTucType) this.modalTucType.textContent = conductor.tucType || 'No disponible';
        
        // Configurar la imagen del avatar
        if (this.modalAvatar) {
            const imageUrl = conductor.getImagenUrl();
            this.modalAvatar.src = imageUrl;
            this.modalAvatar.onerror = () => {
                this.modalAvatar.onerror = null;
                this.modalAvatar.src = '../../assets/Recourse/Imagenes/register_foto_defecto.png';
            };
            
            // También actualizar el fondo del avatar
            const avatarContainer = this.modalAvatar.closest('.avatar');
            if (avatarContainer) {
                avatarContainer.style.backgroundImage = `url('${imageUrl}')`;
            }
        }
        
        console.log('🎯 Abriendo modal de Tabler...');
        if (this.modal) {
            this.modal.show();
            console.log('✅ Modal abierto exitosamente');
        } else {
            console.error('❌ No se encontró el modal de Tabler inicializado');
        }
    }    // Método para aprobar conductor - CON CONFIRMACIÓN
    async aprobarConductor(driverId, nombreConductor = 'el conductor') {
        try {
            console.log('✅ Aprobando conductor:', nombreConductor, 'ID:', driverId);
            
            const confirmed = await this.showConfirmation({
                title: 'Aprobar Conductor',
                message: `¿Está seguro de que desea aprobar a "${nombreConductor}"?`,
                icon: 'fas fa-check-circle',
                iconClass: 'success',
                confirmText: 'Aprobar',
                confirmClass: 'success'
            });
            
            if (!confirmed) return;
            
            this.showLoading(true);
            
            const response = await this.conductorService.aprobarConductor(driverId);
            
            if (response.success) {
                this.showToast('Conductor aprobado con éxito', 'success');
                
                // Solo cerrar modal si está abierto
                if (this.modal && this.modalElement.classList.contains('show')) {
                    this.modal.hide();
                }
                
                // Recargar la lista de conductores
                await this.recargarConductores();
            } else {
                this.showToast(response.message || 'Error al aprobar conductor', 'error');
            }
            
        } catch (error) {
            console.error('Error al aprobar conductor:', error);
            this.showToast('Error al aprobar conductor: ' + error.message, 'error');
        } finally {
            this.showLoading(false);
        }
    }    // Método para rechazar conductor - CON CONFIRMACIÓN
    async rechazarConductor(driverId, nombreConductor = 'el conductor') {
        try {
            console.log('❌ Rechazando conductor:', nombreConductor, 'ID:', driverId);
            
            const confirmed = await this.showConfirmation({
                title: 'Rechazar Conductor',
                message: `¿Está seguro de que desea rechazar a "${nombreConductor}"?`,
                icon: 'fas fa-times-circle',
                iconClass: 'danger',
                confirmText: 'Rechazar',
                confirmClass: 'danger'
            });
            
            if (!confirmed) return;
            
            this.showLoading(true);
            
            const response = await this.conductorService.rechazarConductor(driverId);
            
            if (response.success) {
                this.showToast('Conductor rechazado con éxito', 'success');
                
                // Solo cerrar modal si está abierto
                if (this.modal && this.modalElement.classList.contains('show')) {
                    this.modal.hide();
                }
                
                // Recargar la lista de conductores
                await this.recargarConductores();
            } else {
                this.showToast(response.message || 'Error al rechazar conductor', 'error');
            }
            
        } catch (error) {
            console.error('Error al rechazar conductor:', error);
            this.showToast('Error al rechazar conductor: ' + error.message, 'error');
        } finally {
            this.showLoading(false);
        }
    }    // Método para recargar conductores - NUEVO
    async recargarConductores() {
        try {
            this.showLoading(true);
            const conductoresData = await this.conductorService.obtenerConductoresPendientes();
            this.allDrivers = conductoresData.map(data => Conductor.fromApiData(data));
            this.totalPages = Math.ceil(this.allDrivers.length / this.perPage);
            if (this.currentPage >= this.totalPages) this.currentPage = 0;
            this.renderDriversPage();
            this.showLoading(false);
        } catch (error) {
            console.error('Error al recargar conductores:', error);
            this.showToast('Error al recargar la lista de conductores', 'error');
        }
    }

    // Método para mostrar mensajes toast (utiliza la función global)
    showToast(message, type = 'success') {
        window.showRecoveryToast(message, type);
    }

    // Método para alternar la visibilidad del sidebar - REMOVIDO (ahora manejado por SidebarController)
    // toggleSidebar() {
    //     this.sidebar.classList.toggle('active');
    //     if (window.innerWidth > 992) {
    //         this.mainContent.classList.toggle('sidebar-active');
    //     }
    // }

    // Método para mostrar modal de confirmación usando Tabler
    showConfirmation(options = {}) {
        return new Promise((resolve) => {
            // Configuración por defecto
            const config = {
                title: 'Confirmar Acción',
                message: '¿Está seguro de que desea realizar esta acción?',
                icon: 'fas fa-question-circle',
                iconClass: 'warning',
                confirmText: 'Confirmar',
                cancelText: 'Cancelar',
                confirmClass: 'primary',
                ...options
            };
            
            // Configurar el modal
            this.confirmationTitle.innerHTML = `<i id="confirmation-icon" class="${config.icon} me-2 text-${config.iconClass}"></i>${config.title}`;
            this.confirmationMessage.textContent = config.message;
            
            // Configurar el icono grande del cuerpo
            const confirmationIconLarge = document.getElementById('confirmation-icon-large');
            if (confirmationIconLarge) {
                confirmationIconLarge.className = `${config.icon} text-${config.iconClass}`;
                const avatarContainer = confirmationIconLarge.closest('.avatar');
                if (avatarContainer) {
                    avatarContainer.className = `avatar avatar-lg bg-${config.iconClass}-lt`;
                }
            }
            
            // Configurar botones
            this.confirmationConfirm.innerHTML = `<i class="fas fa-check me-2"></i>${config.confirmText}`;
            this.confirmationCancel.innerHTML = `<i class="fas fa-times me-2"></i>${config.cancelText}`;
            
            // Aplicar clase al botón de confirmar
            this.confirmationConfirm.className = `btn btn-${config.confirmClass}`;
            
            // Limpiar event listeners anteriores
            const newCancelBtn = this.confirmationCancel.cloneNode(true);
            const newConfirmBtn = this.confirmationConfirm.cloneNode(true);
            this.confirmationCancel.parentNode.replaceChild(newCancelBtn, this.confirmationCancel);
            this.confirmationConfirm.parentNode.replaceChild(newConfirmBtn, this.confirmationConfirm);
            
            // Actualizar referencias
            this.confirmationCancel = newCancelBtn;
            this.confirmationConfirm = newConfirmBtn;
            
            // Event listeners
            const handleCancel = () => {
                this.confirmationModalInstance.hide();
                resolve(false);
            };
            
            const handleConfirm = () => {
                this.confirmationModalInstance.hide();
                resolve(true);
            };
            
            // Agregar event listeners
            this.confirmationCancel.addEventListener('click', handleCancel);
            this.confirmationConfirm.addEventListener('click', handleConfirm);
            
            // Mostrar el modal
            this.confirmationModalInstance.show();
            
            // Enfocar el botón de cancelar por defecto
            setTimeout(() => {
                this.confirmationCancel.focus();
            }, 300);
        });
    }
}

// Exportar la clase para que esté disponible en otros archivos
if (typeof module !== 'undefined' && module.exports) {    module.exports = AdmissionControlController;
} else {
    window.AdmissionControlController = AdmissionControlController;
}

// Verificar y mostrar datos de autenticación en la consola
console.log("=== DATOS DE AUTENTICACIÓN ===");
console.log("isLoggedIn:", sessionStorage.getItem("isLoggedIn"));
console.log("Token:", sessionStorage.getItem("authToken"));
console.log("userId:", sessionStorage.getItem("userId"));
console.log("documentValue:", sessionStorage.getItem("documentValue"));
console.log("firstName:", sessionStorage.getItem("firstName"));
console.log("lastName:", sessionStorage.getItem("lastName"));
console.log("userFullName:", sessionStorage.getItem("userFullName"));
console.log("userRating:", sessionStorage.getItem("userRating"));
console.log("Roles:", sessionStorage.getItem("userRoles"));
console.log("Permisos:", sessionStorage.getItem("userPermissions"));
console.log("Disponibilidad:", sessionStorage.getItem("userAvailability"));
console.log("============================");

// Función de depuración para el botón X del modal
function debugCloseButton() {
    const modal = document.getElementById('driver-modal');
    const closeBtn = document.querySelector('.close-modal');
    
    console.log('Modal element:', modal);
    console.log('Close button element:', closeBtn);
    
    if (closeBtn) {
        const styles = window.getComputedStyle(closeBtn);
        console.log('Close button computed styles:', {
            display: styles.display,
            visibility: styles.visibility,
            opacity: styles.opacity,
            zIndex: styles.zIndex,
            position: styles.position,
            backgroundColor: styles.backgroundColor,
            border: styles.border,
            right: styles.right,
            top: styles.top
        });
        
        console.log('Close button should now be positioned correctly within the header');
    } else {
        console.error('Close button not found');
    }
}

// Ejecutar después de que se cargue la página
window.addEventListener('DOMContentLoaded', () => {
    setTimeout(debugCloseButton, 1000);
});