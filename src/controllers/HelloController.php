<?php

namespace itaxcix\controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "General", description: "Endpoints relacionados con utilidades y pruebas")]
class HelloController extends BaseController {
    #[OA\Get(
        path: "/hello/{name}",
        operationId: "sayHello",
        summary: "Saludar al usuario",
        security: [["bearerAuth" => []]],
        tags: ["General"],
        parameters: [
            new OA\PathParameter(name: "name", schema: new OA\Schema(type: "string"))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Respuesta exitosa",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "message", type: "string"),
                        new OA\Property(property: "user", type: "object")
                    ]
                )
            )
        ]
    )]
    /**
     * Saluda al usuario autenticado.
     *
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function sayHello(Request $request, Response $response): Response {
        $user = $request->getAttribute('user');

        if ($user && isset($user->alias)) {
            error_log("Usuario autenticado: {$user->alias}");
        }

        $routeParams = $request->getAttribute('route_params');
        $name = htmlspecialchars($routeParams['name'] ?? 'World');

        return $this->respondWithJson($response, [
            'message' => "Hola, $name",
            'user' => $user ? (array)$user : null,
        ], 200);
    }
}