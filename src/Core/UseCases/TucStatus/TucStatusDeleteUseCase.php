<?php

namespace itaxcix\Core\UseCases\TucStatus;

use itaxcix\Core\Interfaces\vehicle\TucProcedureRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\TucStatusRepositoryInterface;

class TucStatusDeleteUseCase
{
    private TucStatusRepositoryInterface $repository;
    private TucProcedureRepositoryInterface $procedureRepository;

    public function __construct(TucStatusRepositoryInterface $repository, TucProcedureRepositoryInterface $procedureRepository)
    {
        $this->repository = $repository;
        $this->procedureRepository = $procedureRepository;
    }

    public function execute(int $id): bool
    {
        // Check if the tuc status is associated with any tuc procedures
        $tucProcedures = $this->procedureRepository->findByTucStatusId($id);
        if (!empty($tucProcedures)) {
            return false; // Cannot delete if there are associated tuc procedures
        }

        return $this->repository->delete($id);
    }
}

