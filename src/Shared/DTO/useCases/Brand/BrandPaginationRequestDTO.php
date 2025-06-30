<?php

namespace itaxcix\Shared\DTO\useCases\Brand;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "BrandPaginationRequestDTO",
    description: "DTO para paginación y filtros de marcas de vehículo",
    type: "object"
)]
class BrandPaginationRequestDTO
{
    #[OA\Property(
        property: "page",
        description: "Número de página",
        type: "integer",
        minimum: 1,
        default: 1,
        example: 1
    )]
    public int $page;

    #[OA\Property(
        property: "perPage",
        description: "Elementos por página",
        type: "integer",
        minimum: 1,
        maximum: 100,
        default: 15,
        example: 15
    )]
    public int $perPage;

    #[OA\Property(
        property: "search",
        description: "Búsqueda global en nombre de marca",
        type: "string",
        nullable: true,
        example: "Toyota"
    )]
    public ?string $search;

    #[OA\Property(
        property: "name",
        description: "Filtro por nombre de marca",
        type: "string",
        nullable: true,
        example: "Honda"
    )]
    public ?string $name;

    #[OA\Property(
        property: "active",
        description: "Filtro por estado activo",
        type: "boolean",
        nullable: true,
        example: true
    )]
    public ?bool $active;

    #[OA\Property(
        property: "sortBy",
        description: "Campo de ordenamiento",
        type: "string",
        enum: ["id", "name", "active"],
        default: "name",
        example: "name"
    )]
    public string $sortBy;

    #[OA\Property(
        property: "sortDirection",
        description: "Dirección de ordenamiento",
        type: "string",
        enum: ["asc", "desc"],
        default: "asc",
        example: "asc"
    )]
    public string $sortDirection;

    #[OA\Property(
        property: "onlyActive",
        description: "Incluir solo marcas activas",
        type: "boolean",
        default: false,
        example: false
    )]
    public bool $onlyActive;

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

    // Getters
    public function getPage(): int { return $this->page; }
    public function getPerPage(): int { return $this->perPage; }
    public function getSearch(): ?string { return $this->search; }
    public function getName(): ?string { return $this->name; }
    public function getActive(): ?bool { return $this->active; }
    public function getSortBy(): string { return $this->sortBy; }
    public function getSortDirection(): string { return $this->sortDirection; }
    public function isOnlyActive(): bool { return $this->onlyActive; }
}
