<?php

namespace itaxcix\Shared\DTO\useCases\Province;

class ProvincePaginationRequestDTO
{
    private int $page;
    private int $perPage;
    private ?string $search;
    private ?string $name;
    private ?int $departmentId;
    private ?string $ubigeo;
    private string $sortBy;
    private string $sortOrder;

    public function __construct(
        int $page = 1,
        int $perPage = 15,
        ?string $search = null,
        ?string $name = null,
        ?int $departmentId = null,
        ?string $ubigeo = null,
        string $sortBy = 'name',
        string $sortOrder = 'ASC'
    ) {
        $this->page = $page;
        $this->perPage = $perPage;
        $this->search = $search;
        $this->name = $name;
        $this->departmentId = $departmentId;
        $this->ubigeo = $ubigeo;
        $this->sortBy = $sortBy;
        $this->sortOrder = $sortOrder;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getDepartmentId(): ?int
    {
        return $this->departmentId;
    }

    public function getUbigeo(): ?string
    {
        return $this->ubigeo;
    }

    public function getSortBy(): string
    {
        return $this->sortBy;
    }

    public function getSortOrder(): string
    {
        return $this->sortOrder;
    }

    public function getFilters(): array
    {
        $filters = [];

        if ($this->name !== null) {
            $filters['name'] = $this->name;
        }

        if ($this->departmentId !== null) {
            $filters['departmentId'] = $this->departmentId;
        }

        if ($this->ubigeo !== null) {
            $filters['ubigeo'] = $this->ubigeo;
        }

        return $filters;
    }
}
