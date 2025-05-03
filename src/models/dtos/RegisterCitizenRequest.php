<?php

namespace itaxcix\model\dtos;

class RegisterCitizenRequest
{
    public function __construct(
        public readonly string $documentType,
        public readonly string $documentNumber,
        public readonly string $alias,
        public readonly string $password,
        public readonly array $contactMethod
    ) {}
}