<?php

namespace itaxcix\config;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    description: "Documentación automática generada con zircote/swagger-php",
    title: "Mi API RESTful",
    contact: new OA\Contact(
        name: "Soporte Técnico",
        email: "soporte@example.com"
    ),
    license: new OA\License(
        name: "MIT",
        url: "https://opensource.org/licenses/MIT"
    )
)]

#[OA\Server(
    url: "http://localhost:80",
    description: "Servidor local de desarrollo"
)]
class OpenApi {}
