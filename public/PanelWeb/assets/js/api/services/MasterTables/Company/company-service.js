/**
 * Servicio para la gestión de compañías/empresas
 */
class CompanyService {
    constructor() {
        this.baseUrl = 'https://149.130.161.148/api/v1';
        this.endpoint = '/admin/companies';
        
        // Cache para optimizar rendimiento
        this.companiesCache = new Map();
        this.cacheExpiry = 5 * 60 * 1000; // 5 minutos
        
        console.log('🏢 CompanyService inicializado con base URL:', this.baseUrl);
    }

    /**
     * Obtiene la lista de empresas con filtros y paginación
     * @param {Object} params - Parámetros de búsqueda y filtros
     * @param {number} params.page - Número de página (default: 1)
     * @param {number} params.perPage - Elementos por página (default: 15, max: 100)
     * @param {string} params.search - Búsqueda global en RUC y nombre
     * @param {string} params.ruc - Filtro por RUC exacto
     * @param {string} params.name - Filtro por nombre (contiene)
     * @param {boolean} params.active - Filtro por estado activo
     * @param {string} params.sortBy - Campo de ordenamiento (id, ruc, name, active)
     * @param {string} params.sortDirection - Dirección de ordenamiento (asc, desc)
     * @returns {Promise<Object>} - Respuesta con lista paginada de empresas
     */
    async getCompanies(params = {}) {
        console.log('🏢 Obteniendo lista de empresas...', params);

        try {
            const token = sessionStorage.getItem("authToken");
            if (!token) {
                throw new Error('No hay token de autenticación');
            }

            // Construir parámetros de consulta
            const queryParams = this.buildQueryParams(params);
            const cacheKey = `companies_${queryParams}`;

            // Verificar cache
            if (this.companiesCache.has(cacheKey)) {
                const cached = this.companiesCache.get(cacheKey);
                if (Date.now() - cached.timestamp < this.cacheExpiry) {
                    console.log('🏢 Usando datos de empresas desde cache');
                    return cached.data;
                }
            }

            // Realizar petición HTTP
            const url = `${this.baseUrl}${this.endpoint}?${queryParams}`;
            console.log('🌐 Realizando petición a:', url);

            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 10000); // 10 segundos

            try {
                const response = await fetch(url, {
                    method: 'GET',
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
                    throw new Error(`HTTP ${response.status}: ${errorData.message || 'Error al obtener empresas'}`);
                }

                const result = await response.json();
                console.log('✅ Empresas obtenidas exitosamente:', result);

                // Guardar en cache
                this.companiesCache.set(cacheKey, {
                    data: result,
                    timestamp: Date.now()
                });

                return result;

            } catch (fetchError) {
                clearTimeout(timeoutId);
                
                if (fetchError.name === 'AbortError') {
                    throw new Error('Timeout: La petición tardó demasiado');
                }
                throw fetchError;
            }

        } catch (error) {
            console.error('❌ Error al obtener empresas:', error);
            throw error;
        }
    }

    /**
     * Obtiene una empresa específica por su ID
     * @param {number} id - ID de la empresa
     * @returns {Promise<Object>} - Datos de la empresa
     */
    async getCompanyById(id) {
        console.log('🏢 Obteniendo empresa por ID:', id);

        try {
            const token = sessionStorage.getItem("authToken");
            if (!token) {
                throw new Error('No hay token de autenticación');
            }

            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 8000);

            try {
                const response = await fetch(`${this.baseUrl}${this.endpoint}/${id}`, {
                    method: 'GET',
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
                    throw new Error(`HTTP ${response.status}: ${errorData.message || 'Error al obtener empresa'}`);
                }

                const result = await response.json();
                console.log('✅ Empresa obtenida exitosamente:', result);

                return result;

            } catch (fetchError) {
                clearTimeout(timeoutId);
                
                if (fetchError.name === 'AbortError') {
                    throw new Error('Timeout: La petición tardó demasiado');
                }
                throw fetchError;
            }

        } catch (error) {
            console.error('❌ Error al obtener empresa:', error);
            throw error;
        }
    }

    /**
     * Crea una nueva empresa
     * @param {Object} companyData - Datos de la empresa
     * @returns {Promise<Object>} - Respuesta del servidor
     */
    async createCompany(companyData) {
        console.log('🏢 Creando nueva empresa:', companyData);

        try {
            const token = sessionStorage.getItem("authToken");
            if (!token) {
                throw new Error('No hay token de autenticación');
            }

            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 10000);

            try {
                const response = await fetch(`${this.baseUrl}${this.endpoint}`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(companyData),
                    signal: controller.signal
                });

                clearTimeout(timeoutId);

                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(`HTTP ${response.status}: ${errorData.message || 'Error al crear empresa'}`);
                }

                const result = await response.json();
                console.log('✅ Empresa creada exitosamente:', result);

                // Limpiar cache
                this.clearCache();

