<?php

namespace itaxcix\Core\UseCases\InfractionSeverity;

use itaxcix\Core\Domain\infraction\InfractionSeverityModel;
use itaxcix\Core\Interfaces\infraction\InfractionSeverityRepositoryInterface;
use itaxcix\Shared\DTO\useCases\InfractionSeverity\InfractionSeverityRequestDTO;
use itaxcix\Shared\DTO\useCases\InfractionSeverity\InfractionSeverityResponseDTO;

class InfractionSeverityUpdateUseCase
{
    private InfractionSeverityRepositoryInterface $repository;

    public function __construct(InfractionSeverityRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id, InfractionSeverityRequestDTO $request): InfractionSeverityResponseDTO
    {
        $infractionSeverity = new InfractionSeverityModel(
            id: $id,
            name: $request->name,
            active: $request->active
        );

        $updated = $this->repository->saveInfractionSeverity($infractionSeverity);

        return new InfractionSeverityResponseDTO(
            id: $updated->getId(),
            name: $updated->getName(),
            active: $updated->isActive()
        );
    }
}

