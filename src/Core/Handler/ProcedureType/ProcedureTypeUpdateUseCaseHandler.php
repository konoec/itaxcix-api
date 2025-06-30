<?php

namespace itaxcix\Core\Handler\ProcedureType;

use itaxcix\Core\UseCases\ProcedureType\ProcedureTypeUpdateUseCase;
use itaxcix\Shared\DTO\useCases\ProcedureType\ProcedureTypeRequestDTO;
use itaxcix\Shared\DTO\useCases\ProcedureType\ProcedureTypeResponseDTO;

class ProcedureTypeUpdateUseCaseHandler
{
    private ProcedureTypeUpdateUseCase $useCase;

    public function __construct(ProcedureTypeUpdateUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    public function handle(int $id, ProcedureTypeRequestDTO $request): ProcedureTypeResponseDTO
    {
        return $this->useCase->execute($id, $request);
    }
}
