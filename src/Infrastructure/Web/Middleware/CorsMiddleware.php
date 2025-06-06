<?php

namespace itaxcix\Infrastructure\Web\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class CorsMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $response = $handler->handle($request)
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withHeader('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
            ->withHeader('Vary', 'Origin');

        if ($request->getMethod() === 'OPTIONS') {
            return $response->withStatus(204);
        }

        // Agregar logs
        print_r('==== CORS Debug ====');
        print_r('URI: ' . $request->getUri()->getPath());
        print_r('Method: ' . $request->getMethod());
        print_r('Headers: ' . json_encode($request->getHeaders()));

        // Log de respuesta
        print_r('Response status: ' . $response->getStatusCode());
        print_r('Response headers: ' . json_encode($response->getHeaders()));

        return $response;
    }
}