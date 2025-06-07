<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use itaxcix\Infrastructure\Websocket\DriverLocationHandler;
use Predis\Client as RedisClient;
use Dotenv\Dotenv;

// Cargar variables de entorno
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Configurar cliente Redis
$redisHost = $_ENV['REDIS_HOST'] ?? 'redis';
$redisPort = $_ENV['REDIS_PORT'] ?? 6379;
$redis = new RedisClient([
    'scheme' => 'tcp',
    'host'   => $redisHost,
    'port'   => $redisPort,
]);

echo "Inicializando servidor WebSocket...\n";
echo "Conectando a Redis: $redisHost:$redisPort\n";

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new DriverLocationHandler($redis)
        )
    ),
    8080
);

echo "Servidor WebSocket iniciado en puerto 8080\n";
$server->run();