<?php

namespace itaxcix\Infrastructure\Database\Config;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Dotenv\Dotenv;

class EntityManagerFactory {
    public static function create(): EntityManager {
        // Cargar variables de entorno
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 4));
        $dotenv->load();

        // Configuración del ORM
        $isDevMode = $_ENV['APP_ENV'] === 'dev';

        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: [dirname(__DIR__, 3) . '/Entity'],
            isDevMode: $isDevMode
        );

        // Parámetros de conexión
        $connectionParams = [
            'driver' => 'pdo_pgsql',
            'host' => $_ENV['DB_HOST'],
            'port' => $_ENV['DB_PORT'],
            'dbname' => $_ENV['DB_DATABASE'],
            'user' => $_ENV['DB_USERNAME'],
            'password' => $_ENV['DB_PASSWORD'],
        ];

        $connection = DriverManager::getConnection($connectionParams, $config);

        return new EntityManager($connection, $config);
    }
}