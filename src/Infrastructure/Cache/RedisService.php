<?php

namespace itaxcix\Infrastructure\Cache;

use Predis\Client;

class RedisService
{
    private Client $client;

    // En RedisService.php
    public function __construct()
    {
        $config = [
            'scheme' => 'tcp',
            'host'   => $_ENV['REDIS_HOST'] ?? '127.0.0.1',
            'port'   => $_ENV['REDIS_PORT'] ?? 6379,
            'read_write_timeout' => 0
        ];

        try {
            $this->client = new Client($config);
            $this->client->connect();
        } catch (\Exception $e) {
            echo "Error conectando a Redis: " . $e->getMessage() . "\n";
            throw $e;
        }
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}
