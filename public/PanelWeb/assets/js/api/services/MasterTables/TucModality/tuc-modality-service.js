// Servicio para listar modalidades TUC
class TucModalityService {
  static get API_BASE_URL() {
    return 'https://149.130.161.148/api/v1';
  }

  static getAuthToken() {
    // Cambia los nombres de las keys si tu app usa otro nombre para el token
    return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
  }

  /**
   * Obtiene la lista de modalidades TUC con filtros, búsqueda y paginación
   * @param {Object} params - { page, perPage, search, name, active, sortBy, sortDirection }
   * @returns {Promise<Object>} Respuesta de la API
   */
  static async getTucModalities(params = {}) {
    const q = new URLSearchParams();
    if (params.page)            q.append('page', params.page);
    if (params.perPage)         q.append('perPage', params.perPage);
    if (params.search)          q.append('search', params.search);
    if (params.name)            q.append('name', params.name);
    if (typeof params.active === 'boolean') q.append('active', params.active);
    if (params.sortBy)          q.append('sortBy', params.sortBy);
    if (params.sortDirection)   q.append('sortDirection', params.sortDirection);

    const token = this.getAuthToken();
    const res = await fetch(
      `${this.API_BASE_URL}/admin/tuc-modalities?${q.toString()}`,
      {
        method: 'GET',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Accept': 'application/json'
        }
      }
    );

    if (!res.ok) {
      const err = await res.json().catch(() => ({}));
      throw new Error(err.message || 'Error al obtener modalidades TUC');
    }

    return res.json();
  }
}

window.TucModalityService = TucModalityService;
