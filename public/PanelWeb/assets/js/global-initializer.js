/**
 * Inicializador Global del Sistema
 * Este archivo se encarga de inicializar funcionalidades globales que deben ejecutarse en todas las páginas
 */

/**
 * Sistema Global de Monitoreo de Usuario
 * Verifica periódicamente el estado del usuario para logout automático
 */
class GlobalUserMonitor {
    constructor() {
        this.intervalId = null;
        this.checkInterval = 30 * 1000; // 30 segundos para verificación más frecuente
        this.isMonitoring = false;
        this.retryCount = 0;
        this.maxRetries = 3;
    }

    /**
     * Inicia el monitoreo global del usuario
     */
    async startMonitoring() {
        if (this.isMonitoring) {
            console.log('🌐 GlobalUserMonitor: Ya está activo');
            return;
        }

        // Verificar si tenemos una sesión válida
        const token = sessionStorage.getItem('authToken');
        const userId = sessionStorage.getItem('userId');
        
        if (!token || !userId) {
            console.log('🌐 GlobalUserMonitor: No hay sesión activa, no iniciando monitoreo');
            return;
        }

        console.log('🌐 GlobalUserMonitor: Iniciando monitoreo global del usuario');
        this.isMonitoring = true;

        // Verificación inicial después de un breve delay para que carguen los servicios
        setTimeout(() => {
            this.checkUserStatus();
        }, 5000); // 5 segundos después de inicializar

        // Configurar verificación periódica
        this.intervalId = setInterval(() => {
            this.checkUserStatus();
        }, this.checkInterval);

        // Escuchar eventos de foco para verificar cuando el usuario regresa a la ventana
        this.setupVisibilityListener();
    }

    /**
     * Configura el listener para cuando la página vuelve a ser visible
     */
    setupVisibilityListener() {
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden && this.isMonitoring) {
                console.log('🌐 GlobalUserMonitor: Página visible, verificando estado del usuario');
                this.checkUserStatus();
            }
        });

        // También verificar cuando la ventana obtiene el foco
        window.addEventListener('focus', () => {
            if (this.isMonitoring) {
                console.log('🌐 GlobalUserMonitor: Ventana enfocada, verificando estado del usuario');
                this.checkUserStatus();
            }
        });
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
        this.retryCount = 0;
        console.log('🌐 GlobalUserMonitor: Monitoreo global detenido');
    }

    /**
     * Verifica el estado actual del usuario
     */
    async checkUserStatus() {
        try {
            // Esperar a que UserService esté disponible con mayor robustez
            if (typeof window.UserService === 'undefined') {
                console.log('🌐 GlobalUserMonitor: UserService no disponible, intentando carga dinámica...');
                
                // Intentar cargar UserService dinámicamente si no está disponible
                if (this.retryCount < this.maxRetries) {
                    this.retryCount++;
                    
                    // Verificar si hay un script tag para user-service.js en la página
                    const userServiceScript = document.querySelector('script[src*="user-service.js"]');
                    if (!userServiceScript && this.retryCount === 1) {
                        console.log('🌐 GlobalUserMonitor: Intentando cargar UserService dinámicamente...');
                        await this.loadUserServiceDynamically();
                    }
                    
                    setTimeout(() => this.checkUserStatus(), 3000);
                }
                return;
            }

            this.retryCount = 0; // Reset contador si UserService está disponible

            console.log('🌐 GlobalUserMonitor: Verificando estado del usuario...');
            
            const statusResult = await window.UserService.getCurrentUserStatusLight();

            if (statusResult.needsLogin) {
                console.log('🚫 GlobalUserMonitor: Usuario requiere login -', statusResult.message);
                this.handleUserLogout(statusResult.message, statusResult.userDeactivated);
            } else if (statusResult.success) {
                console.log('✅ GlobalUserMonitor: Usuario activo');
            } else {
                console.log('⚠️ GlobalUserMonitor: Error en verificación:', statusResult.message);
            }

        } catch (error) {
            console.error('❌ GlobalUserMonitor: Error al verificar estado:', error);
        }
    }

    /**
     * Carga UserService dinámicamente si no está disponible
     */
    async loadUserServiceDynamically() {
        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = '../../assets/js/api/services/Configuration/user-service.js';
            script.onload = () => {
                console.log('✅ GlobalUserMonitor: UserService cargado dinámicamente');
                resolve();
            };
            script.onerror = () => {
                console.error('❌ GlobalUserMonitor: Error al cargar UserService dinámicamente');
                reject();
            };
            document.head.appendChild(script);
        });
    }

    /**
     * Maneja el logout del usuario
     */
    handleUserLogout(message, userDeactivated = false) {
        console.log('🚫 GlobalUserMonitor: Iniciando logout automático...');
        
        this.stopMonitoring();
        
        // Determinar el mensaje apropiado
        let toastMessage = 'Tu sesión ha expirado. Serás redirigido al login.';
        let toastType = 'warning';
        
        if (userDeactivated) {
            toastMessage = 'Tu cuenta ha sido desactivada. Serás redirigido al login.';
            toastType = 'deactivated';
        } else if (message && message.includes('inválido')) {
            toastMessage = 'Tu sesión ha expirado. Serás redirigido al login.';
            toastType = 'warning';
        }

        // Mostrar mensaje al usuario
        this.showLogoutMessage(toastMessage, toastType);

        // Esperar un momento para que se muestre el mensaje
        setTimeout(() => {
            // Limpiar sesión
            this.cleanSession();
            
            // Redirigir al login
            this.redirectToLogin();
        }, 3000);
    }

    /**
     * Muestra el mensaje de logout
     */
    showLogoutMessage(message, type) {
        // Intentar usar diferentes sistemas de toast disponibles
        if (typeof window.showToast === 'function') {
            window.showToast(message, type);
        } else if (typeof window.GlobalToast !== 'undefined' && window.GlobalToast.show) {
            window.GlobalToast.show(message, type);
        } else if (document.getElementById('toast')) {
            // Toast básico si existe
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toast-message');
            if (toastMessage) {
                toastMessage.textContent = message;
                toast.className = `toast show ${type}`;
                setTimeout(() => toast.className = 'toast', 3000);
            }
        } else {
            // Fallback a alert
            alert(message);
        }
    }

    /**
     * Limpia la sesión del usuario
     */
    cleanSession() {
        // Usar la función global de limpieza si existe
        if (typeof window.cleanSession === 'function') {
            window.cleanSession();
        } else {
            // Implementación de respaldo
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
    }

    /**
     * Redirige al login
     */
    redirectToLogin() {
        // Determinar la ruta correcta al login basándose en la ubicación actual
        const currentPath = window.location.pathname;
        let loginPath = "../../index.html";
        
        // Ajustar la ruta según la estructura de carpetas
        if (currentPath.includes('/pages/')) {
            if (currentPath.includes('/Configuration/') || 
                currentPath.includes('/Admission/') || 
                currentPath.includes('/Inicio/') ||
                currentPath.includes('/Audit/') ||
                currentPath.includes('/Master-tables/')) {
                loginPath = "../../index.html";
            }
        } else if (currentPath.includes('/assets/')) {
            loginPath = "../../index.html";
        } else {
            // Si estamos en la raíz
            loginPath = "index.html";
        }
        
        console.log('🔄 GlobalUserMonitor: Redirigiendo a login:', loginPath);
        window.location.href = loginPath;
    }
}

