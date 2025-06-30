<?php

namespace itaxcix\Core\UseCases\TucStatus;

use itaxcix\Core\Domain\vehicle\TucStatusModel;
use itaxcix\Core\Interfaces\vehicle\TucStatusRepositoryInterface;
use itaxcix\Shared\DTO\useCases\TucStatus\TucStatusRequestDTO;
use itaxcix\Shared\DTO\useCases\TucStatus\TucStatusResponseDTO;

class TucStatusUpdateUseCase
{
    private TucStatusRepositoryInterface $repository;

    public function __construct(TucStatusRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id, TucStatusRequestDTO $request): TucStatusResponseDTO
    {
        $tucStatus = new TucStatusModel(
            id: $id,
            name: $request->name,
            active: $request->active
        );

        $updated = $this->repository->saveTucStatus($tucStatus);

        return new TucStatusResponseDTO(
            id: $updated->getId(),
            name: $updated->getName(),
            active: $updated->isActive()
        );
    }
}

