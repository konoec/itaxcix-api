<?php

namespace itaxcix\Shared\DTO\useCases\UserCodeType;

class UserCodeTypePaginationRequestDTO
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $perPage = 15,
        public readonly ?string $search = null,
        public readonly ?string $name = null,
        public readonly ?bool $active = null,
        public readonly string $sortBy = 'name',
        public readonly string $sortDirection = 'ASC'
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            page: max(1, (int)($data['page'] ?? 1)),
            perPage: min(100, max(1, (int)($data['perPage'] ?? 15))),
            search: !empty($data['search']) ? $data['search'] : null,
            name: !empty($data['name']) ? $data['name'] : null,
            active: isset($data['active']) ? filter_var($data['active'], FILTER_VALIDATE_BOOLEAN) : null,
            sortBy: in_array($data['sortBy'] ?? 'name', ['id', 'name', 'active']) ? ($data['sortBy'] ?? 'name') : 'name',
            sortDirection: in_array(strtoupper($data['sortDirection'] ?? 'ASC'), ['ASC', 'DESC']) ? strtoupper($data['sortDirection'] ?? 'ASC') : 'ASC'
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
