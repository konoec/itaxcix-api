/**
 * Department Delete Service
 * Servicio especializado para la eliminación de departamentos
 * 
 * Funciones principales:
 * - Eliminación de departamentos específicos
 * - Manejo de errores y validaciones
 * - Gestión de respuestas de la API
 */

class DepartmentDeleteService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.endpoint = '/admin/departments';
        
        console.log('🗑️ Inicializando DepartmentDeleteService...');
    }

    /**
     * Elimina un departamento específico
     * @param {number} id - ID del departamento a eliminar
     * @returns {Promise<Object>} Respuesta de la API
     */
    async deleteDepartment(id) {
        console.log('🏛️ Eliminando departamento:', id);

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
                    throw new Error(`HTTP ${response.status}: ${errorData.message || 'Error al eliminar departamento'}`);
                }

                const result = await response.json();
                console.log('✅ Departamento eliminado exitosamente:', result);

                return result;

            } catch (fetchError) {
                clearTimeout(timeoutId);
                
                if (fetchError.name === 'AbortError') {
                    throw new Error('La solicitud tardó demasiado en responder');
                }
                throw fetchError;
            }

        } catch (error) {
            console.error('❌ Error eliminando departamento:', error);
            
            // Manejo específico de errores comunes
            if (error.message.includes('404')) {
                throw new Error('El departamento no existe o ya fue eliminado');
            } else if (error.message.includes('403')) {
                throw new Error('No tienes permisos para eliminar este departamento');
            } else if (error.message.includes('409')) {
                throw new Error('No se puede eliminar el departamento porque tiene dependencias');
            } else if (error.message.includes('500')) {
                throw new Error('Error interno del servidor. Inténtelo más tarde');
            }
            
            throw error;
        }
    }

    /**
     * Valida si un departamento puede ser eliminado
     * @param {number} id - ID del departamento
     * @returns {Promise<Object>} Resultado de la validación
     */
    async validateDeletion(id) {
        console.log('🔍 Validando eliminación del departamento:', id);

        try {
            // Por ahora solo validamos que el ID sea válido
            if (!id || isNaN(id) || parseInt(id) <= 0) {
                return {
                    canDelete: false,
                    reason: 'ID de departamento inválido'
                };
            }

            return {
                canDelete: true,
                reason: 'Departamento válido para eliminación'
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
     * Obtiene información de impacto antes de la eliminación
     * @param {number} id - ID del departamento
     * @returns {Promise<Object>} Información de impacto
     */
    async getDeleteImpact(id) {
        console.log('📊 Obteniendo impacto de eliminación para departamento:', id);

        try {
            // Nota: Esta funcionalidad podría implementarse en el futuro
            // si la API proporciona endpoints para verificar dependencias
            return {
                hasProvinces: false,
                provincesCount: 0,
                hasDistricts: false,
                districtsCount: 0,
                canSafelyDelete: true,
                warnings: []
            };

        } catch (error) {
            console.error('❌ Error obteniendo impacto:', error);
            return {
                hasProvinces: false,
                provincesCount: 0,
                hasDistricts: false,
                districtsCount: 0,
                canSafelyDelete: true,
                warnings: ['No se pudo verificar el impacto de la eliminación']
            };
        }
    }
}

// Hacer el servicio disponible globalmente
window.DepartmentDeleteService = DepartmentDeleteService;

console.log('✅ DepartmentDeleteService cargado correctamente');
