<?php

namespace itaxcix\Shared\DTO\useCases\District;

class DistrictPaginationRequestDTO
{
    private int $page;
    private int $perPage;
    private ?string $search;
    private ?string $name;
    private ?int $provinceId;
    private ?string $ubigeo;
    private string $sortBy;
    private string $sortDirection;

    public function __construct(
        int $page = 1,
        int $perPage = 15,
        ?string $search = null,
        ?string $name = null,
        ?int $provinceId = null,
        ?string $ubigeo = null,
        string $sortBy = 'name',
        string $sortDirection = 'asc'
    ) {
        $this->page = $page;
        $this->perPage = $perPage;
        $this->search = $search;
        $this->name = $name;
        $this->provinceId = $provinceId;
        $this->ubigeo = $ubigeo;
        $this->sortBy = $sortBy;
        $this->sortDirection = $sortDirection;
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

    public function getProvinceId(): ?int
    {
        return $this->provinceId;
    }

    public function getUbigeo(): ?string
    {
        return $this->ubigeo;
    }

    public function getSortBy(): string
    {
        return $this->sortBy;
    }

    public function getSortDirection(): string
    {
        return $this->sortDirection;
    }
}
