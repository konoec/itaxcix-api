// Clase Conductor actualizada para coincidir con la API
class Conductor {
    constructor(data) {
        // Datos básicos del conductor
        this.driverId = data.driverId;
        this.fullName = data.fullName;
        this.documentValue = data.documentValue;
        this.plateValue = data.plateValue;
        this.contactValue = data.contactValue;
        this.avatarUrl = data.avatarUrl;
        
        // Datos adicionales del TUC (Tarjeta Única de Circulación)
        this.rucCompany = data.rucCompany;
        this.tucIssueDate = data.tucIssueDate;
        this.tucExpirationDate = data.tucExpirationDate;
        this.tucModality = data.tucModality;
        this.tucType = data.tucType;
        this.tucStatus = data.tucStatus;
    }

    // Método para obtener el nombre completo (ya viene completo de la API)
    getNombreCompleto() {
        return this.fullName || 'No disponible';
    }

    // Método para cambiar el estado del TUC
    cambiarEstado(nuevoEstado) {
        const estadosValidos = ['PENDIENTE', 'APROBADO', 'RECHAZADO', 'VENCIDO'];
        if (estadosValidos.includes(nuevoEstado.toUpperCase())) {
            this.tucStatus = nuevoEstado.toUpperCase();
            return true;
        }
        return false;
    }

    // Método para obtener la imagen con fallback a imagen por defecto
    getImagenUrl() {
        return this.avatarUrl || '../../assets/Recourse/Imagenes/register_foto_defecto.png';
    }

    // Método para verificar si el TUC está vencido
    isTucVencido() {
        return this.tucStatus === 'VENCIDO';
    }

    // Método para verificar si está pendiente de aprobación
    isPendiente() {
        return this.tucStatus === 'PENDIENTE' || !this.tucStatus;
    }

    // Método para verificar si está aprobado
    isAprobado() {
        return this.tucStatus === 'APROBADO';
    }

    // Formato de fecha más legible para vencimiento
    getFechaVencimientoFormateada() {
        if (!this.tucExpirationDate) return 'No disponible';
        try {
            const fecha = new Date(this.tucExpirationDate);
            return fecha.toLocaleDateString('es-ES', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit'
            });
        } catch (error) {
            return this.tucExpirationDate;
        }
    }

    // Formato de fecha más legible para emisión
    getFechaEmisionFormateada() {
        if (!this.tucIssueDate) return 'No disponible';
        try {
            const fecha = new Date(this.tucIssueDate);
            return fecha.toLocaleDateString('es-ES', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit'
            });
        } catch (error) {
            return this.tucIssueDate;
        }
    }

    // Método para obtener el estado con formato visual
    getEstadoFormateado() {
        const estados = {
            'PENDIENTE': { texto: 'Pendiente', clase: 'estado-pendiente' },
            'APROBADO': { texto: 'Aprobado', clase: 'estado-aprobado' },
            'RECHAZADO': { texto: 'Rechazado', clase: 'estado-rechazado' },
            'VENCIDO': { texto: 'Vencido', clase: 'estado-vencido' }
        };
        
        return estados[this.tucStatus] || { texto: 'Desconocido', clase: 'estado-desconocido' };
    }

    // Método para validar si los datos están completos
    isDatosCompletos() {
        const camposRequeridos = [
            'fullName', 'documentValue', 'plateValue', 'contactValue'
        ];
        
        return camposRequeridos.every(campo => 
            this[campo] && this[campo].toString().trim() !== ''
        );
    }

    // Método para obtener un resumen del conductor
    getResumen() {
        return {
            id: this.driverId,
            nombre: this.fullName,
            dni: this.documentValue,
            placa: this.plateValue,
            contacto: this.contactValue,
            estado: this.tucStatus,
            fechaVencimiento: this.getFechaVencimientoFormateada(),
            datosCompletos: this.isDatosCompletos()
        };
    }

    // Método estático para crear una instancia desde los datos de la API
    static fromApiData(apiData) {
        return new Conductor(apiData);
    }

    // Método para convertir a objeto plano (útil para enviar a la API)
    toApiData() {
        return {
            driverId: this.driverId,
            fullName: this.fullName,
            documentValue: this.documentValue,
            plateValue: this.plateValue,
            contactValue: this.contactValue,
            avatarUrl: this.avatarUrl,
            rucCompany: this.rucCompany,
            tucIssueDate: this.tucIssueDate,
            tucExpirationDate: this.tucExpirationDate,
            tucModality: this.tucModality,
            tucType: this.tucType,
            tucStatus: this.tucStatus
        };
    }
}

// Exportar la clase para que esté disponible en otros archivos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = Conductor;
} else {
    // Para navegadores sin soporte de módulos
    window.Conductor = Conductor;
}