<?php

namespace itaxcix\middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;

class JwtMiddleware implements MiddlewareInterface
{
    private string $secretKey;

    public function __construct(string $secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public function process(Request $request, Handler $handler): Response
    {
        // Obtener encabezado Authorization
        $authHeader = $request->getHeaderLine('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return $this->unauthorized($request, "Token no proporcionado", 401);
        }

        $token = substr($authHeader, 7); // Quitar "Bearer "

        if (empty($token)) {
            return $this->unauthorized($request, "Token no proporcionado", 401);
        }

        try {
            $decoded = JWT::decode($token, new Key($this->secretKey, 'HS256'));
            $userData = (array) $decoded;

            // Adjuntar los datos del usuario al request
            $request = $request->withAttribute('user', $userData);

            // Pasar el request actualizado al siguiente middleware/controlador
            return $handler->handle($request);

        } catch (ExpiredException $e) {
            return $this->unauthorized($request, "Token expirado", 401);
        } catch (SignatureInvalidException $e) {
            return $this->unauthorized($request, "Firma del token inválida", 401);
        } catch (\Exception $e) {
            return $this->unauthorized($request, "Token inválido", 401);
        }
    }

    private function unauthorized(Request $request, string $message, int $code): Response
    {
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
        $body = json_encode($responseBody);
        $response = $response
            ->withStatus($code)
            ->withHeader('Content-Type', 'application/json');
        $response->getBody()->write($body);

        return $response;
    }
}