<?php

namespace itaxcix\Infrastructure\WebSocket;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class WebSocketAuthService
{
    private string $jwtSecret;
    private string $jwtAlgorithm;

    public function __construct()
    {
        $this->jwtSecret = $_ENV['JWT_SECRET'] ?? 'itaxcix-jwt';
        $this->jwtAlgorithm = $_ENV['JWT_ALGORITHM'] ?? 'HS256';
    }

    /**
     * Valida un token JWT y retorna los datos del usuario
     */
    public function validateToken(string $token): ?array
    {
        try {
            echo "🔍 Validando token: " . substr($token, 0, 20) . "...\n";
            echo "📏 Longitud del token: " . strlen($token) . "\n";
            echo "🔑 Secret usado: " . substr($this->jwtSecret, 0, 10) . "...\n";
            echo "⚙️ Algoritmo: " . $this->jwtAlgorithm . "\n";

            $decoded = JWT::decode($token, new Key($this->jwtSecret, $this->jwtAlgorithm));
            $userData = (array) $decoded;

            echo "✅ Token decodificado exitosamente\n";
            echo "📋 Claims encontrados: " . implode(', ', array_keys($userData)) . "\n";

            // Verificar que el token no haya expirado
            if (isset($userData['exp']) && $userData['exp'] < time()) {
                echo "❌ Token expirado - Exp: " . date('Y-m-d H:i:s', $userData['exp']) . ", Ahora: " . date('Y-m-d H:i:s') . "\n";
                return null;
            }

            // Verificar campos requeridos para seguridad
            if (!isset($userData['userId']) || !isset($userData['userType'])) {
                echo "❌ Token sin datos de usuario requeridos para seguridad\n";
                echo "   - userId: " . (isset($userData['userId']) ? '✅' : '❌') . "\n";
                echo "   - userType: " . (isset($userData['userType']) ? '✅' : '❌') . "\n";
                return null;
            }

            // Validar que userType sea válido
            if (!in_array($userData['userType'], ['driver', 'citizen'])) {
                echo "❌ userType inválido: " . $userData['userType'] . "\n";
                return null;
            }

            echo "✅ Token válido - Usuario ID: {$userData['userId']}, Tipo: {$userData['userType']}\n";
            return $userData;
        } catch (Exception $e) {
            echo "❌ Error validando token: " . $e->getMessage() . "\n";
            echo "📊 Trace: " . $e->getTraceAsString() . "\n";
            return null;
        }
    }

    /**
     * Extrae el token de los headers de la conexión WebSocket
     */
    public function extractTokenFromHeaders(array $headers): ?string
    {
        // Buscar en Authorization header
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                return $matches[1];
            }
        }

        // Buscar en Sec-WebSocket-Protocol (método alternativo)
        if (isset($headers['Sec-WebSocket-Protocol'])) {
            $protocols = explode(',', $headers['Sec-WebSocket-Protocol']);
            foreach ($protocols as $protocol) {
                $protocol = trim($protocol);
                if (strpos($protocol, 'token.') === 0) {
                    return substr($protocol, 6); // Remover 'token.'
                }
            }
        }

        return null;
    }

    /**
     * Extrae el token de query parameters
     */
    public function extractTokenFromQuery(string $queryString): ?string
    {
        parse_str($queryString, $params);
        return $params['token'] ?? null;
    }
}
