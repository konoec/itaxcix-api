<?php

namespace itaxcix\Infrastructure\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use SplObjectStorage;
use itaxcix\Infrastructure\Cache\RedisService;

class DriverStatusHandler implements MessageComponentInterface
{
    private SplObjectStorage $clients;
    private RedisService $redisService;

    public function __construct(RedisService $redisService)
    {
        $this->clients = new SplObjectStorage();
        $this->redisService = $redisService;
        $this->forkRedisSubscriber();
    }

    private function forkRedisSubscriber(): void
    {
        if (!function_exists('pcntl_fork')) {
            return;
        }

        $pid = pcntl_fork();

        if ($pid === -1) {
            return;
        }

        if ($pid === 0) {
            $this->listenToRedisChannel();
            exit(0);
        }
    }

    private function listenToRedisChannel(): void
    {
        try {
            $pubsub = $this->redisService->getClient()->pubSubLoop();
            $pubsub->subscribe('driver_status_changed');

            foreach ($pubsub as $message) {
                if (isset($message->kind) && $message->kind === 'message') {
                    $this->broadcastMessage($message->payload);
                }
            }

            unset($pubsub);
        } catch (\Exception $e) {
            // Silenciado
        }
    }

    private function broadcastMessage(string $message): void
    {
        foreach ($this->clients as $client) {
            $client->send($message);
        }
    }

    public function onOpen(ConnectionInterface $conn): void
    {
        $this->clients->attach($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg): void
    {
        // No-op
    }

    public function onClose(ConnectionInterface $conn): void
    {
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e): void
    {
        $conn->close();
    }
}
