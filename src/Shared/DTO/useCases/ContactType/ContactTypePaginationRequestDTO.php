<?php

namespace itaxcix\Shared\DTO\useCases\ContactType;

class ContactTypePaginationRequestDTO
{
    public int $page;
    public int $limit;
    public ?string $name;
    public ?bool $active;
    public string $sortBy;
    public string $sortDirection;

    public function __construct(
        int $page = 1,
        int $limit = 15,
        ?string $name = null,
        ?bool $active = null,
        string $sortBy = 'name',
        string $sortDirection = 'ASC'
    ) {
        $this->page = $page;
        $this->limit = $limit;
        $this->name = $name;
        $this->active = $active;
        $this->sortBy = $sortBy;
        $this->sortDirection = $sortDirection;
    }
}
