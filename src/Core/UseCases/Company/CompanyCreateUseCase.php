<?php

namespace itaxcix\Core\UseCases\Company;

use InvalidArgumentException;
use itaxcix\Core\Domain\company\CompanyModel;
use itaxcix\Core\Interfaces\company\CompanyRepositoryInterface;
use itaxcix\Shared\DTO\useCases\Company\CompanyRequestDTO;

class CompanyCreateUseCase
{
    private CompanyRepositoryInterface $repository;

    public function __construct(CompanyRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(CompanyRequestDTO $request): array
    {
        // Validar que el RUC no exista
        if ($this->repository->existsByRuc($request->getRuc())) {
            throw new InvalidArgumentException('Ya existe una empresa con este RUC');
        }

        $company = new CompanyModel(
            null,
            $request->getRuc(),
            $request->getName(),
            $request->isActive()
        );

        $createdCompany = $this->repository->create($company);

        return [
            'id' => $createdCompany->getId(),
            'ruc' => $createdCompany->getRuc(),
            'name' => $createdCompany->getName(),
            'active' => $createdCompany->isActive()
        ];
    }
}
