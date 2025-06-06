<?php

namespace itaxcix\Infrastructure\WebSocket;

use Clue\React\Redis\Factory as RedisFactory;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use SplObjectStorage;
use React\EventLoop\LoopInterface;

class DriverStatusHandler implements MessageComponentInterface
{
    private SplObjectStorage $clients;

    public function __construct(LoopInterface $loop)
    {
        $this->clients = new SplObjectStorage();
        $this->setupAsyncRedisSubscriber($loop);
    }

    private function setupAsyncRedisSubscriber(LoopInterface $loop): void
    {
        $factory = new RedisFactory($loop);
        $redisDsn = sprintf('redis://%s:%d', getenv('REDIS_HOST') ?: 'redis', getenv('REDIS_PORT') ?: 6379);
        $factory->createClient($redisDsn)->then(function ($client) {
            $client->subscribe('driver_status_changed');
            error_log('[WebSocket] Suscrito a canal Redis (async): driver_status_changed');
            $client->on('message', function ($message) {
                // $message es un array: ['kind' => 'message', 'channel' => ..., 'payload' => ...]
                if (isset($message['kind']) && $message['kind'] === 'message') {
                    error_log('[WebSocket] Mensaje recibido de Redis (async): ' . $message['payload']);
                    $this->broadcastMessage($message['payload']);
                }
            });
        }, function ($e) {
            error_log('[WebSocket] Error al conectar a Redis async: ' . $e->getMessage());
        });
    }

    private function broadcastMessage(string $message): void
    {
        // Log para depuración
        error_log('[WebSocket] Enviando mensaje a clientes: ' . $message);
        foreach ($this->clients as $client) {
            $client->send($message);
        }
    }

    public function onOpen(ConnectionInterface $conn): void
    {
        $this->clients->attach($conn);
        error_log('[WebSocket] Cliente conectado');
        $conn->send(json_encode(['test' => '¡Conexión exitosa!']));
    }

    public function onMessage(ConnectionInterface $from, $msg): void
    {
        // No-op
    }

    public function onClose(ConnectionInterface $conn): void
    {
        $this->clients->detach($conn);
        error_log('[WebSocket] Cliente desconectado');
    }

    public function onError(ConnectionInterface $conn, \Exception $e): void
    {
        error_log('[WebSocket] Error en conexión: ' . $e->getMessage());
        $conn->close();
    }
}
