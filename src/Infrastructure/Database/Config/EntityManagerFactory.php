<?php

namespace itaxcix\Infrastructure\Database\Config;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Events;
use Doctrine\ORM\ORMSetup;
use Dotenv\Dotenv;
use itaxcix\Infrastructure\Database\EventListener\AuditEventListener;
use itaxcix\Core\Interfaces\audit\AuditLogRepositoryInterface;
use itaxcix\Infrastructure\Database\Repository\audit\DoctrineAuditLogRepository;

class EntityManagerFactory {
    public static function create(): EntityManager {
        // Cargar variables de entorno
        $dotenv = Dotenv::createImmutable(dirname(__DIR__, 4));
        $dotenv->load();

        // Configuración del ORM
        $isDevMode = $_ENV['APP_ENV'] === 'dev';

        $config = ORMSetup::createAttributeMetadataConfiguration(
            paths: [dirname(__DIR__) . '/Entity'],
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
        $entityManager = new EntityManager($connection, $config);

        // Configurar Event Listener para auditoría automática
        self::configureAuditListener($entityManager);

        return $entityManager;
    }

    private static function configureAuditListener(EntityManager $entityManager): void
    {
        $eventManager = $entityManager->getEventManager();

        // Crear el repositorio de auditoría
        $auditRepository = new DoctrineAuditLogRepository($entityManager);

        // Crear y registrar el listener de auditoría
        $auditListener = new AuditEventListener($auditRepository);

        // Registrar eventos
        $eventManager->addEventListener([
            Events::preUpdate,
            Events::postPersist,
            Events::postUpdate,
            Events::postRemove,
        ], $auditListener);
    }
}