<?php

namespace itaxcix\Shared\DTO\Admin\User;

readonly class ListUsersResponseDTO
{
    public function __construct(
        public array $users,
        public int $total,
        public int $page,
        public int $limit,
        public int $totalPages
    ) {}
}
