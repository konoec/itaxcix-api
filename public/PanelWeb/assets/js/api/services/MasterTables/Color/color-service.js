// Servicio para listar colores
class ColorService {
  static get API_BASE_URL() {
    return 'https://149.130.161.148/api/v1';
  }

  static getAuthToken() {
    return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
  }

  /**
   * Obtiene la lista de colores con filtros, búsqueda y paginación
   * @param {Object} params - { page, perPage, search, name, active, sortBy, sortDirection }
   * @returns {Promise<Object>} Respuesta de la API
   */
  static async getColors(params = {}) {
    const query = new URLSearchParams();
    if (params.page) query.append('page', params.page);
    if (params.perPage) query.append('perPage', params.perPage);
    if (params.search) query.append('search', params.search);
    if (params.name) query.append('name', params.name);
    if (typeof params.active === 'boolean') query.append('active', params.active);
    if (params.sortBy) query.append('sortBy', params.sortBy);
    if (params.sortDirection) query.append('sortDirection', params.sortDirection);

    try {
      const token = this.getAuthToken();
      const res = await fetch(
        `${this.API_BASE_URL}/admin/colors?${query.toString()}`,
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
        throw new Error(err.message || 'Error al obtener colores');
      }
      return await res.json();
    } catch (e) {
      throw e;
    }
  }
}

window.ColorService = ColorService;
