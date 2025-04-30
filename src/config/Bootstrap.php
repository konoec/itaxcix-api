<?php

// Carga autoload de Composer
require_once __DIR__ . '/../../vendor/autoload.php';

// Carga las variables de entorno (.env)
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

// Carga la configuración de la base de datos
$config = require __DIR__ . '/Database.php';

// Inicializa Eloquent
use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;
if (!isset($config['connections'][$config['default']])) {
    die("Error: Conexión predeterminada '{$config['default']}' no configurada.");
}

$capsule->addConnection($config['connections'][$config['default']]);
$capsule->setAsGlobal();
$capsule->bootEloquent();