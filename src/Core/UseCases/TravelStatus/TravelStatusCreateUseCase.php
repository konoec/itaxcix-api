<?php

namespace itaxcix\Core\UseCases\TravelStatus;

use itaxcix\Core\Domain\travel\TravelStatusModel;
use itaxcix\Core\Interfaces\travel\TravelStatusRepositoryInterface;
use itaxcix\Shared\DTO\useCases\TravelStatus\TravelStatusRequestDTO;
use itaxcix\Shared\DTO\useCases\TravelStatus\TravelStatusResponseDTO;

class TravelStatusCreateUseCase
{
    private TravelStatusRepositoryInterface $repository;

    public function __construct(TravelStatusRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(TravelStatusRequestDTO $request): TravelStatusResponseDTO
    {
        $travelStatus = new TravelStatusModel(
            id: null,
            name: $request->name,
            active: $request->active
        );

        $saved = $this->repository->saveTravelStatus($travelStatus);

        return new TravelStatusResponseDTO(
            id: $saved->getId(),
            name: $saved->getName(),
            active: $saved->isActive()
        );
    }
}

