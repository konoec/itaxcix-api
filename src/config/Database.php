<?php

return [
    'default' => 'pgsql',
    'connections' => [
        'pgsql' => [
            'driver'   => 'pgsql',
            'host'     => $_ENV['DB_HOST'] ?? die('Variable DB_HOST no definida'),
            'port'     => $_ENV['DB_PORT'] ?? die('Variable DB_PORT no definida'),
            'database' => $_ENV['DB_NAME'] ?? die('Variable DB_NAME no definida'),
            'username' => $_ENV['DB_USER'] ?? die('Variable DB_USER no definida'),
            'password' => $_ENV['DB_PASSWORD'] ?? die('Variable DB_PASSWORD no definida'),
            'charset'  => 'utf8',
            'prefix'   => '',
            'schema'   => 'public',
        ],
    ],
];