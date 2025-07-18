// company-update-service.js

/**
 * Servicio para actualizar empresas
 * Endpoint: PUT /api/v1/admin/companies/{id}
 */
class CompanyUpdateService {
  constructor() {
    this.baseUrl  = 'https://149.130.161.148/api/v1';
    this.endpoint = '/admin/companies';
    console.log('🏢 CompanyUpdateService inicializado');
  }

  /**
   * Actualiza una empresa existente
   * @param {number} id - ID de la empresa
   * @param {Object} companyData
   * @param {string} companyData.ruc    – RUC (11 dígitos)
   * @param {string} companyData.name   – Nombre de la empresa
   * @param {boolean} companyData.active– Estado activo/inactivo
   * @returns {Promise<Object>}
   *   { success, message, data: { id, ruc, name, active } }
   */
  async update(id, companyData) {
    console.log('🏢 CompanyUpdateService: actualizando empresa', id, companyData);

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

    const url = `${this.baseUrl}${this.endpoint}/${id}`;
    const controller = new AbortController();
    const timeoutId  = setTimeout(() => controller.abort(), 10000);

    try {
      const resp = await fetch(url, {
        method:  'PUT',
        headers: {
          'Authorization':  `Bearer ${token}`,
          'Accept':         'application/json',
          'Content-Type':   'application/json'
        },
        body:    JSON.stringify(companyData),
        signal:  controller.signal
      });
      clearTimeout(timeoutId);

      if (!resp.ok) {
        const errData = await resp.json().catch(() => ({}));
        throw new Error(errData.message || `Error al actualizar empresa (HTTP ${resp.status})`);
      }

      const result = await resp.json();
      console.log('✅ CompanyUpdateService: empresa actualizada', result);
      // Limpiar cache de CompanyService
      window.companyServiceInstance?.cache.clear();
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
document.addEventListener('DOMContentLoaded', () => {
  if (!window.companyUpdateServiceInstance) {
    window.companyUpdateServiceInstance = new CompanyUpdateService();
  }
});
