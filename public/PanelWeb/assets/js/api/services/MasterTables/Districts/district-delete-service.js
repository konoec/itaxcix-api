/**
 * Servicio para la eliminación de distritos
 * Maneja las operaciones DELETE contra la API de distritos
 * @author Sistema de Gestión
 * @version 1.0.0
 */

class DistrictDeleteService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1/admin/districts';
        this.timeout = 30000; // 30 segundos
    }

    /**
     * Elimina un distrito del sistema
     * @param {number} districtId - ID del distrito a eliminar
     * @returns {Promise<Object>} Respuesta de la API
     */
    async deleteDistrict(districtId) {
        console.log(`🗑️ Iniciando eliminación del distrito ID: ${districtId}`);
        
        try {
            // Validar parámetros
            if (!districtId) {
                throw new Error('El ID del distrito es requerido');
            }

            // Obtener token de autenticación
            const token = sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
            if (!token) {
                throw new Error('Token de autenticación no encontrado');
            }

            console.log(`🔐 Token obtenido para eliminación del distrito ${districtId}`);

            // Configurar la petición con timeout
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), this.timeout);

            const response = await fetch(`${this.baseUrl}/${districtId}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                signal: controller.signal
            });

            clearTimeout(timeoutId);

            console.log(`📡 Respuesta DELETE recibida para distrito ${districtId}:`, response.status);

            // Parsear respuesta JSON
            const data = await response.json();
            console.log(`📄 Datos de respuesta para distrito ${districtId}:`, data);

            if (response.ok) {
                console.log(`✅ Distrito ${districtId} eliminado exitosamente`);
                return {
                    success: true,
                    message: data.message || 'Distrito eliminado exitosamente',
                    data: data
                };
            } else {
                console.error(`❌ Error al eliminar distrito ${districtId}:`, data);
                throw new Error(data.message || `Error ${response.status}: No se pudo eliminar el distrito`);
            }

        } catch (error) {
            console.error(`💥 Error crítico al eliminar distrito ${districtId}:`, error);
            
            if (error.name === 'AbortError') {
                throw new Error('La operación de eliminación tardó demasiado tiempo');
            }
            
            if (error.message.includes('fetch')) {
                throw new Error('Error de conexión. Verifique su conexión a internet');
            }
            
            throw error;
        }
    }

    /**
     * Valida si un distrito puede ser eliminado
     * @param {Object} districtData - Datos del distrito a validar
     * @returns {Object} Resultado de la validación
     */
    validateDeletion(districtData) {
        console.log('🔍 Validando eliminación de distrito:', districtData);
        
        const warnings = [];
        const confirmations = [];

        // Validaciones básicas
        if (!districtData) {
            return {
                canDelete: false,
                error: 'No se proporcionaron datos del distrito'
            };
        }

        if (!districtData.id) {
            return {
                canDelete: false,
                error: 'ID del distrito no válido'
            };
        }

        // Advertencias informativas
        if (districtData.name) {
            warnings.push(`Se eliminará el distrito: "${districtData.name}"`);
        }

        if (districtData.province && districtData.province.name) {
            warnings.push(`Provincia: ${districtData.province.name}`);
        }

        if (districtData.ubigeo) {
            warnings.push(`Código UBIGEO: ${districtData.ubigeo}`);
        }

        // Confirmaciones de impacto
        confirmations.push('Esta acción no se puede deshacer');
        confirmations.push('Verifique que no existan registros dependientes');

        return {
            canDelete: true,
            warnings,
            confirmations,
            districtInfo: {
                id: districtData.id,
                name: districtData.name,
                ubigeo: districtData.ubigeo,
                province: districtData.province?.name || 'N/A'
            }
        };
    }

    /**
     * Obtiene estadísticas después de la eliminación
     * @returns {Promise<Object>} Estadísticas actualizadas
     */
    async getPostDeletionStats() {
        try {
            console.log('📊 Obteniendo estadísticas post-eliminación...');
            
            // Si existe un servicio de estadísticas, usarlo
            if (window.DistrictsService && typeof window.DistrictsService.getStats === 'function') {
                return await window.DistrictsService.getStats();
            }
            
            return { message: 'Estadísticas no disponibles' };
            
        } catch (error) {
            console.warn('⚠️ No se pudieron obtener estadísticas:', error);
            return { message: 'Error al obtener estadísticas' };
        }
    }
}

// Hacer el servicio disponible globalmente
window.DistrictDeleteService = DistrictDeleteService;

// Log de inicialización
console.log('🗑️ DistrictDeleteService cargado y disponible globalmente');
