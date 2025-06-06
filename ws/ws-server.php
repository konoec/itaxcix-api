<?php

// Oculta los warnings deprecados de PHP 8.2+
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_DEPRECATED);

require __DIR__ . '/../vendor/autoload.php';

use itaxcix\Infrastructure\WebSocket\DriverStatusHandler;
use itaxcix\Infrastructure\WebSocket\WebSocketServer;
use React\EventLoop\Factory as LoopFactory;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\Server\IoServer;

// Carga configuración de WebSockets
$config = require __DIR__ . '/../config/ws.php';

// Crea el event loop de ReactPHP
$loop = LoopFactory::create();

// Instancia el handler solo con el event loop
$handler = new DriverStatusHandler($loop);

// Instancia el servidor Ratchet usando el mismo loop
$wsServer = new WsServer($handler);
$httpServer = new HttpServer($wsServer);
$ioServer = IoServer::factory($httpServer, $config['port'], $config['host'], $loop);

$loop->run();
