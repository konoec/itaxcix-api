/**
 * Servicio para reportes de usuarios administrativos
 * Maneja las peticiones HTTP para obtener reportes de usuarios con filtros avanzados
 */
class UserReportsService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.endpoints = {
            users: '/reports/users'
        };
        console.log('👥 UserReportsService inicializado');
    }

    /**
     * Obtiene reportes de usuarios con filtros
     * @param {Object} filters - Filtros a aplicar
     * @returns {Promise<Object>} Respuesta de la API
     */
    async getUserReports(filters = {}) {
        try {
            // Verificar autenticación
            const token = sessionStorage.getItem('authToken') || localStorage.getItem('authToken');
            if (!token) {
                throw new Error('Token de autenticación no encontrado');
            }

            console.log('📊 Obteniendo reportes de usuarios con filtros:', filters);
            
            // Construir parámetros de consulta
            const params = new URLSearchParams();
            
            // Paginación
            if (filters.page) {
                params.append('page', filters.page.toString());
            }
            if (filters.perPage) {
                params.append('perPage', filters.perPage.toString());
            }
            
            // Filtros de texto
            if (filters.name && filters.name.trim()) {
                params.append('name', filters.name.trim());
            }
            if (filters.lastName && filters.lastName.trim()) {
                params.append('lastName', filters.lastName.trim());
            }
            if (filters.document && filters.document.trim()) {
                params.append('document', filters.document.trim());
            }
            if (filters.email && filters.email.trim()) {
                params.append('email', filters.email.trim());
            }
            if (filters.phone && filters.phone.trim()) {
                params.append('phone', filters.phone.trim());
            }
            
            // Filtros numéricos (IDs)
            if (filters.documentTypeId && Number.isInteger(filters.documentTypeId)) {
                params.append('documentTypeId', filters.documentTypeId.toString());
            }
            if (filters.statusId && Number.isInteger(filters.statusId)) {
                params.append('statusId', filters.statusId.toString());
            }
            
            // Filtro boolean
            if (typeof filters.active === 'boolean') {
                params.append('active', filters.active.toString());
            }
            
            // Filtros de fecha
            if (filters.validationStartDate) {
                params.append('validationStartDate', filters.validationStartDate);
            }
            if (filters.validationEndDate) {
                params.append('validationEndDate', filters.validationEndDate);
            }
            
            // Ordenamiento
            if (filters.sortBy) {
                params.append('sortBy', filters.sortBy);
            }
            if (filters.sortDirection) {
                params.append('sortDirection', filters.sortDirection);
            }
            
            const url = `${this.baseUrl}${this.endpoints.users}?${params.toString()}`;
            console.log('🔗 URL de petición:', url);
            
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                const errorText = await response.text();
                console.error('❌ Error en respuesta HTTP:', response.status, errorText);
                throw new Error(`Error ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            console.log('✅ Respuesta exitosa de reportes de usuarios:', data);
            
            return data;
            
        } catch (error) {
            console.error('❌ Error en getUserReports:', error);
            throw error;
        }
    }

    /**
     * Exporta los reportes de usuarios a CSV
     * @param {Object} filters - Filtros a aplicar para la exportación
     * @returns {Promise<Blob>} Archivo CSV
     */
    async exportUserReports(filters = {}) {
        try {
            // Verificar autenticación
            const token = sessionStorage.getItem('authToken') || localStorage.getItem('authToken');
            if (!token) {
                throw new Error('Token de autenticación no encontrado');
            }

            console.log('📤 Exportando reportes de usuarios a CSV con filtros:', filters);
            
            // Construir parámetros de consulta (sin paginación para exportar todo)
            const params = new URLSearchParams();
            
            // Filtros de texto
            if (filters.name && filters.name.trim()) {
                params.append('name', filters.name.trim());
            }
            if (filters.lastName && filters.lastName.trim()) {
                params.append('lastName', filters.lastName.trim());
            }
            if (filters.document && filters.document.trim()) {
                params.append('document', filters.document.trim());
            }
            if (filters.email && filters.email.trim()) {
                params.append('email', filters.email.trim());
            }
            if (filters.phone && filters.phone.trim()) {
                params.append('phone', filters.phone.trim());
            }
            
            // Filtros numéricos (IDs)
            if (filters.documentTypeId && Number.isInteger(filters.documentTypeId)) {
                params.append('documentTypeId', filters.documentTypeId.toString());
            }
            if (filters.statusId && Number.isInteger(filters.statusId)) {
                params.append('statusId', filters.statusId.toString());
            }
            
            // Filtro boolean
            if (typeof filters.active === 'boolean') {
                params.append('active', filters.active.toString());
            }
            
            // Filtros de fecha
            if (filters.validationStartDate) {
                params.append('validationStartDate', filters.validationStartDate);
            }
            if (filters.validationEndDate) {
                params.append('validationEndDate', filters.validationEndDate);
            }
            
            // Ordenamiento
            if (filters.sortBy) {
                params.append('sortBy', filters.sortBy);
            }
            if (filters.sortDirection) {
                params.append('sortDirection', filters.sortDirection);
            }
            
            // Parámetros de exportación
            params.append('export', 'csv');
            params.append('perPage', '1000'); // Exportar hasta 1000 registros
            
            const url = `${this.baseUrl}${this.endpoints.users}?${params.toString()}`;
            console.log('🔗 URL de exportación:', url);
            
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'text/csv'
                }
            });

            if (!response.ok) {
                const errorText = await response.text();
                console.error('❌ Error en exportación:', response.status, errorText);
                throw new Error(`Error ${response.status}: ${response.statusText}`);
            }

            const blob = await response.blob();
            console.log('✅ Exportación exitosa de reportes de usuarios');
            
            return blob;
            
        } catch (error) {
            console.error('❌ Error en exportUserReports:', error);
            throw error;
        }
    }

    /**
     * Obtiene las estadísticas de usuarios
     * @param {Object} filters - Filtros a aplicar para las estadísticas
     * @returns {Promise<Object>} Estadísticas de usuarios
     */
    async getUserStats(filters = {}) {
        try {
            // Verificar autenticación
            const token = sessionStorage.getItem('authToken') || localStorage.getItem('authToken');
            if (!token) {
                throw new Error('Token de autenticación no encontrado');
            }

            console.log('📈 Obteniendo estadísticas de usuarios con filtros:', filters);
            
            // Construir parámetros de consulta
            const params = new URLSearchParams();
            
            // Filtros de texto
            if (filters.name && filters.name.trim()) {
                params.append('name', filters.name.trim());
            }
            if (filters.lastName && filters.lastName.trim()) {
                params.append('lastName', filters.lastName.trim());
            }
            if (filters.document && filters.document.trim()) {
                params.append('document', filters.document.trim());
            }
            if (filters.email && filters.email.trim()) {
                params.append('email', filters.email.trim());
            }
            if (filters.phone && filters.phone.trim()) {
                params.append('phone', filters.phone.trim());
            }
            
            // Filtros numéricos (IDs)
            if (filters.documentTypeId && Number.isInteger(filters.documentTypeId)) {
                params.append('documentTypeId', filters.documentTypeId.toString());
            }
            if (filters.statusId && Number.isInteger(filters.statusId)) {
                params.append('statusId', filters.statusId.toString());
            }
            
            // Filtro boolean
            if (typeof filters.active === 'boolean') {
                params.append('active', filters.active.toString());
            }
            
            // Filtros de fecha
            if (filters.validationStartDate) {
                params.append('validationStartDate', filters.validationStartDate);
            }
            if (filters.validationEndDate) {
                params.append('validationEndDate', filters.validationEndDate);
            }
            
            params.append('stats', 'true');
            
            const url = `${this.baseUrl}${this.endpoints.users}?${params.toString()}`;
            
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });

            if (!response.ok) {
                const errorText = await response.text();
                console.error('❌ Error en estadísticas:', response.status, errorText);
                throw new Error(`Error ${response.status}: ${response.statusText}`);
            }

            const data = await response.json();
            console.log('✅ Estadísticas de usuarios obtenidas:', data);
            
            return data;
            
        } catch (error) {
            console.error('❌ Error en getUserStats:', error);
            throw error;
        }
    }
}

// Crear instancia global del servicio
window.userReportsService = new UserReportsService();

// Hacer la clase disponible globalmente
window.UserReportsService = UserReportsService;
