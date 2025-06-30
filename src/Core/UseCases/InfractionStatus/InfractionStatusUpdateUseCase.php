<?php

namespace itaxcix\Core\UseCases\InfractionStatus;

use itaxcix\Core\Domain\infraction\InfractionStatusModel;
use itaxcix\Core\Interfaces\infraction\InfractionStatusRepositoryInterface;
use itaxcix\Shared\DTO\useCases\InfractionStatus\InfractionStatusRequestDTO;
use itaxcix\Shared\DTO\useCases\InfractionStatus\InfractionStatusResponseDTO;

class InfractionStatusUpdateUseCase
{
    private InfractionStatusRepositoryInterface $repository;

    public function __construct(InfractionStatusRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id, InfractionStatusRequestDTO $request): InfractionStatusResponseDTO
    {
        $infractionStatus = new InfractionStatusModel(
            id: $id,
            name: $request->name,
            active: $request->active
        );

        $updated = $this->repository->saveInfractionStatus($infractionStatus);

        return new InfractionStatusResponseDTO(
            id: $updated->getId(),
            name: $updated->getName(),
            active: $updated->isActive()
        );
    }
}

