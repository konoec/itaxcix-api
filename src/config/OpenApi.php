<?php

namespace itaxcix\config;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.1",
    description: "Documentación de la API RESTful del aplicativo iTaxCix",
    title: "API iTaxCix",
    contact: new OA\Contact(
        name: "Desarrollador",
        email: "antoniopaiva2608@gmail.com"
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
