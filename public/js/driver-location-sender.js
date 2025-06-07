class DriverLocationSender {
    constructor() {
        this.ws = null;
        this.watchId = null;
        this.driverData = null;
        this.isConnected = false;
    }

    connect(driverData) {
        if (!driverData || !driverData.id || !driverData.fullName || !driverData.image || !driverData.rating) {
            console.error('Datos de conductor incompletos');
            return false;
        }

        this.driverData = driverData;

        // Usar WebSocket seguro (wss) si estamos en HTTPS
        const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
        const wsUrl = `${protocol}//${window.location.host}/ws`;

        this.ws = new WebSocket(wsUrl);

        this.ws.onopen = () => {
            console.log('Conectado al servidor de WebSocket');
            this.isConnected = true;

            // Empezar a seguir la ubicación
            this.startLocationTracking();
        };

        this.ws.onclose = () => {
            console.log('Desconectado del servidor WebSocket');
            this.isConnected = false;

            // Detener seguimiento
            this.stopLocationTracking();

            // Intentar reconectar después de 5 segundos
            setTimeout(() => this.connect(this.driverData), 5000);
        };

        this.ws.onerror = (error) => {
            console.error('Error en WebSocket:', error);
            this.isConnected = false;
        };

        return true;
    }

    startLocationTracking() {
        if (!navigator.geolocation) {
            console.error('Geolocalización no soportada en este navegador');
            return;
        }

        // Si ya estamos siguiendo, detener primero
        if (this.watchId !== null) {
            this.stopLocationTracking();
        }

        // Identificarse como conductor con ubicación inicial
        navigator.geolocation.getCurrentPosition((position) => {
            const location = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };

            // Añadir ubicación a los datos del conductor
            this.driverData.location = location;

            // Enviar identificación con datos completos
            this.sendMessage({
                type: 'identify',
                clientType: 'driver',
                userId: this.driverData.id,
                driverData: this.driverData
            });

            // Configurar seguimiento continuo
            this.watchId = navigator.geolocation.watchPosition(
                (position) => this.handleLocationUpdate(position),
                (error) => console.error('Error de geolocalización:', error),
                {
                    enableHighAccuracy: true,
                    maximumAge: 10000,
                    timeout: 10000
                }
            );
        }, (error) => {
            console.error('Error al obtener ubicación inicial:', error);
        });
    }

    stopLocationTracking() {
        if (this.watchId !== null) {
            navigator.geolocation.clearWatch(this.watchId);
            this.watchId = null;
        }
    }

    handleLocationUpdate(position) {
        if (!this.isConnected) return;

        const location = {
            lat: position.coords.latitude,
            lng: position.coords.longitude
        };

        this.sendMessage({
            type: 'update_location',
            location: location
        });
    }

    sendMessage(message) {
        if (this.ws && this.isConnected) {
            this.ws.send(JSON.stringify(message));
        }
    }

    disconnect() {
        this.stopLocationTracking();
        if (this.ws) {
            this.ws.close();
            this.ws = null;
        }
        this.isConnected = false;
    }
}

// Instancia global
window.driverLocationSender = new DriverLocationSender();