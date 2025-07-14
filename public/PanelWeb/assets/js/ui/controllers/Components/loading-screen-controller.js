/**
 * Controlador para la pantalla de carga
 * Maneja mostrar/ocultar la pantalla de carga del sistema
 */
class LoadingScreenController {
    constructor() {
        this.loadingScreen = null;
        this.isVisible = false;
        this.timeoutId = null;
        this.maxLoadTime = 20000; // 20 segundos máximo (más que los 10 segundos de prueba)
        
        // Configurar tiempo límite automático
        this.setupAutoHide();
    }

    /**
     * Configura el tiempo límite automático para ocultar la pantalla
     */
    setupAutoHide() {
        if (this.maxLoadTime > 0) {
            this.timeoutId = setTimeout(() => {
                console.warn('⚠️ Tiempo límite alcanzado, ocultando pantalla de carga automáticamente');
                this.forceHide();
            }, this.maxLoadTime);
        } else {
            console.log('⏱️ Timeout automático deshabilitado (maxLoadTime = 0)');
        }
    }

    /**
     * Muestra la pantalla de carga
     * @param {string} message - Mensaje opcional a mostrar
     */
    show(message = 'Cargando aplicación') {
        if (!this.loadingScreen) {
            this.loadingScreen = document.querySelector('.loading-screen');
        }

        if (this.loadingScreen) {
            // Actualizar mensaje si se proporciona
            const messageElement = this.loadingScreen.querySelector('.fw-bold');
            if (messageElement && message) {
                messageElement.textContent = message;
            }

            this.loadingScreen.classList.remove('hidden');
            this.loadingScreen.style.display = 'flex';
            this.loadingScreen.style.opacity = '1';
            this.loadingScreen.style.visibility = 'visible';
            this.isVisible = true;
            
            console.log('🔄 Pantalla de carga mostrada');
        } else {
            console.warn('⚠️ No se encontró la pantalla de carga');
        }
    }

    /**
     * Oculta la pantalla de carga
     */
    hide() {
        // Cancelar timeout automático
        if (this.timeoutId) {
            clearTimeout(this.timeoutId);
            this.timeoutId = null;
        }

        if (!this.loadingScreen) {
            this.loadingScreen = document.querySelector('.loading-screen');
        }

        if (this.loadingScreen) {
            console.log('🔄 Ocultando pantalla de carga...');
            
            // Agregar la clase hidden para la transición
            this.loadingScreen.classList.add('hidden');
            this.isVisible = false;
            
            // Remover completamente después de la transición
            setTimeout(() => {
                if (this.loadingScreen && !this.isVisible) {
                    this.loadingScreen.style.display = 'none';
                }
            }, 300);
            
            console.log('✅ Pantalla de carga ocultada');
        } else {
            console.warn('⚠️ No se encontró la pantalla de carga para ocultar');
        }
    }

    /**
     * Fuerza el ocultamiento de la pantalla de carga (sin transición)
     */
    forceHide() {
        if (!this.loadingScreen) {
            this.loadingScreen = document.querySelector('.loading-screen');
        }

        if (this.loadingScreen) {
            console.log('🚨 Forzando ocultamiento de pantalla de carga...');
            
            this.loadingScreen.style.display = 'none';
            this.loadingScreen.style.opacity = '0';
            this.loadingScreen.style.visibility = 'hidden';
            this.isVisible = false;
            
            console.log('✅ Pantalla de carga ocultada forzadamente');
        }
    }

    /**
     * Actualiza el mensaje de la pantalla de carga
     * @param {string} message - Nuevo mensaje
     */
    updateMessage(message) {
        if (this.loadingScreen) {
            const messageElement = this.loadingScreen.querySelector('.fw-bold');
            if (messageElement) {
                messageElement.textContent = message;
            }
        }
    }

    /**
     * Actualiza el submensaje de la pantalla de carga
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
     * Método de prueba para verificar el funcionamiento
     */
    test() {
        console.log('🧪 Probando controlador de pantalla de carga...');
        
        // Buscar elemento
        const element = document.querySelector('.loading-screen');
        console.log('📍 Elemento encontrado:', element);
        
        if (element) {
            console.log('✅ Elemento existe');
            console.log('🎨 Estilos actuales:', {
                display: element.style.display,
                opacity: element.style.opacity,
                visibility: element.style.visibility
            });
            
            // Probar ocultar
            setTimeout(() => {
                this.hide();
                console.log('🔄 Comando hide() ejecutado');
            }, 1000);
        } else {
            console.error('❌ No se encontró el elemento .loading-screen');
        }
    }
}

// Instancia global para uso en toda la aplicación
window.LoadingScreenController = LoadingScreenController;
window.loadingScreen = new LoadingScreenController();

console.log(`🔄 LoadingScreenController inicializado con maxLoadTime: ${window.loadingScreen.maxLoadTime}ms`);
