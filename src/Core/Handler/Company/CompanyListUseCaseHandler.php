<?php

namespace itaxcix\Core\Handler\Company;

use itaxcix\Core\Interfaces\company\CompanyRepositoryInterface;
use itaxcix\Core\UseCases\Company\CompanyListUseCase;
use itaxcix\Shared\DTO\useCases\Company\CompanyPaginationRequestDTO;
use itaxcix\Shared\DTO\useCases\Company\CompanyResponseDTO;
use itaxcix\Shared\DTO\generic\PaginationResponseDTO;
use itaxcix\Shared\DTO\generic\PaginationMetaDTO;

class CompanyListUseCaseHandler implements CompanyListUseCase
{
    private CompanyRepositoryInterface $companyRepository;

    public function __construct(CompanyRepositoryInterface $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    public function execute(CompanyPaginationRequestDTO $dto): PaginationResponseDTO
    {
        $paginationResult = $this->companyRepository->findAllCompaniesPaginated($dto->page, $dto->perPage);

        // Convertir los modelos de dominio a DTOs de respuesta
        $responseDTOs = array_map(function($company) {
            return new CompanyResponseDTO(
                id: $company->getId(),
                ruc: $company->getRuc(),
                name: $company->getName(),
                active: $company->isActive()
            );
        }, $paginationResult->items);

        return new PaginationResponseDTO(
            items: $responseDTOs,
            meta: $paginationResult->meta
        );
    }
}
