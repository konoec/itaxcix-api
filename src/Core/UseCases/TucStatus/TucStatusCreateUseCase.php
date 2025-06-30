<?php

namespace itaxcix\Core\UseCases\TucStatus;

use itaxcix\Core\Domain\vehicle\TucStatusModel;
use itaxcix\Core\Interfaces\vehicle\TucStatusRepositoryInterface;
use itaxcix\Shared\DTO\useCases\TucStatus\TucStatusRequestDTO;
use itaxcix\Shared\DTO\useCases\TucStatus\TucStatusResponseDTO;

class TucStatusCreateUseCase
{
    private TucStatusRepositoryInterface $repository;

    public function __construct(TucStatusRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(TucStatusRequestDTO $request): TucStatusResponseDTO
    {
        $tucStatus = new TucStatusModel(
            id: null,
            name: $request->name,
            active: $request->active
        );

        $saved = $this->repository->saveTucStatus($tucStatus);

        return new TucStatusResponseDTO(
            id: $saved->getId(),
            name: $saved->getName(),
            active: $saved->isActive()
        );
    }
}

