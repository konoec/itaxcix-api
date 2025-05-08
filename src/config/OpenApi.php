<?php

namespace itaxcix\config;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    description: "Documentación de la API de autenticación y usuarios de iTaxcix",
    title: "iTaxcix API",
    contact: new OA\Contact(email: "soporte@itaxcix.com")
)]
#[OA\Server(url: "https://api.itaxcix.com", description: "Servidor de producción")]
#[OA\Server(url: "http://localhost:8080", description: "Entorno local de desarrollo")]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    bearerFormat: "JWT",
    scheme: "bearer"
)]
class OpenApi {}