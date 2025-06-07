class DriverTracker {
    constructor() {
        this.ws = null;
        this.drivers = {};
        this.onDriversUpdateCallbacks = [];
        this.mapInitialized = false;
        this.map = null;
        this.markers = {};
    }

    connect() {
        // Usar WebSocket seguro (wss) si estamos en HTTPS
        const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
        const wsUrl = `${protocol}//${window.location.host}/ws`;

        this.ws = new WebSocket(wsUrl);

        this.ws.onopen = () => {
            console.log('Conectado al servidor de WebSocket');

            // Identificarse como ciudadano
            this.ws.send(JSON.stringify({
                type: 'identify',
                clientType: 'citizen',
                userId: this.getUserId()
            }));
        };

        this.ws.onmessage = (event) => {
            const message = JSON.parse(event.data);
            this.handleMessage(message);
        };

        this.ws.onclose = () => {
            console.log('Desconectado del servidor WebSocket');
            // Intentar reconectar después de 5 segundos
            setTimeout(() => this.connect(), 5000);
        };

        this.ws.onerror = (error) => {
            console.error('Error en WebSocket:', error);
        };
    }

    getUserId() {
        // Obtener ID de usuario desde localStorage o generar uno temporal
        let userId = localStorage.getItem('userId');
        if (!userId) {
            userId = 'citizen_' + Math.random().toString(36).substr(2, 9);
            localStorage.setItem('userId', userId);
        }
        return userId;
    }

    handleMessage(message) {
        switch (message.type) {
            case 'initial_drivers':
                this.drivers = {};
                message.drivers.forEach(driver => {
                    this.drivers[driver.id] = driver;
                });
                this.updateDriversOnMap();
                this.notifyDriversUpdate();
                break;

            case 'new_driver':
                this.drivers[message.data.id] = message.data;
                this.updateDriverOnMap(message.data);
                this.notifyDriversUpdate();
                break;

            case 'driver_location_update':
                if (this.drivers[message.data.id]) {
                    this.drivers[message.data.id].location = message.data.location;
                    this.updateDriverPosition(message.data.id, message.data.location);
                    this.notifyDriversUpdate();
                }
                break;

            case 'driver_offline':
                if (this.drivers[message.data.id]) {
                    this.removeDriverFromMap(message.data.id);
                    delete this.drivers[message.data.id];
                    this.notifyDriversUpdate();
                }
                break;
        }
    }

    initializeMap(mapElement) {
        if (!this.mapInitialized && window.google && window.google.maps) {
            this.map = new google.maps.Map(mapElement, {
                center: { lat: 19.4326, lng: -99.1332 }, // Coordenadas por defecto (ajustar según tu ubicación)
                zoom: 13
            });
            this.mapInitialized = true;

            // Si ya hay conductores, mostrarlos en el mapa
            if (Object.keys(this.drivers).length > 0) {
                this.updateDriversOnMap();
            }

            return true;
        }
        return false;
    }

    updateDriversOnMap() {
        if (!this.mapInitialized) return;

        Object.values(this.drivers).forEach(driver => {
            this.updateDriverOnMap(driver);
        });
    }

    updateDriverOnMap(driver) {
        if (!this.mapInitialized) return;

        if (this.markers[driver.id]) {
            // Actualizar marcador existente
            this.markers[driver.id].setPosition({
                lat: parseFloat(driver.location.lat),
                lng: parseFloat(driver.location.lng)
            });
        } else {
            // Crear nuevo marcador
            const marker = new google.maps.Marker({
                position: {
                    lat: parseFloat(driver.location.lat),
                    lng: parseFloat(driver.location.lng)
                },
                map: this.map,
                title: driver.fullName,
                icon: {
                    url: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png'
                }
            });

            // Crear infoWindow para mostrar datos del conductor
            const infoWindow = new google.maps.InfoWindow({
                content: `
                    <div class="driver-info">
                        <img src="${driver.image}" alt="${driver.fullName}" style="width: 50px; height: 50px; border-radius: 25px;">
                        <h4>${driver.fullName}</h4>
                        <div>Calificación: ${driver.rating} ⭐</div>
                    </div>
                `
            });

            marker.addListener('click', () => {
                infoWindow.open(this.map, marker);
            });

            this.markers[driver.id] = marker;
        }
    }

    updateDriverPosition(driverId, location) {
        if (!this.mapInitialized || !this.markers[driverId]) return;

        this.markers[driverId].setPosition({
            lat: parseFloat(location.lat),
            lng: parseFloat(location.lng)
        });
    }

    removeDriverFromMap(driverId) {
        if (this.markers[driverId]) {
            this.markers[driverId].setMap(null);
            delete this.markers[driverId];
        }
    }

    onDriversUpdate(callback) {
        if (typeof callback === 'function') {
            this.onDriversUpdateCallbacks.push(callback);

            // Ejecutar inmediatamente con los conductores actuales
            if (Object.keys(this.drivers).length > 0) {
                callback(Object.values(this.drivers));
            }
        }
    }

    notifyDriversUpdate() {
        const driversArray = Object.values(this.drivers);
        this.onDriversUpdateCallbacks.forEach(callback => {
            callback(driversArray);
        });
    }
}

// Instancia global
window.driverTracker = new DriverTracker();

// Conectar automáticamente cuando se carga el script
document.addEventListener('DOMContentLoaded', () => {
    window.driverTracker.connect();
});