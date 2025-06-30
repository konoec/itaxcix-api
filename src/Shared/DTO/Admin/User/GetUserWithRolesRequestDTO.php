<?php

namespace itaxcix\Shared\DTO\Admin\User;

readonly class GetUserWithRolesRequestDTO
{
    public function __construct(
        public int $userId
    ) {}
}
