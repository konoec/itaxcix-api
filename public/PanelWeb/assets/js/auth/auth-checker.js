/**
 * Verifica la autenticación del usuario mediante tokens y estado de sesión.
 * Si no está autenticado, redirige al login.
 * @returns {boolean} true si el usuario está autenticado, false en caso contrario
 */

/**
 * Limpia la sesión de forma específica y segura
 */
function cleanSession() {
    // Limpiar datos específicos de autenticación
    sessionStorage.removeItem("isLoggedIn");
    sessionStorage.removeItem("loginTime");
    sessionStorage.removeItem("authToken");
    sessionStorage.removeItem("userId");
    sessionStorage.removeItem("documentValue");
    sessionStorage.removeItem("userRoles");
    sessionStorage.removeItem("userPermissions");
    sessionStorage.removeItem("userAvailability");
    sessionStorage.removeItem("firstName");
    sessionStorage.removeItem("lastName");
    sessionStorage.removeItem("userFullName");
    sessionStorage.removeItem("userRating");
}

function checkAuthentication() {
    // Verifica si el usuario está logueado y si existe un token en sessionStorage
    const isLoggedIn = sessionStorage.getItem("isLoggedIn") === "true";
    const token = sessionStorage.getItem("authToken");
    
    // Si no está logueado o no hay token, redirige al login
    if (!isLoggedIn || !token) {
        console.log("Usuario no autenticado o token no encontrado, redirigiendo al login...");
        window.location.href = "../../index.html";
        return false;
    }
    // Si pasa la validación, retorna true
    return true;
}

/**
 * Actualiza la información del usuario en la interfaz.
 * Muestra el nombre completo del usuario si está disponible, 
 * caso contrario muestra el número de documento, y como último recurso el ID de usuario.
 */
function updateUserDisplay() {
    // Obtiene la información del usuario desde sessionStorage
    const fullName = sessionStorage.getItem("userFullName");
    const firstName = sessionStorage.getItem("firstName");
    const lastName = sessionStorage.getItem("lastName");
    const documentValue = sessionStorage.getItem("documentValue");
    const userId = sessionStorage.getItem("userId");
    
    // Obtiene el elemento donde se muestra el usuario
    const userDisplay = document.getElementById("user-display");

    if (userDisplay) {
        // Prioridad 1: Nombre completo
        if (fullName && fullName.trim() !== "") {
            userDisplay.textContent = fullName.trim();
        }
        // Prioridad 2: Nombre y apellido por separado
        else if (firstName || lastName) {
            const displayName = `${firstName || ""} ${lastName || ""}`.trim();
            userDisplay.textContent = displayName || "Admin";
        }
        // Prioridad 3: Número de documento
        else if (documentValue) {
            userDisplay.textContent = `Doc. ${documentValue}`;
        }
        // Prioridad 4: ID de usuario como último recurso
        else if (userId) {
            userDisplay.textContent = `Usuario ${userId}`;
        }
        // Fallback: Texto por defecto
        else {
            userDisplay.textContent = "Admin";
        }
    }
}

/**
 * Verifica si la sesión ha expirado basado en el tiempo de inicio.
 * La sesión expira después de 30 minutos de inactividad.
 * Si expira, limpia la sesión y redirige al login.
 * @returns {boolean} true si la sesión es válida, false si ha expirado
 */
function checkTokenExpiration() {
    // Obtiene el tiempo de inicio de sesión
    const loginTime = sessionStorage.getItem("loginTime");
    if (!loginTime) return false;
    
    // Calcula el tiempo transcurrido desde el inicio de sesión
    const now = Date.now();
    const sessionTime = parseInt(loginTime);
    const sessionDuration = 30 * 60 * 1000; // 30 minutos en milisegundos
    
    // Si la sesión ha expirado, limpia y redirige
    if (now - sessionTime > sessionDuration) {
        console.log("Sesión expirada, redirigiendo al login...");
        cleanSession();
        window.location.href = "../../index.html";
        return false;
    }
    // Si la sesión sigue activa, retorna true
    return true;
}

/**
 * Configura el evento de cierre de sesión en el botón de logout.
 * Al hacer click, limpia la sesión y redirige al login.
 */
function setupLogoutButton() {
    // Busca el botón de logout en la interfaz
    const logoutBtn = document.querySelector('.logout-btn');
    if (logoutBtn) {
        logoutBtn.addEventListener("click", function(e) {
            e.preventDefault(); // Previene el comportamiento por defecto
            cleanSession(); // Usar función de limpieza específica
            window.location.href = "../../index.html"; // Redirige al login
        });
    }
}

/**
 * Verificador automático de estado de usuario
 * Verifica periódicamente si el usuario web sigue activo en el sistema
 */
class UserStatusMonitor {
    constructor() {
        this.intervalId = null;
        this.checkInterval = 2 * 60 * 1000; // 2 minutos (más frecuente)
        this.isMonitoring = false;
    }

