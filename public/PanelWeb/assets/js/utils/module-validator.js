/**
 * Script de validación para verificar la integridad de los módulos de tablas maestras
 * Este script verifica que cada página HTML tenga su inicializador correspondiente
 */

// Configuración de módulos esperados
const MODULES_CONFIG = {
    // Módulos existentes (verificados)
    'Companies': {
        htmlPath: 'pages/MasterTables/Companies.html',
        jsPath: 'assets/js/ui/initializers/MasterTables/companies-initializer.js',
        permissionKey: 'TABLAS MAESTRAS - EMPRESAS',
        menuId: 'menu-tablas-empresas',
        status: 'existing'
    },
    'Configuration': {
        htmlPath: 'pages/MasterTables/Configuration.html',
        jsPath: 'assets/js/ui/initializers/MasterTables/configuration-initializer.js',
        permissionKey: 'TABLAS MAESTRAS - CONFIGURACIÓN',
        menuId: 'menu-tablas-configuracion',
        status: 'existing'
    },
    'District': {
        htmlPath: 'pages/MasterTables/District.html',
        jsPath: 'assets/js/ui/initializers/MasterTables/district-initializer.js',
        permissionKey: 'TABLAS MAESTRAS - DISTRITO',
        menuId: 'menu-tablas-distrito',
        status: 'existing'
    },
    
    // Módulos nuevos creados
    'Departaments': {
        htmlPath: 'pages/MasterTables/Departaments.html',
        jsPath: 'assets/js/ui/initializers/MasterTables/departaments-initializer.js',
        permissionKey: 'TABLAS MAESTRAS - DEPARTAMENTOS',
        menuId: 'menu-tablas-departamentos',
        status: 'new'
    },
    'Province': {
        htmlPath: 'pages/MasterTables/Province.html',
        jsPath: 'assets/js/ui/initializers/MasterTables/province-initializer.js',
        permissionKey: 'TABLAS MAESTRAS - PROVINCIAS',
        menuId: 'menu-tablas-provincias',
        status: 'new'
    },
    'UserStatus': {
        htmlPath: 'pages/MasterTables/UserStatus.html',
        jsPath: 'assets/js/ui/initializers/MasterTables/user-status-initializer.js',
        permissionKey: 'TABLAS MAESTRAS - ESTADO DE USUARIOS',
        menuId: 'menu-tablas-user-status',
        status: 'new'
    },
    'UserCodeType': {
        htmlPath: 'pages/MasterTables/UserCodeType.html',
        jsPath: 'assets/js/ui/initializers/MasterTables/user-code-type-initializer.js',
        permissionKey: 'TABLAS MAESTRAS - TIPOS DE CÓDIGO USUARIO',
        menuId: 'menu-tablas-user-code-type',
        status: 'new'
    },
    'DriverStatus': {
        htmlPath: 'pages/MasterTables/DriverStatus.html',
        jsPath: 'assets/js/ui/initializers/MasterTables/driver-status-initializer.js',
        permissionKey: 'TABLAS MAESTRAS - ESTADO DE CONDUCTORES',
        menuId: 'menu-tablas-driver-status',
        status: 'new'
    },
    'ContactTypes': {
        htmlPath: 'pages/MasterTables/ContactTypes.html',
        jsPath: 'assets/js/ui/initializers/MasterTables/contact-types-initializer.js',
        permissionKey: 'TABLAS MAESTRAS - TIPOS DE CONTACTO',
        menuId: 'menu-tablas-contact-types',
        status: 'new'
    },
    'DocumentTypes': {
        htmlPath: 'pages/MasterTables/DocumentTypes.html',
        jsPath: 'assets/js/ui/initializers/MasterTables/document-types-initializer.js',
        permissionKey: 'TABLAS MAESTRAS - TIPOS DE DOCUMENTOS',
        menuId: 'menu-tablas-document-types',
        status: 'new'
    },
    'Brand': {
        htmlPath: 'pages/MasterTables/Brand.html',
        jsPath: 'assets/js/ui/initializers/MasterTables/brand-initializer.js',
        permissionKey: 'TABLAS MAESTRAS - MARCAS',
        menuId: 'menu-tablas-brands',
        status: 'new'
    },
    'VehicleModel': {
        htmlPath: 'pages/MasterTables/VehicleModel.html',
        jsPath: 'assets/js/ui/initializers/MasterTables/vehicle-model-initializer.js',
        permissionKey: 'TABLAS MAESTRAS - MODELOS DE VEHÍCULOS',
        menuId: 'menu-tablas-vehicle-model',
        status: 'new'
    },
    'VehicleClass': {
        htmlPath: 'pages/MasterTables/VehicleClass.html',
        jsPath: 'assets/js/ui/initializers/MasterTables/vehicle-class-initializer.js',
        permissionKey: 'TABLAS MAESTRAS - CLASES DE VEHÍCULOS',
        menuId: 'menu-tablas-vehicle-class',
        status: 'new'
    },
    'Color': {
        htmlPath: 'pages/MasterTables/Color.html',
        jsPath: 'assets/js/ui/initializers/MasterTables/color-initializer.js',
        permissionKey: 'TABLAS MAESTRAS - COLORES',
        menuId: 'menu-tablas-colors',
        status: 'new'
    },
    'FuelType': {
        htmlPath: 'pages/MasterTables/FuelType.html',
        jsPath: 'assets/js/ui/initializers/MasterTables/fuel-type-initializer.js',
        permissionKey: 'TABLAS MAESTRAS - TIPOS DE COMBUSTIBLE',
        menuId: 'menu-tablas-fuel-type',
        status: 'new'
    },
    'Category': {
        htmlPath: 'pages/MasterTables/Category.html',
        jsPath: 'assets/js/ui/initializers/MasterTables/category-initializer.js',
        permissionKey: 'TABLAS MAESTRAS - CATEGORÍAS',
        menuId: 'menu-tablas-categories',
        status: 'new'
    },
    'ServiceType': {
        htmlPath: 'pages/MasterTables/ServiceType.html',
        jsPath: 'assets/js/ui/initializers/MasterTables/service-type-initializer.js',
        permissionKey: 'TABLAS MAESTRAS - TIPOS DE SERVICIO',
        menuId: 'menu-tablas-service-type',
        status: 'new'
    },
    'ProcedureTypes': {
        htmlPath: 'pages/MasterTables/ProcedureTypes.html',
        jsPath: 'assets/js/ui/initializers/MasterTables/procedure-types-initializer.js',
        permissionKey: 'TABLAS MAESTRAS - TIPOS DE PROCEDIMIENTOS',
        menuId: 'menu-tablas-procedure-types',
        status: 'new'
    },
    'TravelStatus': {
        htmlPath: 'pages/MasterTables/TravelStatus.html',
        jsPath: 'assets/js/ui/initializers/MasterTables/travel-status-initializer.js',
        permissionKey: 'TABLAS MAESTRAS - ESTADO DE VIAJES',
        menuId: 'menu-tablas-travel-status',
        status: 'new'
    },
    'TucModality': {
        htmlPath: 'pages/MasterTables/TucModality.html',
        jsPath: 'assets/js/ui/initializers/MasterTables/tuc-modality-initializer.js',
        permissionKey: 'TABLAS MAESTRAS - MODALIDADES TUC',
        menuId: 'menu-tablas-tuc-modality',
        status: 'new'
    },
    'TucStatus': {
        htmlPath: 'pages/MasterTables/TucStatus.html',
        jsPath: 'assets/js/ui/initializers/MasterTables/tuc-status-initializer.js',
        permissionKey: 'TABLAS MAESTRAS - ESTADO TUC',
        menuId: 'menu-tablas-tuc-status',
        status: 'new'
    },
    'IncidentType': {
        htmlPath: 'pages/MasterTables/IncidentType.html',
        jsPath: 'assets/js/ui/initializers/MasterTables/incident-type-initializer.js',
        permissionKey: 'TABLAS MAESTRAS - TIPOS DE INCIDENTES',
        menuId: 'menu-tablas-incident-type',
        status: 'new'
    },
    'InfractionSeverity': {
        htmlPath: 'pages/MasterTables/InfractionSeverity.html',
        jsPath: 'assets/js/ui/initializers/MasterTables/infraction-severity-initializer.js',
        permissionKey: 'TABLAS MAESTRAS - SEVERIDAD DE INFRACCIONES',
        menuId: 'menu-tablas-infraction-severity',
        status: 'new'
    },
    'InfractionStatus': {
        htmlPath: 'pages/MasterTables/InfractionStatus.html',
        jsPath: 'assets/js/ui/initializers/MasterTables/infraction-status-initializer.js',
        permissionKey: 'TABLAS MAESTRAS - ESTADO DE INFRACCIONES',
        menuId: 'menu-tablas-infraction-status',
        status: 'new'
    }
};

