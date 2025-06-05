<?php

require __DIR__ . '/../vendor/autoload.php';

// Carga configuración de WebSockets
$config = require __DIR__ . '/../config/ws.php';

use itaxcix\Infrastructure\WebSocket\DriverStatusHandler;
use itaxcix\Infrastructure\WebSocket\WebSocketServer;

// Instancia y arranca el servidor
$server = new WebSocketServer($config, new DriverStatusHandler());
$server->run();