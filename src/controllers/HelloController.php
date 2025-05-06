<?php

namespace itaxcix\controllers;

use OpenApi\Attributes as OA;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
class HelloController
{
    #[OA\Get(
        path: "/api/v1/hello/{name}",
        summary: "Saludar a un usuario",
        tags: ["Hello"],
        parameters: [
            new OA\Parameter(
                name: "name",
                description: "Nombre del usuario",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(
                response: "200",
                description: "Mensaje de saludo exitoso",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string", example: "Hola, Juan"),
                        new OA\Property(
                            property: "user",
                            type: "object",
                            properties: [
                                new OA\Property(property: "id", type: "integer", example: 15),
                                new OA\Property(property: "alias", type: "string", example: "juanperez211"),
                                new OA\Property(property: "role", type: "string", example: "user"),
                                new OA\Property(property: "iss", type: "string", example: "itaxcix-api"),
                                new OA\Property(property: "iat", type: "integer", example: 1746521578),
                                new OA\Property(property: "exp", type: "integer", example: 1746525178)
                            ]
                        )
                    ]
                )
            ),
            new OA\Response(
                response: "401",
                description: "No autorizado - Token inválido o expirado",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "error", type: "object", properties: [
                            new OA\Property(property: "message", type: "string", example: "Token no proporcionado"),
                            new OA\Property(property: "code", type: "integer", example: 401),
                            new OA\Property(property: "status", type: "integer", example: 401),
                            new OA\Property(property: "timestamp", type: "string", format: "date-time"),
                            new OA\Property(property: "path", type: "string", example: "/api/v1/hello/Juan")
                        ])
                    ]
                )
            )
        ],
        security: [[ "bearerAuth" => [] ]]
    )]
    public function sayHello(Request $request, Response $response): Response
    {
        // Datos del usuario desde el token (si el middleware lo inyectó)
        $user = $request->getAttribute('user');

        // Registrar alias del usuario (opcional)
        if ($user && isset($user->alias)) {
            error_log("Usuario autenticado: {$user->alias}");
        }

        // Parámetro de ruta
        $routeParams = $request->getAttribute('route_params');
        $name = htmlspecialchars($routeParams['name'] ?? 'World');

        // Construir respuesta
        $payload = json_encode([
            'message' => "Hola, $name",
            'user' => $user ? (array)$user : null,
        ]);

        $response->getBody()->write($payload);

        return $response
            ->withStatus(200)
            ->withHeader('Content-Type', 'application/json');
    }
}