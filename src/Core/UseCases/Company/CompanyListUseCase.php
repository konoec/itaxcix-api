<?php

namespace itaxcix\Core\UseCases\Company;

use itaxcix\Core\Interfaces\company\CompanyRepositoryInterface;
use itaxcix\Shared\DTO\useCases\Company\CompanyPaginationRequestDTO;

class CompanyListUseCase
{
    private CompanyRepositoryInterface $repository;

    public function __construct(CompanyRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(CompanyPaginationRequestDTO $request): array
    {
        // Usar el método que maneja filtros y paginación
        $paginationResponse = $this->repository->findCompaniesPaginatedWithFilters($request);

        return [
            'items' => array_map(fn($company) => [
                'id' => $company->getId(),
                'ruc' => $company->getRuc(),
                'name' => $company->getName(),
                'active' => $company->isActive()
            ], $paginationResponse->items),
            'pagination' => [
                'page' => $paginationResponse->meta->currentPage,
                'perPage' => $paginationResponse->meta->perPage,
                'total' => $paginationResponse->meta->total,
                'totalPages' => $paginationResponse->meta->lastPage
            ],
            'search' => $paginationResponse->meta->search,
            'filters' => $paginationResponse->meta->filters,
            'sorting' => [
                'sortBy' => $paginationResponse->meta->sortBy,
                'sortDirection' => $paginationResponse->meta->sortDirection
            ]
        ];
    }
}
