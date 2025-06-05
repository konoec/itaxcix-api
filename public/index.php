<?php

use itaxcix\Infrastructure\Web\Http\Kernel;

require_once __DIR__ . '/../vendor/autoload.php';

// Cargar variables de entorno
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Configurar zona horaria
date_default_timezone_set(getenv('APP_TIMEZONE') ?: 'America/Lima');

// Iniciar contenedor
$container = require __DIR__ . '/../config/container.php';

// Cargar rutas
$routeDefinitionCallback = require __DIR__ . '/../src/Infrastructure/Web/Routes/routes.php';

// Iniciar kernel
$kernel = new Kernel($container, $routeDefinitionCallback);

// Correr aplicación
$kernel->run();