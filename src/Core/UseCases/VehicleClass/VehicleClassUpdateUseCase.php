<?php

namespace itaxcix\Core\UseCases\VehicleClass;

use itaxcix\Core\Domain\vehicle\VehicleClassModel;
use itaxcix\Core\Interfaces\vehicle\VehicleClassRepositoryInterface;
use itaxcix\Shared\DTO\useCases\VehicleClass\VehicleClassRequestDTO;
use itaxcix\Shared\DTO\useCases\VehicleClass\VehicleClassResponseDTO;

class VehicleClassUpdateUseCase
{
    private VehicleClassRepositoryInterface $repository;

    public function __construct(VehicleClassRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function execute(int $id, VehicleClassRequestDTO $request): VehicleClassResponseDTO
    {
        $vehicleClass = new VehicleClassModel(
            id: $id,
            name: $request->name,
            active: $request->active
        );

        $updatedVehicleClass = $this->repository->saveVehicleClass($vehicleClass);

        return new VehicleClassResponseDTO(
            id: $updatedVehicleClass->getId(),
            name: $updatedVehicleClass->getName(),
            active: $updatedVehicleClass->isActive()
        );
    }
}
