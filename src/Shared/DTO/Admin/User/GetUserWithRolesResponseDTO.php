<?php

namespace itaxcix\Shared\DTO\Admin\User;

readonly class GetUserWithRolesResponseDTO
{
    public function __construct(
        public int $userId,
        public string $userName,
        public string $userLastName,
        public string $userDocument,
        public ?string $userEmail,
        public array $roles
    ) {}
}
