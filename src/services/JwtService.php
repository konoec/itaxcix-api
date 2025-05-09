<?php

namespace itaxcix\services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JwtService {
    private string $secretKey;
    private int $tokenTtl;

    /**
     * Constructor de la clase JwtService.
     * Inicializa la clave secreta y el tiempo de vida del token.
     */
    public function __construct() {
        $this->secretKey = $_ENV['JWT_SECRET'];
        $this->tokenTtl = (int) ($_ENV['JWT_TTL']);
    }

    /**
     * Genera un token JWT con la información del usuario.
     *
     * @param array $payload Datos del usuario a incluir en el token.
     * @return string Token JWT generado.
     */
    public function generateToken(array $payload): string {
        $issuedAt = time();
        $payload += [
            'iss' => 'itaxcix-api',
            'iat' => $issuedAt,
            'exp' => $issuedAt + $this->tokenTtl,
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    /**
     * Decodifica un token JWT y devuelve su contenido.
     *
     * @param string $token Token JWT a decodificar.
     * @return object Contenido del token decodificado.
     * @throws Exception Si el token es inválido o ha expirado.
     */
    public function decodeToken(string $token): object {
        try {
            return JWT::decode($token, new Key($this->secretKey, 'HS256'));
        } catch (Exception $e) {
            throw new Exception("Token inválido o expirado: " . $e->getMessage(), 401);
        }
    }
}