    /**
     * Inicia el monitoreo automático del estado del usuario
     */
    startMonitoring() {
        if (this.isMonitoring) {
            console.log('🔍 UserStatusMonitor: Ya está activo');
            return;
        }

        // Solo monitorear usuarios web
        const userRoles = sessionStorage.getItem('userRoles');
        if (!userRoles) {
            console.log('🔍 UserStatusMonitor: No hay roles de usuario en sesión');
            return;
        }

        try {
            const roles = JSON.parse(userRoles);
            const hasWebRoles = roles.some(role => role.web === true);
            
            if (!hasWebRoles) {
                console.log('🔍 UserStatusMonitor: Usuario no tiene roles web, no es necesario monitorear');
                return;
            }
        } catch (e) {
            console.log('🔍 UserStatusMonitor: Error al parsear roles');
            return;
        }

        console.log('🔍 UserStatusMonitor: Iniciando monitoreo para usuario web');
        this.isMonitoring = true;

        // Verificación inicial comentada para evitar logout inmediato al cargar la página
        // Solo usar verificación periódica y on-demand
        // this.checkUserStatus();

        // Configurar verificación periódica
        this.intervalId = setInterval(() => {
            this.checkUserStatus();
        }, this.checkInterval);
    }

    /**
     * Detiene el monitoreo
     */
    stopMonitoring() {
        if (this.intervalId) {
            clearInterval(this.intervalId);
            this.intervalId = null;
        }
        this.isMonitoring = false;
        console.log('🔍 UserStatusMonitor: Monitoreo detenido');
    }

    /**
     * Verifica el estado actual del usuario
     */
    async checkUserStatus() {
        try {
            // Verificar que UserService esté disponible
            if (typeof window.UserService === 'undefined') {
                console.log('🔍 UserStatusMonitor: UserService no disponible, saltando verificación');
                return;
            }

            console.log('🔍 UserStatusMonitor: Verificación periódica del estado del usuario...');
            
            // Usar verificación ligera para monitoreo periódico (solo estado activo, no roles)
            const statusResult = await window.UserService.getCurrentUserStatusLight();

            if (statusResult.needsLogin) {
                console.log('🚫 UserStatusMonitor: Usuario requiere login');
                this.handleUserDeactivated(statusResult.message);
            } else if (statusResult.success) {
                console.log('✅ UserStatusMonitor: Usuario activo');
            } else {
                console.log('⚠️ UserStatusMonitor: Error en verificación:', statusResult.message);
            }

        } catch (error) {
            console.error('❌ UserStatusMonitor: Error al verificar estado:', error);
        }
    }

    /**
     * Fuerza una verificación inmediata del estado del usuario
     * Método público para uso externo
     */
    async forceCheck() {
        console.log('🔍 UserStatusMonitor: Forzando verificación inmediata...');
        await this.checkUserStatus();
    }

    /**
     * Maneja el caso cuando el usuario ha sido desactivado
     */
    handleUserDeactivated(message) {
        console.log('🚫 UserStatusMonitor: Usuario desactivado, cerrando sesión...');
        
        this.stopMonitoring();
        
        // Mostrar mensaje al usuario con color celeste específico para desactivación
        if (typeof window.showToast === 'function') {
            window.showToast('Tu cuenta ha sido desactivada. Serás redirigido al login.', 'deactivated');
        } else if (typeof window.GlobalToast !== 'undefined' && window.GlobalToast.show) {
            window.GlobalToast.show('Tu cuenta ha sido desactivada. Serás redirigido al login.', 'deactivated');
        } else {
            alert('Tu cuenta ha sido desactivada. Serás redirigido al login.');
        }

        // Esperar un momento para que se muestre el mensaje
        setTimeout(() => {
            // Limpiar sesión
            if (typeof cleanSession === 'function') {
                cleanSession();
            }
            
            // Redirigir al login
            window.location.href = "../../index.html";
        }, 2000);
    }
}

// Crear instancia global del monitor
const userStatusMonitor = new UserStatusMonitor();

/**
 * Función de utilidad para inicializar automáticamente el monitoreo
 * Se puede llamar desde cualquier página web
 */
function initUserStatusMonitoring() {
    if (window.authChecker && window.authChecker.userStatusMonitor) {
        console.log('🔍 Iniciando monitoreo automático de estado de usuario...');
        window.authChecker.userStatusMonitor.startMonitoring();
        return true;
    } else {
        console.log('⚠️ Monitor de estado de usuario no disponible');
        return false;
    }
}

/**
 * Exporta las funciones como un objeto global para uso en otros archivos.
 * Permite acceder a las funciones desde cualquier parte de la aplicación.
 */
window.authChecker = {
    checkAuthentication,
    updateUserDisplay,
    checkTokenExpiration,
    setupLogoutButton,
    userStatusMonitor, // Añadir el monitor al objeto global
    initUserStatusMonitoring // Añadir función de utilidad
};

// Exportar también la función de utilidad globalmente
window.initUserStatusMonitoring = initUserStatusMonitoring;