/**
 * Sistema Global de Inicialización
 */
class GlobalInitializer {
    constructor() {
        this.userMonitor = new GlobalUserMonitor();
        this.initialized = false;
    }

    /**
     * Inicializa todos los sistemas globales
     */
    async initialize() {
        if (this.initialized) {
            console.log('🌐 GlobalInitializer: Ya está inicializado');
            return;
        }

        console.log('🌐 GlobalInitializer: Iniciando sistemas globales...');

        // Verificar autenticación básica
        if (!this.checkBasicAuth()) {
            console.log('🌐 GlobalInitializer: No hay autenticación válida');
            return;
        }

        // Iniciar monitoreo de usuario
        await this.userMonitor.startMonitoring();

        // Configurar eventos globales
        this.setupGlobalEvents();

        this.initialized = true;
        console.log('✅ GlobalInitializer: Sistemas globales inicializados');
    }

    /**
     * Verifica autenticación básica
     */
    checkBasicAuth() {
        const isLoggedIn = sessionStorage.getItem("isLoggedIn") === "true";
        const token = sessionStorage.getItem("authToken");
        return isLoggedIn && token;
    }

    /**
     * Configura eventos globales
     */
    setupGlobalEvents() {
        // Event listener para storage changes (útil para múltiples pestañas)
        window.addEventListener('storage', (event) => {
            if (event.key === 'authToken' || event.key === 'isLoggedIn') {
                if (!event.newValue) {
                    console.log('🌐 GlobalInitializer: Sesión limpiada en otra pestaña');
                    this.userMonitor.handleUserLogout('Sesión cerrada en otra pestaña');
                }
            }
        });

        // Event listener para beforeunload (limpiar recursos)
        window.addEventListener('beforeunload', () => {
            this.userMonitor.stopMonitoring();
        });
    }

    /**
     * Fuerza una verificación del estado del usuario
     */
    async forceUserCheck() {
        await this.userMonitor.checkUserStatus();
    }

    /**
     * Detiene todos los sistemas globales
     */
    shutdown() {
        this.userMonitor.stopMonitoring();
        this.initialized = false;
        console.log('🌐 GlobalInitializer: Sistemas globales detenidos');
    }
}

// Crear instancia global
const globalInitializer = new GlobalInitializer();

// Auto-inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    // Esperar un poco para que otros scripts se carguen
    setTimeout(() => {
        globalInitializer.initialize();
    }, 1000);
});

