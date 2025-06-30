<?php

namespace itaxcix\Core\UseCases\Company;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\company\CompanyRepositoryInterface;

class CompanyDeleteUseCase
{
    private CompanyRepositoryInterface $repository;

    public function __construct(CompanyRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id): bool
    {
        $existingCompany = $this->repository->findById($id);
        if (!$existingCompany) {
            throw new InvalidArgumentException('Empresa no encontrada');
        }

        return $this->repository->delete($id);
    }
}
