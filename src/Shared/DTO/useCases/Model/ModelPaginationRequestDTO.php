<?php

namespace itaxcix\Shared\DTO\useCases\Model;

class ModelPaginationRequestDTO
{
    public int $page;
    public int $perPage;
    public ?string $search;
    public ?string $name;
    public ?int $brandId;
    public ?bool $active;
    public string $sortBy;
    public string $sortOrder;

    public function __construct(
        int $page = 1,
        int $perPage = 15,
        ?string $search = null,
        ?string $name = null,
        ?int $brandId = null,
        ?bool $active = null,
        string $sortBy = 'name',
        string $sortOrder = 'ASC'
    ) {
        $this->page = $page;
        $this->perPage = $perPage;
        $this->search = $search;
        $this->name = $name;
        $this->brandId = $brandId;
        $this->active = $active;
        $this->sortBy = $sortBy;
        $this->sortOrder = $sortOrder;
    }

    public function getFilters(): array
    {
        $filters = [];

        if ($this->search !== null) {
            $filters['search'] = $this->search;
        }

        if ($this->name !== null) {
            $filters['name'] = $this->name;
        }

        if ($this->brandId !== null) {
            $filters['brandId'] = $this->brandId;
        }

        if ($this->active !== null) {
            $filters['active'] = $this->active;
        }

        return $filters;
    }
}
