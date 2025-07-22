<?php

namespace itaxcix\Core\UseCases\VehicleClass;

use itaxcix\Core\Interfaces\vehicle\VehicleClassRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\VehicleRepositoryInterface;

class VehicleClassDeleteUseCase
{
    private VehicleClassRepositoryInterface $repository;
    private VehicleRepositoryInterface $vehicleRepository;

    public function __construct(VehicleClassRepositoryInterface $repository, VehicleRepositoryInterface $vehicleRepository)
    {
        $this->repository = $repository;
        $this->vehicleRepository = $vehicleRepository;
    }

    public function execute(int $id): bool
    {
        $existingVehicleClass = $this->repository->findById($id);
        if (!$existingVehicleClass) {
            throw new \InvalidArgumentException("Clase de vehículo con ID {$id} no encontrada.");
        }

        // Verificar si la clase de vehículo está asociada a algún vehículo
        $vehicles = $this->vehicleRepository->findActiveByVehicleClassId($id);
        if (!empty($vehicles)) {
            throw new \InvalidArgumentException('No se puede eliminar la clase de vehículo porque está asociada a uno o más vehículos.');
        }

        return $this->repository->delete($id);
    }
}
