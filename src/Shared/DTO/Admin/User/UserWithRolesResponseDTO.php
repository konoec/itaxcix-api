<?php

namespace itaxcix\Shared\DTO\Admin\User;

readonly class UserWithRolesResponseDTO
{
    public function __construct(
        public int $userId,
        public array $roles
    ) {}
}
