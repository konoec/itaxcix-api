<?php

namespace itaxcix\Core\UseCases\Category;

use itaxcix\Core\Interfaces\vehicle\VehicleCategoryRepositoryInterface;
use itaxcix\Core\Interfaces\vehicle\VehicleRepositoryInterface;

class CategoryDeleteUseCase
{
    private VehicleCategoryRepositoryInterface $vehicleCategoryRepository;
    private VehicleRepositoryInterface $vehicleRepository;

    public function __construct(VehicleCategoryRepositoryInterface $vehicleCategoryRepository, VehicleRepositoryInterface $vehicleRepository)
    {
        $this->vehicleCategoryRepository = $vehicleCategoryRepository;
        $this->vehicleRepository = $vehicleRepository;
    }

    public function execute(int $id): bool
    {
        // Verificar que la categoría existe antes de eliminarla
        $existingCategory = $this->vehicleCategoryRepository->findById($id);
        if (!$existingCategory) {
            throw new \InvalidArgumentException("Categoría con ID {$id} no encontrada.");
        }

        // Verificar si la categoría está asociada a algún vehículo
        $vehicles = $this->vehicleRepository->findActiveByCategoryId($id);
        if (!empty($vehicles)) {
            throw new \InvalidArgumentException('No se puede eliminar la categoría porque está asociada a uno o más vehículos.');
        }

        return $this->vehicleCategoryRepository->delete($id);
    }
}