// Exportar globalmente
window.GlobalInitializer = globalInitializer;
window.GlobalUserMonitor = GlobalUserMonitor;

// Función de utilidad para verificación manual
window.forceUserStatusCheck = () => {
    return globalInitializer.forceUserCheck();
};

/**
 * Manejo del Sidebar Responsive
 * Solo para móviles - desktop mantiene su comportamiento actual
 */
function initializeSidebarResponsive() {
    console.log('🔧 Inicializando sidebar responsive...');
    
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarClose = document.getElementById('sidebar-close');
    
    console.log('📱 Elementos encontrados:', {
        toggle: !!sidebarToggle,
        sidebar: !!sidebar,
        close: !!sidebarClose
    });
    
    if (sidebarToggle && sidebar) {
        // Evento click del botón hamburguesa
        sidebarToggle.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            
            console.log('🍔 Click en hamburguesa, ancho ventana:', window.innerWidth);
            
            if (window.innerWidth < 992) {
                // Móviles: Toggle con clase .show
                const isOpen = sidebar.classList.contains('show');
                console.log('📱 Estado actual sidebar móvil:', isOpen ? 'abierto' : 'cerrado');
                
                sidebar.classList.toggle('show');
                
                if (isOpen) {
                    console.log('➡️ Cerrando sidebar móvil (toggle)');
                } else {
                    console.log('⬅️ Abriendo sidebar móvil (toggle)');
                }
            } else {
                // Desktop: Toggle con clases .hidden y .sidebar-hidden
                const isHidden = sidebar.classList.contains('hidden');
                const pageWrapper = document.querySelector('.page-wrapper');
                
                console.log('🖥️ Estado actual sidebar desktop:', isHidden ? 'oculto' : 'visible');
                
                if (isHidden) {
                    // Mostrar sidebar
                    sidebar.classList.remove('hidden');
                    if (pageWrapper) {
                        pageWrapper.classList.remove('sidebar-hidden');
                    }
                    console.log('⬅️ Mostrando sidebar desktop');
                } else {
                    // Ocultar sidebar
                    sidebar.classList.add('hidden');
                    if (pageWrapper) {
                        pageWrapper.classList.add('sidebar-hidden');
                    }
                    console.log('➡️ Ocultando sidebar desktop');
                }
            }
        });
        
        // Evento click del botón X para cerrar
        if (sidebarClose) {
            sidebarClose.addEventListener('click', function(event) {
                event.preventDefault();
                event.stopPropagation();
                
                console.log('❌ Click en botón cerrar');
                sidebar.classList.remove('show');
            });
        }
        
        // Cerrar sidebar al hacer click fuera en móviles
        document.addEventListener('click', function(event) {
            if (window.innerWidth < 992 && 
                sidebar.classList.contains('show') && 
                !sidebar.contains(event.target) && 
                !sidebarToggle.contains(event.target)) {
                
                console.log('🖱️ Click fuera, cerrando sidebar');
                sidebar.classList.remove('show');
            }
        });
        
        // Manejar cambio de tamaño de ventana
        window.addEventListener('resize', function() {
            const pageWrapper = document.querySelector('.page-wrapper');
            
            if (window.innerWidth >= 992) {
                // Cambio a desktop: limpiar clases de móvil
                if (sidebar.classList.contains('show')) {
                    sidebar.classList.remove('show');
                    console.log('🖥️ Cambio a desktop, removiendo clase show móvil');
                }
            } else {
                // Cambio a móvil: limpiar clases de desktop
                if (sidebar.classList.contains('hidden')) {
                    sidebar.classList.remove('hidden');
                    console.log('📱 Cambio a móvil, removiendo clase hidden desktop');
                }
                if (pageWrapper && pageWrapper.classList.contains('sidebar-hidden')) {
                    pageWrapper.classList.remove('sidebar-hidden');
                    console.log('📱 Cambio a móvil, removiendo clase sidebar-hidden');
                }
            }
        });
        
        console.log('✅ Sidebar responsive inicializado correctamente');
    } else {
        console.warn('⚠️ No se encontraron los elementos del sidebar');
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOM listo, inicializando sistemas...');
    // La inicialización del sidebar ahora se maneja por SidebarController
    // initializeSidebarResponsive(); // Deshabilitado para evitar conflictos
});

// También inicializar después de que se carguen los componentes (por si acaso)
window.addEventListener('load', function() {
    console.log('🔄 Window load, sistemas globales cargados');
    // El sidebar ahora se maneja por SidebarController
    // Solo reinicializar si no se encontraron los elementos antes
    // const toggle = document.getElementById('sidebar-toggle');
    // const sidebar = document.getElementById('sidebar');
    
    // if (!toggle || !sidebar) {
    //     console.log('🔧 Elementos no encontrados antes, reintentando...');
    //     setTimeout(initializeSidebarResponsive, 500);
    // }
});

console.log('✅ Global Initializer cargado');
