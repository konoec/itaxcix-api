/**
 * Servicio para actualizar modelos de vehículo
 * Endpoint: PUT /api/v1/admin/models/{id}
 */
class ModelUpdateService {
  constructor() {
    this.baseUrl = 'https://149.130.161.148/api/v1/admin/models';
  }

  /**
   * Actualiza un modelo de vehículo existente
   * @param {number} id - ID del modelo
   * @param {Object} data - Datos del modelo
   * @param {string} data.name - Nombre del modelo
   * @param {number} data.brandId - ID de la marca asociada
   * @param {boolean} data.active - Estado activo/inactivo
   * @returns {Promise<Object>} Respuesta de la API
   */
  async updateModel(id, data) {
    try {
      console.log('🔄 Actualizando modelo:', { id, data });

      // Validar datos de entrada
      const validation = this.validateModelData(data);
      if (!validation.isValid) {
        throw new Error(validation.errors.join(', '));
      }

      // Obtener token de autenticación
      const token = sessionStorage.getItem('token') || sessionStorage.getItem('authToken');
      if (!token) {
        throw new Error('Token de autenticación no encontrado');
      }

      // Preparar payload
      const payload = {
        name:    data.name.trim(),
        brandId: parseInt(data.brandId, 10),
        active:  Boolean(data.active)
      };
      console.log('📤 Payload enviado:', payload);

      // Ejecución de la petición
      const response = await fetch(`${this.baseUrl}/${id}`, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Authorization': `Bearer ${token}`
        },
        body: JSON.stringify(payload)
      });
      console.log('📥 HTTP status:', response.status);

      // Parsear cuerpo
      let result;
      const ct = response.headers.get('content-type') || '';
      if (ct.includes('application/json')) {
        result = await response.json();
      } else {
        const text = await response.text();
        console.error('❌ Respuesta no JSON:', text);
        throw new Error(`Error del servidor (${response.status})`);
      }
      console.log('📋 Respuesta API:', result);

      // Manejo de errores HTTP
      if (!response.ok) {
        let errorMsg = result?.error?.message || result.message || `Error del servidor (${response.status})`;
        switch (response.status) {
          case 400: throw new Error(errorMsg || 'Datos inválidos. Verifique los campos.');
          case 401: throw new Error('Sesión expirada. Inicie sesión nuevamente.');
          case 404: throw new Error('Modelo no encontrado.');
          case 409: throw new Error('Conflicto: datos duplicados.');
          case 422: throw new Error('Validación fallida. Revise los datos.');
          default:  throw new Error(errorMsg);
        }
      }

      // Si la API indica éxito
      if (result.success) {
        // Reemplazar mensaje genérico "OK"
        let msg = result.message;
        if (!msg || msg.trim().toUpperCase() === 'OK') {
          msg = 'Modelo actualizado exitosamente';
        }
        console.log('✅ Modelo actualizado:', msg);

        return {
          success: true,
          message: msg,
          data:    result.data
        };
      } else {
        // Priorizar el mensaje exacto de la API
        let errorMsg = '';
        if (result && typeof result === 'object') {
          if (result.error && typeof result.error === 'object' && result.error.message) {
            errorMsg = result.error.message;
          } else if (result.message) {
            errorMsg = result.message;
          } else {
            errorMsg = 'Error desconocido al actualizar el modelo';
          }
        } else {
          errorMsg = 'Error desconocido al actualizar el modelo';
        }
        throw new Error(errorMsg);
      }

    } catch (err) {
      console.error('❌ Error en updateModel:', err);
      // Si el error tiene un mensaje, mostrarlo siempre
      if (err && err.message) {
        throw new Error(err.message);
      }
      // Error de conexión genérico solo si no hay mensaje
      throw new Error('Error de conexión. Intente nuevamente más tarde.');
    }
  }

  /**
   * Valida los datos del modelo
   * @param {Object} data
   * @returns {{isValid: boolean, errors: string[]}}
   */
  validateModelData(data) {
    const errors = [];
    if (!data.name || typeof data.name !== 'string' || !data.name.trim()) {
      errors.push('El nombre del modelo es requerido');
    }
    if (!data.brandId || isNaN(parseInt(data.brandId, 10))) {
      errors.push('El ID de la marca es inválido');
    }
    if (typeof data.active !== 'boolean') {
      errors.push('El estado activo debe ser verdadero o falso');
    }
    return { isValid: errors.length === 0, errors };
  }
}

// Instancia global
window.ModelUpdateService = new ModelUpdateService();
console.log('✅ ModelUpdateService cargado y disponible globalmente');
