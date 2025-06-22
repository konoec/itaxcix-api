/**
 * Clase para proteger rutas privadas, verificar permisos y limpiar sesión.
 * Úsala en todas las páginas privadas del sistema.
 */
class RouteGuard {
    /**
     * @param {string} loginPath - Ruta a la página de login (ej: "../../index.html")
     */
    constructor(loginPath = "../../index.html") {
        this.loginPath = loginPath;
        this.permissionsService = null;
        this.isInitialized = false;
        this.init();
    }

    // Inicializa la protección de ruta
    async init() {
        this.checkAuthOnLoad();
        this.setupPopStateListener();
        
        // Cargar PermissionsService si está disponible
        await this.loadPermissionsService();
    }

    /**
     * Carga el PermissionsService de forma asíncrona
     */
    async loadPermissionsService() {
        try {
            // Si ya existe globalmente, usarlo
            if (typeof window.PermissionsService !== 'undefined') {
                this.permissionsService = window.PermissionsService;
                this.isInitialized = true;
                console.log('✅ PermissionsService encontrado');
                return;
            }

            // Intentar cargar dinámicamente
            const baseUrl = window.location.hostname.includes('github.io') ? '/PanelWeb' : '';
            const script = document.createElement('script');
            script.src = `${baseUrl}/assets/js/auth/permissions.js`;
            
            await new Promise((resolve, reject) => {
                script.onload = () => {
                    this.permissionsService = window.PermissionsService;
                    this.isInitialized = true;
                    console.log('✅ PermissionsService cargado dinámicamente');
                    resolve();
                };
                script.onerror = () => {
                    console.warn('⚠️ No se pudo cargar PermissionsService');
                    resolve(); // No es crítico, continuar sin permisos
                };
                document.head.appendChild(script);
            });
            
        } catch (error) {
            console.warn('⚠️ Error cargando PermissionsService:', error);
        }
    }

    // Verifica autenticación al cargar la página
    checkAuthOnLoad() {
        if (!this.isAuthenticated()) {
            this.redirectToLogin();
            return;
        }

        // Verificar permisos si está disponible
        this.checkPermissions();
    }    /**
     * Verifica permisos para la ruta actual
     */
    checkPermissions() {
        if (!this.permissionsService) {
            console.log('ℹ️ Sistema de permisos no disponible, omitiendo validación');
            return;
        }

        try {
            // Solo verificar ruta si estamos en una página de módulo específico
            const currentPath = window.location.pathname;
            console.log(`🔍 Verificando permisos para: ${currentPath}`);
            
            // Inicializar sistema de permisos (esto configura el menú)
            this.permissionsService.initializePermissions();
            
        } catch (error) {
            console.error('❌ Error verificando permisos:', error);
            // No bloquear en caso de error, solo loggear
        }
    }

    // Listener para navegación hacia atrás/adelante
    setupPopStateListener() {
        window.addEventListener("popstate", () => {
            if (!this.isAuthenticated()) {
                this.redirectToLogin();
            } else {
                this.checkPermissions();
            }
        });
        
        // También para pageshow (cuando vuelve del caché)
        window.addEventListener("pageshow", (event) => {
            // Si la página viene del caché o la sesión no es válida, redirige
            if (event.persisted || !this.isAuthenticated()) {
                // Forzar recarga desde el servidor y redirigir
                window.location.replace(this.loginPath);
            } else {
                this.checkPermissions();
            }
        });
    }

    // Verifica si hay sesión activa (ajusta según tu lógica)
    isAuthenticated() {
        const hasToken = !!sessionStorage.getItem("authToken");
        const hasLoginTime = !!sessionStorage.getItem("loginTime");
        const isLoggedIn = sessionStorage.getItem("isLoggedIn") === "true";
        
        return hasToken && hasLoginTime && isLoggedIn;
    }

    /**
     * Verifica si el usuario tiene un permiso específico
     * @param {string} permission - Permiso a verificar
     * @returns {boolean}
     */
    hasPermission(permission) {
        if (!this.permissionsService) {
            console.warn('⚠️ PermissionsService no disponible para verificar permiso');
            return true; // Por defecto permitir si no hay sistema de permisos
        }
        
        return this.permissionsService.hasPermission(permission);
    }

    /**
     * Oculta elementos del DOM basado en permisos
     * @param {string} permission - Permiso requerido
     * @param {string} selector - Selector CSS del elemento a ocultar
     */
    hideElementByPermission(permission, selector) {
        if (!this.hasPermission(permission)) {
            const elements = document.querySelectorAll(selector);
            elements.forEach(element => {
                element.style.display = 'none';
                element.classList.add('permission-hidden');
            });
            
            console.log(`🔒 Elemento oculto por permisos: ${selector} (requiere: ${permission})`);
        }
    }

    /**
     * Muestra elementos del DOM basado en permisos
     * @param {string} permission - Permiso requerido
     * @param {string} selector - Selector CSS del elemento a mostrar
     */
    showElementByPermission(permission, selector) {
        if (this.hasPermission(permission)) {
            const elements = document.querySelectorAll(selector);
            elements.forEach(element => {
                element.style.display = '';
                element.classList.remove('permission-hidden');
            });
            
            console.log(`✅ Elemento mostrado por permisos: ${selector} (tiene: ${permission})`);
        }
    }

    // Limpia la sesión y redirige al login
    logout() {
        console.log('🚪 Cerrando sesión...');
        sessionStorage.clear();
        this.redirectToLogin();
    }

    // Redirige al login
    redirectToLogin() {
        console.log('🔄 Redirigiendo al login:', this.loginPath);
        window.location.replace(this.loginPath);
    }

    /**
     * Método estático para proteger páginas fácilmente
     * @param {string} loginPath - Ruta al login
     * @returns {RouteGuard} - Instancia del guard
     */
    static protect(loginPath = "../../index.html") {
        return new RouteGuard(loginPath);
    }

    /**
     * Debug: Muestra información del guard
     */
    debug() {
        console.log('🐛 DEBUG - RouteGuard Info:');
        console.log('🔒 Autenticado:', this.isAuthenticated());
        console.log('🛡️ Permisos inicializados:', this.isInitialized);
        console.log('📍 Ruta actual:', window.location.pathname);
        console.log('🚪 Ruta de login:', this.loginPath);
        
        if (this.permissionsService) {
            console.log('🔐 PermissionsService disponible: Sí');
            this.permissionsService.debugPermissions();
        } else {
            console.log('🔐 PermissionsService disponible: No');
        }
    }
}

// Exponer la clase globalmente
window.RouteGuard = RouteGuard;
