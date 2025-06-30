<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use itaxcix\Infrastructure\WebSocket\DriverLocationHandler;
use itaxcix\Infrastructure\WebSocket\AuthenticatedWebSocketHandler;
use itaxcix\Infrastructure\WebSocket\WebSocketAuthService;
use Predis\Client as RedisClient;
use Dotenv\Dotenv;
use React\EventLoop\Loop;

// Cargar variables de entorno
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$redisHost = $_ENV['REDIS_HOST'] ?? 'redis';
$redisPort = $_ENV['REDIS_PORT'] ?? 6379;

echo "Inicializando servidor WebSocket con autenticación JWT...\n";

try {
    // Configurar Redis
    $redis = new RedisClient([
        'scheme' => 'tcp',
        'host'   => $redisHost,
        'port'   => $redisPort,
        'read_write_timeout' => 0
    ]);

    $loop = \React\EventLoop\Factory::create();

    // Crear el handler principal
    $driverHandler = new DriverLocationHandler($redis, $loop);

    // Crear el servicio de autenticación
    $authService = new WebSocketAuthService();

    // Envolver el handler con autenticación
    $authenticatedHandler = new AuthenticatedWebSocketHandler($driverHandler, $authService);

    $socket = new \React\Socket\Server('0.0.0.0:8080', $loop);
    $server = new IoServer(
        new HttpServer(
            new WsServer($authenticatedHandler)
        ),
        $socket,
        $loop
    );

    // Agregar timer para verificar notificaciones Redis
    $loop->addPeriodicTimer(0.5, function () use ($driverHandler) {
        try {
            echo "\n=== Verificando notificaciones ===\n";
            $driverHandler->checkRedisNotifications();
        } catch (\Exception $e) {
            echo "Error en timer: " . $e->getMessage() . "\n";
        }
    });

    echo "🔒 Servidor WebSocket seguro iniciado en puerto 8080\n";
    echo "✅ Autenticación JWT habilitada\n";
    echo "📋 Métodos de autenticación soportados:\n";
    echo "   - Query parameter: ?token=JWT_TOKEN\n";
    echo "   - Header Authorization: Bearer JWT_TOKEN\n";
    echo "   - WebSocket subprotocol: token.JWT_TOKEN\n\n";

    $loop->run();
} catch (\Exception $e) {
    echo "❌ Error al iniciar el servidor WebSocket: " . $e->getMessage() . "\n";
    exit(1);
}