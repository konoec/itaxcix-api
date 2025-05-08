<?php

use DI\ContainerBuilder;
use Doctrine\ORM\EntityManagerInterface;
use FastRoute\Dispatcher;
use itaxcix\middleware\JwtMiddleware;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function DI\autowire;
use function FastRoute\simpleDispatcher;

require_once __DIR__ . '/../vendor/autoload.php';

// Cargar EntityManager
$entityManager = require __DIR__ . '/../src/config/Bootstrap.php';

// Configurar PHP-DI
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions([
    EntityManagerInterface::class => fn() => $entityManager,
    JwtMiddleware::class => autowire()
        ->constructor($_ENV['JWT_SECRET']),
]);

$container = $containerBuilder->build();

// Cargar rutas
$routeDefinitionCallback = require __DIR__ . '/../src/config/Api.php';
$dispatcher = simpleDispatcher($routeDefinitionCallback);

// Procesar URI
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

        // PSR-17 Factory
        $psr17Factory = new Psr17Factory();

        // Procesar URI
        $uri = $_SERVER['REQUEST_URI'];
        if (false !== ($pos = strpos($uri, '?'))) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);

        // Body
        $bodyContent = file_get_contents('php://input');
        $stream = $psr17Factory->createStream($bodyContent);

        // Request
        $request = $psr17Factory->createServerRequest($_SERVER['REQUEST_METHOD'], $uri, $_SERVER)
            ->withBody($stream)
            ->withAttribute('route_params', $vars);

        // Headers
        foreach (getallheaders() as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        // Verificar si es una ruta protegida con middleware
        if (
            is_array($handler) &&
            count($handler) === 2 &&
            class_exists($handler[0]) &&
            is_subclass_of($handler[0], MiddlewareInterface::class) &&
            is_string($handler[1])
        ) {

            [$middlewareClass, $target] = $handler;
            [$controllerClass, $controllerMethod] = explode('@', $target);

            // Cargar middleware desde DI
            $middleware = $container->get($middlewareClass);

            // Handler final
            $handlerFn = function (ServerRequestInterface $request) use ($container, $controllerClass, $controllerMethod) {
                $controller = $container->get($controllerClass);
                return $controller->$controllerMethod($request, new Response());
            };

            // Ejecutar middleware
            $response = $middleware->process(
                $request,
                new class($handlerFn) implements RequestHandlerInterface {
                    private $handler;

                    public function __construct(callable $handler) {
                        $this->handler = $handler;
                    }

                    public function handle(ServerRequestInterface $request): ResponseInterface {
                        return ($this->handler)($request);
                    }
                }
            );

        } else {
            // Ruta pública sin middleware
            [$controllerClass, $controllerMethod] = $handler;
            $controller = $container->get($controllerClass);
            $response = $controller->$controllerMethod($request, new Response());
        }

        // Enviar respuesta
        http_response_code($response->getStatusCode());

        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value));
            }
        }

        echo $response->getBody();
        break;
}