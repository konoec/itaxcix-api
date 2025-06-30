<?php

namespace itaxcix\Core\UseCases\ProcedureType;

use itaxcix\Core\Interfaces\vehicle\ProcedureTypeRepositoryInterface;

class ProcedureTypeDeleteUseCase
{
    private ProcedureTypeRepositoryInterface $repository;

    public function __construct(ProcedureTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id): bool
    {
        return $this->repository->delete($id);
    }
}

