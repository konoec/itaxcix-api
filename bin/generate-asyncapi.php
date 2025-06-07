<?php
// bin/generate-asyncapi.php

// Asegurarse de que existe el directorio docs para el archivo de especificación
$docsDir = __DIR__ . '/../docs';
if (!is_dir($docsDir)) {
    mkdir($docsDir, 0755, true);
    echo "Creado directorio: $docsDir\n";
}

// Crear/actualizar la especificación AsyncAPI si es necesario
$specFile = $docsDir . '/asyncapi.yaml';
if (!file_exists($specFile)) {
    // Crear la especificación basada en tu WebSocketHandler
    file_put_contents($specFile, getAsyncApiSpec());
    echo "Creado archivo de especificación: $specFile\n";
}

// Asegurarse de que el directorio de destino existe
$outputDir = __DIR__ . '/../public/asyncapi-docs';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
    echo "Creado directorio: $outputDir\n";
}

// Crear un archivo HTML básico como alternativa al generador
$htmlContent = generateSimpleHtml();
file_put_contents("$outputDir/index.html", $htmlContent);

echo "Documentación AsyncAPI generada en: $outputDir\n";

/**
 * Genera la especificación AsyncAPI basada en el DriverLocationHandler
 */
function getAsyncApiSpec() {
    return <<<YAML
asyncapi: '2.6.0'
info:
  title: iTaxCix WebSocket API
  version: '1.0.0'
  description: API de WebSocket para seguimiento de conductores en tiempo real

servers:
  production:
    url: wss://api.itaxcix.com/ws
    protocol: wss
    description: Servidor de producción
  development:
    url: wss://localhost/ws
    protocol: wss
    description: Servidor de desarrollo (a través de proxy Nginx)
  direct:
    url: ws://localhost:8080
    protocol: ws
    description: Conexión directa al servidor WebSocket (sin proxy)

channels:
  /:
    publish:
      summary: Mensajes enviados por clientes al servidor
      message:
        oneOf:
          - \$ref: '#/components/messages/IdentifyMessage'
          - \$ref: '#/components/messages/UpdateLocationMessage'
    subscribe:
      summary: Mensajes enviados por el servidor a los clientes
      message:
        oneOf:
          - \$ref: '#/components/messages/InitialDriversMessage'
          - \$ref: '#/components/messages/NewDriverMessage'
          - \$ref: '#/components/messages/DriverLocationUpdateMessage'
          - \$ref: '#/components/messages/DriverOfflineMessage'

components:
  messages:
    IdentifyMessage:
      name: identify
      summary: Mensaje de identificación de cliente
      payload:
        \$ref: '#/components/schemas/IdentifyPayload'
    UpdateLocationMessage:
      name: update_location
      summary: Actualización de ubicación del conductor
      payload:
        \$ref: '#/components/schemas/UpdateLocationPayload'
    InitialDriversMessage:
      name: initial_drivers
      summary: Lista inicial de conductores activos
      payload:
        \$ref: '#/components/schemas/InitialDriversPayload'
    NewDriverMessage:
      name: new_driver
      summary: Nuevo conductor conectado
      payload:
        \$ref: '#/components/schemas/DriverEventPayload'
    DriverLocationUpdateMessage:
      name: driver_location_update
      summary: Actualización de ubicación de un conductor
      payload:
        \$ref: '#/components/schemas/LocationUpdatePayload'
    DriverOfflineMessage:
      name: driver_offline
      summary: Conductor desconectado
      payload:
        \$ref: '#/components/schemas/DriverOfflinePayload'

  schemas:
    IdentifyPayload:
      type: object
      required:
        - type
        - clientType
        - userId
      properties:
        type:
          type: string
          const: identify
        clientType:
          type: string
          enum: [driver, citizen]
        userId:
          type: string
        driverData:
          type: object
          properties:
            fullName:
              type: string
            location:
              \$ref: '#/components/schemas/Location'
            image:
              type: string
            rating:
              type: number

    UpdateLocationPayload:
      type: object
      required:
        - type
        - location
      properties:
        type:
          type: string
          const: update_location
        location:
          \$ref: '#/components/schemas/Location'

    InitialDriversPayload:
      type: object
      required:
        - type
        - drivers
      properties:
        type:
          type: string
          const: initial_drivers
        drivers:
          type: array
          items:
            \$ref: '#/components/schemas/DriverInfo'

    DriverEventPayload:
      type: object
      required:
        - type
        - data
      properties:
        type:
          type: string
          const: new_driver
        data:
          \$ref: '#/components/schemas/DriverInfo'

    LocationUpdatePayload:
      type: object
      required:
        - type
        - data
      properties:
        type:
          type: string
          const: driver_location_update
        data:
          type: object
          properties:
            id:
              type: string
            location:
              \$ref: '#/components/schemas/Location'

    DriverOfflinePayload:
      type: object
      required:
        - type
        - data
      properties:
        type:
          type: string
          const: driver_offline
        data:
          type: object
          properties:
            id:
              type: string

    DriverInfo:
      type: object
      properties:
        id:
          type: string
        fullName:
          type: string
        image:
          type: string
        location:
          \$ref: '#/components/schemas/Location'
        rating:
          type: number
        timestamp:
          type: integer

    Location:
      type: object
      required:
        - lat
        - lng
      properties:
        lat:
          type: number
        lng:
          type: number
YAML;
}

