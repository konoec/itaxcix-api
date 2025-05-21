<?php

namespace itaxcix\Infrastructure\Auth\Interfaces;

interface JwtEncoderInterface {
    public function encode(array $payload): string;
    public function decode(string $token): array;
}