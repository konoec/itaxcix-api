<?php

namespace itaxcix\Core\UseCases\TucModality;

use itaxcix\Core\Interfaces\vehicle\TucModalityRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\TucProcedureRepositoryInterface;

class TucModalityDeleteUseCase
{
    private TucModalityRepositoryInterface $repository;
    private TucProcedureRepositoryInterface $procedureRepository;

    public function __construct(TucModalityRepositoryInterface $repository, TucProcedureRepositoryInterface $procedureRepository)
    {
        $this->repository = $repository;
        $this->procedureRepository = $procedureRepository;
    }

    public function execute(int $id): bool
    {
        // Check if the modality is associated with any procedures
        $procedures = $this->procedureRepository->findByTucModalityId($id);
        if (!empty($procedures)) {
            return false; // Cannot delete if there are associated procedures
        }

        return $this->repository->delete($id);
    }
}

