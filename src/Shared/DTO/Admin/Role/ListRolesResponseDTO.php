<?php

namespace itaxcix\Shared\DTO\Admin\Role;

readonly class ListRolesResponseDTO
{
    public function __construct(
        public array $roles,
        public int $total,
        public int $page,
        public int $limit,
        public int $totalPages
    ) {}
}
