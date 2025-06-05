<?php

namespace itaxcix\Infrastructure\Auth\Middleware;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use itaxcix\Infrastructure\Auth\Service\JwtService;

class JwtPermissionMiddleware implements MiddlewareInterface
{
    private JwtService $jwtService;
    private string $requiredPermission;
    private Psr17Factory $psr17Factory;

    public function __construct(JwtService $jwtService, string $requiredPermission)
    {
        $this->jwtService = $jwtService;
        $this->requiredPermission = $requiredPermission;
        $this->psr17Factory  = new Psr17Factory();
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $header = $request->getHeaderLine('Authorization');
        if (!preg_match('/^Bearer\s+(.+)$/i', $header, $m)) {
            return $this->errorResponse($request, 'Token no proporcionado o inválido', 401);
        }

        try {
            $claims = $this->jwtService->decode($m[1]);
        } catch (\Exception $e) {
            return $this->errorResponse($request, $e->getMessage(), 401);
        }

        $userId      = $claims['user_id'] ?? null;
        $permissions = $claims['permissions'] ?? [];

        if (!$userId) {
            return $this->errorResponse($request, 'Token inválido', 401);
        }

        if (!in_array($this->requiredPermission, $permissions, true)) {
            return $this->errorResponse($request, 'Permiso denegado', 403);
        }

        $request = $request
            ->withAttribute('user_id', $userId)
            ->withAttribute('roles', $claims['roles'] ?? [])
            ->withAttribute('permissions', $permissions);

        return $handler->handle($request);
    }

    private function errorResponse(ServerRequestInterface $request, string $message, int $code): ResponseInterface
    {
        $body = [
            'error' => [
                'message'   => $message,
                'code'      => $code,
                'status'    => $code,
                'timestamp' => date('c'),
                'path'      => $request->getUri()->getPath()
            ]
        ];

        $response = $this->psr17Factory->createResponse($code)
            ->withHeader('Content-Type', 'application/json');

        $stream = $this->psr17Factory->createStream(json_encode($body, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        return $response->withBody($stream);
    }
}