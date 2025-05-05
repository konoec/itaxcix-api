<?php

namespace itaxcix\controllers;

use OpenApi\Attributes as OA;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

#[OA\Get(
    path: "/api/v1/hello/{name}",
    summary: "Saludar a un usuario",
    tags: ["Hello"],
    parameters: [
        new OA\Parameter(name: "name", description: "Nombre del usuario", in: "path", required: true)
    ],
    responses: [
        new OA\Response(
            response: "200",
            description: "Mensaje de saludo exitoso",
            content: new OA\JsonContent(properties: [
                new OA\Property(property: "message", type: "string")
            ])
        )
    ]
)]
class HelloController
{
    public function sayHello(Request $request, Response $response): Response
    {
        // Obtener los parámetros de la ruta inyectados previamente
        $routeParams = $request->getAttribute('route_params');

        // Extraer 'name'
        $name = $routeParams['name'] ?? 'World';

        // Sanitizar nombre
        $name = htmlspecialchars($name);

        // Preparar respuesta JSON
        $payload = json_encode(['message' => "Hola, $name"]);
        $response->getBody()->write($payload);

        return $response
            ->withStatus(200)
            ->withHeader('Content-Type', 'application/json');
    }
}