/**
 * Category Update Service
 * Servicio para actualizar categorías de vehículo existentes
 *
 * Funciones principales:
 * - Actualización de categoría por ID
 * - Validación de datos antes de actualizar
 * - Manejo de errores específicos
 */
class CategoryUpdateService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.endpoint = '/admin/categories';
        console.log('🔄 Inicializando CategoryUpdateService...');
    }

    /**
     * Actualiza una categoría existente
     * @param {number} id - ID de la categoría
     * @param {Object} categoryData - Datos de la categoría a actualizar
     * @param {string} categoryData.name - Nombre de la categoría
     * @param {boolean} categoryData.active - Estado activo/inactivo
     * @returns {Promise<Object>} Respuesta de la API
     */
    async updateCategory(id, categoryData) {
        console.log(`🔄 Actualizando categoría ${id}:`, categoryData);
        try {
            // Validar datos antes de enviar
            const validationResult = this.validateCategoryData(categoryData);
            if (!validationResult.isValid) {
                throw new Error(validationResult.message);
            }

            const token = sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
            if (!token) {
                throw new Error('No hay token de autenticación');
            }

            // Preparar datos para envío
            const payload = {
                name: categoryData.name.trim(),
                active: !!categoryData.active
            };

            console.log('📤 Payload a enviar:', payload);

            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 segundos para updates

            try {
                const response = await fetch(`${this.baseUrl}${this.endpoint}/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload),
                    signal: controller.signal
                });

                clearTimeout(timeoutId);

                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(`HTTP ${response.status}: ${errorData.message || 'Error al actualizar categoría'}`);
                }

                const result = await response.json();
                console.log('✅ Categoría actualizada exitosamente:', result);
                return result;
            } catch (fetchError) {
                clearTimeout(timeoutId);
                if (fetchError.name === 'AbortError') {
                    throw new Error('La solicitud tardó demasiado en responder');
                }
                throw fetchError;
            }
        } catch (error) {
            console.error('❌ Error actualizando categoría:', error);
            // Manejo específico de errores comunes
            if (error.message.includes('404')) {
                throw new Error('La categoría no existe');
            } else if (error.message.includes('403')) {
                throw new Error('No tienes permisos para actualizar esta categoría');
            } else if (error.message.includes('409')) {
                throw new Error('Ya existe una categoría con estos datos');
            } else if (error.message.includes('422')) {
                throw new Error('Los datos proporcionados no son válidos');
            } else if (error.message.includes('500')) {
                throw new Error('Error interno del servidor. Inténtelo más tarde');
            }
            throw error;
        }
    }

    /**
     * Valida los datos de la categoría antes de actualizar
     * @param {Object} categoryData - Datos a validar
     * @returns {Object} Resultado de la validación
     */
    validateCategoryData(categoryData) {
        console.log('🔍 Validando datos de la categoría:', categoryData);
        if (!categoryData) {
            return {
                isValid: false,
                message: 'No se proporcionaron datos de la categoría'
            };
        }
        if (!categoryData.name || typeof categoryData.name !== 'string') {
            return {
                isValid: false,
                message: 'El nombre de la categoría es requerido'
            };
        }
        const trimmedName = categoryData.name.trim();
        if (trimmedName.length === 0) {
            return {
                isValid: false,
                message: 'El nombre de la categoría no puede estar vacío'
            };
        }
        if (trimmedName.length > 100) {
            return {
                isValid: false,
                message: 'El nombre no puede exceder 100 caracteres'
            };
        }
        if (typeof categoryData.active !== 'boolean') {
            return {
                isValid: false,
                message: 'El estado activo/inactivo es requerido'
            };
        }
        console.log('✅ Validación exitosa');
        return {
            isValid: true,
            message: 'Datos válidos'
        };
    }
}

// Exportar para uso global
window.CategoryUpdateService = new CategoryUpdateService();
console.log('✅ CategoryUpdateService cargado correctamente');
