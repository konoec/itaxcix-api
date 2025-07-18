// company-create-service.js

/**
 * Servicio para crear empresas
 * Endpoint: POST /api/v1/admin/companies
 */
class CompanyCreateService {
  constructor() {
    this.baseUrl  = 'https://149.130.161.148/api/v1';
    this.endpoint = '/admin/companies';
    console.log('🏢 CompanyCreateService inicializado');
  }

  /**
   * Crea una nueva empresa
   * @param {Object} companyData
   * @param {string} companyData.ruc    – RUC (11 dígitos)
   * @param {string} companyData.name   – Nombre de la empresa
   * @param {boolean} companyData.active– Estado activo/inactivo
   * @returns {Promise<Object>}
   *   { success, message, data: { id, ruc, name, active } }
   */
  async create(companyData) {
    console.log('🏢 CompanyCreateService: creando empresa', companyData);

    // Validación básica
    const errors = [];
    if (!companyData.ruc || companyData.ruc.length !== 11) {
      errors.push('El RUC debe tener 11 dígitos');
    }
    if (!companyData.name || !companyData.name.trim()) {
      errors.push('El nombre de la empresa es requerido');
    }
    if (typeof companyData.active !== 'boolean') {
      errors.push('El campo active debe ser true o false');
    }
    if (errors.length) {
      throw new Error(errors.join('; '));
    }

    const token = sessionStorage.getItem('authToken');
    if (!token) throw new Error('No hay token de autenticación');

    const url = `${this.baseUrl}${this.endpoint}`;
    const controller = new AbortController();
    const timeoutId  = setTimeout(() => controller.abort(), 10000);

    try {
      const resp = await fetch(url, {
        method:  'POST',
        headers: {
          'Authorization':  `Bearer ${token}`,
          'Accept':         'application/json',
          'Content-Type':   'application/json'
        },
        body:    JSON.stringify(companyData),
        signal:  controller.signal
      });
      clearTimeout(timeoutId);

      if (resp.status === 201 || resp.status === 200) {
        const result = await resp.json();
        console.log('✅ CompanyCreateService: empresa creada', result);
        // invalidar cache de lista
        window.companyServiceInstance?.cache.clear();
        return result;
      } else {
        const errData = await resp.json().catch(() => ({}));
        let errorMsg = errData.message;
        if (!errorMsg || errorMsg === 'OK') {
          errorMsg = `Error al crear empresa (HTTP ${resp.status})`;
        }
        throw new Error(errorMsg);
      }
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
window.CompanyCreateService = CompanyCreateService;
