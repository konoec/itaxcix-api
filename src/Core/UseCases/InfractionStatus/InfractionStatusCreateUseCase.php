<?php

namespace itaxcix\Core\UseCases\InfractionStatus;

use itaxcix\Core\Domain\infraction\InfractionStatusModel;
use itaxcix\Core\Interfaces\infraction\InfractionStatusRepositoryInterface;
use itaxcix\Shared\DTO\useCases\InfractionStatus\InfractionStatusRequestDTO;
use itaxcix\Shared\DTO\useCases\InfractionStatus\InfractionStatusResponseDTO;

class InfractionStatusCreateUseCase
{
    private InfractionStatusRepositoryInterface $repository;

    public function __construct(InfractionStatusRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(InfractionStatusRequestDTO $request): InfractionStatusResponseDTO
    {
        $infractionStatus = new InfractionStatusModel(
            id: null,
            name: $request->name,
            active: $request->active
        );

        $saved = $this->repository->saveInfractionStatus($infractionStatus);

        return new InfractionStatusResponseDTO(
            id: $saved->getId(),
            name: $saved->getName(),
            active: $saved->isActive()
        );
    }
}

