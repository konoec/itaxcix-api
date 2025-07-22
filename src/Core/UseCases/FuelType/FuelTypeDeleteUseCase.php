<?php

namespace itaxcix\Core\UseCases\FuelType;

use itaxcix\Core\Interfaces\vehicle\FuelTypeRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\VehicleRepositoryInterface;

class FuelTypeDeleteUseCase
{
    private FuelTypeRepositoryInterface $fuelTypeRepository;
    private VehicleRepositoryInterface $vehicleRepository;

    public function __construct(FuelTypeRepositoryInterface $fuelTypeRepository, VehicleRepositoryInterface $vehicleRepository)
    {
        $this->fuelTypeRepository = $fuelTypeRepository;
        $this->vehicleRepository = $vehicleRepository;
    }

    public function execute(int $id): bool
    {
        // Verificar que el tipo de combustible existe antes de eliminarlo
        $existingFuelType = $this->fuelTypeRepository->findById($id);
        if (!$existingFuelType) {
            throw new \InvalidArgumentException("Tipo de combustible con ID {$id} no encontrado.");
        }

        // Verificar si el tipo de combustible está asociado a algún vehículo
        $vehicles = $this->vehicleRepository->findActiveByFuelTypeId($id);
        if (!empty($vehicles)) {
            throw new \InvalidArgumentException('No se puede eliminar el tipo de combustible porque está asociado a uno o más vehículos.');
        }

        return $this->fuelTypeRepository->delete($id);
    }
}
