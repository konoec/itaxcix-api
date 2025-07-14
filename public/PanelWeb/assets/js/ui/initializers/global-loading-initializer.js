/**
 * Inicializador Global de Pantalla de Carga
 * Maneja la pantalla de carga de forma centralizada para toda la aplicación
 */
class GlobalLoadingInitializer {
    constructor() {
        this.loadingScreen = null;
        this.isInitialized = false;
        this.startTime = Date.now();
        this.minDisplayTime = 500; // Tiempo mínimo para mostrar la pantalla (evitar parpadeo)
        this.maxDisplayTime = 10000; // Tiempo máximo antes de ocultar automáticamente
        this.timeoutId = null;
        
        this.init();
    }

    /**
     * Inicializa la pantalla de carga
     */
    init() {
        console.log('🔄 Inicializando pantalla de carga global...');
        
        // Buscar elemento de pantalla de carga
        this.loadingScreen = document.querySelector('.loading-screen');
        
        if (!this.loadingScreen) {
            console.warn('⚠️ No se encontró elemento .loading-screen');
            return;
        }

        // Configurar timeout de seguridad
        this.setupSafetyTimeout();
        
        // Escuchar eventos de carga de página
        this.setupEventListeners();
        
        this.isInitialized = true;
        console.log('✅ Pantalla de carga global inicializada');
    }

    /**
     * Configura el timeout de seguridad para ocultar automáticamente
     */
    setupSafetyTimeout() {
        this.timeoutId = setTimeout(() => {
            console.warn('⚠️ Tiempo límite alcanzado, ocultando pantalla automáticamente');
            this.hide('timeout');
        }, this.maxDisplayTime);
    }

    /**
     * Configura los event listeners
     */
    setupEventListeners() {
        // Escuchar cuando la página esté completamente cargada
        if (document.readyState === 'complete') {
            this.onPageLoad();
        } else {
            window.addEventListener('load', () => this.onPageLoad());
        }

        // Escuchar eventos personalizados de módulos
        window.addEventListener('moduleLoaded', (event) => {
            console.log(`📦 Módulo cargado: ${event.detail.moduleName}`);
            this.checkIfReadyToHide();
        });

        window.addEventListener('allModulesLoaded', () => {
            console.log('🎯 Todos los módulos cargados');
            this.hide('allModulesLoaded');
        });
    }

    /**
     * Se ejecuta cuando la página termina de cargar
     */
    onPageLoad() {
        console.log('📄 Página completamente cargada');
        
        // Esperar un poco más para que los scripts se ejecuten
        setTimeout(() => {
            this.checkIfReadyToHide();
        }, 100);
    }

    /**
     * Verifica si está listo para ocultar la pantalla
     */
    checkIfReadyToHide() {
        const elapsed = Date.now() - this.startTime;
        
        // Asegurar tiempo mínimo de display
        if (elapsed < this.minDisplayTime) {
            setTimeout(() => {
                this.hide('minTimeReached');
            }, this.minDisplayTime - elapsed);
        } else {
            this.hide('ready');
        }
    }

    /**
     * Oculta la pantalla de carga
     * @param {string} reason - Razón por la que se oculta
     */
    hide(reason = 'manual') {
        if (!this.loadingScreen || !this.isInitialized) {
            return;
        }

        console.log(`🔄 Ocultando pantalla de carga (${reason})`);

        // Cancelar timeout de seguridad
        if (this.timeoutId) {
            clearTimeout(this.timeoutId);
            this.timeoutId = null;
        }

        // Aplicar transición de salida
        this.loadingScreen.classList.add('hidden');
        
        // Remover completamente después de la transición
        setTimeout(() => {
            if (this.loadingScreen) {
                this.loadingScreen.style.display = 'none';
                console.log('✅ Pantalla de carga ocultada completamente');
            }
        }, 300);

        this.isInitialized = false;
    }

    /**
     * Actualiza el mensaje de la pantalla
     * @param {string} message - Nuevo mensaje
     */
    updateMessage(message) {
        if (this.loadingScreen) {
            const messageElement = this.loadingScreen.querySelector('.fw-bold');
            if (messageElement) {
                messageElement.textContent = message;
                console.log(`💬 Mensaje actualizado: ${message}`);
            }
        }
    }

    /**
     * Actualiza el submensaje de la pantalla
     * @param {string} submessage - Nuevo submensaje
     */
    updateSubMessage(submessage) {
        if (this.loadingScreen) {
            const subMessageElement = this.loadingScreen.querySelector('.small.text-muted');
            if (subMessageElement) {
                subMessageElement.textContent = submessage;
            }
        }
    }

    /**
     * Muestra la pantalla de carga (para casos especiales)
     */
    show(message = 'Cargando aplicación') {
        if (this.loadingScreen) {
            this.updateMessage(message);
            this.loadingScreen.classList.remove('hidden');
            this.loadingScreen.style.display = 'flex';
            this.isInitialized = true;
            console.log('🔄 Pantalla de carga mostrada');
        }
    }
}

// Inicializar automáticamente cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Crear instancia global
    window.globalLoadingInitializer = new GlobalLoadingInitializer();
    
    // Mantener compatibilidad con el controlador anterior
    if (!window.loadingScreen) {
        window.loadingScreen = {
            hide: () => window.globalLoadingInitializer.hide('manual'),
            show: (msg) => window.globalLoadingInitializer.show(msg),
            updateMessage: (msg) => window.globalLoadingInitializer.updateMessage(msg),
            updateSubMessage: (msg) => window.globalLoadingInitializer.updateSubMessage(msg)
        };
    }
});

console.log('🔄 GlobalLoadingInitializer definido');
