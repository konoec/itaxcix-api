<?php

namespace itaxcix\controllers;

use OpenApi\Attributes as OA;

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
class HelloController {
    public function sayHello(array $vars): void
    {
        $name = htmlspecialchars($vars['name'] ?? 'World');
        echo json_encode(['message' => "Hola, $name"]);
    }
}