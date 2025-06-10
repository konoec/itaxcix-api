/**
 * Verifica la autenticación del usuario mediante tokens y estado de sesión.
 * Si no está autenticado, redirige al login.
 * @returns {boolean} true si el usuario está autenticado, false en caso contrario
 */
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
        clearSession();
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
            clearSession(); // Limpia la sesión (debe estar definida en session-cleaner.js)
            window.location.href = "../../index.html"; // Redirige al login
        });
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
    setupLogoutButton
};