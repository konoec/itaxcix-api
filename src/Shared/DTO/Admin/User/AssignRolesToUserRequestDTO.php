<?php

namespace itaxcix\Shared\DTO\Admin\User;

readonly class AssignRolesToUserRequestDTO
{
    public function __construct(
        public int $userId,
        public array $roleIds
    ) {}
}
