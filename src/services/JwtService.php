<?php

namespace itaxcix\services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JwtService
{
    private string $secretKey;
    private int $tokenTtl; // Tiempo de vida del token en segundos

    public function __construct()
    {
        $this->secretKey = $_ENV['JWT_SECRET']; // Mejor desde .env
        $this->tokenTtl = (int) ($_ENV['JWT_TTL']); // 1 hora por defecto
    }

    public function generateToken(array $payload): string
    {
        $issuedAt = time();
        $payload += [
            'iss' => 'itaxcix-api', // Emisor
            'iat' => $issuedAt,      // Fecha de emisión
            'exp' => $issuedAt + $this->tokenTtl, // Expiración
        ];

        return JWT::encode($payload, $this->secretKey, 'HS256');
    }

    public function decodeToken(string $token): object
    {
        try {
            return JWT::decode($token, new Key($this->secretKey, 'HS256'));
        } catch (Exception $e) {
            throw new Exception("Token inválido o expirado: " . $e->getMessage(), 401);
        }
    }
}