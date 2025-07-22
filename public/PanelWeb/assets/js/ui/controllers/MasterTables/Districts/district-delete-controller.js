/**
 * Controlador para la eliminación de distritos
 * Maneja la interfaz de usuario y la lógica de eliminación
 * @author Sistema de Gestión
 * @version 1.0.0
 */

class DistrictDeleteController {
    constructor() {
        this.deleteService = null;
        this.currentDistrictData = null;
        this.init();
    }

    /**
     * Inicializa el controlador
     */
    init() {
        console.log('🗑️ Inicializando DistrictDeleteController...');
        
        // Verificar dependencias críticas
        const dependencies = {
            'DistrictDeleteService': typeof DistrictDeleteService,
            'globalConfirmationModal': typeof window.globalConfirmationModal,
            'GlobalToast': typeof window.GlobalToast
        };
        
        console.log('🔍 Estado de dependencias:', dependencies);
        
        // Verificar disponibilidad del servicio
        if (typeof DistrictDeleteService !== 'undefined') {
            this.deleteService = new DistrictDeleteService();
            console.log('✅ DistrictDeleteService inicializado');
        } else {
            console.error('❌ DistrictDeleteService no está disponible');
        }
        
        // Verificar modal de confirmación global
        if (!window.globalConfirmationModal) {
            console.warn('⚠️ globalConfirmationModal no está disponible');
        } else {
            console.log('✅ globalConfirmationModal disponible');
        }
    }

    /**
     * Maneja la eliminación de un distrito
     * @param {Object} districtData - Datos del distrito a eliminar
     */
    async handleDeleteDistrict(districtData) {
        console.log('🗑️ Iniciando proceso de eliminación de distrito:', districtData);
        try {
            // Validar servicio
            if (!this.deleteService) {
                throw new Error('Servicio de eliminación no disponible');
            }
            // Validar datos del distrito
            const validation = this.deleteService.validateDeletion(districtData);
            if (!validation.canDelete) {
                throw new Error(validation.error);
            }
            // Guardar datos para el proceso de eliminación
            this.currentDistrictData = districtData;
            // Configurar modal de confirmación al estilo tipos de código de usuario
            const districtInfo = validation.districtInfo;
            const formattedUbigeo = districtInfo.ubigeo ? districtInfo.ubigeo.padEnd(6, '0') : 'N/A';
            const subtitle = `${districtInfo.province} - UBIGEO: ${formattedUbigeo}`;
            const config = {
                title: 'Eliminar Distrito',
                name: districtInfo.name,
                subtitle: subtitle,
                avatarColor: 'bg-primary',
                confirmText: 'Eliminar',
                loadingText: 'Eliminando distrito...',
                onConfirm: async () => {
                    await this.executeDelete();
                },
                data: {
                    id: districtInfo.id,
                    name: districtInfo.name,
                    ubigeo: districtInfo.ubigeo,
                    province: districtInfo.province
                }
            };
            if (window.globalConfirmationModal && typeof window.globalConfirmationModal.showConfirmation === 'function') {
                window.globalConfirmationModal.showConfirmation(config);
            } else {
                console.error('❌ Modal de confirmación global no disponible');
                throw new Error('Sistema de confirmación no disponible');
            }
        } catch (error) {
            console.error('💥 Error en handleDeleteDistrict:', error);
            this.showErrorMessage(error.message);
        }
    }

    /**
     * Construye la configuración del modal de confirmación
     * @param {Object} validation - Datos de validación
     * @returns {Object} Configuración del modal
     */
    buildModalConfig(validation) {
        const districtInfo = validation.districtInfo;
        
        // SVG de Tabler para distritos (map-pin)
        const districtIconSvg = `

                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"/>
                <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z"/>
            </svg>
        `;

        // Formatear UBIGEO para mostrar 6 dígitos
        const formattedUbigeo = districtInfo.ubigeo ? districtInfo.ubigeo.padEnd(6, '0') : 'N/A';

        // Preparar subtítulo con información adicional
        const subtitle = `${districtInfo.province} - UBIGEO: ${formattedUbigeo}`;

        return {
            title: '¿Está seguro de eliminar?',
            name: districtInfo.name,
            subtitle: subtitle,
            iconSvg: districtIconSvg,
            avatarColor: 'bg-primary',
            confirmText: 'Eliminar',
            loadingText: 'Eliminando distrito...',
            onConfirm: async (data) => {
                // Ejecutar eliminación
                await this.executeDelete();
            },
            data: { 
                id: districtInfo.id, 
                name: districtInfo.name, 
                ubigeo: districtInfo.ubigeo,
                province: districtInfo.province 
            }
        };
    }

