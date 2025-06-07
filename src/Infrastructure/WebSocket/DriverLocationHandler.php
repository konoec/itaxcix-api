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

    public function __construct(RedisClient $redisClient)
    {
        $this->clients = new \SplObjectStorage;
        $this->redisClient = $redisClient;

        // Cargar conductores activos desde Redis al inicio
        $this->loadActiveDriversFromRedis();
    }

    public function onOpen(ConnectionInterface $conn)
    {
        // Almacenar la nueva conexión
        $this->clients->attach($conn);
        $conn->clientType = null; // Se establecerá cuando el cliente envíe su identificación

        echo "Nueva conexión! ({$conn->resourceId})\n";

        // Enviar conductores activos al nuevo cliente
        if (!empty($this->activeDrivers)) {
            $conn->send(json_encode([
                'type' => 'initial_drivers',
                'drivers' => array_values($this->activeDrivers)
            ]));
        }
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