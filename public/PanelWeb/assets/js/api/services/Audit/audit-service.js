/**
 * Servicio para manejo de auditoría
 * Proporciona métodos para obtener, filtrar y gestionar registros de auditoría
 */
class AuditService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.endpoints = {
            audit: '/audit'
        };
    }

    /**
     * Obtiene la lista completa de registros de auditoría
     * @param {Object} filters - Filtros opcionales (fecha, usuario, acción, etc.)
     * @returns {Promise<Object>} Respuesta con registros de auditoría y paginación
     */
    async getAuditLogs(filters = {}) {
        try {
            const token = sessionStorage.getItem('authToken') || localStorage.getItem('authToken');
            if (!token) {
                throw new Error('Token de autenticación no encontrado');
            }

            let url = `${this.baseUrl}${this.endpoints.audit}`;
            
            // Agregar filtros como parámetros de consulta según la documentación de la API
            const queryParams = new URLSearchParams();
            
            // Parámetros de paginación
            if (filters.page) queryParams.append('page', filters.page);
            if (filters.limit) queryParams.append('perPage', filters.limit); // API usa 'perPage' no 'limit'
            
            // Filtros específicos
            if (filters.affectedTable || filters.module) {
                queryParams.append('affectedTable', filters.affectedTable || filters.module);
            }
            if (filters.operation || filters.action) {
                queryParams.append('operation', filters.operation || filters.action);
            }
            if (filters.systemUser || filters.userId) {
                queryParams.append('systemUser', filters.systemUser || filters.userId);
            }
            if (filters.dateFrom || filters.startDate) {
                queryParams.append('dateFrom', filters.dateFrom || filters.startDate);
            }
            if (filters.dateTo || filters.endDate) {
                queryParams.append('dateTo', filters.dateTo || filters.endDate);
            }
            
            // Parámetros de ordenamiento
            if (filters.sortBy) queryParams.append('sortBy', filters.sortBy);
            if (filters.sortDirection) queryParams.append('sortDirection', filters.sortDirection);

            if (queryParams.toString()) {
                url += `?${queryParams.toString()}`;
            }

            console.log('🌐 Llamando a API de auditoría:', url);

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({ message: 'Error desconocido' }));
                throw new Error(errorData.message || `Error ${response.status}`);
            }

            const data = await response.json();
            console.log('📊 Respuesta de API de auditoría:', data);
            return data;
        } catch (error) {
            console.error('Error al obtener registros de auditoría:', error);
            throw error;
        }
    }

    /**
     * Obtiene un registro de auditoría específico por ID
     * @param {number} auditId - ID del registro de auditoría
     * @returns {Promise<Object>} Registro de auditoría
     */
    async getAuditLogById(auditId) {
        try {
            const token = sessionStorage.getItem('authToken') || localStorage.getItem('authToken');
            if (!token) {
                throw new Error('Token de autenticación no encontrado');
            }

            console.log('🔍 Obteniendo registro de auditoría ID:', auditId);

            const response = await fetch(`${this.baseUrl}${this.endpoints.audit}/${auditId}`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                if (response.status === 404) {
                    throw new Error('Registro de auditoría no encontrado');
                }
                
                const errorData = await response.json().catch(() => ({ message: 'Error desconocido' }));
                throw new Error(errorData.message || `Error ${response.status}`);
            }

            const data = await response.json();
            console.log('📊 Detalles de auditoría obtenidos:', data);
            return data;
        } catch (error) {
            console.error('❌ Error al obtener registro de auditoría:', error);
            throw error;
        }
    }

    /**
     * Obtiene estadísticas de auditoría
     * @param {Object} statsParams - Parámetros para las estadísticas
     * @returns {Promise<Object>} Estadísticas de auditoría
     */
    async getAuditStatistics(statsParams = {}) {
        try {
            const token = sessionStorage.getItem('authToken') || localStorage.getItem('authToken');
            if (!token) {
                throw new Error('Token de autenticación no encontrado');
            }

            let url = `${this.baseUrl}${this.endpoints.auditLogs}/statistics`;
            
            // Agregar parámetros de estadísticas
            const queryParams = new URLSearchParams();
            if (statsParams.startDate) queryParams.append('startDate', statsParams.startDate);
            if (statsParams.endDate) queryParams.append('endDate', statsParams.endDate);
            if (statsParams.groupBy) queryParams.append('groupBy', statsParams.groupBy);

            if (queryParams.toString()) {
                url += `?${queryParams.toString()}`;
            }

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({ message: 'Error desconocido' }));
                throw new Error(errorData.message || `Error ${response.status}`);
            }

            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error al obtener estadísticas de auditoría:', error);
            throw error;
        }
    }

    /**
     * Formatea una fecha para mostrar en la interfaz
     * @param {string} dateString - Fecha en formato ISO
     * @returns {string} Fecha formateada
     */
    formatDate(dateString) {
        if (!dateString) return 'N/A';
        
        try {
            const date = new Date(dateString);
            return date.toLocaleString('es-ES', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
        } catch (error) {
            console.warn('Error al formatear fecha:', error);
            return dateString;
        }
    }

    /**
     * Obtiene el color del badge según el tipo de acción
     * @param {string} action - Tipo de acción
     * @returns {string} Clase CSS para el color del badge
     */
    getActionBadgeClass(action) {
        const actionMap = {
            'CREATE': 'badge-success',
            'UPDATE': 'badge-warning',
            'DELETE': 'badge-danger',
            'LOGIN': 'badge-info',
            'LOGOUT': 'badge-secondary',
            'VIEW': 'badge-primary',
            'EXPORT': 'badge-info',
            'IMPORT': 'badge-warning'
        };

        return actionMap[action?.toUpperCase()] || 'badge-secondary';
    }

    /**
     * Exporta los registros de auditoría a CSV
     * @param {Object} filters - Filtros opcionales para la exportación
     * @returns {Promise<Blob>} Archivo CSV como blob
     */
    async exportAuditLogsToCSV(filters = {}) {
        try {
            const token = sessionStorage.getItem('authToken') || localStorage.getItem('authToken');
            if (!token) {
                throw new Error('Token de autenticación no encontrado');
            }

            console.log('📥 Exportando registros de auditoría a CSV...');

            let url = `${this.baseUrl}${this.endpoints.audit}/export`;
            
            // Agregar filtros como parámetros de consulta si existen
            const queryParams = new URLSearchParams();
            
            if (filters.affectedTable) {
                queryParams.append('affectedTable', filters.affectedTable);
            }
            if (filters.operation) {
                queryParams.append('operation', filters.operation);
            }
            if (filters.systemUser) {
                queryParams.append('systemUser', filters.systemUser);
            }
            if (filters.dateFrom) {
                queryParams.append('dateFrom', filters.dateFrom);
            }
            if (filters.dateTo) {
                queryParams.append('dateTo', filters.dateTo);
            }
            
            if (queryParams.toString()) {
                url += `?${queryParams.toString()}`;
            }

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                if (response.status === 404) {
                    throw new Error('Endpoint de exportación no encontrado');
                }
                if (response.status === 403) {
                    throw new Error('No tiene permisos para exportar registros de auditoría');
                }
                throw new Error(`Error al exportar: ${response.status} ${response.statusText}`);
            }

            // Retornar el blob del CSV
            const blob = await response.blob();
            
            console.log('✅ Exportación de auditoría completada');
            return blob;

        } catch (error) {
            console.error('❌ Error al exportar registros de auditoría:', error);
            throw error;
        }
    }
}

// Hacer el servicio disponible globalmente
window.AuditService = AuditService;

// Crear instancia global
if (typeof window !== 'undefined') {
    window.auditService = new AuditService();
}