                return result;

            } catch (fetchError) {
                clearTimeout(timeoutId);
                
                if (fetchError.name === 'AbortError') {
                    throw new Error('Timeout: La petición tardó demasiado');
                }
                throw fetchError;
            }

        } catch (error) {
            console.error('❌ Error al crear empresa:', error);
            throw error;
        }
    }

    /**
     * Actualiza una empresa existente
     * @param {number} id - ID de la empresa
     * @param {Object} companyData - Datos actualizados de la empresa
     * @returns {Promise<Object>} - Respuesta del servidor
     */
    async updateCompany(id, companyData) {
        console.log('🏢 Actualizando empresa:', id, companyData);

        try {
            const token = sessionStorage.getItem("authToken");
            if (!token) {
                throw new Error('No hay token de autenticación');
            }

            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 10000);

            try {
                const response = await fetch(`${this.baseUrl}${this.endpoint}/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(companyData),
                    signal: controller.signal
                });

                clearTimeout(timeoutId);

                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(`HTTP ${response.status}: ${errorData.message || 'Error al actualizar empresa'}`);
                }

                const result = await response.json();
                console.log('✅ Empresa actualizada exitosamente:', result);

                // Limpiar cache
                this.clearCache();

                return result;

            } catch (fetchError) {
                clearTimeout(timeoutId);
                
                if (fetchError.name === 'AbortError') {
                    throw new Error('Timeout: La petición tardó demasiado');
                }
                throw fetchError;
            }

        } catch (error) {
            console.error('❌ Error al actualizar empresa:', error);
            throw error;
        }
    }

    /**
     * Elimina una empresa
     * @param {number} id - ID de la empresa
     * @returns {Promise<Object>} - Respuesta del servidor
     */
    async deleteCompany(id) {
        console.log('🏢 Eliminando empresa:', id);

        try {
            const token = sessionStorage.getItem("authToken");
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
                    throw new Error(`HTTP ${response.status}: ${errorData.message || 'Error al eliminar empresa'}`);
                }

                const result = await response.json();
                console.log('✅ Empresa eliminada exitosamente:', result);

                // Limpiar cache
                this.clearCache();

                return result;

            } catch (fetchError) {
                clearTimeout(timeoutId);
                
                if (fetchError.name === 'AbortError') {
                    throw new Error('Timeout: La petición tardó demasiado');
                }
                throw fetchError;
            }

        } catch (error) {
            console.error('❌ Error al eliminar empresa:', error);
            throw error;
        }
    }

    /**
     * Construye los parámetros de consulta para la URL
     * @param {Object} params - Parámetros de entrada
     * @returns {string} - Query string formateado
     */
    buildQueryParams(params) {
        const defaults = {
            page: 1,
            perPage: 15,
            sortBy: 'id',
            sortDirection: 'asc'
        };

        const mergedParams = { ...defaults, ...params };
        const queryParams = new URLSearchParams();

        // Validar y limpiar parámetros antes de agregarlos
        const validParams = {
            page: parseInt(mergedParams.page) || 1,
            perPage: Math.min(parseInt(mergedParams.perPage) || 15, 100), // Máximo 100
            sortBy: ['id', 'ruc', 'name', 'active', 'created_at'].includes(mergedParams.sortBy) ? mergedParams.sortBy : 'id',
            sortDirection: ['asc', 'desc'].includes(mergedParams.sortDirection) ? mergedParams.sortDirection : 'asc'
        };

        // Agregar filtros opcionales solo si tienen valor válido
        if (mergedParams.search && typeof mergedParams.search === 'string' && mergedParams.search.trim() !== '') {
            validParams.search = mergedParams.search.trim();
        }
        
        if (mergedParams.ruc && typeof mergedParams.ruc === 'string' && mergedParams.ruc.trim() !== '') {
            validParams.ruc = mergedParams.ruc.trim();
        }
        
        if (mergedParams.name && typeof mergedParams.name === 'string' && mergedParams.name.trim() !== '') {
            validParams.name = mergedParams.name.trim();
        }
        
        if (mergedParams.active !== undefined && mergedParams.active !== 'all') {
            // Convertir string a boolean si es necesario
            if (mergedParams.active === 'true' || mergedParams.active === true) {
                validParams.active = true;
            } else if (mergedParams.active === 'false' || mergedParams.active === false) {
                validParams.active = false;
            }
        }

        // Construir query string solo con parámetros válidos
        Object.entries(validParams).forEach(([key, value]) => {
            if (value !== undefined && value !== null && value !== '') {
                queryParams.append(key, value);
            }
        });

        console.log('🔧 Parámetros construidos:', validParams);
        console.log('🔗 Query string:', queryParams.toString());
        
        return queryParams.toString();
    }

    /**
     * Limpia el cache de empresas
     */
    clearCache() {
        console.log('🧹 Limpiando cache de empresas');
        this.companiesCache.clear();
    }

    /**
     * Valida los datos de una empresa antes de enviarlos
     * @param {Object} companyData - Datos de la empresa
     * @returns {Object} - Objeto con resultado de validación
     */
    validateCompanyData(companyData) {
        const errors = [];

        if (!companyData.ruc || companyData.ruc.trim() === '') {
            errors.push('El RUC es requerido');
        } else if (companyData.ruc.length !== 11) {
            errors.push('El RUC debe tener 11 dígitos');
        }

        if (!companyData.name || companyData.name.trim() === '') {
            errors.push('El nombre de la empresa es requerido');
        }

        if (companyData.active === undefined || companyData.active === null) {
            errors.push('El estado activo es requerido');
        }

        return {
            isValid: errors.length === 0,
            errors
        };
    }

    /**
     * Formatea un RUC para mostrar
     * @param {string} ruc - RUC sin formato
     * @returns {string} - RUC formateado
     */
    formatRuc(ruc) {
        if (!ruc || ruc.length !== 11) {
            return ruc;
        }

        // Formato: 20-123456789
        return `${ruc.substring(0, 2)}-${ruc.substring(2)}`;
    }

}

// Instancia global del servicio
window.CompanyService = CompanyService;