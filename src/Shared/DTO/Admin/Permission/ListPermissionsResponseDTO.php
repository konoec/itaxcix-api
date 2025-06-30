<?php

namespace itaxcix\Shared\DTO\Admin\Permission;

readonly class ListPermissionsResponseDTO
{
    public function __construct(
        public array $permissions,
        public int $total,
        public int $page,
        public int $limit,
        public int $totalPages
    ) {}
}
