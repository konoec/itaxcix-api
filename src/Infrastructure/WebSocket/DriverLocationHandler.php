<?php

namespace itaxcix\Infrastructure\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Predis\Client as RedisClient;
use React\EventLoop\LoopInterface;

class DriverLocationHandler implements MessageComponentInterface
{
    protected $clients;
    protected $redisClient;
    protected $loop;
    protected $activeDrivers = [];
    protected $activeTrips = [];

    // TTLs en segundos
    const TTL_TRIP_REQUEST = 30;
    const TTL_TRIP_RESPONSE = 30;
    const TTL_DRIVER_LOCATION_UPDATE = 10;

    // Llamar en el constructor
    public function __construct(RedisClient $redisClient, LoopInterface $loop)
    {
        $this->clients = new \SplObjectStorage;
        $this->redisClient = $redisClient;
        $this->loop = $loop;
        $this->loadActiveDriversFromRedis();
        echo "Handler inicializado\n";
    }

    // En DriverLocationHandler.php
    protected function processRedisNotifications()
    {
        try {
            echo "Verificando mensajes en Redis...\n";
            echo "Clientes conectados:\n";
            foreach ($this->clients as $client) {
                echo "- Tipo: " . ($client->clientType ?? 'no definido');
                echo ", ID: " . ($client->userId ?? 'no definido') . "\n";
            }

            $notification = $this->redisClient->rpop('trip_notifications_queue');

            if ($notification) {
                echo "Notificación encontrada: $notification\n";
                $data = json_decode($notification, true);

                // Validar timestamp y tipo
                $now = time();
                $type = $data['type'] ?? null;
                $timestamp = $data['timestamp'] ?? null;
                $shouldDeliver = true;

                if ($type === 'trip_request' && $timestamp) {
                    if ($now - $timestamp > self::TTL_TRIP_REQUEST) {
                        echo "Descartando trip_request por antigüedad.\n";
                        $shouldDeliver = false;
                    }
                } elseif ($type === 'trip_response' && $timestamp) {
                    if ($now - $timestamp > self::TTL_TRIP_RESPONSE) {
                        echo "Descartando trip_response por antigüedad.\n";
                        $shouldDeliver = false;
                    }
                } elseif ($type === 'driver_location_update' && $timestamp) {
                    if ($now - $timestamp > self::TTL_DRIVER_LOCATION_UPDATE) {
                        echo "Descartando driver_location_update por antigüedad.\n";
                        $shouldDeliver = false;
                    }
                }
                // trip_status_update siempre se entrega

                if ($shouldDeliver && $data && isset($data['recipientType']) && isset($data['recipientId'])) {
                    $found = false;
                    foreach ($this->clients as $client) {
                        echo "Comparando con cliente - Tipo: " . ($client->clientType ?? 'none');
                        echo ", ID: " . ($client->userId ?? 'none') . "\n";

                        if (isset($client->clientType) &&
                            $client->clientType === $data['recipientType'] &&
                            isset($client->userId) &&
                            $client->userId == $data['recipientId']) {

                            echo "¡Destinatario encontrado! Enviando mensaje...\n";
                            $client->send(json_encode([
                                'type' => $data['type'],
                                'data' => $data['data']
                            ]));
                            $found = true;
                            break;
                        }
                    }

                    if (!$found) {
                        echo "Destinatario no encontrado, devolviendo mensaje a la cola\n";
                        $this->redisClient->lpush('trip_notifications_queue', $notification);
                    }
                } else if (!$shouldDeliver) {
                    echo "Notificación descartada por antigüedad.\n";
                }
            } else {
                echo "No hay mensajes nuevos en Redis\n";
            }
        } catch (\Exception $e) {
            echo "Error en processRedisNotifications: " . $e->getMessage() . "\n";
        }
    }

