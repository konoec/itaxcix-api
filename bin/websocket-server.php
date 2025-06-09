<?php

require dirname(__DIR__) . '/vendor/autoload.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use itaxcix\Infrastructure\WebSocket\DriverLocationHandler;
use Predis\Client as RedisClient;
use Dotenv\Dotenv;
use React\EventLoop\Factory;

// Cargar variables de entorno
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Configurar cliente Redis
$redisHost = $_ENV['REDIS_HOST'] ?? 'redis';
$redisPort = $_ENV['REDIS_PORT'] ?? 6379;

echo "Inicializando servidor WebSocket...\n";
echo "Conectando a Redis: $redisHost:$redisPort\n";

try {
    // Crear el loop de eventos compartido
    $loop = Factory::create();

    // Configurar Redis con timeout adecuado para operaciones normales
    $redis = new RedisClient([
        'scheme' => 'tcp',
        'host'   => $redisHost,
        'port'   => $redisPort,
        'read_write_timeout' => 60,
        'connection_timeout' => 5,
    ]);

    // Probar la conexión con un ping
    $redis->ping();
    echo "Conexión a Redis establecida correctamente\n";

    // Crear el handler
    $handler = new DriverLocationHandler($redis);

    // Configurar el servidor WebSocket con el mismo loop
    $server = IoServer::factory(
        new HttpServer(
            new WsServer($handler)
        ),
        8080,
        '0.0.0.0',
        $loop
    );

    echo "Servidor WebSocket iniciado en puerto 8080\n";

    // Agregar un timer periódico para mantener el proceso vivo
    $loop->addPeriodicTimer(60, function() {
        echo "Servidor WebSocket en ejecución... " . date('Y-m-d H:i:s') . "\n";
    });

    // Configurar timer para procesar notificaciones de viajes usando colas Redis
    $loop->addPeriodicTimer(0.5, function() use ($redis, $handler) {
        try {
            // Usar una cola en lugar de PubSub para mayor robustez
            $message = $redis->lpop('trip_notifications_queue');
            if ($message) {
                // Llamar al método de manejo de notificaciones existente en DriverLocationHandler
                if (method_exists($handler, 'handleTripNotification')) {
                    $handler->handleTripNotification($message);
                    echo "Notificación procesada: " . substr($message, 0, 100) . "\n";
                } else {
                    echo "handleTripNotification no existe en el handler\n";
                }
            }
        } catch (\Exception $e) {
            echo "Error procesando cola Redis: " . $e->getMessage() . "\n";
        }
    });

    echo "Servidor listo para recibir conexiones y notificaciones\n";

    // Iniciar el loop de eventos
    $loop->run();

} catch (\Predis\Connection\ConnectionException $e) {
    echo "Error de conexión con Redis: " . $e->getMessage() . "\n";
    echo "Verifica que Redis esté en ejecución y sea accesible desde $redisHost:$redisPort\n";
    exit(1);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}