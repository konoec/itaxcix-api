<?php

namespace itaxcix\Infrastructure\Web\Controller\generic;

use OpenApi\Attributes as OA;

#[OA\Info(version: "1.0.0", title: "iTaxCix API")]
#[OA\Server(url: "http://localhost/api/v1")]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    bearerFormat: "JWT",
    scheme: "bearer"
)]

// Añadir documentación sobre WebSockets
#[OA\ExternalDocumentation(
    description: "Documentación de WebSockets",
    url: "/web/v1/websocket-docs"
)]
class OpenApi {}