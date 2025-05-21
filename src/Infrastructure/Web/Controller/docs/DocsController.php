<?php

namespace itaxcix\Infrastructure\Web\Controller\docs;

class DocsController {
    public function index(): string {
        return file_get_contents(__DIR__ . '/../../../../../public/swagger-ui/index.html');
    }
}