// Este comentario indica que aquí se importarían las clases necesarias si estuviéramos usando un sistema de módulos
// En este caso, está comentado porque estamos usando carga de scripts directa en el HTML
// const ConductorService = require('../../api/services/conductor-service');

// Clase principal que controla toda la interfaz de usuario del panel de administración
class UIController {
    constructor() {
        // Instancia del servicio de conductores que maneja la comunicación con la API real
        this.conductorService = new ConductorService();

        // Referencias a elementos del DOM
        this.driversList = document.getElementById('drivers-list');
        this.loadingIndicator = document.getElementById('loading-indicator');
        this.modal = document.getElementById('driver-modal');
        this.closeModal = document.querySelector('.close-modal');
        this.toast = document.getElementById('toast');
        this.toastMessage = document.getElementById('toast-message');
        this.modalName = document.getElementById('modal-name');
        this.modalDni = document.getElementById('modal-dni');
        this.modalPlaca = document.getElementById('modal-placa');
        this.modalContacto = document.getElementById('modal-contacto');
        this.modalEstadoTuc = document.getElementById('modal-estado');
        this.modalAvatar = document.getElementById('modal-avatar');
        // Eliminamos las referencias a modalAccept y modalReject
        this.sidebar = document.getElementById('sidebar');
        this.openSidebarBtn = document.getElementById('open-sidebar');
        this.closeSidebarBtn = document.getElementById('close-sidebar');
        this.mainContent = document.querySelector('.main-content');
    }    // Método principal que inicializa la aplicación - CORREGIDO
    // Es async porque realiza operaciones asíncronas (como cargar datos de la API)
    async init() {
        try {
            this.showLoading(true);

            const conductoresData = await this.conductorService.obtenerConductoresPendientes(0, 10);
            console.log('Respuesta de la API conductores:', conductoresData);

            // CONVERTIR A INSTANCIAS DE LA CLASE CONDUCTOR
            const conductores = conductoresData.map(data => Conductor.fromApiData(data));

            // Verificar si hay conductores pendientes
            if (conductores.length === 0) {
                // No hay conductores pendientes - mostrar mensaje
                this.driversList.innerHTML = `
                    <tr>
                        <td colspan="5" class="no-data">
                            <i class="fas fa-info-circle"></i>
                            Aún no hay solicitudes de conductores pendientes por revisar.
                        </td>
                    </tr>
                `;
            } else {
                // Renderizar conductores existentes
                conductores.forEach(conductor => {
                    this.renderConductor(conductor);
                });
            }

            this.initializeEvents();
            this.showLoading(false);
        } catch (error) {
            console.error('Error al inicializar la aplicación:', error);
            this.showToast('Error al cargar los datos de conductores');
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

        row.innerHTML = `
            <td class="driver-name">
                <div class="avatar">
                    <img src="${conductor.getImagenUrl()}" 
                         alt="Foto de perfil"
                         onerror="this.onerror=null; this.src='../../assets/Recourse/Imagenes/register_foto_defecto.png';">
                </div>
                <span>${conductor.getNombreCompleto()}</span>
            </td>
            <td>${conductor.documentValue || 'N/A'}</td>
            <td>${conductor.plateValue || 'N/A'}</td>
            <td>${conductor.contactValue || 'N/A'}</td>
            <td class="actions">
                <button class="btn btn-details" data-id="${conductor.driverId}" title="Ver detalles">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-approve" data-id="${conductor.driverId}" title="Aprobar conductor">
                    <i class="fas fa-check"></i>
                </button>
                <button class="btn btn-reject" data-id="${conductor.driverId}" title="Rechazar conductor">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        `;
        
        this.driversList.appendChild(row);
    }

    // Método para inicializar eventos - LIMPIO
    initializeEvents() {
        // DELEGACIÓN DE EVENTOS para todos los botones de la tabla
        this.driversList.addEventListener('click', async (e) => {
            const button = e.target.closest('button');
            if (!button) return;
            
            const driverId = parseInt(button.dataset.id);
            
            if (button.classList.contains('btn-details')) {
                // BOTÓN DETALLES - SOLO MOSTRAR INFORMACIÓN
                try {
                    const conductorDetallado = await this.conductorService.obtenerConductorPendientePorId(driverId);
                    if (conductorDetallado) {
                        this.showDriverDetails(conductorDetallado);
                    }
                } catch (error) {
                    console.error('Error al obtener detalles:', error);
                    this.showToast('Error al cargar los detalles del conductor', 'error');
                }
            } else if (button.classList.contains('btn-approve')) {
                // BOTÓN APROBAR - SOLO DESDE LA TABLA
                await this.aprobarConductor(driverId);
            } else if (button.classList.contains('btn-reject')) {
                // BOTÓN RECHAZAR - SOLO DESDE LA TABLA
                await this.rechazarConductor(driverId);
            }
        });

        // CERRAR MODAL
        if (this.closeModal) {
            this.closeModal.addEventListener('click', () => {
                this.modal.style.display = 'none';
            });
        }

        // CERRAR MODAL AL HACER CLIC FUERA
        window.addEventListener('click', (event) => {
            if (event.target === this.modal) {
                this.modal.style.display = 'none';
            }
        });

        // SIDEBAR TOGGLE
        if (this.openSidebarBtn) {
            this.openSidebarBtn.addEventListener('click', () => this.toggleSidebar());
        }
        if (this.closeSidebarBtn) {
            this.closeSidebarBtn.addEventListener('click', () => this.toggleSidebar());
        }
    }

    // Método para mostrar los detalles de un conductor en el modal - SOLO INFORMACIÓN
    showDriverDetails(conductor) {
        // Si recibe datos planos, convertir a clase
        if (!(conductor instanceof Conductor)) {
            conductor = Conductor.fromApiData(conductor);
        }
        
        // Ya no necesitamos currentConductorId
        // this.currentConductorId = conductor.driverId;
        
        // Usar métodos de la clase
        this.modalName.textContent = conductor.getNombreCompleto();
        this.modalDni.textContent = conductor.documentValue || 'No disponible';
        this.modalPlaca.textContent = conductor.plateValue || 'No disponible';
        this.modalContacto.textContent = conductor.contactValue || 'No disponible';
        this.modalEstadoTuc.textContent = conductor.tucStatus || 'PENDIENTE';
        
        // Nuevos campos con métodos de la clase
        document.getElementById('modal-ruc').textContent = conductor.rucCompany || 'No disponible';
        document.getElementById('modal-tuc-issue').textContent = conductor.getFechaEmisionFormateada();
        document.getElementById('modal-tuc-expiration').textContent = conductor.getFechaVencimientoFormateada();
        document.getElementById('modal-tuc-modality').textContent = conductor.tucModality || 'No disponible';
        document.getElementById('modal-tuc-type').textContent = conductor.tucType || 'No disponible';
        
        // Usar método para imagen
        this.modalAvatar.src = conductor.getImagenUrl();
        this.modalAvatar.onerror = () => {
            this.modalAvatar.onerror = null;
            this.modalAvatar.src = conductor.getImagenUrl();
        };
        
        this.modal.style.display = 'block';
    }

    // Método para aprobar conductor - NUEVO
    async aprobarConductor(driverId) {
        try {
            this.showLoading(true);
            
            const response = await this.conductorService.aprobarConductor(driverId);
            
            if (response.success) {
                this.showToast('Conductor aprobado con éxito', 'success');
                this.modal.style.display = 'none';
                
                // Recargar la lista de conductores
                await this.recargarConductores();
            } else {
                this.showToast(response.message || 'Error al aprobar conductor', 'error');
            }
            
            this.showLoading(false);
        } catch (error) {
            console.error('Error al aprobar conductor:', error);
            this.showToast('Error al aprobar conductor: ' + error.message, 'error');
            this.showLoading(false);
        }
    }

    // Método para rechazar conductor - RESTAURADO
    async rechazarConductor(driverId) {
        try {
            // Confirmar acción
            if (!confirm('¿Está seguro de que desea rechazar este conductor?')) {
                return;
            }
            
            this.showLoading(true);
            
            const response = await this.conductorService.rechazarConductor(driverId);
            
            if (response.success) {
                this.showToast('Conductor rechazado con éxito', 'success');
                this.modal.style.display = 'none';
                
                // Recargar la lista de conductores
                await this.recargarConductores();
            } else {
                this.showToast(response.message || 'Error al rechazar conductor', 'error');
            }
            
            this.showLoading(false);
        } catch (error) {
            console.error('Error al rechazar conductor:', error);
            this.showToast('Error al rechazar conductor: ' + error.message, 'error');
            this.showLoading(false);
        }
    }    // Método para recargar conductores - NUEVO
    async recargarConductores() {
        try {
            this.driversList.innerHTML = '';
            const conductoresData = await this.conductorService.obtenerConductoresPendientes(0, 10);
            const conductores = conductoresData.map(data => Conductor.fromApiData(data));
            
            // Verificar si hay conductores pendientes
            if (conductores.length === 0) {
                // No hay conductores pendientes - mostrar mensaje
                this.driversList.innerHTML = `
                    <tr>
                        <td colspan="5" class="no-data">
                            <i class="fas fa-info-circle"></i>
                            Aún no hay solicitudes de conductores pendientes por revisar.
                        </td>
                    </tr>
                `;
            } else {
                // Renderizar conductores existentes
                conductores.forEach(conductor => {
                    this.renderConductor(conductor);
                });
            }
        } catch (error) {
            console.error('Error al recargar conductores:', error);
            this.showToast('Error al recargar la lista de conductores', 'error');
        }
    }

    // Método para mostrar mensajes toast mejorado - ACTUALIZADO
    showToast(message, type = 'info') {
        if (this.toast && this.toastMessage) {
            this.toastMessage.textContent = message;
            this.toast.className = `toast show ${type}`;
            this.toast.style.display = 'block';
            
            setTimeout(() => {
                this.toast.style.display = 'none';
                this.toast.className = 'toast';
            }, 3000);
        } else {
            // Fallback si no hay toast
            alert(message);
        }
    }

    // Método para alternar la visibilidad del sidebar
    toggleSidebar() {
        this.sidebar.classList.toggle('active');
        if (window.innerWidth > 992) {
            this.mainContent.classList.toggle('sidebar-active');
        }
    }
}

// Exportar la clase para que esté disponible en otros archivos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = UIController;
} else {
    window.UIController = UIController;
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