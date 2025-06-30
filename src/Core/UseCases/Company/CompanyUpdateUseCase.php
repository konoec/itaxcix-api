<?php

namespace itaxcix\Core\UseCases\Company;

use InvalidArgumentException;
use itaxcix\Core\Domain\company\CompanyModel;
use itaxcix\Core\Interfaces\company\CompanyRepositoryInterface;
use itaxcix\Shared\DTO\useCases\Company\CompanyRequestDTO;

class CompanyUpdateUseCase
{
    private CompanyRepositoryInterface $repository;

    public function __construct(CompanyRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id, CompanyRequestDTO $request): array
    {
        $existingCompany = $this->repository->findById($id);
        if (!$existingCompany) {
            throw new InvalidArgumentException('Empresa no encontrada');
        }

        // Validar que el RUC no exista en otra empresa
        if ($this->repository->existsByRuc($request->getRuc(), $id)) {
            throw new InvalidArgumentException('Ya existe otra empresa con este RUC');
        }

        $company = new CompanyModel(
            $id,
            $request->getRuc(),
            $request->getName(),
            $request->isActive()
        );

        $updatedCompany = $this->repository->update($company);

        return [
            'id' => $updatedCompany->getId(),
            'ruc' => $updatedCompany->getRuc(),
            'name' => $updatedCompany->getName(),
            'active' => $updatedCompany->isActive()
        ];
    }
}
