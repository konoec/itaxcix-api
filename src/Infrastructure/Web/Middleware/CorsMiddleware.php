<?php

namespace itaxcix\Infrastructure\Web\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

readonly class CorsMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly array $corsConfig = [
            'origin' => '*',
            'methods' => 'GET, POST, PUT, DELETE, PATCH, OPTIONS',
            'headers' => 'Origin, X-Requested-With, Content-Type, Accept, Authorization',
            'max_age' => '86400'
        ]
    ) {}

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request)
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', $this->corsConfig['methods'])
            ->withHeader('Access-Control-Allow-Headers', $this->corsConfig['headers'])
            ->withHeader('Access-Control-Max-Age', $this->corsConfig['max_age']);

        if ($request->getMethod() === 'OPTIONS') {
            return $response->withStatus(204);
        }

        return $response;
    }
}
