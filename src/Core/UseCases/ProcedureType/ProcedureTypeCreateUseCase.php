<?php

namespace itaxcix\Core\UseCases\ProcedureType;

use itaxcix\Core\Domain\vehicle\ProcedureTypeModel;
use itaxcix\Core\Interfaces\vehicle\ProcedureTypeRepositoryInterface;
use itaxcix\Shared\DTO\useCases\ProcedureType\ProcedureTypeRequestDTO;
use itaxcix\Shared\DTO\useCases\ProcedureType\ProcedureTypeResponseDTO;

class ProcedureTypeCreateUseCase
{
    private ProcedureTypeRepositoryInterface $repository;

    public function __construct(ProcedureTypeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(ProcedureTypeRequestDTO $request): ProcedureTypeResponseDTO
    {
        $procedureType = new ProcedureTypeModel(
            id: null,
            name: $request->name,
            active: $request->active
        );

        $saved = $this->repository->saveProcedureType($procedureType);

        return new ProcedureTypeResponseDTO(
            id: $saved->getId(),
            name: $saved->getName(),
            active: $saved->isActive()
        );
    }
}