    /**
     * Ejecuta la eliminación del distrito
     */
    async executeDelete() {
        console.log('🔥 Ejecutando eliminación del distrito...');

        try {
            // Mostrar indicador de carga
            this.showLoadingState(true);

            // Realizar eliminación
            const result = await this.deleteService.deleteDistrict(this.currentDistrictData.id);

            if (result.success) {
                console.log('✅ Distrito eliminado exitosamente');
                
                // Mostrar mensaje de éxito
                this.showSuccessMessage(result.message || 'Distrito eliminado exitosamente');
                
                // Recargar lista de distritos
                await this.refreshDistrictsList();
                
                // Obtener estadísticas actualizadas
                try {
                    await this.deleteService.getPostDeletionStats();
                } catch (statsError) {
                    console.warn('⚠️ Error al obtener estadísticas:', statsError);
                }

            } else {
                throw new Error(result.message || 'Error desconocido al eliminar distrito');
            }

        } catch (error) {
            console.error('💥 Error al ejecutar eliminación:', error);
            this.showErrorMessage(error.message);
        } finally {
            this.showLoadingState(false);
            this.cleanup();
        }
    }

    /**
     * Cancela la eliminación
     */
    cancelDelete() {
        console.log('❌ Eliminación de distrito cancelada por el usuario');
        this.cleanup();
    }

    /**
     * Refresca la lista de distritos
     */
    async refreshDistrictsList() {
        try {
            console.log('🔄 Refrescando lista de distritos...');
            
            // Intentar múltiples métodos de recarga
            if (window.districtsController && typeof window.districtsController.load === 'function') {
                await window.districtsController.load();
            } else if (window.DistrictsListController && typeof window.DistrictsListController.load === 'function') {
                await window.DistrictsListController.load();
            } else {
                console.warn('⚠️ Controlador de lista no encontrado, recargando página...');
                setTimeout(() => location.reload(), 1500);
            }
            
        } catch (error) {
            console.error('❌ Error al refrescar lista:', error);
            setTimeout(() => location.reload(), 1500);
        }
    }

    /**
     * Muestra estado de carga
     * @param {boolean} loading - Si está cargando
     */
    showLoadingState(loading) {
        try {
            // Actualizar modal de confirmación si está visible
            if (window.globalConfirmationModal) {
                if (loading) {
                    window.globalConfirmationModal.setLoading('Eliminando distrito...');
                } else {
                    window.globalConfirmationModal.setLoading(false);
                }
            }
        } catch (error) {
            console.warn('⚠️ Error al mostrar estado de carga:', error);
        }
    }

    /**
     * Muestra mensaje de éxito
     * @param {string} message - Mensaje a mostrar
     */
    showSuccessMessage(message) {
        if (window.GlobalToast) {
            window.GlobalToast.show(message, 'success');
        } else {
            alert(message);
        }
    }

    /**
     * Muestra mensaje de error
     * @param {string} message - Mensaje a mostrar
     */
    showErrorMessage(message) {
        if (window.GlobalToast) {
            window.GlobalToast.show(message, 'error');
        } else {
            alert(`Error: ${message}`);
        }
    }

    /**
     * Limpia datos temporales
     */
    cleanup() {
        this.currentDistrictData = null;
        console.log('🧹 Datos temporales limpiados');
    }
}

// Hacer el controlador disponible globalmente
window.DistrictDeleteController = DistrictDeleteController;

// Crear instancia global para uso en toda la aplicación (igual que en provincias)
window.districtDeleteController = new DistrictDeleteController();

// Log de inicialización
console.log('🗑️ DistrictDeleteController cargado y disponible globalmente');
console.log('🔍 Verificación de carga:', {
    classAvailable: typeof DistrictDeleteController !== 'undefined',
    windowProperty: !!window.DistrictDeleteController,
    instanceAvailable: !!window.districtDeleteController,
    timestamp: new Date().toLocaleTimeString()
});
