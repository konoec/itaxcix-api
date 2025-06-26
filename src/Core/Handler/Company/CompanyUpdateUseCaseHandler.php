<?php

namespace itaxcix\Core\Handler\Company;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\company\CompanyRepositoryInterface;
use itaxcix\Core\UseCases\Company\CompanyUpdateUseCase;
use itaxcix\Shared\DTO\useCases\Company\CompanyRequestDTO;

class CompanyUpdateUseCaseHandler implements CompanyUpdateUseCase
{
    private CompanyRepositoryInterface $companyRepository;

    public function __construct(CompanyRepositoryInterface $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    public function execute(CompanyRequestDTO $dto): array
    {
        if (!$dto->id) {
            throw new InvalidArgumentException('ID es requerido para actualizar.');
        }

        $company = $this->companyRepository->findCompanyById($dto->id);
        if (!$company) {
            throw new InvalidArgumentException('Empresa no encontrada.');
        }

        // Verificar que el RUC no esté registrado por otra empresa
        $existingCompany = $this->companyRepository->findCompanyByRuc($dto->ruc);
        if ($existingCompany && $existingCompany->getId() !== $dto->id) {
            throw new InvalidArgumentException('Ya existe otra empresa registrada con ese RUC.');
        }

        $company->setRuc($dto->ruc);
        $company->setName($dto->name);
        $company->setActive($dto->active);

        $this->companyRepository->saveCompany($company);

        return ['message' => 'Empresa actualizada correctamente.'];
    }
}
