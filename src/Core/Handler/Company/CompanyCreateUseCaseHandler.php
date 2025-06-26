<?php

namespace itaxcix\Core\Handler\Company;

use InvalidArgumentException;
use itaxcix\Core\Domain\company\CompanyModel;
use itaxcix\Core\Interfaces\company\CompanyRepositoryInterface;
use itaxcix\Core\UseCases\Company\CompanyCreateUseCase;
use itaxcix\Shared\DTO\useCases\Company\CompanyRequestDTO;

class CompanyCreateUseCaseHandler implements CompanyCreateUseCase
{
    private CompanyRepositoryInterface $companyRepository;

    public function __construct(CompanyRepositoryInterface $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    public function execute(CompanyRequestDTO $dto): array
    {
        // Verificar que el RUC no esté registrado
        $existingCompany = $this->companyRepository->findCompanyByRuc($dto->ruc);
        if ($existingCompany) {
            throw new InvalidArgumentException('Ya existe una empresa registrada con ese RUC.');
        }

        $company = new CompanyModel(
            id: null,
            ruc: $dto->ruc,
            name: $dto->name,
            active: $dto->active
        );

        $this->companyRepository->saveCompany($company);

        return ['message' => 'Empresa creada correctamente.'];
    }
}
