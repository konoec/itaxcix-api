<?php

namespace itaxcix\Infrastructure\Auth\Middleware;

use Exception;
use itaxcix\Infrastructure\Auth\Interfaces\JwtEncoderInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;

class JwtMiddleware implements MiddlewareInterface {
    private JwtEncoderInterface $jwt;

    public function __construct(JwtEncoderInterface $jwt) {
        $this->jwt = $jwt;
    }

    public function process(Request $request, Handler $handler): Response {
        // 1. Obtener el encabezado Authorization
        $authHeader = $request->getHeaderLine('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return $this->unauthorized($request, "Token no proporcionado", 401);
        }

        // 2. Extraer el token
        $token = substr($authHeader, 7); // Quitar "Bearer "

        if (empty($token)) {
            return $this->unauthorized($request, "Token vacío o inválido", 401);
        }

        try {
            // 3. Decodificar el token usando JwtService
            $userData = $this->jwt->decode($token);

            // 4. Adjuntar los datos del usuario al request
            $request = $request->withAttribute('user', $userData);

            // 5. Continuar con el siguiente middleware/controlador
            return $handler->handle($request);

        } catch (Exception $e) {
            return $this->unauthorized($request, $e->getMessage(), 401);
        }
    }

    /**
     * Devuelve una respuesta de error en formato JSON
     */
    private function unauthorized(Request $request, string $message, int $code): Response {
        $responseBody = [
            'error' => [
                'message' => $message,
                'code' => $code,
                'status' => $code,
                'timestamp' => date('c'),
                'path' => $request->getUri()->getPath()
            ]
        ];

        $response = new \Nyholm\Psr7\Response();
        $body = json_encode($responseBody, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        $response = $response
            ->withStatus($code)
            ->withHeader('Content-Type', 'application/json');
        $response->getBody()->write($body);

        return $response;
    }
}