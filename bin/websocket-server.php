<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use itaxcix\Infrastructure\WebSocket\DriverLocationHandler;
use Predis\Client as RedisClient;
use Dotenv\Dotenv;
use React\EventLoop\Loop;

// Cargar variables de entorno
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$redisHost = $_ENV['REDIS_HOST'] ?? 'redis';
$redisPort = $_ENV['REDIS_PORT'] ?? 6379;

echo "Inicializando servidor WebSocket...\n";

try {

    // Configurar Redis
    $redis = new RedisClient([
        'scheme' => 'tcp',
        'host'   => $redisHost,
        'port'   => $redisPort,
        'read_write_timeout' => 0
    ]);

    $loop = \React\EventLoop\Factory::create();
    $handler = new DriverLocationHandler($redis, $loop);

    $socket = new \React\Socket\Server('0.0.0.0:8080', $loop);
    $server = new IoServer(
        new HttpServer(
            new WsServer($handler)
        ),
        $socket,
        $loop
    );

    // Agregar timer con más frecuencia
    $loop->addPeriodicTimer(0.5, function () use ($handler) {
        try {
            echo "\n=== Verificando notificaciones ===\n";
            $handler->checkRedisNotifications();
        } catch (\Exception $e) {
            echo "Error en timer: " . $e->getMessage() . "\n";
        }
    });

    echo "Servidor WebSocket iniciado en puerto 8080\n";
    $loop->run();
} catch (\Exception $e) {
    echo "Error al iniciar el servidor WebSocket: " . $e->getMessage() . "\n";
    exit(1);
}