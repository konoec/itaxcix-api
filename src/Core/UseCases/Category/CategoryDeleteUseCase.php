<?php

namespace itaxcix\Core\UseCases\Category;

use itaxcix\Core\Interfaces\vehicle\VehicleCategoryRepositoryInterface;

class CategoryDeleteUseCase
{
    private VehicleCategoryRepositoryInterface $vehicleCategoryRepository;

    public function __construct(VehicleCategoryRepositoryInterface $vehicleCategoryRepository)
    {
        $this->vehicleCategoryRepository = $vehicleCategoryRepository;
    }

    public function execute(int $id): bool
    {
        // Verificar que la categoría existe antes de eliminarla
        $existingCategory = $this->vehicleCategoryRepository->findById($id);
        if (!$existingCategory) {
            throw new \InvalidArgumentException("Category with ID {$id} not found");
        }

        return $this->vehicleCategoryRepository->delete($id);
    }
}
