<?php

namespace itaxcix\Shared\DTO\useCases\InfractionSeverity;

readonly class InfractionSeverityPaginationRequestDTO
{
    public function __construct(
        public int     $page = 1,
        public int     $perPage = 15,
        public ?string $search = null,
        public ?string $name = null,
        public ?bool   $active = null,
        public string  $sortBy = 'name',
        public string  $sortDirection = 'ASC'
    ) {}

    public static function fromArray(array $data): self
    {
        $sortBy = $data['sortBy'] ?? 'name';
        $sortDirection = strtoupper($data['sortDirection'] ?? 'ASC');

        return new self(
            page: max(1, (int)($data['page'] ?? 1)),
            perPage: min(100, max(1, (int)($data['perPage'] ?? 15))),
            search: !empty($data['search']) ? $data['search'] : null,
            name: !empty($data['name']) ? $data['name'] : null,
            active: isset($data['active']) ? filter_var($data['active'], FILTER_VALIDATE_BOOLEAN) : null,
            sortBy: in_array($sortBy, ['id', 'name', 'active']) ? $sortBy : 'name',
            sortDirection: in_array($sortDirection, ['ASC', 'DESC']) ? $sortDirection : 'ASC'
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

