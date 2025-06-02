<?php

namespace itaxcix\Infrastructure\Auth\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use itaxcix\Infrastructure\Auth\Interfaces\JwtEncoderInterface;

class JwtService implements JwtEncoderInterface {
    private string $secret;
    private string $algorithm;
    private int $expiresIn;

    public function __construct(
        string $secret,
        string $algorithm = 'HS256',
        int $expiresIn = 3600
    ) {
        $this->secret = $secret;
        $this->algorithm = $algorithm;
        $this->expiresIn = $expiresIn;
    }

    public function encode(array $payload, ?int $customExpiresIn = null): string {
        $payload['exp'] = time() + ($customExpiresIn ?? $this->expiresIn);
        return JWT::encode($payload, $this->secret, $this->algorithm);
    }

    public function decode(string $token): array {
        return (array) JWT::decode($token, new Key($this->secret, $this->algorithm));
    }
}