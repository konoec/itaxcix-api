<?php

namespace itaxcix\Infrastructure\Cache;

use Predis\Client;

class RedisService
{
    private Client $client;

    public function __construct()
    {
        $config = [
            'scheme' => 'tcp',
            'host'   => $_ENV['REDIS_HOST'] ?? '127.0.0.1',
            'port'   => $_ENV['REDIS_PORT'] ?? 6379,
            'read_write_timeout' => 0
        ];

        $this->client = new Client($config);

        try {
            $this->client->connect();
        } catch (\Exception $e) {
            // Silenciado
        }
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}
