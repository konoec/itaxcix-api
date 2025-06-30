<?php

namespace itaxcix\Core\UseCases\TravelStatus;

use itaxcix\Core\Domain\travel\TravelStatusModel;
use itaxcix\Core\Interfaces\travel\TravelStatusRepositoryInterface;
use itaxcix\Shared\DTO\useCases\TravelStatus\TravelStatusRequestDTO;
use itaxcix\Shared\DTO\useCases\TravelStatus\TravelStatusResponseDTO;

class TravelStatusUpdateUseCase
{
    private TravelStatusRepositoryInterface $repository;

    public function __construct(TravelStatusRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id, TravelStatusRequestDTO $request): TravelStatusResponseDTO
    {
        $travelStatus = new TravelStatusModel(
            id: $id,
            name: $request->name,
            active: $request->active
        );

        $updated = $this->repository->saveTravelStatus($travelStatus);

        return new TravelStatusResponseDTO(
            id: $updated->getId(),
            name: $updated->getName(),
            active: $updated->isActive()
        );
    }
}