    // Enviar la notificación al cliente específico
    public function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);

        // Enviar mensaje de conexión exitosa
        $conn->send(json_encode([
            'type' => 'connection_status',
            'data' => [
                'status' => 'connected',
                'clientId' => $conn->resourceId
            ]
        ]));

        echo "Nueva conexión! ({$conn->resourceId})\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Error detallado: {$e->getMessage()}\n";
        echo "Traza: {$e->getTraceAsString()}\n";
        $conn->close();
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        try {
            echo "Mensaje recibido RAW: $msg\n";
            $data = json_decode($msg, true);
            echo "Mensaje decodificado: " . print_r($data, true) . "\n";

            if (!$data || !isset($data['type'])) {
                $from->send(json_encode([
                    'type' => 'error',
                    'message' => 'Formato de mensaje inválido'
                ]));
                return;
            }

            switch ($data['type']) {
                case 'identify':
                            if (!isset($data['clientType']) || !isset($data['userId'])) {
                                $from->send(json_encode([
                                    'type' => 'error',
                                    'message' => 'Datos de identificación incompletos'
                                ]));
                                return;
                            }

                            $from->clientType = $data['clientType'];
                            $from->userId = $data['userId'];

                            echo "\n=== Cliente identificado ===\n";
                            echo "Tipo: {$from->clientType}\n";
                            echo "ID: {$from->userId}\n";

                            // Confirmar identificación
                            $from->send(json_encode([
                                'type' => 'identify_confirm',
                                'data' => [
                                    'clientType' => $from->clientType,
                                    'userId' => $from->userId
                                ]
                            ]));

                    if ($from->clientType === 'driver' && isset($data['driverData'])) {
                        $this->registerDriver($from, $data['driverData']);
                    }
                    elseif ($from->clientType === 'citizen') {
                        $this->sendInitialDriversList($from);
                    }
                    break;

                case 'update_location':
                    if (!isset($from->clientType) || $from->clientType !== 'driver') {
                        return;
                    }
                    if (isset($from->userId) && isset($data['location'])) {
                        $this->updateDriverLocation($from->userId, $data['location']);
                    }
                    break;
                case 'trip_request':
                    if (!isset($from->clientType) || $from->clientType !== 'citizen') {
                        return;
                    }
                    $this->handleTripRequest($from, $data['data']);
                    break;

                case 'trip_response':
                    if (!isset($from->clientType) || $from->clientType !== 'driver') {
                        return;
                    }
                    $this->handleTripResponse($from, $data['data']);
                    break;

                case 'trip_status_update':
                    if (!isset($from->clientType) || !in_array($from->clientType, ['driver', 'citizen'])) {
                        return;
                    }
                    $this->handleTripStatusUpdate($from, $data['data']);
                    break;
            }
        } catch (\Exception $e) {
            echo "Error procesando mensaje: " . $e->getMessage() . "\n";
            $from->send(json_encode([
                'type' => 'error',
                'message' => 'Error interno del servidor'
            ]));
        }
    }

    protected function handleTripRequest(ConnectionInterface $from, array $tripData)
    {
        $tripId = $tripData['tripId'];
        $targetDriverId = $tripData['driverId'];

        // Guardar el viaje en memoria
        $this->activeTrips[$tripId] = [
            'passengerId' => $from->userId,
            'driverId' => $targetDriverId,
            'status' => 'pending',
            'data' => $tripData
        ];

        // Enviar solicitud al conductor
        foreach ($this->clients as $client) {
            if (isset($client->clientType) &&
                $client->clientType === 'driver' &&
                isset($client->userId) &&
                $client->userId === $targetDriverId) {

                $client->send(json_encode([
                    'type' => 'trip_request',
                    'data' => $tripData
                ]));
                break;
            }
        }
    }

    protected function handleTripResponse(ConnectionInterface $from, array $responseData)
    {
        $tripId = $responseData['tripId'];

        if (!isset($this->activeTrips[$tripId])) {
            return;
        }

        $passengerId = $this->activeTrips[$tripId]['passengerId'];

        // Actualizar estado del viaje
        $this->activeTrips[$tripId]['status'] = $responseData['accepted'] ? 'accepted' : 'rejected';

        // Notificar al pasajero
        foreach ($this->clients as $client) {
            if (isset($client->clientType) &&
                $client->clientType === 'citizen' &&
                isset($client->userId) &&
                $client->userId === $passengerId) {

                $client->send(json_encode([
                    'type' => 'trip_response',
                    'data' => $responseData
                ]));
                break;
            }
        }
    }

    protected function handleTripStatusUpdate(ConnectionInterface $from, array $statusData)
    {
        $tripId = $statusData['tripId'];

        if (!isset($this->activeTrips[$tripId])) {
            return;
        }

        $trip = $this->activeTrips[$tripId];
        $recipientType = $from->clientType === 'driver' ? 'citizen' : 'driver';
        $recipientId = $from->clientType === 'driver' ? $trip['passengerId'] : $trip['driverId'];

        // Actualizar estado del viaje
        $this->activeTrips[$tripId]['status'] = $statusData['status'];

        // Notificar al otro participante
        foreach ($this->clients as $client) {
            if (isset($client->clientType) &&
                $client->clientType === $recipientType &&
                isset($client->userId) &&
                $client->userId === $recipientId) {

                $client->send(json_encode([
                    'type' => 'trip_status_update',
                    'data' => $statusData
                ]));
                break;
            }
        }

        // Limpiar viaje si está completado o cancelado
        if (in_array($statusData['status'], ['completed', 'canceled'])) {
            unset($this->activeTrips[$tripId]);
        }
    }

    protected function sendInitialDriversList(ConnectionInterface $conn)
    {
        if (!empty($this->activeDrivers)) {
            $conn->send(json_encode([
                'type' => 'initial_drivers',
                'drivers' => array_values($this->activeDrivers)
            ]));
        } else {
            $conn->send(json_encode([
                'type' => 'initial_drivers',
                'drivers' => []
            ]));
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        // Eliminar la conexión
        $this->clients->detach($conn);

        // Si era un conductor, eliminarlo de la lista de conductores activos
        if (isset($conn->clientType) && $conn->clientType === 'driver' && isset($conn->userId)) {
            $this->removeDriver($conn->userId);
        }

        echo "Conexión {$conn->resourceId} cerrada\n";
    }

    protected function registerDriver(ConnectionInterface $conn, array $driverData)
    {
        $driverId = $conn->userId;
        echo "Iniciando registro de conductor $driverId\n";

        // Verificar datos requeridos
        if (!isset($driverData['fullName']) || !isset($driverData['image']) ||
            !isset($driverData['location']) || !isset($driverData['rating'])) {
            echo "Error: Faltan datos requeridos del conductor\n";
            return;
        }

        // Verificar si el conductor ya está registrado
        if (isset($this->activeDrivers[$driverId])) {
            // Buscar y cerrar la conexión anterior
            foreach ($this->clients as $client) {
                if (isset($client->clientType) &&
                    $client->clientType === 'driver' &&
                    isset($client->userId) &&
                    $client->userId === $driverId &&
                    $client !== $conn) {
                    $client->close();
                    break;
                }
            }
        }

        // Guardar información del conductor
        $this->activeDrivers[$driverId] = [
            'id' => $driverId,
            'fullName' => $driverData['fullName'],
            'image' => $driverData['image'],
            'location' => $driverData['location'],
            'rating' => $driverData['rating'],
            'timestamp' => time()
        ];

        // Guardar en Redis
        $this->redisClient->hset('active_drivers', $driverId, json_encode($this->activeDrivers[$driverId]));

        // Notificar a todos los ciudadanos sobre el nuevo conductor
        $this->broadcastToClients('new_driver', $this->activeDrivers[$driverId]);

        echo "Conductor registrado: $driverId - {$driverData['fullName']}\n";
    }

    protected function updateDriverLocation($driverId, $location)
    {
        $now = time();
        $this->activeDrivers[$driverId]['location'] = $location;
        $this->activeDrivers[$driverId]['timestamp'] = $now;

        // Notificar a ciudadanos conectados
        foreach ($this->clients as $client) {
            if (isset($client->clientType) && $client->clientType === 'citizen') {
                $client->send(json_encode([
                    'type' => 'driver_location_update',
                    'data' => [
                        'id' => $driverId,
                        'location' => $location,
                        'timestamp' => $now
                    ]
                ]));
            }
        }
        // Si quieres encolar en Redis para otros procesos, incluye el timestamp
        // $this->redisClient->lpush('trip_notifications_queue', json_encode([
        //     'type' => 'driver_location_update',
        //     'recipientType' => 'citizen',
        //     'recipientId' => 'broadcast',
        //     'data' => [
        //         'id' => $driverId,
        //         'location' => $location
        //     ],
        //     'timestamp' => $now
        // ]));
    }

    protected function removeDriver(string $driverId)
    {
        if (!isset($this->activeDrivers[$driverId])) {
            return;
        }

        // Eliminar del array en memoria
        unset($this->activeDrivers[$driverId]);

        // Eliminar de Redis
        $this->redisClient->hdel('active_drivers', [$driverId]);

        // Notificar a todos los clientes
        $this->broadcastToClients('driver_offline', ['id' => $driverId]);

        echo "Conductor desconectado: $driverId\n";
    }

    protected function loadActiveDriversFromRedis()
    {
        $drivers = $this->redisClient->hgetall('active_drivers');
        if ($drivers) {
            foreach ($drivers as $id => $data) {
                $this->activeDrivers[$id] = json_decode($data, true);
            }
            echo "Cargados " . count($this->activeDrivers) . " conductores desde Redis\n";
        }
    }

    protected function broadcastToClients(string $type, array $data)
    {
        $message = json_encode([
            'type' => $type,
            'data' => $data
        ]);

        echo "Enviando broadcast: $message\n"; // Log del broadcast

        foreach ($this->clients as $client) {
            if (isset($client->clientType) && $client->clientType === 'citizen') {
                echo "Enviando a ciudadano ID: {$client->userId}\n";
                $client->send($message);
            }
        }
    }

    public function checkRedisNotifications()
    {
        try {
            // Verificar conexión a Redis
            if (!$this->redisClient->isConnected()) {
                echo "⚠️ Reconectando a Redis...\n";
                $this->redisClient->connect();
            }

            // Verificar si hay mensajes
            $len = $this->redisClient->llen('trip_notifications_queue');
            echo "📊 Mensajes en cola: $len\n";

            if ($len == 0) {
                return;
            }

            // Obtener notificación
            $notification = $this->redisClient->rpop('trip_notifications_queue');
            if (!$notification) {
                return;
            }

            echo "📩 Procesando notificación: $notification\n";

            $data = json_decode($notification, true);
            if (!$data) {
                echo "❌ Error decodificando JSON\n";
                return;
            }

            // Imprimir estado actual
            echo "\nClientes conectados:\n";
            foreach ($this->clients as $client) {
                echo "- Tipo: " . ($client->clientType ?? 'none');
                echo ", ID: " . ($client->userId ?? 'none') . "\n";
            }

            // Buscar destinatario
            foreach ($this->clients as $client) {
                if (!isset($client->clientType) || !isset($client->userId)) {
                    continue;
                }

                if ($client->clientType === $data['recipientType'] &&
                    (string)$client->userId === $data['recipientId']) {

                    echo "✅ Enviando a {$client->clientType} {$client->userId}\n";
                    $client->send(json_encode([
                        'type' => $data['type'],
                        'data' => $data['data']
                    ]));
                    return;
                }
            }

            echo "⚠️ Destinatario no encontrado, devolviendo a la cola\n";
            $this->redisClient->lpush('trip_notifications_queue', $notification);

        } catch (\Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "\n";
            echo "Traza: " . $e->getTraceAsString() . "\n";
        }
    }
}