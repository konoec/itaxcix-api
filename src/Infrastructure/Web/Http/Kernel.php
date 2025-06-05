<?php

namespace itaxcix\Infrastructure\Web\Http;

use Closure;
use DI\Container;
use Exception;
use FastRoute\Dispatcher;
use itaxcix\Infrastructure\Auth\Service\JwtService;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function FastRoute\simpleDispatcher;

class Kernel implements RequestHandlerInterface
{
    private Dispatcher $dispatcher;
    private Psr17Factory $psr17Factory;
    private Container $container;

    public function __construct(Container $container, callable $routeDefinitionCallback)
    {
        $this->container = $container;
        $this->dispatcher = simpleDispatcher($routeDefinitionCallback);
        $this->psr17Factory = new Psr17Factory();
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $httpMethod = $request->getMethod();
        $uri = $this->normalizeUri($request->getUri()->getPath());

        $routeInfo = $this->dispatcher->dispatch($httpMethod, $uri);

        return match ($routeInfo[0]) {
            Dispatcher::NOT_FOUND => $this->notFound(),
            Dispatcher::METHOD_NOT_ALLOWED => $this->methodNotAllowed($routeInfo[1]),
            Dispatcher::FOUND => $this->handleFoundRoute($request, $routeInfo[1], $routeInfo[2]),
            default => $this->serverError(),
        };
    }

    private function normalizeUri(string $uri): string
    {
        if (false !== ($pos = strpos($uri, '?'))) {
            $uri = substr($uri, 0, $pos);
        }
        return rawurldecode($uri);
    }

    private function notFound(): ResponseInterface
    {
        return $this->jsonResponse(404, ['error' => 'Ruta no encontrada']);
    }

    private function methodNotAllowed(array $allowedMethods): ResponseInterface
    {
        return $this->jsonResponse(405, [
            'error' => 'Método no permitido',
            'allowed_methods' => $allowedMethods,
        ]);
    }

    private function serverError(): ResponseInterface
    {
        return $this->jsonResponse(500, ['error' => 'Error interno del servidor']);
    }

    private function jsonResponse(int $status, array $data): ResponseInterface
    {
        $response = new Response($status);
        $response = $response->withHeader('Content-Type', 'application/json');
        $body = $this->psr17Factory->createStream(json_encode($data));
        return $response->withBody($body);
    }

    private function handleFoundRoute(
        ServerRequestInterface $request,
        array $handler,
        array $routeParams
    ): ResponseInterface {
        // Añadir parámetros de ruta al request
        $request = $request->withAttribute('route_params', $routeParams);

        foreach ($routeParams as $name => $value) {
            $request = $request->withAttribute($name, $value);
        }

        if ($this->isMiddlewareHandler($handler)) {
            // Desempaquetar
            $middlewareClass = $handler[0];
            if (count($handler) === 3) {
                // Soporta [Middleware::class, permiso, Controller
                [$_, $requiredPermission, $controller] = $handler;
            } else {
                $requiredPermission = null;
                $controller = $handler[1];
            }

            try {
                // Instanciar middleware dinámico
                if ($requiredPermission !== null) {
                    $middleware = new $middlewareClass(
                        $this->container->get(JwtService::class),
                        $requiredPermission
                    );
                } else {
                    $middleware = $this->container->get($middlewareClass);
                }

                if (!$middleware instanceof MiddlewareInterface) {
                    return $this->jsonResponse(500, [
                        'error' => 'El middleware debe implementar MiddlewareInterface'
                    ]);
                }

                // Preparar el handler final
                [$controllerClass, $controllerMethod] = $controller;
                $handlerFn = function (ServerRequestInterface $req) use ($controllerClass, $controllerMethod) {
                    $controller = $this->container->get($controllerClass);
                    return $controller->{$controllerMethod}($req, new Response());
                };
                $requestHandler = new class($handlerFn) implements RequestHandlerInterface {
                    public function __construct(private readonly \Closure $handler) {}
                    public function handle(ServerRequestInterface $request): ResponseInterface {
                        return ($this->handler)($request);
                    }
                };

                return $middleware->process($request, $requestHandler);

            } catch (Exception $e) {
                return $this->jsonResponse(500, [
                    'error'   => 'Error al procesar middleware',
                    'message' => $e->getMessage()
                ]);
            }
        }

        // Handler directo: [Controller::class, 'method']
        if (!is_array($handler) || count($handler) !== 2) {
            return $this->jsonResponse(500, ['error' => 'Formato de handler inválido']);
        }

        [$controllerClass, $controllerMethod] = $handler;

        try {
            $controller = $this->container->get($controllerClass);
            return $controller->{$controllerMethod}($request, new Response());
        } catch (Exception $e) {
            return $this->jsonResponse(500, [
                'error' => 'Error interno',
                'message' => $e->getMessage()
            ]);
        }
    }

    private function isMiddlewareHandler(array $handler): bool
    {
        // Soporta [Middleware::class, Controller], o [Middleware::class, permiso, Controller]
        return is_array($handler)
            && in_array(count($handler), [2, 3], true)
            && class_exists($handler[0])
            && is_subclass_of($handler[0], MiddlewareInterface::class);
    }

    public function run(): void
    {
        $request = $this->createServerRequestFromGlobals();

        // Invocar el middleware CORS antes de manejar la petición
        $cors     = $this->container->get(CorsMiddleware::class);
        $response = $cors->process($request, $this);

        $this->sendResponse($response);
    }

    private function sendResponse(ResponseInterface $response): void
    {
        http_response_code($response->getStatusCode());
        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value));
            }
        }
        echo $response->getBody();
    }

    private function createServerRequestFromGlobals(): ServerRequestInterface
    {
        $uri = $this->normalizeUri($_SERVER['REQUEST_URI']);
        $bodyContent = file_get_contents('php://input');
        $stream = $this->psr17Factory->createStream($bodyContent);

        $request = $this->psr17Factory->createServerRequest(
            $_SERVER['REQUEST_METHOD'],
            $uri,
            $_SERVER
        )->withBody($stream);

        foreach (getallheaders() as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        return $request;
    }
}