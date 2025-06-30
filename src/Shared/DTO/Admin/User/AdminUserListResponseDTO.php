<?php

namespace itaxcix\Shared\DTO\Admin\User;

class AdminUserListResponseDTO
{
    public readonly array $users;
    public readonly int $total;
    public readonly int $page;
    public readonly int $limit;
    public readonly int $totalPages;

    public function __construct(
        array $users,
        int $total,
        int $page,
        int $limit
    ) {
        $this->users = $users;
        $this->total = $total;
        $this->page = $page;
        $this->limit = $limit;
        $this->totalPages = (int) ceil($total / $limit);
    }
}
