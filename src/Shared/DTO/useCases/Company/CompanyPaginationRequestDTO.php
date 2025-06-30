<?php

namespace itaxcix\Shared\DTO\useCases\Company;

class CompanyPaginationRequestDTO
{
    private int $page;
    private int $perPage;
    private ?string $search;
    private ?string $ruc;
    private ?string $name;
    private ?bool $active;
    private ?string $sortBy;
    private string $sortDirection;

    public function __construct(
        int $page = 1,
        int $perPage = 15,
        ?string $search = null,
        ?string $ruc = null,
        ?string $name = null,
        ?bool $active = null,
        ?string $sortBy = null,
        string $sortDirection = 'asc'
    ) {
        $this->page = max(1, $page);
        $this->perPage = min(100, max(1, $perPage));
        $this->search = $search;
        $this->ruc = $ruc;
        $this->name = $name;
        $this->active = $active;
        $this->sortBy = $sortBy;
        $this->sortDirection = in_array(strtolower($sortDirection), ['asc', 'desc']) ? strtolower($sortDirection) : 'asc';
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function getRuc(): ?string
    {
        return $this->ruc;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function getSortBy(): ?string
    {
        return $this->sortBy;
    }

    public function getSortDirection(): string
    {
        return $this->sortDirection;
    }

    public function getFilters(): array
    {
        $filters = [];

        if ($this->ruc !== null) {
            $filters['ruc'] = $this->ruc;
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
