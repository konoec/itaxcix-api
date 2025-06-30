<?php

namespace itaxcix\Shared\DTO\Admin\Permission;

readonly class ListPermissionsRequestDTO
{
    public function __construct(
        public int $page = 1,
        public int $limit = 20,
        public ?string $search = null,
        public ?bool $webOnly = null,
        public ?bool $activeOnly = true
    ) {}
}
