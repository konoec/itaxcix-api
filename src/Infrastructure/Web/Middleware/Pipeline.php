<?php

namespace itaxcix\Infrastructure\Web\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Pipeline implements RequestHandlerInterface {
    private array $middlewares = [];
    private RequestHandlerInterface $handler;

    public function __construct(RequestHandlerInterface $handler) {
        $this->handler = $handler;
    }

    public function pipe(MiddlewareInterface $middleware): void {
        $this->middlewares[] = $middleware;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface {
        $handler = $this->handler;

        foreach (array_reverse($this->middlewares) as $middleware) {
            $handler = new class($middleware, $handler) implements RequestHandlerInterface {
                public function __construct(
                    private readonly MiddlewareInterface $middleware,
                    private readonly RequestHandlerInterface $next
                ) {}

                public function handle(ServerRequestInterface $request): ResponseInterface {
                    return $this->middleware->process($request, $this->next);
                }
            };
        }

        return $handler->handle($request);
    }
}