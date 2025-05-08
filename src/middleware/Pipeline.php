<?php

namespace itaxcix\middleware;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Pipeline implements RequestHandlerInterface {
    private array $middlewares = [];

    public function pipe(MiddlewareInterface $middleware): void {
        $this->middlewares[] = $middleware;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface {
        $stack = array_reduce(
            array_reverse($this->middlewares),
            fn(?callable $next, MiddlewareInterface $middleware) => fn() => $middleware->process($request, $next),
            fn() => $this->runController($request)
        );

        return $stack();
    }

    private function runController(ServerRequestInterface $request): ResponseInterface {
        [$class, $method] = $request->getAttribute('route_handler');
        $container = $request->getAttribute('container');

        $controller = $container->get($class);
        return $controller->$method($request, new Response());
    }
}