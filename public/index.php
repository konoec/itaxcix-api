<?php

use DI\ContainerBuilder;
use Doctrine\ORM\EntityManagerInterface;
use FastRoute\Dispatcher;
use Nyholm\Psr7\Factory\Psr17Factory;
use function FastRoute\simpleDispatcher;

require_once __DIR__ . '/../vendor/autoload.php';

// Cargar el EntityManager desde Bootstrap
$entityManager = require __DIR__ . '/../src/config/Bootstrap.php';

// Configurar PHP-DI
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions([
    EntityManagerInterface::class => fn() => $entityManager,
]);
$container = $containerBuilder->build();

// Cargar rutas
$routeDefinitionCallback = require __DIR__ . '/../src/config/Api.php';
$dispatcher = simpleDispatcher($routeDefinitionCallback);

// Procesar la solicitud
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== ($pos = strpos($uri, '?'))) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
        break;

    case Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed', 'allowed' => $allowedMethods]);
        break;

    case Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        // ✅ Crear PSR-17 Factory
        $psr17Factory = new Psr17Factory();

        // ✅ Crear ServerRequest desde globales
        $uri = $_SERVER['REQUEST_URI'];
        $method = $_SERVER['REQUEST_METHOD'];
        $serverParams = $_SERVER;

        $bodyContent = file_get_contents('php://input');

        $stream = $psr17Factory->createStream($bodyContent);

        $request = $psr17Factory->createServerRequest($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], $_SERVER)
            ->withBody($stream);

        // ✅ Agregar headers al request
        foreach (getallheaders() as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        // ✅ Añadir parámetros de ruta como atributo del request
        $request = $request->withAttribute('route_params', $vars);

        // ✅ Crear respuesta
        $response = $psr17Factory->createResponse();

        // ✅ Inyectar dependencias y ejecutar controlador
        [$class, $method] = $handler;
        $controller = $container->get($class);
        $response = $controller->$method($request, $response);

        // ✅ Enviar respuesta usando funciones nativas de PHP
        // (en lugar de ResponseSender, que no existe en nyholm/psr7 v3+)

        // Enviar código de estado
        http_response_code($response->getStatusCode());

        // Enviar encabezados
        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value));
            }
        }

        // Enviar cuerpo de la respuesta
        echo $response->getBody();
        break;
}