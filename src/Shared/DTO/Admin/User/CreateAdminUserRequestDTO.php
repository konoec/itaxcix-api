<?php

namespace itaxcix\Shared\DTO\Admin\User;

class CreateAdminUserRequestDTO
{
    public function __construct(
        public readonly string $document,
        public readonly string $email,
        public readonly string $password,
        public readonly string $area,
        public readonly string $position
    ) {}
}
