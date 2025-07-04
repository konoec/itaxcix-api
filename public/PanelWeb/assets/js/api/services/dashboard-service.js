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
                        // Usuario sin permisos de configuración - mostrar bienvenida
                        console.log('⚠️ Usuario sin permisos de configuración - mostrando bienvenida');
                        return {
                            success: true,
                            data: this.getWelcomeData(),
                            message: 'Bienvenido al sistema',
                            isWelcomeMode: true
                        };
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
     * Obtiene datos de bienvenida para usuarios sin permisos de configuración
     * @returns {Object} Datos de bienvenida
     */
    getWelcomeData() {
        console.log('🏠 Generando datos de bienvenida para usuario sin permisos de configuración');
        
        // Obtener módulos disponibles según permisos del usuario
        const availableModules = this.getAvailableModules();
        
        return {
            // Información de bienvenida
            welcomeMessage: 'Bienvenido a iTaxCix',
            subtitle: 'Sistema de Gestión Municipal',
            
            // Información del sistema
            systemInfo: {
                name: 'Panel iTaxCix',
                version: '1.0.0',
                description: 'Sistema integral de gestión municipal para la administración de servicios de transporte y ciudadanía.',
                status: 'Operativo'
            },
            
            // Módulos disponibles según permisos
            availableModules: availableModules,
            
            // Información adicional
            userMessage: availableModules.length > 1 
                ? 'Puedes acceder a los módulos listados según tus permisos asignados.'
                : 'Para acceder a funcionalidades adicionales, contacte con el administrador del sistema.',
            
            // Flags para el controlador
            isWelcomeMode: true,
            showStats: false
        };
    }

    /**
     * Obtiene los módulos disponibles para el usuario actual
     * @returns {Array} Lista de módulos disponibles
     */
    getAvailableModules() {
        const modules = [
            {
                name: 'Inicio',
                description: 'Panel principal del sistema',
                icon: 'fas fa-home',
                status: 'Activo'
            }
        ];

        // Si hay sistema de permisos disponible, agregar módulos según permisos
        if (typeof window.PermissionsService !== 'undefined' && window.PermissionsService) {
            try {
                const permissionsService = window.PermissionsService;
                const userPermissions = permissionsService.getUserPermissions();
                
                // Mapear permisos a módulos
                const permissionModules = {
                    'ADMISIÓN DE CONDUCTORES': {
                        name: 'Admisión de Conductores',
                        description: 'Gestión de admisión de conductores',
                        icon: 'fas fa-user-plus',
                        status: 'Activo'
                    },
                    'AUDITORIA': {
                        name: 'Auditoría',
                        description: 'Registro y reportes de auditoría',
                        icon: 'fas fa-clipboard-list',
                        status: 'Activo'
                    },
                    'TABLAS MAESTRAS': {
                        name: 'Tablas Maestras',
                        description: 'Gestión de datos maestros',
                        icon: 'fas fa-table',
                        status: 'Activo'
                    }
                };

                // Agregar módulos según permisos del usuario
                userPermissions.forEach(permission => {
                    if (permissionModules[permission]) {
                        modules.push(permissionModules[permission]);
                    }
                });
                
            } catch (error) {
                console.warn('⚠️ Error al obtener permisos del usuario:', error);
            }
        }

        return modules;
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
