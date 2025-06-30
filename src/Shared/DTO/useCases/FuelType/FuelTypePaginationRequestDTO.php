<?php

namespace itaxcix\Shared\DTO\useCases\FuelType;

class FuelTypePaginationRequestDTO
{
    public int $page;
    public int $perPage;
    public ?string $search;
    public ?string $name;
    public ?bool $active;
    public string $sortBy;
    public string $sortDirection;

    public function __construct(
        int $page = 1,
        int $perPage = 15,
        ?string $search = null,
        ?string $name = null,
        ?bool $active = null,
        string $sortBy = 'name',
        string $sortDirection = 'ASC'
    ) {
        $this->page = max(1, $page);
        $this->perPage = min(100, max(1, $perPage));
        $this->search = $search;
        $this->name = $name;
        $this->active = $active;
        $this->sortBy = in_array($sortBy, ['id', 'name', 'active']) ? $sortBy : 'name';
        $this->sortDirection = in_array(strtoupper($sortDirection), ['ASC', 'DESC']) ? strtoupper($sortDirection) : 'ASC';
    }

    public static function fromArray(array $data): self
    {
        return new self(
            page: (int) ($data['page'] ?? 1),
            perPage: (int) ($data['perPage'] ?? 15),
            search: $data['search'] ?? null,
            name: $data['name'] ?? null,
            active: isset($data['active']) ? (bool) $data['active'] : null,
            sortBy: $data['sortBy'] ?? 'name',
            sortDirection: $data['sortDirection'] ?? 'ASC'
        );
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

        if ($this->active !== null) {
            $filters['active'] = $this->active;
        }

        return $filters;
    }
}
