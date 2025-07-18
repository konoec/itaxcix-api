// company-delete-service.js

/**
 * Servicio para eliminar empresas
 * Endpoint: DELETE /api/v1/admin/companies/{id}
 */
class CompanyDeleteService {
  constructor() {
    this.baseUrl  = 'https://149.130.161.148/api/v1';
    this.endpoint = '/admin/companies';
    console.log('🏢 CompanyDeleteService inicializado');
  }

  /**
   * Elimina una empresa existente
   * @param {number} id - ID de la empresa
   * @returns {Promise<Object>} { success, message }
   */
  async delete(id) {
    console.log('🏢 CompanyDeleteService: eliminando empresa', id);
    const token = sessionStorage.getItem('authToken');
    if (!token) throw new Error('No hay token de autenticación');

    const url = `${this.baseUrl}${this.endpoint}/${id}`;
    const controller = new AbortController();
    const timeoutId  = setTimeout(() => controller.abort(), 8000);

    try {
      const resp = await fetch(url, {
        method:  'DELETE',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept':        'application/json'
        },
        signal: controller.signal
      });
      clearTimeout(timeoutId);

      if (!resp.ok) {
        const errData = await resp.json().catch(() => ({}));
        throw new Error(errData.message || `Error al eliminar empresa (HTTP ${resp.status})`);
      }

      const result = await resp.json();
      console.log('✅ CompanyDeleteService: empresa eliminada exitosamente', result);
      // Invalidar cache de listado
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

// Exponer la clase para instanciación por el Initializer
window.CompanyDeleteService = CompanyDeleteService;
