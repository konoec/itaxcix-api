<?php

namespace itaxcix\Core\Handler\ProcedureType;

use itaxcix\Core\UseCases\ProcedureType\ProcedureTypeListUseCase;
use itaxcix\Shared\DTO\useCases\ProcedureType\ProcedureTypePaginationRequestDTO;

class ProcedureTypeListUseCaseHandler
{
    private ProcedureTypeListUseCase $useCase;

    public function __construct(ProcedureTypeListUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(ProcedureTypePaginationRequestDTO $request): array
    {
        return $this->useCase->execute($request);
    }
}

