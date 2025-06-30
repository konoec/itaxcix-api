<?php

namespace itaxcix\Infrastructure\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\RFC6455\Messaging\MessageInterface;

class AuthenticatedWebSocketHandler implements MessageComponentInterface
{
    protected $wrappedComponent;
    protected $authService;
    protected $authenticatedClients;

    public function __construct(MessageComponentInterface $component, WebSocketAuthService $authService)
    {
        $this->wrappedComponent = $component;
        $this->authService = $authService;
        $this->authenticatedClients = new \SplObjectStorage();
    }

    public function onOpen(ConnectionInterface $conn)
    {
        echo "Nueva conexión WebSocket - Iniciando autenticación\n";

        // Extraer token de headers o query parameters
        $token = $this->extractToken($conn);

        if (!$token) {
            echo "❌ No se encontró token de autenticación\n";
            $this->sendAuthError($conn, 'Token de autenticación requerido');
            $conn->close();
            return;
        }

        // Validar token
        $userData = $this->authService->validateToken($token);

        if (!$userData) {
            echo "❌ Token inválido o expirado\n";
            $this->sendAuthError($conn, 'Token inválido o expirado');
            $conn->close();
            return;
        }

        // Marcar conexión como autenticada
        $conn->isAuthenticated = true;
        $conn->userId = $userData['userId'];
        $conn->userType = $userData['userType'];
        $conn->userData = $userData;

        echo "✅ Cliente autenticado - ID: {$conn->userId}, Tipo: {$conn->userType}\n";

        // Agregar a clientes autenticados
        $this->authenticatedClients->attach($conn);

        // Pasar al componente original
        $this->wrappedComponent->onOpen($conn);
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        // Verificar que el cliente esté autenticado
        if (!isset($from->isAuthenticated) || !$from->isAuthenticated) {
            echo "❌ Mensaje de cliente no autenticado\n";
            $this->sendAuthError($from, 'Cliente no autenticado');
            $from->close();
            return;
        }

        // Pasar mensaje al componente original
        $this->wrappedComponent->onMessage($from, $msg);
    }

    public function onClose(ConnectionInterface $conn)
    {
        if ($this->authenticatedClients->contains($conn)) {
            $this->authenticatedClients->detach($conn);
        }

        $this->wrappedComponent->onClose($conn);
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Error en conexión autenticada: " . $e->getMessage() . "\n";
        $this->wrappedComponent->onError($conn, $e);
    }

    /**
     * Extrae el token de la conexión
     */
    private function extractToken(ConnectionInterface $conn): ?string
    {
        // Método 1: Desde query parameters
        if (isset($conn->httpRequest)) {
            $query = $conn->httpRequest->getUri()->getQuery();
            $token = $this->authService->extractTokenFromQuery($query);
            if ($token) {
                return $token;
            }
        }

        // Método 2: Desde headers
        if (isset($conn->WebSocket) && isset($conn->WebSocket->request)) {
            $headers = $conn->WebSocket->request->getHeaders();
            $token = $this->authService->extractTokenFromHeaders($headers);
            if ($token) {
                return $token;
            }
        }

        // Método 3: Desde subprotocol (backup)
        if (isset($conn->WebSocket) && isset($conn->WebSocket->request)) {
            $protocols = $conn->WebSocket->request->getHeader('Sec-WebSocket-Protocol');
            if ($protocols) {
                foreach (explode(',', $protocols[0]) as $protocol) {
                    $protocol = trim($protocol);
                    if (strpos($protocol, 'token.') === 0) {
                        return substr($protocol, 6);
                    }
                }
            }
        }

        return null;
    }

    /**
     * Envía error de autenticación
     */
    private function sendAuthError(ConnectionInterface $conn, string $message): void
    {
        $error = json_encode([
            'type' => 'auth_error',
            'message' => $message,
            'timestamp' => time()
        ]);

        try {
            $conn->send($error);
        } catch (\Exception $e) {
            echo "Error enviando mensaje de autenticación: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Permite acceder a métodos del componente original
     */
    public function __call($method, $args)
    {
        return call_user_func_array([$this->wrappedComponent, $method], $args);
    }
}
