<?php

namespace itaxcix\Core\UseCases\Company;

use InvalidArgumentException;
use itaxcix\Core\Interfaces\company\CompanyRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\TucProcedureRepositoryInterface;

class CompanyDeleteUseCase
{
    private CompanyRepositoryInterface $repository;
    private TucProcedureRepositoryInterface $tucProcedureRepository;

    public function __construct(CompanyRepositoryInterface $repository, TucProcedureRepositoryInterface $tucProcedureRepository)
    {
        $this->repository = $repository;
        $this->tucProcedureRepository = $tucProcedureRepository;
    }

    public function execute(int $id): bool
    {
        $existingCompany = $this->repository->findById($id);
        if (!$existingCompany) {
            throw new InvalidArgumentException('Empresa no encontrada');
        }

        // Verificar si la empresa tiene procedimientos TUC asociados
        $tucProcedures = $this->tucProcedureRepository->findByCompanyId($id);
        if (!empty($tucProcedures)) {
            throw new InvalidArgumentException('No se puede eliminar la empresa porque tiene procedimientos TUC asociados.');
        }

        return $this->repository->delete($id);
    }
}
