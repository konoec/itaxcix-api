/**
 * Utilitario para comunicación con la pantalla de carga global
 * Permite a los módulos comunicarse con el inicializador global
 * ELIMINA LA NECESIDAD DE DUPLICAR CÓDIGO EN CADA INICIALIZADOR
 */
class LoadingScreenUtil {
    static moduleLoadedCount = 0;
    static expectedModules = 1; // Por defecto espera 1 módulo, puede configurarse
    static hideDelay = 0; // 👈 1 segundo de margen para que TODO termine de cargar

    /**
     * Configura cuántos módulos se espera que carguen
     * @param {number} count - Número de módulos esperados
     */
    static setExpectedModules(count) {
        this.expectedModules = count;
        console.log(`🔢 Esperando ${count} módulos para completar carga`);
    }

    /**
     * Configura el tiempo de delay antes de ocultar la pantalla
     * @param {number} milliseconds - Tiempo en milisegundos
     */
    static setHideDelay(milliseconds) {
        this.hideDelay = milliseconds;
        console.log(`⏱️ Tiempo de ocultamiento configurado: ${milliseconds}ms`);
    }

    /**
     * Notifica que un módulo específico ha terminado de cargar
     * @param {string} moduleName - Nombre del módulo que terminó de cargar
     */
    static notifyModuleLoaded(moduleName) {
        console.log(`🔔 LoadingScreenUtil.notifyModuleLoaded() llamado con: ${moduleName}`);
        console.log(`📊 Estado actual: count=${this.moduleLoadedCount}, expected=${this.expectedModules}, delay=${this.hideDelay}ms`);
        
        this.moduleLoadedCount++;
        console.log(`✅ Módulo cargado: ${moduleName} (${this.moduleLoadedCount}/${this.expectedModules})`);
        
        const event = new CustomEvent('moduleLoaded', {
            detail: { moduleName, timestamp: Date.now() }
        });
        window.dispatchEvent(event);

        // Auto-ocultar cuando todos los módulos estén listos
        if (this.moduleLoadedCount >= this.expectedModules) {
            console.log(`🎯 Todos los módulos cargados! Iniciando timeout de ${this.hideDelay}ms...`);
            setTimeout(() => {
                console.log('🚀 Ejecutando hide() después del timeout...');
                this.hide();
                console.log('🎉 Todos los módulos cargados, ocultando pantalla de carga');
            }, this.hideDelay); // Usar el delay configurado
        } else {
            console.log(`⏳ Esperando más módulos... (${this.moduleLoadedCount}/${this.expectedModules})`);
        }
    }

    /**
     * Notifica que todos los módulos han terminado de cargar
     */
    static notifyAllModulesLoaded() {
        const event = new CustomEvent('allModulesLoaded', {
            detail: { timestamp: Date.now() }
        });
        window.dispatchEvent(event);
        this.hide();
    }

    /**
     * Actualiza el mensaje de la pantalla de carga
     * @param {string} message - Nuevo mensaje
     */
    static updateMessage(message) {
        if (window.globalLoadingInitializer) {
            window.globalLoadingInitializer.updateMessage(message);
        }
    }

    /**
     * Actualiza el submensaje de la pantalla de carga
     * @param {string} submessage - Nuevo submensaje
     */
    static updateSubMessage(submessage) {
        if (window.globalLoadingInitializer) {
            window.globalLoadingInitializer.updateSubMessage(submessage);
        }
    }

    /**
     * Oculta manualmente la pantalla de carga
     */
    static hide() {
        if (window.loadingScreen) {
            window.loadingScreen.hide();
            console.log('🎯 Pantalla de carga ocultada via LoadingScreenUtil');
        } else if (window.globalLoadingInitializer) {
            window.globalLoadingInitializer.hide('manual');
        } else {
            // Fallback directo
            const loadingElement = document.querySelector('.loading-screen');
            if (loadingElement) {
                loadingElement.style.display = 'none';
                console.log('🔄 Pantalla de carga ocultada directamente');
            }
        }
    }

    /**
     * Muestra la pantalla de carga (para casos especiales)
     * @param {string} message - Mensaje a mostrar
     */
    static show(message = 'Cargando aplicación') {
        if (window.loadingScreen) {
            window.loadingScreen.show(message);
        } else if (window.globalLoadingInitializer) {
            window.globalLoadingInitializer.show(message);
        }
    }

    /**
     * Resetea el contador de módulos (útil para testing)
     */
    static reset() {
        this.moduleLoadedCount = 0;
        console.log('🔄 Contador de módulos reseteado');
    }
}

// Exportar para uso global
window.LoadingScreenUtil = LoadingScreenUtil;

console.log('🔧 LoadingScreenUtil definido');
console.log(`⏱️ CONFIGURACIÓN ACTUAL: hideDelay = ${LoadingScreenUtil.hideDelay}ms (10 segundos)`);
console.log('🔄 Si ves este mensaje, el archivo se está cargando correctamente');
