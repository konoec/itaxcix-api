<?php

namespace itaxcix\Infrastructure\WebSocket;

use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Ratchet\MessageComponentInterface;

class WebSocketServer
{
    private array $config;
    private MessageComponentInterface $handler;

    public function __construct(array $config, MessageComponentInterface $handler)
    {
        $this->config  = $config;
        $this->handler = $handler;
    }

    public function run(): void
    {
        $server = IoServer::factory(
            new HttpServer(new WsServer($this->handler)),
            $this->config['port'],
            $this->config['host']
        );
        $server->run();
    }
}