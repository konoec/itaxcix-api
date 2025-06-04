<?php

namespace itaxcix\Infrastructure\Auth\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use itaxcix\Infrastructure\Auth\Service\JwtService;
use Nyholm\Psr7\Response;

class JwtPermissionMiddleware implements MiddlewareInterface
{
    private JwtService $jwtService;
    private string $requiredPermission;

    public function __construct(JwtService $jwtService, string $requiredPermission)
    {
        $this->jwtService = $jwtService;
        $this->requiredPermission = $requiredPermission;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $header = $request->getHeaderLine('Authorization');
        if (!preg_match('/^Bearer\s+(.+)$/i', $header, $m)) {
            return new Response(401);
        }

        try {
            $claims = $this->jwtService->decode($m[1]);
        } catch (\Exception $e) {
            return new Response(401);
        }

        $userId = $claims['user_id'] ?? null;
        $permissions = $claims['permissions'] ?? [];

        if (!$userId) {
            return new Response(401);
        }

        if (!in_array($this->requiredPermission, $permissions, true)) {
            return new Response(403);
        }

        // Inyectar claims en la request para uso en controlador
        $request = $request
            ->withAttribute('user_id', $userId)
            ->withAttribute('roles', $claims['roles'] ?? [])
            ->withAttribute('permissions', $permissions);

        return $handler->handle($request);
    }
}