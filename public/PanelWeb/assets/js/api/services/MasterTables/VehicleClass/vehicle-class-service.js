// Servicio para listar clases de vehículos
class VehicleClassService {
  /**
   * Valida los datos para crear una clase de vehículo
   * @param {Object} data - { name, active }
   * @returns {Object} { isValid, errors }
   */
  static validateVehicleClassData(data) {
    const errors = [];
    if (!data.name || typeof data.name !== 'string' || !data.name.trim()) {
      errors.push('El nombre de la clase es requerido');
    } else if (data.name.length > 100) {
      errors.push('El nombre no puede exceder 100 caracteres');
    }
    return {
      isValid: errors.length === 0,
      errors
    };
  }

  /**
   * Crea una nueva clase de vehículo
   * @param {Object} data - { name, active }
   * @returns {Promise<Object>} Respuesta de la API
   */
  static async createVehicleClass(data) {
    try {
      const token = this.getAuthToken();
      const res = await fetch(
        `${this.API_BASE_URL}/admin/vehicle-classes`,
        {
          method: 'POST',
          headers: {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            name: data.name,
            active: !!data.active
          })
        }
      );
      const result = await res.json();
      if (!res.ok) {
        throw new Error(result.message || 'Error al crear la clase de vehículo');
      }
      return {
        success: true,
        message: result.message || 'Clase de vehículo creada correctamente',
        data: result.data || null
      };
    } catch (e) {
      return {
        success: false,
        message: e.message || 'Error al crear la clase de vehículo'
      };
    }
  }
  static get API_BASE_URL() {
    return 'https://149.130.161.148/api/v1';
  }

  static getAuthToken() {
    return sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
  }

  /**
   * Obtiene la lista de clases de vehículos con filtros, búsqueda y paginación
   * @param {Object} params - { page, perPage, search, name, active, sortBy, sortDirection }
   * @returns {Promise<Object>} Respuesta de la API
   */
  static async getVehicleClasses(params = {}) {
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
        `${this.API_BASE_URL}/admin/vehicle-classes?${query.toString()}`,
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
        throw new Error(err.message || 'Error al obtener clases de vehículos');
      }
      return await res.json();
    } catch (e) {
      throw e;
    }
  }
}

window.VehicleClassService = VehicleClassService;
