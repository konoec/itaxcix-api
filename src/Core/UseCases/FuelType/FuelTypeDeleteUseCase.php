<?php

namespace itaxcix\Core\UseCases\FuelType;

use itaxcix\Core\Interfaces\vehicle\FuelTypeRepositoryInterface;

class FuelTypeDeleteUseCase
{
    private FuelTypeRepositoryInterface $fuelTypeRepository;

    public function __construct(FuelTypeRepositoryInterface $fuelTypeRepository)
    {
        $this->fuelTypeRepository = $fuelTypeRepository;
    }

    public function execute(int $id): bool
    {
        // Verificar que el tipo de combustible existe antes de eliminarlo
        $existingFuelType = $this->fuelTypeRepository->findById($id);
        if (!$existingFuelType) {
            throw new \InvalidArgumentException("FuelType with ID {$id} not found");
        }

        return $this->fuelTypeRepository->delete($id);
    }
}
