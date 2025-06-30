<?php

namespace itaxcix\Core\Handler\ProcedureType;

use itaxcix\Core\UseCases\ProcedureType\ProcedureTypeDeleteUseCase;

class ProcedureTypeDeleteUseCaseHandler
{
    private ProcedureTypeDeleteUseCase $useCase;

    public function __construct(ProcedureTypeDeleteUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id): bool
    {
        return $this->useCase->execute($id);
    }
}

