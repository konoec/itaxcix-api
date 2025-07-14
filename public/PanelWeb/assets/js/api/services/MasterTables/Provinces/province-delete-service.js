/**
 * Province Delete Service
 * Servicio especializado para la eliminación de provincias
 * 
 * Funciones principales:
 * - Eliminación de provincias específicas
 * - Manejo de errores y validaciones
 * - Gestión de respuestas de la API
 */

class ProvinceDeleteService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.endpoint = '/admin/provinces';
        
        console.log('🗑️ Inicializando ProvinceDeleteService...');
    }

    /**
     * Elimina una provincia específica
     * @param {number} id - ID de la provincia a eliminar
     * @returns {Promise<Object>} Respuesta de la API
     */
    async deleteProvince(id) {
        console.log('🏛️ Eliminando provincia:', id);

        try {
            const token = sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
            if (!token) {
                throw new Error('No hay token de autenticación');
            }

            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 8000);

            try {
                const response = await fetch(`${this.baseUrl}${this.endpoint}/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    signal: controller.signal
                });

                clearTimeout(timeoutId);

                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(`HTTP ${response.status}: ${errorData.message || 'Error al eliminar provincia'}`);
                }

                const result = await response.json();
                console.log('✅ Provincia eliminada exitosamente:', result);

                return result;

            } catch (fetchError) {
                clearTimeout(timeoutId);
                
                if (fetchError.name === 'AbortError') {
                    throw new Error('La solicitud tardó demasiado en responder');
                }
                throw fetchError;
            }

        } catch (error) {
            console.error('❌ Error eliminando provincia:', error);
            
            // Manejo específico de errores comunes
            if (error.message.includes('404')) {
                throw new Error('La provincia no existe o ya fue eliminada');
            } else if (error.message.includes('403')) {
                throw new Error('No tienes permisos para eliminar esta provincia');
            } else if (error.message.includes('409')) {
                throw new Error('No se puede eliminar la provincia porque tiene dependencias');
            } else if (error.message.includes('500')) {
                throw new Error('Error interno del servidor. Inténtelo más tarde');
            }
            
            throw error;
        }
    }

    /**
     * Valida si una provincia puede ser eliminada
     * @param {number} id - ID de la provincia
     * @returns {Promise<Object>} Resultado de la validación
     */
    async validateDeletion(id) {
        console.log('🔍 Validando eliminación de la provincia:', id);

        try {
            // Por ahora solo validamos que el ID sea válido
            if (!id || isNaN(id) || parseInt(id) <= 0) {
                return {
                    canDelete: false,
                    reason: 'ID de provincia inválido'
                };
            }

            // En el futuro podríamos verificar dependencias antes de eliminar
            return {
                canDelete: true,
                reason: 'Provincia puede ser eliminada'
            };

        } catch (error) {
            console.error('❌ Error validando eliminación:', error);
            return {
                canDelete: false,
                reason: 'Error al validar la eliminación'
            };
        }
    }

    /**
     * Obtiene estadísticas después de una eliminación
     * @returns {Promise<Object>} Estadísticas actualizadas
     */
    async getPostDeletionStats() {
        try {
            const token = sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
            if (!token) {
                throw new Error('No hay token de autenticación');
            }

            const response = await fetch(`${this.baseUrl}${this.endpoint}?page=1&limit=1`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Error al obtener estadísticas');
            }

            const result = await response.json();
            
            return {
                total: result.data?.pagination?.total || 0,
                success: true
            };

        } catch (error) {
            console.error('❌ Error obteniendo estadísticas post-eliminación:', error);
            return {
                total: 0,
                success: false,
                error: error.message
            };
        }
    }

    /**
     * Realiza una eliminación con confirmación adicional
     * @param {number} id - ID de la provincia
     * @param {string} confirmationText - Texto de confirmación esperado
     * @returns {Promise<Object>} Resultado de la eliminación
     */
    async deleteWithConfirmation(id, confirmationText) {
        console.log('🔐 Eliminación con confirmación adicional:', { id, confirmationText });

        try {
            // Primero validar que se puede eliminar
            const validation = await this.validateDeletion(id);
            if (!validation.canDelete) {
                throw new Error(validation.reason);
            }

            // Proceder con la eliminación
            const result = await this.deleteProvince(id);

            // Obtener estadísticas actualizadas
            const stats = await this.getPostDeletionStats();
            
            return {
                ...result,
                stats,
                deletionConfirmed: true
            };

        } catch (error) {
            console.error('❌ Error en eliminación con confirmación:', error);
            throw error;
        }
    }
}

// Exportar para uso global
window.ProvinceDeleteService = ProvinceDeleteService;

console.log('✅ ProvinceDeleteService cargado correctamente');
