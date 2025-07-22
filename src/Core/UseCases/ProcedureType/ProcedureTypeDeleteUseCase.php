<?php

namespace itaxcix\Core\UseCases\ProcedureType;

use itaxcix\Core\Interfaces\vehicle\ProcedureTypeRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\TucProcedureRepositoryInterface;

class ProcedureTypeDeleteUseCase
{
    private ProcedureTypeRepositoryInterface $repository;
    private TucProcedureRepositoryInterface $procedureRepository;

    public function __construct(ProcedureTypeRepositoryInterface $repository, TucProcedureRepositoryInterface $procedureRepository)
    {
        $this->repository = $repository;
        $this->procedureRepository = $procedureRepository;
    }

    public function execute(int $id): bool
    {
        // Check if the procedure type is associated with any procedures
        $procedures = $this->procedureRepository->findByProcedureTypeId($id);
        if (!empty($procedures)) {
            return false; // Cannot delete if there are associated procedures
        }

        return $this->repository->delete($id);
    }
}

