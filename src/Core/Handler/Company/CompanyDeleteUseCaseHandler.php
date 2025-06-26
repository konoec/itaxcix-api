<?php

namespace itaxcix\Core\Handler\Company;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\company\CompanyRepositoryInterface;
use itaxcix\Core\UseCases\Company\CompanyDeleteUseCase;

class CompanyDeleteUseCaseHandler implements CompanyDeleteUseCase
{
    private CompanyRepositoryInterface $companyRepository;

    public function __construct(CompanyRepositoryInterface $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    public function execute(int $id): array
    {
        $success = $this->companyRepository->deleteCompany($id);

        if (!$success) {
            throw new InvalidArgumentException('Empresa no encontrada.');
        }

        return ['message' => 'Empresa eliminada correctamente.'];
    }
}
