<?php

namespace itaxcix\Core\UseCases\VehicleClass;

use itaxcix\Core\Domain\vehicle\VehicleClassModel;
use itaxcix\Core\Interfaces\vehicle\VehicleClassRepositoryInterface;
use itaxcix\Shared\DTO\useCases\VehicleClass\VehicleClassRequestDTO;
use itaxcix\Shared\DTO\useCases\VehicleClass\VehicleClassResponseDTO;

class VehicleClassCreateUseCase
{
    private VehicleClassRepositoryInterface $repository;

    public function __construct(VehicleClassRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(VehicleClassRequestDTO $request): VehicleClassResponseDTO
    {
        $vehicleClass = new VehicleClassModel(
            id: null,
            name: $request->name,
            active: $request->active
        );

        $savedVehicleClass = $this->repository->saveVehicleClass($vehicleClass);

        return new VehicleClassResponseDTO(
            id: $savedVehicleClass->getId(),
            name: $savedVehicleClass->getName(),
            active: $savedVehicleClass->isActive()
        );
    }
}
