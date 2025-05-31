<?php

namespace itaxcix\Infrastructure\Web\Controller\generic;

use OpenApi\Attributes as OA;

#[OA\Info(version: "1.0.0", title: "iTaxCix API")]
#[OA\Server(url: "http://localhost/api/v1")]
class OpenApi {}