<?php

namespace itaxcix\Infrastructure\Web\Controller\docs;

use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class DocsController {
    public function index(ServerRequestInterface $request): ResponseInterface {
        $html = file_get_contents(__DIR__ . '/../../../../../public/swagger-ui/index.html');
        $response = new Response(200, ['Content-Type' => 'text/html'], $html);
        return $response;
    }
}