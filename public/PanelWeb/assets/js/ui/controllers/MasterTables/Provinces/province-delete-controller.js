/**
 * Province Delete Controller
 * Controlador para manejar la eliminación de provincias usando el modal global de confirmación
 * 
 * Funciones principales:
 * - Mostrar modal de confirmación global
 * - Ejecutar eliminación de provincias
 * - Manejo de estados y notificaciones
 * - Actualización de la lista después de eliminar
 */

class ProvinceDeleteController {
    constructor() {
        this.deleteService = null;
        this.isDeleting = false;
        
        console.log('🗑️ Inicializando ProvinceDeleteController...');
        
        // Inicializar servicio de eliminación
        this.initializeService();
    }

    /**
     * Inicializa el servicio de eliminación
     */
    initializeService() {
        try {
            this.deleteService = new ProvinceDeleteService();
            console.log('✅ Servicio de eliminación de provincias inicializado');
        } catch (error) {
            console.error('❌ Error inicializando servicio de eliminación:', error);
        }
    }

    /**
     * Maneja la solicitud de eliminación de una provincia
     * @param {number} provinceId - ID de la provincia
     * @param {string} provinceName - Nombre de la provincia
     * @param {string} provinceUbigeo - UBIGEO de la provincia
     * @param {string} departmentName - Nombre del departamento
     */
    handleDeleteProvince(provinceId, provinceName, provinceUbigeo, departmentName) {
        console.log('🗑️ Solicitud de eliminación de provincia:', { 
            id: provinceId, 
            name: provinceName, 
            ubigeo: provinceUbigeo,
            department: departmentName 
        });
        
        // Verificar que el modal global esté disponible
        if (!window.globalConfirmationModal) {
            this.showErrorToast('El sistema de eliminación no está disponible');
            console.error('❌ GlobalConfirmationModal no encontrado');
            return;
        }

        // Verificar que el servicio esté disponible
        if (!this.deleteService) {
            this.showErrorToast('El servicio de eliminación no está disponible');
            console.error('❌ ProvinceDeleteService no inicializado');
            return;
        }

        // SVG de Tabler para provincias (map-marker-alt)
        const provinceIconSvg = `
            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                <circle cx="12" cy="11" r="3"/>
                <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z"/>
            </svg>
        `;

        // Formatear UBIGEO para mostrar 6 dígitos
        const formattedUbigeo = provinceUbigeo ? provinceUbigeo.padEnd(6, '0') : 'N/A';

        // Preparar subtítulo con información adicional
        const subtitle = `${departmentName} - UBIGEO: ${formattedUbigeo}`;

        // Abrir modal de confirmación global
        window.globalConfirmationModal.showConfirmation({
            title: '¿Está seguro de eliminar?',
            name: provinceName,
            subtitle: subtitle,
            iconSvg: provinceIconSvg,
            avatarColor: 'bg-primary',
            confirmText: 'Eliminar',
            loadingText: 'Eliminando provincia...',
            onConfirm: async (data) => {
                // Ejecutar eliminación
                await this.deleteProvince(provinceId);
            },
            data: { 
                id: provinceId, 
                name: provinceName, 
                ubigeo: provinceUbigeo,
                department: departmentName 
            }
        });
    }

    /**
     * Elimina una provincia usando el servicio de eliminación
     * @param {number} provinceId - ID de la provincia a eliminar
     */
    async deleteProvince(provinceId) {
        if (this.isDeleting) {
            console.log('⏳ Eliminación ya en progreso, omitiendo...');
            return;
        }

        this.isDeleting = true;

        try {
            console.log('🗑️ Iniciando eliminación de provincia:', provinceId);
            
            const result = await this.deleteService.deleteProvince(provinceId);

            if (result.status === 'success') {
                console.log('✅ Provincia eliminada exitosamente');
                
                // Mostrar notificación de éxito
                this.showSuccessToast('Provincia eliminada correctamente');
                
                // Recargar la lista de provincias si existe el controlador
                this.reloadProvincesList();
                
            } else {
                throw new Error(result.message || 'Error al eliminar la provincia');
            }

        } catch (error) {
            console.error('❌ Error eliminando provincia:', error);
            
            // Re-lanzar el error para que el modal lo maneje
            throw error;
            
        } finally {
            this.isDeleting = false;
        }
    }