/**
 * Clase para validar la integridad de los módulos
 */
class ModuleValidator {
    constructor() {
        this.results = {
            total: 0,
            passed: 0,
            failed: 0,
            warnings: 0,
            errors: []
        };
    }

    /**
     * Ejecuta todas las validaciones
     */
    async runAllValidations() {
        console.log('🔍 Iniciando validación de módulos...');
        console.log('═'.repeat(50));

        this.results.total = Object.keys(MODULES_CONFIG).length;

        // Validar cada módulo
        for (const [moduleName, config] of Object.entries(MODULES_CONFIG)) {
            try {
                await this.validateModule(moduleName, config);
                this.results.passed++;
            } catch (error) {
                this.results.failed++;
                this.results.errors.push({
                    module: moduleName,
                    error: error.message
                });
            }
        }

        // Validar integración con sistema de permisos
        await this.validatePermissionsIntegration();

        // Validar integración con sidebar
        await this.validateSidebarIntegration();

        // Mostrar resultados
        this.showResults();
    }

    /**
     * Valida un módulo específico
     */
    async validateModule(moduleName, config) {
        console.log(`📋 Validando módulo: ${moduleName}`);

        // Verificar existencia de archivos HTML y JS
        const htmlExists = await this.fileExists(config.htmlPath);
        const jsExists = await this.fileExists(config.jsPath);

        if (!htmlExists) {
            throw new Error(`Archivo HTML no encontrado: ${config.htmlPath}`);
        }

        if (!jsExists) {
            throw new Error(`Archivo JS no encontrado: ${config.jsPath}`);
        }

        // Verificar que el HTML contenga la referencia al inicializador
        const htmlContent = await this.getFileContent(config.htmlPath);
        const jsReference = config.jsPath.replace(/^assets\//, '../../assets/');
        
        if (!htmlContent.includes(jsReference)) {
            this.results.warnings++;
            console.warn(`⚠️ ${moduleName}: Referencia JS no encontrada en HTML`);
        }

        // Verificar que el JS contenga la estructura esperada
        const jsContent = await this.getFileContent(config.jsPath);
        const expectedInitializer = `${moduleName.charAt(0).toLowerCase() + moduleName.slice(1)}Initializer`;
        
        if (!jsContent.includes(expectedInitializer) && !jsContent.includes('function initialize')) {
            this.results.warnings++;
            console.warn(`⚠️ ${moduleName}: Estructura del inicializador JS no estándar`);
        }

        console.log(`✅ ${moduleName}: Validación completada`);
    }

    /**
     * Valida integración con sistema de permisos
     */
    async validatePermissionsIntegration() {
        console.log('🔐 Validando integración con sistema de permisos...');

        try {
            const permissionsContent = await this.getFileContent('assets/js/auth/permissions.js');
            
            for (const [moduleName, config] of Object.entries(MODULES_CONFIG)) {
                if (!permissionsContent.includes(config.permissionKey)) {
                    this.results.warnings++;
                    console.warn(`⚠️ Permiso no encontrado: ${config.permissionKey}`);
                }
            }

            console.log('✅ Integración con permisos validada');
        } catch (error) {
            this.results.errors.push({
                module: 'PermissionsService',
                error: `Error validando permisos: ${error.message}`
            });
        }
    }

    /**
     * Valida integración con sidebar
     */
    async validateSidebarIntegration() {
        console.log('🔗 Validando integración con sidebar...');

        try {
            const sidebarContent = await this.getFileContent('assets/components/sidebar.html');
            
            for (const [moduleName, config] of Object.entries(MODULES_CONFIG)) {
                if (!sidebarContent.includes(config.menuId)) {
                    this.results.warnings++;
                    console.warn(`⚠️ Menu ID no encontrado en sidebar: ${config.menuId}`);
                }
            }

            console.log('✅ Integración con sidebar validada');
        } catch (error) {
            this.results.errors.push({
                module: 'Sidebar',
                error: `Error validando sidebar: ${error.message}`
            });
        }
    }

    /**
     * Verifica si un archivo existe
     */
    async fileExists(path) {
        try {
            const response = await fetch(path);
            return response.ok;
        } catch (error) {
            return false;
        }
    }

    /**
     * Obtiene el contenido de un archivo
     */
    async getFileContent(path) {
        try {
            const response = await fetch(path);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return await response.text();
        } catch (error) {
            throw new Error(`Error leyendo archivo ${path}: ${error.message}`);
        }
    }

    /**
     * Muestra los resultados de la validación
     */
    showResults() {
        console.log('═'.repeat(50));
        console.log('📊 RESULTADOS DE VALIDACIÓN');
        console.log('═'.repeat(50));
        
        console.log(`📋 Total de módulos: ${this.results.total}`);
        console.log(`✅ Módulos válidos: ${this.results.passed}`);
        console.log(`❌ Módulos fallidos: ${this.results.failed}`);
        console.log(`⚠️ Advertencias: ${this.results.warnings}`);
        
        if (this.results.errors.length > 0) {
            console.log('\n❌ ERRORES ENCONTRADOS:');
            this.results.errors.forEach(error => {
                console.log(`   • ${error.module}: ${error.error}`);
            });
        }

        const successRate = ((this.results.passed / this.results.total) * 100).toFixed(1);
        console.log(`\n🎯 Tasa de éxito: ${successRate}%`);
        
        if (this.results.failed === 0) {
            console.log('🎉 ¡Validación completada exitosamente!');
        } else {
            console.log('⚠️ Algunos módulos requieren atención');
        }
    }

    /**
     * Genera un reporte detallado
     */
    generateReport() {
        const report = {
            timestamp: new Date().toISOString(),
            summary: {
                total: this.results.total,
                passed: this.results.passed,
                failed: this.results.failed,
                warnings: this.results.warnings,
                successRate: ((this.results.passed / this.results.total) * 100).toFixed(1)
            },
            modules: {},
            errors: this.results.errors
        };

        // Agregar información de cada módulo
        for (const [moduleName, config] of Object.entries(MODULES_CONFIG)) {
            report.modules[moduleName] = {
                htmlPath: config.htmlPath,
                jsPath: config.jsPath,
                permissionKey: config.permissionKey,
                menuId: config.menuId,
                status: config.status
            };
        }

        return report;
    }
}

// Función para ejecutar validación desde consola
async function runValidation() {
    const validator = new ModuleValidator();
    await validator.runAllValidations();
    return validator.generateReport();
}

// Exportar para uso en navegador
if (typeof window !== 'undefined') {
    window.ModuleValidator = ModuleValidator;
    window.runValidation = runValidation;
    window.MODULES_CONFIG = MODULES_CONFIG;
}

// Exportar para uso en Node.js
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        ModuleValidator,
        runValidation,
        MODULES_CONFIG
    };
}

console.log('✅ Validador de módulos cargado. Ejecuta runValidation() para validar.');
