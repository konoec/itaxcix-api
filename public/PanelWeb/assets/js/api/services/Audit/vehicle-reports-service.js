/**
 * Servicio para el manejo de reportes de vehículos
 * Proporciona métodos para obtener reportes de vehículos con filtros y paginación
 */
class VehicleReportsService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.endpoints = {
            vehicles: '/reports/vehicles'
        };
        console.log('🚗 VehicleReportsService inicializado');
    }

    /**
     * Obtiene un reporte paginado de vehículos con filtros avanzados
     * @param {Object} params - Parámetros de filtrado y paginación
     * @param {number} params.page - Número de página (default: 1)
     * @param {number} params.perPage - Elementos por página (default: 20)
     * @param {string} params.licensePlate - Placa del vehículo
     * @param {number} params.brandId - ID de la marca
     * @param {number} params.modelId - ID del modelo
     * @param {number} params.colorId - ID del color
     * @param {number} params.manufactureYearFrom - Año de fabricación desde
     * @param {number} params.manufactureYearTo - Año de fabricación hasta
     * @param {number} params.seatCount - Número de asientos
     * @param {number} params.passengerCount - Número de pasajeros
     * @param {number} params.fuelTypeId - ID del tipo de combustible
     * @param {number} params.vehicleClassId - ID de la clase de vehículo
     * @param {number} params.categoryId - ID de la categoría
     * @param {boolean} params.active - Estado activo
     * @param {number} params.companyId - ID de la empresa
     * @param {number} params.districtId - ID del distrito
     * @param {number} params.statusId - ID del estado
     * @param {number} params.procedureTypeId - ID del tipo de procedimiento
     * @param {number} params.modalityId - ID de la modalidad
     * @param {string} params.sortBy - Campo para ordenar (default: 'licensePlate')
     * @param {string} params.sortDirection - Dirección de ordenamiento (ASC|DESC, default: 'ASC')
     * @returns {Promise<Object>} Respuesta con datos paginados de vehículos
     */
    async getVehicleReports(params = {}) {
        try {
            // Verificar autenticación
            const token = sessionStorage.getItem('authToken') || localStorage.getItem('authToken');
            if (!token) {
                throw new Error('Token de autenticación no encontrado');
            }

            console.log('🔄 Obteniendo reportes de vehículos con parámetros:', params);

            // Construir parámetros de consulta de manera más robusta
            const queryParams = new URLSearchParams();

            // Parámetros de paginación - siempre incluir con valores por defecto
            queryParams.append('page', params.page || 1);
            queryParams.append('perPage', params.perPage || 20);

            // Filtros de vehículo - solo agregar si tienen valores válidos
            if (params.licensePlate && typeof params.licensePlate === 'string') {
                queryParams.append('licensePlate', params.licensePlate);
            }
            
            if (params.brandId && Number.isInteger(params.brandId)) {
                queryParams.append('brandId', params.brandId.toString());
            }
            
            if (params.modelId && Number.isInteger(params.modelId)) {
                queryParams.append('modelId', params.modelId.toString());
            }
            
            if (params.colorId && Number.isInteger(params.colorId)) {
                queryParams.append('colorId', params.colorId.toString());
            }
            
            if (params.manufactureYearFrom && Number.isInteger(params.manufactureYearFrom)) {
                queryParams.append('manufactureYearFrom', params.manufactureYearFrom.toString());
            }
            
            if (params.manufactureYearTo && Number.isInteger(params.manufactureYearTo)) {
                queryParams.append('manufactureYearTo', params.manufactureYearTo.toString());
            }
            
            if (params.seatCount && Number.isInteger(params.seatCount)) {
                queryParams.append('seatCount', params.seatCount.toString());
            }
            
            if (params.passengerCount && Number.isInteger(params.passengerCount)) {
                queryParams.append('passengerCount', params.passengerCount.toString());
            }
            
            if (params.fuelTypeId && Number.isInteger(params.fuelTypeId)) {
                queryParams.append('fuelTypeId', params.fuelTypeId.toString());
            }
            
            if (params.vehicleClassId && Number.isInteger(params.vehicleClassId)) {
                queryParams.append('vehicleClassId', params.vehicleClassId.toString());
            }
            
            if (params.categoryId && Number.isInteger(params.categoryId)) {
                queryParams.append('categoryId', params.categoryId.toString());
            }
            
            if (typeof params.active === 'boolean') {
                queryParams.append('active', params.active.toString());
            }

            // Filtros adicionales
            if (params.companyId && Number.isInteger(params.companyId)) {
                queryParams.append('companyId', params.companyId.toString());
            }
            
            if (params.districtId && Number.isInteger(params.districtId)) {
                queryParams.append('districtId', params.districtId.toString());
            }
            
            if (params.statusId && Number.isInteger(params.statusId)) {
                queryParams.append('statusId', params.statusId.toString());
            }
            
            if (params.procedureTypeId && Number.isInteger(params.procedureTypeId)) {
                queryParams.append('procedureTypeId', params.procedureTypeId.toString());
            }
            
            if (params.modalityId && Number.isInteger(params.modalityId)) {
                queryParams.append('modalityId', params.modalityId.toString());
            }

            // Parámetros de ordenamiento - siempre incluir con valores por defecto
            queryParams.append('sortBy', params.sortBy || 'licensePlate');
            queryParams.append('sortDirection', (params.sortDirection === 'DESC') ? 'DESC' : 'ASC');

            // Construir URL completa
            const url = `${this.baseUrl}${this.endpoints.vehicles}?${queryParams.toString()}`;
            console.log('📡 URL de solicitud:', url);

            // Realizar petición
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                let errorMessage = `Error HTTP: ${response.status}`;
                let errorDetails = null;
                
                try {
                    const errorData = await response.json();
                    console.error('❌ Error del servidor completo:', errorData);
                    
                    errorDetails = errorData;
                    errorMessage += ` - ${errorData.message || errorData.error || response.statusText}`;
                    
                    // Si hay detalles específicos de validación, incluirlos
                    if (errorData.data && errorData.data.errors) {
                        console.error('❌ Errores de validación:', errorData.data.errors);
                        errorMessage += `. Errores de validación: ${JSON.stringify(errorData.data.errors)}`;
                    }
                    
                } catch (parseError) {
                    console.error('❌ Error al parsear respuesta de error:', parseError);
                    errorMessage += ` - ${response.statusText}`;
                }
                
                // Mensajes específicos por código de error
                if (response.status === 400) {
                    console.error('❌ Error 400 - Parámetros enviados:', params);
                    console.error('❌ Error 400 - URL generada:', url);
                    errorMessage = `Petición inválida (400). Verifique los parámetros de filtrado. ${errorMessage}`;
                } else if (response.status === 401) {
                    throw new Error('No autorizado. Token de autenticación inválido o expirado');
                } else if (response.status === 403) {
                    throw new Error('No tiene permisos para acceder a los reportes de vehículos');
                } else if (response.status === 404) {
                    throw new Error('Endpoint de reportes de vehículos no encontrado');
                }
                
                const error = new Error(errorMessage);
                error.status = response.status;
                error.details = errorDetails;
                throw error;
            }

            const data = await response.json();
            console.log('✅ Respuesta del servidor:', data);

            // Devolver la respuesta directamente de la API
            return data;

        } catch (error) {
            console.error('❌ Error al obtener reportes de vehículos:', error);
            throw error;
        }
    }

    /**
     * Exporta los reportes de vehículos a CSV
     * @param {Object} filters - Filtros aplicados
     * @returns {Promise<Object>} Respuesta con el archivo CSV
     * 
     * NOTA: Funcionalidad deshabilitada por requerimiento del usuario
     */
    /*
    async exportToCsv(filters = {}) {
        try {
            console.log('📥 Exportando reportes de vehículos a CSV con filtros:', filters);

            // Obtener todos los datos sin paginación para exportar
            const allDataParams = {
                ...filters,
                page: 1,
                perPage: 1000 // Obtener muchos registros para exportar
            };

            const response = await this.getVehicleReports(allDataParams);

            if (!response.success || !response.data || !response.data.data) {
                throw new Error('No hay datos para exportar');
            }

            // Convertir datos a CSV
            const vehicles = response.data.data;
            const csvContent = this.convertToCSV(vehicles);

            // Crear blob y descargar
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            
            link.setAttribute('href', url);
            link.setAttribute('download', `reporte_vehiculos_${new Date().toISOString().slice(0, 10)}.csv`);
            link.style.visibility = 'hidden';
            
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            console.log('✅ Exportación a CSV completada');
            return {
                success: true,
                message: 'Reportes de vehículos exportados exitosamente'
            };

        } catch (error) {
            console.error('❌ Error al exportar reportes de vehículos:', error);
            return {
                success: false,
                message: error.message || 'Error al exportar los reportes de vehículos'
            };
        }
    }
    */

    /**
     * Convierte array de vehículos a formato CSV
     * @param {Array} vehicles - Array de vehículos
     * @returns {string} Contenido CSV
     * 
     * NOTA: Funcionalidad deshabilitada por requerimiento del usuario
     */
    /*
    convertToCSV(vehicles) {
        if (!vehicles || vehicles.length === 0) {
            return '';
        }

        // Headers del CSV
        const headers = [
            'ID',
            'Placa',
            'Marca',
            'Modelo',
            'Color',
            'Año Fabricación',
            'Asientos',
            'Pasajeros',
            'Tipo Combustible',
            'Clase Vehículo',
            'Categoría',
            'Estado',
            'Empresa',
            'Distrito',
            'Estado Vehículo',
            'Tipo Procedimiento',
            'Modalidad'
        ].join(',');

        // Filas de datos
        const rows = vehicles.map(vehicle => [
            vehicle.id || '',
            `"${vehicle.licensePlate || ''}"`,
            `"${vehicle.brandName || ''}"`,
            `"${vehicle.modelName || ''}"`,
            `"${vehicle.colorName || ''}"`,
            vehicle.manufactureYear || '',
            vehicle.seatCount || '',
            vehicle.passengerCount || '',
            `"${vehicle.fuelTypeName || ''}"`,
            `"${vehicle.vehicleClassName || ''}"`,
            `"${vehicle.categoryName || ''}"`,
            vehicle.active ? 'Activo' : 'Inactivo',
            `"${vehicle.companyName || ''}"`,
            `"${vehicle.districtName || ''}"`,
            `"${vehicle.statusName || ''}"`,
            `"${vehicle.procedureTypeName || ''}"`,
            `"${vehicle.modalityName || ''}"`
        ].join(','));

        return [headers, ...rows].join('\n');
    }
    */
}

// Crear instancia global del servicio
window.vehicleReportsService = new VehicleReportsService();

console.log('🚗 Servicio de reportes de vehículos cargado y disponible globalmente');
