<?php

use DI\ContainerBuilder;
use Doctrine\ORM\EntityManagerInterface;
use itaxcix\Infrastructure\Auth\Interfaces\JwtEncoderInterface;
use itaxcix\Infrastructure\Auth\Middleware\JwtMiddleware;
use itaxcix\Infrastructure\Auth\Service\JwtService;
use itaxcix\Infrastructure\Database\Config\EntityManagerFactory;
use function DI\autowire;

$containerBuilder = new ContainerBuilder();

$containerBuilder->addDefinitions([
    // Doctrine
    EntityManagerInterface::class => function () {
        return EntityManagerFactory::create();
    },

    // JWT
    JwtEncoderInterface::class => function () {
        return new JwtService(
            secret: $_ENV['JWT_SECRET'],
            algorithm: 'HS256',
            expiresIn: 3600
        );
    },

    // Middlewares
    JwtMiddleware::class => autowire(),
]);

$containerBuilder->addDefinitions([
    require __DIR__ . '/repositories.php'
]);

try {
    return $containerBuilder->build();
} catch (Exception $e) {
    error_log("Error al construir el contenedor: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal Server Error']);
    exit(1);
}