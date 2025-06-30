<?php

namespace itaxcix\Core\UseCases\ProcedureType;

use itaxcix\Core\Domain\vehicle\ProcedureTypeModel;
use itaxcix\Core\Interfaces\vehicle\ProcedureTypeRepositoryInterface;
use itaxcix\Shared\DTO\useCases\ProcedureType\ProcedureTypeRequestDTO;
use itaxcix\Shared\DTO\useCases\ProcedureType\ProcedureTypeResponseDTO;

class ProcedureTypeUpdateUseCase
{
    private ProcedureTypeRepositoryInterface $repository;

    public function __construct(ProcedureTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id, ProcedureTypeRequestDTO $request): ProcedureTypeResponseDTO
    {
        $procedureType = new ProcedureTypeModel(
            id: $id,
            name: $request->name,
            active: $request->active
        );

        $updated = $this->repository->saveProcedureType($procedureType);

        return new ProcedureTypeResponseDTO(
            id: $updated->getId(),
            name: $updated->getName(),
            active: $updated->isActive()
        );
    }
}

