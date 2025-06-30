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
        $companies = $this->repository->findAll($request);
        $total = $this->repository->count($request);

        return [
            'items' => array_map(fn($company) => [
                'id' => $company->getId(),
                'ruc' => $company->getRuc(),
                'name' => $company->getName(),
                'active' => $company->isActive()
            ], $companies),
            'pagination' => [
                'page' => $request->getPage(),
                'perPage' => $request->getPerPage(),
                'total' => $total,
                'totalPages' => ceil($total / $request->getPerPage())
            ]
        ];
    }
}
