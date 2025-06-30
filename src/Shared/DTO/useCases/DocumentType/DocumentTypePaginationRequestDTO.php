<?php

namespace itaxcix\Shared\DTO\useCases\DocumentType;

class DocumentTypePaginationRequestDTO
{
    private int $page;
    private int $perPage;
    private ?string $search;
    private ?string $name;
    private ?bool $active;
    private string $sortBy;
    private string $sortDirection;
    private bool $onlyActive;

    public function __construct(
        int $page = 1,
        int $perPage = 15,
        ?string $search = null,
        ?string $name = null,
        ?bool $active = null,
        string $sortBy = 'name',
        string $sortDirection = 'asc',
        bool $onlyActive = false
    ) {
        $this->page = $page;
        $this->perPage = $perPage;
        $this->search = $search;
        $this->name = $name;
        $this->active = $active;
        $this->sortBy = $sortBy;
        $this->sortDirection = $sortDirection;
        $this->onlyActive = $onlyActive;
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

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function getSortBy(): string
    {
        return $this->sortBy;
    }

    public function getSortDirection(): string
    {
        return $this->sortDirection;
    }

    public function getOnlyActive(): bool
    {
        return $this->onlyActive;
    }

    public function isValidSortBy(): bool
    {
        return in_array($this->sortBy, ['id', 'name', 'active']);
    }

    public function isValidSortDirection(): bool
    {
        return in_array(strtolower($this->sortDirection), ['asc', 'desc']);
    }
}
