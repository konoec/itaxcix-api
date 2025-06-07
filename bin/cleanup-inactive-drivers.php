<?php
// bin/cleanup-inactive-drivers.php

require dirname(__DIR__) . '/vendor/autoload.php';

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

echo "Iniciando limpieza de conductores inactivos...\n";

// Tiempo límite (15 minutos = 900 segundos)
$timeLimit = time() - 900;
$drivers = $redis->hgetall('active_drivers');
$removedCount = 0;

foreach ($drivers as $id => $data) {
    $driver = json_decode($data, true);
    if (isset($driver['timestamp']) && $driver['timestamp'] < $timeLimit) {
        echo "Eliminando conductor inactivo: {$driver['fullName']} (ID: $id)\n";
        $redis->hdel('active_drivers', [$id]);
        $removedCount++;
    }
}

echo "Limpieza completada. Se eliminaron $removedCount conductores inactivos.\n";