    /**
     * Recarga la lista de provincias después de una eliminación
     */
    reloadProvincesList() {
        try {
            // Verificar si existe el controlador de lista de provincias
            if (window.provincesController && typeof window.provincesController.loadProvinces === 'function') {
                console.log('🔄 Recargando lista de provincias...');
                window.provincesController.lastAction = 'delete_refresh';
                window.provincesController.loadProvinces();
            } else if (window.ProvincesListController && window.ProvincesListController.load) {
                console.log('🔄 Recargando lista con controlador alternativo...');
                window.ProvincesListController.load();
            } else {
                console.warn('⚠️ Controlador de lista no encontrado, recargando página...');
                // Si no existe el controlador, recargar la página después de un breve delay
                setTimeout(() => {
                    location.reload();
                }, 1500);
            }
        } catch (error) {
            console.error('❌ Error recargando lista de provincias:', error);
            // Fallback: recargar página
            setTimeout(() => {
                location.reload();
            }, 1500);
        }
    }

    /**
     * Valida si una provincia puede ser eliminada
     * @param {number} provinceId - ID de la provincia
     * @returns {Promise<boolean>} True si puede ser eliminada
     */
    async validateDeletion(provinceId) {
        try {
            if (!this.deleteService) {
                console.error('❌ Servicio de eliminación no disponible');
                return false;
            }

            const validation = await this.deleteService.validateDeletion(provinceId);
            return validation.canDelete;

        } catch (error) {
            console.error('❌ Error validando eliminación:', error);
            return false;
        }
    }

    /**
     * Obtiene información de una provincia para mostrar en el modal
     * @param {number} provinceId - ID de la provincia
     * @returns {Object|null} Información de la provincia
     */
    getProvinceInfo(provinceId) {
        try {
            // Intentar obtener información del controlador de lista
            if (window.provincesController && window.provincesController.provinces) {
                const province = window.provincesController.provinces.find(p => p.id === provinceId);
                if (province) {
                    return {
                        id: province.id,
                        name: province.name,
                        ubigeo: province.ubigeo,
                        departmentName: province.department?.name || 'N/A'
                    };
                }
            }
            
            console.warn('⚠️ No se pudo obtener información de la provincia desde el controlador');
            return null;

        } catch (error) {
            console.error('❌ Error obteniendo información de provincia:', error);
            return null;
        }
    }

    /**
     * Muestra toast de éxito
     * @param {string} message - Mensaje a mostrar
     */
    showSuccessToast(message) {
        if (window.GlobalToast) {
            GlobalToast.show(message, 'success');
        } else {
            console.log('✅', message);
        }
    }

    /**
     * Muestra toast de error
     * @param {string} message - Mensaje de error
     */
    showErrorToast(message) {
        if (window.GlobalToast) {
            GlobalToast.show(message, 'error');
        } else {
            console.error('❌', message);
            alert(message); // Fallback
        }
    }

    /**
     * Callback ejecutado cuando se elimina una provincia (para compatibilidad)
     * @param {number} provinceId - ID de la provincia eliminada
     */
    onProvinceDeleted(provinceId) {
        console.log(`✅ Provincia eliminada (callback): ${provinceId}`);
        this.reloadProvincesList();
    }

    /**
     * Limpia los recursos del controlador
     */
    destroy() {
        this.deleteService = null;
        this.isDeleting = false;
        console.log('🧹 ProvinceDeleteController destruido');
    }
}

// Crear instancia global para uso en toda la aplicación
window.provinceDeleteController = new ProvinceDeleteController();

// También exportar la clase para uso directo
window.ProvinceDeleteController = ProvinceDeleteController;

console.log('✅ ProvinceDeleteController cargado correctamente');
