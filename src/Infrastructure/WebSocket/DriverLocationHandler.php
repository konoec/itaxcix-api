<?php

namespace itaxcix\Infrastructure\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Predis\Client as RedisClient;

class DriverLocationHandler implements MessageComponentInterface
{
    protected $clients;
    protected $redisClient;
    protected $activeDrivers = [];

    // En el constructor actual, añadir:
    public function __construct(RedisClient $redisClient)
    {
        $this->clients = new \SplObjectStorage;
        $this->redisClient = $redisClient;
        $this->loadActiveDriversFromRedis();
    }

    // Método nuevo para configurar la suscripción Redis
    protected function subscribeToTripNotifications()
    {
        // Crear un cliente Redis separado para PubSub para evitar bloquear el cliente principal
        $pubsubClient = new RedisClient([
            'scheme' => 'tcp',
            'host'   => $_ENV['REDIS_HOST'] ?? 'redis',
            'port'   => $_ENV['REDIS_PORT'] ?? 6379,
            'read_write_timeout' => -1, // No timeout para conexiones pubsub
        ]);

        try {
            $pubsub = $pubsubClient->pubSubLoop();

            // Suscribirse al canal de notificaciones de viajes
            $pubsub->subscribe('trip_notifications');

            // Usar el loop de eventos de React que viene con Ratchet
            $loop = \React\EventLoop\Factory::create();

            // Agregar un manejador periódico para procesar mensajes de Redis
            $loop->addPeriodicTimer(0.1, function() use ($pubsub) {
                try {
                    // Solo procesar un mensaje a la vez para evitar bloqueo
                    $message = $pubsub->current();
                    if ($message && $message->kind === 'message') {
                        $this->handleTripNotification($message->payload);
                    }
                    $pubsub->next();
                } catch (\Exception $e) {
                    echo "Error en PubSub: " . $e->getMessage() . "\n";
                }
            });

            // En lugar de bloquear con run(), devolvemos el loop para que
            // el servidor principal lo gestione
            return $loop;
        } catch (\Exception $e) {
            echo "Error al iniciar subscripción a Redis: " . $e->getMessage() . "\n";
            return null;
        }
    }

// Reemplaza el método startListening
    public function startListening()
    {
        return $this->subscribeToTripNotifications();
    }

    // Procesar notificaciones de viajes
    public function handleTripNotification(string $payload)
    {
        $notification = json_decode($payload, true);

        if (!$notification || !isset($notification['type'])) {
            return;
        }

        // Extraer el destinatario y el mensaje
        $recipientType = $notification['recipientType'] ?? null;  // 'driver' o 'citizen'
        $recipientId = $notification['recipientId'] ?? null;
        $data = $notification['data'] ?? [];

        if (!$recipientType || !$recipientId) {
            echo "Notificación de viaje mal formateada\n";
            return;
        }

        // Enviar la notificación al destinatario específico
        $this->sendTripNotification($recipientType, $recipientId, $notification['type'], $data);
    }

    // Enviar la notificación al cliente específico
    protected function sendTripNotification(string $recipientType, string $recipientId, string $type, array $data)
    {
        $message = json_encode([
            'type' => $type,
            'data' => $data
        ]);

        foreach ($this->clients as $client) {
            if (isset($client->clientType) && $client->clientType === $recipientType &&
                isset($client->userId) && $client->userId === $recipientId) {
                $client->send($message);
                echo "Notificación de viaje enviada a $recipientType: $recipientId\n";
                return;
            }
        }

        echo "Destinatario $recipientType:$recipientId no encontrado para la notificación\n";
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Almacenar la nueva conexión
        $this->clients->attach($conn);
        $conn->clientType = null; // Se establecerá cuando el cliente envíe su identificación

        echo "Nueva conexión! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);

        if (!$data || !isset($data['type'])) {
            return;
        }

        switch ($data['type']) {
            case 'identify':
                // Cliente identificándose
                $from->clientType = $data['clientType']; // 'driver' o 'citizen'
                $from->userId = $data['userId'] ?? null;

                if ($from->clientType === 'driver' && isset($data['driverData'])) {
                    $this->registerDriver($from, $data['driverData']);
                }
                elseif ($from->clientType === 'citizen') {
                    // Enviar lista de conductores activos solo cuando un ciudadano se identifica
                    if (!empty($this->activeDrivers)) {
                        $from->send(json_encode([
                            'type' => 'initial_drivers',
                            'drivers' => array_values($this->activeDrivers)
                        ]));
                    }
                }
                break;

            case 'update_location':
                // Conductor actualizando su ubicación
                if ($from->clientType === 'driver' && isset($from->userId) && isset($data['location'])) {
                    $this->updateDriverLocation($from->userId, $data['location']);
                }
                break;
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

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }

    protected function registerDriver(ConnectionInterface $conn, array $driverData)
    {
        $driverId = $conn->userId;

        // Validar datos mínimos requeridos
        if (!isset($driverData['fullName']) || !isset($driverData['location']) ||
            !isset($driverData['image']) || !isset($driverData['rating'])) {
            echo "Datos del conductor incompletos\n";
            return;
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

    protected function updateDriverLocation(string $driverId, array $location)
    {
        if (!isset($this->activeDrivers[$driverId])) {
            echo "Intentando actualizar ubicación de conductor no registrado: $driverId\n";
            return;
        }

        // Actualizar ubicación
        $this->activeDrivers[$driverId]['location'] = $location;
        $this->activeDrivers[$driverId]['timestamp'] = time();

        // Actualizar en Redis
        $this->redisClient->hset('active_drivers', $driverId, json_encode($this->activeDrivers[$driverId]));

        // Notificar a los ciudadanos
        $this->broadcastToClients('driver_location_update', [
            'id' => $driverId,
            'location' => $location
        ]);

        echo "Ubicación actualizada para conductor: $driverId\n";
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

        foreach ($this->clients as $client) {
            // Solo enviar mensajes a los ciudadanos
            if (isset($client->clientType) && $client->clientType === 'citizen') {
                $client->send($message);
            }
        }
    }
}