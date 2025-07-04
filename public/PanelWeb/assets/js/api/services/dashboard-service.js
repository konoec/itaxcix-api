/**
 * Servicio para gestionar las estadísticas del dashboard
 */
class DashboardService {
    constructor() {
        this.apiUrl = 'https://149.130.161.148/api/v1/dashboard';
        console.log('📊 DashboardService inicializado');
    }

    /**
     * Obtiene las estadísticas del dashboard
     * @returns {Promise<Object>} Respuesta con las estadísticas
     */
    async getStats() {
        try {
            console.log('📊 Obteniendo estadísticas del dashboard...');
            
            // Obtener token de autenticación
            const token = sessionStorage.getItem('authToken') || localStorage.getItem('authToken');
            
            if (!token) {
                throw new Error('Token de autenticación no encontrado');
            }

            const response = await fetch(`${this.apiUrl}/stats`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });

            console.log(`📊 Respuesta del servidor: ${response.status}`);

            if (!response.ok) {
                let errorMessage = 'Error al obtener estadísticas';
                
                switch (response.status) {
                    case 401:
                        errorMessage = 'No autorizado - Token inválido o expirado';
                        // Limpiar tokens inválidos
                        sessionStorage.removeItem('authToken');
                        localStorage.removeItem('authToken');
                        // Redirigir al login si es necesario
                        if (window.location.pathname !== '/index.html') {
                            setTimeout(() => {
                                window.location.href = '../../index.html';
                            }, 2000);
                        }
                        break;
                    case 403:
                        errorMessage = 'Acceso denegado - Sin permisos de CONFIGURACIÓN';
                        break;
                    case 500:
                        errorMessage = 'Error interno del servidor';
                        break;
                    default:
                        errorMessage = `Error del servidor: ${response.status}`;
                }
                
                throw new Error(errorMessage);
            }

            const data = await response.json();
            console.log('📊 Estadísticas obtenidas exitosamente:', data);

            // Validar estructura de respuesta
            if (!data.success) {
                throw new Error(data.message || 'Error en la respuesta del servidor');
            }

            // Validar que existan los datos esperados
            if (!data.data) {
                throw new Error('Datos de estadísticas no encontrados en la respuesta');
            }

            return {
                success: true,
                data: data.data,
                message: data.message || 'Estadísticas obtenidas correctamente'
            };

        } catch (error) {
            console.error('❌ Error al obtener estadísticas del dashboard:', error);
            return {
                success: false,
                message: error.message,
                data: null
            };
        }
    }

    /**
     * Formatea números para mostrar en el dashboard
     * @param {number} number - Número a formatear
     * @returns {string} Número formateado
     */
    formatNumber(number) {
        if (typeof number !== 'number') return '0';
        
        if (number >= 1000000) {
            return (number / 1000000).toFixed(1) + 'M';
        } else if (number >= 1000) {
            return (number / 1000).toFixed(1) + 'K';
        }
        return number.toLocaleString();
    }

    /**
     * Formatea porcentajes para mostrar en el dashboard
     * @param {number} percentage - Porcentaje a formatear
     * @returns {string} Porcentaje formateado
     */
    formatPercentage(percentage) {
        if (typeof percentage !== 'number') return '0%';
        return percentage.toFixed(1) + '%';
    }

    /**
     * Calcula el color del indicador basado en el porcentaje
     * @param {number} percentage - Porcentaje
     * @returns {string} Clase CSS para el color
     */
    getIndicatorColor(percentage) {
        if (percentage >= 80) return 'success';
        if (percentage >= 60) return 'warning';
        if (percentage >= 40) return 'info';
        return 'danger';
    }
}

// Exportar para uso global
if (typeof window !== 'undefined') {
    window.DashboardService = DashboardService;
    console.log('✅ DashboardService exportado globalmente');
}