/**
 * Genera un HTML simple como alternativa al generador AsyncAPI
 */
function generateSimpleHtml() {
    $yamlContent = file_get_contents(__DIR__ . '/../docs/asyncapi.yaml');
    $yamlContentEscaped = htmlspecialchars($yamlContent);

    return <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>iTaxCix WebSocket API Documentation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/default.min.css">
    <style>
        body {
            padding: 20px;
        }
        .yaml-content {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            overflow: auto;
        }
        .tab-content {
            padding: 20px 0;
        }
        .ws-message {
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        .message-direction {
            font-weight: bold;
            color: #0d6efd;
        }
        pre {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="my-4">iTaxCix WebSocket API</h1>
        <p class="lead">Esta documentación describe la API WebSocket para el seguimiento de conductores en tiempo real.</p>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">Descripción General</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="messages-tab" data-bs-toggle="tab" data-bs-target="#messages" type="button" role="tab" aria-controls="messages" aria-selected="false">Mensajes</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="examples-tab" data-bs-toggle="tab" data-bs-target="#examples" type="button" role="tab" aria-controls="examples" aria-selected="false">Ejemplos</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="spec-tab" data-bs-toggle="tab" data-bs-target="#spec" type="button" role="tab" aria-controls="spec" aria-selected="false">Especificación AsyncAPI</button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                <h2>Descripción General</h2>
                <p>La API WebSocket de iTaxCix permite:</p>
                <ul>
                    <li>Seguimiento en tiempo real de conductores en el mapa</li>
                    <li>Identificación de clientes (conductores y ciudadanos)</li>
                    <li>Actualizaciones de ubicación de conductores</li>
                    <li>Notificaciones cuando conductores se conectan o desconectan</li>
                </ul>

                <h3>Conexión al WebSocket</h3>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Entorno</th>
                                <th>URL</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Producción</td>
                                <td><code>wss://api.itaxcix.com/ws</code></td>
                                <td>Para uso en producción</td>
                            </tr>
                            <tr>
                                <td>Desarrollo</td>
                                <td><code>wss://localhost/ws</code></td>
                                <td>Para desarrollo local a través del proxy Nginx</td>
                            </tr>
                            <tr>
                                <td>Directo</td>
                                <td><code>ws://localhost:8080</code></td>
                                <td>Conexión directa al servidor WebSocket (sin usar el proxy)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="alert alert-info mt-3">
                    <strong>Importante:</strong> Para entornos de producción y la mayoría de los casos de desarrollo, se recomienda usar la conexión a través de Nginx (<code>wss://localhost/ws</code>) para aprovechar las ventajas de seguridad del protocolo WSS.
                </div>
            </div>

            <div class="tab-pane fade" id="messages" role="tabpanel" aria-labelledby="messages-tab">
                <h2>Mensajes</h2>

                <h3>Mensajes del Cliente al Servidor</h3>
                <div class="ws-message">
                    <h4>identify</h4>
                    <p>Enviado por el cliente para identificarse al conectarse.</p>
                    <pre><code class="language-json">{
  "type": "identify",
  "clientType": "driver|citizen",
  "userId": "string",
  "driverData": {  // Solo si clientType es "driver"
    "fullName": "Nombre Completo",
    "location": { "lat": 12.34, "lng": -56.78 },
    "image": "url_imagen",
    "rating": 4.5
  }
}</code></pre>
                </div>

                <div class="ws-message">
                    <h4>update_location</h4>
                    <p>Enviado por el conductor para actualizar su ubicación.</p>
                    <pre><code class="language-json">{
  "type": "update_location",
  "location": { "lat": 12.34, "lng": -56.78 }
}</code></pre>
                </div>

                <h3>Mensajes del Servidor al Cliente</h3>
                <div class="ws-message">
                    <h4>initial_drivers</h4>
                    <p>Enviado al cliente cuando se conecta, contiene todos los conductores activos.</p>
                    <pre><code class="language-json">{
  "type": "initial_drivers",
  "drivers": [
    {
      "id": "driver-id-1",
      "fullName": "Nombre Conductor 1",
      "image": "url_imagen_1",
      "location": { "lat": 12.34, "lng": -56.78 },
      "rating": 4.5,
      "timestamp": 1621500000
    },
    // ... más conductores
  ]
}</code></pre>
                </div>

                <div class="ws-message">
                    <h4>new_driver</h4>
                    <p>Enviado cuando un nuevo conductor se conecta.</p>
                    <pre><code class="language-json">{
  "type": "new_driver",
  "data": {
    "id": "driver-id",
    "fullName": "Nombre Conductor",
    "image": "url_imagen",
    "location": { "lat": 12.34, "lng": -56.78 },
    "rating": 4.5,
    "timestamp": 1621500000
  }
}</code></pre>
                </div>

                <div class="ws-message">
                    <h4>driver_location_update</h4>
                    <p>Enviado cuando un conductor actualiza su ubicación.</p>
                    <pre><code class="language-json">{
  "type": "driver_location_update",
  "data": {
    "id": "driver-id",
    "location": { "lat": 12.34, "lng": -56.78 }
  }
}</code></pre>
                </div>

                <div class="ws-message">
                    <h4>driver_offline</h4>
                    <p>Enviado cuando un conductor se desconecta.</p>
                    <pre><code class="language-json">{
  "type": "driver_offline",
  "data": {
    "id": "driver-id"
  }
}</code></pre>
                </div>
            </div>

            <div class="tab-pane fade" id="examples" role="tabpanel" aria-labelledby="examples-tab">
                <h2>Ejemplos de Implementación</h2>

                <h3>Conexión al WebSocket desde JavaScript</h3>
                <pre><code class="language-javascript">// Conexión al servidor WebSocket (a través del proxy Nginx)
const socket = new WebSocket('wss://localhost/ws');

// Manejo de eventos de WebSocket
socket.onopen = function(event) {
  console.log('Conexión WebSocket establecida');
  
  // Identificar al cliente como ciudadano
  socket.send(JSON.stringify({
    type: 'identify',
    clientType: 'citizen',
    userId: 'usuario123'
  }));
};

// Recibir mensajes
socket.onmessage = function(event) {
  const data = JSON.parse(event.data);
  
  switch(data.type) {
    case 'initial_drivers':
      console.log('Conductores iniciales recibidos:', data.drivers);
      // Actualizar mapa con todos los conductores
      break;
      
    case 'new_driver':
      console.log('Nuevo conductor conectado:', data.data);
      // Añadir conductor al mapa
      break;
      
    case 'driver_location_update':
      console.log('Actualización de ubicación:', data.data);
      // Actualizar posición del conductor en el mapa
      break;
      
    case 'driver_offline':
      console.log('Conductor desconectado:', data.data.id);
      // Eliminar conductor del mapa
      break;
  }
};

// Manejar errores y cierre de conexión
socket.onerror = function(error) {
  console.error('Error de WebSocket:', error);
};

socket.onclose = function(event) {
  console.log('Conexión WebSocket cerrada');
};</code></pre>

                <h3>Conexión como Conductor</h3>
                <pre><code class="language-javascript">// Conexión como conductor
const socket = new WebSocket('wss://localhost/ws');

// Coordenadas iniciales del conductor
let currentLocation = { lat: 12.345, lng: -56.789 };

socket.onopen = function(event) {
  console.log('Conexión WebSocket establecida');
  
  // Identificar al cliente como conductor
  socket.send(JSON.stringify({
    type: 'identify',
    clientType: 'driver',
    userId: 'conductor456',
    driverData: {
      fullName: 'Juan Pérez',
      location: currentLocation,
      image: 'https://ejemplo.com/foto.jpg',
      rating: 4.8
    }
  }));
};

// Función para enviar actualizaciones de ubicación
function updateLocation(newLocation) {
  currentLocation = newLocation;
  
  if (socket.readyState === WebSocket.OPEN) {
    socket.send(JSON.stringify({
      type: 'update_location',
      location: currentLocation
    }));
  }
}

// Simular actualizaciones de ubicación cada 10 segundos
setInterval(() => {
  // Simulación: mover ligeramente la ubicación
  const newLocation = {
    lat: currentLocation.lat + (Math.random() - 0.5) * 0.001,
    lng: currentLocation.lng + (Math.random() - 0.5) * 0.001
  };
  
  updateLocation(newLocation);
}, 10000);</code></pre>
            </div>

            <div class="tab-pane fade" id="spec" role="tabpanel" aria-labelledby="spec-tab">
                <h2>Especificación AsyncAPI</h2>
                <p>La especificación completa en formato YAML:</p>
                <pre><code class="language-yaml">{$yamlContentEscaped}</code></pre>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script>hljs.highlightAll();</script>
</body>
</html>
HTML;
}