<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Dotenv\Dotenv;

require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$config = ORMSetup::createAttributeMetadataConfiguration(
    paths: [__DIR__ . "/../../src/models/entities"],
    isDevMode: true,
);

$connectionParams = [
    'driver'   => 'pdo_pgsql',
    'host'     => $_ENV['DB_HOST'],
    'port'     => $_ENV['DB_PORT'],
    'dbname'   => $_ENV['DB_DATABASE'],
    'user'     => $_ENV['DB_USERNAME'],
    'password' => $_ENV['DB_PASSWORD'],
];

$conn = DriverManager::getConnection($connectionParams, $config);

$entityManager = new EntityManager($conn, $config);

$GLOBALS['entityManager'] = $entityManager;