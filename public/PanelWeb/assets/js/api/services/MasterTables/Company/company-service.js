// company-service.js

/**
 * Servicio para la gestión de compañías/empresas (listado y obtención por ID)
 * Endpoint principal: /api/v1/admin/companies
 */
class CompanyService {
  constructor() {
    this.baseUrl     = 'https://149.130.161.148/api/v1';
    this.endpoint    = '/admin/companies';
    this.cache       = new Map();
    this.cacheExpiry = 5 * 60 * 1000; // 5 minutos
    console.log('🏢 CompanyService inicializado con base URL:', this.baseUrl);
  }

  /**
   * Obtiene la lista de empresas con filtros y paginación
   * @param {Object} params
   * @param {number} [params.page=1] - Número de página (mínimo 1)
   * @param {number} [params.perPage=15] - Elementos por página (1–100)
   * @param {string} [params.search] - Búsqueda global en RUC y nombre
   * @param {string} [params.ruc] - Filtro por RUC exacto
   * @param {string} [params.name] - Filtro por nombre (contiene)
   * @param {boolean} [params.active] - Filtro por estado activo
   * @param {string} [params.sortBy='id'] - Campo de ordenamiento: id, ruc, name, active
   * @param {string} [params.sortDirection='asc'] - Dirección de orden: asc, desc
   * @returns {Promise<Object>} - { success, message, data: { items, pagination } }
   */
  async getCompanies(params = {}) {
    const token = sessionStorage.getItem('authToken');
    if (!token) throw new Error('No hay token de autenticación');

    // Normalizar parámetros con sus valores por defecto
    const {
      page = 1,
      perPage = 15,
      search,
      ruc,
      name,
      active,
      sortBy = 'id',
      sortDirection = 'asc'
    } = params;

    const qp = new URLSearchParams({
      page: String(Math.max(1, page)),
      perPage: String(Math.min(Math.max(1, perPage), 100)),
      sortBy,
      sortDirection
    });
    if (search) qp.append('search', search);
    if (ruc)    qp.append('ruc', ruc);
    if (name)   qp.append('name', name);
    if (active !== undefined) qp.append('active', String(active));

    const queryString = qp.toString();
    const cacheKey     = `companies_${queryString}`;

    // Intentar cache
    if (this.cache.has(cacheKey)) {
      const { data, ts } = this.cache.get(cacheKey);
      if (Date.now() - ts < this.cacheExpiry) {
        console.log('🏢 CompanyService: usando cache para getCompanies');
        return data;
      }
    }

    const url = `${this.baseUrl}${this.endpoint}?${queryString}`;
    console.log('🌐 CompanyService: GET', url);

    const controller = new AbortController();
    const timeoutId  = setTimeout(() => controller.abort(), 10000);

    try {
      const resp = await fetch(url, {
        method: 'GET',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept':        'application/json'
        },
        signal: controller.signal
      });
      clearTimeout(timeoutId);

      if (!resp.ok) {
        const errData = await resp.json().catch(() => ({}));
        throw new Error(errData.message || `Error al obtener empresas (HTTP ${resp.status})`);
      }

      const result = await resp.json();
      console.log('✅ CompanyService: empresas obtenidas', result);

      // Guardar en cache
      this.cache.set(cacheKey, { data: result, ts: Date.now() });
      return result;
    } catch (err) {
      clearTimeout(timeoutId);
      if (err.name === 'AbortError') {
        throw new Error('Timeout: la petición tardó demasiado');
      }
      throw err;
    }
  }

  /**
   * Obtiene una empresa por su ID
   * @param {number} id - ID de la empresa
   * @returns {Promise<Object>} - { success, message, data: { id, ruc, name, active } }
   */
  async getCompanyById(id) {
    const token = sessionStorage.getItem('authToken');
    if (!token) throw new Error('No hay token de autenticación');

    const url = `${this.baseUrl}${this.endpoint}/${id}`;
    console.log('🌐 CompanyService: GET', url);

    const controller = new AbortController();
    const timeoutId  = setTimeout(() => controller.abort(), 8000);

    try {
      const resp = await fetch(url, {
        method: 'GET',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept':        'application/json'
        },
        signal: controller.signal
      });
      clearTimeout(timeoutId);

      if (!resp.ok) {
        const errData = await resp.json().catch(() => ({}));
        throw new Error(errData.message || `Error al obtener empresa (HTTP ${resp.status})`);
      }

      const result = await resp.json();
      console.log('✅ CompanyService: empresa obtenida', result);
      return result;
    } catch (err) {
      clearTimeout(timeoutId);
      if (err.name === 'AbortError') {
        throw new Error('Timeout: la petición tardó demasiado');
      }
      throw err;
    }
  }
}

// Exponer la clase para que el Initializer la instancie
window.CompanyService = CompanyService;
