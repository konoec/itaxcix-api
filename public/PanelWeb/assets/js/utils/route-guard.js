/**
 * Clase para proteger rutas privadas y limpiar sesión.
 * Úsala en todas las páginas privadas del sistema.
 */
class RouteGuard {
    /**
     * @param {string} loginPath - Ruta a la página de login (ej: "../../index.html")
     */
    constructor(loginPath = "../../index.html") {
        this.loginPath = loginPath;
        this.init();
    }

    // Inicializa la protección de ruta
    init() {
        this.checkAuthOnLoad();
        this.setupPopStateListener();
    }

    // Verifica autenticación al cargar la página
    checkAuthOnLoad() {
        if (!this.isAuthenticated()) {
            this.redirectToLogin();
        }
    }

    // Listener para navegación hacia atrás/adelante
    setupPopStateListener() {
        window.addEventListener("popstate", () => {
            if (!this.isAuthenticated()) {
                this.redirectToLogin();
            }
        });
        // También para pageshow (cuando vuelve del caché)
        window.addEventListener("pageshow", (event) => {
            // Si la página viene del caché o la sesión no es válida, redirige
            if (event.persisted || !this.isAuthenticated()) {
                // Forzar recarga desde el servidor y redirigir
                window.location.replace(this.loginPath);
            }
        });
    }

    // Verifica si hay sesión activa (ajusta según tu lógica)
    isAuthenticated() {
        return !!sessionStorage.getItem("authToken") && !!sessionStorage.getItem("loginTime");
    }

    // Limpia la sesión y redirige al login
    logout() {
        sessionStorage.clear();
        this.redirectToLogin();
    }

    // Redirige al login
    redirectToLogin() {
        window.location.replace(this.loginPath);
    }
}

// Exponer la clase globalmente
window.RouteGuard = RouteGuard;