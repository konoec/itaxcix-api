<?php
return [
    'host' => $_ENV['WS_HOST'] ?? '0.0.0.0',
    'port' => (int) ($_ENV['WS_PORT'] ?? 8080),
    'path' => '/ws',
];