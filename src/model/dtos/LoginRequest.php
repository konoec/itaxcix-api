<?php

namespace itaxcix\model\dtos;

class LoginRequest {
    public function __construct(
        public readonly string $alias,
        public readonly string $password
    ) {}
}