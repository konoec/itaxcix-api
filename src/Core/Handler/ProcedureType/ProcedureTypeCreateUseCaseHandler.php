<?php

namespace itaxcix\Core\Handler\ProcedureType;

use itaxcix\Core\UseCases\ProcedureType\ProcedureTypeCreateUseCase;
use itaxcix\Shared\DTO\useCases\ProcedureType\ProcedureTypeRequestDTO;
use itaxcix\Shared\DTO\useCases\ProcedureType\ProcedureTypeResponseDTO;

class ProcedureTypeCreateUseCaseHandler
{
    private ProcedureTypeCreateUseCase $useCase;

    public function __construct(ProcedureTypeCreateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(ProcedureTypeRequestDTO $request): ProcedureTypeResponseDTO
    {
        return $this->useCase->execute($request);
    }
}

