<?php

namespace itaxcix\Shared\DTO\Admin\User;

readonly class ListUsersRequestDTO
{
    public function __construct(
        public int $page = 1,
        public int $limit = 20,
        public ?string $search = null,
        public ?int $roleId = null,
        public ?int $statusId = null,
        public ?bool $withWebAccess = null
    ) {}
}
