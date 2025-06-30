<?php

namespace itaxcix\Core\UseCases\InfractionSeverity;

use itaxcix\Core\Domain\infraction\InfractionSeverityModel;
use itaxcix\Core\Interfaces\infraction\InfractionSeverityRepositoryInterface;
use itaxcix\Shared\DTO\useCases\InfractionSeverity\InfractionSeverityRequestDTO;
use itaxcix\Shared\DTO\useCases\InfractionSeverity\InfractionSeverityResponseDTO;

class InfractionSeverityCreateUseCase
{
    private InfractionSeverityRepositoryInterface $repository;

    public function __construct(InfractionSeverityRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(InfractionSeverityRequestDTO $request): InfractionSeverityResponseDTO
    {
        $infractionSeverity = new InfractionSeverityModel(
            id: null,
            name: $request->name,
            active: $request->active
        );

        $saved = $this->repository->saveInfractionSeverity($infractionSeverity);

        return new InfractionSeverityResponseDTO(
            id: $saved->getId(),
            name: $saved->getName(),
            active: $saved->isActive()
        );
    }
}
