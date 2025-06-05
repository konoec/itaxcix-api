<?php

use DI\ContainerBuilder;
use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Infrastructure\Auth\Interfaces\JwtEncoderInterface;
use itaxcix\Infrastructure\Auth\Middleware\JwtMiddleware;
use itaxcix\Infrastructure\Auth\Service\JwtService;
use itaxcix\Infrastructure\Database\Config\EntityManagerFactory;
use itaxcix\Infrastructure\Notifications\EmailNotificationService;
use itaxcix\Infrastructure\Notifications\NotificationServiceFactory;
use itaxcix\Infrastructure\Notifications\SmsNotificationService;
use itaxcix\Infrastructure\Web\Middleware\CorsMiddleware;
use function DI\autowire;
use function DI\get;

$containerBuilder = new ContainerBuilder();

$containerBuilder->addDefinitions([
    // Doctrine
    EntityManagerInterface::class => function () {
        return EntityManagerFactory::create();
    },

    // JWT
    JwtService::class => function () {
        return new JwtService(
            secret: $_ENV['JWT_SECRET'],
            algorithm: $_ENV['JWT_ALGORITHM'] ?? 'HS256',
            expiresIn: (int) ($_ENV['JWT_EXPIRES_IN'] ?? 3600)
        );
    },

    // Interfaz → mismo servicio
    JwtEncoderInterface::class => get(JwtService::class),

    // Middlewares
    JwtMiddleware::class => autowire(),

    CorsMiddleware::class => autowire(),

    // Servicios de notificación
    EmailNotificationService::class => autowire(),
    SmsNotificationService::class => autowire(),

    // Factory de notificaciones
    NotificationServiceFactory::class => function($container) {
        return new NotificationServiceFactory(
            $container->get(EmailNotificationService::class),
            $container->get(SmsNotificationService::class)
        );
    },
]);

$containerBuilder->addDefinitions(__DIR__ . '/repositories.php');
$containerBuilder->addDefinitions(__DIR__ . '/useCases.php');

try {
    return $containerBuilder->build();
} catch (Exception $e) {
    error_log("Error al construir el contenedor: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal Server Error']);
    exit(1);
}