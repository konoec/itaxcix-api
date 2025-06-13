/**
 * Sistema de gestión de componentes reutilizables
 * Este archivo proporciona una arquitectura para cargar y gestionar componentes UI
 * como sidebar, navbar, profile, etc. de manera modular
 */

class ComponentManager {
    constructor() {
        this.components = new Map();
        this.loadedComponents = new Set();
        this.dependencies = new Map();
    }

    /**
     * Registra un componente en el sistema
     * @param {string} name - Nombre único del componente
     * @param {Function} ComponentClass - Clase constructor del componente
     * @param {Array<string>} dependencies - Array de nombres de componentes dependientes
     */
    register(name, ComponentClass, dependencies = []) {
        this.components.set(name, ComponentClass);
        this.dependencies.set(name, dependencies);
        console.log(`✅ Componente '${name}' registrado`);
        return this;
    }

    /**
     * Carga un componente específico y sus dependencias
     * @param {string} name - Nombre del componente a cargar
     * @param {Object} options - Opciones de inicialización para el componente
     * @returns {Object} - Instancia del componente cargado
     */
    async load(name, options = {}) {
        if (!this.components.has(name)) {
            console.error(`❌ No existe el componente: '${name}'`);
            return null;
        }

        // Si ya está cargado, devuelve la instancia existente
        if (this.loadedComponents.has(name)) {
            return this.loadedComponents.get(name);
        }

        // Cargar dependencias primero
        const deps = this.dependencies.get(name) || [];
        const loadedDeps = {};

        for (const dep of deps) {
            loadedDeps[dep] = await this.load(dep);
        }

        try {
            // Inicializar el componente
            const ComponentClass = this.components.get(name);
            const instance = new ComponentClass({
                ...options,
                dependencies: loadedDeps
            });

            // Si el componente tiene un método init, lo ejecutamos
            if (typeof instance.init === 'function') {
                await instance.init();
            }

            // Guardamos la instancia
            this.loadedComponents.set(name, instance);
            console.log(`✅ Componente '${name}' cargado`);

            return instance;
        } catch (error) {
            console.error(`❌ Error al cargar componente '${name}':`, error);
            return null;
        }
    }

    /**
     * Obtiene un componente ya cargado
     * @param {string} name - Nombre del componente
     * @returns {Object|null} - Instancia del componente o null
     */
    get(name) {
        return this.loadedComponents.get(name) || null;
    }

    /**
     * Carga múltiples componentes a la vez
     * @param {Array<string>} names - Array de nombres de componentes
     * @param {Object} globalOptions - Opciones que se aplicarán a todos los componentes
     * @returns {Object} - Mapa con las instancias de los componentes
     */
    async loadMultiple(names, globalOptions = {}) {
        const results = {};
        for (const name of names) {
            results[name] = await this.load(name, globalOptions);
        }
        return results;
    }
}

// Instancia global
window.componentManager = window.componentManager || new ComponentManager();
