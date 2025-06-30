<?php

namespace itaxcix\Shared\DTO\useCases\Configuration;

class ConfigurationPaginationRequestDTO
{
    private int $page;
    private int $perPage;
    private ?string $search;
    private ?string $key;
    private ?string $value;
    private ?bool $active;
    private string $sortBy;
    private string $sortDirection;
    private bool $onlyActive;
    private string $format;

    public function __construct(
        int $page = 1,
        int $perPage = 10,
        ?string $search = null,
        ?string $key = null,
        ?string $value = null,
        ?bool $active = null,
        string $sortBy = 'key',
        string $sortDirection = 'asc',
        bool $onlyActive = false,
        string $format = 'json'
    ) {
        $this->page = $page;
        $this->perPage = $perPage;
        $this->search = $search;
        $this->key = $key;
        $this->value = $value;
        $this->active = $active;
        $this->sortBy = $sortBy;
        $this->sortDirection = $sortDirection;
        $this->onlyActive = $onlyActive;
        $this->format = $format;
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

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function getValue(): ?string
    {
        return $this->value;
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

    public function getFormat(): string
    {
        return $this->format;
    }

    public function isValidSortBy(): bool
    {
        $validFields = ['id', 'key', 'value', 'active'];
        return in_array($this->sortBy, $validFields);
    }

    public function isValidSortDirection(): bool
    {
        return in_array(strtolower($this->sortDirection), ['asc', 'desc']);
    }
}
