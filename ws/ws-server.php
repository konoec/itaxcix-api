<?php

require __DIR__ . '/../vendor/autoload.php';

use itaxcix\Infrastructure\WebSocket\DriverStatusHandler;
use itaxcix\Infrastructure\WebSocket\WebSocketServer;
use DI\ContainerBuilder;
use Vonage\Voice\Endpoint\Websocket;

// Carga configuración de WebSockets
$config = require __DIR__ . '/../config/ws.php';

// Construye el contenedor de dependencias
$container = (new ContainerBuilder())->build();

// Obtiene el handler desde el contenedor (con Redis inyectado)
$handler = $container->get(DriverStatusHandler::class);

// Instancia y arranca el servidor
$server = new WebSocketServer($config, $handler);
$server->run();