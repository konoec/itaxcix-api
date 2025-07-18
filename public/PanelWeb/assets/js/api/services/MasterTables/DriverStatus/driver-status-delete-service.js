/**
 * Servicio para eliminar estados de conductor
 * Endpoint: DELETE /api/v1/admin/driver-statuses/{id}
 * Maneja operaciones de eliminación con validación y manejo de errores
 */
class DriverStatusDeleteService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1/admin/driver-statuses';
    }

    /**
     * Elimina un estado de conductor
     * @param {number} id - ID del estado de conductor a eliminar
     * @returns {Promise<Object>} Respuesta de la API
     */
    async deleteDriverStatus(id) {
        try {
            console.log('🗑️ Eliminando estado de conductor:', id);

            // Validar ID
            if (!id || isNaN(id) || id <= 0) {
                throw new Error('ID del estado de conductor no válido');
            }

            // Obtener token de autenticación
            const token = sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
            if (!token) {
                throw new Error('Token de autenticación no encontrado. Por favor, inicia sesión nuevamente.');
            }

            // Realizar petición DELETE
            const response = await fetch(`${this.baseUrl}/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            console.log('📡 Respuesta del servidor:', response.status);

            // Manejar diferentes códigos de respuesta
            if (!response.ok) {
                await this.handleErrorResponse(response);
                return;
            }

            // Procesar respuesta exitosa
            const result = await response.json();
            console.log('✅ Estado de conductor eliminado exitosamente:', result);

            return {
                success: true,
                message: result.message || 'Estado de conductor eliminado correctamente',
                data: result.data || null
            };

        } catch (error) {
            console.error('❌ Error al eliminar estado de conductor:', error);
            throw error;
        }
    }

    /**
     * Maneja respuestas de error de la API
     * @param {Response} response - Respuesta del fetch
     */
    async handleErrorResponse(response) {
        let errorMessage = 'Error al eliminar el estado de conductor';

        try {
            const errorData = await response.json();
            errorMessage = errorData.message || errorData.error || errorMessage;
        } catch (parseError) {
            console.warn('No se pudo parsear el error del servidor');
        }

        switch (response.status) {
            case 400:
                throw new Error(`Solicitud inválida: ${errorMessage}`);
            case 401:
                throw new Error('No autorizado. Por favor, inicia sesión nuevamente.');
            case 403:
                throw new Error('No tienes permisos para eliminar estados de conductor.');
            case 404:
                throw new Error('Estado de conductor no encontrado.');
            case 409:
                throw new Error('No se puede eliminar el estado de conductor porque está siendo utilizado.');
            case 422:
                throw new Error(`Datos inválidos: ${errorMessage}`);
            case 500:
                throw new Error('Error interno del servidor. Intenta nuevamente más tarde.');
            default:
                throw new Error(`Error del servidor (${response.status}): ${errorMessage}`);
        }
    }

    /**
     * Valida si un estado de conductor puede ser eliminado
     * @param {Object} driverStatus - Datos del estado de conductor
     * @returns {Object} Resultado de la validación
     */
    validateDriverStatusForDeletion(driverStatus) {
        const errors = [];
        const warnings = [];

        if (!driverStatus) {
            errors.push('No se proporcionaron datos del estado de conductor');
            return { isValid: false, errors, warnings };
        }

        // Verificar si el estado está activo
        if (driverStatus.active) {
            warnings.push('El estado de conductor está activo y será desactivado');
        }

        // Verificar campos requeridos
        if (!driverStatus.id) {
            errors.push('ID del estado de conductor requerido');
        }

        if (!driverStatus.name) {
            warnings.push('Nombre del estado no disponible');
        }

        return {
            isValid: errors.length === 0,
            errors,
            warnings
        };
    }

    /**
     * Genera mensaje de confirmación personalizado
     * @param {Object} driverStatus - Datos del estado de conductor
     * @returns {Object} Configuración del modal de confirmación
     */
    generateConfirmationConfig(driverStatus) {
        const validation = this.validateDriverStatusForDeletion(driverStatus);
        
        if (!validation.isValid) {
            throw new Error(validation.errors.join(', '));
        }

        const statusText = driverStatus.active ? 'activo' : 'inactivo';
        const statusIcon = driverStatus.active ? 'fas fa-check-circle text-success' : 'fas fa-times-circle text-danger';

        return {
            title: 'Confirmar Eliminación',
            message: `
                <div class="text-center">
                    <div class="mb-3">
                        <i class="fas fa-exclamation-triangle fa-3x text-warning"></i>
                    </div>
                    <h4 class="mb-3">¿Estás seguro de eliminar este estado de conductor?</h4>
                    <div class="card bg-light">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="fas fa-user-check fa-2x text-primary"></i>
                                </div>
                                <div class="col text-start">
                                    <div class="fw-bold">${driverStatus.name}</div>
                                    <div class="text-muted small">
                                        <i class="${statusIcon} me-1"></i>
                                        Estado ${statusText}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    ${validation.warnings.length > 0 ? `
                        <div class="alert alert-warning mt-3 mb-0">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <div class="text-start">
                                    ${validation.warnings.map(warning => `<div>${warning}</div>`).join('')}
                                </div>
                            </div>
                        </div>
                    ` : ''}
                    <p class="text-muted mt-3 mb-0">
                        <strong>Esta acción no se puede deshacer.</strong>
                    </p>
                </div>
            `,
            confirmText: 'Sí, Eliminar',
            cancelText: 'Cancelar',
            confirmClass: 'btn-danger',
            icon: 'fas fa-trash'
        };
    }
}

// Hacer disponible globalmente
window.DriverStatusDeleteService = new DriverStatusDeleteService();

console.log('✅ DriverStatusDeleteService cargado y disponible globalmente');
